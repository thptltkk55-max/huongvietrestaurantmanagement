SET NAMES utf8mb4;

CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_id INT NULL,
    table_id INT NULL,
    user_id INT NULL,
    order_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    total_amount DECIMAL(12,2) DEFAULT 0,
    discount DECIMAL(12,2) DEFAULT 0,
    final_amount DECIMAL(12,2) DEFAULT 0,
    payment_method VARCHAR(50) DEFAULT 'Tiền mặt',
    payment_status VARCHAR(50) DEFAULT 'Chưa thanh toán',
    order_status VARCHAR(50) DEFAULT 'Đang phục vụ',
    note TEXT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE orders ADD COLUMN IF NOT EXISTS customer_id INT NULL;
ALTER TABLE orders ADD COLUMN IF NOT EXISTS table_id INT NULL;
ALTER TABLE orders ADD COLUMN IF NOT EXISTS user_id INT NULL;
ALTER TABLE orders ADD COLUMN IF NOT EXISTS order_date DATETIME DEFAULT CURRENT_TIMESTAMP;
ALTER TABLE orders ADD COLUMN IF NOT EXISTS total_amount DECIMAL(12,2) DEFAULT 0;
ALTER TABLE orders ADD COLUMN IF NOT EXISTS discount DECIMAL(12,2) DEFAULT 0;
ALTER TABLE orders ADD COLUMN IF NOT EXISTS final_amount DECIMAL(12,2) DEFAULT 0;
ALTER TABLE orders ADD COLUMN IF NOT EXISTS payment_method VARCHAR(50) DEFAULT 'Tiền mặt';
ALTER TABLE orders ADD COLUMN IF NOT EXISTS payment_status VARCHAR(50) DEFAULT 'Chưa thanh toán';
ALTER TABLE orders ADD COLUMN IF NOT EXISTS order_status VARCHAR(50) DEFAULT 'Đang phục vụ';
ALTER TABLE orders ADD COLUMN IF NOT EXISTS note TEXT NULL;
ALTER TABLE orders ADD COLUMN IF NOT EXISTS created_at DATETIME DEFAULT CURRENT_TIMESTAMP;

UPDATE orders
SET total_amount = total
WHERE (total_amount IS NULL OR total_amount = 0)
AND total IS NOT NULL;

UPDATE orders
SET final_amount = total_amount
WHERE final_amount IS NULL OR final_amount = 0;

UPDATE orders
SET payment_status = 'Chưa thanh toán'
WHERE payment_status IS NULL OR payment_status = '';

UPDATE orders
SET order_status = COALESCE(NULLIF(status, ''), 'Đang phục vụ')
WHERE order_status IS NULL OR order_status = '';

CREATE TABLE IF NOT EXISTS order_details (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    food_id INT NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(12,2) NOT NULL,
    total_price DECIMAL(12,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE order_details ADD COLUMN IF NOT EXISTS order_id INT NULL;
ALTER TABLE order_details ADD COLUMN IF NOT EXISTS food_id INT NULL;
ALTER TABLE order_details ADD COLUMN IF NOT EXISTS quantity INT NULL;
ALTER TABLE order_details ADD COLUMN IF NOT EXISTS price DECIMAL(12,2) NULL;
ALTER TABLE order_details ADD COLUMN IF NOT EXISTS total_price DECIMAL(12,2) DEFAULT 0;

UPDATE order_details
SET total_price = quantity * price
WHERE (total_price IS NULL OR total_price = 0)
AND quantity IS NOT NULL
AND price IS NOT NULL;

CREATE TABLE IF NOT EXISTS payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    payment_method VARCHAR(50) NOT NULL,
    amount DECIMAL(12,2) NOT NULL,
    payment_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    note TEXT NULL,
    INDEX idx_payments_order_id (order_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
