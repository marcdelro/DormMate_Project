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
    <link rel="stylesheet" href="../assets/css/dashboard_copy.css" id="theme-stylesheet">
    <link rel="stylesheet" href="../assets/css/animations.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>

<!-- Theme Toggle Button -->
<button class="theme-toggle" id="themeToggle" title="Toggle Light/Dark Mode">
    <span class="toggle-icon light-icon">D</span>
    <div class="toggle-slider">🌙</div>
    <span class="toggle-icon dark-icon">L</span>
</button>

<!-- Navigation Bar -->
<div class="top-navbar">
    <div class="navbar-left">
        <h1 class="logo">DormMate</h1>
        <span class="user-badge">👤 USER</span>
    </div>
    <div class="navbar-center">
        <div class="nav-links">
            <a href="#" class="nav-link active">View Units</a>
            <a href="#" class="nav-link">Reservations</a>
            <a href="#" class="nav-link">Profile</a>
        </div>
    </div>
    <div class="navbar-right">
        <div class="user-greeting">
            <span class="greeting-text"><?php echo htmlspecialchars($_SESSION['user_name']); ?> </span>
        </div>
        <a href="logout.php" onclick="return confirmLogout()" class="logout-btn">Logout</a>
    </div>
</div>


<!-- Hero Section -->
<div class="hero-section">
    <div class="hero-content">
        <h1 class="hero-title">Hello, <?php echo htmlspecialchars($_SESSION['user_name']); ?> ✨</h1>
        <p class="hero-subtitle">Find Your Perfect Home Away From Home</p>
        <div class="hero-actions">
            <button class="hero-btn primary" onclick="scrollToSection('available')">Browse Units</button>
        </div>
    </div>
    <div class="hero-background"></div>
</div>

<!-- Search and Filters Bar -->
<div class="search-bar">
    <div class="search-container">
        <input type="text" id="searchUnits" placeholder="Search for your ideal room..." class="search-input">
        <div class="filter-controls">
            <select id="filterType" class="filter-select">
                <option value="all">All Types</option>
                <option value="Single Room">Single Room</option>
                <option value="Double Room">Double Room</option>
                <option value="Studio Unit">Studio Unit</option>
            </select>
            <label class="available-toggle">
                <input type="checkbox" id="showAvailableOnly">
                <span class="toggle-text">Available Only</span>
            </label>
            <button id="resetFilters" class="reset-btn">Reset</button>
        </div>
    </div>
</div>

<!-- Content Sections -->
<div class="content-wrapper">
    <!-- Available Units Section -->
    <section id="available" class="content-section">
        <h2 class="section-title">Available Now</h2>
        <div class="units-row">
            <div class="units-container" id="availableUnits"></div>
        </div>
    </section>

    <!-- All Units Section -->
    <section id="all-units" class="content-section">
        <h2 class="section-title">Browse All Units</h2>
        <div class="units-row">
            <div class="units-container" id="allUnits">
                <!-- PHP Generated Units will be inserted here -->
<?php
try {
    include '../config/DBConnector.php';

    $sql = "SELECT * FROM units ORDER BY is_reserved ASC, price DESC";
    $result = $conn->query($sql);

    if (!$result) {
        echo "<div class='error-message'>";
        echo "<div class='error-content'>";
        echo "<h3>⚠️ Database Error</h3>";
        echo "<p>Error: " . $conn->error . "</p>";
        echo "<a href='../setup_units.php' class='error-link'>Setup Units Table</a>";
        echo "</div>";
        echo "</div>";
    } else if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()):
            $isReserved = $row['is_reserved'];
            $statusClass = $isReserved ? "occupied" : "available";
            $statusText = $isReserved ? "OCCUPIED" : "AVAILABLE";
            $priceFormatted = number_format($row['price'], 2);
?>
                <div class="unit-card <?= $statusClass ?>" data-type="<?= htmlspecialchars($row['unit_type']) ?>">
                    <div class="unit-image-container">
                        <img class="unit-image" src="../<?= htmlspecialchars($row['photo_path']) ?>" alt="<?= htmlspecialchars($row['unit_type']) ?>" 
                             onerror="this.src='../assets/images/placeholder.jpg'">
                        <div class="unit-overlay">
                            <div class="unit-status <?= $statusClass ?>"><?= $statusText ?></div>
                        </div>
                    </div>
                    <div class="unit-info">
                        <h3 class="unit-title"><?= htmlspecialchars($row['unit_type']) ?></h3>
                        <p class="unit-price">₱<?= $priceFormatted ?>/month</p>
                        <p class="unit-size"><?= htmlspecialchars($row['size']) ?> sqm</p>
                        <p class="unit-description"><?= htmlspecialchars(substr($row['description'], 0, 80)) ?>...</p>
                        <?php if (!$isReserved): ?>
                            <a href="UnitDetails.php?id=<?= $row['id'] ?>" class="view-details-btn">View Details</a>
                        <?php else: ?>
                            <span class="unavailable-text">Currently Unavailable</span>
                        <?php endif; ?>
                    </div>
                </div>
