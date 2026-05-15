SET NAMES utf8mb4;

ALTER TABLE customers ADD COLUMN IF NOT EXISTS customer_name VARCHAR(150) NULL;
ALTER TABLE customers ADD COLUMN IF NOT EXISTS email VARCHAR(150) NULL;
ALTER TABLE customers ADD COLUMN IF NOT EXISTS address VARCHAR(255) NULL;
ALTER TABLE customers ADD COLUMN IF NOT EXISTS customer_type VARCHAR(50) DEFAULT 'Khách vãng lai';
ALTER TABLE customers ADD COLUMN IF NOT EXISTS status VARCHAR(50) DEFAULT 'Hoạt động';
ALTER TABLE customers ADD COLUMN IF NOT EXISTS created_at DATETIME DEFAULT CURRENT_TIMESTAMP;

UPDATE customers
SET customer_name = fullname
WHERE (customer_name IS NULL OR customer_name = '')
AND fullname IS NOT NULL;

UPDATE customers
SET customer_type = 'Khách vãng lai'
WHERE customer_type IS NULL OR customer_type = '';

UPDATE customers
SET status = 'Hoạt động'
WHERE status IS NULL OR status = '';
