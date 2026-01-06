CREATE DATABASE IF NOT EXISTS yt_pro_sub;
USE yt_pro_sub;

-- Table for Users (Channels)
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    channel_id VARCHAR(255) NOT NULL UNIQUE,
    channel_name VARCHAR(255) DEFAULT 'Unknown',
    points INT DEFAULT 0,
    status ENUM('active', 'suspended') DEFAULT 'active',
    joined_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table to track who subscribed to whom (Transaction History)
CREATE TABLE IF NOT EXISTS subscriptions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    subscriber_channel_id VARCHAR(255) NOT NULL,
    target_channel_id VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_sub (subscriber_channel_id, target_channel_id)
);

-- Table for Admin Help tickets
CREATE TABLE IF NOT EXISTS admin_help (
    id INT AUTO_INCREMENT PRIMARY KEY,
    channel_id VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    status ENUM('open', 'closed') DEFAULT 'open',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);