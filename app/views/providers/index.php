<?php require_once ROOT_PATH . '/app/views/layouts/header.php'; ?>

<!-- Marketplace Search & Filter Header -->
<section style="padding: 50px 0 36px; background: radial-gradient(circle at 80% 20%, #F7EAE5 0%, transparent 60%);">
  <div class="container">
    
    <div style="max-width: 720px; margin: 0 auto; text-align: center;" class="reveal-on-scroll">
      <span class="badge badge-terracotta" style="margin-bottom: 12px;">✦ Two-Sided Marketplace Directory</span>
      <h1 style="margin-bottom: 12px; font-size: 2.2rem;">Find Nearby Specialists within 15 km</h1>
      <p style="font-size: 1.05rem; color: var(--charcoal-light);">
        Discover background-checked trade professionals near your exact GPS coordinates with upfront INR pricing.
      </p>
    </div>

    <!-- Location & Service Search Bar -->
    <div class="card" style="margin-top: 28px; padding: 20px 24px; box-shadow: var(--shadow-floating); border-radius: 20px;" class="reveal-on-scroll reveal-delay-1">
      <form method="GET" action="<?= BASE_URL ?>/providers" id="marketplaceSearchForm" style="display: grid; grid-template-columns: 1.4fr 1.6fr 0.8fr 1fr auto; gap: 12px; align-items: flex-end;">
        
        <!-- 1. Select Required Service / Profession -->
        <div>
          <label class="form-label" style="font-size: 0.82rem; margin-bottom: 4px; color: var(--forest-green); font-weight: 700;">Select Profession</label>
          <select name="profession" id="searchProfession" class="form-control" style="font-size: 0.9rem;">
            <option value="all" <?= ($selectedProfession === 'all') ? 'selected' : '' ?>>All Professions</option>
            <option value="Plumber" <?= ($selectedProfession === 'Plumber') ? 'selected' : '' ?>>🔧 Plumber</option>
            <option value="Electrician" <?= ($selectedProfession === 'Electrician') ? 'selected' : '' ?>>⚡ Electrician</option>
            <option value="AC Repair" <?= ($selectedProfession === 'AC Repair') ? 'selected' : '' ?>>❄️ AC Repair</option>
            <option value="Carpenter" <?= ($selectedProfession === 'Carpenter') ? 'selected' : '' ?>>🪚 Carpenter</option>
            <option value="Mechanic" <?= ($selectedProfession === 'Mechanic') ? 'selected' : '' ?>>🚗 Mechanic</option>
            <option value="Cleaner" <?= ($selectedProfession === 'Cleaner') ? 'selected' : '' ?>>🧹 Deep Cleaner</option>
            <option value="Painter" <?= ($selectedProfession === 'Painter') ? 'selected' : '' ?>>🎨 Painter</option>
            <option value="Appliance Repair" <?= ($selectedProfession === 'Appliance Repair') ? 'selected' : '' ?>>🔌 Appliance Care</option>
          </select>
        </div>

        <!-- 2. Customer Location with GPS Auto-Detect Button -->
        <div>
          <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 4px;">
            <label class="form-label" style="font-size: 0.82rem; margin: 0; color: var(--forest-green); font-weight: 700;">Your Location</label>
            <button type="button" style="border: none; background: transparent; color: var(--terracotta); font-size: 0.78rem; font-weight: 700; cursor: pointer; display: flex; align-items: center; gap: 2px;" onclick="syncCustomerGPS()">
              <span>📍 Auto-Detect GPS</span>
            </button>
          </div>
          <input type="text" id="custLocationInput" name="location" class="form-control" style="font-size: 0.9rem;" value="<?= htmlspecialchars($location) ?>" placeholder="e.g. Bandra West, Mumbai">
          <input type="hidden" id="custLat" name="lat" value="<?= $custLat ?>">
          <input type="hidden" id="custLng" name="lng" value="<?= $custLng ?>">
        </div>

        <!-- 3. Search Radius (Max 15 km default) -->
        <div>
          <label class="form-label" style="font-size: 0.82rem; margin-bottom: 4px; color: var(--forest-green); font-weight: 700;">Max Radius</label>
          <select name="radius" class="form-control" style="font-size: 0.9rem;">
            <option value="5" <?= ($radiusKm == 5) ? 'selected' : '' ?>>5 km</option>
            <option value="10" <?= ($radiusKm == 10) ? 'selected' : '' ?>>10 km</option>
            <option value="15" <?= ($radiusKm == 15) ? 'selected' : '' ?>>15 km (Standard)</option>
            <option value="25" <?= ($radiusKm == 25) ? 'selected' : '' ?>>25 km</option>
          </select>
        </div>

        <!-- 4. Sort Filter -->
        <div>
          <label class="form-label" style="font-size: 0.82rem; margin-bottom: 4px; color: var(--forest-green); font-weight: 700;">Sort By</label>
          <select name="sort" class="form-control" style="font-size: 0.9rem;">
            <option value="distance" <?= ($sort === 'distance') ? 'selected' : '' ?>>📍 Nearest First</option>
            <option value="price_asc" <?= ($sort === 'price_asc') ? 'selected' : '' ?>>₹ Price: Low to High</option>
            <option value="price_desc" <?= ($sort === 'price_desc') ? 'selected' : '' ?>>₹ Price: High to Low</option>
            <option value="rating" <?= ($sort === 'rating') ? 'selected' : '' ?>>★ Highest Rated</option>
          </select>
        </div>

        <button type="submit" class="btn btn-primary" style="padding: 10px 20px; font-weight: 600;">
          Filter →
        </button>

      </form>
    </div>

    <!-- Active Search Bar Status & View Toggle -->
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 14px; margin-top: 24px;">
      <div style="font-size: 0.92rem; color: var(--charcoal-muted); display: flex; align-items: center; gap: 8px;">
        <span style="display: inline-block; width: 8px; height: 8px; border-radius: 50%; background: #4ADE80; box-shadow: 0 0 6px #4ADE80;"></span>
        <span>Showing <strong><?= count($providers) ?> verified specialists</strong> within <strong><?= $radiusKm ?> km</strong> (Haversine formula verified).</span>
      </div>

      <!-- Grid vs Map Toggle -->
      <div style="display: flex; gap: 8px;">
        <button type="button" id="btnGridView" class="btn btn-primary btn-sm" onclick="toggleDirectoryView('grid')">
          ⊞ Grid View
        </button>
        <button type="button" id="btnMapView" class="btn btn-outline btn-sm" onclick="toggleDirectoryView('map')">
          🗺️ Interactive Map (<?= count($providers) ?> Pins)
        </button>
      </div>
    </div>

  </div>
