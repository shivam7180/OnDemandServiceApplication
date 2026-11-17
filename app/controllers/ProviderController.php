<?php
/**
 * Provider Controller
 * Handles provider partner dashboard, dynamic job queue, live acceptance/rejection,
 * and working schedule.
 * Protected by role authorization (provider only).
 */

class ProviderController extends Controller {

    public function dashboard() {
        // Enforce role authorization
        $user = $this->requireAuth(['provider']);

        /** @var Provider $providerModel */
        $providerModel = $this->model('Provider');
        $providerProfile = $providerModel->getByUserId((int)$user['id']);

        $providerId = $providerProfile ? (int)$providerProfile['id'] : 1;

        $provider = [
            'id'             => $providerId,
            'name'           => $user['full_name'],
            'business'       => $providerProfile['business_name'] ?? 'Sterling Master Plumbing & Heating',
            'is_available'   => (int)($providerProfile['is_available'] ?? 1),
            'rating'         => (float)($providerProfile['rating_avg'] ?? 4.95),
            'reviews_count'  => (int)($providerProfile['reviews_count'] ?? 48),
            'month_earnings' => 2840.00,
            'pending_payout' => 475.00,
            'completed_jobs' => 48
        ];

        /** @var Booking $bookingModel */
        $bookingModel = $this->model('Booking');
        $dbBookings   = $bookingModel->getByProviderId($providerId);

        $pendingRequests = [];
        $upcomingJobs    = [];

        if (!empty($dbBookings)) {
            foreach ($dbBookings as $b) {
                if ($b['status'] === 'pending') {
                    $pendingRequests[] = [
                        'id'       => (int)$b['id'],
                        'code'     => $b['booking_code'],
                        'customer' => $b['customer_name'],
                        'phone'    => $b['customer_phone'],
                        'address'  => $b['service_address'],
                        'service'  => $b['service_name'],
                        'date'     => date('M d, h:i A', strtotime($b['booking_date'] . ' ' . $b['booking_time'])),
                        'amount'   => (float)$b['total_amount'],
                        'notes'    => $b['customer_notes']
                    ];
                } elseif (in_array($b['status'], ['accepted', 'in_progress'])) {
                    $upcomingJobs[] = [
                        'id'       => (int)$b['id'],
                        'code'     => $b['booking_code'],
                        'customer' => $b['customer_name'],
                        'phone'    => $b['customer_phone'],
                        'address'  => $b['service_address'],
                        'service'  => $b['service_name'],
                        'date'     => date('M d, h:i A', strtotime($b['booking_date'] . ' ' . $b['booking_time'])),
                        'amount'   => (float)$b['total_amount'],
                        'status'   => $b['status']
                    ];
                }
            }
        }

        // Demo fallback if tables not yet populated
        if (empty($pendingRequests) && empty($upcomingJobs)) {
            $pendingRequests = [
                [
                    'id'       => 101,
                    'code'     => 'BK-89022',
                    'customer' => 'Marcus Vance',
                    'phone'    => '+1 (555) 0102',
                    'address'  => '1204 Pine Hollow Road, Oakland, CA',
                    'service'  => 'Emergency Pipe Leak & Valve Repair',
                    'date'     => 'Tomorrow, 1:00 PM',
                    'amount'   => 95.00,
                    'notes'    => 'Slow drip under master bath sink. Main shutoff is in garage.'
                ]
            ];

            $upcomingJobs = [
                [
                    'id'       => 100,
                    'code'     => 'BK-89021',
                    'customer' => 'Sophia Miller',
                    'phone'    => '+1 (555) 0101',
                    'address'  => '742 Evergreen Terrace, San Francisco, CA',
                    'service'  => 'Luxury Faucet & Fixture Installation',
                    'date'     => 'Nov 20, 2026 - 10:00 AM',
                    'amount'   => 85.00,
                    'status'   => 'confirmed'
                ]
            ];
        }

        $data = [
            'pageTitle'       => 'Partner Dashboard | NEARZO Pro Console',
            'activePage'      => 'provider',
            'provider'        => $provider,
            'pendingRequests' => $pendingRequests,
            'upcomingJobs'    => $upcomingJobs
        ];

        $this->view('provider/dashboard', $data);
    }

    /**
     * AJAX: Toggle provider availability status
     */
    public function toggleAvailability() {
        $user = $this->requireAuth(['provider']);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $status = (int)($_POST['status'] ?? 1);

            /** @var Provider $providerModel */
            $providerModel = $this->model('Provider');
            $providerProfile = $providerModel->getByUserId((int)$user['id']);

            if ($providerProfile) {
                $providerModel->toggleAvailability((int)$providerProfile['id'], $status);
            }

            $this->json(['success' => true, 'is_available' => $status]);
        }
    }
}
