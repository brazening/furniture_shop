<?php
session_start();
require_once 'connect.php';

if (!isset($_SESSION['id'])) {
    die(json_encode(['error' => 'Not authorized']));
}

$userId = $_SESSION['id'];
$favorites = json_decode($_POST['favorites'] ?? '[]', true);

// Валидация ID товаров
$validFavorites = array_filter($favorites, function($id) {
    return is_numeric($id) && $id > 0;
});

// Обновляем в БД
$stmt = $db->dbs->prepare("UPDATE users SET favorites = ? WHERE id = ?");
$stmt->execute([json_encode($validFavorites), $userId]);

echo json_encode(['success' => true]);