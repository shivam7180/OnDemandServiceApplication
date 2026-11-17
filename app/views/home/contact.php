<?php require_once ROOT_PATH . '/app/views/layouts/header.php'; ?>

<!-- Contact Hero -->
<section style="padding: 70px 0 40px; background: radial-gradient(circle at 90% 10%, #F7EAE5 0%, transparent 50%);">
  <div class="container">
    <div style="max-width: 680px; margin: 0 auto; text-align: center;" class="reveal-on-scroll">
      <span class="badge badge-terracotta" style="margin-bottom: 14px;">✦ Concierge Support</span>
      <h1 style="margin-bottom: 16px;">We are always at your service.</h1>
      <p style="font-size: 1.1rem; color: var(--charcoal-light);">
        Have an inquiry about an appointment, enterprise partner inquiries, or custom home remodeling? Reach out to our dedicated client desk.
      </p>
    </div>
  </div>
</section>

<!-- Contact Form & Info Grid -->
<section class="section-spacer">
  <div class="container">
    <div style="display: grid; grid-template-columns: 1fr 1.2fr; gap: 48px; align-items: flex-start;">
      
      <!-- Contact Info Cards -->
      <div style="display: flex; flex-direction: column; gap: 24px;" class="reveal-on-scroll">
        <div class="card">
          <div style="font-size: 1.8rem; margin-bottom: 12px; color: var(--forest-green);">📞</div>
          <h4>Client Concierge Desk</h4>
          <p style="font-size: 0.95rem; margin: 8px 0 12px;">Immediate assistance for ongoing appointments and active bookings.</p>
          <a href="tel:+18005550199" style="font-weight: 700; color: var(--forest-green); font-size: 1.05rem;">+1 (800) 555-0199</a>
          <div style="font-size: 0.8rem; color: var(--charcoal-muted); margin-top: 4px;">Mon - Sun: 7:00 AM - 10:00 PM PST</div>
        </div>

        <div class="card">
          <div style="font-size: 1.8rem; margin-bottom: 12px; color: var(--terracotta);">✉️</div>
          <h4>Written Inquiries & Partnerships</h4>
          <p style="font-size: 0.95rem; margin: 8px 0 12px;">General inquiries, press contacts, or provider application assistance.</p>
          <a href="mailto:concierge@nearzo.com" style="font-weight: 700; color: var(--terracotta); font-size: 1.05rem;">concierge@nearzo.com</a>
        </div>

        <div class="card">
          <div style="font-size: 1.8rem; margin-bottom: 12px; color: var(--forest-green);">📍</div>
          <h4>Headquarters</h4>
          <p style="font-size: 0.95rem; margin: 8px 0 0;">
            NEARZO Technologies Inc.<br>
            Bandra Kurla Complex, Bandra East<br>
            Mumbai, Maharashtra 400051
          </p>
        </div>
      </div>

      <!-- Contact Message Form -->
      <div class="card reveal-on-scroll reveal-delay-1" style="padding: 40px;">
        <h3 style="margin-bottom: 8px;">Send a Direct Message</h3>
        <p style="font-size: 0.95rem; margin-bottom: 28px;">Our concierge desk usually replies within 20 minutes during working hours.</p>

        <form onsubmit="event.preventDefault(); AuraCraft.showToast('Thank you! Your message has been received by our concierge desk.', 'success'); this.reset();">
          <div class="form-row">
            <div class="form-group">
              <label class="form-label">Your Name</label>
              <input type="text" required class="form-control" placeholder="e.g. Eleanor Vance">
            </div>
            <div class="form-group">
              <label class="form-label">Email Address</label>
              <input type="email" required class="form-control" placeholder="eleanor@example.com">
            </div>
          </div>

          <div class="form-row">
            <div class="form-group">
              <label class="form-label">Phone Number (Optional)</label>
              <input type="tel" class="form-control" placeholder="+1 (555) 000-0000">
            </div>
            <div class="form-group">
              <label class="form-label">Subject</label>
              <select class="form-control" required>
                <option value="appointment">Appointment Assistance</option>
                <option value="provider">Provider Inquiry</option>
                <option value="feedback">Service Quality / Feedback</option>
                <option value="other">General Question</option>
              </select>
            </div>
          </div>

          <div class="form-group">
            <label class="form-label">Message Details</label>
            <textarea class="form-control" rows="5" required placeholder="Describe your request, appointment code, or questions..."></textarea>
          </div>

          <button type="submit" class="btn btn-primary btn-lg" style="width: 100%;">
            Send Message to Concierge
          </button>
        </form>
      </div>

    </div>
  </div>
</section>

<?php require_once ROOT_PATH . '/app/views/layouts/footer.php'; ?>
