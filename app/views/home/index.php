<?php require_once ROOT_PATH . '/app/views/layouts/header.php'; ?>

<!-- Hero Section with Animated Floating Badges -->
<section style="position: relative; overflow: hidden; padding: 70px 0 90px; background: radial-gradient(circle at 80% 20%, #EEF4ED 0%, transparent 60%);">
  <div class="container">
    <div style="display: grid; grid-template-columns: 1.2fr 0.8fr; gap: 48px; align-items: center;">
      
      <!-- Left Column: Copy & Search -->
      <div>
        <div class="reveal-on-scroll">
          <span class="badge badge-sage" style="margin-bottom: 20px;">
            ✦ Reimagined Home Care & Living
          </span>
          <h1 style="margin-bottom: 24px; line-height: 1.15;">
            Master craftsmanship, <br>
            <span style="color: var(--terracotta);">on-demand</span> at your door.
          </h1>
          <p style="font-size: 1.15rem; max-width: 540px; margin-bottom: 36px; color: var(--charcoal-light);">
            Book certified plumbers, eco-luxury housekeepers, certified electricians, and master artisans with transparent pricing in Rupees and real-time GPS appointment tracking.
          </p>
        </div>

        <!-- Floating Search Banner -->
        <div class="reveal-on-scroll reveal-delay-1">
          <form action="<?= BASE_URL ?>/services" method="GET" class="search-banner">
            <div class="search-field">
              <span style="font-size: 1.2rem; color: var(--forest-green);">🔍</span>
              <input type="text" name="q" placeholder="Which service do you need? (e.g. Deep cleaning, leak repair)">
            </div>
            <div class="search-divider"></div>
            <div class="search-field" style="max-width: 180px;">
              <span style="font-size: 1.2rem; color: var(--forest-green);">📍</span>
              <input type="text" name="location" placeholder="City" value="Mumbai">
            </div>
            <button type="submit" class="btn btn-secondary">
              Find Services
            </button>
          </form>
          
          <div style="display: flex; gap: 10px; align-items: center; margin-top: 18px; font-size: 0.88rem; color: var(--charcoal-muted);">
            <span>Popular:</span>
            <a href="<?= BASE_URL ?>/services?q=Deep+Clean" class="badge badge-outline" style="text-transform: none;">Deep Clean (₹1,499)</a>
            <a href="<?= BASE_URL ?>/services?q=Plumbing" class="badge badge-outline" style="text-transform: none;">Emergency Plumbing (₹499)</a>
            <a href="<?= BASE_URL ?>/services?q=Electrical" class="badge badge-outline" style="text-transform: none;">Smart Switch Setup (₹899)</a>
          </div>
        </div>
      </div>

      <!-- Right Column: Visual Stage with Floating Animated Elements -->
      <div style="position: relative;" class="reveal-on-scroll reveal-delay-2">
        <div class="card" style="padding: 16px; border-radius: 28px; background: #FFFFFF; box-shadow: var(--shadow-floating); position: relative; z-index: 2;">
          <div style="height: 380px; background: linear-gradient(135deg, #234C3D 0%, #19382D 100%); border-radius: 20px; padding: 32px; color: #FFFFFF; display: flex; flex-direction: column; justify-content: space-between; overflow: hidden; position: relative;">
            
            <div style="position: absolute; right: -40px; bottom: -40px; width: 220px; height: 220px; border-radius: 50%; border: 40px solid rgba(220, 232, 218, 0.1);"></div>
            <div style="position: absolute; right: 40px; top: -30px; width: 140px; height: 140px; border-radius: 50%; background: rgba(217, 121, 91, 0.15);"></div>

            <div style="position: relative; z-index: 2;">
              <span class="badge" style="background: rgba(255,255,255,0.15); color: #FFF; backdrop-filter: blur(8px);">Instant Booking</span>
              <h3 style="color: #FFFFFF; margin-top: 16px; font-size: 1.8rem;">Vetted Specialists in Minutes</h3>
              <p style="color: #DCE8DA; font-size: 0.95rem; margin-top: 8px;">Upfront guaranteed pricing in Rupees with zero hidden surcharges.</p>
            </div>

            <div style="background: rgba(255,255,255,0.1); backdrop-filter: blur(10px); border-radius: 14px; padding: 14px 18px; display: flex; align-items: center; justify-content: space-between; border: 1px solid rgba(255,255,255,0.15); z-index: 2;">
              <div style="display: flex; align-items: center; gap: 12px;">
                <div style="width: 12px; height: 12px; border-radius: 50%; background: #4ADE80; box-shadow: 0 0 10px #4ADE80;"></div>
                <div>
                  <div style="font-size: 0.85rem; font-weight: 600;">Active Service Pros</div>
                  <div style="font-size: 0.75rem; color: #DCE8DA;">Ready across your neighborhood</div>
                </div>
              </div>
              <span style="font-weight: 700; font-size: 1.1rem; color: #FFFFFF;">48 Online</span>
            </div>
          </div>
        </div>

        <!-- Floating Badge 1: Top Rated Pro -->
        <div class="card animate-float" style="position: absolute; top: -20px; left: -30px; z-index: 3; padding: 14px 20px; border-radius: var(--radius-md); box-shadow: var(--shadow-lg); border-color: var(--soft-sage); display: flex; align-items: center; gap: 12px;">
          <div style="width: 44px; height: 44px; border-radius: 50%; background: var(--terracotta-light); color: var(--terracotta); display: flex; align-items: center; justify-content: center; font-size: 1.2rem; font-weight: bold;">
            ★
          </div>
          <div>
            <div style="font-weight: 700; font-size: 0.95rem; color: var(--forest-green);">4.98 Rating</div>
            <div style="font-size: 0.78rem; color: var(--charcoal-muted);">1,200+ Verified Reviews</div>
          </div>
        </div>

        <!-- Floating Badge 2: Swift Arrival -->
        <div class="card animate-float-delayed" style="position: absolute; bottom: -25px; right: -20px; z-index: 3; padding: 14px 20px; border-radius: var(--radius-md); box-shadow: var(--shadow-lg); border-color: var(--soft-sage); display: flex; align-items: center; gap: 12px;">
          <div style="width: 44px; height: 44px; border-radius: 50%; background: var(--soft-sage); color: var(--forest-green); display: flex; align-items: center; justify-content: center; font-size: 1.2rem;">
            ⏱
          </div>
          <div>
            <div style="font-weight: 700; font-size: 0.95rem; color: var(--forest-green);">Under 30 Mins</div>
            <div style="font-size: 0.78rem; color: var(--charcoal-muted);">Average Arrival Time</div>
          </div>
        </div>

      </div>

    </div>
  </div>
