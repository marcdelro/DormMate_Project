<?php
session_start();
require_once __DIR__ . '/../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = $_SESSION['user_id'] ?? null;
    $reservationId = $_POST['reservation_id'] ?? null;

    if (!$userId) {
        $_SESSION['error'] = "You must be logged in to cancel a reservation.";
        header("Location: reservation_page.php");
        exit;
    }

    if (!$reservationId) {
        $_SESSION['error'] = "Invalid reservation.";
        header("Location: reservation_page.php");
        exit;
    }

    $db = new Database();
    $conn = $db->getConnection();

    try {
        // Get the reservation to find the unit_id and valid_id_path
        $stmt = $conn->prepare("SELECT unit_id, valid_id_path FROM reservations WHERE id = :reservation_id AND user_id = :user_id");
        $stmt->bindParam(':reservation_id', $reservationId, PDO::PARAM_INT);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        $reservation = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$reservation) {
            $_SESSION['error'] = "Reservation not found or you do not have permission to cancel it.";
            header("Location: reservation_page.php");
            exit;
        }

        $unitId = $reservation['unit_id'];
        $validIdPath = $reservation['valid_id_path'];

        // Delete the valid ID file if it exists
        if ($validIdPath && file_exists(__DIR__ . '/../' . $validIdPath)) {
            unlink(__DIR__ . '/../' . $validIdPath);
        }

        // Delete the reservation
        $deleteStmt = $conn->prepare("DELETE FROM reservations WHERE id = :reservation_id");
        $deleteStmt->bindParam(':reservation_id', $reservationId, PDO::PARAM_INT);
        $deleteStmt->execute();

        // Update the unit to not reserved
        $updateStmt = $conn->prepare("UPDATE units SET is_reserved = 0 WHERE id = :unit_id");
        $updateStmt->bindParam(':unit_id', $unitId, PDO::PARAM_INT);
        $updateStmt->execute();

        $_SESSION['success'] = "Your reservation has been canceled successfully.";
        header("Location: reservation_page.php");
        exit;
    } catch (PDOException $e) {
        $_SESSION['error'] = "Error canceling reservation: " . $e->getMessage();
        header("Location: reservation_page.php");
        exit;
    }
} else {
    header("Location: reservation_page.php");
    exit;
}
?>
