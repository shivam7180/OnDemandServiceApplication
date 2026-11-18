"""
NEARZO Local Development Server
Provides instant local preview on http://localhost:8000
Supports two-sided marketplace (Customer & Service Provider),
Haversine location-based search strictly within 25 km, session cookies,
Google Maps & Leaflet integration, and Indian Rupee (₹) pricing.
"""

import http.server
import socketserver
import os
import re
import math
import json
import urllib.parse
from http.cookies import SimpleCookie

PORT = 8000
BASE_DIR = os.path.dirname(os.path.abspath(__file__))

def calculate_haversine(lat1, lon1, lat2, lon2):
    R = 6371.0 # Earth radius in km
    dlat = math.radians(lat2 - lat1)
    dlon = math.radians(lon2 - lon1)
    a = math.sin(dlat / 2)**2 + math.cos(math.radians(lat1)) * math.cos(math.radians(lat2)) * math.sin(dlon / 2)**2
    c = 2 * math.atan2(math.sqrt(a), math.sqrt(1 - a))
    return round(R * c, 2)

CATALOG_PROVIDERS = [
    {
        'id': 1,
        'name': 'David Sterling',
        'business_name': 'Sterling Master Plumbing & Pipeworks',
        'profession': 'Plumber',
        'bio': 'Licensed master plumber with 12 years of leak detection, sanitary fitting, and geyser installation experience.',
        'hourly_rate': 599.00,
        'price_type': 'per_hour',
        'experience_years': 12,
        'rating_avg': 4.95,
        'reviews_count': 48,
        'city': 'Bandra West, Mumbai',
        'address': 'Shop 12, Hill Road, Bandra West',
        'latitude': 19.0600,
        'longitude': 72.8650,
        'phone': '+91 98200 00201',
        'show_phone': 1,
        'avatar': '👨‍🔧',
        'availability': 'available'
    },
    {
        'id': 2,
        'name': 'Elena Rostova',
        'business_name': 'LuxeClean Botanical Housekeeping',
        'profession': 'Cleaner',
        'bio': 'Eco-certified residential sanitization and deep kitchen/sofa steam cleaning specialist.',
        'hourly_rate': 799.00,
        'price_type': 'per_visit',
        'experience_years': 7,
        'rating_avg': 4.98,
        'reviews_count': 64,
        'city': 'Khar West, Mumbai',
        'address': '14th Road, Khar West',
        'latitude': 19.0720,
        'longitude': 72.8380,
        'phone': '+91 98200 00202',
        'show_phone': 1,
        'avatar': '🧹',
        'availability': 'available'
    },
    {
        'id': 3,
        'name': 'Julian Thorne',
        'business_name': 'Thorne Smart Electric & Wiring',
        'profession': 'Electrician',
        'bio': 'Certified electrician for smart lighting, short-circuit diagnostics, EV chargers, and inverter setups.',
        'hourly_rate': 499.00,
        'price_type': 'per_visit',
        'experience_years': 9,
        'rating_avg': 4.89,
        'reviews_count': 39,
        'city': 'Santacruz West, Mumbai',
        'address': 'Linking Road, Santacruz West',
        'latitude': 19.0820,
        'longitude': 72.8420,
        'phone': '+91 98200 00203',
        'show_phone': 1,
        'avatar': '⚡',
        'availability': 'available'
    },
    {
        'id': 4,
        'name': 'Vikram Singhal',
        'business_name': 'FrostAir AC Solutions & Repair',
        'profession': 'AC Repair',
        'bio': 'Expert technician for split and inverter AC gas charging, PCB repair, and jet foam deep cleaning.',
        'hourly_rate': 699.00,
        'price_type': 'per_visit',
        'experience_years': 10,
        'rating_avg': 4.92,
        'reviews_count': 52,
        'city': 'Andheri West, Mumbai',
        'address': 'Lokhandwala Complex, Andheri West',
        'latitude': 19.1300,
        'longitude': 72.8250,
        'phone': '+91 98200 00204',
        'show_phone': 1,
        'avatar': '❄️',
        'availability': 'available'
    },
    {
        'id': 5,
        'name': 'Rameshwar Suthar',
        'business_name': 'Heritage Woodcraft & Carpentry',
        'profession': 'Carpenter',
        'bio': 'Custom furniture restoration, modular kitchen hinge repair, and lock replacement master.',
        'hourly_rate': 450.00,
        'price_type': 'per_hour',
        'experience_years': 14,
        'rating_avg': 4.91,
        'reviews_count': 41,
        'city': 'Mahim, Mumbai',
        'address': 'Lady Jamshedji Road, Mahim',
        'latitude': 19.0350,
        'longitude': 72.8420,
        'phone': '+91 98200 00205',
        'show_phone': 1,
        'avatar': '🪚',
        'availability': 'available'
    },
    {
        'id': 6,
        'name': 'Imran Qureshi',
        'business_name': 'Speedy Auto & Machine Repair',
        'profession': 'Mechanic',
        'bio': 'On-demand mechanical diagnostics, doorstep battery replacement, and appliance motor servicing.',
        'hourly_rate': 549.00,
        'price_type': 'per_visit',
        'experience_years': 8,
        'rating_avg': 4.88,
        'reviews_count': 34,
        'city': 'Dadar West, Mumbai',
        'address': 'Gokhale Road, Dadar West',
        'latitude': 19.0190,
        'longitude': 72.8410,
        'phone': '+91 98200 00206',
        'show_phone': 1,
        'avatar': '🚗',
        'availability': 'available'
    },
    # Providers located outside 25 km (strictly excluded by Haversine formula)
    {
        'id': 7,
        'name': 'Sanjay Deshmukh',
        'business_name': 'Deccan Solar & Electricals',
        'profession': 'Electrician',
        'bio': 'Industrial solar panel installation and commercial electrical substation repair.',
        'hourly_rate': 899.00,
        'price_type': 'per_visit',
        'experience_years': 15,
        'rating_avg': 4.95,
        'reviews_count': 22,
        'city': 'Pune, Maharashtra',
        'address': 'FC Road, Shivajinagar, Pune',
        'latitude': 18.5204,
        'longitude': 73.8567, # ~120 km away
        'phone': '+91 98200 00207',
        'show_phone': 1,
        'avatar': '⚡',
        'availability': 'available'
    },
    {
        'id': 8,
        'name': 'Mahesh Jadhav',
        'business_name': 'Kalyan Deep Plumbing Services',
        'profession': 'Plumber',
        'bio': 'Commercial pipeline repair and deep water borewell drilling.',
        'hourly_rate': 550.00,
        'price_type': 'per_hour',
        'experience_years': 11,
        'rating_avg': 4.80,
        'reviews_count': 18,
        'city': 'Kalyan East, Maharashtra',
        'address': 'Station Road, Kalyan East',
        'latitude': 19.2403,
        'longitude': 73.1305, # ~36 km away
        'phone': '+91 98200 00208',
        'show_phone': 1,
        'avatar': '👨‍🔧',
        'availability': 'available'
    }
]

