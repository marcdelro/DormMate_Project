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
    <link rel="stylesheet" href="../assets/css/dashboard.css">
    <link rel="stylesheet" href="../assets/css/animations.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>

<!-- Navigation Bar -->
<div class="top-navbar">
    <div class="navbar-left">
        <h1 class="logo">DormMate</h1>
        <span class="user-badge">👤 USER</span>
    </div>
    <div class="navbar-center">
        <div class="nav-links">
            <a href="#" class="nav-link active">Dashboard</a>
            <a href="#" class="nav-link">View Units</a>
            <a href="#" class="nav-link">Reservations</a>
            <a href="#" class="nav-link">My Bookings</a>
        </div>
    </div>
    <div class="navbar-right">
        <div class="user-greeting">
            <span class="greeting-text">Hello, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</span>
        </div>
        <a href="logout.php" onclick="return confirmLogout()" class="logout-btn">Logout</a>
    </div>
</div>


<div class="unit-controls">
    <h3>🔍 Find Your Perfect Room</h3>
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
try {
    include '../config/DBConnector.php';

    $sql = "SELECT * FROM units";
    $result = $conn->query($sql);

    if (!$result) {
        echo "<div style='background: #f8d7da; color: #721c24; padding: 20px; border-radius: 8px; margin: 20px; text-align: center;'>";
        echo "<h3>⚠️ Database Error</h3>";
        echo "<p>Error: " . $conn->error . "</p>";
        echo "<p><a href='../setup_units.php' style='color: #721c24;'>Click here to setup units table</a></p>";
        echo "</div>";
    } else if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()):
            $isReserved = $row['is_reserved'];
            $statusClass = $isReserved ? "occupied" : "available";
            $statusText = $isReserved ? "OCCUPIED" : "AVAILABLE";
?>
    <div class="unit-card <?= $statusClass ?>">
        <img class="unit-image" src="../<?= htmlspecialchars($row['photo_path']) ?>" alt="Unit Photo" 
             onerror="this.src='../assets/images/placeholder.jpg'">
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
    } else {
        echo "<div style='background: #fff3cd; color: #856404; padding: 20px; border-radius: 8px; margin: 20px; text-align: center;'>";
        echo "<h3>📦 No Units Available</h3>";
        echo "<p>No dormitory units found in the database.</p>";
        echo "<p><a href='../setup_units.php' style='color: #856404;'>Click here to add sample units</a></p>";
        echo "</div>";
    }
    
    $conn->close();
} catch (Exception $e) {
    echo "<div style='background: #f8d7da; color: #721c24; padding: 20px; border-radius: 8px; margin: 20px; text-align: center;'>";
    echo "<h3>⚠️ Connection Error</h3>";
    echo "<p>Could not connect to database: " . $e->getMessage() . "</p>";
    echo "<p><a href='../setup_units.php' style='color: #721c24;'>Click here to setup database</a></p>";
    echo "</div>";
}
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
