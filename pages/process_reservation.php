<?php
session_start();
require_once __DIR__ . '/../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = $_SESSION['user_id'] ?? null; // Assuming user is logged in and user_id is stored in session
    $unitId = $_POST['unit'] ?? null;
    $email = $_POST['email'] ?? '';
    $contactNumber = $_POST['contact_number'] ?? '';

    if (!$userId) {
        $_SESSION['error'] = "You must be logged in to make a reservation.";
        header("Location: reservation_page.php");
        exit;
    }

    if (!$unitId) {
        $_SESSION['error'] = "Please select a unit.";
        header("Location: reservation_page.php");
        exit;
    }

    $db = new Database();
    $conn = $db->getConnection();

    // Validate email and contact number against logged-in user's data
    $userStmt = $conn->prepare("SELECT email, contact_number FROM users WHERE id = :id");
    $userStmt->bindParam(':id', $userId, PDO::PARAM_INT);
    $userStmt->execute();
    $user = $userStmt->fetch(PDO::FETCH_ASSOC);

    if (!$user || $user['email'] !== $email || $user['contact_number'] !== $contactNumber) {
        $_SESSION['error'] = "Email and contact number do not match our records.";
        header("Location: reservation_page.php");
        exit;
    }

    $validIdPath = null;
    if (isset($_FILES['valid_id']) && $_FILES['valid_id']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = __DIR__ . '/../uploads/valid_ids/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        $fileTmpPath = $_FILES['valid_id']['tmp_name'];
        $fileName = basename($_FILES['valid_id']['name']);
        $fileName = time() . '_' . preg_replace('/[^a-zA-Z0-9_\.-]/', '_', $fileName);
        $destPath = $uploadDir . $fileName;

        if (move_uploaded_file($fileTmpPath, $destPath)) {
            $validIdPath = 'uploads/valid_ids/' . $fileName;
        } else {
            $_SESSION['error'] = "Failed to upload valid ID.";
            header("Location: reservation_page.php");
            exit;
        }
    }

    try {
        $status = 'Pending';
        $createdAt = date('Y-m-d H:i:s');

        $stmt = $conn->prepare("INSERT INTO reservations (user_id, unit_id, valid_id_path, status, reservation_time_and_date) VALUES (:user_id, :unit_id, :valid_id_path, :status, :reservation_time_and_date)");
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindParam(':unit_id', $unitId, PDO::PARAM_INT);
        $stmt->bindParam(':valid_id_path', $validIdPath, PDO::PARAM_STR);
        $stmt->bindParam(':status', $status, PDO::PARAM_STR);
        $stmt->bindParam(':reservation_time_and_date', $createdAt, PDO::PARAM_STR);
        $stmt->execute();

        // Update unit as reserved
        $updateStmt = $conn->prepare("UPDATE units SET is_reserved = 1 WHERE id = :unit_id");
        $updateStmt->bindParam(':unit_id', $unitId, PDO::PARAM_INT);
        $updateStmt->execute();

        // Fetch unit details for confirmation message
        $unitStmt = $conn->prepare("SELECT unit_type, price FROM units WHERE id = :unit_id");
        $unitStmt->bindParam(':unit_id', $unitId, PDO::PARAM_INT);
        $unitStmt->execute();
        $unit = $unitStmt->fetch(PDO::FETCH_ASSOC);

        $unitType = $unit['unit_type'] ?? 'Unit';
        $unitPrice = $unit['price'] ?? 'N/A';

        $_SESSION['success'] = "Your reservation for {$unitType} at price {$unitPrice} has been received! We'll get back to you shortly.";
        header("Location: user_dashboard.php");
        exit;
    } catch (PDOException $e) {
        $_SESSION['error'] = "Error processing reservation: " . $e->getMessage();
        header("Location: reservation_page.php");
        exit;
    }
} else {
    header("Location: reservation_page.php");
    exit;
}
?>
