
/*The e-commerce website database is organized into sections. Each section plays a crucial role in the overall functioning*/
CREATE TABLE `admin` (
  `id` integer PRIMARY KEY AUTO_INCREMENT,
  `username` varchar(50) UNIQUE NOT NULL,
  `password_hash` varchar(50) NOT NULL,
  `email` varchar(100) UNIQUE NOT NULL,
  `full_name` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT 'CURRENT_TIMESTAMP',
  `updated_at` timestamp NOT NULL DEFAULT 'CURRENT_TIMESTAMP'
);

CREATE TABLE `role` (
  `id` integer PRIMARY KEY AUTO_INCREMENT,
  `name` varchar(50) UNIQUE NOT NULL
);

CREATE TABLE `admin_role` (
  `admin_id` integer NOT NULL,
  `role_id` integer NOT NULL
);

CREATE TABLE `users` (
  `id` INT PRIMARY KEY AUTO_INCREMENT,
  `name` VARCHAR(100),
  `email` VARCHAR(100) UNIQUE,
  `sex` ENUM(Male,Female),
  `password_hash` TEXT,
  `active` BOOLEAN DEFAULT true,
  `created_at` TIMESTAMP DEFAULT (CURRENT_TIMESTAMP),
  `updated_at` TIMESTAMP DEFAULT (CURRENT_TIMESTAMP)
);

CREATE TABLE `user_address` (
  `id` INT PRIMARY KEY AUTO_INCREMENT,
  `user_id` INT,
  `address_line1` VARCHAR(150),
  `contact_email` VARCHAR(50),
  `city` VARCHAR(50),
  `state` VARCHAR(50),
  `postal_code` VARCHAR(20),
  `phone_number` VARCHAR(20),
  `country` VARCHAR(50),
  `is_primary` BOOLEAN DEFAULT false,
  `created_at` TIMESTAMP DEFAULT (CURRENT_TIMESTAMP),
  `updated_at` TIMESTAMP DEFAULT (CURRENT_TIMESTAMP)
);

CREATE TABLE `user_payment` (
  `id` INT PRIMARY KEY AUTO_INCREMENT,
  `user_id` INT,
  `address_id` INT,
  `cardholder_name` VARCHAR(255),
  `card_number` VARCHAR(20),
  `expiration_month` INT,
  `expiration_year` INT,
  `cvv` VARCHAR(4),
  `created_at` TIMESTAMP DEFAULT (CURRENT_TIMESTAMP),
  `updated_at` TIMESTAMP DEFAULT (CURRENT_TIMESTAMP)
);

CREATE TABLE `supplier` (
  `id` INT PRIMARY KEY AUTO_INCREMENT,
  `supplier_name` VARCHAR(100),
  `contact_name` VARCHAR(100),
  `email` VARCHAR(100),
  `phone` VARCHAR(20),
  `address` VARCHAR(100),
  `city` VARCHAR(100),
  `country` VARCHAR(100),
  `created_at` TIMESTAMP DEFAULT (CURRENT_TIMESTAMP),
  `updated_at` TIMESTAMP DEFAULT (CURRENT_TIMESTAMP)
);

CREATE TABLE `products` (
  `id` INT PRIMARY KEY AUTO_INCREMENT,
  `product_name` VARCHAR(255),
  `description` TEXT,
  `price` DECIMAL(10,2),
  `compare_price` DECIMAL(10,2) DEFAULT 0,
  `vente_price` DECIMAL(10,2),
  `stock_quantity` INT,
  `supplier_id` INT,
  `active` BOOLEAN DEFAULT true,
  `created_at` TIMESTAMP DEFAULT (CURRENT_TIMESTAMP),
  `updated_at` TIMESTAMP DEFAULT (CURRENT_TIMESTAMP)
);

CREATE TABLE `categories` (
  `id` INT PRIMARY KEY AUTO_INCREMENT,
  `name` VARCHAR(100),
  `parent_id` INT,
  `created_at` TIMESTAMP DEFAULT (CURRENT_TIMESTAMP),
  `updated_at` TIMESTAMP DEFAULT (CURRENT_TIMESTAMP)
);

CREATE TABLE `product_categories` (
  `category_id` INT,
  `product_id` INT,
  PRIMARY KEY (`category_id`, `product_id`)
);

CREATE TABLE `variant_options` (
  `id` INT PRIMARY KEY AUTO_INCREMENT,
  `title` TEXT,
  `product_id` INT,
  `quantity` INT DEFAULT 0,
  `sku` VARCHAR(255),
  `active` BOOLEAN DEFAULT true
);

