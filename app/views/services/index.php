<?php require_once ROOT_PATH . '/app/views/layouts/header.php'; ?>

<!-- Services Catalog Header -->
<section style="padding: 60px 0 40px; background: radial-gradient(circle at 50% 10%, #EEF4ED 0%, transparent 60%);">
  <div class="container">
    <div style="max-width: 700px; margin: 0 auto; text-align: center;" class="reveal-on-scroll">
      <span class="badge badge-sage" style="margin-bottom: 12px;">✦ Service Offerings</span>
      <h1 style="margin-bottom: 16px;">Curated Home Disciplines</h1>
      <p style="font-size: 1.1rem; color: var(--charcoal-light);">
        Select from our certified disciplines or filter by specific specialty. All services include transparent Rupee pricing, bonded protection, and live GPS arrival tracking.
      </p>
    </div>

    <!-- Category Pill Filter Tabs -->
    <div style="display: flex; gap: 10px; justify-content: center; flex-wrap: wrap; margin-top: 36px;" class="reveal-on-scroll reveal-delay-1">
      <a href="<?= BASE_URL ?>/services?cat=all" class="btn btn-sm <?= ($selectedCat === 'all') ? 'btn-primary' : 'btn-outline' ?>">All Services</a>
      <a href="<?= BASE_URL ?>/services?cat=home-cleaning" class="btn btn-sm <?= ($selectedCat === 'home-cleaning') ? 'btn-primary' : 'btn-outline' ?>">✨ Cleaning</a>
      <a href="<?= BASE_URL ?>/services?cat=plumbing" class="btn btn-sm <?= ($selectedCat === 'plumbing') ? 'btn-primary' : 'btn-outline' ?>">🔧 Plumbing</a>
      <a href="<?= BASE_URL ?>/services?cat=electrical" class="btn btn-sm <?= ($selectedCat === 'electrical') ? 'btn-primary' : 'btn-outline' ?>">⚡ Electrical</a>
      <a href="<?= BASE_URL ?>/services?cat=painting" class="btn btn-sm <?= ($selectedCat === 'painting') ? 'btn-primary' : 'btn-outline' ?>">🎨 Painting</a>
      <a href="<?= BASE_URL ?>/services?cat=appliance-care" class="btn btn-sm <?= ($selectedCat === 'appliance-care') ? 'btn-primary' : 'btn-outline' ?>">❄️ Appliances</a>
    </div>
  </div>
</section>

<!-- Services Grid -->
<section class="section-spacer">
  <div class="container">
    <?php if (empty($services)): ?>
      <div class="card" style="text-align: center; padding: 60px 20px;">
        <div style="font-size: 3rem; margin-bottom: 16px;">🔍</div>
        <h3>No services found matching your criteria</h3>
        <p style="margin-top: 8px;">Try clearing your filters or search keywords.</p>
        <a href="<?= BASE_URL ?>/services" class="btn btn-secondary" style="margin-top: 20px;">View All Disciplines</a>
      </div>
    <?php else: ?>
      <div class="grid-3">
        <?php foreach ($services as $service): ?>
          <div class="card card-hoverable tilt-card reveal-on-scroll" style="display: flex; flex-direction: column; justify-content: space-between;">
            <div>
              <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 16px;">
                <div style="width: 48px; height: 48px; border-radius: 14px; background: var(--soft-sage); color: var(--forest-green); display: flex; align-items: center; justify-content: center; font-size: 1.4rem;">
                  <?= $service['icon'] ?>
                </div>
                <span class="badge badge-terracotta"><?= htmlspecialchars($service['badge']) ?></span>
              </div>
              
              <div style="font-size: 0.85rem; font-weight: 600; color: var(--terracotta); text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 6px;">
                <?= htmlspecialchars($service['category_name']) ?>
              </div>
              <h3 style="font-size: 1.25rem; margin-bottom: 10px; color: var(--forest-green);">
                <?= htmlspecialchars($service['name']) ?>
              </h3>
              <p style="font-size: 0.92rem; color: var(--charcoal-light); margin-bottom: 24px; line-height: 1.55;">
                <?= htmlspecialchars($service['description']) ?>
              </p>
            </div>

            <div>
              <div style="display: flex; align-items: center; justify-content: space-between; padding-top: 18px; border-top: 1px solid var(--border-light); margin-bottom: 18px;">
                <div>
                  <div style="font-size: 0.78rem; color: var(--charcoal-muted); text-transform: uppercase;">Standard Rate</div>
                  <div style="font-size: 1.35rem; font-weight: 700; color: var(--forest-green);">
                    ₹<?= number_format($service['price'], 0) ?>
                  </div>
                </div>
                <div style="text-align: right;">
                  <div style="font-size: 0.78rem; color: var(--charcoal-muted); text-transform: uppercase;">Estimated Time</div>
                  <div style="font-size: 0.95rem; font-weight: 600; color: var(--charcoal);">
                    ⏱ <?= $service['duration'] ?> mins
                  </div>
                </div>
              </div>

              <div style="display: flex; gap: 10px;">
                <button type="button" class="btn btn-primary" style="flex: 1;" onclick="openBookingModal(<?= htmlspecialchars(json_encode($service)) ?>)">
                  Book Appointment
                </button>
                <a href="<?= BASE_URL ?>/providers?service=<?= urlencode($service['name']) ?>" class="btn btn-outline" style="padding: 12px 16px;" title="Find Specialists">
                  Pros →
                </a>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>
