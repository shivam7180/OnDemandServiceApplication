<?php require_once ROOT_PATH . '/app/views/layouts/header.php'; ?>

<!-- Provider Profile Header -->
<section style="padding: 60px 0 40px; background: linear-gradient(135deg, #FAF8F3 0%, #EEF4ED 100%); border-bottom: 1px solid var(--border-light);">
  <div class="container">
    <div style="display: flex; gap: 32px; align-items: center; flex-wrap: wrap;" class="reveal-on-scroll">
      <div style="width: 100px; height: 100px; border-radius: 50%; background: #FFFFFF; font-size: 3rem; display: flex; align-items: center; justify-content: center; box-shadow: var(--shadow-md); border: 3px solid var(--soft-sage);">
        <?= $provider['avatar'] ?>
      </div>

      <div style="flex: 1; min-width: 280px;">
        <div style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap; margin-bottom: 8px;">
          <span class="badge badge-sage">✓ Certified NEARZO Partner</span>
          <span class="badge badge-success">Available for Booking</span>
        </div>
        <h1 style="font-size: 2.2rem; margin-bottom: 4px;"><?= htmlspecialchars($provider['name']) ?></h1>
        <div style="font-size: 1.1rem; color: var(--forest-green); font-weight: 600; margin-bottom: 8px;">
          <?= htmlspecialchars($provider['business_name']) ?>
        </div>
        <div style="display: flex; gap: 20px; color: var(--charcoal-light); font-size: 0.95rem; flex-wrap: wrap;">
          <span>📍 <?= htmlspecialchars($provider['city']) ?> (Serving <?= $provider['service_radius_km'] ?> km radius)</span>
          <span>★ <strong style="color: #D97706;"><?= number_format($provider['rating_avg'], 2) ?></strong> (<?= $provider['reviews_count'] ?> reviews)</span>
          <span>🛡️ <?= $provider['experience_years'] ?> Years Experience</span>
        </div>
      </div>

      <div style="text-align: right;">
        <div style="font-size: 0.85rem; color: var(--charcoal-muted); text-transform: uppercase;">Standard Base Rate</div>
        <div style="font-size: 2rem; font-weight: 800; color: var(--forest-green); margin-bottom: 10px;">
          ₹<?= number_format($provider['hourly_rate'], 0) ?> <span style="font-size: 1rem; font-weight: 500; color: var(--charcoal-muted);">/ hr</span>
        </div>
        <button type="button" class="btn btn-secondary btn-lg" onclick="AuraCraft.openModal('providerBookingModal')">
          Book This Specialist
        </button>
      </div>
    </div>
  </div>
</section>

