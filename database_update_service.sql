CREATE TABLE IF NOT EXISTS service_assignments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    table_id INT NULL,
    booking_id INT NULL,
    order_id INT NULL,
    staff_id INT NULL,
    service_status VARCHAR(50) DEFAULT 'Chờ phục vụ',
    note TEXT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    completed_at DATETIME NULL,
    CONSTRAINT fk_service_table 
        FOREIGN KEY (table_id) REFERENCES tables_restaurant(id)
        ON DELETE SET NULL,
    CONSTRAINT fk_service_booking 
        FOREIGN KEY (booking_id) REFERENCES bookings(id)
        ON DELETE SET NULL,
    CONSTRAINT fk_service_order 
        FOREIGN KEY (order_id) REFERENCES orders(id)
        ON DELETE SET NULL,
    CONSTRAINT fk_service_staff 
        FOREIGN KEY (staff_id) REFERENCES users(id)
        ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

ALTER TABLE tables_restaurant
ADD COLUMN IF NOT EXISTS status VARCHAR(50) DEFAULT 'Trống';
