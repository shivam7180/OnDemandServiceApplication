<?php require_once ROOT_PATH . '/app/views/layouts/header.php'; ?>

<!-- Provider Dashboard Header -->
<section style="padding: 40px 0 24px; background: linear-gradient(135deg, #FAF8F3 0%, #EEF4ED 100%); border-bottom: 1px solid var(--border-light);">
  <div class="container">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 20px;">
      <div>
        <span class="badge badge-terracotta" style="margin-bottom: 8px;">Partner Console</span>
        <h1 style="font-size: 2rem; margin: 0;"><?= htmlspecialchars($provider['business']) ?></h1>
        <div style="font-size: 0.95rem; margin-top: 4px; color: var(--charcoal-muted);">
          Master Specialist: <strong><?= htmlspecialchars($provider['name']) ?></strong> • <?= ($provider['reviews_count'] ?? 0) > 0 ? '★ ' . $provider['rating'] . ' (' . $provider['reviews_count'] . ' reviews)' : '★ New Specialist (0 jobs completed)' ?> • 📍 <?= htmlspecialchars($provider['city'] ?? 'Mumbai') ?>
        </div>
      </div>

      <!-- 3-Way Availability Status Selector -->
      <div style="display: flex; align-items: center; gap: 12px; background: #FFFFFF; padding: 8px 16px; border-radius: var(--radius-full); box-shadow: var(--shadow-sm); border: 1px solid var(--border-light);">
        <span id="availabilityIndicator" style="width: 12px; height: 12px; border-radius: 50%; background: #4ADE80; box-shadow: 0 0 8px #4ADE80;"></span>
        <select id="statusSelect" class="form-control" style="font-size: 0.88rem; padding: 6px 12px; border-radius: 99px; border-color: var(--border-light);" onchange="updateProviderStatus(this.value)">
          <option value="available" selected>● Available (Accepting Jobs)</option>
          <option value="busy">⏳ Busy (On Active Call)</option>
          <option value="offline">⚪ Offline (Taking Rest)</option>
        </select>
        <button type="button" class="btn btn-sage btn-sm" style="padding: 6px 12px; font-size: 0.8rem;" onclick="Nearzo.openModal('initialAvailabilityModal')">
          Change Status
        </button>
      </div>
    </div>
  </div>
</section>

