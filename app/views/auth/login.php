<?php require_once ROOT_PATH . '/app/views/layouts/header.php'; ?>

<section style="padding: 70px 0 90px; min-height: 80vh; display: flex; align-items: center; background: radial-gradient(circle at 50% 30%, #EEF4ED 0%, transparent 70%);">
  <div class="container" style="max-width: 540px;">
    
    <div class="card reveal-on-scroll" style="padding: 44px 36px; box-shadow: var(--shadow-floating); border-radius: 28px;">
      
      <!-- Role Switcher Tabs -->
      <div style="display: grid; grid-template-columns: 1fr 1fr; background: var(--bg-muted); border-radius: 99px; padding: 4px; margin-bottom: 28px;">
        <button type="button" id="tabCustomer" class="btn btn-primary btn-sm" style="border-radius: 99px; padding: 8px 16px;" onclick="switchRole('customer')">
          👤 Customer Sign In
        </button>
        <button type="button" id="tabProvider" class="btn btn-outline btn-sm" style="border-radius: 99px; padding: 8px 16px; border: none;" onclick="switchRole('provider')">
          🛠️ Provider Sign In
        </button>
      </div>

      <div style="text-align: center; margin-bottom: 24px;">
        <div class="brand-icon" style="margin: 0 auto 14px; width: 48px; height: 48px; font-size: 1.4rem; background: var(--forest-green);">✦</div>
        <h2 id="loginHeading" style="font-size: 1.85rem; margin-bottom: 6px;">Customer Sign In</h2>
        <p id="loginSubheading" style="font-size: 0.95rem; color: var(--charcoal-muted);">Sign in to book home services, track live GPS arrivals, and view your appointments.</p>
      </div>

      <!-- Quick Demo Account Fillers -->
      <div style="background: var(--bg-muted); border-radius: 14px; padding: 12px 16px; margin-bottom: 22px;">
        <div style="font-size: 0.78rem; font-weight: 700; color: var(--forest-green); text-transform: uppercase; margin-bottom: 8px;">
          ⚡ Quick Demo Logins (Click to autofill):
        </div>
        <div style="display: flex; gap: 8px; flex-wrap: wrap;">
          <button type="button" class="btn btn-sage btn-sm" onclick="fillDemo('sophia@servicehub.com', 'password', 'customer')">
            👤 Customer Demo
          </button>
          <button type="button" class="btn btn-sage btn-sm" onclick="fillDemo('david@sterlingcraft.com', 'password', 'provider')">
            🛠️ Provider Demo
          </button>
          <button type="button" class="btn btn-sage btn-sm" onclick="fillDemo('admin@servicehub.com', 'password', 'admin')">
            ⚙️ Admin Demo
          </button>
        </div>
      </div>

      <form id="loginForm" method="POST" action="<?= BASE_URL ?>/auth/loginSubmit">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken ?? '') ?>">
        <input type="hidden" id="loginRoleInput" name="intended_role" value="customer">

        <div class="form-group">
          <label class="form-label" for="emailInput">Email Address</label>
          <input type="email" id="emailInput" name="email" class="form-control" required placeholder="sophia@servicehub.com" autocomplete="email">
        </div>

        <div class="form-group">
          <div style="display: flex; justify-content: space-between; align-items: center;">
            <label class="form-label" for="passwordInput">Password</label>
            <a href="#" style="font-size: 0.82rem; color: var(--terracotta); font-weight: 600;" onclick="AuraCraft.showToast('Demo password for all accounts is: password', 'info')">Forgot?</a>
          </div>
          <input type="password" id="passwordInput" name="password" class="form-control" required placeholder="••••••••" autocomplete="current-password" value="password">
        </div>

        <button type="submit" id="loginSubmitBtn" class="btn btn-primary btn-lg" style="width: 100%; margin-top: 10px;">
          Sign In as Customer →
        </button>
      </form>

      <!-- Separate Registration Options -->
      <div style="margin-top: 28px; padding-top: 22px; border-top: 1px solid var(--border-light); text-align: center;">
        <div style="font-size: 0.88rem; color: var(--charcoal-muted); margin-bottom: 12px; font-weight: 600;">
          Don't have an account yet? Create one below:
        </div>
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
          <a href="<?= BASE_URL ?>/auth/registerCustomer" class="btn btn-outline btn-sm" style="display: flex; align-items: center; justify-content: center; gap: 4px; padding: 10px;">
            <span>👤 Join as Customer</span>
          </a>
          <a href="<?= BASE_URL ?>/auth/registerProvider" class="btn btn-secondary btn-sm" style="display: flex; align-items: center; justify-content: center; gap: 4px; padding: 10px;">
            <span>🛠️ Register as Provider</span>
          </a>
        </div>
      </div>

    </div>

  </div>
</section>

<script>
let currentRole = 'customer';

function switchRole(role) {
  currentRole = role;
  const tabCust = document.getElementById('tabCustomer');
  const tabProv = document.getElementById('tabProvider');
  const heading = document.getElementById('loginHeading');
  const subheading = document.getElementById('loginSubheading');
  const submitBtn = document.getElementById('loginSubmitBtn');
  const roleInput = document.getElementById('loginRoleInput');

  if (role === 'customer') {
    tabCust.className = 'btn btn-primary btn-sm';
    tabCust.style.background = 'var(--forest-green)';
    tabCust.style.color = '#FFF';
    tabProv.className = 'btn btn-outline btn-sm';
    tabProv.style.background = 'transparent';
    tabProv.style.color = 'var(--charcoal)';
    tabProv.style.border = 'none';

    heading.textContent = 'Customer Sign In';
    subheading.textContent = 'Sign in to book home services, track live GPS arrivals, and view your appointments.';
    submitBtn.textContent = 'Sign In as Customer →';
    submitBtn.className = 'btn btn-primary btn-lg';
    roleInput.value = 'customer';
    fillDemo('sophia@servicehub.com', 'password', 'customer');
  } else {
    tabProv.className = 'btn btn-secondary btn-sm';
    tabProv.style.background = 'var(--terracotta)';
    tabProv.style.color = '#FFF';
    tabCust.className = 'btn btn-outline btn-sm';
    tabCust.style.background = 'transparent';
    tabCust.style.color = 'var(--charcoal)';
    tabCust.style.border = 'none';

    heading.textContent = 'Service Provider Sign In';
    subheading.textContent = 'Sign in to your partner portal to manage job requests, update your rate in INR, and review earnings.';
    submitBtn.textContent = 'Sign In as Service Provider →';
    submitBtn.className = 'btn btn-secondary btn-lg';
    roleInput.value = 'provider';
    fillDemo('david@sterlingcraft.com', 'password', 'provider');
  }
}

function fillDemo(email, pass, role) {
  document.getElementById('emailInput').value = email;
  document.getElementById('passwordInput').value = pass;
  AuraCraft.showToast(`Autofilled credentials for: ${role.toUpperCase()}`, 'info');
}
</script>

<?php require_once ROOT_PATH . '/app/views/layouts/footer.php'; ?>
