-- Run this in phpMyAdmin (or `mysql -u root Bookstore < schema_staff_addon.sql`)
-- AFTER your existing Bookstore database is already set up.
-- Adds: 4th user role (Staff) + 3 tables for Staff's unique features.

USE Bookstore;

-- 4th role: Staff
CREATE TABLE IF NOT EXISTS staff (
    staff_id     INT AUTO_INCREMENT PRIMARY KEY,
    username     VARCHAR(100) NOT NULL UNIQUE,
    password     VARCHAR(255) NOT NULL,
    staff_name   VARCHAR(150) NOT NULL,
    designation  VARCHAR(100) DEFAULT 'Support Staff',
    created_at   TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Unique feature 1: Customer support tickets (CRUD + Search)
CREATE TABLE IF NOT EXISTS support_tickets (
    ticket_id         INT AUTO_INCREMENT PRIMARY KEY,
    customer_username VARCHAR(100) NOT NULL,
    subject           VARCHAR(200) NOT NULL,
    message           TEXT NOT NULL,
    status            ENUM('Open','In Progress','Resolved') DEFAULT 'Open',
    handled_by        VARCHAR(100) DEFAULT NULL,
    created_at        TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Unique feature 2: Shipment / delivery tracking (CRUD + Search)
CREATE TABLE IF NOT EXISTS shipments (
    shipment_id     INT AUTO_INCREMENT PRIMARY KEY,
    order_id        INT NOT NULL,
    courier_name    VARCHAR(100) NOT NULL,
    tracking_status ENUM('Pending','Shipped','In Transit','Delivered','Returned') DEFAULT 'Pending',
    updated_by      VARCHAR(100) DEFAULT NULL,
    updated_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Unique feature 3: Book review moderation (CRUD + Search)
CREATE TABLE IF NOT EXISTS reviews (
    review_id         INT AUTO_INCREMENT PRIMARY KEY,
    book_id           INT NOT NULL,
    customer_username VARCHAR(100) NOT NULL,
    rating            TINYINT NOT NULL,
    comment           TEXT,
    approval_status   ENUM('Pending','Approved','Rejected') DEFAULT 'Pending',
    moderated_by      VARCHAR(100) DEFAULT NULL,
    created_at        TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Optional: seed one sample ticket/shipment/review so dashboards aren't empty on first run
-- INSERT INTO support_tickets (customer_username, subject, message) VALUES ('@testuser', 'Order not received', 'My order #12 has not arrived yet.');