<!-- Provider Dashboard Main -->
<section class="section-spacer" style="padding-top: 36px;">
  <div class="container">
    
    <!-- Earnings & Metrics Grid -->
    <div class="grid-4" style="margin-bottom: 36px;">
      <div class="card reveal-on-scroll">
        <div style="font-size: 0.82rem; color: var(--charcoal-muted); text-transform: uppercase; font-weight: 600;">Earnings This Month</div>
        <div class="counter-value" style="font-size: 2.2rem; margin-top: 6px;" data-counter="<?= (int)($provider['month_earnings'] ?? 0) ?>" data-prefix="₹">0</div>
        <div style="font-size: 0.8rem; color: <?= ($provider['month_earnings'] ?? 0) > 0 ? 'var(--status-success)' : 'var(--charcoal-muted)' ?>; margin-top: 4px;">
          <?= ($provider['month_earnings'] ?? 0) > 0 ? '↑ Active cycle' : 'No earnings yet this month' ?>
        </div>
      </div>

      <div class="card reveal-on-scroll reveal-delay-1">
        <div style="font-size: 0.82rem; color: var(--charcoal-muted); text-transform: uppercase; font-weight: 600;">Pending Escrow Payout</div>
        <div class="counter-value" style="font-size: 2.2rem; margin-top: 6px;" data-counter="<?= (int)($provider['pending_payout'] ?? 0) ?>" data-prefix="₹">0</div>
        <div style="font-size: 0.8rem; color: var(--charcoal-muted); margin-top: 4px;">
          <?= ($provider['pending_payout'] ?? 0) > 0 ? 'Direct UPI / NEFT payout: Monday' : '₹0.00 escrow balance' ?>
        </div>
      </div>

      <div class="card reveal-on-scroll reveal-delay-2">
        <div style="font-size: 0.82rem; color: var(--charcoal-muted); text-transform: uppercase; font-weight: 600;">Completed Jobs</div>
        <div class="counter-value" style="font-size: 2.2rem; margin-top: 6px;" data-counter="<?= (int)($provider['completed_jobs'] ?? 0) ?>">0</div>
        <div style="font-size: 0.8rem; color: var(--forest-green); margin-top: 4px;">
          <?= ($provider['completed_jobs'] ?? 0) > 0 ? '100% on-time arrival in 25 km' : '0 jobs completed so far' ?>
        </div>
      </div>

      <div class="card reveal-on-scroll reveal-delay-3">
        <div style="font-size: 0.82rem; color: var(--charcoal-muted); text-transform: uppercase; font-weight: 600;">Marketplace Rating</div>
        <?php if (($provider['completed_jobs'] ?? 0) > 0): ?>
          <div class="counter-value" style="font-size: 2.2rem; margin-top: 6px;" data-counter="<?= (float)($provider['rating'] ?? 5.0) ?>" data-decimals="2">0</div>
          <div style="font-size: 0.8rem; color: #D97706; margin-top: 4px;">★ Top Verified Craftsman</div>
        <?php else: ?>
          <div style="font-size: 1.8rem; font-weight: 800; margin-top: 6px; color: var(--forest-green);">New Partner</div>
          <div style="font-size: 0.8rem; color: var(--charcoal-muted); margin-top: 4px;">Awaiting first verified job rating</div>
        <?php endif; ?>
      </div>
    </div>

    <!-- Main Grid: Incoming Requests & Profile Management -->
    <div style="display: grid; grid-template-columns: 1.6fr 1.4fr; gap: 32px; align-items: flex-start; margin-bottom: 40px;">
      
      <!-- Left Column: Pending Booking Requests -->
      <div>
        <h3 style="margin-bottom: 18px; display: flex; justify-content: space-between; align-items: center;">
          <span>Incoming Customer Requests</span>
          <span class="badge badge-terracotta" style="font-size: 0.78rem;">Live Dispatch</span>
        </h3>

        <?php if (empty($pendingRequests)): ?>
          <div class="card" style="text-align: center; padding: 40px;">
            <div style="font-size: 2.5rem; margin-bottom: 8px;">📡</div>
            <h4 style="margin-bottom: 6px; color: var(--forest-green);">Radar Active</h4>
            <p style="color: var(--charcoal-muted); margin: 0;">Waiting for new customer requests within your 25 km radius...</p>
          </div>
        <?php else: ?>
          <div style="display: flex; flex-direction: column; gap: 18px;">
            <?php foreach ($pendingRequests as $req): ?>
              <div class="card reveal-on-scroll" id="request-card-<?= $req['id'] ?>" style="border-left: 6px solid var(--forest-green); padding: 26px;">
                <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 16px;">
                  <div>
                    <div style="display: flex; gap: 10px; align-items: center; margin-bottom: 8px;">
                      <span class="badge badge-warning">Awaiting Your Confirmation</span>
                      <span style="font-weight: 700; color: var(--charcoal-muted); font-size: 0.85rem;">Code: <?= $req['code'] ?></span>
                    </div>
                    <h3 style="font-size: 1.3rem; margin-bottom: 6px;"><?= htmlspecialchars($req['service']) ?></h3>
                    <div style="font-size: 0.92rem; color: var(--charcoal-light); margin-bottom: 6px;">
                      Client: <strong><?= htmlspecialchars($req['customer']) ?></strong> (<?= $req['phone'] ?>)
                    </div>
                    <div style="font-size: 0.88rem; color: var(--forest-green); font-weight: 600; margin-bottom: 6px;">
                      📍 Destination: <?= htmlspecialchars($req['address']) ?>
                    </div>
                    <?php if (!empty($req['notes'])): ?>
                      <div style="margin-top: 8px; background: var(--bg-muted); padding: 8px 12px; border-radius: 8px; font-size: 0.85rem; color: var(--charcoal);">
                        <strong>Client Note:</strong> "<?= htmlspecialchars($req['notes']) ?>"
                      </div>
                    <?php endif; ?>
                  </div>

                  <div style="display: flex; flex-direction: column; align-items: flex-end; gap: 12px;">
                    <div style="font-size: 1.6rem; font-weight: 800; color: var(--forest-green);">
                      ₹<?= number_format($req['amount'], 0) ?>
                    </div>
                    <div style="display: flex; gap: 8px;">
                      <button type="button" class="btn btn-primary btn-sm" onclick="acceptRequest(<?= $req['id'] ?>, '<?= $req['code'] ?>')">
                        ✓ Accept Job
                      </button>
                      <button type="button" class="btn btn-outline btn-sm" style="border-color: #D32F2F; color: #D32F2F;" onclick="rejectRequest(<?= $req['id'] ?>)">
                        Decline
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </div>

      <!-- Right Column: Profile, Rate & Location Manager -->
      <div class="card reveal-on-scroll" style="padding: 30px; border-radius: 24px;">
        <h3 style="margin-bottom: 6px; font-size: 1.25rem;">Marketplace Profile & Rates</h3>
        <p style="font-size: 0.88rem; color: var(--charcoal-muted); margin-bottom: 20px;">
          Adjust your trade profession, hourly/visit pricing in INR, and GPS coordinates to match your current dispatch point.
        </p>

        <form id="providerProfileForm" onsubmit="handleProfileUpdate(event)">
          <div class="form-group">
            <label class="form-label">Primary Trade / Profession</label>
            <select id="editProfession" class="form-control">
              <option value="Plumber" selected>🔧 Master Plumber</option>
              <option value="Electrician">⚡ Certified Electrician</option>
              <option value="AC Repair">❄️ AC Repair Specialist</option>
              <option value="Carpenter">🪚 Master Carpenter</option>
              <option value="Mechanic">🚗 Auto & Machine Mechanic</option>
              <option value="Cleaner">🧹 Deep Cleaning & Housekeeper</option>
              <option value="Painter">🎨 Painter & Wall Artist</option>
            </select>
          </div>

          <div class="form-row">
            <div class="form-group">
              <label class="form-label">Rate (₹ INR)</label>
              <div style="position: relative;">
                <span style="position: absolute; left: 12px; top: 11px; font-weight: 700; color: var(--forest-green);">₹</span>
                <input type="number" id="editRate" class="form-control" value="599" style="padding-left: 28px;">
              </div>
            </div>
            <div class="form-group">
              <label class="form-label">Price Type</label>
              <select id="editPriceType" class="form-control">
                <option value="per_hour" selected>Per Hour (₹ / hr)</option>
                <option value="per_visit">Per Visit (₹ / visit)</option>
                <option value="fixed">Fixed Rate Job</option>
              </select>
            </div>
          </div>

          <div class="form-group">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 4px;">
              <label class="form-label" style="margin: 0;">Workshop / Current GPS Base</label>
              <button type="button" style="border: none; background: transparent; color: var(--terracotta); font-size: 0.78rem; font-weight: 700; cursor: pointer;" onclick="updateCurrentGPS()">
                📍 Auto-Detect Live GPS
              </button>
            </div>
            <input type="text" id="editAddress" class="form-control" value="Shop 12, Hill Road, Bandra West, Mumbai">
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 8px; margin-top: 6px;">
              <input type="text" id="editLat" class="form-control" value="19.060000" style="font-size: 0.8rem; padding: 6px 10px;" title="Latitude">
              <input type="text" id="editLng" class="form-control" value="72.865000" style="font-size: 0.8rem; padding: 6px 10px;" title="Longitude">
            </div>
          </div>

          <button type="submit" class="btn btn-secondary btn-md" style="width: 100%; margin-top: 6px;">
            Save Marketplace Profile & Rates →
          </button>
        </form>
      </div>

    </div>

    <!-- Confirmed Upcoming Schedule Table -->
    <div class="card reveal-on-scroll">
      <h3 style="margin-bottom: 20px;">Confirmed Upcoming Appointments</h3>

      <?php if (empty($upcomingJobs)): ?>
        <div style="text-align: center; padding: 40px; color: var(--charcoal-muted);">
          <div style="font-size: 2.5rem; margin-bottom: 8px;">📅</div>
          <h4 style="margin-bottom: 6px; color: var(--charcoal);">No Upcoming Appointments Scheduled</h4>
          <p style="margin: 0; font-size: 0.9rem;">When you accept incoming customer requests, confirmed bookings will appear in this dispatch schedule.</p>
        </div>
      <?php else: ?>
        <div style="overflow-x: auto;">
          <table style="width: 100%; border-collapse: collapse; text-align: left;">
            <thead>
              <tr style="border-bottom: 2px solid var(--border-light); font-size: 0.85rem; color: var(--charcoal-muted); text-transform: uppercase;">
                <th style="padding: 12px 16px;">Booking Code</th>
                <th style="padding: 12px 16px;">Client Name</th>
                <th style="padding: 12px 16px;">Service</th>
                <th style="padding: 12px 16px;">Appointment Time</th>
                <th style="padding: 12px 16px;">Service Address</th>
                <th style="padding: 12px 16px;">Payout</th>
                <th style="padding: 12px 16px; text-align: right;">Action</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($upcomingJobs as $job): ?>
                <tr style="border-bottom: 1px solid var(--border-light); font-size: 0.92rem;">
                  <td style="padding: 16px; font-weight: 700; color: var(--forest-green);"><?= $job['code'] ?></td>
                  <td style="padding: 16px; font-weight: 600;"><?= htmlspecialchars($job['customer']) ?></td>
                  <td style="padding: 16px;"><?= htmlspecialchars($job['service']) ?></td>
                  <td style="padding: 16px; font-weight: 600;"><?= $job['date'] ?></td>
                  <td style="padding: 16px; color: var(--charcoal-light);"><?= htmlspecialchars($job['address']) ?></td>
                  <td style="padding: 16px; font-weight: 700;">₹<?= number_format($job['amount'], 0) ?></td>
                  <td style="padding: 16px; text-align: right;">
                    <a href="<?= BASE_URL ?>/booking/track" class="btn btn-outline btn-sm">
                      Open Job Tracker
                    </a>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php endif; ?>
    </div>

  </div>