</section>

<!-- Main Marketplace Content -->
<section class="section-spacer" style="padding-top: 30px;">
  <div class="container">
    
    <!-- Interactive Leaflet / Google Map Container -->
    <div id="providerMapContainer" style="display: none; height: 500px; width: 100%; border-radius: 24px; overflow: hidden; box-shadow: var(--shadow-lg); border: 1px solid var(--border-light); margin-bottom: 40px; position: relative;">
    </div>

    <!-- Empty State if no providers in 15 km -->
    <?php if (empty($providers)): ?>
      <div class="card" style="text-align: center; padding: 60px 24px;">
        <div style="font-size: 3rem; margin-bottom: 12px;">📍</div>
        <h3 style="color: var(--forest-green); margin-bottom: 8px;">No Specialists Found in this <?= $radiusKm ?> km Radius</h3>
        <p style="color: var(--charcoal-muted); max-width: 480px; margin: 0 auto 20px;">
          Try expanding your search radius to 25 km or selecting "All Professions" to find verified specialists nearby.
        </p>
        <a href="<?= BASE_URL ?>/providers?radius=25&profession=all" class="btn btn-primary">Expand Radius to 25 km &rarr;</a>
      </div>
    <?php else: ?>

      <!-- Provider Cards Grid -->
      <div class="grid-3" id="providerGridContainer">
        <?php foreach ($providers as $p): ?>
          <div class="card card-hoverable tilt-card reveal-on-scroll" style="display: flex; flex-direction: column; justify-content: space-between; border-radius: 24px; border: 1px solid var(--border-light);">
            <div>
              
              <!-- Card Top: Avatar, Name & Distance -->
              <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 16px;">
                <div style="display: flex; gap: 14px; align-items: center;">
                  <div style="width: 58px; height: 58px; border-radius: 50%; background: var(--soft-sage); font-size: 1.8rem; display: flex; align-items: center; justify-content: center; box-shadow: var(--shadow-xs);">
                    <?= $p['avatar'] ?>
                  </div>
                  <div>
                    <h4 style="margin: 0; font-size: 1.15rem; color: var(--charcoal);"><?= htmlspecialchars($p['name']) ?></h4>
                    <span class="badge badge-sage" style="font-size: 0.72rem; padding: 2px 8px; margin-top: 4px; display: inline-block;">
                      <?= htmlspecialchars($p['profession']) ?>
                    </span>
                  </div>
                </div>

                <!-- Distance Badge (Haversine) -->
                <div style="text-align: right;">
                  <span class="badge badge-terracotta" style="font-size: 0.78rem; font-weight: 700;">
                    📍 <?= $p['distance_km'] ?> km
                  </span>
                  <div style="font-size: 0.72rem; color: var(--status-success); font-weight: 600; margin-top: 4px;">
                    ● Available Now
                  </div>
                </div>
              </div>

              <!-- Business Name & Description -->
              <div style="font-weight: 700; color: var(--forest-green); font-size: 0.98rem; margin-bottom: 8px;">
                <?= htmlspecialchars($p['business_name']) ?>
              </div>
              <p style="font-size: 0.88rem; color: var(--charcoal-light); margin-bottom: 18px; line-height: 1.5; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                <?= htmlspecialchars($p['bio']) ?>
              </p>

              <!-- Ratings & Experience Pill -->
              <div style="background: var(--bg-muted); border-radius: 12px; padding: 10px 14px; display: flex; justify-content: space-between; align-items: center; margin-bottom: 18px;">
                <div>
                  <span style="color: #D97706; font-weight: 700;">★ <?= number_format($p['rating_avg'], 2) ?></span>
                  <span style="font-size: 0.8rem; color: var(--charcoal-muted);"> (<?= $p['reviews_count'] ?> reviews)</span>
                </div>
                <div style="font-size: 0.82rem; font-weight: 600; color: var(--forest-green);">
                  <?= $p['experience_years'] ?> yrs experience
                </div>
              </div>

            </div>

            <div>
              <!-- Pricing Row with Price Type -->
              <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 14px; padding-top: 12px; border-top: 1px solid var(--border-light);">
                <div>
                  <span style="font-size: 0.78rem; color: var(--charcoal-muted); display: block;">Service Price:</span>
                  <span style="font-size: 1.35rem; font-weight: 800; color: var(--forest-green);">
                    ₹<?= number_format($p['hourly_rate'], 0) ?>
                  </span>
                  <span style="font-size: 0.82rem; color: var(--charcoal-muted);">
                    <?= ($p['price_type'] === 'per_visit') ? '/ visit' : (($p['price_type'] === 'fixed') ? 'fixed' : '/ hr') ?>
                  </span>
                </div>
                
                <!-- Phone Contact Button -->
                <?php if ($p['show_phone']): ?>
                  <a href="tel:<?= $p['phone'] ?>" class="btn btn-sage btn-sm" style="padding: 8px 12px;" title="Call Specialist Directly">
                    📞 Call
                  </a>
                <?php endif; ?>
              </div>

              <!-- Action Button -->
              <div style="display: flex; gap: 10px;">
                <a href="<?= BASE_URL ?>/providers/detail/<?= $p['id'] ?>" class="btn btn-primary" style="flex: 1; text-align: center; padding: 11px;">
                  Book Specialist →
                </a>
              </div>
            </div>

          </div>
        <?php endforeach; ?>
      </div>

    <?php endif; ?>

  </div>
