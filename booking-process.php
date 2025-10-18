<?php
session_start();
require_once 'config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate required fields
    $required_fields = ['event_id', 'ticket_qty', 'customer_name', 'customer_email', 'customer_phone', 'payment_method'];
    foreach ($required_fields as $field) {
        if (empty($_POST[$field])) {
            die("Error: Missing required field: $field");
        }
    }
    
    // Sanitize input data
    $event_id = filter_var($_POST['event_id'], FILTER_VALIDATE_INT);
    $ticket_qty = filter_var($_POST['ticket_qty'], FILTER_VALIDATE_INT);
    $customer_name = trim($_POST['customer_name']);
    $customer_email = filter_var($_POST['customer_email'], FILTER_VALIDATE_EMAIL);
    $customer_phone = trim($_POST['customer_phone']);
    $special_requests = trim($_POST['special_requests'] ?? '');
    $payment_method = $_POST['payment_method'];
    
    if (!$event_id || !$ticket_qty || !$customer_email) {
        die("Error: Invalid input data");
    }
    
    try {
        $database = new Database();
        $db = $database->getConnection();
        
        $db->beginTransaction();
        
        // Get event details
        $stmt = $db->prepare("SELECT * FROM events WHERE id = ? FOR UPDATE");
        $stmt->execute([$event_id]);
        $event = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$event) {
            $db->rollBack();
            die("Error: Event not found");
        }
        
        // Check ticket availability
        $available_tickets = $event['total_tickets'] - $event['tickets_sold'];
        if ($ticket_qty > $available_tickets) {
            $db->rollBack();
            header("Location: event-details.php?id=$event_id&error=tickets_sold_out");
            exit;
        }
        
        // Calculate amounts
        $subtotal = $event['ticket_price'] * $ticket_qty;
        $tax_amount = $subtotal * 0.10;
        $fee_amount = 5.00;
        $total_amount = $subtotal + $tax_amount + $fee_amount;
        
        // Generate invoice ID
        $invoice_id = 'INV' . date('YmdHis') . rand(100, 999);
        
        // Create booking
        $booking_stmt = $db->prepare("INSERT INTO bookings (invoice_id, event_id, customer_name, customer_email, customer_phone, tickets_qty, total_amount, special_requests, payment_method, booking_date, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), 'Confirmed')");
        $booking_stmt->execute([
            $invoice_id, $event_id, $customer_name, $customer_email, $customer_phone, 
            $ticket_qty, $total_amount, $special_requests, $payment_method
        ]);
        
        // Create invoice
        $invoice_stmt = $db->prepare("INSERT INTO invoices (invoice_number, booking_id, issue_date, due_date, subtotal, tax_amount, fee_amount, total_amount, status) VALUES (?, ?, NOW(), DATE_ADD(NOW(), INTERVAL 30 DAY), ?, ?, ?, ?, 'Paid')");
        $invoice_stmt->execute([
            $invoice_id, $db->lastInsertId(), $subtotal, $tax_amount, $fee_amount, $total_amount
        ]);
        
        // Update event tickets sold
        $update_event = $db->prepare("UPDATE events SET tickets_sold = tickets_sold + ? WHERE id = ?");
        $update_event->execute([$ticket_qty, $event_id]);
        
        $db->commit();
        
        header("Location: booking-confirmation.php?invoice_id=" . urlencode($invoice_id));
        exit;
        
    } catch (Exception $e) {
        if (isset($db)) {
            $db->rollBack();
        }
        error_log("Booking failed: " . $e->getMessage());
        header("Location: event-details.php?id=$event_id&error=booking_failed");
        exit;
    }
} else {
    header("Location: events.php");
    exit;
}
?>