def get_auth_state(headers):
    cookie_str = headers.get('Cookie', '')
    cookies = SimpleCookie(cookie_str)
    
    is_authenticated = 'auth_user' in cookies and bool(cookies['auth_user'].value)
    has_active_order = is_authenticated and ('active_order' in cookies and bool(cookies['active_order'].value))
    user_role = cookies['auth_role'].value if 'auth_role' in cookies else 'customer'
    user_name = urllib.parse.unquote(cookies['auth_name'].value) if 'auth_name' in cookies else ''
    user_address = urllib.parse.unquote(cookies['auth_address'].value) if 'auth_address' in cookies else 'Mumbai, Maharashtra'
    user_fullname = urllib.parse.unquote(cookies['auth_fullname'].value) if 'auth_fullname' in cookies else (user_name if user_name else 'Valued Client')
    user_lat = float(cookies['auth_lat'].value) if 'auth_lat' in cookies else 19.0760
    user_lng = float(cookies['auth_lng'].value) if 'auth_lng' in cookies else 72.8777
    user_biz = urllib.parse.unquote(cookies['auth_biz'].value) if 'auth_biz' in cookies else ''
    is_demo = ('demo_account' in cookies and cookies['demo_account'].value == '1')
    
    return is_authenticated, has_active_order, user_role, user_name, user_address, user_fullname, user_lat, user_lng, user_biz, is_demo

