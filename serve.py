"""
AuraCraft Local Development Server
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
    user_name = cookies['auth_name'].value if 'auth_name' in cookies else 'Sophia'
    
    return is_authenticated, has_active_order, user_role, user_name

def render_php_view(view_relative_path, context=None, auth_state=None):
    if context is None:
        context = {}
    if auth_state is None:
        auth_state = (False, False, 'customer', 'Sophia')
    
    is_authenticated, has_active_order, user_role, user_name = auth_state
    
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
    page_title = context.get('pageTitle', 'AuraCraft | Premium On-Demand Services')
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
    content = content.replace("<?= htmlspecialchars($pageTitle ?? 'AuraCraft | Premium On-Demand Services') ?>", page_title)
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
    content = content.replace("<?= htmlspecialchars(explode(' ', $customer['name'] ?? 'Sophia Miller')[0]) ?>", user_name)
    content = content.replace("<?= htmlspecialchars($customer['address'] ?? 'Flat 402, Green Acres, Bandra West, Mumbai') ?>", "Flat 402, Green Acres, Bandra West, Mumbai")
    content = content.replace("<?= htmlspecialchars($booking['code'] ?? 'BK-89021') ?>", "BK-89021")
    content = content.replace("<?= htmlspecialchars($booking['service'] ?? 'Emergency Pipe Leak & Valve Repair') ?>", "Emergency Pipe Leak & Valve Repair")
    content = content.replace("<?= htmlspecialchars($booking['address'] ?? 'Flat 402, Green Acres, Bandra West, Mumbai') ?>", "Flat 402, Green Acres, Bandra West, Mumbai")
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
    if is_authenticated:
        actions_html = f'''<div class="nav-actions">
          <a href="/booking/track" id="headerTrackBtn" class="btn btn-secondary btn-sm" style="display: {header_track_display}; align-items: center; gap: 6px; padding: 8px 14px; font-size: 0.85rem;" title="Track Live Order">
            <span style="width: 8px; height: 8px; border-radius: 50%; background: #FFFFFF; animation: pulseGlow 1.5s infinite;"></span>
            <span>Live Tracker</span>
          </a>
          <button type="button" class="btn btn-sage btn-sm" style="padding: 8px 12px; position: relative;" onclick="AuraCraft.showToast('Specialist David Sterling has received your request', 'info')" title="Notifications">
            🔔 <span style="position: absolute; top: -4px; right: -4px; background: var(--terracotta); color: #FFF; width: 18px; height: 18px; border-radius: 50%; font-size: 0.65rem; display: flex; align-items: center; justify-content: center; font-weight: 700;">1</span>
          </button>
          <a href="{dash_url}" class="btn btn-sage btn-sm">
            <span>👤 {user_name}</span>
            <span class="badge badge-terracotta" style="font-size: 0.7rem; padding: 2px 8px;">{user_role.capitalize()}</span>
          </a>
          <a href="/auth/logout" class="btn btn-outline btn-sm">Sign Out</a>
          <button class="mobile-toggle" id="mobileMenuToggle" aria-label="Toggle navigation">☰</button>
        </div>'''
    else:
        actions_html = '''<div class="nav-actions">
          <a href="/auth/login" class="btn btn-outline btn-sm">Sign In</a>
          <button type="button" class="btn btn-primary btn-sm" onclick="AuraCraft.openModal('roleChooseModal')">Get Started</button>
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
            self.send_header('Set-Cookie', 'active_order=; Path=/; Max-Age=0')
            self.send_header('Location', '/')
            self.end_headers()
            return

        routes = {
            '': ('home/index.php', {'activePage': 'home', 'pageTitle': 'AuraCraft | Premium On-Demand Home Services'}),
            'home': ('home/index.php', {'activePage': 'home'}),
            'home/about': ('home/about.php', {'activePage': 'about', 'pageTitle': 'About Us | AuraCraft'}),
            'home/contact': ('home/contact.php', {'activePage': 'contact', 'pageTitle': 'Contact Concierge | AuraCraft'}),
            'services': ('services/index.php', {'activePage': 'services', 'pageTitle': 'Curated Disciplines | AuraCraft'}),
            'providers': ('providers/index.php', {'activePage': 'providers', 'pageTitle': 'Find Specialists within 15 km | AuraCraft'}),
            'booking/track': ('booking/track.php', {'activePage': 'track', 'pageTitle': 'Live Appointment Tracker | AuraCraft'}),
            'auth/login': ('auth/login.php', {'activePage': 'auth', 'pageTitle': 'Sign In | AuraCraft'}),
            'auth/register': ('auth/register_choose.php', {'activePage': 'auth', 'pageTitle': 'Choose Account Type | AuraCraft'}),
            'auth/choose': ('auth/register_choose.php', {'activePage': 'auth', 'pageTitle': 'Choose Account Type | AuraCraft'}),
            'auth/registerCustomer': ('auth/register_customer.php', {'activePage': 'auth', 'pageTitle': 'Customer Membership | AuraCraft'}),
            'auth/registerProvider': ('auth/register_provider.php', {'activePage': 'auth', 'pageTitle': 'Partner Network | AuraCraft'}),
            'customer/dashboard': ('customer/dashboard.php', {'activePage': 'customer', 'pageTitle': 'Customer Portal | AuraCraft'}),
            'provider/dashboard': ('provider/dashboard.php', {'activePage': 'provider', 'pageTitle': 'Partner Console | AuraCraft'}),
            'admin/dashboard': ('admin/dashboard.php', {'activePage': 'admin', 'pageTitle': 'Super Admin | AuraCraft'})
        }
        
        if path.startswith('providers/detail'):
            html = render_php_view('providers/detail.php', {'activePage': 'providers', 'pageTitle': 'Specialist Profile | AuraCraft'}, auth_state)
            self.send_response(200)
            self.send_header('Content-type', 'text/html; charset=utf-8')
            self.end_headers()
            self.wfile.write(html.encode('utf-8'))
            return
            
        if path.startswith('booking/track'):
            html = render_php_view('booking/track.php', {'activePage': 'track', 'pageTitle': 'Live Booking Tracker | AuraCraft'}, auth_state)
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
        
        self.send_response(303)
        
        if 'loginSubmit' in self.path:
            self.send_header('Set-Cookie', 'auth_user=1; Path=/')
            if 'admin' in post_data:
                self.send_header('Set-Cookie', 'auth_role=admin; Path=/')
                self.send_header('Set-Cookie', 'auth_name=Admin; Path=/')
                self.send_header('Location', '/admin/dashboard')
            elif 'provider' in post_data or 'sterling' in post_data or 'david' in post_data:
                self.send_header('Set-Cookie', 'auth_role=provider; Path=/')
                self.send_header('Set-Cookie', 'auth_name=David; Path=/')
                self.send_header('Set-Cookie', 'prompt_avail=1; Path=/')
                self.send_header('Location', '/provider/dashboard')
            else:
                self.send_header('Set-Cookie', 'auth_role=customer; Path=/')
                self.send_header('Set-Cookie', 'auth_name=Sophia; Path=/')
                self.send_header('Location', '/customer/dashboard')
        elif 'registerCustomerSubmit' in self.path:
            self.send_header('Set-Cookie', 'auth_user=1; Path=/')
            self.send_header('Set-Cookie', 'auth_role=customer; Path=/')
            self.send_header('Set-Cookie', 'auth_name=Sophia; Path=/')
            self.send_header('Location', '/customer/dashboard')
        elif 'registerProviderSubmit' in self.path:
            # Save provider session and redirect directly to Provider Dashboard!
            self.send_header('Set-Cookie', 'auth_user=1; Path=/')
            self.send_header('Set-Cookie', 'auth_role=provider; Path=/')
            self.send_header('Set-Cookie', 'auth_name=David; Path=/')
            self.send_header('Set-Cookie', 'prompt_avail=1; Path=/')
            self.send_header('Location', '/provider/dashboard')
        elif 'create' in self.path:
            self.send_header('Set-Cookie', 'auth_user=1; Path=/')
            self.send_header('Set-Cookie', 'auth_role=customer; Path=/')
            self.send_header('Set-Cookie', 'auth_name=Sophia; Path=/')
            self.send_header('Set-Cookie', 'active_order=BK-89021; Path=/')
            self.send_header('Location', '/booking/track')
        elif 'cancel' in self.path:
            self.send_header('Set-Cookie', 'active_order=; Path=/; Max-Age=0')
            self.send_header('Location', '/customer/dashboard')
        else:
            self.send_header('Location', '/')
            
        self.end_headers()

if __name__ == '__main__':
    with socketserver.TCPServer(("", PORT), AuraHandler) as httpd:
        print(f"AuraCraft server listening at http://localhost:{PORT}")
        httpd.serve_forever()
