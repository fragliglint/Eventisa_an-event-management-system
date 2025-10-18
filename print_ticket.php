<?php
session_start();
if (!isset($_SESSION['ticket_data'])) {
    header('Location: ticket_generator.php');
    exit;
}

$data = $_SESSION['ticket_data'];
$event_code = isset($data['event_id']) ? $data['event_id'] : '';
?>
<!DOCTYPE html>
<html>
<head>
    <title>Print Ticket - <?php echo htmlspecialchars($data['event_name']); ?></title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 20px; }
        .ticket { border: 2px solid #a78bfa; border-radius: 10px; padding: 20px; max-width: 420px; margin: 0 auto; }
        .header { background: #a78bfa; color: white; padding: 15px; text-align: center; border-radius: 5px; margin-bottom: 20px; }
        .header .meta { font-size: 13px; opacity: 0.95; margin-top: 6px; }
        .info-group { margin-bottom: 10px; display: flex; justify-content: space-between; }
        .qr-code { text-align: center; margin: 20px 0; }
        .ticket-id { text-align: center; font-weight: bold; color: #a78bfa; margin-top: 15px; }
        .small-muted { font-size: 12px; color: #6b7280; }
        @media print {
            body { padding: 0; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="ticket">
        <div class="header">
            <h2 style="margin:0;"><?php echo htmlspecialchars($data['event_name']); ?></h2>
            <p class="meta" style="margin:6px 0 0;"><?php echo htmlspecialchars($data['event_date']); ?> | <?php echo htmlspecialchars($data['event_location']); ?></p>
            <?php if (!empty($event_code)): ?>
                <div class="meta small-muted">Event Code: <?php echo htmlspecialchars($event_code); ?></div>
            <?php endif; ?>
        </div>
        
        <div class="customer-info">
            <div class="info-group">
                <strong>Name:</strong> <span><?php echo htmlspecialchars($data['customer_name']); ?></span>
            </div>
            <div class="info-group">
                <strong>Email:</strong> <span><?php echo htmlspecialchars($data['customer_email']); ?></span>
            </div>
            <div class="info-group">
                <strong>Phone:</strong> <span><?php echo htmlspecialchars($data['customer_phone']); ?></span>
            </div>
            <div class="info-group">
                <strong>Ticket Type:</strong> <span><?php echo htmlspecialchars($data['ticket_type']); ?></span>
            </div>
            <div class="info-group">
                <strong>Seat:</strong> <span><?php echo htmlspecialchars($data['seat_number']); ?></span>
            </div>
        </div>
        
        <div class="qr-code">
            <img src="<?php echo htmlspecialchars($data['qr_code']); ?>" alt="QR Code" width="200">
            <p class="small-muted">Scan for entry</p>
        </div>
        
        <div class="ticket-id">
            <?php echo htmlspecialchars($data['ticket_id']); ?>
        </div>

        <?php if (!empty($event_code)): ?>
            <div style="text-align:center; margin-top:8px;" class="small-muted">Event Code: <?php echo htmlspecialchars($event_code); ?></div>
        <?php endif; ?>
    </div>
    
    <div class="no-print" style="text-align: center; margin-top: 20px;">
        <button onclick="window.print()">Print Ticket</button>
        <button onclick="window.close()">Close</button>
    </div>
    
    <script>
        // Auto-print when page loads
        window.onload = function() {
            window.print();
        }
    </script>
</body>
</html>