SET NAMES utf8mb4;

CREATE TABLE IF NOT EXISTS ingredients (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ingredient_name VARCHAR(255) NOT NULL,
    unit VARCHAR(50) NOT NULL,
    quantity DECIMAL(10,2) DEFAULT 0,
    min_quantity DECIMAL(10,2) DEFAULT 0,
    status VARCHAR(50) DEFAULT 'Đang dùng',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS suppliers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    supplier_name VARCHAR(255) NOT NULL,
    phone VARCHAR(50) NULL,
    email VARCHAR(255) NULL,
    address TEXT NULL,
    status VARCHAR(50) DEFAULT 'Hoạt động',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS import_receipts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    supplier_id INT NULL,
    import_date DATE NOT NULL,
    total_amount DECIMAL(12,2) DEFAULT 0,
    note TEXT NULL,
    created_by INT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_import_supplier (supplier_id),
    INDEX idx_import_created_by (created_by),
    CONSTRAINT fk_import_supplier FOREIGN KEY (supplier_id) REFERENCES suppliers(id)
        ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS import_details (
    id INT AUTO_INCREMENT PRIMARY KEY,
    import_id INT NOT NULL,
    ingredient_id INT NOT NULL,
    quantity DECIMAL(10,2) NOT NULL,
    unit_price DECIMAL(12,2) NOT NULL,
    total_price DECIMAL(12,2) NOT NULL,
    INDEX idx_detail_import (import_id),
    INDEX idx_detail_ingredient (ingredient_id),
    CONSTRAINT fk_detail_import FOREIGN KEY (import_id) REFERENCES import_receipts(id)
        ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT fk_detail_ingredient FOREIGN KEY (ingredient_id) REFERENCES ingredients(id)
        ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO ingredients (ingredient_name, unit, quantity, min_quantity, status)
SELECT 'Gạo', 'kg', 50, 10, 'Đang dùng'
WHERE NOT EXISTS (SELECT 1 FROM ingredients WHERE ingredient_name = 'Gạo');

INSERT INTO ingredients (ingredient_name, unit, quantity, min_quantity, status)
SELECT 'Thịt gà', 'kg', 20, 5, 'Đang dùng'
WHERE NOT EXISTS (SELECT 1 FROM ingredients WHERE ingredient_name = 'Thịt gà');

INSERT INTO ingredients (ingredient_name, unit, quantity, min_quantity, status)
SELECT 'Tôm', 'kg', 15, 3, 'Đang dùng'
WHERE NOT EXISTS (SELECT 1 FROM ingredients WHERE ingredient_name = 'Tôm');

INSERT INTO ingredients (ingredient_name, unit, quantity, min_quantity, status)
SELECT 'Mực', 'kg', 10, 3, 'Đang dùng'
WHERE NOT EXISTS (SELECT 1 FROM ingredients WHERE ingredient_name = 'Mực');

INSERT INTO ingredients (ingredient_name, unit, quantity, min_quantity, status)
SELECT 'Rau muống', 'bó', 30, 10, 'Đang dùng'
WHERE NOT EXISTS (SELECT 1 FROM ingredients WHERE ingredient_name = 'Rau muống');

INSERT INTO ingredients (ingredient_name, unit, quantity, min_quantity, status)
SELECT 'Dầu ăn', 'lít', 20, 5, 'Đang dùng'
WHERE NOT EXISTS (SELECT 1 FROM ingredients WHERE ingredient_name = 'Dầu ăn');

INSERT INTO ingredients (ingredient_name, unit, quantity, min_quantity, status)
SELECT 'Gia vị tổng hợp', 'kg', 8, 2, 'Đang dùng'
WHERE NOT EXISTS (SELECT 1 FROM ingredients WHERE ingredient_name = 'Gia vị tổng hợp');

INSERT INTO suppliers (supplier_name, status)
SELECT 'Công ty Thực phẩm Bình Dương', 'Hoạt động'
WHERE NOT EXISTS (SELECT 1 FROM suppliers WHERE supplier_name = 'Công ty Thực phẩm Bình Dương');

INSERT INTO suppliers (supplier_name, status)
SELECT 'Hải sản Tươi Sống Sài Gòn', 'Hoạt động'
WHERE NOT EXISTS (SELECT 1 FROM suppliers WHERE supplier_name = 'Hải sản Tươi Sống Sài Gòn');

INSERT INTO suppliers (supplier_name, status)
SELECT 'Rau củ sạch Thới Hòa', 'Hoạt động'
WHERE NOT EXISTS (SELECT 1 FROM suppliers WHERE supplier_name = 'Rau củ sạch Thới Hòa');