<?php
        endwhile;
    } else {
        echo "<div class='empty-state'>";
        echo "<div class='empty-content'>";
        echo "<h3>📦 No Units Available</h3>";
        echo "<p>No dormitory units found in the database.</p>";
        echo "<a href='../setup_units.php' class='setup-link'>Add Sample Units</a>";
        echo "</div>";
        echo "</div>";
    }
    
    $conn->close();
} catch (Exception $e) {
    echo "<div class='error-message'>";
    echo "<div class='error-content'>";
    echo "<h3>⚠️ Connection Error</h3>";
    echo "<p>Could not connect to database: " . $e->getMessage() . "</p>";
    echo "<a href='../setup_units.php' class='error-link'>Setup Database</a>";
    echo "</div>";
    echo "</div>";
}
?>
            </div>
        </div>
    </section>
</div>

<!-- Footer - Outside content wrapper -->
<footer class="dashboard-footer">
    <div class="footer-content">
        <div class="footer-section">
            <h4>About DormMate</h4>
            <p>Modern dormitory management system designed for students and administrators. Find your perfect dorm room with ease.</p>
        </div>
        <div class="footer-section">
            <h4>Connect</h4>
            <p>
                <a href="https://github.com/search?q=DormMate&type=repositories" target="_blank" rel="noopener noreferrer" class="github-link">
                    <span class="github-icon">📚</span> View on GitHub
                </a>
            </p>
        </div>
        <div class="footer-section">
            <h4>About This Project</h4>
            <p class="footer-info">
                Created: June 2025<br>
                Built with PHP, MySQL & Modern CSS
            </p>
        </div>
    </div>
    <div class="footer-bottom">
        <p>&copy; 2025 DormMate. All rights reserved. | Built for dormitory management excellence.</p>
    </div>
</footer>

