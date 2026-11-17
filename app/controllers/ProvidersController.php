<?php
/**
 * Providers Controller
 * Handles customer two-sided marketplace search:
 * - Service selection
 * - Automatic GPS & manual location
 * - Haversine distance calculation within 15 km radius
 * - Sorting by distance, price, and rating
 * - Interactive Google Maps & Leaflet visualization
 */

class ProvidersController extends Controller {

    public function index() {
        $profession = $_GET['profession'] ?? $_GET['service'] ?? $_GET['cat'] ?? 'all';
        $location   = $_GET['location'] ?? 'Mumbai';
        $custLat    = (float)($_GET['lat'] ?? 19.07600000);
        $custLng    = (float)($_GET['lng'] ?? 72.87770000);
        $radiusKm   = (float)($_GET['radius'] ?? 15.0);
        $sort       = $_GET['sort'] ?? 'distance';

        /** @var Provider $providerModel */
        $providerModel = $this->model('Provider');
        
        // Find nearby providers using MySQL Haversine formula
        $dbProviders = $providerModel->findNearby($custLat, $custLng, $radiusKm, [
            'profession' => $profession,
            'sort'       => $sort
        ]);

        if (!empty($dbProviders)) {
            $providers = [];
            foreach ($dbProviders as $p) {
                $providers[$p['id']] = [
                    'id'                => (int)$p['id'],
                    'user_id'           => (int)$p['user_id'],
                    'name'              => $p['provider_name'],
                    'business_name'     => $p['business_name'],
                    'profession'        => $p['profession'] ?? 'Plumber',
                    'bio'               => $p['bio'],
                    'hourly_rate'       => (float)$p['hourly_rate'],
                    'price_type'        => $p['price_type'] ?? 'per_hour',
                    'experience_years'  => (int)$p['experience_years'],
                    'service_radius_km' => (int)$p['service_radius_km'],
                    'rating_avg'        => (float)$p['rating_avg'],
                    'reviews_count'     => (int)$p['reviews_count'],
                    'city'              => $p['city'] ?? 'Mumbai',
                    'address'           => $p['address'] ?? 'Mumbai',
                    'latitude'          => (float)$p['latitude'],
                    'longitude'         => (float)$p['longitude'],
                    'distance_km'       => (float)($p['distance_km'] ?? 1.5),
                    'phone'             => $p['phone'] ?? '+91 98200 00201',
                    'show_phone'        => (bool)($p['show_phone'] ?? 1),
                    'avatar'            => $p['avatar'] ?? '👨‍🔧',
                    'availability'      => $p['availability_status'] ?? 'available'
                ];
            }
        } else {
            // Provide realistic curated providers in Mumbai area with Haversine distance
            $providers = $this->getMarketplaceProviders($custLat, $custLng, $profession, $radiusKm, $sort);
        }

        $data = [
            'pageTitle'          => 'Find Local Specialists within 15 km | NEARZO',
            'activePage'         => 'providers',
            'providers'          => $providers,
            'selectedProfession' => $profession,
            'location'           => $location,
            'custLat'            => $custLat,
            'custLng'            => $custLng,
            'radiusKm'           => $radiusKm,
            'sort'               => $sort
        ];

        $this->view('providers/index', $data);
    }

