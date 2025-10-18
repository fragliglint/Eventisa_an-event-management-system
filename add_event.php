<?php
session_start();
require_once 'config/database.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $database = new Database();
    $db = $database->getConnection();
    
    // Get form data
    $title = $_POST['title'] ?? '';
    $category = $_POST['category'] ?? '';
    $event_date = $_POST['event_date'] ?? '';
    $event_time = $_POST['event_time'] ?? '';
    $location = $_POST['location'] ?? '';
    $description = $_POST['description'] ?? '';
    $ticket_price = $_POST['ticket_price'] ?? 0;
    $total_tickets = $_POST['total_tickets'] ?? 0;
    $status = $_POST['status'] ?? 'Draft';
    
    // Validate required fields
    $errors = [];
    
    if (empty($title)) {
        $errors[] = "Event title is required";
    }
    
    if (empty($category)) {
        $errors[] = "Category is required";
    }
    
    if (empty($event_date)) {
        $errors[] = "Event date is required";
    }
    
    if (empty($event_time)) {
        $errors[] = "Event time is required";
    }
    
    if (empty($location)) {
        $errors[] = "Location is required";
    }
    
    if (!is_numeric($ticket_price) || $ticket_price < 0) {
        $errors[] = "Valid ticket price is required";
    }
    
    if (!is_numeric($total_tickets) || $total_tickets < 1) {
        $errors[] = "Valid total tickets number is required";
    }
    
    // Handle file upload
    $image_url = 'assets/default-event.jpg'; // Default image
    if (isset($_FILES['event_image']) && $_FILES['event_image']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['event_image'];
        
        // Validate file type
        $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
        $file_type = mime_content_type($file['tmp_name']);
        
        if (!in_array($file_type, $allowed_types)) {
            $errors[] = "Only JPEG, PNG, and GIF images are allowed";
        } else {
            // Generate unique filename
            $file_extension = pathinfo($file['name'], PATHINFO_EXTENSION);
            $filename = uniqid() . '.' . $file_extension;
            $upload_path = 'uploads/events/' . $filename;
            
            // Create uploads directory if it doesn't exist
            if (!is_dir('uploads/events')) {
                mkdir('uploads/events', 0777, true);
            }
            
            // Move uploaded file
            if (move_uploaded_file($file['tmp_name'], $upload_path)) {
                $image_url = $upload_path;
            } else {
                $errors[] = "Failed to upload image";
            }
        }
    }
    
    // If no errors, insert into database
    if (empty($errors)) {
        try {
            $query = "INSERT INTO events (title, category, event_date, event_time, location, description, ticket_price, total_tickets, tickets_sold, image_url, status, created_by, created_at) 
                      VALUES (?, ?, ?, ?, ?, ?, ?, ?, 0, ?, ?, ?, NOW())";
            
            $stmt = $db->prepare($query);
            $stmt->execute([
                $title, 
                $category, 
                $event_date, 
                $event_time, 
                $location, 
                $description, 
                $ticket_price, 
                $total_tickets, 
                $image_url, 
                $status,
                $_SESSION['user_id']
            ]);
            
            $_SESSION['success_message'] = "Event created successfully!";
            header('Location: Event.php');
            exit();
            
        } catch (PDOException $e) {
            $_SESSION['error_message'] = "Database error: " . $e->getMessage();
            header('Location: Event.php');
            exit();
        }
    } else {
        $_SESSION['error_message'] = implode("<br>", $errors);
        header('Location: Event.php');
        exit();
    }
} else {
    // If not POST request, redirect back
    header('Location: Event.php');
    exit();
}
?>