</section>

<!-- Service Categories Section -->
<section class="section-spacer">
  <div class="container">
    <div style="text-align: center; max-width: 650px; margin: 0 auto 56px;" class="reveal-on-scroll">
      <span class="badge badge-terracotta" style="margin-bottom: 12px;">Curated Disciplines</span>
      <h2>Specialized Services for Modern Homes</h2>
      <p style="margin-top: 12px;">Every service provider undergoes background validation, trade credential checks, and craftsmanship reviews.</p>
    </div>

    <div class="grid-3">
      <!-- Category 1 -->
      <a href="<?= BASE_URL ?>/services?cat=home-cleaning" class="card card-hoverable tilt-card reveal-on-scroll" style="text-decoration: none;">
        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 20px;">
          <div style="width: 56px; height: 56px; border-radius: 16px; background: var(--soft-sage); color: var(--forest-green); display: flex; align-items: center; justify-content: center; font-size: 1.6rem;">
            ✨
          </div>
          <span class="badge badge-sage">Popular</span>
        </div>
        <h3 style="font-size: 1.35rem; margin-bottom: 8px;">Home Cleaning</h3>
        <p style="font-size: 0.95rem; margin-bottom: 20px;">Deep sanitization, botanical housekeeping, seasonal refreshing, and luxury upkeep.</p>
        <div style="display: flex; justify-content: space-between; align-items: center; font-weight: 600; font-size: 0.9rem; color: var(--forest-green);">
          <span>From ₹499/hr</span>
          <span style="color: var(--terracotta);">Explore →</span>
        </div>
      </a>

      <!-- Category 2 -->
      <a href="<?= BASE_URL ?>/services?cat=plumbing" class="card card-hoverable tilt-card reveal-on-scroll reveal-delay-1" style="text-decoration: none;">
        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 20px;">
          <div style="width: 56px; height: 56px; border-radius: 16px; background: var(--terracotta-light); color: var(--terracotta); display: flex; align-items: center; justify-content: center; font-size: 1.6rem;">
            🔧
          </div>
          <span class="badge badge-terracotta">Emergency Ready</span>
        </div>
        <h3 style="font-size: 1.35rem; margin-bottom: 8px;">Master Plumbing</h3>
        <p style="font-size: 0.95rem; margin-bottom: 20px;">High-pressure leak repair, pipe renewal, architectural fixtures, and water heaters.</p>
        <div style="display: flex; justify-content: space-between; align-items: center; font-weight: 600; font-size: 0.9rem; color: var(--forest-green);">
          <span>From ₹599/hr</span>
          <span style="color: var(--terracotta);">Explore →</span>
        </div>
      </a>

      <!-- Category 3 -->
      <a href="<?= BASE_URL ?>/services?cat=electrical" class="card card-hoverable tilt-card reveal-on-scroll reveal-delay-2" style="text-decoration: none;">
        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 20px;">
          <div style="width: 56px; height: 56px; border-radius: 16px; background: #FEF3C7; color: #D97706; display: flex; align-items: center; justify-content: center; font-size: 1.6rem;">
            ⚡
          </div>
          <span class="badge badge-outline">Certified</span>
        </div>
        <h3 style="font-size: 1.35rem; margin-bottom: 8px;">Electrical & Smart Home</h3>
        <p style="font-size: 0.95rem; margin-bottom: 20px;">Smart lighting setup, EV chargers, inverter/breaker upgrades, and safety audits.</p>
        <div style="display: flex; justify-content: space-between; align-items: center; font-weight: 600; font-size: 0.9rem; color: var(--forest-green);">
          <span>From ₹699/hr</span>
          <span style="color: var(--terracotta);">Explore →</span>
        </div>
      </a>

      <!-- Category 4 -->
      <a href="<?= BASE_URL ?>/services?cat=painting" class="card card-hoverable tilt-card reveal-on-scroll" style="text-decoration: none;">
        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 20px;">
          <div style="width: 56px; height: 56px; border-radius: 16px; background: #FCE7F3; color: #BE185D; display: flex; align-items: center; justify-content: center; font-size: 1.6rem;">
            🎨
          </div>
          <span class="badge badge-outline">Artisanal</span>
        </div>
        <h3 style="font-size: 1.35rem; margin-bottom: 8px;">Painting & Finishes</h3>
        <p style="font-size: 0.95rem; margin-bottom: 20px;">Accent walls, zero-VOC formulations, trim detailing, and texture coating.</p>
        <div style="display: flex; justify-content: space-between; align-items: center; font-weight: 600; font-size: 0.9rem; color: var(--forest-green);">
          <span>From ₹899/hr</span>
          <span style="color: var(--terracotta);">Explore →</span>
        </div>
      </a>

      <!-- Category 5 -->
      <a href="<?= BASE_URL ?>/services?cat=appliance-care" class="card card-hoverable tilt-card reveal-on-scroll reveal-delay-1" style="text-decoration: none;">
        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 20px;">
          <div style="width: 56px; height: 56px; border-radius: 16px; background: #E0E7FF; color: #4338CA; display: flex; align-items: center; justify-content: center; font-size: 1.6rem;">
            🛡️
          </div>
          <span class="badge badge-outline">Guaranteed</span>
        </div>
        <h3 style="font-size: 1.35rem; margin-bottom: 8px;">Appliance Care & Repair</h3>
        <p style="font-size: 0.95rem; margin-bottom: 20px;">Refrigeration diagnosis, washing machines, microwaves, and AC maintenance.</p>
        <div style="display: flex; justify-content: space-between; align-items: center; font-weight: 600; font-size: 0.9rem; color: var(--forest-green);">
          <span>From ₹549/hr</span>
          <span style="color: var(--terracotta);">Explore →</span>
        </div>
      </a>

      <!-- Category 6 -->
      <a href="<?= BASE_URL ?>/services?cat=smart-home" class="card card-hoverable tilt-card reveal-on-scroll reveal-delay-2" style="text-decoration: none;">
        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 20px;">
          <div style="width: 56px; height: 56px; border-radius: 16px; background: var(--soft-sage); color: var(--forest-green); display: flex; align-items: center; justify-content: center; font-size: 1.6rem;">
            🔒
          </div>
          <span class="badge badge-sage">Trending</span>
        </div>
        <h3 style="font-size: 1.35rem; margin-bottom: 8px;">Security & CCTV</h3>
        <p style="font-size: 0.95rem; margin-bottom: 20px;">Smart digital door locks, HD video doorbells, mesh WiFi, and sensor alarms.</p>
        <div style="display: flex; justify-content: space-between; align-items: center; font-weight: 600; font-size: 0.9rem; color: var(--forest-green);">
          <span>From ₹799/hr</span>
          <span style="color: var(--terracotta);">Explore →</span>
        </div>
      </a>
    </div>
  </div>
