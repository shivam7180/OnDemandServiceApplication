  </main>

  <!-- Footer Section -->
  <footer class="footer">
    <div class="container">
      <div class="footer-top">
        <div class="footer-brand">
          <div class="brand-logo" style="color: #FFFFFF; margin-bottom: 12px;">
            <div class="brand-icon" style="background: var(--terracotta);">✦</div>
            <span>AuraCraft</span>
          </div>
          <p>Connecting conscientious homeowners with rigorously vetted artisans, certified technicians, and luxury home specialists.</p>
          <div style="display: flex; gap: 12px; margin-top: 16px;">
            <span class="badge badge-sage">✓ Verified Pros</span>
            <span class="badge badge-terracotta">★ 4.9/5 Rating</span>
          </div>
        </div>

        <div>
          <h4 class="footer-heading">Services</h4>
          <ul class="footer-links">
            <li><a href="<?= BASE_URL ?>/services?cat=home-cleaning">Botanical Housekeeping</a></li>
            <li><a href="<?= BASE_URL ?>/services?cat=plumbing">Master Plumbing</a></li>
            <li><a href="<?= BASE_URL ?>/services?cat=electrical">Smart Electrical</a></li>
            <li><a href="<?= BASE_URL ?>/services?cat=painting">Bespoke Wall Painting</a></li>
            <li><a href="<?= BASE_URL ?>/services">View All Services</a></li>
          </ul>
        </div>

        <div>
          <h4 class="footer-heading">Platform</h4>
          <ul class="footer-links">
            <li><a href="<?= BASE_URL ?>/home/about">Our Story</a></li>
            <li><a href="<?= BASE_URL ?>/providers">Explore Professionals</a></li>
            <li><a href="<?= BASE_URL ?>/booking/track">Track Live Appointment</a></li>
            <li><a href="<?= BASE_URL ?>/auth/registerProvider">Become a Service Partner</a></li>
            <li><a href="<?= BASE_URL ?>/home/contact">Help & Support</a></li>
          </ul>
        </div>

        <div>
          <h4 class="footer-heading">Join The Circle</h4>
          <p style="font-size: 0.92rem; color: #A3B3A9; margin-bottom: 16px;">
            Receive seasonal home maintenance schedules, curated architectural guides, and partner perks.
          </p>
          <form onsubmit="event.preventDefault(); AuraCraft.showToast('Thank you for subscribing to AuraCraft journal.', 'success');" style="display: flex; gap: 8px;">
            <input type="email" required placeholder="Enter your email" class="form-control" style="background: #253930; border-color: #354F43; color: #FFF; font-size: 0.9rem; padding: 10px 14px;">
            <button type="submit" class="btn btn-secondary btn-sm" style="padding: 10px 18px;">Join</button>
          </form>
        </div>
      </div>

      <div class="footer-bottom">
        <div>&copy; <?= date('Y') ?> AuraCraft Technologies Inc. All rights reserved. Crafted with care.</div>
        <div style="display: flex; gap: 24px;">
          <a href="#">Privacy Policy</a>
          <a href="#">Terms of Service</a>
          <a href="#">Security Guarantee</a>
        </div>
      </div>
    </div>
  </footer>

  <!-- Scripts -->
  <script src="<?= BASE_URL ?>/public/js/main.js"></script>
  <script src="<?= BASE_URL ?>/public/js/animations.js"></script>

  <!-- Flash notification handler if session flash exists -->
  <?php if (!empty($_SESSION['flash_success'])): ?>
    <script>
      document.addEventListener('DOMContentLoaded', () => {
        AuraCraft.showToast(<?= json_encode($_SESSION['flash_success']) ?>, 'success');
      });
    </script>
    <?php unset($_SESSION['flash_success']); ?>
  <?php endif; ?>

  <?php if (!empty($_SESSION['flash_error'])): ?>
    <script>
      document.addEventListener('DOMContentLoaded', () => {
        AuraCraft.showToast(<?= json_encode($_SESSION['flash_error']) ?>, 'error');
      });
    </script>
    <?php unset($_SESSION['flash_error']); ?>
  <?php endif; ?>

</body>
</html>
