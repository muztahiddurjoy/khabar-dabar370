<?php
require_once('connect.php');
session_start();

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Please login to book items']);
    exit();
}

$user_id = $_SESSION['user_id'];
$cart_data = json_decode($_POST['cart_data'], true);
$booking_date = $_POST['booking_date'];
$booking_time = $_POST['booking_time'];
$special_instructions = $_POST['special_instructions'] ?? '';
$booking_token = rand(1000, 9999);

if (empty($cart_data)) {
    echo json_encode(['success' => false, 'message' => 'Cart is empty']);
    exit();
}

try {
    // Start transaction
    $conn->begin_transaction();
    
    foreach ($cart_data as $item) {
        $item_id = $item['id'];
        $quantity = $item['quantity'];
        
        $stmt = $conn->prepare("INSERT INTO advance_booking (user_id, food_item_id, quantity, booking_date, booking_time, special_instructions, booking_token) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("iiisssi", $user_id, $item_id, $quantity, $booking_date, $booking_time, $special_instructions, $booking_token);
        $stmt->execute();
        $stmt->close();
    }
    
    // Commit transaction
    $conn->commit();
    
    echo json_encode([
        'success' => true,
        'message' => "Booking confirmed for ".date('F j, Y', strtotime($booking_date))." at ".date('g:i A', strtotime($booking_time))."! Your reservation #".$booking_token." is ready."
    ]);
    
} catch (Exception $e) {
    // Rollback transaction on error
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => 'Booking failed: ' . $e->getMessage()]);
}
?>