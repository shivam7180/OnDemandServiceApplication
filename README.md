# AuraCraft — On-Demand Home Care & Master Craftsmen Platform

A commercial-grade, responsive, and animated on-demand service application crafted with a custom **Core PHP MVC architecture**, **MySQL**, **Vanilla JavaScript**, **CSS3**, and **Google Maps API**.

---

## ✦ Technology Stack (Strictly Framework-Free)
- **Frontend:** HTML5, Modern CSS3 (CSS Custom Properties, Keyframe Animations, Flexbox, Grid), Vanilla JavaScript (IntersectionObserver, ES6+).
- **Backend:** Pure Core PHP following MVC (Model-View-Controller) architecture.
- **Database:** MySQL (12 Normalized tables with InnoDB engine & foreign key constraints).
- **Server:** Apache HTTP Server on XAMPP (`mod_rewrite` clean URL routing).
- **Maps:** Google Maps JavaScript API (with brand styling & directions polyline routing).
- **Design System:** Warm Ivory (`#FAF8F3`), Deep Forest Green (`#234C3D`), Terracotta (`#D9795B`), Soft Sage (`#DCE8DA`), and Charcoal (`#292B27`).

---

## ✦ Turnkey Setup & Deployment on XAMPP

### 1. Place Project in XAMPP
Copy or clone this directory into your XAMPP `htdocs` directory:
```
C:\xampp\htdocs\OnDemandServiceApplication
```

### 2. Start Apache and MySQL
1. Launch **XAMPP Control Panel**.
2. Click **Start** for **Apache**.
3. Click **Start** for **MySQL**.

### 3. Import MySQL Database
1. Open your browser and go to `http://localhost/phpmyadmin`.
2. Click the **Import** tab.
3. Import the following files in order:
   - `database/schema.sql` (Creates database `ondemand_db` and all 12 tables)
   - `database/seed.sql` (Seeds demo admin, providers, customers, categories, and services)

### 4. Launch Application
Navigate to:
```
http://localhost/OnDemandServiceApplication
```

*(If running through a virtual host mapped directly to the root, navigate to `http://localhost/`)*

---

## ✦ Demo Accounts & Credentials

Default password for all demo accounts is: **`password`**

| Role | Email Address | Password | Features Accessible |
| :--- | :--- | :--- | :--- |
| **Admin** | `admin@servicehub.com` | `password` | Super Admin Console, Provider Approvals, Platform GMV, Dispute Tracker |
| **Customer** | `sophia@servicehub.com` | `password` | Service Booking, Live Tracking, Booking History, Customer Reviews |
| **Provider** | `david@sterlingcraft.com` | `password` | Partner Console, Availability Toggle, Job Accept/Reject, Earnings Tracker |

*(The Sign In page features 1-click demo login buttons to instantly test any of these roles!)*

---

## ✦ Google Maps API Configuration
Google Maps integration is already wired up in `public/js/maps.js`.
To enable live Google satellite tiles:
1. Open `config/config.php`.
2. Replace `'YOUR_GOOGLE_MAPS_API_KEY_HERE'` with your Google Maps JavaScript API key:
   ```php
   define('GOOGLE_MAPS_API_KEY', 'AIzaSyYourActualKeyHere...');
   ```
*(If no key is configured, the application automatically uses a styled CSS radar preview with floating vehicle badges without crashing).*

---

## ✦ Platform Pages & URL Routes

| Page | URL Path | Description |
| :--- | :--- | :--- |
| **Landing Page** | `/` | Animated hero, floating badges, category cards, and live counter statistics |
| **Services Catalog** | `/services` | Filterable discipline catalog with rate badges and instant booking modal |
| **Find Providers** | `/providers` | Specialist directory with toggle between Grid View and Interactive Google Maps |
| **Provider Profile** | `/providers/detail/:id` | Bio, verified credentials, weekly schedule, client reviews, and direct booking |
| **Track Booking** | `/booking/track/:code` | Dynamic progress stepper, live ETA countdown, and Google Maps route preview |
| **About Us** | `/home/about` | Heritage story, 5-tier vetting pillars, and platform milestones |
| **Contact Us** | `/home/contact` | Concierge message desk with toast notifications and headquarters hotline |
| **Customer Sign In** | `/auth/login` | Dual role sign in with 1-click demo autofillers and CSRF validation |
| **Customer Register** | `/auth/registerCustomer` | Customer onboarding with address and password validation |
| **Provider Apply** | `/auth/registerProvider` | Trade application with hourly rate and discipline selection |
| **Customer Portal** | `/customer/dashboard` | Active appointment tracker, spending counters, and booking history |
| **Provider Portal** | `/provider/dashboard` | Live availability toggle, job queue with Accept/Decline, and earnings |
| **Admin Console** | `/admin/dashboard` | Provider background review queue and dispute ticket resolution |

---

## ✦ Security Highlights
- **Prepared Statements:** 100% of SQL queries executed through PDO prepared statements with bound parameters.
- **CSRF Tokens:** 32-byte cryptographic tokens verified on every POST submission.
- **Session Security:** `session.cookie_httponly` and `session_regenerate_id(true)` preventing session fixation.
- **Password Encryption:** Native `password_hash()` utilizing Bcrypt cost 10.
- **RBAC:** Strict controller-level authorization preventing privilege escalation.