</section>

<!-- Initial Availability Status Modal (Auto-pops on provider sign-in) -->
<div id="initialAvailabilityModal" class="modal-overlay">
  <div class="modal-card" style="max-width: 580px; padding: 40px 32px; border-radius: 28px; text-align: center;">
    <div style="width: 60px; height: 60px; border-radius: 50%; background: var(--soft-sage); margin: 0 auto 16px; display: flex; align-items: center; justify-content: center; font-size: 2rem;">
      ⏱️
    </div>
    <span class="badge badge-terracotta" style="margin-bottom: 8px;">Partner Shift Check-In</span>
    <h2 style="font-size: 1.75rem; margin-bottom: 8px; color: var(--forest-green);">Mark Your Availability Status</h2>
    <p style="font-size: 0.92rem; color: var(--charcoal-muted); margin-bottom: 24px; line-height: 1.5;">
      Welcome to your partner console! Choose your current shift status so local customers within a 25 km radius know when you are available for booking.
    </p>

    <!-- 3 Choice Cards -->
    <div style="display: flex; flex-direction: column; gap: 12px; margin-bottom: 26px; text-align: left;">
      
      <!-- Option 1: Available Now -->
      <div id="optAvailable" class="card" style="padding: 16px 20px; border: 2px solid var(--forest-green); border-radius: 18px; cursor: pointer; transition: all 0.2s ease; display: flex; align-items: center; justify-content: space-between; background: #F4FAF4;" onclick="selectInitialStatus('available')">
        <div style="display: flex; gap: 14px; align-items: center;">
          <div style="width: 14px; height: 14px; border-radius: 50%; background: #4ADE80; box-shadow: 0 0 10px #4ADE80;"></div>
          <div>
            <strong style="color: var(--forest-green); font-size: 1.02rem; display: block;">● Available (Online & Accepting Jobs)</strong>
            <span style="font-size: 0.82rem; color: var(--charcoal-muted);">Live on the 25 km search radar. Receive instant customer calls & bookings.</span>
          </div>
        </div>
        <span id="checkAvailable" style="font-size: 1.3rem; color: var(--forest-green); font-weight: 800;">✓</span>
      </div>

      <!-- Option 2: Busy -->
      <div id="optBusy" class="card" style="padding: 16px 20px; border: 2px solid var(--border-light); border-radius: 18px; cursor: pointer; transition: all 0.2s ease; display: flex; align-items: center; justify-content: space-between; background: #FFF;" onclick="selectInitialStatus('busy')">
        <div style="display: flex; gap: 14px; align-items: center;">
          <div style="width: 14px; height: 14px; border-radius: 50%; background: #F59E0B; box-shadow: 0 0 10px #F59E0B;"></div>
          <div>
            <strong style="color: #D97706; font-size: 1.02rem; display: block;">⏳ Busy (On Active Assignment)</strong>
            <span style="font-size: 0.82rem; color: var(--charcoal-muted);">Currently working on a client job. Temporarily pause new immediate calls.</span>
          </div>
        </div>
        <span id="checkBusy" style="font-size: 1.3rem; color: #D97706; font-weight: 800; display: none;">✓</span>
      </div>

      <!-- Option 3: Offline -->
      <div id="optOffline" class="card" style="padding: 16px 20px; border: 2px solid var(--border-light); border-radius: 18px; cursor: pointer; transition: all 0.2s ease; display: flex; align-items: center; justify-content: space-between; background: #FFF;" onclick="selectInitialStatus('offline')">
        <div style="display: flex; gap: 14px; align-items: center;">
          <div style="width: 14px; height: 14px; border-radius: 50%; background: #9CA3AF;"></div>
          <div>
            <strong style="color: var(--charcoal-muted); font-size: 1.02rem; display: block;">⚪ Offline (Rest / Off-Duty)</strong>
            <span style="font-size: 0.82rem; color: var(--charcoal-muted);">Hide profile from live map directory. No job requests will be dispatched.</span>
          </div>
        </div>
        <span id="checkOffline" style="font-size: 1.3rem; color: var(--charcoal-muted); font-weight: 800; display: none;">✓</span>
      </div>

    </div>

    <button type="button" class="btn btn-primary btn-lg" style="width: 100%; font-weight: 700;" onclick="confirmInitialStatus()">
      Confirm Availability & Enter Console →
    </button>
  </div>
