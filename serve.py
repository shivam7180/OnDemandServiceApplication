"""
NEARZO Local Development Server
Provides instant local preview on http://localhost:8000
Supports two-sided marketplace (Customer & Service Provider),
Haversine location-based search within 15 km, session cookies,
Google Maps & Leaflet integration, and Indian Rupee (₹) pricing.
"""

import http.server
import socketserver
import os
import re
import urllib.parse
from http.cookies import SimpleCookie

PORT = 8000
BASE_DIR = os.path.dirname(os.path.abspath(__file__))

def get_auth_state(headers):
    cookie_str = headers.get('Cookie', '')
    cookies = SimpleCookie(cookie_str)
    
    is_authenticated = 'auth_user' in cookies and bool(cookies['auth_user'].value)
    has_active_order = is_authenticated and ('active_order' in cookies and bool(cookies['active_order'].value))
    user_role = cookies['auth_role'].value if 'auth_role' in cookies else 'customer'
    user_name = urllib.parse.unquote(cookies['auth_name'].value) if 'auth_name' in cookies else ''
    user_address = urllib.parse.unquote(cookies['auth_address'].value) if 'auth_address' in cookies else 'Mumbai, Maharashtra'
    user_fullname = urllib.parse.unquote(cookies['auth_fullname'].value) if 'auth_fullname' in cookies else (user_name if user_name else 'Valued Client')
    
    return is_authenticated, has_active_order, user_role, user_name, user_address, user_fullname

def render_php_view(view_relative_path, context=None, auth_state=None):
    if context is None:
        context = {}
    if auth_state is None:
        auth_state = (False, False, 'customer', '', 'Mumbai, Maharashtra', 'Valued Client')
    
    is_authenticated, has_active_order, user_role, user_name, user_address, user_fullname = auth_state
    
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
    content = content.replace("<?= htmlspecialchars($provider['name']) ?>", "David Sterling")
    content = content.replace("<?= htmlspecialchars($provider['business_name'] ?? 'Sterling Master Plumbing & Pipeworks') ?>", "Sterling Master Plumbing & Pipeworks")
    content = content.replace("<?= htmlspecialchars($provider['business'] ?? 'Sterling Master Plumbing & Pipeworks') ?>", "Sterling Master Plumbing & Pipeworks")
    content = content.replace("<?= htmlspecialchars($provider['city'] ?? 'Mumbai') ?>", "Mumbai")
    content = content.replace("<?= $provider['service_radius_km'] ?? 15 ?>", "15")
    content = content.replace("<?= number_format($provider['rating_avg'] ?? 4.95, 2) ?>", "4.95")
    content = content.replace("<?= $provider['reviews_count'] ?? 48 ?>", "48")
    content = content.replace("<?= $provider['experience_years'] ?? 12 ?>", "12")
    content = content.replace("<?= number_format($provider['hourly_rate'] ?? 599, 0) ?>", "599")
    content = content.replace("<?= number_format($provider['hourly_rate'] ?? 599, 2) ?>", "599.00")
    content = content.replace("<?= (float)($provider['hourly_rate'] ?? 599) ?>", "599.00")
    content = content.replace("<?= nl2br(htmlspecialchars($provider['bio'] ?? '')) ?>", "Licensed master craftsman with over 12 years of residential plumbing, leak detection, and sanitary renovation expertise across Mumbai.")
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
            self.send_header('Set-Cookie', 'auth_address=; Path=/; Max-Age=0')
            self.send_header('Set-Cookie', 'auth_email=; Path=/; Max-Age=0')
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
            'providers': ('providers/index.php', {'activePage': 'providers', 'pageTitle': 'Find Specialists within 15 km | NEARZO'}),
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
            elif role == 'provider' or 'provider' in email or 'sterling' in email or 'david' in email:
                self.send_header('Set-Cookie', 'auth_role=provider; Path=/')
                self.send_header('Set-Cookie', 'auth_name=David; Path=/')
                self.send_header('Set-Cookie', 'auth_fullname=David%20Sterling; Path=/')
                self.send_header('Set-Cookie', 'auth_address=Bandra%20West%2C%20Mumbai; Path=/')
                self.send_header('Set-Cookie', 'prompt_avail=1; Path=/')
                self.send_header('Location', '/provider/dashboard')
            elif 'sophia' in email:
                # Sophia is the pre-configured demo customer account
                self.send_header('Set-Cookie', 'auth_role=customer; Path=/')
                self.send_header('Set-Cookie', 'auth_name=Sophia; Path=/')
                self.send_header('Set-Cookie', 'auth_fullname=Sophia%20Miller; Path=/')
                self.send_header('Set-Cookie', 'auth_address=Flat%20402%2C%20Green%20Acres%2C%20Bandra%20West%2C%20Mumbai; Path=/')
                self.send_header('Set-Cookie', 'active_order=BK-89021; Path=/')
                self.send_header('Location', '/customer/dashboard')
            else:
                # Custom registered customer login
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

            self.send_header('Set-Cookie', 'auth_user=1; Path=/')
            self.send_header('Set-Cookie', 'auth_role=customer; Path=/')
            self.send_header('Set-Cookie', f'auth_name={urllib.parse.quote(first_name)}; Path=/')
            self.send_header('Set-Cookie', f'auth_fullname={urllib.parse.quote(full_name)}; Path=/')
            self.send_header('Set-Cookie', f'auth_address={urllib.parse.quote(full_addr)}; Path=/')
            self.send_header('Set-Cookie', f'auth_email={urllib.parse.quote(email)}; Path=/')
            # CLEAR active_order so newly registered customer starts completely fresh with 0 booked services!
            self.send_header('Set-Cookie', 'active_order=; Path=/; Max-Age=0')
            self.send_header('Location', '/customer/dashboard')

        elif 'registerProviderSubmit' in self.path:
            full_name = params.get('full_name', ['Provider'])[0].strip()
            first_name = full_name.split()[0] if full_name else 'Provider'
            biz_name = params.get('business_name', [full_name])[0].strip()
            city = params.get('city', ['Mumbai'])[0].strip()

            self.send_header('Set-Cookie', 'auth_user=1; Path=/')
            self.send_header('Set-Cookie', 'auth_role=provider; Path=/')
            self.send_header('Set-Cookie', f'auth_name={urllib.parse.quote(first_name)}; Path=/')
            self.send_header('Set-Cookie', f'auth_fullname={urllib.parse.quote(full_name)}; Path=/')
            self.send_header('Set-Cookie', f'auth_biz={urllib.parse.quote(biz_name)}; Path=/')
            self.send_header('Set-Cookie', f'auth_address={urllib.parse.quote(city)}; Path=/')
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
