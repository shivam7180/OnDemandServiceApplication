<?php require_once ROOT_PATH . '/app/views/layouts/header.php'; ?>

<section style="padding: 60px 0 80px; min-height: 80vh; background: radial-gradient(circle at 90% 20%, #F7EAE5 0%, transparent 60%);">
  <div class="container" style="max-width: 720px;">
    
    <div class="card reveal-on-scroll" style="padding: 44px 38px; box-shadow: var(--shadow-floating); border-radius: 28px;">
      
      <div style="text-align: center; margin-bottom: 28px;">
        <span class="badge badge-terracotta" style="margin-bottom: 12px;">Partner Network</span>
        <h2 style="font-size: 1.85rem; margin-bottom: 6px;">Register as Service Provider</h2>
        <p style="font-size: 0.95rem; color: var(--charcoal-muted);">Deliver trade expertise to verified local clients. Set your own INR rates, define your radius, and manage bookings.</p>
      </div>

      <div style="background: var(--soft-sage-light); border-radius: 12px; padding: 14px 18px; margin-bottom: 24px; display: flex; gap: 12px; align-items: center;">
        <span style="font-size: 1.4rem;">🛡️</span>
        <div style="font-size: 0.86rem; color: var(--forest-green);">
          <strong>Direct Marketplace Access:</strong> Provide your complete service address and GPS location so nearby customers within 15 km can discover and book you.
        </div>
      </div>

      <form method="POST" action="<?= BASE_URL ?>/auth/registerProviderSubmit">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken ?? '') ?>">

        <!-- Row 1: Name and Business -->
        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Full Name</label>
            <input type="text" name="full_name" class="form-control" required placeholder="David Sterling" value="David Sterling">
          </div>
          <div class="form-group">
            <label class="form-label">Business / Brand Name</label>
            <input type="text" name="business_name" class="form-control" required placeholder="Sterling Master Plumbing" value="Sterling Master Craft">
          </div>
        </div>

        <!-- Row 2: Profession and Experience -->
        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Profession / Trade</label>
            <select name="profession" id="providerProfession" class="form-control" required onchange="handleProfessionChange(this.value)">
              <option value="Plumber" selected>🔧 Plumber</option>
              <option value="Electrician">⚡ Electrician</option>
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
            <input type="number" name="experience_years" class="form-control" required min="1" max="50" value="8">
          </div>
        </div>

        <!-- Row 3: Pricing in INR and Price Type -->
        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Service Price (₹ INR)</label>
            <div style="position: relative;">
              <span style="position: absolute; left: 14px; top: 12px; font-weight: 700; color: var(--forest-green);">₹</span>
              <input type="number" name="hourly_rate" class="form-control" required min="100" max="10000" step="10" value="599" style="padding-left: 32px;">
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

        <!-- Row 4: Coverage Radius -->
        <div class="form-group">
          <label class="form-label">Maximum Service Radius (km)</label>
          <input type="number" name="service_radius_km" class="form-control" required min="5" max="50" value="15" placeholder="15">
          <span style="font-size: 0.78rem; color: var(--charcoal-muted); margin-top: 4px; display: block;">
            ✦ You will receive customer requests within this radius (Default: 15 km). You can mark your live availability status (Available, Busy, or Offline) right after signing in.
          </span>
        </div>

        <!-- Location & GPS Auto-Detection -->
        <div class="form-group">
          <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
            <label class="form-label" style="margin: 0;">Complete Service Address & Workshop Location</label>
            <button type="button" class="badge badge-terracotta" style="cursor: pointer; border: none; font-size: 0.78rem; text-transform: none; display: flex; align-items: center; gap: 4px;" onclick="detectProviderLocation()">
              <span>📍 Auto-Detect My Live Location</span>
            </button>
          </div>
          
          <input type="text" id="providerAddressInput" name="address" class="form-control" required placeholder="e.g. Shop 12, Hill Road, Bandra West, Mumbai" value="Shop 12, Hill Road, Bandra West, Mumbai">
          
          <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-top: 8px;">
            <div>
              <span style="font-size: 0.75rem; color: var(--charcoal-muted);">GPS Latitude:</span>
              <input type="number" step="any" id="providerLatitude" name="latitude" class="form-control" required value="19.060000" style="font-size: 0.85rem; padding: 8px 12px;">
            </div>
            <div>
              <span style="font-size: 0.75rem; color: var(--charcoal-muted);">GPS Longitude:</span>
              <input type="number" step="any" id="providerLongitude" name="longitude" class="form-control" required value="72.865000" style="font-size: 0.85rem; padding: 8px 12px;">
            </div>
          </div>
          <span id="providerLocationStatus" style="font-size: 0.78rem; color: var(--forest-green); display: block; margin-top: 6px; font-weight: 500;">
            ✦ Customers within a 15 km radius will locate you via the Haversine distance engine.
          </span>
        </div>

        <!-- Profile Photo / Avatar -->
        <div class="form-group">
          <label class="form-label">Profile Avatar / Photo</label>
          <div style="display: flex; gap: 12px; align-items: center;">
            <select name="avatar" id="avatarSelector" class="form-control" style="max-width: 220px;" onchange="document.getElementById('avatarPreview').textContent = this.value">
              <option value="👨‍🔧" selected>👨‍🔧 Master Plumber</option>
              <option value="⚡">⚡ Electrician</option>
              <option value="❄️">❄️ AC Specialist</option>
              <option value="🪚">🪚 Carpenter</option>
              <option value="🚗">🚗 Mechanic</option>
              <option value="🧹">🧹 Cleaner</option>
              <option value="🎨">🎨 Painter</option>
              <option value="🧑‍💼">🧑‍💼 Contractor</option>
            </select>
            <div id="avatarPreview" style="font-size: 2rem; width: 50px; height: 50px; background: var(--soft-sage); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
              👨‍🔧
            </div>
          </div>
        </div>

        <!-- Row 5: Contact Information -->
        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Email Address</label>
            <input type="email" name="email" class="form-control" required placeholder="david@sterlingcraft.com" value="david@sterlingcraft.com">
          </div>
          <div class="form-group">
            <label class="form-label">Direct Phone Number</label>
            <input type="tel" name="phone" class="form-control" required placeholder="+91 98200 00201" value="+91 98200 00201">
          </div>
        </div>

        <div class="form-group">
          <label class="form-label">Service Description & Trade Experience</label>
          <textarea name="bio" class="form-control" rows="3" required placeholder="Describe your craft, years of field experience, certifications, and specialized tools...">Licensed master specialist with over 8 years of residential and commercial renovation expertise. Dedicated to meticulous craft and rapid response times across the city.</textarea>
        </div>

        <!-- Row 6: Passwords -->
        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Account Password</label>
            <input type="password" name="password" class="form-control" required minlength="6" placeholder="••••••••" value="password">
          </div>
          <div class="form-group">
            <label class="form-label">Confirm Password</label>
            <input type="password" name="confirm_password" class="form-control" required minlength="6" placeholder="••••••••" value="password">
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

<script>
function detectProviderLocation() {
  const addr = document.getElementById('providerAddressInput');
  const lat = document.getElementById('providerLatitude');
  const lng = document.getElementById('providerLongitude');
  const status = document.getElementById('providerLocationStatus');
  AuraCraft.detectLocation(addr, lat, lng, status);
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
</script>

<?php require_once ROOT_PATH . '/app/views/layouts/footer.php'; ?>
