<?php require_once ROOT_PATH . '/app/views/layouts/header.php'; ?>

<!-- Tracking Header -->
<section style="padding: 40px 0 20px; background: linear-gradient(135deg, #FAF8F3 0%, #EEF4ED 100%); border-bottom: 1px solid var(--border-light);">
  <div class="container">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 20px;">
      <div>
        <div style="display: flex; gap: 10px; align-items: center; margin-bottom: 6px;">
          <span class="badge badge-terracotta">Live Status Tracker</span>
          <span style="font-weight: 700; color: var(--forest-green); font-size: 1rem;">Booking #<?= htmlspecialchars($booking['code']) ?></span>
        </div>
        <h1 style="font-size: 2rem; margin: 0;"><?= htmlspecialchars($booking['service']) ?></h1>
        <p style="font-size: 0.95rem; margin-top: 4px; color: var(--charcoal-muted);">
          Destination: <strong id="liveDestinationText"><?= htmlspecialchars($booking['address']) ?></strong>
        </p>
      </div>

      <!-- ETA Badge -->
      <div style="background: #FFFFFF; padding: 14px 24px; border-radius: var(--radius-lg); box-shadow: var(--shadow-sm); border: 1.5px solid var(--soft-sage); text-align: right;">
        <div style="font-size: 0.8rem; color: var(--charcoal-muted); text-transform: uppercase; font-weight: 600;">Estimated Arrival</div>
        <div id="liveEtaText" style="font-size: 1.8rem; font-weight: 800; color: var(--forest-green);">
          <?= $booking['provider']['eta'] ?>
        </div>
        <div style="font-size: 0.78rem; color: var(--status-success); font-weight: 600;">● Approaching via Main Road</div>
      </div>
    </div>
  </div>
</section>

<!-- Animated Stepper Section -->
<section style="padding: 30px 0 20px; background: #FFFFFF; border-bottom: 1px solid var(--border-subtle);">
  <div class="container">
    
    <?php
      $status = $booking['status'];
      $progressWidth = '25%';
      if ($status === 'accepted') $progressWidth = '45%';
      if ($status === 'in_progress') $progressWidth = '75%';
      if ($status === 'completed') $progressWidth = '100%';
    ?>

    <div class="timeline-stepper">
      <div class="timeline-progress-bar" id="stepperProgressBar" style="width: <?= $progressWidth ?>;"></div>

      <!-- Step 1: Placed -->
      <div class="timeline-step completed" id="stepNode1">
        <div class="timeline-dot">✓</div>
        <div class="timeline-label">Order Placed</div>
        <div style="font-size: 0.75rem; color: var(--charcoal-muted);">Confirmed</div>
      </div>

      <!-- Step 2: Confirmed -->
      <div class="timeline-step <?= in_array($status, ['accepted', 'in_progress', 'completed']) ? 'completed' : (($status === 'pending') ? 'active' : '') ?>" id="stepNode2">
        <div class="timeline-dot"><?= in_array($status, ['accepted', 'in_progress', 'completed']) ? '✓' : '2' ?></div>
        <div class="timeline-label">Accepted</div>
        <div style="font-size: 0.75rem; color: var(--charcoal-muted);">Pro Assigned</div>
      </div>

      <!-- Step 3: En Route -->
      <div class="timeline-step <?= ($status === 'completed') ? 'completed' : (($status === 'in_progress') ? 'active' : '') ?>" id="stepNode3">
        <div class="timeline-dot"><?= ($status === 'completed') ? '✓' : '🚗' ?></div>
        <div class="timeline-label">En Route</div>
        <div style="font-size: 0.75rem; color: var(--forest-green); font-weight: 600;">GPS Active</div>
      </div>

      <!-- Step 4: Execution -->
      <div class="timeline-step <?= ($status === 'completed') ? 'completed' : '' ?>" id="stepNode4">
        <div class="timeline-dot"><?= ($status === 'completed') ? '✓' : '🔨' ?></div>
        <div class="timeline-label">In Service</div>
        <div style="font-size: 0.75rem; color: var(--charcoal-muted);">On Site</div>
      </div>

      <!-- Step 5: Completed -->
      <div class="timeline-step <?= ($status === 'completed') ? 'active' : '' ?>" id="stepNode5">
        <div class="timeline-dot">★</div>
        <div class="timeline-label">Completed</div>
        <div style="font-size: 0.75rem; color: var(--charcoal-muted);">Inspected</div>
      </div>
    </div>

  </div>
</section>

