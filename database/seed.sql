-- ==========================================================
-- Demo Data Seed for On-Demand Service Application (INR Currency)
-- Default password for all seeded users is: password
-- Bcrypt Hash: $2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi
-- ==========================================================

USE `ondemand_db`;

-- 1. SEED USERS (Admin, Providers, Customers)
INSERT INTO `users` (`id`, `full_name`, `email`, `phone`, `password_hash`, `role`, `status`) VALUES
(1, 'System Administrator', 'admin@servicehub.com', '+91 98200 00100', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'active'),
(2, 'Sophia Miller', 'sophia@servicehub.com', '+91 98200 00101', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'customer', 'active'),
(3, 'Marcus Vance', 'marcus@servicehub.com', '+91 98200 00102', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'customer', 'active'),
(4, 'David Sterling', 'david@sterlingcraft.com', '+91 98200 00201', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'provider', 'active'),
(5, 'Elena Rostova', 'elena@luxeclean.com', '+91 98200 00202', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'provider', 'active'),
(6, 'Julian Thorne', 'julian@thorneelectric.com', '+91 98200 00203', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'provider', 'active');

-- 2. SEED CUSTOMERS
INSERT INTO `customers` (`id`, `user_id`, `address`, `city`, `state`, `postal_code`, `latitude`, `longitude`) VALUES
(1, 2, 'Flat 402, Green Acres, Bandra West', 'Mumbai', 'MH', '400050', 19.0596, 72.8295),
(2, 3, 'Tower 3, Hiranandani Gardens, Powai', 'Mumbai', 'MH', '400076', 19.1197, 72.9051);

-- 3. SEED PROVIDERS (Hourly rates in INR: ₹599, ₹449, ₹699)
INSERT INTO `providers` (`id`, `user_id`, `business_name`, `bio`, `hourly_rate`, `experience_years`, `service_radius_km`, `address`, `city`, `state`, `postal_code`, `latitude`, `longitude`, `is_approved`, `is_available`, `rating_avg`, `reviews_count`) VALUES
(1, 4, 'Sterling Master Plumbing & Heating', 'Licensed master plumber with over 12 years of residential and commercial renovation expertise. Dedicated to meticulous craft and rapid response times across Mumbai.', 599.00, 12, 25, 'Hill Road, Bandra West', 'Mumbai', 'MH', '400050', 19.0544, 72.8322, 1, 1, 4.95, 48),
(2, 5, 'LuxeClean Botanical Home Care', 'Eco-friendly, chemical-free deep cleaning and housekeeping. Trusted by luxury apartments and busy families across Mumbai.', 449.00, 7, 20, 'Linking Road, Khar West', 'Mumbai', 'MH', '400052', 19.0700, 72.8339, 1, 1, 4.88, 36),
(3, 6, 'Thorne Certified Electrical Solutions', 'Certified electrical contractor specializing in smart home integration, panel upgrades, EV chargers, and safety inspections.', 699.00, 9, 30, 'Bandra Kurla Complex', 'Mumbai', 'MH', '400051', 19.0657, 72.8687, 1, 1, 4.92, 52);

-- 4. SEED SERVICE CATEGORIES
INSERT INTO `service_categories` (`id`, `name`, `slug`, `icon`, `description`, `badge`, `display_order`, `is_active`) VALUES
(1, 'Home Cleaning', 'home-cleaning', 'sparkles', 'Deep cleaning, sanitized surfaces, and sparkling living spaces.', 'Popular', 1, 1),
(2, 'Plumbing Services', 'plumbing', 'wrench', 'Pipe repairs, fixture installation, leak fixes, and emergency drainage.', 'Top Rated', 2, 1),
(3, 'Electrical & Lighting', 'electrical', 'zap', 'Rewiring, fixture setup, smart devices, and breaker replacements.', 'Essential', 3, 1),
(4, 'Appliance Care', 'appliance-care', 'tool', 'Repair and tune-ups for washers, dryers, refrigerators, and AC units.', 'Guaranteed', 4, 1),
(5, 'Painting & Wall Decor', 'painting', 'brush', 'Interior accents, wall texturing, trim touch-ups, and full-room paint.', 'Creative', 5, 1),
(6, 'Smart Home & Security', 'smart-home', 'shield', 'Smart locks, security camera setup, doorbell cameras, and home automation.', 'Trending', 6, 1);

