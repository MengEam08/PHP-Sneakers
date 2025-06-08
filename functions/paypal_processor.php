<?php
require_once __DIR__ . '/../admin/conf.php'; // adjust path if needed
header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

if (
    !isset($data['transaction_id']) ||
    !isset($data['status']) ||
    !isset($data['amount']) ||
    !isset($data['currency'])
) {
    echo json_encode(['success' => false, 'error' => 'Missing data']);
    exit;
}

// You can get the sale_id from session, cart, or add logic here
$sale_id = $_SESSION['sale_id'] ?? null;

if (!$sale_id) {
    echo json_encode(['success' => false, 'error' => 'Missing sale ID']);
    exit;
}

try {
    $stmt = $conn->prepare("
        INSERT INTO payment (sale_id, amount, payment_method, payment_status, payment_date)
        VALUES (?, ?, ?, ?, NOW())
    ");

    $stmt->execute([
        $sale_id,
        $data['amount'],
        'PayPal',
        $data['status']
    ]);

    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