</div>

<script>
let selectedStatus = 'available';

function selectInitialStatus(status) {
  selectedStatus = status;
  const optAvail = document.getElementById('optAvailable');
  const optBusy = document.getElementById('optBusy');
  const optOff = document.getElementById('optOffline');

  const checkAvail = document.getElementById('checkAvailable');
  const checkBusy = document.getElementById('checkBusy');
  const checkOff = document.getElementById('checkOffline');

  // Reset all
  optAvail.style.borderColor = 'var(--border-light)';
  optAvail.style.background = '#FFF';
  checkAvail.style.display = 'none';

  optBusy.style.borderColor = 'var(--border-light)';
  optBusy.style.background = '#FFF';
  checkBusy.style.display = 'none';

  optOff.style.borderColor = 'var(--border-light)';
  optOff.style.background = '#FFF';
  checkOff.style.display = 'none';

  // Highlight selected
  if (status === 'available') {
    optAvail.style.borderColor = 'var(--forest-green)';
    optAvail.style.background = '#F4FAF4';
    checkAvail.style.display = 'block';
  } else if (status === 'busy') {
    optBusy.style.borderColor = '#F59E0B';
    optBusy.style.background = '#FFFBEB';
    checkBusy.style.display = 'block';
  } else {
    optOff.style.borderColor = '#9CA3AF';
    optOff.style.background = '#F9FAFB';
    checkOff.style.display = 'block';
  }
}

