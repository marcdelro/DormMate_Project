SET FOREIGN_KEY_CHECKS=0;
DROP TABLE IF EXISTS reservations;
DROP TABLE IF EXISTS units;
SET FOREIGN_KEY_CHECKS=1;

CREATE TABLE IF NOT EXISTS units (
    id INT AUTO_INCREMENT PRIMARY KEY,
    unit_type VARCHAR(100) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    is_reserved TINYINT(1) DEFAULT 0,
    description TEXT,
    photo_path VARCHAR(255),
    size VARCHAR(20)
);

INSERT INTO `units` (`id`, `unit_type`, `price`, `is_reserved`, `description`, `photo_path`, `size`) VALUES
(1, 'Single Room', 5000.00, 1, 'Cozy room with private bathroom', 'https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?w=400&h=300&fit=crop&q=80', '40'),
(2, 'Double Room', 3500.00, 0, 'Shared room with two beds and common bath', 'https://images.unsplash.com/photo-1631049307264-da0ec9d70304?w=400&h=300&fit=crop&q=80', '70'),
(3, 'Studio Unit', 7300.00, 0, 'Studio type with kitchenette and own bath', 'https://images.unsplash.com/photo-1502672260266-1c1ef2d93688?w=400&h=300&fit=crop&q=80', '100'),
(4, 'Single Room', 4000.00, 0, 'Simple unfurnished room', 'https://images.unsplash.com/photo-1586023492125-27b2c045efd7?w=400&h=300&fit=crop&q=80', '30');

CREATE TABLE IF NOT EXISTS reservations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    unit_id INT NOT NULL,
    valid_id_path VARCHAR(255),
    status VARCHAR(50) DEFAULT 'Pending',
    reservation_time_and_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (unit_id) REFERENCES units(id),
    FOREIGN KEY (user_id) REFERENCES users(id)
);
