<!--for checking if the user is logged in and has the correct role-->

<?php
ob_start(); // Start output buffering
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'user') {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <!-- Link main CSS -->
    <link rel="stylesheet" href="../assets/css/Dashboard.css">
</head>
<body>

<!-- Navigation Bar -->
<div class="top-navbar">
    <h2 class="logo">📦 DormMate</h2>
   <div class="nav-links">
    <a href="ViewUnits.php" class="active">🏠 Dashboard</a>
    <a href="#">🛏️ View Units</a>
    <a href="#">📝 Make Reservation</a>
    <a href="#">📂 Manage Bookings</a>
    <a href="about.php">ℹ️ About Us</a>
    <a href="logout.php" onclick="return confirmLogout()">🚪 Logout</a>
</div>
</div>


<div class="unit-controls">
    <select id="filterType">
        <option value="all">All Types</option>
        <option value="Single Room">Single Room</option>
        <option value="Double Room">Double Room</option>
        <option value="Studio Unit">Studio Unit</option>
    </select>
    <label class="available-toggle">
        <input type="checkbox" id="showAvailableOnly">
        Show Available Only
    </label>
    <button id="resetFilters">🔄 Reset Filters</button>
</div>




<!-- Scrollable Unit Cards -->
<div class="unit-container">
<?php
include '../config/DBConnector.php';

$sql = "SELECT * FROM units";
$result = $conn->query($sql);

if ($result->num_rows > 0):
    while ($row = $result->fetch_assoc()):
        $isReserved = $row['is_reserved'];
        $statusClass = $isReserved ? "occupied" : "available";
        $statusText = $isReserved ? "OCCUPIED" : "AVAILABLE";
?>
    <div class="unit-card <?= $statusClass ?>">
        <img class="unit-image" src="<?= htmlspecialchars($row['photo_path']) ?>" alt="Unit Photo">
        <div class="unit-details">
            <h2><?= htmlspecialchars($row['unit_type']) ?></h2>
            <span class="status"><?= $statusText ?></span>
            <p><strong>Price:</strong> ₱<?= number_format($row['price'], 2) ?></p>
            <p><strong>Size:</strong> <?= htmlspecialchars($row['size']) ?> sqm</p>
            <p><?= htmlspecialchars($row['description']) ?></p>
            <?php if (!$isReserved): ?>
                <a href="UnitDetails.php?id=<?= $row['id'] ?>">
                    <button class="reserve-btn">View Details</button>
                </a>
            <?php else: ?>
                <button class="reserve-btn" disabled>Not Available</button>
            <?php endif; ?>
        </div>
    </div>
<?php
    endwhile;
else:
    echo "<p style='margin-left:20px;'>No units found.</p>";
endif;

$conn->close();
?>
</div> <!-- end unit-container -->

<script>
    const filterType = document.getElementById('filterType');
    const showAvailableOnly = document.getElementById('showAvailableOnly');
    const unitCards = Array.from(document.querySelectorAll('.unit-card'));

    function updateUnits() {
        const selectedType = filterType.value;
        const onlyAvailable = showAvailableOnly.checked;

        let filtered = [...unitCards];

        // Filter by unit type
        if (selectedType !== 'all') {
            filtered = filtered.filter(card =>
                card.querySelector('h2').innerText === selectedType
            );
        }

        // Filter by availability
        if (onlyAvailable) {
            filtered = filtered.filter(card =>
                card.classList.contains('available')
            );
        }

        // Hide all first
        unitCards.forEach(card => card.style.display = 'none');

        // Show filtered cards
        filtered.forEach(card => card.style.display = 'flex');
    }

    // Event listeners
    filterType.addEventListener('change', updateUnits);
    showAvailableOnly.addEventListener('change', updateUnits);

    // ✅ Reset filter logic
    document.getElementById('resetFilters').addEventListener('click', () => {
        filterType.value = 'all';
        showAvailableOnly.checked = false;
        updateUnits();
    });
    
    // Logout confirmation
    function confirmLogout() {
        return confirm('Are you sure you want to logout?');
    }
</script>




</body>

</html>