function confirmInitialStatus() {
  updateProviderStatus(selectedStatus);
  const statusSelect = document.getElementById('statusSelect');
  if (statusSelect) statusSelect.value = selectedStatus;

  sessionStorage.setItem('provider_avail_prompted', 'true');
  document.cookie = "prompt_avail=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
  document.cookie = "provider_avail=" + selectedStatus + "; path=/;";

  const app = window.Nearzo || window.AuraCraft;
  if (app && app.closeModal) app.closeModal('initialAvailabilityModal');
  if (app && app.showToast) app.showToast(`Initial shift status locked: ${selectedStatus.toUpperCase()}`, 'success');
}

// Auto-prompt initial availability status on login if not already acknowledged this session
document.addEventListener('DOMContentLoaded', () => {
  const hasPromptCookie = document.cookie.includes('prompt_avail=1');
  const isSessionPrompted = sessionStorage.getItem('provider_avail_prompted');

  if (hasPromptCookie || !isSessionPrompted) {
    setTimeout(() => {
      const app = window.Nearzo || window.AuraCraft;
      if (app && app.openModal) app.openModal('initialAvailabilityModal');
    }, 350);
  }
});
function updateProviderStatus(val) {
  const indicator = document.getElementById('availabilityIndicator');
  const app = window.Nearzo || window.AuraCraft;
  if (val === 'available') {
    indicator.style.background = '#4ADE80';
    indicator.style.boxShadow = '0 0 8px #4ADE80';
    if (app && app.showToast) app.showToast('Availability status updated: You are now LIVE for jobs within 25 km.', 'success');
  } else if (val === 'busy') {
    indicator.style.background = '#F59E0B';
    indicator.style.boxShadow = '0 0 8px #F59E0B';
    if (app && app.showToast) app.showToast('Status set to BUSY. You will not be dispatched new immediate calls.', 'warning');
  } else {
    indicator.style.background = '#9CA3AF';
    indicator.style.boxShadow = 'none';
    if (app && app.showToast) app.showToast('You are now OFFLINE. Profile hidden from live map search.', 'info');
  }
}

