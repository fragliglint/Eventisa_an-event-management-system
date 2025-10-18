<?php
// Manual QR code generation using Google Charts API
function generateQRCode($data) {
    // encode payload for URL
    $encoded = rawurlencode($data);
    // Use goqr.me API for QR generation
    return "https://api.qrserver.com/v1/create-qr-code/?size=300x300&data={$encoded}";
}

// Simple PDF generation using browser print for now
function generatePDFTicket($ticketData) {
    // For now, we'll use a simple approach - redirect to a print-friendly version
    $_SESSION['ticket_data'] = $ticketData;
    header('Location: print_ticket.php');
    exit;
}

session_start();

// ensure there's a default event id (preserve posted value when form submitted)
$event_id = $_POST['event_id'] ?? ('EVT-' . strtoupper(substr(uniqid(), 0, 6)));

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Process form data
    $customer_name = $_POST['customer_name'] ?? '';
    $customer_email = $_POST['customer_email'] ?? '';
    $customer_phone = $_POST['customer_phone'] ?? '';
    $event_name = $_POST['event_name'] ?? 'Summer Music Festival';
    $event_date = $_POST['event_date'] ?? '2024-08-15';
    $event_location = $_POST['event_location'] ?? 'Central Park';
    $ticket_type = $_POST['ticket_type'] ?? 'General Admission';
    $ticket_price = $_POST['ticket_price'] ?? '$75.00';
    $seat_number = $_POST['seat_number'] ?? 'GA-' . rand(1000, 9999);
    
    // preserve or use posted event_id
    $event_id = $_POST['event_id'] ?? $event_id;

    // Generate unique ticket ID
    $ticket_id = 'TKT-' . strtoupper(uniqid());
    
    // Create QR code data (plain text payload) - include Event ID
    $qr_data = "TicketID: {$ticket_id}\nEventID: {$event_id}\nName: {$customer_name}\nEvent: {$event_name}\nDate: {$event_date}";
    
    // Generate QR code using Google Charts API
    $qrCodeImage = generateQRCode($qr_data);
    
    // Store data in session for PDF generation (include event_id)
    $_SESSION['ticket_data'] = [
        'customer_name' => $customer_name,
        'customer_email' => $customer_email,
        'customer_phone' => $customer_phone,
        'event_name' => $event_name,
        'event_date' => $event_date,
        'event_location' => $event_location,
        'ticket_type' => $ticket_type,
        'ticket_price' => $ticket_price,
        'seat_number' => $seat_number,
        'ticket_id' => $ticket_id,
        'event_id' => $event_id,
        'qr_code' => $qrCodeImage
    ];
    
    // Generate PDF ticket
    if (isset($_POST['generate_pdf'])) {
        generatePDFTicket($_SESSION['ticket_data']);
        exit;
    }
}

