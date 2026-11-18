<?php require_once ROOT_PATH . '/app/views/layouts/header.php'; ?>

<!-- Leaflet Map Stylesheet -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>

<section style="padding: 60px 0 80px; min-height: 80vh; background: radial-gradient(circle at 90% 20%, #F7EAE5 0%, transparent 60%);">
  <div class="container" style="max-width: 740px;">
    
    <div class="card reveal-on-scroll" style="padding: 44px 38px; box-shadow: var(--shadow-floating); border-radius: 28px;">
      
      <div style="text-align: center; margin-bottom: 28px;">
        <span class="badge badge-terracotta" style="margin-bottom: 12px;">Partner Network</span>
        <h2 style="font-size: 1.85rem; margin-bottom: 6px;">Register as Service Provider</h2>
        <p style="font-size: 0.95rem; color: var(--charcoal-muted);">Deliver trade expertise to verified local clients. Set your own INR rates, define your 25 km service radius, and manage bookings.</p>
      </div>

      <div style="background: var(--soft-sage-light); border-radius: 12px; padding: 14px 18px; margin-bottom: 24px; display: flex; gap: 12px; align-items: center;">
        <span style="font-size: 1.4rem;">🛡️</span>
        <div style="font-size: 0.86rem; color: var(--forest-green);">
          <strong>Direct Marketplace Access:</strong> Tap the GPS auto-detect button or tap directly on the map to set your base location. Verified customers within <strong>25 km</strong> will discover you with direct call and booking access.
        </div>
      </div>

      <form method="POST" action="<?= BASE_URL ?>/auth/registerProviderSubmit">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken ?? '') ?>">

        <!-- Row 1: Name and Business -->
        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Full Name</label>
            <input type="text" name="full_name" class="form-control" required placeholder="e.g. Ramesh Sharma" value="">
          </div>
          <div class="form-group">
            <label class="form-label">Business / Brand Name</label>
            <input type="text" name="business_name" class="form-control" required placeholder="e.g. Sharma Quick Electricals" value="">
          </div>
        </div>

        <!-- Row 2: Profession and Experience -->
        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Profession / Trade</label>
            <select name="profession" id="providerProfession" class="form-control" required onchange="handleProfessionChange(this.value)">
              <option value="Plumber">🔧 Plumber</option>
              <option value="Electrician" selected>⚡ Electrician</option>
              <option value="AC Repair">❄️ AC Repair & Maintenance</option>
              <option value="Carpenter">🪚 Carpenter</option>
              <option value="Mechanic">🚗 Auto & Appliance Mechanic</option>
              <option value="Cleaner">🧹 Deep Cleaning & Housekeeping</option>
              <option value="Painter">🎨 Painter & Wall Finishes</option>
              <option value="Appliance Repair">🔌 Home Appliance Care</option>
            </select>
          </div>
          <div class="form-group">
            <label class="form-label">Experience (Years)</label>
            <input type="number" name="experience_years" class="form-control" required min="1" max="50" placeholder="e.g. 5" value="5">
          </div>
        </div>

        <!-- Row 3: Pricing in INR and Price Type -->
        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Service Price (₹ INR)</label>
            <div style="position: relative;">
              <span style="position: absolute; left: 14px; top: 12px; font-weight: 700; color: var(--forest-green);">₹</span>
              <input type="number" name="hourly_rate" class="form-control" required min="100" max="10000" step="10" placeholder="e.g. 499" value="499" style="padding-left: 32px;">
            </div>
          </div>
          <div class="form-group">
            <label class="form-label">Price Type</label>
            <select name="price_type" class="form-control" required>
              <option value="per_hour" selected>Per Hour (₹ / hr)</option>
              <option value="per_visit">Per Visit / Inspection (₹ / visit)</option>
              <option value="fixed">Fixed Price Job (₹ Flat)</option>
            </select>
          </div>
        </div>

        <!-- Location with Embedded Map -->
        <div class="form-group">
          <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px; flex-wrap: wrap; gap: 8px;">
            <label class="form-label" style="margin: 0; font-weight: 700;">Workshop / Base Dispatch Address</label>
            <button type="button" class="btn btn-sage btn-sm" style="padding: 4px 12px; font-size: 0.8rem; display: flex; align-items: center; gap: 6px;" onclick="detectProviderLocation()">
              <span>📡 Auto-Detect My Workshop Location</span>
            </button>
          </div>
          
          <p style="font-size: 0.82rem; color: var(--charcoal-muted); margin: 0 0 10px; line-height: 1.4;">
            Tap the button above or tap directly on the map below. NEARZO automatically fills your address and connects you with nearby customers within <strong>25 km</strong>.
          </p>

          <div style="position: relative; margin-bottom: 12px;">
            <input type="text" id="providerAddressInput" name="address" class="form-control" required placeholder="Enter shop or workshop address (e.g. Linking Road, Bandra West)" value="" style="padding-right: 105px;" oninput="handleManualProviderAddressInput()" onblur="forwardGeocodeProviderAddress()">
            <button type="button" class="btn btn-sage btn-sm" style="position: absolute; right: 6px; top: 6px; padding: 6px 14px; font-size: 0.8rem; font-weight: 700;" onclick="forwardGeocodeProviderAddress()">
              🔍 Locate
            </button>
          </div>

          <!-- Embedded Interactive Location Map -->
          <div style="position: relative; border-radius: 14px; overflow: hidden; border: 1px solid var(--border-light); margin-bottom: 12px; box-shadow: 0 4px 14px rgba(35,76,61,0.08);">
            <div id="registerProviderMap" style="height: 250px; width: 100%; z-index: 1;"></div>
            <div id="providerMapHint" style="position: absolute; bottom: 8px; left: 8px; background: rgba(35, 76, 61, 0.92); color: #FFF; padding: 5px 12px; border-radius: 20px; font-size: 0.74rem; z-index: 999; pointer-events: none; display: flex; align-items: center; gap: 6px; box-shadow: 0 2px 8px rgba(0,0,0,0.2);">
              <span>👆 Tap map or drag pin to position your base location</span>
            </div>
          </div>

          <div style="display: flex; justify-content: space-between; align-items: center; font-size: 0.82rem; margin-bottom: 12px; flex-wrap: wrap; gap: 8px;">
            <span id="providerLocationStatus" style="font-weight: 600; color: var(--forest-green);">
              📍 GPS Ready: Lat 19.0760, Lng 72.8777
            </span>
            <span class="badge badge-sage" style="font-size: 0.74rem; padding: 3px 10px;">
              ● 25 km Service Radar Active
            </span>
          </div>

          <div class="form-row" style="margin-bottom: 0;">
            <div class="form-group" style="margin-bottom: 0;">
              <label class="form-label" style="font-size: 0.8rem;">City</label>
              <input type="text" id="providerCity" name="city" class="form-control" value="Mumbai" style="font-size: 0.88rem; padding: 9px 12px;">
            </div>
            <div class="form-group" style="margin-bottom: 0;">
              <label class="form-label" style="font-size: 0.8rem;">Max Radius (km)</label>
              <input type="number" name="service_radius_km" class="form-control" required min="5" max="25" value="25" style="font-size: 0.88rem; padding: 9px 12px;">
            </div>
          </div>

          <!-- Hidden Coordinates Submitted With Registration -->
          <input type="hidden" id="providerLatitude" name="latitude" value="19.07600000">
          <input type="hidden" id="providerLongitude" name="longitude" value="72.87770000">
        </div>

        <!-- Profile Photo / Avatar -->
        <div class="form-group">
          <label class="form-label">Profile Avatar / Trade Icon</label>
          <div style="display: flex; gap: 12px; align-items: center;">
            <select name="avatar" id="avatarSelector" class="form-control" style="max-width: 220px;" onchange="document.getElementById('avatarPreview').textContent = this.value">
              <option value="⚡" selected>⚡ Electrician</option>
              <option value="👨‍🔧">👨‍🔧 Master Plumber</option>
              <option value="❄️">❄️ AC Specialist</option>
              <option value="🪚">🪚 Carpenter</option>
              <option value="🚗">🚗 Mechanic</option>
              <option value="🧹">🧹 Cleaner</option>
              <option value="🎨">🎨 Painter</option>
              <option value="🧑‍💼">🧑‍💼 Contractor</option>
            </select>
            <div id="avatarPreview" style="font-size: 2rem; width: 50px; height: 50px; background: var(--soft-sage); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
              ⚡
            </div>
          </div>
        </div>

        <!-- Row 5: Contact Information -->
        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Email Address</label>
            <input type="email" name="email" class="form-control" required placeholder="e.g. partner@example.com" value="">
          </div>
          <div class="form-group">
            <label class="form-label">Direct Phone Number</label>
            <input type="tel" name="phone" class="form-control" required placeholder="e.g. +91 98200 12345" value="">
          </div>
        </div>

        <div class="form-group">
          <label class="form-label">Service Description & Trade Experience</label>
          <textarea name="bio" class="form-control" rows="3" required placeholder="Describe your craft, years of field experience, certifications, and specialized tools..."></textarea>
        </div>

        <!-- Row 6: Passwords -->
        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Account Password</label>
            <input type="password" name="password" class="form-control" required minlength="6" placeholder="Create strong password" value="">
          </div>
          <div class="form-group">
            <label class="form-label">Confirm Password</label>
            <input type="password" name="confirm_password" class="form-control" required minlength="6" placeholder="Confirm password" value="">
          </div>
        </div>

        <button type="submit" class="btn btn-secondary btn-lg" style="width: 100%; margin-top: 10px;">
          Register as Service Provider →
        </button>
      </form>

      <div style="text-align: center; margin-top: 24px; font-size: 0.92rem; color: var(--charcoal-light);">
        Already registered? <a href="<?= BASE_URL ?>/auth/login" style="color: var(--forest-green); font-weight: 700;">Partner Sign In</a>
      </div>

    </div>

  </div>
