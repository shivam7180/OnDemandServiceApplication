<?php require_once ROOT_PATH . '/app/views/layouts/header.php'; ?>

<section style="padding: 80px 0 100px; min-height: 80vh; display: flex; align-items: center; background: radial-gradient(circle at 50% 25%, #EEF4ED 0%, transparent 70%);">
  <div class="container" style="max-width: 800px;">
    
    <div style="text-align: center; margin-bottom: 40px;" class="reveal-on-scroll">
      <div style="width: 58px; height: 58px; border-radius: 16px; background: #FAF8F3; margin: 0 auto 16px; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 14px rgba(35,76,61,0.12); border: 1px solid var(--border-light);">
        <img src="<?= BASE_URL ?>/public/images/nearzo-logo.svg" alt="NEARZO" width="44" height="44" style="display: block;">
      </div>
      <h1 style="font-size: 2.2rem; margin-bottom: 10px;">Join NEARZO</h1>
      <p style="font-size: 1.05rem; color: var(--charcoal-muted); max-width: 540px; margin: 0 auto;">
        Choose how you would like to continue. Book verified specialists or grow your service practice.
      </p>
    </div>

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px;" class="reveal-on-scroll reveal-delay-1">
      
      <!-- Option 1: Customer Account -->
      <div class="card" style="padding: 36px 30px; border-radius: 24px; border: 2px solid transparent; transition: all 0.3s ease; display: flex; flex-direction: column; justify-content: space-between;" onmouseover="this.style.borderColor='var(--forest-green)'; this.style.transform='translateY(-6px)'" onmouseout="this.style.borderColor='transparent'; this.style.transform='none'">
        <div>
          <div style="width: 60px; height: 60px; border-radius: 16px; background: var(--soft-sage); display: flex; align-items: center; justify-content: center; font-size: 2rem; margin-bottom: 20px;">
            👤
          </div>
          <span class="badge badge-sage" style="margin-bottom: 8px;">For Clients & Homeowners</span>
          <h3 style="font-size: 1.5rem; margin-bottom: 10px; color: var(--forest-green);">Continue as Customer</h3>
          <p style="font-size: 0.92rem; color: var(--charcoal-light); line-height: 1.6; margin-bottom: 24px;">
            Discover vetted craftspeople, compare real prices in INR, track live GPS arrivals on the map, and enjoy bonded escrow protection.
          </p>
          <ul style="font-size: 0.88rem; color: var(--charcoal-muted); margin-bottom: 28px; list-style: none; padding: 0; display: flex; flex-direction: column; gap: 8px;">
            <li>✓ Browse verified plumbers, electricians, cleaners</li>
            <li>✓ Live GPS satellite order tracking</li>
            <li>✓ Direct calling and chat with specialists</li>
          </ul>
        </div>
        <a href="<?= BASE_URL ?>/auth/registerCustomer" class="btn btn-primary btn-lg" style="width: 100%; text-align: center;">
          Create Customer Account &rarr;
        </a>
      </div>

      <!-- Option 2: Provider Account -->
      <div class="card" style="padding: 36px 30px; border-radius: 24px; border: 2px solid transparent; transition: all 0.3s ease; display: flex; flex-direction: column; justify-content: space-between;" onmouseover="this.style.borderColor='var(--terracotta)'; this.style.transform='translateY(-6px)'" onmouseout="this.style.borderColor='transparent'; this.style.transform='none'">
        <div>
          <div style="width: 60px; height: 60px; border-radius: 16px; background: #FDE8E1; display: flex; align-items: center; justify-content: center; font-size: 2rem; margin-bottom: 20px;">
            🛠️
          </div>
          <span class="badge badge-terracotta" style="margin-bottom: 8px;">For Specialists & Craftsmen</span>
          <h3 style="font-size: 1.5rem; margin-bottom: 10px; color: var(--terracotta);">Register as Service Provider</h3>
          <p style="font-size: 0.92rem; color: var(--charcoal-light); line-height: 1.6; margin-bottom: 24px;">
            Grow your trade business, define your hourly or visit rate in INR, set your 15 km service radius, and receive instant local requests.
          </p>
          <ul style="font-size: 0.88rem; color: var(--charcoal-muted); margin-bottom: 28px; list-style: none; padding: 0; display: flex; flex-direction: column; gap: 8px;">
            <li>✓ Custom rates (per visit, per hour, or fixed)</li>
            <li>✓ Live availability toggle (Available / Busy)</li>
            <li>✓ Direct customer dispatch & verified earnings</li>
          </ul>
        </div>
        <a href="<?= BASE_URL ?>/auth/registerProvider" class="btn btn-secondary btn-lg" style="width: 100%; text-align: center;">
          Register as Partner &rarr;
        </a>
      </div>

    </div>

    <div style="text-align: center; margin-top: 36px; font-size: 0.95rem; color: var(--charcoal-light);">
      Already have an account? <a href="<?= BASE_URL ?>/auth/login" style="color: var(--forest-green); font-weight: 700;">Sign in to your account</a>
    </div>

  </div>
</section>

<?php require_once ROOT_PATH . '/app/views/layouts/footer.php'; ?>
