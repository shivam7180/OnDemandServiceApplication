<?php require_once ROOT_PATH . '/app/views/layouts/header.php'; ?>

<!-- Admin Header -->
<section style="padding: 40px 0 24px; background: linear-gradient(135deg, #FAF8F3 0%, #EEF4ED 100%); border-bottom: 1px solid var(--border-light);">
  <div class="container">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 20px;">
      <div>
        <span class="badge badge-sage" style="margin-bottom: 8px;">Super Admin Portal</span>
        <h1 style="font-size: 2rem; margin: 0;">Platform Operations & Governance</h1>
        <p style="font-size: 0.95rem; margin-top: 4px; color: var(--charcoal-muted);">
          System Status: <strong style="color: var(--status-success);">● All Systems Operational</strong> • Currency: INR (₹)
        </p>
      </div>

      <div style="display: flex; gap: 12px;">
        <button type="button" class="btn btn-outline" onclick="AuraCraft.showToast('Platform cache purged and re-indexed', 'info')">
          Purge Cache
        </button>
      </div>
    </div>
  </div>
</section>

<!-- Admin Body -->
<section class="section-spacer" style="padding-top: 40px;">
  <div class="container">
    
    <!-- Platform KPI Counters -->
    <div class="grid-4" style="margin-bottom: 36px;">
      <div class="card reveal-on-scroll">
        <div style="font-size: 0.82rem; color: var(--charcoal-muted); text-transform: uppercase; font-weight: 600;">Gross Platform Volume</div>
        <div class="counter-value" style="font-size: 2.2rem; margin-top: 6px;" data-counter="1450000" data-prefix="₹">0</div>
        <div style="font-size: 0.8rem; color: var(--status-success); margin-top: 4px;">+24.6% this quarter</div>
      </div>

      <div class="card reveal-on-scroll reveal-delay-1">
        <div style="font-size: 0.82rem; color: var(--charcoal-muted); text-transform: uppercase; font-weight: 600;">Active Bookings</div>
        <div class="counter-value" style="font-size: 2.2rem; margin-top: 6px;" data-counter="24">0</div>
        <div style="font-size: 0.8rem; color: var(--forest-green); margin-top: 4px;">In progress right now</div>
      </div>

      <div class="card reveal-on-scroll reveal-delay-2">
        <div style="font-size: 0.82rem; color: var(--charcoal-muted); text-transform: uppercase; font-weight: 600;">Verified Providers</div>
        <div class="counter-value" style="font-size: 2.2rem; margin-top: 6px;" data-counter="142">0</div>
        <div style="font-size: 0.8rem; color: var(--charcoal-muted); margin-top: 4px;">Across 6 categories</div>
      </div>

      <div class="card reveal-on-scroll reveal-delay-3">
        <div style="font-size: 0.82rem; color: var(--charcoal-muted); text-transform: uppercase; font-weight: 600;">Pending Approvals</div>
        <div class="counter-value" style="font-size: 2.2rem; margin-top: 6px; color: var(--terracotta);" data-counter="2">0</div>
        <div style="font-size: 0.8rem; color: var(--terracotta); margin-top: 4px;">Requires validation</div>
      </div>
    </div>

    <!-- Provider Applications Review Queue -->
    <div class="card reveal-on-scroll" style="margin-bottom: 40px;">
      <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h3 style="margin: 0;">Partner Approval Queue (Trade & Background Audits)</h3>
        <span class="badge badge-warning">2 Awaiting Action</span>
      </div>

      <div style="overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse; text-align: left;">
          <thead>
            <tr style="border-bottom: 2px solid var(--border-light); font-size: 0.85rem; color: var(--charcoal-muted); text-transform: uppercase;">
              <th style="padding: 12px 16px;">Applicant & Trade</th>
              <th style="padding: 12px 16px;">Discipline</th>
              <th style="padding: 12px 16px;">Experience</th>
              <th style="padding: 12px 16px;">Hourly Rate</th>
              <th style="padding: 12px 16px;">Applied</th>
              <th style="padding: 12px 16px; text-align: right;">Review Action</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($pendingProviders as $applicant): ?>
              <tr id="app-row-<?= $applicant['id'] ?>" style="border-bottom: 1px solid var(--border-light); font-size: 0.92rem;">
                <td style="padding: 16px;">
                  <strong style="color: var(--forest-green); font-size: 1rem;"><?= htmlspecialchars($applicant['name']) ?></strong>
                  <div style="font-size: 0.84rem; color: var(--charcoal-muted);"><?= htmlspecialchars($applicant['business']) ?> • <?= htmlspecialchars($applicant['city']) ?></div>
                </td>
                <td style="padding: 16px; font-weight: 600;"><?= htmlspecialchars($applicant['category']) ?></td>
                <td style="padding: 16px;"><?= $applicant['experience'] ?> years</td>
                <td style="padding: 16px; font-weight: 700;">₹<?= number_format($applicant['rate'], 0) ?>/hr</td>
                <td style="padding: 16px; color: var(--charcoal-muted);"><?= $applicant['applied_date'] ?></td>
                <td style="padding: 16px; text-align: right;">
                  <div style="display: flex; gap: 8px; justify-content: flex-end;">
                    <button type="button" class="btn btn-primary btn-sm" onclick="approveProvider(<?= $applicant['id'] ?>, '<?= htmlspecialchars($applicant['name']) ?>')">
                      ✓ Approve
                    </button>
                    <button type="button" class="btn btn-outline btn-sm" style="border-color: #D32F2F; color: #D32F2F;" onclick="rejectProvider(<?= $applicant['id'] ?>)">
                      Reject
                    </button>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Platform Disputes & Support Tickets -->
    <div class="card reveal-on-scroll">
      <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h3 style="margin: 0;">Disputes & Guarantee Tickets</h3>
        <span class="badge badge-sage">Support SLA: < 15m</span>
      </div>

      <div style="overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse; text-align: left;">
          <thead>
            <tr style="border-bottom: 2px solid var(--border-light); font-size: 0.85rem; color: var(--charcoal-muted); text-transform: uppercase;">
              <th style="padding: 12px 16px;">Booking Code</th>
              <th style="padding: 12px 16px;">Client</th>
              <th style="padding: 12px 16px;">Provider</th>
              <th style="padding: 12px 16px;">Subject</th>
              <th style="padding: 12px 16px;">Status</th>
              <th style="padding: 12px 16px; text-align: right;">Action</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($recentDisputes as $ticket): ?>
              <tr id="ticket-row-<?= $ticket['id'] ?>" style="border-bottom: 1px solid var(--border-light); font-size: 0.92rem;">
                <td style="padding: 16px; font-weight: 700; color: var(--forest-green);"><?= $ticket['code'] ?></td>
                <td style="padding: 16px; font-weight: 600;"><?= htmlspecialchars($ticket['customer']) ?></td>
                <td style="padding: 16px;"><?= htmlspecialchars($ticket['provider']) ?></td>
                <td style="padding: 16px;"><?= htmlspecialchars($ticket['subject']) ?></td>
                <td style="padding: 16px;">
                  <span class="badge badge-warning" id="ticket-badge-<?= $ticket['id'] ?>"><?= ucfirst($ticket['status']) ?></span>
                </td>
                <td style="padding: 16px; text-align: right;">
                  <button type="button" class="btn btn-sage btn-sm" onclick="resolveTicket(<?= $ticket['id'] ?>)">
                    Mark Resolved
                  </button>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>

  </div>
</section>

<script>
function approveProvider(id, name) {
  const row = document.getElementById('app-row-' + id);
  if (row) {
    row.style.background = '#E8F5E9';
    setTimeout(() => row.remove(), 400);
  }
  AuraCraft.showToast(`Partner profile for ${name} has been APPROVED and activated.`, 'success');
}

function rejectProvider(id) {
  const row = document.getElementById('app-row-' + id);
  if (row) {
    row.style.background = '#FFEBEE';
    setTimeout(() => row.remove(), 400);
  }
  AuraCraft.showToast('Application rejected.', 'info');
}

function resolveTicket(id) {
  const badge = document.getElementById('ticket-badge-' + id);
  if (badge) {
    badge.className = 'badge badge-success';
    badge.textContent = 'Resolved';
  }
  AuraCraft.showToast('Ticket marked as resolved and customer notified.', 'success');
}
</script>

<?php require_once ROOT_PATH . '/app/views/layouts/footer.php'; ?>
