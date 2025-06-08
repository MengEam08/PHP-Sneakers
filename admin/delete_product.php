<?php
@include 'conf.php';

if (isset($_POST['delete_id'])) {
    $id = $_POST['delete_id'];

    try {
        $stmt = $conn->prepare("DELETE FROM product WHERE id = ?");
        $stmt->execute([$id]);
        echo json_encode(['success' => true]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
}