def render_php_view(view_relative_path, context=None, auth_state=None):
    if context is None:
        context = {}
    if auth_state is None:
        auth_state = (False, False, 'customer', '', 'Mumbai, Maharashtra', 'Valued Client', 19.0760, 72.8777, '', False)
    
    is_authenticated, has_active_order, user_role, user_name, user_address, user_fullname, user_lat, user_lng, user_biz, is_demo = auth_state
    
    file_path = os.path.join(BASE_DIR, 'app', 'views', view_relative_path)
    if not os.path.exists(file_path):
        return f"<h1>View not found: {view_relative_path}</h1>"
    
    with open(file_path, 'r', encoding='utf-8') as f:
        content = f.read()
    
    # Inline header and footer
    header_path = os.path.join(BASE_DIR, 'app', 'views', 'layouts', 'header.php')
    footer_path = os.path.join(BASE_DIR, 'app', 'views', 'layouts', 'footer.php')
    
    with open(header_path, 'r', encoding='utf-8') as f:
        header = f.read()
    with open(footer_path, 'r', encoding='utf-8') as f:
        footer = f.read()
    
    content = re.sub(r'<\?php\s+require_once\s+ROOT_PATH\s*\.\s*[\'"]/app/views/layouts/header\.php[\'"];\s*\?>', header, content)
    content = re.sub(r'<\?php\s+require_once\s+ROOT_PATH\s*\.\s*[\'"]/app/views/layouts/footer\.php[\'"];\s*\?>', footer, content)
    
    # Context defaults
    page_title = context.get('pageTitle', 'NEARZO | Nearby Services On-Demand')
    location = context.get('location', 'Mumbai')
    selected_cat = context.get('selectedCat', 'all')
    active_page = context.get('activePage', '')
    selected_prof = context.get('selectedProfession', 'all')
    radius_km = str(context.get('radiusKm', 15))
    sort_filter = context.get('sort', 'distance')
    
    # Active page nav tags
    content = content.replace("<?= ($activePage === 'home') ? 'active' : '' ?>", "active" if active_page == 'home' else "")
    content = content.replace("<?= ($activePage === 'services') ? 'active' : '' ?>", "active" if active_page == 'services' else "")
    content = content.replace("<?= ($activePage === 'providers') ? 'active' : '' ?>", "active" if active_page == 'providers' else "")
    content = content.replace("<?= ($activePage === 'about') ? 'active' : '' ?>", "active" if active_page == 'about' else "")
    content = content.replace("<?= ($activePage === 'contact') ? 'active' : '' ?>", "active" if active_page == 'contact' else "")
    content = content.replace("<?= ($activePage === 'track') ? 'active' : '' ?>", "active" if active_page == 'track' else "")
    
    # Replace standard tokens
    content = content.replace("<?= BASE_URL ?>", "")
    content = content.replace("<?= htmlspecialchars($pageTitle ?? 'NEARZO | Nearby Services On-Demand') ?>", page_title)
    content = content.replace("<?= htmlspecialchars($pageTitle ?? 'AuraCraft | Premium On-Demand Services') ?>", page_title)
    content = content.replace("<?= htmlspecialchars($pageTitle ?? 'NEARZO') ?>", page_title)
    content = content.replace("<?= htmlspecialchars($pageTitle ?? 'AuraCraft') ?>", page_title)
    content = content.replace("<?= htmlspecialchars($location) ?>", location)
    content = content.replace("<?= $radiusKm ?>", radius_km)
    content = content.replace("<?= $custLat ?>", "19.0760")
    content = content.replace("<?= $custLng ?>", "72.8777")
    content = content.replace("<?= count($providers) ?>", "6")
    content = content.replace("<?= date('Y') ?>", "2026")
    content = content.replace("<?= date('Y-m-d', strtotime('+1 day')) ?>", "2026-11-20")
    content = content.replace("<?= htmlspecialchars($csrfToken ?? '') ?>", "dev-csrf-token")
    content = content.replace("<?= $booking['provider']['eta'] ?>", "12 mins")
    content = content.replace("<?= $progressWidth ?>", "75%")
    content = content.replace("<?= $provider['avatar'] ?>", "👨‍🔧")

    # Provider identity and metrics: ONLY explicit demo_account cookie activates demo stats
    is_demo_provider = is_demo and (user_name.lower() == 'david')
    is_new_provider = not is_demo_provider

    provider_name = user_fullname if is_new_provider else 'David Sterling'
    provider_biz = (user_biz if user_biz else f"{user_fullname}'s Services") if is_new_provider else 'Sterling Master Plumbing & Heating'
    provider_city = user_address if (is_new_provider and user_address) else 'Mumbai'

    content = content.replace("<?= htmlspecialchars($provider['name']) ?>", provider_name)
    content = content.replace("<?= htmlspecialchars($provider['business_name'] ?? 'Sterling Master Plumbing & Pipeworks') ?>", provider_biz)
    content = content.replace("<?= htmlspecialchars($provider['business'] ?? 'Sterling Master Plumbing & Pipeworks') ?>", provider_biz)
    content = content.replace("<?= htmlspecialchars($provider['business']) ?>", provider_biz)
    content = content.replace("<?= htmlspecialchars($provider['city'] ?? 'Mumbai') ?>", provider_city)
    content = content.replace("<?= $provider['service_radius_km'] ?? 15 ?>", "25")
    content = content.replace("<?= $provider['service_radius_km'] ?? 25 ?>", "25")

    if is_new_provider:
        content = content.replace("<?= (int)($provider['month_earnings'] ?? 0) ?>", "0")
        content = content.replace("<?= (int)($provider['pending_payout'] ?? 0) ?>", "0")
        content = content.replace("<?= (int)($provider['completed_jobs'] ?? 0) ?>", "0")
        content = content.replace("<?= (float)($provider['rating'] ?? 5.0) ?>", "0")
        content = content.replace("<?= ($provider['month_earnings'] ?? 0) > 0 ? '↑ Active cycle' : 'No earnings yet this month' ?>", "No earnings yet this month")
        content = content.replace("<?= ($provider['pending_payout'] ?? 0) > 0 ? 'Direct UPI / NEFT payout: Monday' : '₹0.00 escrow balance' ?>", "₹0.00 escrow balance")
        content = content.replace("<?= ($provider['completed_jobs'] ?? 0) > 0 ? '100% on-time arrival in 25 km' : '0 jobs completed so far' ?>", "0 jobs completed so far")
        content = content.replace("<?= ($provider['reviews_count'] ?? 0) > 0 ? '★ ' . $provider['rating'] . ' (' . $provider['reviews_count'] . ' reviews)' : '★ New Specialist (0 jobs completed)' ?>", "★ New Specialist (0 jobs completed)")
        content = content.replace("<?= $provider['reviews_count'] ?? 48 ?>", "0")
        content = content.replace("<?= number_format($provider['rating_avg'] ?? 4.95, 2) ?>", "0.00")
        content = content.replace("<?= $provider['experience_years'] ?? 12 ?>", "1")
        content = content.replace("<?= number_format($provider['hourly_rate'] ?? 599, 0) ?>", "499")
        content = content.replace("<?= number_format($provider['hourly_rate'] ?? 599, 2) ?>", "499.00")
        content = content.replace("<?= (float)($provider['hourly_rate'] ?? 599) ?>", "499.00")
        content = content.replace("<?= nl2br(htmlspecialchars($provider['bio'] ?? '')) ?>", f"Certified local service specialist providing on-demand repair services across {provider_city}.")

        # Rating card block: pick else branch (New Partner)
        content = re.sub(
            r'<\?php\s+if\s*\(\(\$provider\[\'completed_jobs\'\]\s*\?\?\s*0\)\s*>\s*0\):\s*\?>.*?<\?php\s+else:\s*\?>\s*(.*?)\s*<\?php\s+endif;\s*\?>',
            r'\1',
            content,
            flags=re.DOTALL
        )

        # Incoming requests: pick empty radar state
        content = re.sub(
            r'<\?php\s+if\s*\(empty\(\$pendingRequests\)\):\s*\?>\s*(.*?)\s*<\?php\s+else:\s*\?>.*?<\?php\s+endif;\s*\?>',
            r'\1',
            content,
            flags=re.DOTALL
        )

        # Upcoming jobs: pick empty scheduled appointments state
        content = re.sub(
            r'<\?php\s+if\s*\(empty\(\$upcomingJobs\)\):\s*\?>\s*(.*?)\s*<\?php\s+else:\s*\?>.*?<\?php\s+endif;\s*\?>',
            r'\1',
            content,
            flags=re.DOTALL
        )
    else:
        content = content.replace("<?= (int)($provider['month_earnings'] ?? 0) ?>", "48500")
        content = content.replace("<?= (int)($provider['pending_payout'] ?? 0) ?>", "4850")
        content = content.replace("<?= (int)($provider['completed_jobs'] ?? 0) ?>", "48")
        content = content.replace("<?= (float)($provider['rating'] ?? 5.0) ?>", "4.95")
        content = content.replace("<?= ($provider['month_earnings'] ?? 0) > 0 ? '↑ Active cycle' : 'No earnings yet this month' ?>", "↑ 18% from last month")
        content = content.replace("<?= ($provider['pending_payout'] ?? 0) > 0 ? 'Direct UPI / NEFT payout: Monday' : '₹0.00 escrow balance' ?>", "Direct UPI / NEFT payout: Monday")
        content = content.replace("<?= ($provider['completed_jobs'] ?? 0) > 0 ? '100% on-time arrival in 25 km' : '0 jobs completed so far' ?>", "100% on-time arrival in 25 km")
        content = content.replace("<?= ($provider['reviews_count'] ?? 0) > 0 ? '★ ' . $provider['rating'] . ' (' . $provider['reviews_count'] . ' reviews)' : '★ New Specialist (0 jobs completed)' ?>", "★ 4.95 (48 reviews)")
        content = content.replace("<?= $provider['reviews_count'] ?? 48 ?>", "48")
        content = content.replace("<?= number_format($provider['rating_avg'] ?? 4.95, 2) ?>", "4.95")
        content = content.replace("<?= $provider['experience_years'] ?? 12 ?>", "12")
        content = content.replace("<?= number_format($provider['hourly_rate'] ?? 599, 0) ?>", "599")
        content = content.replace("<?= number_format($provider['hourly_rate'] ?? 599, 2) ?>", "599.00")
        content = content.replace("<?= (float)($provider['hourly_rate'] ?? 599) ?>", "599.00")
        content = content.replace("<?= nl2br(htmlspecialchars($provider['bio'] ?? '')) ?>", "Licensed master craftsman with over 12 years of residential plumbing, leak detection, and sanitary renovation expertise across Mumbai.")

        # Rating card block: pick if branch (4.95 Top Verified Craftsman)
        content = re.sub(
            r'<\?php\s+if\s*\(\(\$provider\[\'completed_jobs\'\]\s*\?\?\s*0\)\s*>\s*0\):\s*\?>\s*(.*?)\s*<\?php\s+else:\s*\?>.*?<\?php\s+endif;\s*\?>',
            r'\1',
            content,
            flags=re.DOTALL
        )

        # Incoming requests: pick else branch (Marcus Vance)
        content = re.sub(
            r'<\?php\s+if\s*\(empty\(\$pendingRequests\)\):\s*\?>.*?<\?php\s+else:\s*\?>\s*(.*?)\s*<\?php\s+endif;\s*\?>',
            r'\1',
            content,
            flags=re.DOTALL
        )

        # Upcoming jobs: pick else branch (Sophia Miller)
        content = re.sub(
            r'<\?php\s+if\s*\(empty\(\$upcomingJobs\)\):\s*\?>.*?<\?php\s+else:\s*\?>\s*(.*?)\s*<\?php\s+endif;\s*\?>',
            r'\1',
            content,
            flags=re.DOTALL
        )

        content = content.replace("<?= $req['id'] ?>", "101")
        content = content.replace("<?= $req['code'] ?>", "BK-89022")
        content = content.replace("<?= htmlspecialchars($req['service']) ?>", "Emergency Pipe Leak & Valve Repair")
        content = content.replace("<?= htmlspecialchars($req['customer']) ?>", "Marcus Vance")
        content = content.replace("<?= $req['phone'] ?>", "+91 98200 45678")
        content = content.replace("<?= htmlspecialchars($req['address']) ?>", "Shop 12, Hill Road, Bandra West, Mumbai")
        content = content.replace("<?= htmlspecialchars($req['notes']) ?>", "Slow drip under kitchen sink. Main valve is under counter.")
        content = content.replace("<?= number_format($req['amount'], 0) ?>", "499")

        content = content.replace("<?= $job['code'] ?>", "BK-89021")
        content = content.replace("<?= htmlspecialchars($job['customer']) ?>", "Sophia Miller")
        content = content.replace("<?= htmlspecialchars($job['service']) ?>", "Luxury Faucet & Fixture Installation")
        content = content.replace("<?= $job['date'] ?>", "Nov 20, 2026 - 10:00 AM")
        content = content.replace("<?= htmlspecialchars($job['address']) ?>", "Flat 402, Green Acres, Bandra West, Mumbai")
        content = content.replace("<?= number_format($job['amount'], 0) ?>", "899")
    # Customer Dashboard dynamic values
    display_user_name = user_name if user_name else 'Valued Client'
    display_address = user_address if user_address else 'Mumbai, Maharashtra'

    content = content.replace("<?= htmlspecialchars(explode(' ', $customer['name'] ?? 'Friend')[0]) ?>", display_user_name)
    content = content.replace("<?= htmlspecialchars(explode(' ', $customer['name'] ?? 'Sophia Miller')[0]) ?>", display_user_name)
    content = content.replace("<?= htmlspecialchars(explode(' ', $customer['name'])[0]) ?>", display_user_name)
    content = content.replace("<?= htmlspecialchars($customer['address'] ?? 'Nearby Location') ?>", display_address)
    content = content.replace("<?= htmlspecialchars($customer['address'] ?? 'Flat 402, Green Acres, Bandra West, Mumbai') ?>", display_address)
    content = content.replace("<?= htmlspecialchars($customer['address']) ?>", display_address)

    # Dynamic KPI counters for Customer Dashboard
    active_count = "1" if has_active_order else "0"
    completed_count = "1" if (user_name == 'Sophia' and not has_active_order) else "0"
    total_spent = "499" if has_active_order else ("899" if completed_count != "0" else "0")
    preferred_count = "1" if (user_name == 'Sophia') else "0"

    content = content.replace("<?= $activeCount ?>", active_count)
    content = content.replace("<?= $completedCount ?>", completed_count)
    content = content.replace("<?= (int)$totalInvested ?>", total_spent)
    content = content.replace("<?= $preferredCount ?>", preferred_count)
    content = content.replace("<?= $activeCount > 0 ? '● 1 In Progress' : 'No active orders' ?>", "● 1 In Progress" if has_active_order else "No active orders")
    content = content.replace("<?= $activeCount > 0 ? 'var(--status-success)' : 'var(--charcoal-muted)' ?>", "var(--status-success)" if has_active_order else "var(--charcoal-muted)")
    content = content.replace("<?= $completedCount > 0 ? '100% Satisfied' : 'Fresh Account' ?>", "100% Satisfied" if completed_count != "0" else "Fresh Account")

    # Render Active Booking block vs Empty state
    if has_active_order:
        content = re.sub(
            r'<\?php\s+if\s*\(!empty\(\$activeBooking\)\):\s*\?>\s*(.*?)\s*<\?php\s+else:\s*\?>.*?<\?php\s+endif;\s*\?>',
            r'\1',
            content,
            flags=re.DOTALL
        )
    else:
        content = re.sub(
            r'<\?php\s+if\s*\(!empty\(\$activeBooking\)\):\s*\?>.*?<\?php\s+else:\s*\?>\s*(.*?)\s*<\?php\s+endif;\s*\?>',
            r'\1',
            content,
            flags=re.DOTALL
        )

    # Render History Table vs Empty state
    if completed_count != "0":
        content = re.sub(
            r'<\?php\s+if\s*\(!empty\(\$pastBookings\)\):\s*\?>\s*(.*?)\s*<\?php\s+else:\s*\?>.*?<\?php\s+endif;\s*\?>',
            r'\1',
            content,
            flags=re.DOTALL
        )
    else:
        content = re.sub(
            r'<\?php\s+if\s*\(!empty\(\$pastBookings\)\):\s*\?>.*?<\?php\s+else:\s*\?>\s*(.*?)\s*<\?php\s+endif;\s*\?>',
            r'\1',
            content,
            flags=re.DOTALL
        )

    content = content.replace("<?= $activeBooking['code'] ?>", "BK-89021")
    content = content.replace("<?= ucfirst($activeBooking['status']) ?>", "In Progress")
    content = content.replace("<?= htmlspecialchars($activeBooking['service']) ?>", "Emergency Pipe Leak & Valve Repair")
    content = content.replace("<?= htmlspecialchars($activeBooking['provider']) ?>", "David Sterling")
    content = content.replace("<?= $activeBooking['date'] ?>", "Today")
    content = content.replace("<?= $activeBooking['time'] ?>", "10:00 AM")
    content = content.replace("<?= number_format($activeBooking['amount'], 0) ?>", "499")

    content = content.replace("<?= htmlspecialchars($booking['code'] ?? 'BK-89021') ?>", "BK-89021")
    content = content.replace("<?= htmlspecialchars($booking['service'] ?? 'Emergency Pipe Leak & Valve Repair') ?>", "Emergency Pipe Leak & Valve Repair")
    content = content.replace("<?= htmlspecialchars($booking['address'] ?? 'Flat 402, Green Acres, Bandra West, Mumbai') ?>", display_address)
    content = content.replace("<?= htmlspecialchars($booking['provider']['name'] ?? 'David Sterling') ?>", "David Sterling")
    content = content.replace("<?= htmlspecialchars($booking['provider']['business'] ?? 'Sterling Master Plumbing') ?>", "Sterling Master Plumbing")
    content = content.replace("<?= $booking['provider']['phone'] ?? '+91 98200 00201' ?>", "+91 98200 00201")
    content = content.replace("<?= $booking['provider']['rating'] ?? '4.95' ?>", "4.95")
    content = content.replace("<?= $booking['provider']['avatar'] ?? '👨‍🔧' ?>", "👨‍🔧")
    content = content.replace("<?= number_format($booking['total_amount'] ?? 499, 0) ?>", "499")
    content = content.replace("<?= number_format($booking['total_amount'] ?? 499, 2) ?>", "499.00")
    content = content.replace("<?= $booking['provider']['current_location'] ?? 'Linking Road, 1.8 km away' ?>", "Linking Road, 1.8 km away")
    content = content.replace("<?= (int)($booking['id'] ?? 1) ?>", "1")
    content = content.replace("<?= (int)($provider['id'] ?? 1) ?>", "1")

    # Stepper tags
    content = content.replace("<?= in_array($status, ['accepted', 'in_progress', 'completed']) ? 'completed' : (($status === 'pending') ? 'active' : '') ?>", "completed")
    content = content.replace("<?= in_array($status, ['accepted', 'in_progress', 'completed']) ? '✓' : '2' ?>", "✓")
    content = content.replace("<?= ($status === 'completed') ? 'completed' : (($status === 'in_progress') ? 'active' : '') ?>", "active")
    content = content.replace("<?= ($status === 'completed') ? '✓' : '🚗' ?>", "🚗")
    content = content.replace("<?= ($status === 'completed') ? 'completed' : '' ?>", "")
    content = content.replace("<?= ($status === 'completed') ? '✓' : '🔨' ?>", "🔨")
    content = content.replace("<?= ($status === 'completed') ? 'active' : '' ?>", "")
    content = content.replace("<?= ($status === 'completed') ? '✓' : '5' ?>", "5")
    content = content.replace("<?= in_array($status, ['pending', 'accepted', 'in_progress']) ? 'Active Escrow Protection' : 'Completed' ?>", "Active Escrow Protection")
    content = content.replace("<?= in_array($status, ['pending', 'accepted']) ? 'true' : 'false' ?>", "true")

    # Cleanly render navbar auth section based on actual authentication state
    dash_url = "/customer/dashboard"
    if user_role == 'provider':
        dash_url = "/provider/dashboard"
    elif user_role == 'admin':
        dash_url = "/admin/dashboard"

    # Replace Track Live Order navbar visibility
    nav_track_display = "block" if (is_authenticated and has_active_order) else "none"
    content = re.sub(
        r'<li id="navTrackItem"[^>]*>.*?</li>',
        f'''<li id="navTrackItem" style="display: {nav_track_display};">
            <a href="/booking/track" class="nav-link" style="color: var(--terracotta); font-weight: 700; display: flex; align-items: center; gap: 6px;">
              <span style="width: 8px; height: 8px; border-radius: 50%; background: #4ADE80; box-shadow: 0 0 8px #4ADE80; display: inline-block;"></span>
              <span>Track Live Order</span>
            </a>
          </li>''',
        content,
        flags=re.DOTALL
    )

    # Replace Nav Actions section
    header_track_display = "inline-flex" if has_active_order else "none"
    nav_display_name = user_name if user_name else 'Account'
    if is_authenticated:
        actions_html = f'''<div class="nav-actions">
          <a href="/booking/track" id="headerTrackBtn" class="btn btn-secondary btn-sm" style="display: {header_track_display}; align-items: center; gap: 6px; padding: 8px 14px; font-size: 0.85rem;" title="Track Live Order">
            <span style="width: 8px; height: 8px; border-radius: 50%; background: #FFFFFF; animation: pulseGlow 1.5s infinite;"></span>
            <span>Live Tracker</span>
          </a>
          <button type="button" class="btn btn-sage btn-sm" style="padding: 8px 12px; position: relative;" onclick="Nearzo.showToast('Specialist David Sterling has received your request', 'info')" title="Notifications">
            🔔 <span style="position: absolute; top: -4px; right: -4px; background: var(--terracotta); color: #FFF; width: 18px; height: 18px; border-radius: 50%; font-size: 0.65rem; display: flex; align-items: center; justify-content: center; font-weight: 700;">1</span>
          </button>
          <a href="{dash_url}" class="btn btn-sage btn-sm">
            <span>👤 {nav_display_name}</span>
            <span class="badge badge-terracotta" style="font-size: 0.7rem; padding: 2px 8px;">{user_role.capitalize()}</span>
          </a>
          <a href="/auth/logout" class="btn btn-outline btn-sm">Sign Out</a>
          <button class="mobile-toggle" id="mobileMenuToggle" aria-label="Toggle navigation">☰</button>
        </div>'''
    else:
        actions_html = '''<div class="nav-actions">
          <a href="/auth/login" class="btn btn-outline btn-sm">Sign In</a>
          <button type="button" class="btn btn-primary btn-sm" onclick="Nearzo.openModal('roleChooseModal')">Get Started</button>
          <button class="mobile-toggle" id="mobileMenuToggle" aria-label="Toggle navigation">☰</button>
        </div>'''

    content = re.sub(r'<div class="nav-actions">.*?<button class="mobile-toggle"[^>]*>☰</button>\s*</div>', actions_html, content, flags=re.DOTALL)

    # Boolean flags for JS
    content = content.replace("<?= $currentUser ? 'true' : 'false' ?>", "true" if is_authenticated else "false")
    content = content.replace("<?= $hasActiveOrder ? 'true' : 'false' ?>", "true" if has_active_order else "false")
    content = content.replace("<?= $dashUrl ?>", dash_url)

    # Providers Marketplace Directory Dynamic 25 km Haversine Rendering
    if view_relative_path == 'providers/index.php':
        cust_lat = float(context.get('custLat', user_lat))
        cust_lng = float(context.get('custLng', user_lng))
        max_radius = min(25.0, float(context.get('radiusKm', 25.0)))
        selected_prof = context.get('selectedProfession', 'all')
        sort_by = context.get('sort', 'distance')

        matched_providers = []
        for p in CATALOG_PROVIDERS:
            d = calculate_haversine(cust_lat, cust_lng, p['latitude'], p['longitude'])
            # STRICT FILTER: Provider must be strictly within 25 km (or selected <= 25 km)
            if d <= max_radius:
                if selected_prof != 'all' and selected_prof.lower() not in p['profession'].lower():
                    continue
                p_item = dict(p)
                p_item['distance_km'] = d
                matched_providers.append(p_item)

        # Sort
        if sort_by == 'price_asc':
            matched_providers.sort(key=lambda x: x['hourly_rate'])
        elif sort_by == 'price_desc':
            matched_providers.sort(key=lambda x: x['hourly_rate'], reverse=True)
        elif sort_by == 'rating':
            matched_providers.sort(key=lambda x: x['rating_avg'], reverse=True)
        else:
            matched_providers.sort(key=lambda x: x['distance_km'])

        # Generate HTML cards
        cards_html = ""
        for p in matched_providers:
            price_suffix = '/ visit' if p['price_type'] == 'per_visit' else ('fixed' if p['price_type'] == 'fixed' else '/ hr')
            cards_html += f'''
              <div class="card card-hoverable tilt-card reveal-on-scroll" style="display: flex; flex-direction: column; justify-content: space-between; border-radius: 24px; border: 1px solid var(--border-light);">
                <div>
                  <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 16px;">
                    <div style="display: flex; gap: 14px; align-items: center;">
                      <div style="width: 58px; height: 58px; border-radius: 50%; background: var(--soft-sage); font-size: 1.8rem; display: flex; align-items: center; justify-content: center; box-shadow: var(--shadow-xs);">
                        {p['avatar']}
                      </div>
                      <div>
                        <h4 style="margin: 0; font-size: 1.15rem; color: var(--charcoal);">{p['name']}</h4>
                        <span class="badge badge-sage" style="font-size: 0.72rem; padding: 2px 8px; margin-top: 4px; display: inline-block;">
                          {p['profession']}
                        </span>
                      </div>
                    </div>

                    <div style="text-align: right;">
                      <span class="badge badge-terracotta" style="font-size: 0.78rem; font-weight: 700;">
                        📍 {p['distance_km']} km
                      </span>
                      <div style="font-size: 0.72rem; color: var(--status-success); font-weight: 600; margin-top: 4px;">
                        ● Available Now
                      </div>
                    </div>
                  </div>

                  <div style="font-weight: 700; color: var(--forest-green); font-size: 0.98rem; margin-bottom: 8px;">
                    {p['business_name']}
                  </div>
                  <p style="font-size: 0.88rem; color: var(--charcoal-light); margin-bottom: 18px; line-height: 1.5;">
                    {p['bio']}
                  </p>

                  <div style="background: var(--bg-muted); border-radius: 12px; padding: 10px 14px; display: flex; justify-content: space-between; align-items: center; margin-bottom: 18px;">
                    <div>
                      <span style="color: #D97706; font-weight: 700;">★ {p['rating_avg']:.2f}</span>
                      <span style="font-size: 0.8rem; color: var(--charcoal-muted);"> ({p['reviews_count']} reviews)</span>
                    </div>
                    <div style="font-size: 0.82rem; font-weight: 600; color: var(--forest-green);">
                      {p['experience_years']} yrs experience
                    </div>
                  </div>
                </div>

                <div>
                  <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 14px; padding-top: 12px; border-top: 1px solid var(--border-light);">
                    <div>
                      <span style="font-size: 0.78rem; color: var(--charcoal-muted); display: block;">Service Price:</span>
                      <span style="font-size: 1.35rem; font-weight: 800; color: var(--forest-green);">
                        ₹{int(p['hourly_rate'])}
                      </span>
                      <span style="font-size: 0.82rem; color: var(--charcoal-muted);">
                        {price_suffix}
                      </span>
                    </div>
                    <a href="tel:{p['phone']}" class="btn btn-sage btn-sm" style="padding: 8px 12px;" title="Call Specialist Directly">
                      📞 Call
                    </a>
                  </div>

                  <div style="display: flex; gap: 10px;">
                    <a href="/providers/detail/{p['id']}" class="btn btn-primary" style="flex: 1; text-align: center; padding: 11px;">
                      Book Specialist →
                    </a>
                  </div>
                </div>
              </div>'''

        content = re.sub(
            r'<\?php\s+foreach\s*\(\$providers\s+as\s+\$p\):\s*\?>.*?<\?php\s+endforeach;\s*\?>',
            cards_html,
            content,
            flags=re.DOTALL
        )

        map_items = [{
            'id': p['id'],
            'business_name': p['business_name'],
            'profession': p['profession'],
            'hourly_rate': p['hourly_rate'],
            'rating_avg': p['rating_avg'],
            'distance_km': p['distance_km'],
            'lat': p['latitude'],
            'lng': p['longitude'],
            'profile_url': f'/providers/detail/{p["id"]}'
        } for p in matched_providers]

        content = re.sub(
            r'const\s+providersMapData\s*=\s*<\?.*?\?>;',
            f'const providersMapData = {json.dumps(map_items)};',
            content,
            flags=re.DOTALL
        )
        content = content.replace("<?= count($providers) ?>", str(len(matched_providers)))
        content = content.replace("<?= $radiusKm ?>", str(int(max_radius)))
        content = content.replace("<?= $custLat ?>", str(cust_lat))
        content = content.replace("<?= $custLng ?>", str(cust_lng))

    # Clean remaining unmatched PHP tags safely
    content = re.sub(r'<\?php\s*if\s*\([^)]*\)\s*:\s*\?>', '', content)
    content = re.sub(r'<\?php\s*else\s*:\s*\?>', '', content)
    content = re.sub(r'<\?php\s*endif;\s*\?>', '', content)
    content = re.sub(r'<\?php\s*foreach\s*\([^)]*\)\s*:\s*\?>', '', content)
    content = re.sub(r'<\?php\s*endforeach;\s*\?>', '', content)
    content = re.sub(r'<\?(=|php).*?\?>', '', content, flags=re.DOTALL)
    
    return content

