-- ==========================================================
-- Database Migration: Two-Sided Marketplace System
-- Adds profession, pricing models, availability status,
-- profile photos, and location-based indexes.
-- ==========================================================

USE `ondemand_db`;

-- Add marketplace columns to providers table if not existing
ALTER TABLE `providers`
    ADD COLUMN IF NOT EXISTS `profession` VARCHAR(100) NOT NULL DEFAULT 'Plumber' AFTER `business_name`,
    ADD COLUMN IF NOT EXISTS `price_type` ENUM('per_hour', 'per_visit', 'fixed') NOT NULL DEFAULT 'per_hour' AFTER `hourly_rate`,
    ADD COLUMN IF NOT EXISTS `availability_status` ENUM('available', 'busy', 'offline') NOT NULL DEFAULT 'available' AFTER `is_available`,
    ADD COLUMN IF NOT EXISTS `profile_photo` VARCHAR(255) DEFAULT NULL AFTER `availability_status`,
    ADD COLUMN IF NOT EXISTS `show_phone` TINYINT(1) NOT NULL DEFAULT 1 AFTER `profile_photo`;

-- Add performance indexes for location & profession queries
ALTER TABLE `providers`
    ADD INDEX IF NOT EXISTS `idx_provider_profession` (`profession`),
    ADD INDEX IF NOT EXISTS `idx_provider_avail_status` (`availability_status`),
    ADD INDEX IF NOT EXISTS `idx_provider_spatial` (`latitude`, `longitude`, `is_approved`, `availability_status`);

-- Update existing demo providers with realistic professions and coordinates
UPDATE `providers` SET 
    `profession` = 'Plumber',
    `price_type` = 'per_hour',
    `availability_status` = 'available',
    `hourly_rate` = 599.00,
    `latitude` = 19.06000000,
    `longitude` = 72.86500000
WHERE `id` = 1;

UPDATE `providers` SET 
    `profession` = 'Cleaner',
    `price_type` = 'per_visit',
    `availability_status` = 'available',
    `hourly_rate` = 799.00,
    `latitude` = 19.07500000,
    `longitude` = 72.87500000
WHERE `id` = 2;

UPDATE `providers` SET 
    `profession` = 'Electrician',
    `price_type` = 'per_visit',
    `availability_status` = 'available',
    `hourly_rate` = 499.00,
    `latitude` = 19.08200000,
    `longitude` = 72.88500000
WHERE `id` = 3;