</section>

<!-- Interactive Booking Modal with Geolocation Auto-Detection -->
<div class="modal-overlay" id="bookingModal">
  <div class="modal-content">
    <button class="modal-close" aria-label="Close modal">✕</button>
    <span class="badge badge-sage" style="margin-bottom: 12px;">Instant Reservation</span>
    <h3 id="modalServiceTitle" style="margin-bottom: 6px;">Service Name</h3>
    <p id="modalServicePrice" style="font-weight: 700; color: var(--forest-green); margin-bottom: 20px;">₹0</p>

    <form method="POST" action="<?= BASE_URL ?>/booking/create" onsubmit="handleOrderSubmission(event, this)">
      <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken ?? '') ?>">
      <input type="hidden" id="modalServiceId" name="service_id" value="1">
      <input type="hidden" id="modalTotalAmount" name="total_amount" value="499.00">
      <input type="hidden" id="modalLatitude" name="latitude" value="19.0760">
      <input type="hidden" id="modalLongitude" name="longitude" value="72.8777">

      <!-- Geolocation Auto-Detect Address Field -->
      <div class="form-group">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 4px;">
          <label class="form-label" style="margin: 0;">Service Destination Address</label>
          <button type="button" class="badge badge-terracotta" style="cursor: pointer; border: none; font-size: 0.78rem; text-transform: none; display: flex; align-items: center; gap: 4px;" onclick="fetchCurrentLocation()">
            <span>📍 Auto-Detect Location</span>
          </button>
        </div>
        
        <div style="position: relative;">
          <input type="text" id="serviceAddressInput" name="service_address" class="form-control" required placeholder="Allow location permission or enter address" value="Flat 402, Green Acres, Bandra West, Mumbai">
          <span id="locationFetchStatus" style="font-size: 0.78rem; color: var(--forest-green); display: block; margin-top: 6px; font-weight: 500;">
            ✦ Click 'Auto-Detect Location' to allow browser GPS coordinates
          </span>
        </div>
      </div>

      <div class="form-row">
        <div class="form-group">
          <label class="form-label">Preferred Date</label>
          <input type="date" name="booking_date" class="form-control" required value="<?= date('Y-m-d', strtotime('+1 day')) ?>">
        </div>
        <div class="form-group">
          <label class="form-label">Preferred Time Window</label>
          <select name="booking_time" class="form-control" required>
            <option value="09:00:00">Morning (9:00 AM - 12:00 PM)</option>
            <option value="13:00:00">Afternoon (1:00 PM - 4:00 PM)</option>
            <option value="17:00:00">Late Afternoon (5:00 PM - 7:00 PM)</option>
          </select>
        </div>
      </div>

      <div class="form-group">
        <label class="form-label">Special Notes or Entry Instructions</label>
        <textarea name="customer_notes" class="form-control" placeholder="e.g. Ring bell at gate, tower B lift 2, pet friendly supplies..."></textarea>
      </div>

      <button type="submit" class="btn btn-secondary btn-lg" style="width: 100%; margin-top: 10px;">
        Confirm Purchase & Track Order →
      </button>
    </form>
  </div>
</div>

<script>
function openBookingModal(service) {
  document.getElementById('modalServiceTitle').textContent = service.name;
  document.getElementById('modalServicePrice').textContent = '₹' + parseFloat(service.price).toFixed(0) + ' • Approx ' + service.duration + ' mins';
  document.getElementById('modalServiceId').value = service.id;
  document.getElementById('modalTotalAmount').value = service.price;
  
  const savedLabel = localStorage.getItem('user_location_label');
  const savedLat = localStorage.getItem('user_lat');
  const savedLng = localStorage.getItem('user_lng');
  if (savedLabel && savedLat) {
    document.getElementById('serviceAddressInput').value = savedLabel;
    document.getElementById('modalLatitude').value = savedLat;
    document.getElementById('modalLongitude').value = savedLng;
    document.getElementById('locationFetchStatus').textContent = '✓ Using saved device location';
  } else {
    setTimeout(() => {
      fetchCurrentLocation();
    }, 400);
  }

  AuraCraft.openModal('bookingModal');
}

function fetchCurrentLocation() {
  const addrInput = document.getElementById('serviceAddressInput');
  const latInput = document.getElementById('modalLatitude');
  const lngInput = document.getElementById('modalLongitude');
  const statusElem = document.getElementById('locationFetchStatus');
  
  AuraCraft.detectLocation(addrInput, latInput, lngInput, statusElem);
}

function handleOrderSubmission(event, form) {
  // Store active order flag in localStorage and cookie
  localStorage.setItem('active_order', 'BK-89021');
  document.cookie = "active_order=BK-89021; path=/";

  // Reveal the track item in navbar immediately
  const navTrack = document.getElementById('navTrackItem');
  const headerTrack = document.getElementById('headerTrackBtn');
  if (navTrack) navTrack.style.display = 'block';
  if (headerTrack) headerTrack.style.display = 'inline-flex';

  AuraCraft.showToast('Order confirmed! Opening live GPS tracker...', 'success');
}
</script>

<?php require_once ROOT_PATH . '/app/views/layouts/footer.php'; ?>
