<?php
/**
 * Provider Model
 * Manages provider profile, onboarding verification, availability, rates, and schedule
 */

class Provider extends Model {

    /**
     * Create provider profile record linked to user_id
     */
    public function create(int $userId, array $details = []): int {
        $sql = "INSERT INTO `providers` 
                (`user_id`, `business_name`, `profession`, `bio`, `hourly_rate`, `price_type`, 
                 `experience_years`, `service_radius_km`, `address`, `city`, `state`, `postal_code`, 
                 `latitude`, `longitude`, `is_approved`, `is_available`, `availability_status`, `profile_photo`, `show_phone`)
                VALUES 
                (:user_id, :business_name, :profession, :bio, :hourly_rate, :price_type, 
                 :experience_years, :service_radius_km, :address, :city, :state, :postal_code, 
                 :latitude, :longitude, :is_approved, :is_available, :availability_status, :profile_photo, :show_phone)";

        $this->query($sql, [
            ':user_id'             => $userId,
            ':business_name'       => $details['business_name'] ?? 'Professional Craft Service',
            ':profession'          => $details['profession'] ?? 'Plumber',
            ':bio'                 => $details['bio'] ?? null,
            ':hourly_rate'         => $details['hourly_rate'] ?? 499.00,
            ':price_type'          => $details['price_type'] ?? 'per_hour',
            ':experience_years'    => $details['experience_years'] ?? 1,
            ':service_radius_km'   => $details['service_radius_km'] ?? 15,
            ':address'             => $details['address'] ?? 'Mumbai',
            ':city'                => $details['city'] ?? 'Mumbai',
            ':state'               => $details['state'] ?? 'MH',
            ':postal_code'         => $details['postal_code'] ?? '400050',
            ':latitude'            => $details['latitude'] ?? 19.07600000,
            ':longitude'           => $details['longitude'] ?? 72.87770000,
            ':is_approved'         => 1, // Auto-approved for frictionless demonstration
            ':is_available'        => 1,
            ':availability_status' => $details['availability_status'] ?? 'available',
            ':profile_photo'       => $details['profile_photo'] ?? null,
            ':show_phone'          => $details['show_phone'] ?? 1
        ]);

        return (int)$this->lastInsertId();
    }

    /**
     * Find providers within maximum radius (default 15 km) using the Haversine formula
     * Calculates distance between customer GPS ($custLat, $custLng) and provider GPS ($lat, $lng)
     */
    public function findNearby(float $custLat, float $custLng, float $radiusKm = 15.0, array $filters = []): array {
        try {
            $haversine = "(6371 * acos(
                LEAST(1.0, GREATEST(-1.0, 
                    cos(radians(:cust_lat1)) * cos(radians(p.latitude)) * 
                    cos(radians(p.longitude) - radians(:cust_lng)) + 
                    sin(radians(:cust_lat2)) * sin(radians(p.latitude))
                ))
            ))";

            $sql = "SELECT p.*, 
                           u.full_name AS provider_name, 
                           u.email, 
                           u.phone, 
                           u.avatar,
                           ROUND({$haversine}, 2) AS distance_km
                    FROM `providers` p
                    JOIN `users` u ON u.id = p.user_id
                    WHERE p.is_approved = 1 
                      AND u.status = 'active'
                      AND p.availability_status = 'available'
                      AND p.latitude IS NOT NULL 
                      AND p.longitude IS NOT NULL";

            $params = [
                ':cust_lat1' => $custLat,
                ':cust_lng'  => $custLng,
                ':cust_lat2' => $custLat
            ];

            // Optional profession filter
            if (!empty($filters['profession']) && $filters['profession'] !== 'all') {
                $sql .= " AND (p.profession LIKE :profession OR p.business_name LIKE :profession_alt)";
                $params[':profession'] = '%' . $filters['profession'] . '%';
                $params[':profession_alt'] = '%' . $filters['profession'] . '%';
            }

            // Radius constraint
            $sql .= " HAVING distance_km <= :radius_km";
            $params[':radius_km'] = $radiusKm;

            // Sorting
            $sort = $filters['sort'] ?? 'distance';
            if ($sort === 'price_asc') {
                $sql .= " ORDER BY p.hourly_rate ASC, distance_km ASC";
            } elseif ($sort === 'price_desc') {
                $sql .= " ORDER BY p.hourly_rate DESC, distance_km ASC";
            } elseif ($sort === 'rating') {
                $sql .= " ORDER BY p.rating_avg DESC, p.reviews_count DESC";
            } else {
                $sql .= " ORDER BY distance_km ASC";
            }

