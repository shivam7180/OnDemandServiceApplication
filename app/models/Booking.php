<?php
/**
 * Booking Model
 * Manages appointment reservations, state transitions, timeline audit trail,
 * and escrow calculations.
 */

class Booking extends Model {

    /**
     * Create a new booking request with initial timeline entry
     */
    public function create(array $data): string {
        return $this->transaction(function(PDO $db) use ($data) {
            // Generate unique booking code
            $code = 'BK-' . strtoupper(substr(uniqid(), -5));

            $sql = "INSERT INTO `bookings` 
                    (`booking_code`, `customer_id`, `provider_id`, `service_id`, `booking_date`, 
                     `booking_time`, `service_address`, `latitude`, `longitude`, `total_amount`, 
                     `payment_method`, `payment_status`, `status`, `customer_notes`)
                    VALUES 
                    (:code, :cid, :pid, :sid, :bdate, :btime, :address, :lat, :lng, :amount, :pmethod, 'pending', 'pending', :notes)";

            $stmt = $db->prepare($sql);
            $stmt->execute([
                ':code'    => $code,
                ':cid'     => (int)$data['customer_id'],
                ':pid'     => (int)$data['provider_id'],
                ':sid'     => (int)$data['service_id'],
                ':bdate'   => $data['booking_date'],
                ':btime'   => $data['booking_time'] ?? '10:00:00',
                ':address' => trim($data['service_address']),
                ':lat'     => $data['latitude'] ?? 37.774929,
                ':lng'     => $data['longitude'] ?? -122.419416,
                ':amount'  => (float)$data['total_amount'],
                ':pmethod' => $data['payment_method'] ?? 'card',
                ':notes'   => trim($data['customer_notes'] ?? '')
            ]);

            $bookingId = (int)$db->lastInsertId();

            // Insert initial timeline event
            $timeSql = "INSERT INTO `booking_timeline` (`booking_id`, `status`, `notes`) 
                        VALUES (:bid, 'pending', 'Booking request initiated by client')";
            $tStmt = $db->prepare($timeSql);
            $tStmt->execute([':bid' => $bookingId]);

            // Notify Provider
            $notifSql = "INSERT INTO `notifications` (`user_id`, `title`, `message`, `type`, `link`)
                         SELECT u.id, 'New Booking Request', CONCAT('New appointment request ', :code, ' received.'), 'booking', CONCAT('/provider/dashboard')
                         FROM `providers` p
                         JOIN `users` u ON u.id = p.user_id
                         WHERE p.id = :pid";
            $nStmt = $db->prepare($notifSql);
            $nStmt->execute([':code' => $code, ':pid' => (int)$data['provider_id']]);

            return $code;
        });
    }

    /**
     * Get booking details by code including customer, provider, service, and timeline
     */
    public function getByCode(string $code): ?array {
        try {
            $sql = "SELECT b.*, 
                           s.name AS service_name, s.duration_mins, c_cat.name AS category_name,
                           u_c.full_name AS customer_name, u_c.phone AS customer_phone, u_c.email AS customer_email,
                           p.business_name, p.rating_avg AS provider_rating, p.hourly_rate AS provider_rate,
                           u_p.full_name AS provider_name, u_p.phone AS provider_phone
                    FROM `bookings` b
                    JOIN `services` s ON s.id = b.service_id
                    JOIN `service_categories` c_cat ON c_cat.id = s.category_id
                    JOIN `customers` cust ON cust.id = b.customer_id
                    JOIN `users` u_c ON u_c.id = cust.user_id
                    JOIN `providers` p ON p.id = b.provider_id
                    JOIN `users` u_p ON u_p.id = p.user_id
                    WHERE b.booking_code = :code LIMIT 1";

            $booking = $this->fetchOne($sql, [':code' => $code]);

            if ($booking) {
                // Fetch timeline
                $tlSql = "SELECT * FROM `booking_timeline` WHERE `booking_id` = :bid ORDER BY `created_at` ASC";
                $booking['timeline'] = $this->fetchAll($tlSql, [':bid' => $booking['id']]);
            }

            return $booking ?: null;
        } catch (Throwable $e) {
            return null;
        }
    }

