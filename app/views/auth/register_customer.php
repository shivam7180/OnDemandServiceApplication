<?php require_once ROOT_PATH . '/app/views/layouts/header.php'; ?>

<section style="padding: 60px 0 80px; min-height: 80vh; background: radial-gradient(circle at 10% 20%, #EEF4ED 0%, transparent 60%);">
  <div class="container" style="max-width: 600px;">
    
    <div class="card reveal-on-scroll" style="padding: 44px 36px; box-shadow: var(--shadow-floating); border-radius: 28px;">
      
      <div style="text-align: center; margin-bottom: 28px;">
        <span class="badge badge-sage" style="margin-bottom: 12px;">Customer Membership</span>
        <h2 style="font-size: 1.85rem; margin-bottom: 6px;">Experience Bespoke Home Care</h2>
        <p style="font-size: 0.95rem; color: var(--charcoal-muted);">Create your account to book vetted specialists and track appointments live.</p>
      </div>

      <form method="POST" action="<?= BASE_URL ?>/auth/registerCustomerSubmit">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken ?? '') ?>">

        <div class="form-group">
          <label class="form-label">Full Name</label>
          <input type="text" name="full_name" class="form-control" required placeholder="e.g. Rahul Sharma">
        </div>

        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Email Address</label>
            <input type="email" name="email" class="form-control" required placeholder="rahul@example.com">
          </div>
          <div class="form-group">
            <label class="form-label">Phone Number</label>
            <input type="tel" name="phone" class="form-control" required placeholder="+91 98765 43210">
          </div>
        </div>

        <!-- Home Address & Interactive GPS Map Section -->
        <div class="form-group" style="background: #FDFBF7; padding: 20px; border-radius: 20px; border: 1px solid var(--border-light); margin-bottom: 22px;">
          <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px; flex-wrap: wrap; gap: 8px;">
            <label class="form-label" style="margin: 0; font-weight: 700; color: var(--forest-green); font-size: 0.95rem;">
              📍 Home Service Address & Location Map
            </label>
            <button type="button" class="btn btn-secondary btn-sm" id="btnAutoDetectGPS" style="padding: 7px 14px; font-size: 0.8rem; display: flex; align-items: center; gap: 6px;" onclick="detectCustomerGPSLocation()">
              <span>📡 Auto-Detect My Location</span>
            </button>
          </div>

          <p style="font-size: 0.82rem; color: var(--charcoal-muted); margin: 0 0 12px; line-height: 1.4;">
            Tap the button above or tap directly on the map below. NEARZO automatically detects your address and connects you with verified trade specialists strictly within <strong>25 km</strong>.
          </p>

          <div class="form-group" style="margin-bottom: 12px;">
            <input type="text" id="custStreetAddress" name="address" class="form-control" required placeholder="e.g. Flat 302, Green Acres, Bandra West" value="Flat 302, Green Acres, Bandra West">
          </div>

          <!-- Embedded Interactive Location Map -->
          <div style="position: relative; border-radius: 14px; overflow: hidden; border: 1px solid var(--border-light); margin-bottom: 12px; box-shadow: 0 4px 14px rgba(35,76,61,0.08);">
            <div id="registerCustomerMap" style="height: 250px; width: 100%; z-index: 1;"></div>
            <div id="mapTapHint" style="position: absolute; bottom: 8px; left: 8px; background: rgba(35, 76, 61, 0.92); color: #FFF; padding: 5px 12px; border-radius: 20px; font-size: 0.74rem; z-index: 999; pointer-events: none; display: flex; align-items: center; gap: 6px; box-shadow: 0 2px 8px rgba(0,0,0,0.2);">
              <span>👆 Tap map or drag pin to update your location</span>
            </div>
          </div>

          <div style="display: flex; justify-content: space-between; align-items: center; font-size: 0.82rem; margin-bottom: 12px; flex-wrap: wrap; gap: 8px;">
            <span id="custLocationStatus" style="font-weight: 600; color: var(--forest-green);">
              📍 GPS Ready: Lat 19.0760, Lng 72.8777
            </span>
            <span class="badge badge-sage" style="font-size: 0.74rem; padding: 3px 10px;">
              ● 25 km Service Match Active
            </span>
          </div>

          <div class="form-row" style="margin-bottom: 0;">
            <div class="form-group" style="margin-bottom: 0;">
              <label class="form-label" style="font-size: 0.8rem;">City</label>
              <input type="text" id="custCity" name="city" class="form-control" value="Mumbai" style="font-size: 0.88rem; padding: 9px 12px;">
            </div>
            <div class="form-group" style="margin-bottom: 0;">
              <label class="form-label" style="font-size: 0.8rem;">Postal Code</label>
              <input type="text" id="custPostalCode" name="postal_code" class="form-control" value="400050" placeholder="400050" style="font-size: 0.88rem; padding: 9px 12px;">
            </div>
          </div>

          <!-- Hidden Coordinates Submitted With Registration -->
          <input type="hidden" id="custLat" name="latitude" value="19.07600000">
          <input type="hidden" id="custLng" name="longitude" value="72.87770000">
        </div>

        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Password</label>
            <input type="password" name="password" class="form-control" required minlength="8" placeholder="At least 8 characters">
          </div>
          <div class="form-group">
            <label class="form-label">Confirm Password</label>
            <input type="password" name="confirm_password" class="form-control" required minlength="8" placeholder="Repeat password">
          </div>
        </div>

        <div style="margin: 16px 0 24px; display: flex; gap: 10px; align-items: flex-start; font-size: 0.88rem; color: var(--charcoal-light);">
          <input type="checkbox" required id="terms" style="margin-top: 4px; accent-color: var(--forest-green);">
          <label for="terms">I agree to NEARZO's Terms of Service, Privacy Policy, and Escrow Guarantee terms.</label>
        </div>

        <button type="submit" class="btn btn-primary btn-lg" style="width: 100%;">
          Create Customer Account
        </button>
      </form>

      <div style="text-align: center; margin-top: 24px; font-size: 0.92rem; color: var(--charcoal-light);">
        Already registered? <a href="<?= BASE_URL ?>/auth/login" style="color: var(--forest-green); font-weight: 700;">Sign In</a>
      </div>

    </div>

  </div>
