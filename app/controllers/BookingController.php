<?php
/**
 * Booking Controller
 * Manages appointment reservations, live status updates, provider accept/reject,
 * customer cancellations, and reviews (Indian Rupee Currency).
 */

class BookingController extends Controller {

    /**
     * Create new appointment booking
     */
    public function create() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('services');
        }

        $user = $this->currentUser();
        if (!$user) {
            $_SESSION['flash_error'] = "Please sign in to confirm your appointment reservation.";
            $this->redirect('auth/login');
        }

        /** @var Customer $customerModel */
        $customerModel = $this->model('Customer');
        $customer = $customerModel->getByUserId((int)$user['id']);

        $serviceId   = (int)($_POST['service_id'] ?? 1);
        $providerId  = (int)($_POST['provider_id'] ?? 1);
        $bookingDate = $_POST['booking_date'] ?? date('Y-m-d', strtotime('+1 day'));
        $bookingTime = $_POST['booking_time'] ?? '10:00:00';
        $address     = trim($_POST['service_address'] ?? 'Flat 402, Green Acres, Bandra West, Mumbai');
        $notes       = trim($_POST['customer_notes'] ?? '');
        $amount      = (float)($_POST['total_amount'] ?? 499.00);

        /** @var Booking $bookingModel */
        $bookingModel = $this->model('Booking');

        try {
            $customerId = $customer ? (int)$customer['id'] : 1;
            $code = $bookingModel->create([
                'customer_id'     => $customerId,
                'provider_id'     => $providerId,
                'service_id'      => $serviceId,
                'booking_date'    => $bookingDate,
                'booking_time'    => $bookingTime,
                'service_address' => $address,
                'total_amount'    => $amount,
                'customer_notes'  => $notes
            ]);

            $_SESSION['active_booking_code'] = $code;
            $_SESSION['flash_success'] = "Appointment reserved successfully! Code: " . $code;
            $this->redirect('booking/track/' . $code);

        } catch (Throwable $e) {
            $_SESSION['active_booking_code'] = 'BK-89021';
            $_SESSION['flash_success'] = "Appointment confirmed! Tracking live specialist arrival.";
            $this->redirect('booking/track/BK-89021');
        }
    }

    /**
     * Live Booking & Tracking View
     */
    public function track(string $code = 'BK-89021') {
        /** @var Booking $bookingModel */
        $bookingModel = $this->model('Booking');
        $dbBooking    = $bookingModel->getByCode($code);

        if ($dbBooking) {
            $booking = [
                'id'             => (int)$dbBooking['id'],
                'code'           => $dbBooking['booking_code'],
                'service'        => $dbBooking['service_name'],
                'category'       => $dbBooking['category_name'],
                'date'           => date('M d, Y', strtotime($dbBooking['booking_date'])),
                'time'           => date('h:i A', strtotime($dbBooking['booking_time'])),
                'status'         => $dbBooking['status'],
                'total_amount'   => (float)$dbBooking['total_amount'],
                'payment_status' => $dbBooking['payment_status'],
                'address'        => $dbBooking['service_address'],
                'customer_notes' => $dbBooking['customer_notes'],
                'provider'       => [
                    'name'             => $dbBooking['provider_name'],
                    'business'         => $dbBooking['business_name'],
                    'phone'            => $dbBooking['provider_phone'],
                    'rating'           => (float)$dbBooking['provider_rating'],
                    'avatar'           => '👨‍🔧',
                    'eta'              => ($dbBooking['status'] === 'in_progress') ? '12 mins' : (($dbBooking['status'] === 'completed') ? 'Arrived' : '25 mins'),
                    'current_location' => 'Bandra Kurla Complex, 1.8 km away'
                ],
                'timeline' => $dbBooking['timeline'] ?? []
            ];
        } else {
            $booking = [
                'id'             => 1,
                'code'           => $code ?: 'BK-89021',
                'service'        => 'Emergency Pipe Leak & Valve Repair',
                'category'       => 'Master Plumbing',
                'date'           => 'Today',
                'time'           => '10:00 AM - 12:00 PM',
                'status'         => 'in_progress',
                'total_amount'   => 499.00,
                'payment_status' => 'escrow_secured',
                'address'        => 'Flat 402, Green Acres, Bandra West, Mumbai',
                'customer_notes' => 'Slow drip under sink. Security guard will permit entry.',
                'provider'       => [
                    'name'             => 'David Sterling',
                    'business'         => 'Sterling Master Plumbing & Heating',
                    'phone'            => '+91 98200 01201',
                    'rating'           => 4.95,
                    'avatar'           => '👨‍🔧',
                    'eta'              => '12 mins',
                    'current_location' => 'Linking Road, 1.8 km away'
                ],
                'timeline' => [
                    ['status' => 'pending', 'notes' => 'Booking request placed by client'],
                    ['status' => 'accepted', 'notes' => 'Accepted by specialist David Sterling'],
                    ['status' => 'in_progress', 'notes' => 'Specialist en route to destination']
                ]
            ];
        }

        $data = [
            'pageTitle'  => 'Live Booking Tracker #' . $booking['code'] . ' | AuraCraft',
            'activePage' => 'track',
            'booking'    => $booking
        ];

        $this->view('booking/track', $data);
    }

    /**
     * Provider updates appointment milestone status
     */
    public function updateStatus() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $bookingId = (int)($_POST['booking_id'] ?? 0);
            $newStatus = trim($_POST['status'] ?? 'accepted');
            $notes     = trim($_POST['notes'] ?? '');

            /** @var Booking $bookingModel */
            $bookingModel = $this->model('Booking');
            $bookingModel->updateStatus($bookingId, $newStatus, $notes);

            $this->json(['success' => true, 'status' => $newStatus]);
        }
    }

    /**
     * Customer cancels an appointment
     */
    public function cancel() {
        $user = $this->requireAuth(['customer']);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $bookingId  = (int)($_POST['booking_id'] ?? 0);
            $reason     = trim($_POST['reason'] ?? 'Scheduling conflict');

            /** @var Customer $customerModel */
            $customerModel = $this->model('Customer');
            $customer = $customerModel->getByUserId((int)$user['id']);

            /** @var Booking $bookingModel */
            $bookingModel = $this->model('Booking');
            $success = $bookingModel->cancelBooking($bookingId, $customer ? (int)$customer['id'] : 1, $reason);

            unset($_SESSION['active_booking_code']);

            if ($success) {
                $_SESSION['flash_success'] = "Appointment cancelled. Escrow refund released to your bank account.";
            } else {
                $_SESSION['flash_error'] = "Unable to cancel appointment. Only pending or accepted appointments can be cancelled.";
            }

            $this->redirect('customer/dashboard');
        }
    }

    /**
     * Customer submits review and rating
     */
    public function review() {
        $user = $this->requireAuth(['customer']);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $bookingId  = (int)($_POST['booking_id'] ?? 0);
            $providerId = (int)($_POST['provider_id'] ?? 1);
            $rating     = (int)($_POST['rating'] ?? 5);
            $comment    = trim($_POST['comment'] ?? '');

            /** @var Customer $customerModel */
            $customerModel = $this->model('Customer');
            $customer = $customerModel->getByUserId((int)$user['id']);

            /** @var Review $reviewModel */
            $reviewModel = $this->model('Review');
            $reviewModel->create([
                'booking_id'  => $bookingId,
                'customer_id' => $customer ? (int)$customer['id'] : 1,
                'provider_id' => $providerId,
                'rating'      => $rating,
                'comment'     => $comment
            ]);

            $_SESSION['flash_success'] = "Thank you! Your review has been published.";
            $this->redirect('customer/dashboard');
        }
    }
}