    /**
     * Get all bookings for a customer
     */
    public function getByCustomerId(int $customerId): array {
        try {
            $sql = "SELECT b.*, s.name AS service_name, p.business_name, u_p.full_name AS provider_name,
                           r.rating AS customer_rating
                    FROM `bookings` b
                    JOIN `services` s ON s.id = b.service_id
                    JOIN `providers` p ON p.id = b.provider_id
                    JOIN `users` u_p ON u_p.id = p.user_id
                    LEFT JOIN `reviews` r ON r.booking_id = b.id
                    WHERE b.customer_id = :cid
                    ORDER BY b.created_at DESC";
            return $this->fetchAll($sql, [':cid' => $customerId]);
        } catch (Throwable $e) {
            return [];
        }
    }

    /**
     * Get all bookings for a provider
     */
    public function getByProviderId(int $providerId): array {
        try {
            $sql = "SELECT b.*, s.name AS service_name, u_c.full_name AS customer_name, u_c.phone AS customer_phone
                    FROM `bookings` b
                    JOIN `services` s ON s.id = b.service_id
                    JOIN `customers` cust ON cust.id = b.customer_id
                    JOIN `users` u_c ON u_c.id = cust.user_id
                    WHERE b.provider_id = :pid
                    ORDER BY b.created_at DESC";
            return $this->fetchAll($sql, [':pid' => $providerId]);
        } catch (Throwable $e) {
            return [];
        }
    }

    /**
     * Transition booking status and record timeline event
     */
    public function updateStatus(int $bookingId, string $status, ?string $notes = null): bool {
        return $this->transaction(function(PDO $db) use ($bookingId, $status, $notes) {
            $sql = "UPDATE `bookings` SET `status` = :status WHERE `id` = :id";
            $stmt = $db->prepare($sql);
            $stmt->execute([':status' => $status, ':id' => $bookingId]);

            // If completed, update payment status to paid
            if ($status === 'completed') {
                $db->prepare("UPDATE `bookings` SET `payment_status` = 'paid' WHERE `id` = :id")
                   ->execute([':id' => $bookingId]);
            }

            // Append timeline
            $tSql = "INSERT INTO `booking_timeline` (`booking_id`, `status`, `notes`) 
                     VALUES (:bid, :status, :notes)";
            $tStmt = $db->prepare($tSql);
            $tStmt->execute([
                ':bid'    => $bookingId,
                ':status' => $status,
                ':notes'  => $notes ?? "Status updated to " . ucfirst($status)
            ]);

            // Notify Customer
            $nSql = "INSERT INTO `notifications` (`user_id`, `title`, `message`, `type`, `link`)
                     SELECT u.id, CONCAT('Appointment ', UPPER(:status)), 
                            CONCAT('Your appointment status has been updated to: ', :status), 'booking', 
                            CONCAT('/booking/track/', b.booking_code)
                     FROM `bookings` b
                     JOIN `customers` c ON c.id = b.customer_id
                     JOIN `users` u ON u.id = c.user_id
                     WHERE b.id = :bid";
            $nStmt = $db->prepare($nSql);
            $nStmt->execute([':status' => $status, ':bid' => $bookingId]);

            return true;
        });
    }

    /**
     * Customer cancels booking (only allowed if pending or accepted)
     */
    public function cancelBooking(int $bookingId, int $customerId, string $reason): bool {
        return $this->transaction(function(PDO $db) use ($bookingId, $customerId, $reason) {
            $chk = $db->prepare("SELECT `status` FROM `bookings` WHERE `id` = :id AND `customer_id` = :cid");
            $chk->execute([':id' => $bookingId, ':cid' => $customerId]);
            $row = $chk->fetch();

            if (!$row || !in_array($row['status'], ['pending', 'accepted'])) {
                return false;
            }

            $sql = "UPDATE `bookings` SET `status` = 'cancelled', `cancellation_reason` = :reason, `payment_status` = 'refunded' 
                    WHERE `id` = :id";
            $db->prepare($sql)->execute([':reason' => $reason, ':id' => $bookingId]);

            // Append to timeline
            $db->prepare("INSERT INTO `booking_timeline` (`booking_id`, `status`, `notes`) VALUES (:bid, 'cancelled', :notes)")
               ->execute([':bid' => $bookingId, ':notes' => 'Cancelled by client: ' . $reason]);

            return true;
        });
    }
}