</section>

<!-- Leaflet & Maps Scripts -->
<script src="<?= BASE_URL ?>/public/js/maps.js"></script>

<script>
const providersMapData = <?= json_encode(array_values(array_map(function($p) {
  return [
    'id'            => $p['id'],
    'business_name' => $p['business_name'],
    'profession'    => $p['profession'],
    'hourly_rate'   => $p['hourly_rate'],
    'rating_avg'    => $p['rating_avg'],
    'distance_km'   => $p['distance_km'],
    'lat'           => $p['latitude'],
    'lng'           => $p['longitude'],
    'profile_url'   => BASE_URL . '/providers/detail/' . $p['id']
  ];
}, $providers))) ?>;

let mapInitialized = false;

function toggleDirectoryView(view) {
  const grid = document.getElementById('providerGridContainer');
  const mapCont = document.getElementById('providerMapContainer');
  const btnGrid = document.getElementById('btnGridView');
  const btnMap = document.getElementById('btnMapView');

  if (view === 'map') {
    if (grid) grid.style.display = 'none';
    if (mapCont) mapCont.style.display = 'block';
    if (btnMap) btnMap.className = 'btn btn-primary btn-sm';
    if (btnGrid) btnGrid.className = 'btn btn-outline btn-sm';

    if (!mapInitialized) {
      AuraMaps.initProviderMap('providerMapContainer', providersMapData);
      mapInitialized = true;
    }
  } else {
    if (mapCont) mapCont.style.display = 'none';
    if (grid) grid.style.display = 'grid';
    if (btnGrid) btnGrid.className = 'btn btn-primary btn-sm';
    if (btnMap) btnMap.className = 'btn btn-outline btn-sm';
  }
}