</section>

<!-- How It Works Section -->
<section style="background: var(--bg-muted); padding: 90px 0;">
  <div class="container">
    <div style="text-align: center; max-width: 600px; margin: 0 auto 60px;" class="reveal-on-scroll">
      <span class="badge badge-sage" style="margin-bottom: 12px;">Seamless Experience</span>
      <h2>How NEARZO Works</h2>
      <p style="margin-top: 10px;">Three effortless steps from selecting a discipline to an immaculate living space.</p>
    </div>

    <div class="grid-3">
      <!-- Step 1 -->
      <div class="card reveal-on-scroll" style="text-align: center; padding: 36px 24px;">
        <div style="width: 64px; height: 64px; border-radius: 50%; background: var(--forest-green); color: #FFF; font-size: 1.4rem; font-weight: 800; display: flex; align-items: center; justify-content: center; margin: 0 auto 24px;">
          1
        </div>
        <h4 style="margin-bottom: 12px;">Discover & Select</h4>
        <p style="font-size: 0.95rem;">Browse vetted service specialists in your city, inspect transparent Rupee pricing, and pick your preferred time slot.</p>
      </div>

      <!-- Step 2 -->
      <div class="card reveal-on-scroll reveal-delay-1" style="text-align: center; padding: 36px 24px;">
        <div style="width: 64px; height: 64px; border-radius: 50%; background: var(--terracotta); color: #FFF; font-size: 1.4rem; font-weight: 800; display: flex; align-items: center; justify-content: center; margin: 0 auto 24px;">
          2
        </div>
        <h4 style="margin-bottom: 12px;">Live GPS Tracking & Arrival</h4>
        <p style="font-size: 0.95rem;">Track your specialist's approach on live satellite maps with real-time ETA and status milestone updates.</p>
      </div>

      <!-- Step 3 -->
      <div class="card reveal-on-scroll reveal-delay-2" style="text-align: center; padding: 36px 24px;">
        <div style="width: 64px; height: 64px; border-radius: 50%; background: var(--soft-sage); color: var(--forest-green); font-size: 1.4rem; font-weight: 800; display: flex; align-items: center; justify-content: center; margin: 0 auto 24px;">
          3
        </div>
        <h4 style="margin-bottom: 12px;">Flawless Execution</h4>
        <p style="font-size: 0.95rem;">Pay securely upon total satisfaction, protected by our ₹25,000 NEARZO Craftsmanship Guarantee.</p>
      </div>
    </div>
  </div>
