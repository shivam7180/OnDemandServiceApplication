-- ==========================================================
-- Database Schema for On-Demand Service Application
-- Architecture: Normalized Relational MySQL Schema (InnoDB)
-- ==========================================================

CREATE DATABASE IF NOT EXISTS `ondemand_db` 
CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;

USE `ondemand_db`;

-- Drop tables in reverse foreign key order for clean recreation
SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS `complaints`;
DROP TABLE IF EXISTS `notifications`;
DROP TABLE IF EXISTS `reviews`;
DROP TABLE IF EXISTS `booking_timeline`;
DROP TABLE IF EXISTS `bookings`;
DROP TABLE IF EXISTS `provider_availability`;
DROP TABLE IF EXISTS `provider_services`;
DROP TABLE IF EXISTS `services`;
DROP TABLE IF EXISTS `service_categories`;
DROP TABLE IF EXISTS `providers`;
DROP TABLE IF EXISTS `customers`;
DROP TABLE IF EXISTS `users`;
SET FOREIGN_KEY_CHECKS = 1;

-- ----------------------------------------------------------
-- 1. USERS TABLE (Core Authentication & Role Management)
-- ----------------------------------------------------------
CREATE TABLE `users` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `full_name` VARCHAR(100) NOT NULL,
    `email` VARCHAR(150) NOT NULL UNIQUE,
    `phone` VARCHAR(25) NOT NULL,
    `password_hash` VARCHAR(255) NOT NULL,
    `role` ENUM('customer', 'provider', 'admin') NOT NULL DEFAULT 'customer',
    `status` ENUM('active', 'inactive', 'suspended') NOT NULL DEFAULT 'active',
    `avatar` VARCHAR(255) DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_user_email` (`email`),
    INDEX `idx_user_role` (`role`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------
-- 2. CUSTOMERS PROFILE TABLE
-- ----------------------------------------------------------
CREATE TABLE `customers` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT UNSIGNED NOT NULL UNIQUE,
    `address` VARCHAR(255) DEFAULT NULL,
    `city` VARCHAR(100) DEFAULT NULL,
    `state` VARCHAR(100) DEFAULT NULL,
    `postal_code` VARCHAR(20) DEFAULT NULL,
    `latitude` DECIMAL(10, 8) DEFAULT NULL,
    `longitude` DECIMAL(11, 8) DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT `fk_customers_user` FOREIGN KEY (`user_id`) 
        REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------
-- 3. PROVIDERS PROFILE TABLE
-- ----------------------------------------------------------
CREATE TABLE `providers` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT UNSIGNED NOT NULL UNIQUE,
    `business_name` VARCHAR(150) DEFAULT NULL,
    `profession` VARCHAR(100) NOT NULL DEFAULT 'Plumber',
    `bio` TEXT DEFAULT NULL,
    `hourly_rate` DECIMAL(10, 2) DEFAULT 0.00,
    `price_type` ENUM('per_hour', 'per_visit', 'fixed') NOT NULL DEFAULT 'per_hour',
    `experience_years` INT UNSIGNED DEFAULT 1,
    `service_radius_km` INT UNSIGNED DEFAULT 15,
    `address` VARCHAR(255) DEFAULT NULL,
    `city` VARCHAR(100) DEFAULT NULL,
    `state` VARCHAR(100) DEFAULT NULL,
    `postal_code` VARCHAR(20) DEFAULT NULL,
    `latitude` DECIMAL(10, 8) DEFAULT NULL,
    `longitude` DECIMAL(11, 8) DEFAULT NULL,
    `is_approved` TINYINT(1) DEFAULT 1 COMMENT '0: Pending, 1: Approved, 2: Rejected',
    `is_available` TINYINT(1) DEFAULT 1 COMMENT '0: Offline, 1: Ready for jobs',
    `availability_status` ENUM('available', 'busy', 'offline') NOT NULL DEFAULT 'available',
    `profile_photo` VARCHAR(255) DEFAULT NULL,
    `show_phone` TINYINT(1) NOT NULL DEFAULT 1,
    `rating_avg` DECIMAL(3, 2) DEFAULT 5.00,
    `reviews_count` INT UNSIGNED DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_provider_location` (`latitude`, `longitude`),
    INDEX `idx_provider_profession` (`profession`),
    INDEX `idx_provider_approval` (`is_approved`, `availability_status`),
    CONSTRAINT `fk_providers_user` FOREIGN KEY (`user_id`) 
        REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------
-- 4. SERVICE CATEGORIES TABLE
-- ----------------------------------------------------------
CREATE TABLE `service_categories` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL UNIQUE,
    `slug` VARCHAR(120) NOT NULL UNIQUE,
    `icon` VARCHAR(100) NOT NULL DEFAULT 'fa-wrench',
    `description` VARCHAR(255) DEFAULT NULL,
    `badge` VARCHAR(50) DEFAULT NULL,
    `display_order` INT UNSIGNED DEFAULT 0,
    `is_active` TINYINT(1) DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------
