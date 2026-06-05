-- Cassava Database Schema

-- Create database
CREATE DATABASE IF NOT EXISTS cassava_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE cassava_db;

-- Users table
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    avatar VARCHAR(255),
    role ENUM('user', 'landlord', 'admin') DEFAULT 'user',
    is_active TINYINT DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_role (role)
);

-- Rooms table (Phòng trọ)
CREATE TABLE rooms (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    description LONGTEXT,
    price DECIMAL(12, 2) NOT NULL,
    area DECIMAL(8, 2),
    address VARCHAR(255) NOT NULL,
    ward VARCHAR(100),
    district VARCHAR(100),
    city VARCHAR(100),
    latitude DECIMAL(10, 8),
    longitude DECIMAL(11, 8),
    bedrooms INT DEFAULT 1,
    bathrooms INT DEFAULT 1,
    image_main VARCHAR(255),
    status ENUM('available', 'rented', 'pending', 'archived') DEFAULT 'pending',
    views INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_status (status),
    INDEX idx_city (city),
    INDEX idx_district (district),
    FULLTEXT INDEX ft_search (title, description, address)
);

-- Room images table
CREATE TABLE room_images (
    id INT PRIMARY KEY AUTO_INCREMENT,
    room_id INT NOT NULL,
    image_path VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (room_id) REFERENCES rooms(id) ON DELETE CASCADE,
    INDEX idx_room_id (room_id)
);

-- Room amenities table (Tiện nghi)
CREATE TABLE room_amenities (
    id INT PRIMARY KEY AUTO_INCREMENT,
    room_id INT NOT NULL,
    amenity VARCHAR(100) NOT NULL,
    FOREIGN KEY (room_id) REFERENCES rooms(id) ON DELETE CASCADE,
    INDEX idx_room_id (room_id)
);

-- Contact requests table (Yêu cầu liên hệ)
CREATE TABLE contact_requests (
    id INT PRIMARY KEY AUTO_INCREMENT,
    room_id INT NOT NULL,
    user_id INT NOT NULL,
    message LONGTEXT,
    phone VARCHAR(20),
    status ENUM('pending', 'contacted', 'completed', 'rejected') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (room_id) REFERENCES rooms(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_room_id (room_id),
    INDEX idx_user_id (user_id),
    INDEX idx_status (status)
);

-- Reviews/Ratings table
CREATE TABLE reviews (
    id INT PRIMARY KEY AUTO_INCREMENT,
    room_id INT NOT NULL,
    user_id INT NOT NULL,
    rating INT CHECK (rating >= 1 AND rating <= 5),
    comment LONGTEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (room_id) REFERENCES rooms(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_review (room_id, user_id),
    INDEX idx_room_id (room_id),
    INDEX idx_rating (rating)
);

-- Favorites table (Yêu thích)
CREATE TABLE favorites (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    room_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (room_id) REFERENCES rooms(id) ON DELETE CASCADE,
    UNIQUE KEY unique_favorite (user_id, room_id),
    INDEX idx_user_id (user_id)
);

-- Admin logs table
CREATE TABLE admin_logs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    admin_id INT,
    action VARCHAR(100),
    target_table VARCHAR(50),
    target_id INT,
    details LONGTEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_admin_id (admin_id),
    INDEX idx_created_at (created_at)
);

-- Indexes for performance
CREATE INDEX idx_rooms_price ON rooms(price);
CREATE INDEX idx_rooms_created_at ON rooms(created_at DESC);
