<?php
require_once 'conf.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    $id = intval($_POST['delete_id']);

    // First, get the image file
    $stmt = $conn->prepare("SELECT image FROM category WHERE id = ?");
    $stmt->execute([$id]);
    $category = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($category) {
        $image_path = 'uploaded_img/' . $category['image'];
        if (file_exists($image_path)) {
            unlink($image_path);
        }

        $delete_stmt = $conn->prepare("DELETE FROM category WHERE id = ?");
        if ($delete_stmt->execute([$id])) {
            echo json_encode(['success' => true]);
            exit;
        }
    }

    echo json_encode(['success' => false, 'message' => 'Category not found or could not be deleted.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
}