</section>

<!-- Animated Platform Metrics Counter Section -->
<section class="section-spacer">
  <div class="container">
    <div class="card" style="background: var(--forest-green); color: #FFFFFF; padding: 56px 40px; border-radius: 28px;">
      <div class="grid-4" style="text-align: center;">
        <div>
          <div class="counter-value" style="color: #FFFFFF; font-size: 3rem;" data-counter="14500" data-suffix="+">0</div>
          <div style="color: var(--soft-sage); font-size: 0.95rem; margin-top: 6px; font-weight: 500;">Completed Appointments</div>
        </div>
        <div>
          <div class="counter-value" style="color: #FFFFFF; font-size: 3rem;" data-counter="99.4" data-suffix="%" data-decimals="1">0</div>
          <div style="color: var(--soft-sage); font-size: 0.95rem; margin-top: 6px; font-weight: 500;">Satisfaction Rating</div>
        </div>
        <div>
          <div class="counter-value" style="color: #FFFFFF; font-size: 3rem;" data-counter="480" data-suffix="+">0</div>
          <div style="color: var(--soft-sage); font-size: 0.95rem; margin-top: 6px; font-weight: 500;">Vetted Master Pros</div>
        </div>
        <div>
          <div class="counter-value" style="color: #FFFFFF; font-size: 3rem;" data-counter="25" data-suffix=" min">0</div>
          <div style="color: var(--soft-sage); font-size: 0.95rem; margin-top: 6px; font-weight: 500;">Average Arrival Time</div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Call to Action Section -->
<section style="padding: 40px 0 100px;">
  <div class="container">
    <div style="background: linear-gradient(135deg, #F7EAE5 0%, #FAF8F3 100%); border: 1.5px solid var(--soft-sage); border-radius: 28px; padding: 60px 48px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 32px;" class="reveal-on-scroll">
      <div style="max-width: 580px;">
        <span class="badge badge-terracotta" style="margin-bottom: 12px;">Partner Program</span>
        <h2>Are You a Master Craftsman or Home Care Specialist?</h2>
        <p style="margin-top: 12px; font-size: 1.05rem;">
          Expand your client base, set your own schedule, receive instant bank payouts, and enjoy premium support on the NEARZO network.
        </p>
      </div>
      <div style="display: flex; gap: 16px;">
        <a href="<?= BASE_URL ?>/auth/registerProvider" class="btn btn-secondary btn-lg">Apply as Provider</a>
        <a href="<?= BASE_URL ?>/services" class="btn btn-outline btn-lg">Browse Services</a>
      </div>
    </div>
  </div>
</section>

<?php require_once ROOT_PATH . '/app/views/layouts/footer.php'; ?>