function syncCustomerGPS() {
  if (!navigator.geolocation) {
    AuraCraft.showToast('Geolocation is not supported by your browser', 'warning');
    return;
  }

  AuraCraft.showToast('Acquiring live GPS satellite position...', 'info');
  navigator.geolocation.getCurrentPosition(
    (pos) => {
      const lat = pos.coords.latitude;
      const lng = pos.coords.longitude;
      document.getElementById('custLat').value = lat;
      document.getElementById('custLng').value = lng;
      document.getElementById('custLocationInput').value = `GPS (${lat.toFixed(4)}, ${lng.toFixed(4)})`;

      localStorage.setItem('user_lat', lat);
      localStorage.setItem('user_lng', lng);
      localStorage.setItem('user_location_label', `GPS (${lat.toFixed(4)}, ${lng.toFixed(4)})`);

      AuraCraft.showToast('GPS position locked! Refreshing 15 km radar...', 'success');
      document.getElementById('marketplaceSearchForm').submit();
    },
    (err) => {
      AuraCraft.showToast('Location permission denied or timed out.', 'warning');
    },
    { enableHighAccuracy: true }
  );
}

document.addEventListener('DOMContentLoaded', () => {
  const savedLat = localStorage.getItem('user_lat');
  const savedLng = localStorage.getItem('user_lng');
  const savedLabel = localStorage.getItem('user_location_label');
  
  if (savedLat && savedLng && !document.getElementById('custLocationInput').value.includes('GPS')) {
    document.getElementById('custLat').value = savedLat;
    document.getElementById('custLng').value = savedLng;
    if (savedLabel) {
      document.getElementById('custLocationInput').value = savedLabel;
    }
  }
});
</script>

<?php require_once ROOT_PATH . '/app/views/layouts/footer.php'; ?>