CREATE TABLE `gallery` (
  `id` INT PRIMARY KEY AUTO_INCREMENT,
  `product_variant_id` INT,
  `categorie_id` INT,
  `image` TEXT,
  `placeholder` TEXT,
  `is_thumbnail` BOOLEAN DEFAULT false,
  `created_at` TIMESTAMP DEFAULT (CURRENT_TIMESTAMP),
  `updated_at` TIMESTAMP DEFAULT (CURRENT_TIMESTAMP)
);

CREATE TABLE `warehouses` (
  `id` INT PRIMARY KEY AUTO_INCREMENT,
  `name` VARCHAR(255),
  `location` VARCHAR(255),
  `created_at` TIMESTAMP DEFAULT (CURRENT_TIMESTAMP),
  `updated_at` TIMESTAMP DEFAULT (CURRENT_TIMESTAMP)
);

CREATE TABLE `warehouse_inventory` (
  `id` INT PRIMARY KEY AUTO_INCREMENT,
  `warehouse_id` INT,
  `product_id` INT,
  `quantity` INT,
  `created_at` TIMESTAMP DEFAULT (CURRENT_TIMESTAMP),
  `updated_at` TIMESTAMP DEFAULT (CURRENT_TIMESTAMP)
);

CREATE TABLE `components` (
  `id` INT PRIMARY KEY AUTO_INCREMENT,
  `component_name` VARCHAR(255) NOT NULL,
  `description` TEXT,
  `image` TEXT NOT NULL,
  `price` DECIMAL(10,2) NOT NULL,
  `compare_price` DECIMAL(10,2) DEFAULT (0),
  `vente_price` DECIMAL(10,2) NOT NULL,
  `stock_quantity` INT NOT NULL,
  `is_active` BOOLEAN DEFAULT (TRUE),
  `created_at` TIMESTAMP NOT NULL DEFAULT (CURRENT_TIMESTAMP),
  `updated_at` TIMESTAMP NOT NULL DEFAULT (CURRENT_TIMESTAMP)
);

CREATE TABLE `product_composer` (
  `id` INT PRIMARY KEY AUTO_INCREMENT,
  `product_id` INT NOT NULL,
  `component_id` INT NOT NULL,
  `quantity` INT NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT (CURRENT_TIMESTAMP),
  `updated_at` TIMESTAMP NOT NULL DEFAULT (CURRENT_TIMESTAMP)
);

CREATE TABLE `coupons` (
  `id` INT PRIMARY KEY AUTO_INCREMENT,
  `code` VARCHAR(50) NOT NULL,
  `discount_amount` DECIMAL(10,2),
  `valid_from` TIMESTAMP NOT NULL,
  `valid_until` TIMESTAMP NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT (CURRENT_TIMESTAMP)
);

CREATE TABLE `product_coupons` (
  `product_id` INT NOT NULL,
  `coupon_id` INT NOT NULL,
  PRIMARY KEY (`product_id`, `coupon_id`)
);

CREATE TABLE `product_composer_coupons` (
  `components_id` INT NOT NULL,
  `coupon_id` INT NOT NULL,
  PRIMARY KEY (`components_id`, `coupon_id`)
);

CREATE TABLE `carts` (
  `id` INT PRIMARY KEY AUTO_INCREMENT,
  `user_id` INT NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT (CURRENT_TIMESTAMP),
  `updated_at` TIMESTAMP NOT NULL DEFAULT (CURRENT_TIMESTAMP)
);

CREATE TABLE `cart_items` (
  `id` INT PRIMARY KEY AUTO_INCREMENT,
  `cart_id` INT NOT NULL,
  `component_id` INT,
  `product_id` INT,
  `product_variant_id` INT,
  `quantity` INT NOT NULL,
  `price` DECIMAL(10,2) NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT (CURRENT_TIMESTAMP),
  `updated_at` TIMESTAMP NOT NULL DEFAULT (CURRENT_TIMESTAMP)
);

CREATE TABLE `cart_items_compo` (
  `id` INT PRIMARY KEY AUTO_INCREMENT,
  `cart_items_id` INT NOT NULL,
  `product_id` INT NOT NULL,
  `product_variant_id` INT NOT NULL
);

