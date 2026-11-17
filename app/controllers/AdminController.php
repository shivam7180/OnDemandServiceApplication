<?php
/**
 * Admin Controller
 * Manages platform operations, provider approvals, platform statistics, and complaints.
 * Protected by role authorization (admin only).
 */

class AdminController extends Controller {

    public function dashboard() {
        // Enforce role authorization
        $admin = $this->requireAuth(['admin']);

        /** @var Provider $providerModel */
        $providerModel = $this->model('Provider');
        $pendingList = $providerModel->getAllPendingApprovals();

        $pendingProviders = [];
        if (!empty($pendingList)) {
            foreach ($pendingList as $p) {
                $pendingProviders[] = [
                    'id'           => (int)$p['id'],
                    'name'         => $p['full_name'],
                    'business'     => $p['business_name'] ?? 'Independent Specialist',
                    'category'     => 'Verified Trade',
                    'experience'   => (int)$p['experience_years'],
                    'rate'         => (float)$p['hourly_rate'],
                    'city'         => $p['city'],
                    'applied_date' => date('M d, H:i', strtotime($p['created_at']))
                ];
            }
        } else {
            // Default demo queue
            $pendingProviders = [
                [
                    'id'           => 10,
                    'name'         => 'Michael Chen',
                    'business'     => 'Apex Hydronics & Gas Fitting',
                    'category'     => 'Master Plumbing',
                    'experience'   => 14,
                    'rate'         => 80.00,
                    'city'         => 'San Francisco',
                    'applied_date' => 'Today at 09:30 AM'
                ],
                [
                    'id'           => 11,
                    'name'         => 'Claire Dupont',
                    'business'     => 'PureAura Botanical Home Sanitizing',
                    'category'     => 'Home Cleaning',
                    'experience'   => 6,
                    'rate'         => 50.00,
                    'city'         => 'Oakland',
                    'applied_date' => 'Yesterday'
                ]
            ];
        }

        $stats = [
            'total_gmv'          => 142850.00,
            'active_bookings'    => 24,
            'total_customers'    => 1840,
            'verified_providers' => 142,
            'pending_approvals'  => count($pendingProviders)
        ];

        $recentDisputes = [
            [
                'id'       => 301,
                'code'     => 'BK-88902',
                'customer' => 'Arthur Pendelton',
                'provider' => 'Julian Thorne',
                'subject'  => 'Arrival delay clarification',
                'status'   => 'open'
            ]
        ];

        $data = [
            'pageTitle'        => 'Admin Console | NEARZO Operations Control',
            'activePage'       => 'admin',
            'adminUser'        => $admin,
            'stats'            => $stats,
            'pendingProviders' => $pendingProviders,
            'recentDisputes'   => $recentDisputes
        ];

        $this->view('admin/dashboard', $data);
    }

    /**
     * AJAX / Action: Approve or Reject a provider
     */
    public function updateProviderStatus() {
        $this->requireAuth(['admin']);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $providerId = (int)($_POST['provider_id'] ?? 0);
            $status     = (int)($_POST['status'] ?? 1); // 1 = approved, 2 = rejected

            /** @var Provider $providerModel */
            $providerModel = $this->model('Provider');
            $providerModel->setApproval($providerId, $status);

            $this->json(['success' => true, 'message' => 'Provider status updated successfully.']);
        }
    }
}
