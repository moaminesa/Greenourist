<?php
session_start();
require_once __DIR__ . '/../includes/config.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);

try {
    $pdo = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME, DB_USER, DB_PASS);
    $stmt = $pdo->prepare("INSERT INTO bookings (user_id, activity_id, booking_date, participants, status) VALUES (?, ?, ?, ?, 'confirmed')");
    $success = $stmt->execute([
        $_SESSION['user_id'],
        $data['activity_id'],
        $data['booking_date'],
        $data['participants']
    ]);
    
    if ($success) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Booking failed']);
    }
} catch(PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>