-- 5. SERVICES TABLE
-- ----------------------------------------------------------
CREATE TABLE `services` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `category_id` INT UNSIGNED NOT NULL,
    `name` VARCHAR(150) NOT NULL,
    `slug` VARCHAR(170) NOT NULL UNIQUE,
    `description` TEXT DEFAULT NULL,
    `base_price` DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
    `duration_mins` INT UNSIGNED NOT NULL DEFAULT 60,
    `image` VARCHAR(255) DEFAULT NULL,
    `is_active` TINYINT(1) DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_services_category` FOREIGN KEY (`category_id`) 
        REFERENCES `service_categories` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------
-- 6. PROVIDER-SERVICES PIVOT TABLE
-- ----------------------------------------------------------
CREATE TABLE `provider_services` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `provider_id` INT UNSIGNED NOT NULL,
    `service_id` INT UNSIGNED NOT NULL,
    `custom_price` DECIMAL(10, 2) DEFAULT NULL,
    `is_offered` TINYINT(1) DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY `uk_provider_service` (`provider_id`, `service_id`),
    CONSTRAINT `fk_ps_provider` FOREIGN KEY (`provider_id`) 
        REFERENCES `providers` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_ps_service` FOREIGN KEY (`service_id`) 
        REFERENCES `services` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------
-- 7. PROVIDER AVAILABILITY TABLE
-- ----------------------------------------------------------
CREATE TABLE `provider_availability` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `provider_id` INT UNSIGNED NOT NULL,
    `day_of_week` ENUM('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday') NOT NULL,
    `start_time` TIME NOT NULL DEFAULT '09:00:00',
    `end_time` TIME NOT NULL DEFAULT '18:00:00',
    `is_active` TINYINT(1) DEFAULT 1,
    CONSTRAINT `fk_avail_provider` FOREIGN KEY (`provider_id`) 
        REFERENCES `providers` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------
-- 8. BOOKINGS TABLE
-- ----------------------------------------------------------
CREATE TABLE `bookings` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `booking_code` VARCHAR(20) NOT NULL UNIQUE,
    `customer_id` INT UNSIGNED NOT NULL,
    `provider_id` INT UNSIGNED NOT NULL,
    `service_id` INT UNSIGNED NOT NULL,
    `booking_date` DATE NOT NULL,
    `booking_time` TIME NOT NULL,
    `service_address` VARCHAR(255) NOT NULL,
    `latitude` DECIMAL(10, 8) DEFAULT NULL,
    `longitude` DECIMAL(11, 8) DEFAULT NULL,
    `total_amount` DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
    `payment_method` ENUM('cash', 'card', 'upi') DEFAULT 'card',
    `payment_status` ENUM('pending', 'paid', 'refunded') NOT NULL DEFAULT 'pending',
    `status` ENUM('pending', 'accepted', 'in_progress', 'completed', 'cancelled', 'rejected') NOT NULL DEFAULT 'pending',
    `cancellation_reason` VARCHAR(255) DEFAULT NULL,
    `customer_notes` TEXT DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_booking_code` (`booking_code`),
    INDEX `idx_booking_status` (`status`),
    CONSTRAINT `fk_bookings_customer` FOREIGN KEY (`customer_id`) 
        REFERENCES `customers` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_bookings_provider` FOREIGN KEY (`provider_id`) 
        REFERENCES `providers` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_bookings_service` FOREIGN KEY (`service_id`) 
        REFERENCES `services` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------
-- 9. BOOKING TIMELINE / STATUS HISTORY
-- ----------------------------------------------------------
CREATE TABLE `booking_timeline` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `booking_id` INT UNSIGNED NOT NULL,
    `status` ENUM('pending', 'accepted', 'in_progress', 'completed', 'cancelled', 'rejected') NOT NULL,
    `notes` VARCHAR(255) DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_timeline_booking` FOREIGN KEY (`booking_id`) 
        REFERENCES `bookings` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------
-- 10. REVIEWS & RATINGS TABLE
-- ----------------------------------------------------------
CREATE TABLE `reviews` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `booking_id` INT UNSIGNED NOT NULL UNIQUE,
    `customer_id` INT UNSIGNED NOT NULL,
    `provider_id` INT UNSIGNED NOT NULL,
    `rating` TINYINT UNSIGNED NOT NULL CHECK (`rating` BETWEEN 1 AND 5),
    `comment` TEXT DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_reviews_booking` FOREIGN KEY (`booking_id`) 
        REFERENCES `bookings` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_reviews_customer` FOREIGN KEY (`customer_id`) 
        REFERENCES `customers` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_reviews_provider` FOREIGN KEY (`provider_id`) 
        REFERENCES `providers` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------
-- 11. NOTIFICATIONS TABLE
-- ----------------------------------------------------------
CREATE TABLE `notifications` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT UNSIGNED NOT NULL,
    `title` VARCHAR(150) NOT NULL,
    `message` TEXT NOT NULL,
    `type` ENUM('booking', 'status', 'system', 'payment') DEFAULT 'booking',
    `link` VARCHAR(255) DEFAULT NULL,
    `is_read` TINYINT(1) DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_notif_user` FOREIGN KEY (`user_id`) 
        REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------
-- 12. COMPLAINTS & SUPPORT TICKETS TABLE
-- ----------------------------------------------------------
CREATE TABLE `complaints` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `booking_id` INT UNSIGNED DEFAULT NULL,
    `user_id` INT UNSIGNED NOT NULL,
    `subject` VARCHAR(200) NOT NULL,
    `description` TEXT NOT NULL,
    `status` ENUM('open', 'investigating', 'resolved', 'closed') DEFAULT 'open',
    `admin_response` TEXT DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT `fk_complaints_user` FOREIGN KEY (`user_id`) 
        REFERENCES `users` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_complaints_booking` FOREIGN KEY (`booking_id`) 
        REFERENCES `bookings` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