</section>

<!-- Leaflet Map Script & Dynamic Geocoding -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
let regProvMap = null;
let regProvMarker = null;
let regProvCircle = null;

function initProviderRegisterMap() {
  const mapElem = document.getElementById('registerProviderMap');
  if (!mapElem) return;

  let initialLat = 19.0760;
  let initialLng = 72.8777;

  document.getElementById('providerLatitude').value = initialLat.toFixed(6);
  document.getElementById('providerLongitude').value = initialLng.toFixed(6);

  if (typeof L === 'undefined') {
    mapElem.innerHTML = '<div style="padding: 20px; text-align: center; color: var(--charcoal-muted);">Map loading...</div>';
    return;
  }

  regProvMap = L.map('registerProviderMap', {
    center: [initialLat, initialLng],
    zoom: 13,
    zoomControl: true
  });

  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 19,
    attribution: '&copy; OpenStreetMap contributors'
  }).addTo(regProvMap);

  const customPin = L.divIcon({
    className: 'nearzo-provider-marker',
    html: `
      <div style="background: #C46D4E; width: 40px; height: 40px; border-radius: 50% 50% 50% 0; transform: rotate(-45deg); display: flex; align-items: center; justify-content: center; box-shadow: 0 6px 14px rgba(196,109,78,0.4); border: 2.5px solid #FFFFFF;">
        <span style="transform: rotate(45deg); font-size: 19px; line-height: 1;">🛠️</span>
      </div>
    `,
    iconSize: [40, 40],
    iconAnchor: [20, 40],
    popupAnchor: [0, -40]
  });

  regProvMarker = L.marker([initialLat, initialLng], {
    draggable: true,
    icon: customPin
  }).addTo(regProvMap);

  regProvCircle = L.circle([initialLat, initialLng], {
    radius: 25000,
    color: '#C46D4E',
    weight: 2,
    opacity: 0.85,
    fillColor: '#C46D4E',
    fillOpacity: 0.08,
    dashArray: '6, 6'
  }).addTo(regProvMap);

  regProvMarker.on('dragend', function (e) {
    const pos = e.target.getLatLng();
    updateProviderLocationUI(pos.lat, pos.lng, true);
  });

  regProvMap.on('click', function (e) {
    regProvMarker.setLatLng(e.latlng);
    updateProviderLocationUI(e.latlng.lat, e.latlng.lng, true);
  });

  setTimeout(() => {
    regProvMap.invalidateSize();
  }, 400);
}

