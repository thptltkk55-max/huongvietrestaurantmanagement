SET NAMES utf8mb4;

CREATE TABLE IF NOT EXISTS roles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    role_name VARCHAR(100) NOT NULL,
    role_group VARCHAR(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE roles ADD COLUMN IF NOT EXISTS role_name VARCHAR(100) NOT NULL;
ALTER TABLE roles ADD COLUMN IF NOT EXISTS role_group VARCHAR(50) NOT NULL;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    role_id INT NULL,
    full_name VARCHAR(150) NOT NULL,
    username VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(30) NULL,
    status VARCHAR(30) NOT NULL DEFAULT 'Hoạt động',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_users_role_id (role_id),
    CONSTRAINT fk_users_role_id FOREIGN KEY (role_id) REFERENCES roles(id)
        ON UPDATE CASCADE
        ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE users ADD COLUMN IF NOT EXISTS role_id INT NULL;
ALTER TABLE users ADD COLUMN IF NOT EXISTS full_name VARCHAR(150) NULL;
ALTER TABLE users ADD COLUMN IF NOT EXISTS username VARCHAR(100) NULL;
ALTER TABLE users ADD COLUMN IF NOT EXISTS email VARCHAR(150) NULL;
ALTER TABLE users ADD COLUMN IF NOT EXISTS password VARCHAR(255) NULL;
ALTER TABLE users ADD COLUMN IF NOT EXISTS phone VARCHAR(30) NULL;
ALTER TABLE users ADD COLUMN IF NOT EXISTS status VARCHAR(30) NOT NULL DEFAULT 'Hoạt động';
ALTER TABLE users ADD COLUMN IF NOT EXISTS created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP;
ALTER TABLE users MODIFY password VARCHAR(255) NOT NULL;

UPDATE users
SET full_name = fullname
WHERE (full_name IS NULL OR full_name = '')
AND fullname IS NOT NULL;

INSERT INTO roles (role_name, role_group)
SELECT 'Giám đốc', 'Quản lý'
WHERE NOT EXISTS (SELECT 1 FROM roles WHERE role_name = 'Giám đốc');

INSERT INTO roles (role_name, role_group)
SELECT 'Trưởng phòng kế toán', 'Quản lý'
WHERE NOT EXISTS (SELECT 1 FROM roles WHERE role_name = 'Trưởng phòng kế toán');

INSERT INTO roles (role_name, role_group)
SELECT 'Trưởng phòng ẩm thực', 'Quản lý'
WHERE NOT EXISTS (SELECT 1 FROM roles WHERE role_name = 'Trưởng phòng ẩm thực');

INSERT INTO roles (role_name, role_group)
SELECT 'Quản lý nhà hàng', 'Quản lý'
WHERE NOT EXISTS (SELECT 1 FROM roles WHERE role_name = 'Quản lý nhà hàng');

INSERT INTO roles (role_name, role_group)
SELECT 'Thu ngân', 'Nhân viên'
WHERE NOT EXISTS (SELECT 1 FROM roles WHERE role_name = 'Thu ngân');

INSERT INTO roles (role_name, role_group)
SELECT 'Phục vụ', 'Nhân viên'
WHERE NOT EXISTS (SELECT 1 FROM roles WHERE role_name = 'Phục vụ');

INSERT INTO roles (role_name, role_group)
SELECT 'Lao công', 'Nhân viên'
WHERE NOT EXISTS (SELECT 1 FROM roles WHERE role_name = 'Lao công');

INSERT INTO roles (role_name, role_group)
SELECT 'Nhân viên kho', 'Nhân viên'
WHERE NOT EXISTS (SELECT 1 FROM roles WHERE role_name = 'Nhân viên kho');

INSERT INTO roles (role_name, role_group)
SELECT 'Đầu bếp', 'Nhân viên'
WHERE NOT EXISTS (SELECT 1 FROM roles WHERE role_name = 'Đầu bếp');

UPDATE roles SET role_group = 'Quản lý' WHERE role_name = 'Giám đốc';
UPDATE roles SET role_group = 'Quản lý' WHERE role_name = 'Trưởng phòng kế toán';
UPDATE roles SET role_group = 'Quản lý' WHERE role_name = 'Trưởng phòng ẩm thực';
UPDATE roles SET role_group = 'Quản lý' WHERE role_name = 'Quản lý nhà hàng';
UPDATE roles SET role_group = 'Nhân viên' WHERE role_name = 'Thu ngân';
UPDATE roles SET role_group = 'Nhân viên' WHERE role_name = 'Phục vụ';
UPDATE roles SET role_group = 'Nhân viên' WHERE role_name = 'Lao công';
UPDATE roles SET role_group = 'Nhân viên' WHERE role_name = 'Nhân viên kho';
UPDATE roles SET role_group = 'Nhân viên' WHERE role_name = 'Đầu bếp';

INSERT INTO users (
    role_id,
    full_name,
    username,
    email,
    password,
    phone,
    status,
    created_at
)
SELECT
    (SELECT id FROM roles WHERE role_name = 'Giám đốc' LIMIT 1),
    'Quản trị viên',
    'admin',
    'admin@huongviet.com',
    '$2y$10$mFFtETLdVGImgMhtC80ZwemWdsLHszMtgwNfZCpzsTcl0pJ0aUaOq',
    '',
    'Hoạt động',
    NOW()
WHERE NOT EXISTS (
    SELECT 1 FROM users WHERE username = 'admin' OR email = 'admin@huongviet.com'
);