</section>

<!-- Leaflet Interactive Registration Map & Geocoding Script -->
<script>
let regCustMap = null;
let regCustMarker = null;
let regCustCircle = null;

function initCustomerRegisterMap() {
  const mapElem = document.getElementById('registerCustomerMap');
  if (!mapElem) return;

  // Initial Coordinates: Bandra / Mumbai center
  let initialLat = 19.0760;
  let initialLng = 72.8777;

  // Restore previous location if available
  const savedLat = parseFloat(localStorage.getItem('user_lat'));
  const savedLng = parseFloat(localStorage.getItem('user_lng'));
  if (!isNaN(savedLat) && !isNaN(savedLng)) {
    initialLat = savedLat;
    initialLng = savedLng;
  }

  document.getElementById('custLat').value = initialLat.toFixed(6);
  document.getElementById('custLng').value = initialLng.toFixed(6);

  if (typeof L === 'undefined') {
    mapElem.innerHTML = '<div style="padding: 20px; text-align: center; color: var(--charcoal-muted);">Map loading...</div>';
    return;
  }

  regCustMap = L.map('registerCustomerMap', {
    center: [initialLat, initialLng],
    zoom: 13,
    zoomControl: true
  });

  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 19,
    attribution: '&copy; OpenStreetMap contributors'
  }).addTo(regCustMap);

  // Custom NEARZO House Pin
  const customPin = L.divIcon({
    className: 'nearzo-home-marker',
    html: `
      <div style="background: #234C3D; width: 40px; height: 40px; border-radius: 50% 50% 50% 0; transform: rotate(-45deg); display: flex; align-items: center; justify-content: center; box-shadow: 0 6px 14px rgba(35,76,61,0.4); border: 2.5px solid #FFFFFF;">
        <span style="transform: rotate(45deg); font-size: 18px;">🏠</span>
      </div>
    `,
    iconSize: [40, 40],
    iconAnchor: [20, 40],
    popupAnchor: [0, -40]
  });

  regCustMarker = L.marker([initialLat, initialLng], {
    icon: customPin,
    draggable: true
  }).addTo(regCustMap);

  // 25 km Service Coverage Circle
  regCustCircle = L.circle([initialLat, initialLng], {
    radius: 25000, // 25 km
    color: '#D9795B',
    weight: 2,
    opacity: 0.8,
    fillColor: '#DCE8DA',
    fillOpacity: 0.2,
    dashArray: '6, 6'
  }).addTo(regCustMap);

  regCustMarker.bindPopup("<b>Your Service Location</b><br>Matching specialists within 25 km").openPopup();

  // Tap anywhere on map to set position
  regCustMap.on('click', function(e) {
    updateCustomerLocation(e.latlng.lat, e.latlng.lng, true);
  });

  // Drag marker to fine-tune
  regCustMarker.on('dragend', function(e) {
    const pos = e.target.getLatLng();
    updateCustomerLocation(pos.lat, pos.lng, true);
  });

  setTimeout(() => {
    regCustMap.invalidateSize();
  }, 300);
}

