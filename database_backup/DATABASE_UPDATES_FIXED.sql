-- ============================================
-- DATABASE UPDATES FOR NEW FEATURES (FIXED)
-- Run these in phpMyAdmin
-- ============================================

-- First, let's check if users table exists, if not create it
CREATE TABLE IF NOT EXISTS users (
  id INT NOT NULL AUTO_INCREMENT,
  email VARCHAR(255) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  INDEX (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 1. Add favorites/wishlist table (WITHOUT foreign keys first)
CREATE TABLE IF NOT EXISTS favorites (
  id INT NOT NULL AUTO_INCREMENT,
  user_id INT NOT NULL,
  menu_id INT NOT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY unique_favorite (user_id, menu_id),
  INDEX (user_id),
  INDEX (menu_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2. Add order ratings table (WITHOUT foreign keys first)
CREATE TABLE IF NOT EXISTS order_ratings (
  id INT NOT NULL AUTO_INCREMENT,
  order_id INT NOT NULL,
  user_id INT NOT NULL,
  rating INT NOT NULL,
  review TEXT,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY unique_rating (order_id),
  INDEX (order_id),
  INDEX (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3. Add estimated_time column to orders
ALTER TABLE orders 
ADD COLUMN estimated_time INT DEFAULT 15 COMMENT 'Estimated preparation time in minutes';

-- 4. Add cancelled_at column to orders
ALTER TABLE orders 
ADD COLUMN cancelled_at DATETIME NULL;

-- 5. Add cancellation_reason column to orders
ALTER TABLE orders 
ADD COLUMN cancellation_reason VARCHAR(255) NULL;

-- 6. Create notifications table (WITHOUT foreign keys first)
CREATE TABLE IF NOT EXISTS notifications (
  id INT NOT NULL AUTO_INCREMENT,
  user_id INT NOT NULL,
  order_id INT NULL,
  title VARCHAR(255) NOT NULL,
  message TEXT NOT NULL,
  type VARCHAR(50) NOT NULL DEFAULT 'info',
  is_read TINYINT(1) NOT NULL DEFAULT 0,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  INDEX (user_id),
  INDEX (order_id),
  INDEX (is_read),
  INDEX (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 7. Add indexes for better performance
ALTER TABLE orders ADD INDEX idx_user_created (user_id, created_at);
ALTER TABLE orders ADD INDEX idx_status_created (status, created_at);

-- ============================================
-- VERIFICATION QUERIES
-- ============================================

-- Check if tables were created
SELECT 'favorites table created' as status FROM favorites LIMIT 1;
SELECT 'order_ratings table created' as status FROM order_ratings LIMIT 1;
SELECT 'notifications table created' as status FROM notifications LIMIT 1;

-- Check orders table structure
DESCRIBE orders;

-- ============================================
-- DONE!
-- ============================================