function updateProviderLocationUI(lat, lng, doReverseGeocode) {
  document.getElementById('providerLatitude').value = lat.toFixed(8);
  document.getElementById('providerLongitude').value = lng.toFixed(8);

  if (regProvCircle) {
    regProvCircle.setLatLng([lat, lng]);
  }

  const statusElem = document.getElementById('providerLocationStatus');
  if (statusElem) {
    statusElem.textContent = `📍 Workshop Location Set: Lat ${lat.toFixed(4)}, Lng ${lng.toFixed(4)}`;
  }

  if (doReverseGeocode) {
    reverseGeocodeProviderLocation(lat, lng);
  }
}

function reverseGeocodeProviderLocation(lat, lng) {
  const statusElem = document.getElementById('providerLocationStatus');
  if (statusElem) statusElem.textContent = '⏳ Resolving workshop address...';

  fetch(`https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${lat}&lon=${lng}`)
    .then(r => r.json())
    .then(data => {
      if (data && data.address) {
        const a = data.address;
        const street = a.road || a.suburb || a.neighbourhood || a.commercial || a.industrial || '';
        const city = a.city || a.town || a.village || a.county || 'Mumbai';
        
        let full = data.display_name.split(',').slice(0, 3).join(', ').trim();
        if (full) {
          document.getElementById('providerAddressInput').value = full;
        }
        if (city) {
          document.getElementById('providerCity').value = city;
        }

        if (statusElem) {
          statusElem.textContent = `📍 Address: ${street || city} (Within 25 km Radar)`;
        }
      } else {
        if (statusElem) statusElem.textContent = `📍 Coordinates: ${lat.toFixed(4)}, ${lng.toFixed(4)}`;
      }
    })
    .catch(() => {
      if (statusElem) statusElem.textContent = `📍 Coordinates: ${lat.toFixed(4)}, ${lng.toFixed(4)}`;
    });
}

