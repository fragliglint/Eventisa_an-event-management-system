-- Create a new database with different name
CREATE DATABASE IF NOT EXISTS eventisa_dashboard;
USE eventisa_dashboard;

-- Users table for authentication (Merged version)
CREATE TABLE IF NOT EXISTS users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    phone VARCHAR(20),
    avatar_url VARCHAR(255),
    role ENUM('admin', 'manager', 'staff', 'user', 'organizer') DEFAULT 'user',
    status ENUM('active', 'inactive', 'suspended') DEFAULT 'active',
    email_verified BOOLEAN DEFAULT FALSE,
    last_login DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Categories table
CREATE TABLE IF NOT EXISTS categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) UNIQUE NOT NULL,
    description TEXT,
    icon VARCHAR(50),
    color VARCHAR(7),
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Events table (Merged version)
CREATE TABLE IF NOT EXISTS events (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    short_description VARCHAR(500),
    category VARCHAR(50) NOT NULL,
    location VARCHAR(255) NOT NULL,
    venue_name VARCHAR(255),
    event_date DATE NOT NULL,
    event_time TIME NOT NULL,
    end_date DATE,
    end_time TIME,
    image_url VARCHAR(255),
    ticket_price DECIMAL(10,2) DEFAULT 0.00,
    total_tickets INT DEFAULT 0,
    tickets_sold INT DEFAULT 0,
    organizer_id INT,
    status ENUM('Active', 'Draft', 'Past', 'Cancelled', 'Sold Out', 'Inactive') DEFAULT 'Draft',
    featured BOOLEAN DEFAULT FALSE,
    age_restriction ENUM('All Ages', '18+', '21+') DEFAULT 'All Ages',
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (organizer_id) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (created_by) REFERENCES users(id)
);

-- Bookings table (Merged version)
CREATE TABLE IF NOT EXISTS bookings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    invoice_id VARCHAR(20) UNIQUE NOT NULL,
    user_id INT NOT NULL,
    event_id INT NOT NULL,
    customer_name VARCHAR(100) NOT NULL,
    customer_email VARCHAR(100) NOT NULL,
    customer_phone VARCHAR(20),
    ticket_quantity INT NOT NULL,
    total_amount DECIMAL(10,2) NOT NULL,
    booking_date DATE NOT NULL,
    status ENUM('Pending', 'Confirmed', 'Cancelled', 'Refunded') DEFAULT 'Pending',
    payment_status ENUM('Pending', 'Paid', 'Failed', 'Refunded') DEFAULT 'Pending',
    payment_method ENUM('Credit Card', 'PayPal', 'Bank Transfer', 'Cash') DEFAULT 'Credit Card',
    booked_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE
);

-- Invoices table
CREATE TABLE IF NOT EXISTS invoices (
    id INT PRIMARY KEY AUTO_INCREMENT,
    invoice_number VARCHAR(20) UNIQUE NOT NULL,
    booking_id INT NOT NULL,
    issue_date DATE NOT NULL,
    due_date DATE NOT NULL,
    subtotal DECIMAL(10,2) NOT NULL,
    tax_amount DECIMAL(10,2) DEFAULT 0,
    fee_amount DECIMAL(10,2) DEFAULT 0,
    total_amount DECIMAL(10,2) NOT NULL,
    status ENUM('Paid', 'Unpaid', 'Overdue') DEFAULT 'Unpaid',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (booking_id) REFERENCES bookings(id)
);

-- Tickets table
CREATE TABLE IF NOT EXISTS tickets (
    id INT PRIMARY KEY AUTO_INCREMENT,
    booking_id INT NOT NULL,
    event_id INT NOT NULL,
    ticket_number VARCHAR(20) UNIQUE NOT NULL,
    attendee_name VARCHAR(100),
    attendee_email VARCHAR(100),
    qr_code_url VARCHAR(255),
    status ENUM('Active', 'Used', 'Cancelled') DEFAULT 'Active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE CASCADE,
    FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE
);