function generateTicketHTML($data, $isPDF = false) {
    $qr_src = $data['qr_code'] ?? '';
    $event_id_display = htmlspecialchars($data['event_id'] ?? '');

    return '
    <div class="ticket-container">
        <div class="ticket">
            <div class="ticket-header">
                <div class="event-name">' . htmlspecialchars($data['event_name'] ?? '') . '</div>
                <div class="event-details">' . htmlspecialchars($data['event_date'] ?? '') . ' | ' . htmlspecialchars($data['event_location'] ?? '') . '</div>
                <div style="font-size:12px;opacity:0.9;margin-top:6px;">Event ID: ' . $event_id_display . '</div>
            </div>
            
            <div class="ticket-body">
                <div class="customer-info">
                    <div class="info-group">
                        <span class="info-label">Customer Name:</span>
                        <span class="info-value">' . htmlspecialchars($data['customer_name'] ?? '') . '</span>
                    </div>
                    <div class="info-group">
                        <span class="info-label">Email:</span>
                        <span class="info-value">' . htmlspecialchars($data['customer_email'] ?? '') . '</span>
                    </div>
                    <div class="info-group">
                        <span class="info-label">Phone:</span>
                        <span class="info-value">' . htmlspecialchars($data['customer_phone'] ?? '') . '</span>
                    </div>
                    <div class="info-group">
                        <span class="info-label">Ticket Type:</span>
                        <span class="info-value">' . htmlspecialchars($data['ticket_type'] ?? '') . '</span>
                    </div>
                    <div class="info-group">
                        <span class="info-label">Seat Number:</span>
                        <span class="info-value">' . htmlspecialchars($data['seat_number'] ?? '') . '</span>
                    </div>
                    <div class="info-group">
                        <span class="info-label">Price:</span>
                        <span class="info-value">' . htmlspecialchars($data['ticket_price'] ?? '') . '</span>
                    </div>
                </div>
                
                <div class="qr-section">
                    <div style="font-weight: 600; margin-bottom: 10px; color: #6b7280;">Scan QR Code for Entry</div>
                    <img class="qr-code" src="' . $qr_src . '" alt="QR Code" width="200" height="200">
                </div>
                
                <div class="ticket-id">' . htmlspecialchars($data['ticket_id'] ?? '') . '</div>
            </div>
            
            <div class="ticket-footer">
                ' . (!$isPDF ? '
                <form method="POST" style="display:inline;">
                    <input type="hidden" name="customer_name" value="' . htmlspecialchars($data['customer_name'] ?? '') . '">
                    <input type="hidden" name="customer_email" value="' . htmlspecialchars($data['customer_email'] ?? '') . '">
                    <input type="hidden" name="customer_phone" value="' . htmlspecialchars($data['customer_phone'] ?? '') . '">
                    <input type="hidden" name="event_name" value="' . htmlspecialchars($data['event_name'] ?? '') . '">
                    <input type="hidden" name="event_date" value="' . htmlspecialchars($data['event_date'] ?? '') . '">
                    <input type="hidden" name="event_location" value="' . htmlspecialchars($data['event_location'] ?? '') . '">
                    <input type="hidden" name="ticket_type" value="' . htmlspecialchars($data['ticket_type'] ?? '') . '">
                    <input type="hidden" name="ticket_price" value="' . htmlspecialchars($data['ticket_price'] ?? '') . '">
                    <input type="hidden" name="seat_number" value="' . htmlspecialchars($data['seat_number'] ?? '') . '">
                    <input type="hidden" name="event_id" value="' . $event_id_display . '">
                    <button type="submit" name="generate_pdf" class="download-btn">
                        <i class="fas fa-download"></i> Download PDF Ticket
                    </button>
                </form>
                ' : '') . '
                <div class="terms">
                    • This ticket must be presented for entry<br>
                    • Not transferable without authorization<br>
                    • Subject to event terms and conditions
                </div>
            </div>
        </div>
    </div>';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Ticket Generator</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 40px 20px;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            align-items: start;
        }
        @media (max-width: 768px) {
            .container { grid-template-columns: 1fr; }
        }
        .form-section, .preview-section {
            background: white;
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
        }
        .preview-section { position: sticky; top: 20px; }
        h1 { text-align: center; color: white; margin-bottom: 40px; font-size: 2.5em; font-weight: 700; }
        h2 { color: #1f2937; margin-bottom: 30px; font-size: 1.8em; font-weight: 600; }
        .form-group { margin-bottom: 25px; }
        label { display: block; margin-bottom: 8px; font-weight: 500; color: #374151; }
        input, select {
            width: 100%;
            padding: 15px;
            border: 2px solid #e5e7eb;
            border-radius: 10px;
            font-size: 16px;
            transition: border-color 0.3s;
        }
        input:focus, select:focus { outline: none; border-color: #a78bfa; }
        .submit-btn {
            background: linear-gradient(135deg, #a78bfa, #f0abfc);
            color: white;
            border: none;
            padding: 18px 40px;
            border-radius: 10px;
            font-size: 18px;
            font-weight: 600;
            cursor: pointer;
            width: 100%;
            transition: transform 0.2s;
        }
        .submit-btn:hover { transform: translateY(-2px); }
        .preview-placeholder {
            text-align: center;
            color: #6b7280;
            padding: 60px 20px;
            border: 2px dashed #d1d5db;
            border-radius: 10px;
            background: #f9fafb;
        }
        .preview-placeholder i { font-size: 48px; margin-bottom: 20px; color: #9ca3af; }
        
        /* Ticket Styles */
        .ticket-container { max-width: 400px; width: 100%; }
        .ticket { background: white; border-radius: 20px; overflow: hidden; box-shadow: 0 20px 40px rgba(0,0,0,0.1); }
        .ticket-header { background: linear-gradient(135deg, #a78bfa, #f0abfc); color: white; padding: 30px 20px; text-align: center; }
        .event-name { font-size: 24px; font-weight: bold; margin-bottom: 5px; }
        .event-details { font-size: 14px; opacity: 0.9; }
        .ticket-body { padding: 30px 20px; }
        .customer-info { margin-bottom: 25px; }
        .info-group { margin-bottom: 15px; display: flex; justify-content: space-between; border-bottom: 1px dashed #e5e7eb; padding-bottom: 10px; }
        .info-label { font-weight: 600; color: #6b7280; }
        .info-value { font-weight: 500; color: #1f2937; }
        .qr-section { text-align: center; margin: 25px 0; }
        .qr-code { width: 200px; height: 200px; margin: 0 auto; border: 2px solid #e5e7eb; border-radius: 10px; padding: 10px; background: white; }
        .ticket-id { text-align: center; background: #f8fafc; padding: 15px; border-radius: 10px; margin-top: 20px; font-family: monospace; font-weight: bold; color: #a78bfa; }
        .ticket-footer { background: #f8fafc; padding: 20px; text-align: center; border-top: 1px dashed #e5e7eb; }
        .terms { font-size: 12px; color: #6b7280; margin-top: 10px; }
        .download-btn {
            background: linear-gradient(135deg, #a78bfa, #f0abfc);
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            margin-bottom: 15px;
        }
    </style>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <h1><i class="fas fa-ticket-alt"></i> Event Ticket Generator</h1>
    
    <div class="container">
        <div class="form-section">
            <h2>Generate Your Ticket</h2>
            <form method="POST" id="ticketForm">
                <div class="form-group">
                    <label for="customer_name"><i class="fas fa-user"></i> Customer Name</label>
                    <input type="text" id="customer_name" name="customer_name" value="<?php echo htmlspecialchars($customer_name ?? ''); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="customer_email"><i class="fas fa-envelope"></i> Email Address</label>
                    <input type="email" id="customer_email" name="customer_email" value="<?php echo htmlspecialchars($customer_email ?? ''); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="customer_phone"><i class="fas fa-phone"></i> Phone Number</label>
                    <input type="tel" id="customer_phone" name="customer_phone" value="<?php echo htmlspecialchars($customer_phone ?? ''); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="event_id"><i class="fas fa-hashtag"></i> Event ID</label>
                    <input type="text" id="event_id" name="event_id" value="<?php echo htmlspecialchars($event_id); ?>" required>
                </div>

                <div class="form-group">
                    <label for="event_name"><i class="fas fa-calendar"></i> Event Name</label>
                    <input type="text" id="event_name" name="event_name" value="<?php echo htmlspecialchars($event_name ?? 'Summer Music Festival'); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="event_date"><i class="fas fa-clock"></i> Event Date</label>
                    <input type="date" id="event_date" name="event_date" value="<?php echo htmlspecialchars($event_date ?? '2024-08-15'); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="event_location"><i class="fas fa-map-marker-alt"></i> Event Location</label>
                    <input type="text" id="event_location" name="event_location" value="<?php echo htmlspecialchars($event_location ?? 'Central Park'); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="ticket_type"><i class="fas fa-tag"></i> Ticket Type</label>
                    <select id="ticket_type" name="ticket_type">
                        <option value="General Admission" <?php echo ($ticket_type ?? '') === 'General Admission' ? 'selected' : ''; ?>>General Admission</option>
                        <option value="VIP" <?php echo ($ticket_type ?? '') === 'VIP' ? 'selected' : ''; ?>>VIP</option>
                        <option value="Premium" <?php echo ($ticket_type ?? '') === 'Premium' ? 'selected' : ''; ?>>Premium</option>
                        <option value="Student" <?php echo ($ticket_type ?? '') === 'Student' ? 'selected' : ''; ?>>Student</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="ticket_price"><i class="fas fa-dollar-sign"></i> Ticket Price</label>
                    <input type="text" id="ticket_price" name="ticket_price" value="<?php echo htmlspecialchars($ticket_price ?? '$75.00'); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="seat_number"><i class="fas fa-chair"></i> Seat Number</label>
                    <input type="text" id="seat_number" name="seat_number" value="<?php echo htmlspecialchars($seat_number ?? 'GA-' . rand(1000, 9999)); ?>" required>
                </div>
                
                <button type="submit" class="submit-btn">
                    <i class="fas fa-qrcode"></i> Generate Ticket
                </button>
            </form>
        </div>
        
        <div class="preview-section">
            <h2>Ticket Preview</h2>
            <?php if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['generate_pdf'])): ?>
                <?php echo generateTicketHTML($_SESSION['ticket_data']); ?>
            <?php else: ?>
                <div class="preview-placeholder">
                    <i class="fas fa-ticket-alt"></i>
                    <h3>No Ticket Generated Yet</h3>
                    <p>Fill out the form and click "Generate Ticket" to see your ticket preview</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        document.getElementById('ticket_type').addEventListener('change', function() {
            const ticketType = this.value;
            const seatPrefix = ticketType === 'VIP' ? 'VIP-' : 
                              ticketType === 'Premium' ? 'PRE-' : 
                              ticketType === 'Student' ? 'STU-' : 'GA-';
            document.getElementById('seat_number').value = seatPrefix + Math.floor(1000 + Math.random() * 9000);
            
            const prices = {
                'General Admission': '$75.00',
                'VIP': '$150.00',
                'Premium': '$200.00',
                'Student': '$45.00'
            };
            document.getElementById('ticket_price').value = prices[this.value] || '$75.00';
        });
    </script>
</body>
</html>