<!-- Profile Body Content -->
<section class="section-spacer">
  <div class="container">
    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 40px; align-items: flex-start;">
      
      <!-- Left Column: Bio, Services, Schedule & Reviews -->
      <div style="display: flex; flex-direction: column; gap: 36px;">
        
        <!-- About Section -->
        <div class="card reveal-on-scroll">
          <h3 style="margin-bottom: 14px;">About the Specialist</h3>
          <p style="font-size: 1rem; line-height: 1.7; color: var(--charcoal-light);">
            <?= nl2br(htmlspecialchars($provider['bio'])) ?>
          </p>
        </div>

        <!-- Offered Services -->
        <div class="card reveal-on-scroll">
          <h3 style="margin-bottom: 20px;">Services Offered & Rates</h3>
          <div style="display: flex; flex-direction: column; gap: 14px;">
            <?php foreach ($provider['services'] as $svc): ?>
              <div style="display: flex; justify-content: space-between; align-items: center; padding: 14px 18px; border-radius: 12px; background: var(--bg-muted);">
                <div style="font-weight: 600; color: var(--forest-green); font-size: 1rem;">
                  <?= htmlspecialchars($svc['name']) ?>
                </div>
                <div style="display: flex; align-items: center; gap: 16px;">
                  <span style="font-weight: 700; color: var(--charcoal); font-size: 1.1rem;">
                    ₹<?= number_format($svc['price'], 0) ?>
                  </span>
                  <button type="button" class="btn btn-sage btn-sm" onclick="selectServiceForBooking('<?= htmlspecialchars($svc['name']) ?>', <?= $svc['price'] ?>)">
                    Select
                  </button>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        </div>

        <!-- Weekly Availability -->
        <div class="card reveal-on-scroll">
          <h3 style="margin-bottom: 18px;">Weekly Working Hours</h3>
          <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 12px;">
            <?php foreach ($provider['schedule'] as $day => $hours): ?>
              <div style="padding: 12px 14px; border-radius: 10px; border: 1px solid var(--border-light); background: <?= ($hours === 'Closed') ? '#FAF8F3' : '#FFFFFF' ?>;">
                <div style="font-weight: 600; font-size: 0.9rem; color: var(--forest-green);"><?= $day ?></div>
                <div style="font-size: 0.82rem; color: <?= ($hours === 'Closed') ? 'var(--charcoal-muted)' : 'var(--terracotta)' ?>; font-weight: 500; margin-top: 4px;">
                  <?= $hours ?>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        </div>

        <!-- Client Reviews -->
        <div class="card reveal-on-scroll">
          <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
            <h3 style="margin: 0;">Verified Client Reviews</h3>
            <span class="badge badge-terracotta">★ <?= number_format($provider['rating_avg'], 2) ?> Overall</span>
          </div>

          <div style="display: flex; flex-direction: column; gap: 20px;">
            <?php foreach ($provider['reviews'] as $rev): ?>
              <div style="padding-bottom: 20px; border-bottom: 1px solid var(--border-light);">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                  <div style="font-weight: 700; color: var(--charcoal);"><?= htmlspecialchars($rev['author']) ?></div>
                  <div style="font-size: 0.8rem; color: var(--charcoal-muted);"><?= $rev['date'] ?></div>
                </div>
                <div style="color: #D97706; margin-bottom: 8px;">
                  <?= str_repeat('★', $rev['rating']) ?>
                </div>
                <p style="font-size: 0.92rem; color: var(--charcoal-light); margin: 0;">
                  "<?= htmlspecialchars($rev['comment']) ?>"
                </p>
              </div>
            <?php endforeach; ?>
          </div>
        </div>

      </div>

      <!-- Right Column: Quick Booking Card Sticky -->
      <div style="position: sticky; top: 100px;">
        <div class="card" style="box-shadow: var(--shadow-lg); border-color: var(--soft-sage); padding: 32px;">
          <h3 style="margin-bottom: 12px;">Reserve a Slot</h3>
          <p style="font-size: 0.92rem; color: var(--charcoal-light); margin-bottom: 20px;">
            No payment taken until appointment completion and your 100% satisfaction.
          </p>

          <div style="background: var(--soft-sage-light); border-radius: 12px; padding: 14px; margin-bottom: 20px;">
            <div style="display: flex; align-items: center; gap: 10px; font-weight: 600; color: var(--forest-green); font-size: 0.9rem;">
              <span>🛡️</span>
              <span>NEARZO ₹25,000 Bonded Guarantee</span>
            </div>
          </div>

          <button type="button" class="btn btn-secondary btn-lg" style="width: 100%; margin-bottom: 12px;" onclick="AuraCraft.openModal('providerBookingModal')">
            Book Appointment Now
          </button>
          
          <div style="text-align: center; font-size: 0.82rem; color: var(--charcoal-muted);">
            Free cancellation up to 4 hours before start.
          </div>
        </div>
      </div>

    </div>
  </div>
</section>