// Forward Geocoding: Automatically fetch real map location when provider manually types address
let manualProvGeocodeTimer = null;
function handleManualProviderAddressInput() {
  clearTimeout(manualProvGeocodeTimer);
  manualProvGeocodeTimer = setTimeout(() => {
    forwardGeocodeProviderAddress();
  }, 650);
}

async function forwardGeocodeProviderAddress() {
  const street = (document.getElementById('providerAddressInput')?.value || '').trim();
  const city = (document.getElementById('providerCity')?.value || 'Mumbai').trim();

  if (street.length < 3) return;

  const statusElem = document.getElementById('providerLocationStatus');
  if (statusElem) {
    statusElem.innerHTML = `⏳ Locating on real map: <strong>${street}</strong>...`;
    statusElem.style.color = 'var(--terracotta)';
  }

  const query = `${street}, ${city}, Maharashtra, India`;

  try {
    const resp = await fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}&limit=1`);
    if (resp.ok) {
      const results = await resp.json();
      if (results && results.length > 0) {
        const item = results[0];
        const lat = parseFloat(item.lat);
        const lng = parseFloat(item.lon);

        updateProviderLocationUI(lat, lng, false);
        if (regProvMap) {
          regProvMap.flyTo([lat, lng], 14, { duration: 1.2 });
        }
        if (statusElem) {
          statusElem.innerHTML = `📍 Real Workshop Locked: <strong>Lat ${lat.toFixed(4)}, Lng ${lng.toFixed(4)}</strong>`;
          statusElem.style.color = 'var(--forest-green)';
        }
        const shortName = item.display_name.split(',').slice(0, 2).join(',');
        const app = window.Nearzo || window.AuraCraft;
        if (app && app.showToast) app.showToast(`Real workshop location pinned: ${shortName}`, 'success');
        return;
      }
    }

    // Fallback: try querying city/street directly
    const fallbackResp = await fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(street + ', Mumbai')}&limit=1`);
    if (fallbackResp.ok) {
      const r2 = await fallbackResp.json();
      if (r2 && r2.length > 0) {
        const lat = parseFloat(r2[0].lat);
        const lng = parseFloat(r2[0].lon);
        updateProviderLocationUI(lat, lng, false);
        if (regProvMap) regProvMap.flyTo([lat, lng], 14, { duration: 1.2 });
        if (statusElem) {
          statusElem.innerHTML = `📍 Real Workshop Locked: <strong>Lat ${lat.toFixed(4)}, Lng ${lng.toFixed(4)}</strong>`;
          statusElem.style.color = 'var(--forest-green)';
        }
      }
    }
  } catch(e) {
    console.warn('Forward geocode notice:', e);
  }
}

function detectProviderLocation() {
  const app = window.Nearzo || window.AuraCraft;
  if (!navigator.geolocation) {
    if (app && app.showToast) app.showToast('Geolocation is not supported by your browser.', 'warning');
    return;
  }

  if (app && app.showToast) app.showToast('Acquiring workshop GPS location...', 'info');

  navigator.geolocation.getCurrentPosition(
    (pos) => {
      const lat = pos.coords.latitude;
      const lng = pos.coords.longitude;

      if (regProvMap && regProvMarker) {
        regProvMarker.setLatLng([lat, lng]);
        regProvMap.setView([lat, lng], 14);
        updateProviderLocationUI(lat, lng, true);
      }
      if (app && app.showToast) app.showToast('Live workshop GPS detected successfully!', 'success');
    },
    (err) => {
      if (app && app.showToast) app.showToast('Unable to detect GPS. Please tap your base location on the map.', 'warning');
    },
    { timeout: 10000, maximumAge: 60000 }
  );
}

function handleProfessionChange(prof) {
  const avatarSel = document.getElementById('avatarSelector');
  const preview = document.getElementById('avatarPreview');
  const map = {
    'Plumber': '👨‍🔧',
    'Electrician': '⚡',
    'AC Repair': '❄️',
    'Carpenter': '🪚',
    'Mechanic': '🚗',
    'Cleaner': '🧹',
    'Painter': '🎨',
    'Appliance Repair': '🔌'
  };
  if (map[prof] && avatarSel) {
    avatarSel.value = map[prof];
    preview.textContent = map[prof];
  }
}

document.addEventListener('DOMContentLoaded', () => {
  initProviderRegisterMap();
});
</script>

<?php require_once ROOT_PATH . '/app/views/layouts/footer.php'; ?>
