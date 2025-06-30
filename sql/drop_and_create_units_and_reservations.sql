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
(1, 'Single Room', 5000.00, 1, 'Cozy room with private bathroom', 'images/Dr1.jpg', '40'),
(2, 'Double Room', 3500.00, 0, 'Shared room with two beds and common bath', 'images/Dr2.jpg', '70'),
(3, 'Studio Unit', 7300.00, 0, 'Studio type with kitchenette and own bath', 'images/Dr3.jpg', '100'),
(4, 'Single Room', 4000.00, 0, 'Simple unfurnished room', 'images/Dr4.jpg', '30');

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