<!-- Direct Provider Booking Modal with Geolocation Auto-Detection -->
<div class="modal-overlay" id="providerBookingModal">
  <div class="modal-content">
    <button class="modal-close" aria-label="Close modal">✕</button>
    <span class="badge badge-sage" style="margin-bottom: 12px;">Direct Scheduling</span>
    <h3>Book with <?= htmlspecialchars($provider['name']) ?></h3>
    <p style="font-size: 0.92rem; color: var(--charcoal-muted); margin-bottom: 20px;">
      <?= htmlspecialchars($provider['business_name']) ?>
    </p>

    <form method="POST" action="<?= BASE_URL ?>/booking/create" onsubmit="handleProviderOrderSubmit(event, this)">
      <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken ?? '') ?>">
      <input type="hidden" name="provider_id" value="<?= (int)($provider['id'] ?? 1) ?>">
      <input type="hidden" name="service_id" value="3">
      <input type="hidden" id="providerBookingAmount" name="total_amount" value="<?= (float)$provider['hourly_rate'] ?>">
      <input type="hidden" id="providerLatitude" name="latitude" value="19.0760">
      <input type="hidden" id="providerLongitude" name="longitude" value="72.8777">

      <div class="form-group">
        <label class="form-label">Select Requested Service</label>
        <select id="providerServiceSelect" name="service_name" class="form-control" required onchange="updateBookingAmount(this)">
          <?php foreach ($provider['services'] as $s): ?>
            <option value="<?= htmlspecialchars($s['name']) ?>" data-price="<?= $s['price'] ?>">
              <?= htmlspecialchars($s['name']) ?> - ₹<?= number_format($s['price'], 0) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="form-group">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 4px;">
          <label class="form-label" style="margin: 0;">Service Destination Address</label>
          <button type="button" class="badge badge-terracotta" style="cursor: pointer; border: none; font-size: 0.78rem; text-transform: none;" onclick="fetchProviderBookingLocation()">
            <span>📍 Auto-Detect Location</span>
          </button>
        </div>
        <input type="text" id="providerAddressInput" name="service_address" required class="form-control" value="Flat 402, Green Acres, Bandra West, Mumbai">
        <span id="providerLocationStatus" style="font-size: 0.78rem; color: var(--forest-green); display: block; margin-top: 4px;">
          ✦ Auto-fetches GPS coordinates for specialist route navigation
        </span>
      </div>

      <div class="form-row">
        <div class="form-group">
          <label class="form-label">Appointment Date</label>
          <input type="date" name="booking_date" required class="form-control" value="<?= date('Y-m-d', strtotime('+1 day')) ?>">
        </div>
        <div class="form-group">
          <label class="form-label">Arrival Window</label>
          <select name="booking_time" class="form-control">
            <option value="09:00:00">09:00 AM - 11:00 AM</option>
            <option value="13:00:00">01:00 PM - 03:00 PM</option>
            <option value="16:00:00">04:00 PM - 06:00 PM</option>
          </select>
        </div>
      </div>

      <div class="form-group">
        <label class="form-label">Notes for Specialist</label>
        <textarea name="customer_notes" class="form-control" placeholder="Describe the job, brand model, or access directions..."></textarea>
      </div>

      <button type="submit" class="btn btn-secondary btn-lg" style="width: 100%;">
        Confirm Appointment & Track Route →
      </button>
    </form>
  </div>
</div>

<script>
function selectServiceForBooking(serviceName, price) {
  const select = document.getElementById('providerServiceSelect');
  if (select) {
    for (let opt of select.options) {
      if (opt.value === serviceName) {
        opt.selected = true;
        break;
      }
    }
  }
  document.getElementById('providerBookingAmount').value = price;
  AuraCraft.openModal('providerBookingModal');
}

function updateBookingAmount(selectElem) {
  const selectedOption = selectElem.options[selectElem.selectedIndex];
  const price = selectedOption.getAttribute('data-price') || <?= (float)$provider['hourly_rate'] ?>;
  document.getElementById('providerBookingAmount').value = price;
}

function fetchProviderBookingLocation() {
  const addr = document.getElementById('providerAddressInput');
  const lat = document.getElementById('providerLatitude');
  const lng = document.getElementById('providerLongitude');
  const status = document.getElementById('providerLocationStatus');
  AuraCraft.detectLocation(addr, lat, lng, status);
}

function handleProviderOrderSubmit(event, form) {
  localStorage.setItem('active_order', 'BK-89021');
  document.cookie = "active_order=BK-89021; path=/";
  
  const navTrack = document.getElementById('navTrackItem');
  const headerTrack = document.getElementById('headerTrackBtn');
  if (navTrack) navTrack.style.display = 'block';
  if (headerTrack) headerTrack.style.display = 'inline-flex';
}
</script>

<?php require_once ROOT_PATH . '/app/views/layouts/footer.php'; ?>