<!-- Tracking Main Grid -->
<section class="section-spacer" style="padding-top: 40px;">
  <div class="container">
    <div style="display: grid; grid-template-columns: 1.8fr 1.2fr; gap: 36px; align-items: flex-start;">
      
      <!-- Left Column: Live Interactive GPS Map & Route Preview -->
      <div class="card reveal-on-scroll" style="padding: 0; overflow: hidden; border-radius: 24px;">
        <div style="padding: 14px 20px; background: #FAF8F3; border-bottom: 1px solid var(--border-light); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
          <div style="display: flex; gap: 8px; align-items: center;">
            <button type="button" id="btnRadarView" class="btn btn-primary btn-sm" onclick="switchTrackingMapView('radar')">
              🛰️ Live Radar Map
            </button>
            <button type="button" id="btnGoogleView" class="btn btn-outline btn-sm" onclick="switchTrackingMapView('google')">
              🗺️ Google Maps View
            </button>
          </div>
          <div style="display: flex; gap: 8px; align-items: center;">
            <button type="button" class="btn btn-sage btn-sm" onclick="syncWithUserGPS()" title="Use device GPS satellite coordinates">
              📍 Sync My GPS
            </button>
            <a id="btnExternalGoogleMaps" href="https://maps.google.com" target="_blank" class="btn btn-outline btn-sm" style="display: inline-flex; align-items: center; gap: 4px;" title="Open in Google Maps">
              <span>Google Maps ↗</span>
            </a>
          </div>
        </div>

        <!-- Interactive Map Containers (Leaflet Radar & Google Maps Embed) -->
        <div id="trackingMapCanvas" style="height: 420px; width: 100%; position: relative; background: #E8ECE6;"></div>
        <div id="googleMapCanvas" style="height: 420px; width: 100%; display: none; background: #E8ECE6;">
          <iframe id="googleMapIframe" width="100%" height="100%" style="border:0;" loading="lazy" allowfullscreen referrerpolicy="no-referrer-when-downgrade" src="about:blank"></iframe>
        </div>

        <div style="padding: 14px 20px; background: #FFFFFF; font-size: 0.88rem; color: var(--charcoal-light); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 8px;">
          <span>Specialist Location: <strong><?= $booking['provider']['current_location'] ?></strong></span>
          <span id="gpsSyncNotice" style="color: var(--status-success); font-weight: 600;">● Live GPS Telemetry Active</span>
        </div>
      </div>

      <!-- Right Column: Provider Card & Appointment Details -->
      <div style="display: flex; flex-direction: column; gap: 24px;" class="reveal-on-scroll reveal-delay-1">
        
        <div class="card">
          <div style="display: flex; gap: 16px; align-items: center; margin-bottom: 20px;">
            <div style="width: 64px; height: 64px; border-radius: 50%; background: var(--soft-sage); font-size: 2.2rem; display: flex; align-items: center; justify-content: center;">
              <?= $booking['provider']['avatar'] ?>
            </div>
            <div>
              <h3 style="font-size: 1.25rem; margin: 0;"><?= htmlspecialchars($booking['provider']['name']) ?></h3>
              <div style="font-size: 0.88rem; color: var(--charcoal-muted);"><?= htmlspecialchars($booking['provider']['business']) ?></div>
              <div style="font-size: 0.85rem; color: #D97706; font-weight: 600; margin-top: 2px;">★ <?= $booking['provider']['rating'] ?> Master Certified</div>
            </div>
          </div>

          <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 20px;">
            <a href="tel:<?= $booking['provider']['phone'] ?>" class="btn btn-primary" style="padding: 12px;">
              📞 Call Specialist
            </a>
            <button type="button" class="btn btn-outline" style="padding: 12px;" onclick="AuraCraft.showToast('Opening secure client concierge channel...', 'info')">
              💬 Message
            </button>
          </div>

          <div style="background: var(--bg-muted); border-radius: 12px; padding: 14px; font-size: 0.88rem;">
            <div style="display: flex; justify-content: space-between; margin-bottom: 6px;">
              <span style="color: var(--charcoal-muted);">Total Order Amount:</span>
              <strong style="color: var(--forest-green); font-size: 1.15rem;">₹<?= number_format($booking['total_amount'], 0) ?></strong>
            </div>
            <div style="display: flex; justify-content: space-between;">
              <span style="color: var(--charcoal-muted);">Payment:</span>
              <span class="badge badge-success" style="font-size: 0.72rem;">Secured in Escrow</span>
            </div>
          </div>
        </div>

        <!-- Appointment Timeline & Actions -->
        <div class="card" style="border: 1.5px dashed var(--soft-sage);">
          <div style="font-size: 0.85rem; font-weight: 700; color: var(--forest-green); margin-bottom: 10px;">
            ⚡ Timeline Demonstration Controls:
          </div>
          <div style="display: flex; gap: 8px; flex-wrap: wrap;">
            <button type="button" class="btn btn-sage btn-sm" onclick="advanceStep(4)">
              In Service
            </button>
            <button type="button" class="btn btn-sage btn-sm" onclick="advanceStep(5)">
              Mark Complete
            </button>
            <button type="button" class="btn btn-outline btn-sm" style="color: #D32F2F; border-color: #D32F2F;" onclick="AuraCraft.openModal('cancelModal')">
              Cancel Booking
            </button>
          </div>
        </div>

      </div>

    </div>
  </div>
