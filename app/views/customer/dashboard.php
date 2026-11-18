<?php require_once ROOT_PATH . '/app/views/layouts/header.php'; ?>

<!-- Dashboard Banner -->
<section style="padding: 40px 0 24px; background: linear-gradient(135deg, #FAF8F3 0%, #EEF4ED 100%); border-bottom: 1px solid var(--border-light);">
  <div class="container">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 20px;">
      <div>
        <span class="badge badge-sage" style="margin-bottom: 8px;">Client Portal</span>
        <h1 style="font-size: 2rem; margin: 0;">Good afternoon, <?= htmlspecialchars(explode(' ', $customer['name'] ?? 'Friend')[0]) ?></h1>
        <p style="font-size: 0.95rem; margin-top: 4px; color: var(--charcoal-muted);">
          📍 <?= htmlspecialchars($customer['address'] ?? 'Nearby Location') ?>
        </p>
      </div>

      <div style="display: flex; gap: 12px;">
        <a href="<?= BASE_URL ?>/services" class="btn btn-primary">
          + Book New Service
        </a>
      </div>
    </div>
  </div>
</section>

<!-- Dashboard Main Content -->
<section class="section-spacer" style="padding-top: 40px;">
  <div class="container">
    
    <?php
      $activeCount = $activeBooking ? 1 : 0;
      $completedCount = count($pastBookings ?? []);
      $totalInvested = ($activeBooking ? ($activeBooking['amount'] ?? 0) : 0) + array_sum(array_column($pastBookings ?? [], 'amount'));
      $preferredCount = ($completedCount > 0) ? 1 : 0;
    ?>

    <!-- KPI Counters Grid -->
    <div class="grid-4" style="margin-bottom: 36px;">
      <div class="card reveal-on-scroll">
        <div style="font-size: 0.82rem; color: var(--charcoal-muted); text-transform: uppercase; font-weight: 600;">Active Bookings</div>
        <div class="counter-value" style="font-size: 2.2rem; margin-top: 6px;" data-counter="<?= $activeCount ?>"><?= $activeCount ?></div>
        <div style="font-size: 0.8rem; color: <?= $activeCount > 0 ? 'var(--status-success)' : 'var(--charcoal-muted)' ?>; margin-top: 4px;">
          <?= $activeCount > 0 ? '● 1 In Progress' : 'No active orders' ?>
        </div>
      </div>

      <div class="card reveal-on-scroll reveal-delay-1">
        <div style="font-size: 0.82rem; color: var(--charcoal-muted); text-transform: uppercase; font-weight: 600;">Completed Jobs</div>
        <div class="counter-value" style="font-size: 2.2rem; margin-top: 6px;" data-counter="<?= $completedCount ?>"><?= $completedCount ?></div>
        <div style="font-size: 0.8rem; color: var(--charcoal-muted); margin-top: 4px;">
          <?= $completedCount > 0 ? '100% Satisfied' : 'Fresh Account' ?>
        </div>
      </div>

      <div class="card reveal-on-scroll reveal-delay-2">
        <div style="font-size: 0.82rem; color: var(--charcoal-muted); text-transform: uppercase; font-weight: 600;">Total Invested</div>
        <div class="counter-value" style="font-size: 2.2rem; margin-top: 6px;" data-counter="<?= (int)$totalInvested ?>" data-prefix="₹"><?= (int)$totalInvested ?></div>
        <div style="font-size: 0.8rem; color: var(--forest-green); margin-top: 4px;">Bonded & Insured</div>
      </div>

      <div class="card reveal-on-scroll reveal-delay-3">
        <div style="font-size: 0.82rem; color: var(--charcoal-muted); text-transform: uppercase; font-weight: 600;">Preferred Pros</div>
        <div class="counter-value" style="font-size: 2.2rem; margin-top: 6px;" data-counter="<?= $preferredCount ?>"><?= $preferredCount ?></div>
        <div style="font-size: 0.8rem; color: var(--terracotta); margin-top: 4px;">★ Direct Rebook Ready</div>
      </div>
    </div>

    <!-- Active Booking Callout -->
    <?php if (!empty($activeBooking)): ?>
      <div class="card reveal-on-scroll" style="margin-bottom: 40px; background: #FFFFFF; border-left: 6px solid var(--terracotta); box-shadow: var(--shadow-md);">
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 20px;">
          <div>
            <div style="display: flex; gap: 10px; align-items: center; margin-bottom: 8px;">
              <span class="badge badge-warning">Appointment <?= ucfirst($activeBooking['status']) ?></span>
              <span style="font-weight: 700; color: var(--charcoal-muted); font-size: 0.88rem;">Code: <?= $activeBooking['code'] ?></span>
            </div>
            <h3 style="font-size: 1.35rem; margin-bottom: 4px;"><?= htmlspecialchars($activeBooking['service']) ?></h3>
            <div style="font-size: 0.95rem; color: var(--charcoal-light);">
              Specialist: <strong><?= htmlspecialchars($activeBooking['provider']) ?></strong> • 
              Scheduled for <strong><?= $activeBooking['date'] ?> at <?= $activeBooking['time'] ?></strong>
            </div>
          </div>

          <div style="display: flex; gap: 12px; align-items: center;">
            <div style="text-align: right; margin-right: 12px;">
              <div style="font-size: 0.8rem; color: var(--charcoal-muted);">Total Amount</div>
              <div style="font-size: 1.35rem; font-weight: 700; color: var(--forest-green);">₹<?= number_format($activeBooking['amount'], 0) ?></div>
            </div>
            <a href="<?= BASE_URL ?>/booking/track" class="btn btn-secondary">
              Track Live Arrival →
            </a>
          </div>
        </div>
      </div>
    <?php else: ?>
      <div class="card reveal-on-scroll" style="margin-bottom: 40px; background: #FFFFFF; border: 2px dashed var(--border-light); text-align: center; padding: 44px 24px; border-radius: 20px;">
        <div style="font-size: 2.8rem; margin-bottom: 10px;">🏡</div>
        <h3 style="font-size: 1.3rem; margin-bottom: 6px; color: var(--forest-green);">No Active Service Bookings</h3>
        <p style="font-size: 0.95rem; color: var(--charcoal-muted); max-width: 460px; margin: 0 auto 18px;">
          You haven't booked any nearby services yet. Browse our verified plumbers, electricians, cleaners, and appliance technicians within 15 km.
        </p>
        <a href="<?= BASE_URL ?>/services" class="btn btn-primary">
          Explore Services Nearby →
        </a>
      </div>
    <?php endif; ?>

    <!-- Booking History Table -->
    <div class="card reveal-on-scroll">
      <h3 style="margin-bottom: 20px;">Appointment History</h3>

      <?php if (!empty($pastBookings)): ?>
        <div style="overflow-x: auto;">
          <table style="width: 100%; border-collapse: collapse; text-align: left;">
            <thead>
              <tr style="border-bottom: 2px solid var(--border-light); font-size: 0.85rem; color: var(--charcoal-muted); text-transform: uppercase;">
                <th style="padding: 12px 16px;">Booking Code</th>
                <th style="padding: 12px 16px;">Service</th>
                <th style="padding: 12px 16px;">Specialist</th>
                <th style="padding: 12px 16px;">Date</th>
                <th style="padding: 12px 16px;">Amount</th>
                <th style="padding: 12px 16px;">Status</th>
                <th style="padding: 12px 16px; text-align: right;">Action</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($pastBookings as $b): ?>
                <tr style="border-bottom: 1px solid var(--border-light); font-size: 0.92rem;">
                  <td style="padding: 16px; font-weight: 700; color: var(--forest-green);"><?= $b['code'] ?></td>
                  <td style="padding: 16px; font-weight: 600;"><?= htmlspecialchars($b['service']) ?></td>
                  <td style="padding: 16px;"><?= htmlspecialchars($b['provider']) ?></td>
                  <td style="padding: 16px; color: var(--charcoal-muted);"><?= $b['date'] ?></td>
                  <td style="padding: 16px; font-weight: 700;">₹<?= number_format($b['amount'], 0) ?></td>
                  <td style="padding: 16px;">
                    <span class="badge badge-success"><?= ucfirst($b['status']) ?></span>
                  </td>
                  <td style="padding: 16px; text-align: right;">
                    <button type="button" class="btn btn-outline btn-sm" onclick="Nearzo.showToast('Re-booking service request initialized', 'info')">
                      Rebook
                    </button>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php else: ?>
        <div style="text-align: center; padding: 36px 20px; color: var(--charcoal-muted);">
          <div style="font-size: 2.2rem; margin-bottom: 8px;">📋</div>
          <p style="font-size: 1rem; font-weight: 600; margin-bottom: 4px; color: var(--charcoal);">No appointment history yet</p>
          <p style="margin: 0; font-size: 0.9rem;">Once you complete a service, your verified receipts, specialist details, and 1-click rebook options will appear here.</p>
        </div>
      <?php endif; ?>
    </div>

  </div>
</section>

<?php require_once ROOT_PATH . '/app/views/layouts/footer.php'; ?>