    public function detail(string $id = '1') {
        $id = (int)$id;

        /** @var Provider $providerModel */
        $providerModel = $this->model('Provider');
        /** @var Review $reviewModel */
        $reviewModel   = $this->model('Review');

        $provider = $providerModel->getById($id);

        if (!$provider) {
            // Curated fallback
            $provider = [
                'id'                => 1,
                'name'              => 'David Sterling',
                'business_name'     => 'Sterling Master Plumbing & Heating',
                'profession'        => 'Plumber',
                'bio'               => 'Licensed master craftsman with over 12 years of residential plumbing, leak detection, and sanitary renovation expertise. Dedicated to transparent INR pricing and rapid response times across Mumbai.',
                'hourly_rate'       => 599.00,
                'price_type'        => 'per_hour',
                'experience_years'  => 12,
                'service_radius_km' => 15,
                'rating_avg'        => 4.95,
                'reviews_count'     => 48,
                'city'              => 'Mumbai',
                'address'           => 'Shop 12, Hill Road, Bandra West, Mumbai',
                'latitude'          => 19.06000000,
                'longitude'         => 72.86500000,
                'phone'             => '+91 98200 00201',
                'show_phone'        => 1,
                'avatar'            => '👨‍🔧',
                'services'          => [
                    ['id' => 1, 'name' => 'Emergency Pipe Leak & Valve Repair', 'price' => 499.00, 'duration' => 45],
                    ['id' => 2, 'name' => 'Bathroom Fixture & Tap Installation', 'price' => 699.00, 'duration' => 60],
                    ['id' => 3, 'name' => 'Water Heater & Geyser Servicing', 'price' => 899.00, 'duration' => 90]
                ],
                'reviews'           => [
                    ['author' => 'Ananya Sharma', 'rating' => 5, 'date' => '2 days ago', 'comment' => 'David arrived within 20 minutes! Fixed the persistent kitchen pipe leak flawlessly.'],
                    ['author' => 'Rajesh Verma', 'rating' => 5, 'date' => '1 week ago', 'comment' => 'Very polite, professional, and transparent about costs. Highly recommend!']
                ]
            ];
        } else {
            $provider['services'] = $providerModel->getServicesByProviderId($id);
            $provider['reviews']  = $reviewModel->getByProviderId($id);
            if (empty($provider['services'])) {
                $provider['services'] = [
                    ['id' => 1, 'name' => 'Standard Inspection & Diagnosis', 'price' => $provider['hourly_rate'], 'duration' => 60],
                    ['id' => 2, 'name' => 'Full Service & Repair', 'price' => $provider['hourly_rate'] * 1.5, 'duration' => 90]
                ];
            }
        }

        $data = [
            'pageTitle'  => htmlspecialchars($provider['business_name'] ?? 'Provider Profile') . ' | NEARZO',
            'activePage' => 'providers',
            'provider'   => $provider
        ];

        $this->view('providers/detail', $data);
    }