// Update Location Coordinates, Marker, 25km Circle, and Reverse Geocode Address
async function updateCustomerLocation(lat, lng, doGeocode = true) {
  if (regCustMarker) regCustMarker.setLatLng([lat, lng]);
  if (regCustCircle) regCustCircle.setLatLng([lat, lng]);
  if (regCustMap) regCustMap.panTo([lat, lng]);

  document.getElementById('custLat').value = lat.toFixed(6);
  document.getElementById('custLng').value = lng.toFixed(6);

  const status = document.getElementById('custLocationStatus');
  if (status) {
    status.innerHTML = `📍 GPS Locked: <strong>${lat.toFixed(4)}, ${lng.toFixed(4)}</strong>`;
    status.style.color = 'var(--status-success)';
  }

  // Persist for marketplace 25 km queries
  localStorage.setItem('user_lat', lat);
  localStorage.setItem('user_lng', lng);

  // Reverse Geocoding via OpenStreetMap Nominatim
  if (doGeocode) {
    try {
      const resp = await fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&zoom=18&addressdetails=1`);
      if (resp.ok) {
        const data = await resp.json();
        if (data && data.address) {
          const addr = data.address;
          const street = addr.road || addr.suburb || addr.neighbourhood || addr.residential || '';
          const house = addr.house_number ? addr.house_number + ', ' : '';
          const area = addr.suburb || addr.city_district || addr.county || '';
          const fullStreet = [house + street, area].filter(Boolean).join(', ');

          if (fullStreet) {
            document.getElementById('custStreetAddress').value = fullStreet;
            localStorage.setItem('user_location_label', fullStreet);
          }
          if (addr.city || addr.town || addr.village || addr.state_district) {
            document.getElementById('custCity').value = addr.city || addr.town || addr.village || addr.state_district;
          }
          if (addr.postcode) {
            document.getElementById('custPostalCode').value = addr.postcode;
          }
          (window.Nearzo || window.AuraCraft).showToast('Address updated from location pin!', 'success');
        }
      }
    } catch(err) {
      console.warn('Geocoding notice:', err);
    }
  }
}

// Auto-Detect Device GPS Location
function detectCustomerGPSLocation() {
  if (!navigator.geolocation) {
    (window.Nearzo || window.AuraCraft).showToast('Geolocation is not supported by your browser', 'warning');
    return;
  }

  const btn = document.getElementById('btnAutoDetectGPS');
  if (btn) {
    btn.disabled = true;
    btn.innerHTML = '<span>⏳ Detecting GPS...</span>';
  }
  (window.Nearzo || window.AuraCraft).showToast('Fetching current device coordinates...', 'info');

  navigator.geolocation.getCurrentPosition(
    (pos) => {
      if (btn) {
        btn.disabled = false;
        btn.innerHTML = '<span>✓ Location Locked</span>';
      }
      const lat = pos.coords.latitude;
      const lng = pos.coords.longitude;
      updateCustomerLocation(lat, lng, true);
      if (regCustMap) regCustMap.setView([lat, lng], 14);
      (window.Nearzo || window.AuraCraft).showToast('Device location fetched and 25 km radar recalibrated!', 'success');
    },
    (err) => {
      if (btn) {
        btn.disabled = false;
        btn.innerHTML = '<span>📡 Auto-Detect My Location</span>';
      }
      let msg = 'Location request denied or timed out. Tap directly on the map to pin your home.';
      (window.Nearzo || window.AuraCraft).showToast(msg, 'warning');
    },
    { enableHighAccuracy: true, timeout: 10000, maximumAge: 60000 }
  );
}

document.addEventListener('DOMContentLoaded', () => {
  initCustomerRegisterMap();
});
</script>

<?php require_once ROOT_PATH . '/app/views/layouts/footer.php'; ?>