-- Payments table
CREATE TABLE IF NOT EXISTS payments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    booking_id INT NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    currency VARCHAR(3) DEFAULT 'USD',
    payment_method VARCHAR(50) NOT NULL,
    transaction_id VARCHAR(100) UNIQUE,
    status ENUM('Pending', 'Completed', 'Failed', 'Refunded') DEFAULT 'Pending',
    payment_date DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE CASCADE
);

-- Calendar events table
CREATE TABLE IF NOT EXISTS calendar_events (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    event_type ENUM('meeting', 'event', 'setup', 'task') NOT NULL,
    event_date DATE NOT NULL,
    event_time TIME NOT NULL,
    location VARCHAR(255),
    contact_name VARCHAR(100),
    contact_role VARCHAR(100),
    contact_phone VARCHAR(20),
    contact_email VARCHAR(100),
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id)
);

-- Financial transactions table
CREATE TABLE IF NOT EXISTS financial_transactions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    transaction_date DATE NOT NULL,
    description VARCHAR(255) NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    transaction_type ENUM('income', 'expense') NOT NULL,
    category VARCHAR(100),
    status ENUM('Completed', 'Pending', 'Cancelled') DEFAULT 'Pending',
    reference_id VARCHAR(100),
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Reviews table
CREATE TABLE IF NOT EXISTS reviews (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    event_id INT NOT NULL,
    rating INT CHECK (rating >= 1 AND rating <= 5),
    comment TEXT,
    is_approved BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_event (user_id, event_id)
);

-- Favorites table
CREATE TABLE IF NOT EXISTS favorites (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    event_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_event (user_id, event_id)
);

-- Activity log table
CREATE TABLE IF NOT EXISTS activity_logs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    activity_type VARCHAR(100) NOT NULL,
    description TEXT NOT NULL,
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- =============================================================================
-- INSERT SAMPLE DATA
-- =============================================================================

-- Insert sample categories
INSERT IGNORE INTO categories (name, description, icon, color) VALUES
('Music', 'Concerts, festivals, and live performances', '🎵', '#8b5cf6'),
('Sports', 'Games, tournaments, and matches', '⚽', '#ef4444'),
('Art & Design', 'Exhibitions, workshops, and shows', '🎨', '#eab308'),
('Food & Culinary', 'Festivals, tastings, and classes', '🍕', '#ea580c'),
('Technology', 'Conferences, meetups, and expos', '💻', '#3b82f6'),
('Fashion', 'Shows, launches, and exhibitions', '👗', '#ec4899'),
('Business', 'Conferences, networking events', '💼', '#06b6d4'),
('Education', 'Workshops, seminars, courses', '📚', '#10b981'),
('Health & Wellness', 'Fitness, yoga, and wellness events', '💪', '#10b981'),
('Outdoor & Adventure', 'Hiking, camping, and adventure activities', '🏕️', '#059669');

