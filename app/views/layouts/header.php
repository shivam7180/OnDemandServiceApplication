<?php
$currentUser = $_SESSION['user'] ?? null;
$activePage = $activePage ?? '';
$hasActiveOrder = !empty($_SESSION['active_booking_code']) || !empty($_COOKIE['active_order']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($pageTitle ?? 'AuraCraft | Premium On-Demand Services') ?></title>
  
  <!-- Premium Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Outfit:wght@500;600;700;800&display=swap" rel="stylesheet">
  
  <!-- Leaflet Map Styles (High Performance, Interactive Map Tiles) -->
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

  <!-- Stylesheets -->
  <link rel="stylesheet" href="<?= BASE_URL ?>/public/css/main.css">
  <link rel="stylesheet" href="<?= BASE_URL ?>/public/css/animations.css">
</head>
<body>

  <!-- Navigation Bar -->
  <header class="navbar">
    <div class="container nav-container">
      <a href="<?= BASE_URL ?>/" class="brand-logo">
        <div class="brand-icon">✦</div>
        <span>AuraCraft</span>
      </a>

      <!-- Desktop & Mobile Navigation Links -->
      <nav>
        <ul class="nav-links" id="navLinks">
          <li><a href="<?= BASE_URL ?>/" class="nav-link <?= ($activePage === 'home') ? 'active' : '' ?>">Home</a></li>
          <li><a href="<?= BASE_URL ?>/services" class="nav-link <?= ($activePage === 'services') ? 'active' : '' ?>">Services</a></li>
          <li><a href="<?= BASE_URL ?>/providers" class="nav-link <?= ($activePage === 'providers') ? 'active' : '' ?>">Find Providers</a></li>
          <li><a href="<?= BASE_URL ?>/home/about" class="nav-link <?= ($activePage === 'about') ? 'active' : '' ?>">About Us</a></li>
          <li><a href="<?= BASE_URL ?>/home/contact" class="nav-link <?= ($activePage === 'contact') ? 'active' : '' ?>">Contact</a></li>
          
          <!-- Track Live Order Link (Visible ONLY after user signs in AND purchases an appointment) -->
          <li id="navTrackItem" style="display: <?= ($currentUser && $hasActiveOrder) ? 'block' : 'none' ?>;">
            <a href="<?= BASE_URL ?>/booking/track" class="nav-link <?= ($activePage === 'track') ? 'active' : '' ?>" style="color: var(--terracotta); font-weight: 700; display: flex; align-items: center; gap: 6px;">
              <span style="width: 8px; height: 8px; border-radius: 50%; background: #4ADE80; box-shadow: 0 0 8px #4ADE80; display: inline-block;"></span>
              <span>Track Live Order</span>
            </a>
          </li>
        </ul>
      </nav>

      <!-- Auth & Notification Actions -->
      <div class="nav-actions">
        <?php if ($currentUser): ?>
          <?php 
            $dashUrl = BASE_URL . '/customer/dashboard';
            if ($currentUser['role'] === 'provider') $dashUrl = BASE_URL . '/provider/dashboard';
            if ($currentUser['role'] === 'admin') $dashUrl = BASE_URL . '/admin/dashboard';
          ?>
          <!-- Track Live Order Action Button (Visible only after placing an order) -->
          <a href="<?= BASE_URL ?>/booking/track" id="headerTrackBtn" class="btn btn-secondary btn-sm" style="display: <?= $hasActiveOrder ? 'inline-flex' : 'none' ?>; align-items: center; gap: 6px; padding: 8px 14px; font-size: 0.85rem;" title="Track Live Order">
            <span style="width: 8px; height: 8px; border-radius: 50%; background: #FFFFFF; animation: pulseGlow 1.5s infinite;"></span>
            <span>Live Tracker</span>
          </a>

          <!-- Notification Bell -->
          <button type="button" class="btn btn-sage btn-sm" style="padding: 8px 12px; position: relative;" onclick="AuraCraft.showToast('Specialist David Sterling has received your request', 'info')" title="Notifications">
            🔔 <span style="position: absolute; top: -4px; right: -4px; background: var(--terracotta); color: #FFF; width: 18px; height: 18px; border-radius: 50%; font-size: 0.65rem; display: flex; align-items: center; justify-content: center; font-weight: 700;">1</span>
          </button>

          <a href="<?= $dashUrl ?>" class="btn btn-sage btn-sm">
            <span>👤 <?= htmlspecialchars(explode(' ', $currentUser['full_name'])[0]) ?></span>
            <span class="badge badge-terracotta" style="font-size: 0.7rem; padding: 2px 8px;"><?= ucfirst($currentUser['role']) ?></span>
          </a>
          <a href="<?= BASE_URL ?>/auth/logout" class="btn btn-outline btn-sm" onclick="localStorage.removeItem('active_order');">Sign Out</a>
        <?php else: ?>
          <a href="<?= BASE_URL ?>/auth/login" class="btn btn-outline btn-sm">Sign In</a>
          <button type="button" class="btn btn-primary btn-sm" onclick="AuraCraft.openModal('roleChooseModal')">Get Started</button>
        <?php endif; ?>

        <button class="mobile-toggle" id="mobileMenuToggle" aria-label="Toggle navigation">☰</button>
      </div>
    </div>
  </header>

  <!-- Modal: Choose Account Type (Customer vs Provider) -->
  <div id="roleChooseModal" class="modal-overlay">
    <div class="modal-card" style="max-width: 580px; text-align: center; padding: 40px 32px; border-radius: 28px;">
      <button class="modal-close" onclick="AuraCraft.closeModal('roleChooseModal')">✕</button>
      <div class="brand-icon" style="margin: 0 auto 16px; width: 48px; height: 48px; font-size: 1.4rem;">✦</div>
      <h3 style="font-size: 1.8rem; margin-bottom: 8px;">Join AuraCraft</h3>
      <p style="color: var(--charcoal-muted); font-size: 0.95rem; margin-bottom: 28px;">
        Select your account type to access personalized booking or business management tools.
      </p>

      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 18px; margin-bottom: 22px;">
        <!-- Option 1: Customer -->
        <a href="<?= BASE_URL ?>/auth/registerCustomer" class="card" style="padding: 24px 20px; text-decoration: none; text-align: left; border: 2px solid var(--border-light); border-radius: 20px; transition: all 0.25s ease; display: flex; flex-direction: column; justify-content: space-between;" onmouseover="this.style.borderColor='var(--forest-green)'; this.style.transform='translateY(-4px)'" onmouseout="this.style.borderColor='var(--border-light)'; this.style.transform='none'">
          <div>
            <div style="font-size: 2.2rem; margin-bottom: 10px;">👤</div>
            <h4 style="color: var(--forest-green); font-size: 1.15rem; margin-bottom: 6px;">Customer</h4>
            <p style="font-size: 0.84rem; color: var(--charcoal-muted); margin-bottom: 16px; line-height: 1.4;">
              Book verified home specialists, track live appointments with GPS, and pay with escrow safety.
            </p>
          </div>
          <span class="btn btn-primary btn-sm" style="width: 100%; text-align: center;">Continue as Customer &rarr;</span>
        </a>

        <!-- Option 2: Provider -->
        <a href="<?= BASE_URL ?>/auth/registerProvider" class="card" style="padding: 24px 20px; text-decoration: none; text-align: left; border: 2px solid var(--border-light); border-radius: 20px; transition: all 0.25s ease; display: flex; flex-direction: column; justify-content: space-between;" onmouseover="this.style.borderColor='var(--terracotta)'; this.style.transform='translateY(-4px)'" onmouseout="this.style.borderColor='var(--border-light)'; this.style.transform='none'">
          <div>
            <div style="font-size: 2.2rem; margin-bottom: 10px;">🛠️</div>
            <h4 style="color: var(--terracotta); font-size: 1.15rem; margin-bottom: 6px;">Service Provider</h4>
            <p style="font-size: 0.84rem; color: var(--charcoal-muted); margin-bottom: 16px; line-height: 1.4;">
              Offer your trade, set your rates in INR, receive local jobs, and grow your independent practice.
            </p>
          </div>
          <span class="btn btn-secondary btn-sm" style="width: 100%; text-align: center;">Register as Partner &rarr;</span>
        </a>
      </div>

      <div style="font-size: 0.88rem; color: var(--charcoal-muted);">
        Already have an account? <a href="<?= BASE_URL ?>/auth/login" style="color: var(--forest-green); font-weight: 700;">Sign In</a>
      </div>
    </div>
  </div>

  <!-- Leaflet Map Script -->
  <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

  <script>
  // Dynamic sync: Track button ONLY shows when user is logged in AND has placed an order
  document.addEventListener('DOMContentLoaded', () => {
    function getCookie(name) {
      const match = document.cookie.match(new RegExp('(^| )' + name + '=([^;]+)'));
      return match ? match[2] : null;
    }

    const isServerLoggedIn = <?= $currentUser ? 'true' : 'false' ?>;
    const hasAuthCookie = Boolean(getCookie('auth_user'));
    const isUserLoggedIn = isServerLoggedIn || hasAuthCookie;

    if (!isUserLoggedIn) {
      // Clear any leftover order data if user is logged out
      localStorage.removeItem('active_order');
    }

    const hasOrderCookie = Boolean(getCookie('active_order'));
    const hasLocalOrder = Boolean(localStorage.getItem('active_order'));
    const hasActiveOrder = isUserLoggedIn && (hasOrderCookie || hasLocalOrder || <?= $hasActiveOrder ? 'true' : 'false' ?>);

    const navTrack = document.getElementById('navTrackItem');
    const headerTrack = document.getElementById('headerTrackBtn');

    if (isUserLoggedIn && hasActiveOrder) {
      if (navTrack) navTrack.style.display = 'block';
      if (headerTrack) headerTrack.style.display = 'inline-flex';
    } else {
      if (navTrack) navTrack.style.display = 'none';
      if (headerTrack) headerTrack.style.display = 'none';
    }
  });
  </script>

  <main>