</section>

<!-- Cancel Modal -->
<div class="modal-overlay" id="cancelModal">
  <div class="modal-content">
    <button class="modal-close" aria-label="Close modal">✕</button>
    <span class="badge badge-danger" style="margin-bottom: 12px;">Cancel Appointment</span>
    <h3 style="margin-bottom: 10px;">Are you sure you want to cancel?</h3>
    <p style="font-size: 0.95rem; color: var(--charcoal-light); margin-bottom: 20px;">
      Free cancellation applies up to 4 hours before service. Your escrow hold will be refunded immediately.
    </p>

    <form method="POST" action="<?= BASE_URL ?>/booking/cancel" onsubmit="handleCancelOrder(event)">
      <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken ?? '') ?>">
      <input type="hidden" name="booking_id" value="<?= (int)($booking['id'] ?? 1) ?>">

      <div class="form-group">
        <label class="form-label">Reason for Cancellation</label>
        <select name="reason" class="form-control" required>
          <option value="Issue resolved beforehand">Issue resolved beforehand</option>
          <option value="Scheduling conflict">Scheduling conflict</option>
          <option value="Booked another specialist">Booked another specialist</option>
          <option value="Emergency">Emergency</option>
        </select>
      </div>

      <div style="display: flex; gap: 12px; margin-top: 20px;">
        <button type="button" class="btn btn-outline" style="flex: 1;" onclick="AuraCraft.closeModal('cancelModal')">
          Keep Appointment
        </button>
        <button type="submit" class="btn btn-secondary" style="flex: 1; background: #D32F2F;">
          Confirm Cancellation
        </button>
      </div>
    </form>
  </div>
</div>

<!-- Maps Scripts -->
<?php if (defined('GOOGLE_MAPS_API_KEY') && GOOGLE_MAPS_API_KEY !== 'YOUR_GOOGLE_MAPS_API_KEY_HERE'): ?>
  <script src="https://maps.googleapis.com/maps/api/js?key=<?= GOOGLE_MAPS_API_KEY ?>"></script>
<?php endif; ?>
<script src="<?= BASE_URL ?>/public/js/maps.js"></script>

<script>
let currentTrackingView = 'radar';
let userCoords = { lat: 19.0760, lng: 72.8777 };
let providerCoords = { lat: 19.0600, lng: 72.8650 };

document.addEventListener('DOMContentLoaded', () => {
  const savedLat = localStorage.getItem('user_lat');
  const savedLng = localStorage.getItem('user_lng');
  const savedLabel = localStorage.getItem('user_location_label');

  if (savedLat && savedLng) {
    userCoords = { lat: parseFloat(savedLat), lng: parseFloat(savedLng) };
    providerCoords = { lat: userCoords.lat - 0.015, lng: userCoords.lng - 0.012 };
  }

  if (savedLabel) {
    const destEl = document.getElementById('liveDestinationText');
    if (destEl) destEl.textContent = savedLabel;
  }

  updateGoogleMapLinks();
  AuraMaps.initTrackingMap('trackingMapCanvas', providerCoords, userCoords);

  // Auto-request location if not yet fetched
  if (navigator.geolocation && !savedLat) {
    navigator.geolocation.getCurrentPosition(
      (pos) => {
        applyNewLocation(pos.coords.latitude, pos.coords.longitude, true);
      },
      (err) => {
        console.log('Location prompt dismissed or denied, keeping default location.');
      },
      { timeout: 8000, enableHighAccuracy: true }
    );
  }
});

