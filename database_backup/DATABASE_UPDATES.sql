-- ============================================
-- DATABASE UPDATES FOR NEW FEATURES
-- Run these in phpMyAdmin
-- ============================================

-- 1. Add favorites/wishlist table
CREATE TABLE IF NOT EXISTS favorites (
  id INT NOT NULL AUTO_INCREMENT,
  user_id INT NOT NULL,
  menu_id INT NOT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY unique_favorite (user_id, menu_id),
  INDEX (user_id),
  INDEX (menu_id),
  CONSTRAINT fk_favorites_user
    FOREIGN KEY (user_id) REFERENCES users(id)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_favorites_menu
    FOREIGN KEY (menu_id) REFERENCES menu(id)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2. Add order ratings table
CREATE TABLE IF NOT EXISTS order_ratings (
  id INT NOT NULL AUTO_INCREMENT,
  order_id INT NOT NULL,
  user_id INT NOT NULL,
  rating INT NOT NULL CHECK (rating >= 1 AND rating <= 5),
  review TEXT,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY unique_rating (order_id),
  INDEX (order_id),
  INDEX (user_id),
  CONSTRAINT fk_rating_order
    FOREIGN KEY (order_id) REFERENCES orders(id)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_rating_user
    FOREIGN KEY (user_id) REFERENCES users(id)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3. Add estimated_time column to orders
ALTER TABLE orders 
ADD COLUMN IF NOT EXISTS estimated_time INT DEFAULT 15 COMMENT 'Estimated preparation time in minutes' AFTER status;

-- 4. Add cancelled_at column to orders
ALTER TABLE orders 
ADD COLUMN IF NOT EXISTS cancelled_at DATETIME NULL AFTER estimated_time;

-- 5. Add cancellation_reason column to orders
ALTER TABLE orders 
ADD COLUMN IF NOT EXISTS cancellation_reason VARCHAR(255) NULL AFTER cancelled_at;

-- 6. Create notifications table for push notifications
CREATE TABLE IF NOT EXISTS notifications (
  id INT NOT NULL AUTO_INCREMENT,
  user_id INT NOT NULL,
  order_id INT NULL,
  title VARCHAR(255) NOT NULL,
  message TEXT NOT NULL,
  type VARCHAR(50) NOT NULL DEFAULT 'info' COMMENT 'info, success, warning, error',
  is_read TINYINT(1) NOT NULL DEFAULT 0,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  INDEX (user_id),
  INDEX (order_id),
  INDEX (is_read),
  INDEX (created_at),
  CONSTRAINT fk_notification_user
    FOREIGN KEY (user_id) REFERENCES users(id)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_notification_order
    FOREIGN KEY (order_id) REFERENCES orders(id)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 7. Add indexes for better performance
ALTER TABLE orders ADD INDEX IF NOT EXISTS idx_user_created (user_id, created_at);
ALTER TABLE orders ADD INDEX IF NOT EXISTS idx_status_created (status, created_at);

-- ============================================
-- VERIFICATION QUERIES
-- ============================================

-- Check if tables were created
SHOW TABLES LIKE 'favorites';
SHOW TABLES LIKE 'order_ratings';
SHOW TABLES LIKE 'notifications';

-- Check if columns were added
DESCRIBE orders;

-- ============================================
-- DONE!
-- ============================================
