<?php
require_once __DIR__ . '/database.php';

$db = new Database();
$conn = $db->getConnection();

try {
    $stmt = $conn->prepare("SELECT id, unit_type, price, is_reserved, description, photo_path, size FROM units WHERE is_reserved = 0");
    $stmt->execute();
    $units = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error fetching units: " . $e->getMessage();
    $units = [];
}
?>