-- 5. SEED SERVICES (Base prices in INR)
INSERT INTO `services` (`id`, `category_id`, `name`, `slug`, `description`, `base_price`, `duration_mins`, `image`, `is_active`) VALUES
(1, 1, 'Deep Residential Clean', 'deep-residential-clean', 'Comprehensive home cleaning covering kitchens, bathrooms, bedrooms, baseboards, and window sills.', 1499.00, 180, 'cleaning_deep.jpg', 1),
(2, 1, 'Eco Standard Maintenance Clean', 'standard-maintenance-clean', 'Routine bi-weekly or monthly sanitization using biodegradable botanic supplies.', 699.00, 90, 'cleaning_standard.jpg', 1),
(3, 2, 'Emergency Pipe Leak & Repair', 'leak-repair', 'Fast diagnosis and high-grade repair for leaking valves, pipes, and pressurized systems.', 499.00, 60, 'plumbing_leak.jpg', 1),
(4, 2, 'Faucet & Fixture Installation', 'fixture-installation', 'Professional replacement of sinks, luxury faucets, showerheads, and valves.', 599.00, 75, 'plumbing_fixture.jpg', 1),
(5, 3, 'Smart Lighting & Dimmer Setup', 'smart-lighting-setup', 'Upgrade switches to smart dimmers with seamless app integration.', 899.00, 90, 'electric_smart.jpg', 1),
(6, 3, 'Electrical Safety Inspection', 'safety-inspection', 'Thorough diagnostic check of circuits, panel load, outlets, and grounding.', 1199.00, 120, 'electric_panel.jpg', 1);

-- 6. SEED PROVIDER-SERVICES
INSERT INTO `provider_services` (`provider_id`, `service_id`, `custom_price`, `is_offered`) VALUES
(1, 3, 499.00, 1),
(1, 4, 599.00, 1),
(2, 1, 1499.00, 1),
(2, 2, 699.00, 1),
(3, 5, 899.00, 1),
(3, 6, 1199.00, 1);

-- 7. SEED SAMPLE BOOKINGS (In INR)
INSERT INTO `bookings` (`id`, `booking_code`, `customer_id`, `provider_id`, `service_id`, `booking_date`, `booking_time`, `service_address`, `latitude`, `longitude`, `total_amount`, `payment_method`, `payment_status`, `status`, `customer_notes`) VALUES
(1, 'BK-89021', 1, 1, 3, '2026-11-20', '10:00:00', 'Flat 402, Green Acres, Bandra West, Mumbai', 19.0596, 72.8295, 499.00, 'upi', 'paid', 'accepted', 'Slow drip under master bath sink.'),
(2, 'BK-89022', 2, 2, 1, '2026-11-22', '13:00:00', 'Tower 3, Hiranandani Gardens, Powai, Mumbai', 19.1197, 72.9051, 1499.00, 'upi', 'pending', 'pending', 'Pet friendly products only please.'),
(3, 'BK-89020', 1, 3, 5, '2026-11-10', '11:00:00', 'Flat 402, Green Acres, Bandra West, Mumbai', 19.0596, 72.8295, 899.00, 'card', 'paid', 'completed', 'Living room chandeliers and hallway.');

-- 8. SEED BOOKING TIMELINE
INSERT INTO `booking_timeline` (`booking_id`, `status`, `notes`) VALUES
(1, 'pending', 'Booking request placed by customer'),
(1, 'accepted', 'Accepted by specialist David Sterling'),
(2, 'pending', 'Awaiting provider confirmation'),
(3, 'pending', 'Booking request placed by customer'),
(3, 'accepted', 'Provider confirmed appointment'),
(3, 'in_progress', 'Technician arrived on site'),
(3, 'completed', 'Service verified and completed');

-- 9. SEED REVIEWS
INSERT INTO `reviews` (`booking_id`, `customer_id`, `provider_id`, `rating`, `comment`) VALUES
(3, 1, 3, 5, 'Julian was prompt, courteous, and configured our smart lighting system flawlessly. Highly recommended!');

-- 10. SEED NOTIFICATIONS
INSERT INTO `notifications` (`user_id`, `title`, `message`, `type`, `link`) VALUES
(2, 'Booking Confirmed', 'Your plumbing appointment BK-89021 with David Sterling is confirmed for Nov 20.', 'booking', '/customer/bookings/BK-89021'),
(4, 'New Request Received', 'You have a new booking request BK-89021 from Sophia Miller.', 'booking', '/provider/bookings/BK-89021'),
(1, 'System Initialized', 'Welcome to NEARZO On-Demand Service Platform Admin Console.', 'system', '/admin/dashboard');
