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

        <div class="form-group">
          <label class="form-label">Default Home Street Address</label>
          <input type="text" name="address" class="form-control" placeholder="e.g. Flat 302, Green Acres, Bandra West">
        </div>

        <div class="form-row">
          <div class="form-group">
            <label class="form-label">City</label>
            <input type="text" name="city" class="form-control" value="Mumbai">
          </div>
          <div class="form-group">
            <label class="form-label">Postal Code</label>
            <input type="text" name="postal_code" class="form-control" placeholder="400050">
          </div>
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

<?php require_once ROOT_PATH . '/app/views/layouts/footer.php'; ?>