-- Insert sample admin user (password: admin123)
INSERT IGNORE INTO users (username, password_hash, first_name, last_name, email, role, status, email_verified) VALUES
('zahid', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Md.', 'Zahid Hasan', 'zahid@eventisa.com', 'admin', 'active', TRUE),
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Admin', 'User', 'admin@eventisa.com', 'admin', 'active', TRUE);

-- Insert sample regular users
INSERT IGNORE INTO users (first_name, last_name, email, password_hash, role, status) VALUES
('John', 'Doe', 'john@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user', 'active'),
('Jane', 'Smith', 'jane@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user', 'active'),
('Mike', 'Johnson', 'mike@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'organizer', 'active');

-- Insert sample events
INSERT IGNORE INTO events (title, description, short_description, category, location, venue_name, event_date, event_time, end_date, end_time, image_url, ticket_price, total_tickets, tickets_sold, featured, status, created_by) VALUES
('Symphony Under the Stars', 'Immerse yourself in electrifying performances by top classical artists under the beautiful night sky. A magical evening of music and ambiance.', 'Electrifying performances by top classical artists', 'Music', 'Sunset Park, Los Angeles, CA', 'Sunset Park Amphitheater', '2029-04-20', '19:00:00', '2029-04-20', '22:00:00', 'https://images.unsplash.com/photo-1506157786151-b8491531f063?auto=format&fit=crop&w=800&q=60', 50.00, 200, 125, TRUE, 'Active', 1),
('Runway Revolution 2029', 'Annual fashion show featuring emerging designers and established brands. Witness the future of fashion in an spectacular showcase.', 'Annual fashion show featuring emerging designers', 'Fashion', 'Vogue Hall, New York, NY', 'Vogue Hall Main Stage', '2029-05-01', '18:00:00', '2029-05-01', '21:00:00', 'https://images.unsplash.com/photo-1521336575822-6da63fb45455?auto=format&fit=crop&w=800&q=60', 100.00, 150, 75, TRUE, 'Active', 1),
('Global Wellness Summit', 'Health and wellness conference with expert speakers, workshops, and networking opportunities for wellness professionals.', 'Health and wellness conference with expert speakers', 'Health & Wellness', 'Wellness Arena, Miami, FL', 'Wellness Arena Conference Hall', '2029-05-05', '09:00:00', '2029-05-07', '17:00:00', 'https://images.unsplash.com/photo-1544367567-0f2fcb009e0b?auto=format&fit=crop&w=800&q=60', 75.00, 100, 40, FALSE, 'Active', 1),
('Adventure Gear Show', 'Latest outdoor and adventure equipment showcase with live demonstrations and expert talks from industry leaders.', 'Latest outdoor and adventure equipment showcase', 'Outdoor & Adventure', 'Rocky Ridge Exhibition Hall, Denver, CO', 'Main Exhibition Hall', '2029-06-05', '15:00:00', '2029-06-07', '18:00:00', 'https://images.unsplash.com/photo-1501555088652-021faa106b9b?auto=format&fit=crop&w=800&q=60', 40.00, 150, 65, FALSE, 'Active', 1),
('Artistry Unveiled Expo', 'Contemporary art exhibition featuring works from local and international artists across various mediums and styles.', 'Contemporary art exhibition featuring diverse artists', 'Art & Design', 'Modern Art Gallery, Chicago, IL', 'Gallery Main Hall', '2029-05-15', '10:00:00', '2029-05-20', '18:00:00', 'https://images.unsplash.com/photo-1503602642458-232111445657?auto=format&fit=crop&w=800&q=60', 20.00, 120, 85, TRUE, 'Active', 1),
('Summer Music Festival 2024', 'Join us for the biggest summer music festival featuring top artists from around the world. Three days of non-stop music, food trucks, and unforgettable experiences under the stars.', 'Biggest summer music festival with top artists', 'Music', 'Central Park, New York', 'Central Park Main Stage', '2024-06-15', '14:00:00', '2024-06-17', '23:00:00', 'https://images.unsplash.com/photo-1540575467063-178a50c2df87?auto=format&fit=crop&w=800&q=60', 79.00, 1000, 650, TRUE, 'Active', 1),
('Tech Innovators Conference', 'Annual technology conference bringing together industry leaders, startups, and innovators. Featuring keynote speeches, workshops, and networking opportunities.', 'Technology conference with industry leaders', 'Technology', 'Convention Center, San Francisco', 'Moscone Center', '2024-07-20', '09:00:00', '2024-07-21', '18:00:00', 'https://images.unsplash.com/photo-1540575467063-178a50c2df87?auto=format&fit=crop&w=800&q=60', 199.00, 500, 320, TRUE, 'Active', 1),
('Food & Wine Experience', 'Gourmet food tasting event featuring top chefs and wineries from around the region. Enjoy exquisite dishes paired with fine wines in an elegant setting.', 'Gourmet food tasting with top chefs', 'Food & Culinary', 'Downtown Plaza, Chicago', 'Chicago Cultural Center', '2024-08-12', '18:00:00', '2024-08-12', '22:00:00', 'https://images.unsplash.com/photo-1540575467063-178a50c2df87?auto=format&fit=crop&w=800&q=60', 120.00, 200, 150, FALSE, 'Active', 1);

-- Insert sample bookings
INSERT IGNORE INTO bookings (invoice_id, user_id, event_id, customer_name, customer_email, customer_phone, ticket_quantity, total_amount, booking_date, status, payment_status) VALUES
('INV1001', 3, 1, 'Jackson Moore', 'jackson@email.com', '123-456', 2, 100.00, '2029-02-15', 'Confirmed', 'Paid'),
('INV1002', 4, 2, 'Alicia Smithson', 'alicia@email.com', '222-333', 1, 100.00, '2029-02-16', 'Pending', 'Pending'),
('INV1003', 3, 3, 'Natalie Johnson', 'natalie@email.com', '555-111', 3, 225.00, '2029-02-17', 'Confirmed', 'Paid'),
('INV1004', 4, 1, 'Patrick Cooper', 'patrick@email.com', '777-888', 4, 200.00, '2029-02-18', 'Cancelled', 'Refunded'),
('INV1005', 3, 6, 'Sarah Wilson', 'sarah@email.com', '444-555', 2, 158.00, '2024-05-01', 'Confirmed', 'Paid'),
('INV1006', 4, 7, 'David Brown', 'david@email.com', '666-777', 1, 199.00, '2024-05-02', 'Confirmed', 'Paid');

-- Insert sample invoices
INSERT IGNORE INTO invoices (invoice_number, booking_id, issue_date, due_date, subtotal, tax_amount, fee_amount, total_amount, status) VALUES
('INV1001', 1, '2029-02-15', '2029-03-15', 100.00, 10.00, 5.00, 115.00, 'Paid'),
('INV1002', 2, '2029-02-16', '2029-03-16', 100.00, 10.00, 5.00, 115.00, 'Unpaid'),
('INV1003', 3, '2029-02-17', '2029-03-17', 225.00, 22.50, 11.25, 258.75, 'Paid'),
('INV1004', 4, '2029-02-18', '2029-03-18', 200.00, 20.00, 10.00, 230.00, 'Unpaid'),
('INV1005', 5, '2024-05-01', '2024-06-01', 158.00, 15.80, 7.90, 181.70, 'Paid'),
('INV1006', 6, '2024-05-02', '2024-06-02', 199.00, 19.90, 9.95, 228.85, 'Paid');

-- Insert sample tickets
INSERT IGNORE INTO tickets (booking_id, event_id, ticket_number, attendee_name, attendee_email, status) VALUES
(1, 1, 'TKT001001', 'Jackson Moore', 'jackson@email.com', 'Active'),
(1, 1, 'TKT001002', 'Emily Moore', 'emily@email.com', 'Active'),
(3, 3, 'TKT003001', 'Natalie Johnson', 'natalie@email.com', 'Active'),
(3, 3, 'TKT003002', 'Robert Johnson', 'robert@email.com', 'Active'),
(3, 3, 'TKT003003', 'Lisa Johnson', 'lisa@email.com', 'Active');

-- Insert sample payments
INSERT IGNORE INTO payments (booking_id, amount, payment_method, transaction_id, status, payment_date) VALUES
(1, 115.00, 'Credit Card', 'TXN001234', 'Completed', '2029-02-15 14:30:00'),
(3, 258.75, 'PayPal', 'TXN001235', 'Completed', '2029-02-17 16:45:00'),
(5, 181.70, 'Credit Card', 'TXN001236', 'Completed', '2024-05-01 11:20:00'),
(6, 228.85, 'Bank Transfer', 'TXN001237', 'Completed', '2024-05-02 09:15:00');

-- Insert sample calendar events
INSERT IGNORE INTO calendar_events (title, description, event_type, event_date, event_time, location, contact_name, contact_role, contact_phone, contact_email, created_by) VALUES
('Team Meeting', 'Weekly team sync meeting to discuss ongoing projects and upcoming events', 'meeting', '2029-05-01', '10:00:00', 'Conference Room A', 'John Smith', 'Project Manager', '+1-800-123-4567', 'john@example.com', 1),
('Product Launch', 'New product launch event with media and stakeholders', 'event', '2029-05-05', '14:00:00', 'Grand Hall', 'Sarah Johnson', 'Event Coordinator', '+1-800-765-4321', 'sarah@example.com', 1),
('Venue Setup', 'Event venue preparation and equipment testing', 'setup', '2029-05-10', '09:00:00', 'Exhibition Center', 'Mike Wilson', 'Logistics Manager', '+1-800-555-1234', 'mike@example.com', 1),
('Marketing Review', 'Monthly marketing performance review and strategy planning', 'meeting', '2029-05-15', '11:00:00', 'Conference Room B', 'Lisa Chen', 'Marketing Director', '+1-800-999-8888', 'lisa@example.com', 1);

-- Insert sample financial transactions
INSERT IGNORE INTO financial_transactions (transaction_date, description, amount, transaction_type, category, status, reference_id) VALUES
('2029-05-01', 'Sunset Park Venue Booking', 7000.00, 'expense', 'Venue', 'Completed', 'VEN001'),
('2029-05-02', 'Ticket Sales Revenue', 15000.00, 'income', 'Tickets', 'Completed', 'TKT001'),
('2029-05-03', 'Festival Promotion Campaign', 8000.00, 'expense', 'Marketing', 'Pending', 'MKT001'),
('2029-05-04', 'Audio Equipment Rental', 10000.00, 'expense', 'Equipment', 'Completed', 'EQP001'),
('2029-05-05', 'Sponsorship Revenue', 25000.00, 'income', 'Sponsorship', 'Completed', 'SPN001'),
('2029-05-06', 'Catering Services', 5000.00, 'expense', 'Food & Beverage', 'Pending', 'CAT001');

-- Insert sample reviews
INSERT IGNORE INTO reviews (user_id, event_id, rating, comment, is_approved) VALUES
(3, 1, 5, 'Absolutely breathtaking performance! The atmosphere was magical and the musicians were incredibly talented.', TRUE),
(4, 2, 4, 'Great fashion show with some really innovative designs. Looking forward to next year!', TRUE),
(3, 6, 5, 'Best music festival I have ever attended! The lineup was incredible and the organization was flawless.', TRUE),
(4, 7, 4, 'Very informative conference with excellent speakers. Learned a lot about emerging technologies.', TRUE);

-- Insert sample favorites
INSERT IGNORE INTO favorites (user_id, event_id) VALUES
(3, 1),
(3, 6),
(4, 2),
(4, 7),
(3, 3);

-- Insert sample activities
INSERT IGNORE INTO activity_logs (user_id, activity_type, description, ip_address) VALUES
(1, 'booking_review', 'Admin reviewed a refund request for Invoice ID: INV1004', '192.168.1.100'),
(1, 'price_update', 'Updated ticket prices for the event: Runway Revolution 2029', '192.168.1.100'),
(1, 'event_creation', 'Created new event: Symphony Under the Stars', '192.168.1.100'),
(1, 'user_management', 'Approved new organizer account: Mike Johnson', '192.168.1.100'),
(1, 'payment_processed', 'Processed payment for booking INV1001 - $115.00', '192.168.1.100'),
(3, 'booking_created', 'Created new booking for Symphony Under the Stars - 2 tickets', '192.168.1.101'),
(4, 'review_submitted', 'Submitted review for Runway Revolution 2029 - 4 stars', '192.168.1.102');

-- =============================================================================
-- CREATE USEFUL VIEWS
-- =============================================================================

-- View for event statistics
CREATE OR REPLACE VIEW event_stats AS
SELECT 
    e.id,
    e.title,
    e.category,
    e.event_date,
    e.total_tickets,
    e.tickets_sold,
    (e.tickets_sold / e.total_tickets * 100) as sold_percentage,
    e.ticket_price,
    (e.tickets_sold * e.ticket_price) as total_revenue,
    COUNT(DISTINCT b.id) as total_bookings
FROM events e
LEFT JOIN bookings b ON e.id = b.event_id AND b.status = 'Confirmed'
GROUP BY e.id;

-- View for financial summary
CREATE OR REPLACE VIEW financial_summary AS
SELECT 
    DATE_FORMAT(transaction_date, '%Y-%m') as month,
    transaction_type,
    SUM(amount) as total_amount,
    COUNT(*) as transaction_count
FROM financial_transactions
WHERE status = 'Completed'
GROUP BY DATE_FORMAT(transaction_date, '%Y-%m'), transaction_type;

-- View for user activity summary
CREATE OR REPLACE VIEW user_activity_summary AS
SELECT 
    u.id,
    u.first_name,
    u.last_name,
    u.email,
    u.role,
    COUNT(DISTINCT b.id) as total_bookings,
    COUNT(DISTINCT r.id) as total_reviews,
    COUNT(DISTINCT f.id) as total_favorites,
    MAX(a.created_at) as last_activity
FROM users u
LEFT JOIN bookings b ON u.id = b.user_id
LEFT JOIN reviews r ON u.id = r.user_id
LEFT JOIN favorites f ON u.id = f.user_id
LEFT JOIN activity_logs a ON u.id = a.user_id
GROUP BY u.id;

-- =============================================================================
-- CREATE INDEXES FOR PERFORMANCE
-- =============================================================================

CREATE INDEX idx_events_date ON events(event_date);
CREATE INDEX idx_events_category ON events(category);
CREATE INDEX idx_events_status ON events(status);
CREATE INDEX idx_bookings_user_id ON bookings(user_id);
CREATE INDEX idx_bookings_event_id ON bookings(event_id);
CREATE INDEX idx_bookings_status ON bookings(status);
CREATE INDEX idx_tickets_booking_id ON tickets(booking_id);
CREATE INDEX idx_payments_booking_id ON payments(booking_id);
CREATE INDEX idx_reviews_event_id ON reviews(event_id);
CREATE INDEX idx_favorites_user_id ON favorites(user_id);
CREATE INDEX idx_activity_logs_user_id ON activity_logs(user_id);
CREATE INDEX idx_financial_transactions_date ON financial_transactions(transaction_date);

-- =============================================================================
-- CREATE STORED PROCEDURES
-- =============================================================================

DELIMITER //

-- Procedure to get event revenue
CREATE PROCEDURE GetEventRevenue(IN event_id INT)
BEGIN
    SELECT 
        e.title,
        SUM(b.total_amount) as total_revenue,
        COUNT(b.id) as total_bookings,
        AVG(b.total_amount) as average_booking_value
    FROM events e
    LEFT JOIN bookings b ON e.id = b.event_id AND b.status = 'Confirmed'
    WHERE e.id = event_id
    GROUP BY e.id;
END //

-- Procedure to update ticket sales
CREATE PROCEDURE UpdateTicketSales(IN event_id INT)
BEGIN
    UPDATE events 
    SET tickets_sold = (
        SELECT COALESCE(SUM(ticket_quantity), 0) 
        FROM bookings 
        WHERE event_id = event_id AND status = 'Confirmed'
    )
    WHERE id = event_id;
END //

DELIMITER ;

-- =============================================================================
-- CREATE TRIGGERS
-- =============================================================================

DELIMITER //

-- Trigger to update event tickets_sold when booking is confirmed
CREATE TRIGGER after_booking_confirmed
    AFTER UPDATE ON bookings
    FOR EACH ROW
BEGIN
    IF NEW.status = 'Confirmed' AND OLD.status != 'Confirmed' THEN
        CALL UpdateTicketSales(NEW.event_id);
    END IF;
END //

-- Trigger to generate invoice when booking is created
CREATE TRIGGER after_booking_created
    AFTER INSERT ON bookings
    FOR EACH ROW
BEGIN
    INSERT INTO invoices (
        invoice_number, 
        booking_id, 
        issue_date, 
        due_date, 
        subtotal, 
        tax_amount, 
        fee_amount, 
        total_amount
    )
    VALUES (
        NEW.invoice_id,
        NEW.id,
        CURDATE(),
        DATE_ADD(CURDATE(), INTERVAL 30 DAY),
        NEW.total_amount,
        NEW.total_amount * 0.10, -- 10% tax
        NEW.total_amount * 0.05, -- 5% fee
        NEW.total_amount * 1.15  -- total + tax + fee
    );
END //

DELIMITER ;

SELECT 'Database setup completed successfully!' as status;