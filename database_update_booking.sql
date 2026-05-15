SET NAMES utf8mb4;

CREATE TABLE IF NOT EXISTS customers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_name VARCHAR(150) NULL,
    phone VARCHAR(30) NULL,
    email VARCHAR(150) NULL,
    address VARCHAR(255) NULL,
    customer_type VARCHAR(50) NOT NULL DEFAULT 'Khách vãng lai',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE customers ADD COLUMN IF NOT EXISTS customer_name VARCHAR(150) NULL;
ALTER TABLE customers ADD COLUMN IF NOT EXISTS phone VARCHAR(30) NULL;
ALTER TABLE customers ADD COLUMN IF NOT EXISTS email VARCHAR(150) NULL;
ALTER TABLE customers ADD COLUMN IF NOT EXISTS address VARCHAR(255) NULL;
ALTER TABLE customers ADD COLUMN IF NOT EXISTS customer_type VARCHAR(50) NOT NULL DEFAULT 'Khách vãng lai';
ALTER TABLE customers ADD COLUMN IF NOT EXISTS created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP;

UPDATE customers
SET customer_name = fullname
WHERE (customer_name IS NULL OR customer_name = '')
AND fullname IS NOT NULL;

CREATE TABLE IF NOT EXISTS tables_restaurant (
    id INT AUTO_INCREMENT PRIMARY KEY,
    table_name VARCHAR(50) NULL,
    capacity INT NULL,
    status VARCHAR(50) NOT NULL DEFAULT 'Trống'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE tables_restaurant ADD COLUMN IF NOT EXISTS table_name VARCHAR(50) NULL;
ALTER TABLE tables_restaurant ADD COLUMN IF NOT EXISTS capacity INT NULL;
ALTER TABLE tables_restaurant ADD COLUMN IF NOT EXISTS status VARCHAR(50) NOT NULL DEFAULT 'Trống';

UPDATE tables_restaurant
SET capacity = seats
WHERE (capacity IS NULL OR capacity = 0)
AND seats IS NOT NULL;

INSERT INTO tables_restaurant (table_name, capacity, status)
SELECT 'Bàn 1', 2, 'Trống'
WHERE NOT EXISTS (SELECT 1 FROM tables_restaurant WHERE table_name = 'Bàn 1');

INSERT INTO tables_restaurant (table_name, capacity, status)
SELECT 'Bàn 2', 4, 'Trống'
WHERE NOT EXISTS (SELECT 1 FROM tables_restaurant WHERE table_name = 'Bàn 2');

INSERT INTO tables_restaurant (table_name, capacity, status)
SELECT 'Bàn 3', 4, 'Trống'
WHERE NOT EXISTS (SELECT 1 FROM tables_restaurant WHERE table_name = 'Bàn 3');

INSERT INTO tables_restaurant (table_name, capacity, status)
SELECT 'Bàn 4', 6, 'Trống'
WHERE NOT EXISTS (SELECT 1 FROM tables_restaurant WHERE table_name = 'Bàn 4');

INSERT INTO tables_restaurant (table_name, capacity, status)
SELECT 'Bàn 5', 8, 'Trống'
WHERE NOT EXISTS (SELECT 1 FROM tables_restaurant WHERE table_name = 'Bàn 5');

INSERT INTO tables_restaurant (table_name, capacity, status)
SELECT 'Bàn VIP 1', 10, 'Trống'
WHERE NOT EXISTS (SELECT 1 FROM tables_restaurant WHERE table_name = 'Bàn VIP 1');

CREATE TABLE IF NOT EXISTS bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_id INT NULL,
    table_id INT NULL,
    booking_date DATE NULL,
    booking_time TIME NULL,
    number_of_people INT NULL,
    note TEXT NULL,
    status VARCHAR(50) NOT NULL DEFAULT 'Chờ xác nhận',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_bookings_customer_id (customer_id),
    INDEX idx_bookings_table_id (table_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE bookings ADD COLUMN IF NOT EXISTS customer_id INT NULL;
ALTER TABLE bookings ADD COLUMN IF NOT EXISTS table_id INT NULL;
ALTER TABLE bookings ADD COLUMN IF NOT EXISTS booking_date DATE NULL;
ALTER TABLE bookings ADD COLUMN IF NOT EXISTS booking_time TIME NULL;
ALTER TABLE bookings ADD COLUMN IF NOT EXISTS number_of_people INT NULL;
ALTER TABLE bookings ADD COLUMN IF NOT EXISTS note TEXT NULL;
ALTER TABLE bookings ADD COLUMN IF NOT EXISTS status VARCHAR(50) NOT NULL DEFAULT 'Chờ xác nhận';
ALTER TABLE bookings ADD COLUMN IF NOT EXISTS created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP;

UPDATE bookings
SET number_of_people = number_people
WHERE (number_of_people IS NULL OR number_of_people = 0)
AND number_people IS NOT NULL;

UPDATE bookings
SET booking_time = TIME(booking_date)
WHERE booking_time IS NULL
AND booking_date IS NOT NULL;