function updateCurrentGPS() {
  const app = window.Nearzo || window.AuraCraft;
  if (!navigator.geolocation) {
    if (app && app.showToast) app.showToast('Geolocation not supported by browser', 'warning');
    return;
  }
  if (app && app.showToast) app.showToast('Fetching current device coordinates...', 'info');
  navigator.geolocation.getCurrentPosition(
    (pos) => {
      document.getElementById('editLat').value = pos.coords.latitude.toFixed(6);
      document.getElementById('editLng').value = pos.coords.longitude.toFixed(6);
      document.getElementById('editAddress').value = `📍 GPS Location (${pos.coords.latitude.toFixed(4)}, ${pos.coords.longitude.toFixed(4)})`;
      if (app && app.showToast) app.showToast('Base GPS location updated!', 'success');
    },
    (err) => {
      if (app && app.showToast) app.showToast('Location permission denied or timed out.', 'warning');
    }
  );
}

function handleProfileUpdate(e) {
  e.preventDefault();
  const prof = document.getElementById('editProfession').value;
  const rate = document.getElementById('editRate').value;
  const type = document.getElementById('editPriceType').value;
  const app = window.Nearzo || window.AuraCraft;
  if (app && app.showToast) app.showToast(`Marketplace profile saved: ${prof} at ₹${rate} (${type})!`, 'success');
}

function acceptRequest(id, code) {
  const card = document.getElementById('request-card-' + id);
  if (card) {
    card.style.opacity = '0';
    card.style.transform = 'scale(0.95)';
    card.style.transition = 'all 0.3s ease';
    setTimeout(() => card.remove(), 300);
  }
  const app = window.Nearzo || window.AuraCraft;
  if (app && app.showToast) app.showToast(`Job ${code} accepted! Route scheduled on live tracker.`, 'success');
}

function rejectRequest(id) {
  const card = document.getElementById('request-card-' + id);
  if (card) {
    card.style.opacity = '0';
    card.style.transform = 'scale(0.95)';
    card.style.transition = 'all 0.3s ease';
    setTimeout(() => card.remove(), 300);
  }
  const app = window.Nearzo || window.AuraCraft;
  if (app && app.showToast) app.showToast('Request declined. Job returned to platform pool.', 'info');
}
</script>

<?php require_once ROOT_PATH . '/app/views/layouts/footer.php'; ?>