    /**
     * Curated providers with real Haversine formula calculation for preview
     */
    private function getMarketplaceProviders(float $custLat, float $custLng, string $profession, float $radiusKm, string $sort): array {
        $pool = [
            1 => [
                'id'                => 1,
                'user_id'           => 4,
                'name'              => 'David Sterling',
                'business_name'     => 'Sterling Master Plumbing & Pipeworks',
                'profession'        => 'Plumber',
                'bio'               => 'Licensed master plumber with 12 years of leak detection, sanitary fitting, and geyser installation experience.',
                'hourly_rate'       => 599.00,
                'price_type'        => 'per_hour',
                'experience_years'  => 12,
                'service_radius_km' => 15,
                'rating_avg'        => 4.95,
                'reviews_count'     => 48,
                'city'              => 'Bandra West, Mumbai',
                'address'           => 'Shop 12, Hill Road, Bandra West',
                'latitude'          => 19.06000000,
                'longitude'         => 72.86500000,
                'phone'             => '+91 98200 00201',
                'show_phone'        => 1,
                'avatar'            => '👨‍🔧',
                'availability'      => 'available'
            ],
            2 => [
                'id'                => 2,
                'user_id'           => 5,
                'name'              => 'Elena Rostova',
                'business_name'     => 'LuxeClean Botanical Housekeeping',
                'profession'        => 'Cleaner',
                'bio'               => 'Eco-certified residential sanitization and deep kitchen/sofa steam cleaning specialist.',
                'hourly_rate'       => 799.00,
                'price_type'        => 'per_visit',
                'experience_years'  => 7,
                'service_radius_km' => 15,
                'rating_avg'        => 4.98,
                'reviews_count'     => 64,
                'city'              => 'Khar West, Mumbai',
                'address'           => '14th Road, Khar West',
                'latitude'          => 19.07200000,
                'longitude'         => 72.83800000,
                'phone'             => '+91 98200 00202',
                'show_phone'        => 1,
                'avatar'            => '🧹',
                'availability'      => 'available'
            ],
            3 => [
                'id'                => 3,
                'user_id'           => 6,
                'name'              => 'Julian Thorne',
                'business_name'     => 'Thorne Smart Electric & Wiring',
                'profession'        => 'Electrician',
                'bio'               => 'Certified electrician for smart lighting, short-circuit diagnostics, EV chargers, and inverter setups.',
                'hourly_rate'       => 499.00,
                'price_type'        => 'per_visit',
                'experience_years'  => 9,
                'service_radius_km' => 15,
                'rating_avg'        => 4.89,
                'reviews_count'     => 39,
                'city'              => 'Santacruz West, Mumbai',
                'address'           => 'Linking Road, Santacruz West',
                'latitude'          => 19.08200000,
                'longitude'         => 72.84200000,
                'phone'             => '+91 98200 00203',
                'show_phone'        => 1,
                'avatar'            => '⚡',
                'availability'      => 'available'
            ],
            4 => [
                'id'                => 4,
                'user_id'           => 7,
                'name'              => 'Vikram Singhal',
                'business_name'     => 'FrostAir AC Solutions & Repair',
                'profession'        => 'AC Repair',
                'bio'               => 'Expert technician for split and inverter AC gas charging, PCB repair, and jet foam deep cleaning.',
                'hourly_rate'       => 699.00,
                'price_type'        => 'per_visit',
                'experience_years'  => 10,
                'service_radius_km' => 15,
                'rating_avg'        => 4.92,
                'reviews_count'     => 52,
                'city'              => 'Andheri West, Mumbai',
                'address'           => 'Lokhandwala Complex, Andheri West',
                'latitude'          => 19.13000000,
                'longitude'         => 72.82500000,
                'phone'             => '+91 98200 00204',
                'show_phone'        => 1,
                'avatar'            => '❄️',
                'availability'      => 'available'
            ],
            5 => [
                'id'                => 5,
                'user_id'           => 8,
                'name'              => 'Rameshwar Suthar',
                'business_name'     => 'Heritage Woodcraft & Carpentry',
                'profession'        => 'Carpenter',
                'bio'               => 'Custom furniture restoration, modular kitchen hinge repair, and lock replacement master.',
                'hourly_rate'       => 450.00,
                'price_type'        => 'per_hour',
                'experience_years'  => 14,
                'service_radius_km' => 15,
                'rating_avg'        => 4.91,
                'reviews_count'     => 41,
                'city'              => 'Mahim, Mumbai',
                'address'           => 'Lady Jamshedji Road, Mahim',
                'latitude'          => 19.03500000,
                'longitude'         => 72.84200000,
                'phone'             => '+91 98200 00205',
                'show_phone'        => 1,
                'avatar'            => '🪚',
                'availability'      => 'available'
            ],
            6 => [
                'id'                => 6,
                'user_id'           => 9,
                'name'              => 'Imran Qureshi',
                'business_name'     => 'Speedy Auto & Machine Repair',
                'profession'        => 'Mechanic',
                'bio'               => 'On-demand mechanical diagnostics, doorstep battery replacement, and appliance motor servicing.',
                'hourly_rate'       => 549.00,
                'price_type'        => 'per_visit',
                'experience_years'  => 8,
                'service_radius_km' => 15,
                'rating_avg'        => 4.88,
                'reviews_count'     => 34,
                'city'              => 'Dadar West, Mumbai',
                'address'           => 'Gokhale Road, Dadar West',
                'latitude'          => 19.01900000,
                'longitude'         => 72.84100000,
                'phone'             => '+91 98200 00206',
                'show_phone'        => 1,
                'avatar'            => '🚗',
                'availability'      => 'available'
            ]
        ];

        // Compute Haversine distance for each provider
        $results = [];
        foreach ($pool as $p) {
            $dist = $this->calculateHaversineDistance($custLat, $custLng, $p['latitude'], $p['longitude']);
            $p['distance_km'] = round($dist, 2);

            // Filter by 15 km radius
            if ($dist > $radiusKm) {
                continue;
            }

            // Filter by profession
            if ($profession !== 'all') {
                $searchProf = strtolower(str_replace([' ', '-'], '', $profession));
                $pProf = strtolower(str_replace([' ', '-'], '', $p['profession']));
                if (strpos($pProf, $searchProf) === false && strpos($searchProf, $pProf) === false) {
                    continue;
                }
            }

            $results[$p['id']] = $p;
        }

        // Sorting
        if ($sort === 'price_asc') {
            uasort($results, fn($a, $b) => $a['hourly_rate'] <=> $b['hourly_rate']);
        } elseif ($sort === 'price_desc') {
            uasort($results, fn($a, $b) => $b['hourly_rate'] <=> $a['hourly_rate']);
        } elseif ($sort === 'rating') {
            uasort($results, fn($a, $b) => $b['rating_avg'] <=> $a['rating_avg']);
        } else {
            // Default: Nearest distance
            uasort($results, fn($a, $b) => $a['distance_km'] <=> $b['distance_km']);
        }

        return $results;
    }

    /**
     * Standard Haversine distance calculation in kilometers
     */
    private function calculateHaversineDistance(float $lat1, float $lon1, float $lat2, float $lon2): float {
        $earthRadius = 6371.0;
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon / 2) * sin($dLon / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return $earthRadius * $c;
    }
}