CREATE TABLE `statuses` (
  `id` INT PRIMARY KEY AUTO_INCREMENT,
  `status_name` VARCHAR(255) NOT NULL,
  `color` VARCHAR(50) NOT NULL,
  `privacy` VARCHAR(10) NOT NULL DEFAULT (private),
  `created_at` TIMESTAMP NOT NULL DEFAULT (CURRENT_TIMESTAMP),
  `updated_at` TIMESTAMP NOT NULL DEFAULT (CURRENT_TIMESTAMP)
);

CREATE TABLE `order_statuses` (
  `id` INT PRIMARY KEY AUTO_INCREMENT,
  `status_id` INT NOT NULL,
  `orders_id` INT NOT NULL,
  `description` VARCHAR(100) NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT (CURRENT_TIMESTAMP),
  `updated_at` TIMESTAMP NOT NULL DEFAULT (CURRENT_TIMESTAMP)
);

CREATE TABLE `porso_statuses` (
  `id` INT PRIMARY KEY AUTO_INCREMENT,
  `orders_id` INT NOT NULL,
  `order_items_id` INT NOT NULL,
  `component_id` INT NOT NULL,
  `quantity_obli_comp` INT NOT NULL,
  `product_id` INT NOT NULL,
  `product_variant_id` INT NOT NULL,
  `quantity_obli_var` INT NOT NULL
);

CREATE TABLE `porso_statuses_comp` (
  `id` INT PRIMARY KEY AUTO_INCREMENT,
  `porso_statuses_id` INT NOT NULL,
  `product_id` INT NOT NULL,
  `product_variant_id` INT NOT NULL,
  `quantity_obli_var` INT NOT NULL
);

CREATE TABLE `shippers` (
  `id` INT PRIMARY KEY AUTO_INCREMENT,
  `shipper_name` VARCHAR(45) NOT NULL,
  `email` VARCHAR(45) NOT NULL,
  `phone` VARCHAR(20) NOT NULL,
  `adress` VARCHAR(100) NOT NULL,
  `country` VARCHAR(20) DEFAULT (Maroc),
  `city` VARCHAR(20) NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT (CURRENT_TIMESTAMP),
  `updated_at` TIMESTAMP NOT NULL DEFAULT (CURRENT_TIMESTAMP)
);

CREATE TABLE `orders` (
  `id` INT PRIMARY KEY AUTO_INCREMENT,
  `user_id` INT NOT NULL,
  `shipping_id` INT NOT NULL,
  `address_id` INT NOT NULL,
  `price` DECIMAL(10,2) NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT (CURRENT_TIMESTAMP),
  `updated_at` TIMESTAMP NOT NULL DEFAULT (CURRENT_TIMESTAMP)
);

CREATE TABLE `order_items` (
  `id` INT PRIMARY KEY AUTO_INCREMENT,
  `order_id` INT NOT NULL,
  `component_id` INT,
  `product_id` INT,
  `product_variant_id` INT,
  `quantity` INT NOT NULL,
  `price` DECIMAL(10,2) NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT (CURRENT_TIMESTAMP),
  `updated_at` TIMESTAMP NOT NULL DEFAULT (CURRENT_TIMESTAMP)
);

CREATE TABLE `orders_items_compo` (
  `id` INT PRIMARY KEY AUTO_INCREMENT,
  `orders_items_id` INT NOT NULL,
  `product_id` INT NOT NULL,
  `product_variant_id` INT NOT NULL,
  `quantity` INT NOT NULL
);

CREATE TABLE `reviews` (
  `id` INT PRIMARY KEY AUTO_INCREMENT,
  `user_id` INT NOT NULL,
  `product_id` INT,
  `components_id` INT,
  `riagi` INT DEFAULT (0),
  `rating` INT NOT NULL,
  `comment` TEXT NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT (CURRENT_TIMESTAMP)
);

ALTER TABLE `admin_role` ADD FOREIGN KEY (`admin_id`) REFERENCES `admin` (`id`);

ALTER TABLE `admin_role` ADD FOREIGN KEY (`role_id`) REFERENCES `role` (`id`);

ALTER TABLE `user_address` ADD FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

ALTER TABLE `user_payment` ADD FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

ALTER TABLE `user_payment` ADD FOREIGN KEY (`address_id`) REFERENCES `user_address` (`id`);

ALTER TABLE `products` ADD FOREIGN KEY (`supplier_id`) REFERENCES `supplier` (`id`);

ALTER TABLE `categories` ADD FOREIGN KEY (`parent_id`) REFERENCES `categories` (`id`);