<script>
    // Disney+ Like Interface JavaScript
    
    // Get all unit cards and filter elements
    const filterType = document.getElementById('filterType');
    const showAvailableOnly = document.getElementById('showAvailableOnly');
    const searchInput = document.getElementById('searchUnits');
    const unitCards = Array.from(document.querySelectorAll('.unit-card'));
    
    // Organize units into different sections
    function organizeUnits() {
        const availableContainer = document.getElementById('availableUnits');
        
        // Clear containers
        availableContainer.innerHTML = '';
        
        // Separate units
        const availableUnits = unitCards.filter(card => card.classList.contains('available'));
        
        // Populate available units (all available)
        availableUnits.forEach(card => {
            const clone = card.cloneNode(true);
            clone.classList.add('available-item');
            availableContainer.appendChild(clone);
        });
    }
    
    // Smooth scrolling to sections
    function scrollToSection(sectionId) {
        const section = document.getElementById(sectionId);
        if (section) {
            section.scrollIntoView({ 
                behavior: 'smooth',
                block: 'start'
            });
        }
    }
    
    // Filter and search functionality
    function updateUnits() {
        const selectedType = filterType.value;
        const onlyAvailable = showAvailableOnly.checked;
        const searchTerm = searchInput.value.toLowerCase();
        
        let filtered = [...unitCards];
        
        // Filter by search term
        if (searchTerm) {
            filtered = filtered.filter(card => {
                const title = card.querySelector('.unit-title')?.innerText.toLowerCase() || '';
                const description = card.querySelector('.unit-description')?.innerText.toLowerCase() || '';
                return title.includes(searchTerm) || description.includes(searchTerm);
            });
        }
        
        // Filter by unit type
        if (selectedType !== 'all') {
            filtered = filtered.filter(card => {
                const unitType = card.getAttribute('data-type');
                return unitType === selectedType;
            });
        }
        
        // Filter by availability
        if (onlyAvailable) {
            filtered = filtered.filter(card => card.classList.contains('available'));
        }
        
        // Show/hide cards in all sections
        const allContainers = document.querySelectorAll('.units-container');
        allContainers.forEach(container => {
            const cards = container.querySelectorAll('.unit-card');
            cards.forEach(card => {
                const originalCard = unitCards.find(original => 
                    original.getAttribute('data-type') === card.getAttribute('data-type') &&
                    original.querySelector('.unit-title')?.innerText === card.querySelector('.unit-title')?.innerText
                );
                
                if (originalCard && filtered.includes(originalCard)) {
                    card.style.display = 'block';
                    card.classList.add('fade-in');
                } else {
                    card.style.display = 'none';
                    card.classList.remove('fade-in');
                }
            });
        });
    }
    
    // View unit details function
    function viewUnitDetails(unitId) {
        window.location.href = `UnitDetails.php?id=${unitId}`;
    }
    
    // Event listeners
    filterType.addEventListener('change', updateUnits);
    showAvailableOnly.addEventListener('change', updateUnits);
    searchInput.addEventListener('input', updateUnits);
    
    // Reset filters
    document.getElementById('resetFilters').addEventListener('click', () => {
        filterType.value = 'all';
        showAvailableOnly.checked = false;
        searchInput.value = '';
        updateUnits();
        
        // Reset scroll position
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });
    
    // Horizontal scroll for unit rows
    function setupHorizontalScroll() {
        const unitsContainers = document.querySelectorAll('.units-container');
        
        unitsContainers.forEach(container => {
            let isDown = false;
            let startX;
            let scrollLeft;
            
            container.addEventListener('mousedown', (e) => {
                isDown = true;
                container.classList.add('dragging');
                startX = e.pageX - container.offsetLeft;
                scrollLeft = container.scrollLeft;
            });
            
            container.addEventListener('mouseleave', () => {
                isDown = false;
                container.classList.remove('dragging');
            });
            
            container.addEventListener('mouseup', () => {
                isDown = false;
                container.classList.remove('dragging');
            });
            
            container.addEventListener('mousemove', (e) => {
                if (!isDown) return;
                e.preventDefault();
                const x = e.pageX - container.offsetLeft;
                const walk = (x - startX) * 2;
                container.scrollLeft = scrollLeft - walk;
            });
        });
    }
    
    // Hero background animation
    function animateHeroBackground() {
        const heroBackground = document.querySelector('.hero-background');
        if (heroBackground) {
            setInterval(() => {
                heroBackground.style.backgroundPosition = 
                    `${Math.random() * 100}% ${Math.random() * 100}%`;
            }, 10000);
        }
    }
    
    // Logout confirmation
    function confirmLogout() {
        return confirm('Are you sure you want to logout?');
    }
    
    // Initialize everything when page loads
    document.addEventListener('DOMContentLoaded', function() {
        organizeUnits();
        setupHorizontalScroll();
        animateHeroBackground();
        initializeThemeToggle();
        
        // Add scroll effects
        window.addEventListener('scroll', () => {
            const scrolled = window.pageYOffset;
            const hero = document.querySelector('.hero-section');
            if (hero) {
                hero.style.transform = `translateY(${scrolled * 0.5}px)`;
            }
        });
    });
    
    // Theme Toggle Functionality
    function initializeThemeToggle() {
        const themeToggle = document.getElementById('themeToggle');
        const themeStylesheet = document.getElementById('theme-stylesheet');
        
        // Check for saved theme preference or default to light mode
        const savedTheme = localStorage.getItem('dashboardTheme') || 'light';
        applyTheme(savedTheme);
        
        themeToggle.addEventListener('click', () => {
            const currentTheme = themeStylesheet.getAttribute('href').includes('dashboard_copy.css') ? 'light' : 'dark';
            const newTheme = currentTheme === 'light' ? 'dark' : 'light';
            
            applyTheme(newTheme);
            localStorage.setItem('dashboardTheme', newTheme);
            
            // Add a nice click animation
            themeToggle.style.transform = 'scale(0.9)';
            setTimeout(() => {
                themeToggle.style.transform = '';
            }, 150);
        });
    }
    
    function applyTheme(theme) {
        const themeToggle = document.getElementById('themeToggle');
        const themeStylesheet = document.getElementById('theme-stylesheet');
        const toggleSlider = themeToggle.querySelector('.toggle-slider');
        
        if (theme === 'dark') {
            themeStylesheet.setAttribute('href', '../assets/css/dashboard.css');
            themeToggle.classList.add('dark-mode');
            toggleSlider.innerHTML = '🌙';
            themeToggle.title = 'Switch to Light Mode';
        } else {
            themeStylesheet.setAttribute('href', '../assets/css/dashboard_copy.css');
            themeToggle.classList.remove('dark-mode');
            toggleSlider.innerHTML = '☀️';
            themeToggle.title = 'Switch to Dark Mode';
        }
    }
    
    // Expose functions globally
    window.scrollToSection = scrollToSection;
    window.viewUnitDetails = viewUnitDetails;
    window.confirmLogout = confirmLogout;
</script>

</body>
</html>
