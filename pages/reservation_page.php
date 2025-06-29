<?php
include __DIR__ . '/../config/units_database.php';
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Dorm Unit Reservation</title>
    <link rel="stylesheet" href="../assets/css/style.css" />
    <link rel="stylesheet" href="../assets/css/reservation_page.css" />
</head>
<body class="reservation-page">
    <div class="reservation-wrapper">
        <div class="left-panel">
            <h1>DormMate</h1>
            <p>Find Your Perfect Home Away From Home</p>
            <ul class="features-list">
                <li>✔ Verified Dormitory Listings</li>
                <li>✔ Secure Application Process</li>
                <li>✔ 24/7 Support System</li>
                <li>✔ Student Community Platform</li>
            </ul>
        </div>
        <div class="right-panel">
            <div class="reservation-container">
                <div class="reservation-header">
                    <h2>Dorm Unit Reservation</h2>
                    <p>Please fill out the form below to reserve a dorm unit.</p>
                </div>
                <div style="height: 20px;"></div>

                <?php if (isset($_SESSION['success'])): ?>
                    <div class="message success"><?php echo htmlspecialchars($_SESSION['success']); ?></div>
                    <?php unset($_SESSION['success']); ?>
                <?php endif; ?>

                <?php if (isset($_SESSION['error'])): ?>
                    <div class="message error"><?php echo htmlspecialchars($_SESSION['error']); ?></div>
                    <?php unset($_SESSION['error']); ?>
                <?php endif; ?>

                <form class="reservation-form" action="process_reservation.php" method="POST" enctype="multipart/form-data">
                    <div class="units-display">
                        <?php
                        if (isset($units) && is_array($units)) {
                            foreach ($units as $unit) {
                                if ($unit['is_reserved']) {
                                    $reservedClass = 'reserved';
                                    $reservedText = 'Reserved';
                                } else {
                                    $reservedClass = '';
                                    $reservedText = 'Available';
                                }
                                $unitId = htmlspecialchars($unit['id']);
                                $unitType = htmlspecialchars($unit['unit_type']);
                                $price = htmlspecialchars($unit['price']);
                                $description = htmlspecialchars($unit['description']);
                                $photoPath = htmlspecialchars($unit['photo_path']);
                                $size = htmlspecialchars($unit['size']);
                                echo "<div class='unit-card $reservedClass' data-unit-id='$unitId'>
                                        <img src='../$photoPath' alt='$unitType' class='unit-photo' />
                                        <div class='unit-info'>
                                            <h3>$unitType</h3>
                                            <p class='price'>Price: ₱$price</p>
                                            <p class='size'>Size: $size sqm</p>
                                            <p class='description'>$description</p>
                                            <p class='status'>$reservedText</p>
                                        </div>
                                    </div>";
                            }
                        } else {
                            echo '<p>No units available</p>';
                        }
                        ?>
                    </div>
                    <input type="hidden" name="unit" id="selected-unit" required />
                    <div class="form-group">
                        <label for="valid_id">Upload Valid ID (optional)</label>
                        <input type="file" name="valid_id" id="valid_id" accept="image/*,application/pdf" />
                    </div>
                    <button type="submit" class="btn" id="confirm-btn" disabled>Confirm Reservation</button>
                </form>
            </div>
        </div>
    </div>

<script>
    const unitCards = document.querySelectorAll('.unit-card:not(.reserved)');
    const selectedUnitInput = document.getElementById('selected-unit');
    const confirmBtn = document.getElementById('confirm-btn');

    unitCards.forEach(card => {
        card.addEventListener('click', () => {
            if (card.classList.contains('selected')) {
                // Deselect if already selected
                card.classList.remove('selected');
                selectedUnitInput.value = '';
                confirmBtn.disabled = true;
            } else {
                // Remove selection from all cards
                unitCards.forEach(c => c.classList.remove('selected'));
                // Add selection to clicked card
                card.classList.add('selected');
                // Set hidden input value
                selectedUnitInput.value = card.getAttribute('data-unit-id');
                // Enable confirm button
                confirmBtn.disabled = false;
            }
        });
    });
</script>

</body>
</html>
