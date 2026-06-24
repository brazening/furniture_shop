<?php
require_once 'connect.php';

header('Content-Type: application/json');

try {
    $ids = $_POST['ids'] ?? [];
    
    if (empty($ids)) {
        echo json_encode(['data' => []]);
        exit;
    }

    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $stmt = $db->dbs->prepare("
        SELECT p.*, c.nam AS category_name 
        FROM products p
        LEFT JOIN categories c ON p.categoryid = c.id 
        WHERE p.id IN ($placeholders)
    ");
    
    $stmt->execute($ids);
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['data' => $products]);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: '.$e->getMessage()]);
}

