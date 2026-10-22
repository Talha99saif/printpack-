CREATE DATABASE IF NOT EXISTS printpack CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE printpack;
SET FOREIGN_KEY_CHECKS=0;
DROP TABLE IF EXISTS reviews; DROP TABLE IF EXISTS notifications; DROP TABLE IF EXISTS order_items; DROP TABLE IF EXISTS orders; DROP TABLE IF EXISTS services; DROP TABLE IF EXISTS categories; DROP TABLE IF EXISTS users;
SET FOREIGN_KEY_CHECKS=1;
CREATE TABLE users (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 name VARCHAR(120) NOT NULL,
 email VARCHAR(190) NOT NULL UNIQUE,
 password_hash VARCHAR(255) NOT NULL,
 role ENUM('customer','vendor','admin') NOT NULL DEFAULT 'customer',
 status ENUM('pending','active','rejected','suspended') NOT NULL DEFAULT 'active',
 created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
 updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
 INDEX idx_users_role_status(role,status)
) ENGINE=InnoDB;
CREATE TABLE categories (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 name VARCHAR(100) NOT NULL,
 slug VARCHAR(100) NOT NULL UNIQUE,
 description VARCHAR(255) NULL,
 icon VARCHAR(30) NULL,
 sort_order INT NOT NULL DEFAULT 0,
 status ENUM('active','inactive') NOT NULL DEFAULT 'active'
) ENGINE=InnoDB;
CREATE TABLE services (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 vendor_id BIGINT UNSIGNED NOT NULL,
 category_id BIGINT UNSIGNED NULL,
 name VARCHAR(150) NOT NULL,
 description TEXT NULL,
 service_type ENUM('printing','packaging','custom') NOT NULL DEFAULT 'printing',
 base_price DECIMAL(12,2) NOT NULL,
 material VARCHAR(150) NULL,
 size_label VARCHAR(120) NULL,
 status ENUM('draft','active','inactive') NOT NULL DEFAULT 'active',
 order_count INT UNSIGNED NOT NULL DEFAULT 0,
 created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
 updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
 CONSTRAINT fk_services_vendor FOREIGN KEY(vendor_id) REFERENCES users(id),
 CONSTRAINT fk_services_category FOREIGN KEY(category_id) REFERENCES categories(id) ON DELETE SET NULL,
 INDEX idx_services_search(status,category_id,base_price)
) ENGINE=InnoDB;
CREATE TABLE orders (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 customer_id BIGINT UNSIGNED NOT NULL,
 vendor_id BIGINT UNSIGNED NOT NULL,
 delivery_address TEXT NOT NULL,
 distance_km DECIMAL(7,2) NOT NULL DEFAULT 0,
 subtotal DECIMAL(12,2) NOT NULL,
 delivery_fee DECIMAL(12,2) NOT NULL DEFAULT 0,
 total_amount DECIMAL(12,2) NOT NULL,
 payment_method ENUM('cod','stripe','bank_transfer') NOT NULL DEFAULT 'cod',
 payment_status ENUM('unpaid','pending','paid','failed','refunded') NOT NULL DEFAULT 'unpaid',
 status ENUM('pending','accepted','processing','dispatched','delivered','cancelled') NOT NULL DEFAULT 'pending',
 design_file VARCHAR(255) NULL,
 created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
 updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
 CONSTRAINT fk_orders_customer FOREIGN KEY(customer_id) REFERENCES users(id),
 CONSTRAINT fk_orders_vendor FOREIGN KEY(vendor_id) REFERENCES users(id),
 INDEX idx_orders_customer(customer_id,created_at), INDEX idx_orders_vendor(vendor_id,status,created_at)
) ENGINE=InnoDB;
CREATE TABLE order_items (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 order_id BIGINT UNSIGNED NOT NULL,
 service_id BIGINT UNSIGNED NOT NULL,
 quantity INT UNSIGNED NOT NULL,
 unit_price DECIMAL(12,2) NOT NULL,
 total_price DECIMAL(12,2) NOT NULL,
 customization_details JSON NULL,
 CONSTRAINT fk_items_order FOREIGN KEY(order_id) REFERENCES orders(id) ON DELETE CASCADE,
 CONSTRAINT fk_items_service FOREIGN KEY(service_id) REFERENCES services(id),
 INDEX idx_items_service(service_id)
) ENGINE=InnoDB;
CREATE TABLE notifications (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 user_id BIGINT UNSIGNED NOT NULL,
 title VARCHAR(150) NOT NULL,
 message TEXT NOT NULL,
 read_at TIMESTAMP NULL,
 created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
 CONSTRAINT fk_notifications_user FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE,
 INDEX idx_notifications_user(user_id,created_at)
) ENGINE=InnoDB;
CREATE TABLE reviews (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 order_id BIGINT UNSIGNED NOT NULL,
 customer_id BIGINT UNSIGNED NOT NULL,
 vendor_id BIGINT UNSIGNED NOT NULL,
 rating TINYINT UNSIGNED NOT NULL,
 comment TEXT NULL,
 created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
 UNIQUE KEY uq_review_order_customer(order_id,customer_id),
 CONSTRAINT chk_review_rating CHECK(rating BETWEEN 1 AND 5),
 CONSTRAINT fk_reviews_order FOREIGN KEY(order_id) REFERENCES orders(id) ON DELETE CASCADE,
 CONSTRAINT fk_reviews_customer FOREIGN KEY(customer_id) REFERENCES users(id),
 CONSTRAINT fk_reviews_vendor FOREIGN KEY(vendor_id) REFERENCES users(id)
) ENGINE=InnoDB;
INSERT INTO categories(name,slug,description,icon,sort_order) VALUES
('Printing','printing','Business cards, flyers, brochures and more','✳',1),
('Packaging','packaging','Boxes, bags and branded packaging','▧',2),
('Labels & Stickers','labels','Product labels and custom stickers','◉',3),
('Banners & Posters','banners','Large-format print for events and shops','▤',4),
('Stationery','stationery','Notebooks, letterheads and office stationery','✎',5),
('Custom Printing','custom','Unique products and special print jobs','✦',6);
