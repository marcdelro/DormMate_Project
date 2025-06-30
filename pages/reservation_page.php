<?php
include __DIR__ . '/../config/units_database.php';
session_start();

$unitsCount = isset($units) && is_array($units) ? count($units) : 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Dorm Unit Reservation</title>
    <link rel="stylesheet" href="../assets/css/reservation_page.css" />
</head>
<body class="reservation-page">
    <div class="reservation-wrapper">
        <div class="right-panel">
            <div class="reservation-container">
                <div class="reservation-header">
                    <h2>Dorm Unit Reservation</h2>
                    <p>Please fill out the form below to reserve a dorm unit.</p>
                    <a href="user_dashboard.php" class="btn btn-back">Go Back</a>
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

<?php
// Fetch user data if logged in
require_once __DIR__ . '/../config/database.php';
$userEmail = '';
$userContact = '';
$userReservations = [];
if (isset($_SESSION['user_id'])) {
    $db = new Database();
    $conn = $db->getConnection();
    $stmt = $conn->prepare("SELECT email, contact_number FROM users WHERE id = :id");
    $stmt->bindParam(':id', $_SESSION['user_id'], PDO::PARAM_INT);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($user) {
        $userEmail = htmlspecialchars($user['email']);
        $userContact = htmlspecialchars($user['contact_number']);
    }

    // Fetch user's reservations
    $resStmt = $conn->prepare("SELECT id, unit_id FROM reservations WHERE user_id = :user_id");
    $resStmt->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
    $resStmt->execute();
    $userReservations = $resStmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<?php
// Separate reserved units with cancel buttons outside the main reservation form
$reservedUnitsHtml = '';
$availableUnitsHtml = '';
if ($unitsCount > 0) {
    $count = 0;
    foreach ($units as $unit) {
        $unitId = htmlspecialchars($unit['id']);
        $unitType = htmlspecialchars($unit['unit_type']);
        $price = htmlspecialchars($unit['price']);
        $description = htmlspecialchars($unit['description']);
        $photoPath = htmlspecialchars($unit['photo_path']);
        $size = htmlspecialchars($unit['size']);

        // Check if unit is reserved by current user
        $userReservation = null;
        foreach ($userReservations as $res) {
            if ($res['unit_id'] == $unitId) {
                $userReservation = $res;
                break;
            }
        }

        if ($userReservation) {
            $reservedUnitsHtml .= "<div class='unit-card reserved' data-unit-id='$unitId' style='flex:1;'>
                <img src='../$photoPath' alt='$unitType' class='unit-photo' />
                <div class='unit-info'>
                    <h3>$unitType</h3>
                    <p class='price'>Price: ₱$price</p>
                    <p class='size'>Size: $size sqm</p>
                    <p class='description'>$description</p>
                    <p class='status'>Reserved by you</p>
                    <form action='cancel_reservation.php' method='POST' onsubmit='return confirm(\"Are you sure you want to cancel this reservation?\");'>
                        <input type='hidden' name='reservation_id' value='" . htmlspecialchars($userReservation['id']) . "' />
                        <button type='submit' class='btn cancel-btn'>Cancel Reservation</button>
                    </form>
                </div>
            </div>";
        } else if ($unit['is_reserved']) {
            $availableUnitsHtml .= "<div class='unit-card reserved' data-unit-id='$unitId' style='flex:1;'>
                <img src='../$photoPath' alt='$unitType' class='unit-photo' />
                <div class='unit-info'>
                    <h3>$unitType</h3>
                    <p class='price'>Price: ₱$price</p>
                    <p class='size'>Size: $size sqm</p>
                    <p class='description'>$description</p>
                    <p class='status'>Reserved</p>
                </div>
            </div>";
        } else {
            $availableUnitsHtml .= "<div class='unit-card' data-unit-id='$unitId' style='flex:1;'>
                <img src='../$photoPath' alt='$unitType' class='unit-photo' />
                <div class='unit-info'>
                    <h3>$unitType</h3>
                    <p class='price'>Price: ₱$price</p>
                    <p class='size'>Size: $size sqm</p>
                    <p class='description'>$description</p>
                    <p class='status'>Available</p>
                </div>
            </div>";
        }
        $count++;
    }
} else {
    $availableUnitsHtml = '<p>No units available</p>';
}
?>

<form class="reservation-form" action="process_reservation.php" method="POST" enctype="multipart/form-data">
    <h3 class="available-units-caption" style="margin-bottom: 15px;">Dorm Units</h3>
    <div class="units-display available-units" style="margin-bottom: 30px;">
        <?php
        // Display available and reserved by others units inside the main reservation form
        echo $availableUnitsHtml;
        ?>
    </div>
    <input type="hidden" name="unit" id="selected-unit" required />
    <div class="form-group email-group">
        <label for="email">Email</label>
        <input type="email" name="email" id="email" value="<?php echo $userEmail; ?>" required />
    </div>
    <div class="form-group contact-group">
        <label for="contact_number">Contact Number</label>
        <input type="text" name="contact_number" id="contact_number" value="<?php echo $userContact; ?>" required />
    </div>
    <div class="form-group">
        <label for="valid_id">Upload Valid ID (optional)</label>
        <input type="file" name="valid_id" id="valid_id" accept="image/*,application/pdf" />
    </div>
    <button type="submit" class="btn" id="confirm-btn" disabled>Confirm Reservation</button>
</form>

<?php
// Display reserved units with cancel buttons outside the main reservation form
echo "<h3 class='reserved-units-caption' style='margin-top: 30px; margin-bottom: 15px;'>Your Reserved Unit/s</h3>";
echo "<div class='units-display reserved-units'>";
echo $reservedUnitsHtml;
echo "</div>";
?>
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
                console.log('Deselected unit, confirm disabled');
            } else {
                // Remove selection from all cards
                unitCards.forEach(c => c.classList.remove('selected'));
                // Add selection to clicked card
                card.classList.add('selected');
                // Set hidden input value
                selectedUnitInput.value = card.getAttribute('data-unit-id');
                // Enable confirm button
                confirmBtn.disabled = false;
                console.log('Selected unit:', selectedUnitInput.value, 'confirm enabled');
            }
        });
    });

    // Add form submit event listener to log submission and selected unit
    const reservationForm = document.querySelector('.reservation-form');
    if (reservationForm) {
        reservationForm.addEventListener('submit', (e) => {
            const confirmed = confirm('Are you sure you want to reserve this unit?');
            if (!confirmed) {
                e.preventDefault();
                return false;
            }
            console.log('Form submitted with unit:', selectedUnitInput.value);
        });
    }
</script>

</body>
</html>