ALTER TABLE `product_categories` ADD FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`);

ALTER TABLE `product_categories` ADD FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

ALTER TABLE `variant_options` ADD FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

ALTER TABLE `gallery` ADD FOREIGN KEY (`product_variant_id`) REFERENCES `variant_options` (`id`);

ALTER TABLE `gallery` ADD FOREIGN KEY (`categorie_id`) REFERENCES `categories` (`id`);

ALTER TABLE `warehouse_inventory` ADD FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`);

ALTER TABLE `warehouse_inventory` ADD FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

ALTER TABLE `product_composer` ADD FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

ALTER TABLE `product_composer` ADD FOREIGN KEY (`component_id`) REFERENCES `components` (`id`);

ALTER TABLE `product_coupons` ADD FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

ALTER TABLE `product_coupons` ADD FOREIGN KEY (`coupon_id`) REFERENCES `coupons` (`id`);

ALTER TABLE `product_composer_coupons` ADD FOREIGN KEY (`components_id`) REFERENCES `components` (`id`);

ALTER TABLE `product_composer_coupons` ADD FOREIGN KEY (`coupon_id`) REFERENCES `coupons` (`id`);

ALTER TABLE `carts` ADD FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

ALTER TABLE `cart_items` ADD FOREIGN KEY (`cart_id`) REFERENCES `carts` (`id`);

ALTER TABLE `cart_items` ADD FOREIGN KEY (`component_id`) REFERENCES `components` (`id`);

ALTER TABLE `cart_items` ADD FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

ALTER TABLE `cart_items` ADD FOREIGN KEY (`product_variant_id`) REFERENCES `variant_options` (`id`);

ALTER TABLE `cart_items_compo` ADD FOREIGN KEY (`cart_items_id`) REFERENCES `cart_items` (`id`);

ALTER TABLE `cart_items_compo` ADD FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

ALTER TABLE `cart_items_compo` ADD FOREIGN KEY (`product_variant_id`) REFERENCES `variant_options` (`id`);

ALTER TABLE `order_statuses` ADD FOREIGN KEY (`status_id`) REFERENCES `statuses` (`id`);

ALTER TABLE `order_statuses` ADD FOREIGN KEY (`orders_id`) REFERENCES `orders` (`id`);

ALTER TABLE `porso_statuses` ADD FOREIGN KEY (`orders_id`) REFERENCES `orders` (`id`);

ALTER TABLE `porso_statuses` ADD FOREIGN KEY (`order_items_id`) REFERENCES `order_items` (`id`);

ALTER TABLE `porso_statuses` ADD FOREIGN KEY (`component_id`) REFERENCES `components` (`id`);

ALTER TABLE `porso_statuses` ADD FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

ALTER TABLE `porso_statuses` ADD FOREIGN KEY (`product_variant_id`) REFERENCES `variant_options` (`id`);

ALTER TABLE `porso_statuses_comp` ADD FOREIGN KEY (`porso_statuses_id`) REFERENCES `porso_statuses` (`id`);

ALTER TABLE `porso_statuses_comp` ADD FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

ALTER TABLE `porso_statuses_comp` ADD FOREIGN KEY (`product_variant_id`) REFERENCES `variant_options` (`id`);

ALTER TABLE `orders` ADD FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

ALTER TABLE `orders` ADD FOREIGN KEY (`shipping_id`) REFERENCES `shippers` (`id`);

ALTER TABLE `orders` ADD FOREIGN KEY (`address_id`) REFERENCES `user_address` (`id`);

ALTER TABLE `order_items` ADD FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`);

ALTER TABLE `order_items` ADD FOREIGN KEY (`component_id`) REFERENCES `components` (`id`);

ALTER TABLE `order_items` ADD FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

ALTER TABLE `order_items` ADD FOREIGN KEY (`product_variant_id`) REFERENCES `variant_options` (`id`);

ALTER TABLE `orders_items_compo` ADD FOREIGN KEY (`orders_items_id`) REFERENCES `order_items` (`id`);

ALTER TABLE `orders_items_compo` ADD FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

ALTER TABLE `orders_items_compo` ADD FOREIGN KEY (`product_variant_id`) REFERENCES `variant_options` (`id`);

ALTER TABLE `reviews` ADD FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

ALTER TABLE `reviews` ADD FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

ALTER TABLE `reviews` ADD FOREIGN KEY (`components_id`) REFERENCES `components` (`id`);

ALTER TABLE `products` ADD FOREIGN KEY (`price`) REFERENCES `products` (`stock_quantity`);