            return $this->fetchAll($sql, $params);
        } catch (Throwable $e) {
            return [];
        }
    }

    /**
     * Update Provider Availability Status (available / busy / offline)
     */
    public function updateAvailabilityStatus(int $providerId, string $status): bool {
        try {
            $isAvail = ($status === 'available') ? 1 : 0;
            $sql = "UPDATE `providers` SET `availability_status` = :status, `is_available` = :is_avail WHERE `id` = :id";
            $this->query($sql, [
                ':status'   => $status,
                ':is_avail' => $isAvail,
                ':id'       => $providerId
            ]);
            return true;
        } catch (Throwable $e) {
            return false;
        }
    }

    /**
     * Update Provider Profile & Rates
     */
    public function updateProfile(int $providerId, array $data): bool {
        try {
            $sql = "UPDATE `providers` SET 
                        `business_name` = :business_name,
                        `profession` = :profession,
                        `bio` = :bio,
                        `hourly_rate` = :hourly_rate,
                        `price_type` = :price_type,
                        `service_radius_km` = :service_radius_km,
                        `address` = :address,
                        `latitude` = :latitude,
                        `longitude` = :longitude,
                        `show_phone` = :show_phone
                    WHERE `id` = :id";

            $this->query($sql, [
                ':business_name'    => $data['business_name'],
                ':profession'       => $data['profession'],
                ':bio'              => $data['bio'],
                ':hourly_rate'      => $data['hourly_rate'],
                ':price_type'       => $data['price_type'],
                ':service_radius_km'=> $data['service_radius_km'],
                ':address'          => $data['address'],
                ':latitude'         => $data['latitude'],
                ':longitude'        => $data['longitude'],
                ':show_phone'       => $data['show_phone'] ?? 1,
                ':id'               => $providerId
            ]);
            return true;
        } catch (Throwable $e) {
            return false;
        }
    }

    /**
     * Get all approved providers with optional discipline & location filter
     */
    public function getAllApproved(array $filters = []): array {
        try {
            $sql = "SELECT p.*, u.full_name AS provider_name, u.email, u.phone, u.avatar
                    FROM `providers` p
                    JOIN `users` u ON u.id = p.user_id
                    WHERE p.is_approved = 1 AND u.status = 'active'";
            $params = [];

            if (!empty($filters['city'])) {
                $sql .= " AND p.city LIKE :city";
                $params[':city'] = '%' . $filters['city'] . '%';
            }

            $sql .= " ORDER BY p.rating_avg DESC, p.reviews_count DESC";
            return $this->fetchAll($sql, $params);
        } catch (Throwable $e) {
            return [];
        }
    }

    /**
     * Get provider by primary ID
     */
    public function getById(int $id): ?array {
        try {
            $sql = "SELECT p.*, u.full_name, u.email, u.phone, u.avatar 
                    FROM `providers` p
                    JOIN `users` u ON u.id = p.user_id
                    WHERE p.id = :id LIMIT 1";
            return $this->fetchOne($sql, [':id' => $id]);
        } catch (Throwable $e) {
            return null;
        }
    }

    /**
     * Get provider by user_id
     */
    public function getByUserId(int $userId): ?array {
        try {
            $sql = "SELECT p.*, u.full_name, u.email, u.phone, u.avatar 
                    FROM `providers` p
                    JOIN `users` u ON u.id = p.user_id
                    WHERE p.user_id = :user_id LIMIT 1";
            return $this->fetchOne($sql, [':user_id' => $userId]);
        } catch (Throwable $e) {
            return null;
        }
    }

    /**
     * Get services offered by a provider
     */
    public function getServicesByProviderId(int $providerId): array {
        try {
            $sql = "SELECT s.id, s.name, COALESCE(ps.custom_price, s.base_price) AS price, s.duration_mins, c.name AS category_name
                    FROM `provider_services` ps
                    JOIN `services` s ON s.id = ps.service_id
                    JOIN `service_categories` c ON c.id = s.category_id
                    WHERE ps.provider_id = :pid AND ps.is_offered = 1";
            return $this->fetchAll($sql, [':pid' => $providerId]);
        } catch (Throwable $e) {
            return [];
        }
    }

    /**
     * Get weekly working hours
     */
    public function getScheduleByProviderId(int $providerId): array {
        try {
            $sql = "SELECT * FROM `provider_availability` WHERE `provider_id` = :pid AND `is_active` = 1 ORDER BY FIELD(day_of_week, 'Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday')";
            return $this->fetchAll($sql, [':pid' => $providerId]);
        } catch (Throwable $e) {
            return [];
        }
    }

    /**
     * Get all providers pending administrative approval
     */
    public function getAllPendingApprovals(): array {
        try {
            $sql = "SELECT p.*, u.full_name, u.email, u.phone, u.created_at AS registered_at
                    FROM `providers` p
                    JOIN `users` u ON u.id = p.user_id
                    WHERE p.is_approved = 0";
            return $this->fetchAll($sql);
        } catch (Throwable $e) {
            return [];
        }
    }

    /**
     * Toggle availability status
     */
    public function toggleAvailability(int $providerId, int $status): bool {
        $sql = "UPDATE `providers` SET `is_available` = :status WHERE `id` = :id";
        $stmt = $this->query($sql, [':status' => $status, ':id' => $providerId]);
        return $stmt->rowCount() > 0;
    }

    /**
     * Update approval status (0 = pending, 1 = approved, 2 = rejected)
     */
    public function setApproval(int $providerId, int $approvalStatus): bool {
        $sql = "UPDATE `providers` SET `is_approved` = :status WHERE `id` = :id";
        $stmt = $this->query($sql, [':status' => $approvalStatus, ':id' => $providerId]);
        return $stmt->rowCount() > 0;
    }

    /**
     * Update provider profile details
     */
    public function updateProfile(int $userId, array $data): bool {
        $sql = "UPDATE `providers` SET 
                `business_name` = :business_name,
                `bio` = :bio,
                `hourly_rate` = :hourly_rate,
                `experience_years` = :experience_years,
                `service_radius_km` = :service_radius_km
                WHERE `user_id` = :user_id";

        $stmt = $this->query($sql, [
            ':business_name'    => trim($data['business_name']),
            ':bio'              => trim($data['bio']),
            ':hourly_rate'      => (float)$data['hourly_rate'],
            ':experience_years' => (int)$data['experience_years'],
            ':service_radius_km'=> (int)$data['service_radius_km'],
            ':user_id'          => $userId
        ]);

        return $stmt->rowCount() > 0;
    }
}