class AuraHandler(http.server.SimpleHTTPRequestHandler):
    def translate_path(self, path):
        clean_path = path.split('?')[0].split('#')[0]
        if clean_path.startswith('/public/'):
            return os.path.join(BASE_DIR, clean_path.lstrip('/'))
        return super().translate_path(path)
    
    def do_GET(self):
        auth_state = get_auth_state(self.headers)
        url_parts = urllib.parse.urlparse(self.path)
        path = url_parts.path.strip('/')
        
        # Logout
        if path == 'auth/logout':
            self.send_response(303)
            self.send_header('Set-Cookie', 'auth_user=; Path=/; Max-Age=0')
            self.send_header('Set-Cookie', 'auth_role=; Path=/; Max-Age=0')
            self.send_header('Set-Cookie', 'auth_name=; Path=/; Max-Age=0')
            self.send_header('Set-Cookie', 'auth_fullname=; Path=/; Max-Age=0')
            self.send_header('Set-Cookie', 'auth_biz=; Path=/; Max-Age=0')
            self.send_header('Set-Cookie', 'auth_address=; Path=/; Max-Age=0')
            self.send_header('Set-Cookie', 'auth_email=; Path=/; Max-Age=0')
            self.send_header('Set-Cookie', 'auth_lat=; Path=/; Max-Age=0')
            self.send_header('Set-Cookie', 'auth_lng=; Path=/; Max-Age=0')
            self.send_header('Set-Cookie', 'active_order=; Path=/; Max-Age=0')
            self.send_header('Location', '/')
            self.end_headers()
            return

        routes = {
            '': ('home/index.php', {'activePage': 'home', 'pageTitle': 'NEARZO | Nearby Services On-Demand'}),
            'home': ('home/index.php', {'activePage': 'home'}),
            'home/about': ('home/about.php', {'activePage': 'about', 'pageTitle': 'About Us | NEARZO'}),
            'home/contact': ('home/contact.php', {'activePage': 'contact', 'pageTitle': 'Contact Concierge | NEARZO'}),
            'services': ('services/index.php', {'activePage': 'services', 'pageTitle': 'Curated Disciplines | NEARZO'}),
            'providers': ('providers/index.php', {'activePage': 'providers', 'pageTitle': 'Find Specialists within 25 km | NEARZO'}),
            'booking/track': ('booking/track.php', {'activePage': 'track', 'pageTitle': 'Live Appointment Tracker | NEARZO'}),
            'auth/login': ('auth/login.php', {'activePage': 'auth', 'pageTitle': 'Sign In | NEARZO'}),
            'auth/register': ('auth/register_choose.php', {'activePage': 'auth', 'pageTitle': 'Choose Account Type | NEARZO'}),
            'auth/choose': ('auth/register_choose.php', {'activePage': 'auth', 'pageTitle': 'Choose Account Type | NEARZO'}),
            'auth/registerCustomer': ('auth/register_customer.php', {'activePage': 'auth', 'pageTitle': 'Customer Membership | NEARZO'}),
            'auth/registerProvider': ('auth/register_provider.php', {'activePage': 'auth', 'pageTitle': 'Partner Network | NEARZO'}),
            'customer/dashboard': ('customer/dashboard.php', {'activePage': 'customer', 'pageTitle': 'Customer Portal | NEARZO'}),
            'provider/dashboard': ('provider/dashboard.php', {'activePage': 'provider', 'pageTitle': 'Partner Console | NEARZO'}),
            'admin/dashboard': ('admin/dashboard.php', {'activePage': 'admin', 'pageTitle': 'Super Admin | NEARZO'})
        }
        
        if path.startswith('providers/detail'):
            html = render_php_view('providers/detail.php', {'activePage': 'providers', 'pageTitle': 'Specialist Profile | NEARZO'}, auth_state)
            self.send_response(200)
            self.send_header('Content-type', 'text/html; charset=utf-8')
            self.end_headers()
            self.wfile.write(html.encode('utf-8'))
            return

        if path == 'providers' or (path.startswith('providers') and not path.startswith('providers/detail')):
            is_authenticated, has_active_order, user_role, user_name, user_address, user_fullname, user_lat, user_lng, *rest = auth_state
            query_params = urllib.parse.parse_qs(url_parts.query)
            c_lat = float(query_params.get('lat', [user_lat])[0]) if query_params.get('lat') else user_lat
            c_lng = float(query_params.get('lng', [user_lng])[0]) if query_params.get('lng') else user_lng
            c_rad = min(25.0, float(query_params.get('radius', [25.0])[0]))
            c_prof = query_params.get('profession', ['all'])[0]
            c_sort = query_params.get('sort', ['distance'])[0]
            c_loc = query_params.get('location', ['Mumbai'])[0]

            ctx = {
                'activePage': 'providers',
                'pageTitle': 'Find Specialists within 25 km | NEARZO',
                'custLat': c_lat,
                'custLng': c_lng,
                'radiusKm': c_rad,
                'selectedProfession': c_prof,
                'sort': c_sort,
                'location': c_loc
            }
            html = render_php_view('providers/index.php', ctx, auth_state)
            self.send_response(200)
            self.send_header('Content-type', 'text/html; charset=utf-8')
            self.end_headers()
            self.wfile.write(html.encode('utf-8'))
            return
            
        if path.startswith('booking/track'):
            html = render_php_view('booking/track.php', {'activePage': 'track', 'pageTitle': 'Live Booking Tracker | NEARZO'}, auth_state)
            self.send_response(200)
            self.send_header('Content-type', 'text/html; charset=utf-8')
            self.end_headers()
            self.wfile.write(html.encode('utf-8'))
            return

        if path in routes:
            view_file, ctx = routes[path]
            html = render_php_view(view_file, ctx, auth_state)
            self.send_response(200)
            self.send_header('Content-type', 'text/html; charset=utf-8')
            self.end_headers()
            self.wfile.write(html.encode('utf-8'))
            return

        return super().do_GET()

    def do_POST(self):
        length = int(self.headers.get('Content-Length', 0))
        post_data = self.rfile.read(length).decode('utf-8') if length > 0 else ''
        params = urllib.parse.parse_qs(post_data)
        
        self.send_response(303)
        
        if 'loginSubmit' in self.path:
            email = params.get('email', [''])[0].strip().lower()
            role = params.get('role', ['customer'])[0].strip().lower()

            self.send_header('Set-Cookie', 'auth_user=1; Path=/')
            if 'admin' in email or 'admin' in post_data:
                self.send_header('Set-Cookie', 'auth_role=admin; Path=/')
                self.send_header('Set-Cookie', 'auth_name=Admin; Path=/')
                self.send_header('Set-Cookie', 'auth_fullname=System%20Admin; Path=/')
                self.send_header('Set-Cookie', 'auth_address=HQ%20Control%20Room; Path=/')
                self.send_header('Location', '/admin/dashboard')
            elif 'sterling' in email or 'david' in email:
                self.send_header('Set-Cookie', 'auth_role=provider; Path=/')
                self.send_header('Set-Cookie', 'auth_name=David; Path=/')
                self.send_header('Set-Cookie', 'auth_fullname=David%20Sterling; Path=/')
                self.send_header('Set-Cookie', 'auth_biz=Sterling%20Master%20Plumbing%20%26%20Heating; Path=/')
                self.send_header('Set-Cookie', 'auth_address=Bandra%20West%2C%20Mumbai; Path=/')
                self.send_header('Set-Cookie', 'prompt_avail=1; Path=/')
                self.send_header('Location', '/provider/dashboard')
            elif role == 'provider' or 'provider' in email:
                current_cookies = SimpleCookie(self.headers.get('Cookie', ''))
                saved_name = current_cookies.get('auth_name')
                saved_full = current_cookies.get('auth_fullname')
                saved_biz = current_cookies.get('auth_biz')
                provider_first = urllib.parse.unquote(saved_name.value) if (saved_name and saved_name.value and saved_name.value != 'David') else (email.split('@')[0].capitalize() if email else 'Provider')
                provider_full = urllib.parse.unquote(saved_full.value) if (saved_full and saved_full.value and 'Sterling' not in saved_full.value) else provider_first
                provider_biz_val = urllib.parse.unquote(saved_biz.value) if (saved_biz and saved_biz.value) else f"{provider_full}'s Services"
                self.send_header('Set-Cookie', 'auth_role=provider; Path=/')
                self.send_header('Set-Cookie', f'auth_name={urllib.parse.quote(provider_first)}; Path=/')
                self.send_header('Set-Cookie', f'auth_fullname={urllib.parse.quote(provider_full)}; Path=/')
                self.send_header('Set-Cookie', f'auth_biz={urllib.parse.quote(provider_biz_val)}; Path=/')
                self.send_header('Set-Cookie', 'demo_account=; Path=/; Max-Age=0')
                self.send_header('Set-Cookie', 'prompt_avail=1; Path=/')
                self.send_header('Location', '/provider/dashboard')
            elif 'sophia' in email:
                self.send_header('Set-Cookie', 'auth_role=customer; Path=/')
                self.send_header('Set-Cookie', 'auth_name=Sophia; Path=/')
                self.send_header('Set-Cookie', 'auth_fullname=Sophia%20Miller; Path=/')
                self.send_header('Set-Cookie', 'auth_address=Flat%20402%2C%20Green%20Acres%2C%20Bandra%20West%2C%20Mumbai; Path=/')
                self.send_header('Set-Cookie', 'active_order=BK-89021; Path=/')
                self.send_header('Location', '/customer/dashboard')
            else:
                current_cookies = SimpleCookie(self.headers.get('Cookie', ''))
                saved_name = current_cookies.get('auth_name')
                user_first = urllib.parse.unquote(saved_name.value) if saved_name and saved_name.value and saved_name.value != 'Sophia' else (email.split('@')[0].capitalize() if email else 'Customer')
                self.send_header('Set-Cookie', 'auth_role=customer; Path=/')
                self.send_header('Set-Cookie', f'auth_name={urllib.parse.quote(user_first)}; Path=/')
                self.send_header('Location', '/customer/dashboard')

        elif 'registerCustomerSubmit' in self.path:
            full_name = params.get('full_name', ['Customer'])[0].strip()
            first_name = full_name.split()[0] if full_name else 'Customer'
            address = params.get('address', [''])[0].strip()
            city = params.get('city', ['Mumbai'])[0].strip()
            full_addr = f"{address}, {city}" if address else (city if city else "Mumbai, India")
            email = params.get('email', [''])[0].strip()
            lat = params.get('latitude', ['19.07600000'])[0].strip()
            lng = params.get('longitude', ['72.87770000'])[0].strip()

            self.send_header('Set-Cookie', 'auth_user=1; Path=/')
            self.send_header('Set-Cookie', 'auth_role=customer; Path=/')
            self.send_header('Set-Cookie', f'auth_name={urllib.parse.quote(first_name)}; Path=/')
            self.send_header('Set-Cookie', f'auth_fullname={urllib.parse.quote(full_name)}; Path=/')
            self.send_header('Set-Cookie', f'auth_address={urllib.parse.quote(full_addr)}; Path=/')
            self.send_header('Set-Cookie', f'auth_email={urllib.parse.quote(email)}; Path=/')
            self.send_header('Set-Cookie', f'auth_lat={urllib.parse.quote(lat)}; Path=/')
            self.send_header('Set-Cookie', f'auth_lng={urllib.parse.quote(lng)}; Path=/')
            self.send_header('Set-Cookie', 'active_order=; Path=/; Max-Age=0')
            self.send_header('Location', '/customer/dashboard')

        elif 'registerProviderSubmit' in self.path:
            full_name = params.get('full_name', ['Provider'])[0].strip()
            first_name = full_name.split()[0] if full_name else 'Provider'
            biz_name = params.get('business_name', [f"{full_name}'s Services"])[0].strip()
            city = params.get('city', ['Mumbai'])[0].strip()

            self.send_header('Set-Cookie', 'auth_user=1; Path=/')
            self.send_header('Set-Cookie', 'auth_role=provider; Path=/')
            self.send_header('Set-Cookie', f'auth_name={urllib.parse.quote(first_name)}; Path=/')
            self.send_header('Set-Cookie', f'auth_fullname={urllib.parse.quote(full_name)}; Path=/')
            self.send_header('Set-Cookie', f'auth_biz={urllib.parse.quote(biz_name)}; Path=/')
            self.send_header('Set-Cookie', f'auth_address={urllib.parse.quote(city)}; Path=/')
            self.send_header('Set-Cookie', 'active_order=; Path=/; Max-Age=0')
            self.send_header('Set-Cookie', 'demo_account=; Path=/; Max-Age=0')
            self.send_header('Set-Cookie', 'prompt_avail=1; Path=/')
            self.send_header('Location', '/provider/dashboard')

        elif 'create' in self.path:
            # Service booking submitted by customer!
            self.send_header('Set-Cookie', 'auth_user=1; Path=/')
            self.send_header('Set-Cookie', 'auth_role=customer; Path=/')
            self.send_header('Set-Cookie', 'active_order=BK-89021; Path=/')
            self.send_header('Location', '/booking/track')

        elif 'cancel' in self.path:
            # Order cancelled
            self.send_header('Set-Cookie', 'active_order=; Path=/; Max-Age=0')
            self.send_header('Location', '/customer/dashboard')

        else:
            self.send_header('Location', '/')
            
        self.end_headers()

if __name__ == '__main__':
    with socketserver.TCPServer(("", PORT), AuraHandler) as httpd:
        print(f"NEARZO server listening at http://localhost:{PORT}")
        httpd.serve_forever()
