<?php
/**
 * Services Controller
 * Connects to live database models with fallback support for seed data (Indian Rupee Pricing)
 */

class ServicesController extends Controller {

    public function index() {
        $selectedCat = $_GET['cat'] ?? 'all';
        $searchQuery = $_GET['q'] ?? '';

        /** @var Service $serviceModel */
        $serviceModel = $this->model('Service');
        /** @var Category $categoryModel */
        $categoryModel = $this->model('Category');

        $categories = $categoryModel->getAllActive();
        $dbServices = $serviceModel->getAll([
            'category' => $selectedCat,
            'search'   => $searchQuery
        ]);

        if (!empty($dbServices)) {
            $services = array_map(function($s) {
                return [
                    'id'            => $s['id'],
                    'category_slug' => $s['category_slug'],
                    'category_name' => $s['category_name'],
                    'name'          => $s['name'],
                    'description'   => $s['description'],
                    'price'         => (float)$s['base_price'],
                    'duration'      => (int)$s['duration_mins'],
                    'badge'         => $s['category_badge'] ?? 'Featured',
                    'icon'          => $this->getCategoryIcon($s['category_slug'])
                ];
            }, $dbServices);
        } else {
            $services = $this->getFallbackServices($selectedCat, $searchQuery);
        }

        $data = [
            'pageTitle'   => 'Explore Services | AuraCraft Curated Disciplines',
            'activePage'  => 'services',
            'services'    => $services,
            'categories'  => $categories,
            'selectedCat' => $selectedCat,
            'searchQuery' => $searchQuery
        ];

        $this->view('services/index', $data);
    }

    private function getCategoryIcon(string $slug): string {
        $map = [
            'home-cleaning'  => '✨',
            'plumbing'       => '🔧',
            'electrical'     => '⚡',
            'painting'       => '🎨',
            'appliance-care' => '❄️',
            'smart-home'     => '🔒'
        ];
        return $map[$slug] ?? '✦';
    }

    private function getFallbackServices(string $cat, string $query): array {
        $catalog = [
            [
                'id' => 1, 'category_slug' => 'home-cleaning', 'category_name' => 'Home Cleaning',
                'name' => 'Deep Residential Clean',
                'description' => 'Comprehensive whole-home sanitization covering kitchens, luxury baths, baseboards, interiors, and dust-free surfaces.',
                'price' => 1499.00, 'duration' => 180, 'badge' => 'Top Popular', 'icon' => '✨'
            ],
            [
                'id' => 2, 'category_slug' => 'home-cleaning', 'category_name' => 'Home Cleaning',
                'name' => 'Eco Standard Maintenance Clean',
                'description' => 'Routine bi-weekly housekeeping using 100% biodegradable, plant-based and hypoallergenic solutions.',
                'price' => 699.00, 'duration' => 90, 'badge' => 'Eco-Certified', 'icon' => '🌿'
            ],
            [
                'id' => 3, 'category_slug' => 'plumbing', 'category_name' => 'Plumbing',
                'name' => 'Emergency Pipe Leak & Valve Repair',
                'description' => 'Swift diagnostic inspection and high-pressure repair for leaking joints, supply lines, shutoff valves, and traps.',
                'price' => 499.00, 'duration' => 60, 'badge' => '24/7 Rapid', 'icon' => '🔧'
            ],
            [
                'id' => 4, 'category_slug' => 'plumbing', 'category_name' => 'Plumbing',
                'name' => 'Luxury Faucet & Fixture Installation',
                'description' => 'Master-level replacement of kitchen sinks, bathroom faucets, smart valves, and shower columns.',
                'price' => 599.00, 'duration' => 75, 'badge' => 'Artisan', 'icon' => '🚰'
            ],
            [
                'id' => 5, 'category_slug' => 'electrical', 'category_name' => 'Electrical',
                'name' => 'Smart Lighting & Dimmer Integration',
                'description' => 'Certified conversion to smart switches and dimmers with voice assistant mobile calibration.',
                'price' => 899.00, 'duration' => 90, 'badge' => 'Smart Home', 'icon' => '⚡'
            ],
            [
                'id' => 6, 'category_slug' => 'electrical', 'category_name' => 'Electrical',
                'name' => 'Comprehensive Electrical Safety Audit',
                'description' => 'Full diagnostic check of main breaker load, ground fault interrupt circuits, arc faults, and wiring code.',
                'price' => 1199.00, 'duration' => 120, 'badge' => 'Safety First', 'icon' => '💡'
            ]
        ];

        if ($cat !== 'all') {
            $catalog = array_filter($catalog, fn($s) => $s['category_slug'] === $cat);
        }
        if (!empty($query)) {
            $catalog = array_filter($catalog, fn($s) => stripos($s['name'], $query) !== false || stripos($s['description'], $query) !== false);
        }

        return array_values($catalog);
    }
}
