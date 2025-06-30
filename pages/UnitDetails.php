<?php
include 'DBConnector.php';

$unit_id = $_GET['id'] ?? null;

if (!$unit_id) {
    echo "<p>Invalid Unit ID.</p>";
    exit;
}

$sql = "SELECT * FROM units WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $unit_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "<p>Unit not found.</p>";
    exit;
}

$unit = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html>
<head>
    <title><?= htmlspecialchars($unit['unit_type']) ?> - DormMate</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="Units_Details.css"> <!-- Linked external CSS -->
</head>
<body>

    <div class="header">
        <h1>Room Details</h1>
    </div>

    <div class="detail-card">
        <img src="<?= htmlspecialchars($unit['photo_path']) ?>" alt="Room Image" class="detail-image">

        <div class="detail-info">
            <h2><?= htmlspecialchars($unit['unit_type']) ?></h2>
            <span class="status-badge <?= $unit['is_reserved'] ? 'occupied' : 'available' ?>">
                <?= $unit['is_reserved'] ? 'OCCUPIED' : 'AVAILABLE' ?>
            </span>

            <p><strong>Price:</strong> ₱<?= number_format($unit['price'], 2) ?></p>
            <p><strong>Size:</strong> <?= htmlspecialchars($unit['size']) ?> sqm</p>
            <p><strong>Description:</strong> <?= htmlspecialchars($unit['description']) ?></p>
        </div>

        <div class="buttons">
            <?php if (!$unit['is_reserved']): ?>
                <a href="reservation_page.php?unit_id=<?= $unit['id'] ?>">
                    <button class="btn btn-primary">Reserve Now</button>
                </a>
            <?php else: ?>
                <button class="btn btn-primary" disabled>Already Reserved</button>
            <?php endif; ?>
            <a href="ViewUnits.php"><button class="btn btn-secondary">Back</button></a>
        </div>
    </div>

</body>
</html>