function updateGoogleMapLinks() {
  const iframe = document.getElementById('googleMapIframe');
  const externalBtn = document.getElementById('btnExternalGoogleMaps');

  if (iframe) {
    iframe.src = `https://maps.google.com/maps?q=${userCoords.lat},${userCoords.lng}&hl=en&z=14&output=embed`;
  }
  if (externalBtn) {
    externalBtn.href = `https://www.google.com/maps/dir/?api=1&origin=${providerCoords.lat},${providerCoords.lng}&destination=${userCoords.lat},${userCoords.lng}&travelmode=driving`;
  }
}

function switchTrackingMapView(view) {
  const radarCont = document.getElementById('trackingMapCanvas');
  const googleCont = document.getElementById('googleMapCanvas');
  const btnRadar = document.getElementById('btnRadarView');
  const btnGoogle = document.getElementById('btnGoogleView');

  currentTrackingView = view;
  if (view === 'radar') {
    if (radarCont) radarCont.style.display = 'block';
    if (googleCont) googleCont.style.display = 'none';
    if (btnRadar) btnRadar.className = 'btn btn-primary btn-sm';
    if (btnGoogle) btnGoogle.className = 'btn btn-outline btn-sm';
    if (window.auraLiveTrackingMap) {
      setTimeout(() => window.auraLiveTrackingMap.invalidateSize(), 150);
    }
  } else {
    if (radarCont) radarCont.style.display = 'none';
    if (googleCont) googleCont.style.display = 'block';
    if (btnGoogle) btnGoogle.className = 'btn btn-primary btn-sm';
    if (btnRadar) btnRadar.className = 'btn btn-outline btn-sm';
    updateGoogleMapLinks();
  }
}

function applyNewLocation(lat, lng, isAuto = false) {
  userCoords = { lat: parseFloat(lat), lng: parseFloat(lng) };
  providerCoords = { lat: userCoords.lat - 0.015, lng: userCoords.lng - 0.012 };

  localStorage.setItem('user_lat', userCoords.lat);
  localStorage.setItem('user_lng', userCoords.lng);
  const label = `📍 Real GPS Location (${userCoords.lat.toFixed(4)}, ${userCoords.lng.toFixed(4)})`;
  localStorage.setItem('user_location_label', label);

  const destEl = document.getElementById('liveDestinationText');
  if (destEl) destEl.textContent = label;

  const notice = document.getElementById('gpsSyncNotice');
  if (notice) notice.textContent = `✓ Locked to device GPS (${userCoords.lat.toFixed(2)}, ${userCoords.lng.toFixed(2)})`;

  updateGoogleMapLinks();
  AuraMaps.initTrackingMap('trackingMapCanvas', providerCoords, userCoords);

  if (!isAuto) {
    AuraCraft.showToast('Radar & Google Maps route recalibrated to your live GPS position!', 'success');
  }
}

function syncWithUserGPS() {
  if (!navigator.geolocation) {
    AuraCraft.showToast('Geolocation not supported by this browser', 'warning');
    return;
  }

  AuraCraft.showToast('Synchronizing radar with live satellite position...', 'info');
  navigator.geolocation.getCurrentPosition(
    (pos) => {
      applyNewLocation(pos.coords.latitude, pos.coords.longitude, false);
    },
    (err) => {
      AuraCraft.showToast('Location permission denied or timed out.', 'warning');
    },
    { enableHighAccuracy: true }
  );
}

function advanceStep(stepNumber) {
  const progressBar = document.getElementById('stepperProgressBar');
  const liveEta = document.getElementById('liveEtaText');
  
  if (stepNumber === 4) {
    progressBar.style.width = '75%';
    document.getElementById('stepNode3').className = 'timeline-step completed';
    document.getElementById('stepNode4').className = 'timeline-step active';
    liveEta.textContent = 'Arrived';
    AuraCraft.showToast('Specialist has arrived on-site and commenced service.', 'success');
  } else if (stepNumber === 5) {
    progressBar.style.width = '100%';
    document.getElementById('stepNode4').className = 'timeline-step completed';
    document.getElementById('stepNode5').className = 'timeline-step active completed';
    liveEta.textContent = 'Done';
    AuraCraft.showToast('Appointment completed! Escrow funds released to specialist.', 'success');
  }
}

function handleCancelOrder(e) {
  localStorage.removeItem('active_order');
  document.cookie = "active_order=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
  const navTrack = document.getElementById('navTrackItem');
  const headerTrack = document.getElementById('headerTrackBtn');
  if (navTrack) navTrack.style.display = 'none';
  if (headerTrack) headerTrack.style.display = 'none';
}
</script>

<?php require_once ROOT_PATH . '/app/views/layouts/footer.php'; ?>
