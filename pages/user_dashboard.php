<!--for checking if the user is logged in and has the correct role-->

<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'user') {
    header("Location: ../index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <!-- Link main CSS -->
    <link rel="stylesheet" href="../assets/css/dashboard.css">
    <link rel="stylesheet" href="../assets/css/base.css">
    <link rel="stylesheet" href="../assets/css/animations.css">
</head>
<body class="dashboard">

    <div class="dashboard-header clearfix">
        <h1>
            Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!
            <span class="user-badge">💻 USER</span>
        </h1>
        <a href="logout.php" class="logout-btn">Logout</a>
    </div>

    <div class="dashboard-content<?php echo isset($_SESSION['success']) ? ' blurred' : ''; ?>">
        <h2>User Dashboard</h2>
        <p>This is your user dashboard.</p>
        <!-- Add more features below -->

        <a href="reservation_page.php" class="btn make-reservation-btn">Make a Reservation</a>
        
    </div>

    <?php if (isset($_SESSION['success'])): ?>
    <div id="successModal" class="modal">
        <div class="modal-content">
            <span id="closeModal" class="close">&times;</span>
            <p><?php echo htmlspecialchars($_SESSION['success']); ?></p>
        </div>
    </div>
    <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <style>
        /* Modal container */
        .modal {
            display: block;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.4);
            backdrop-filter: blur(5px);
        }

        /* Modal content box */
        .modal-content {
            background-color: #d4edda;
            color: #155724;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #c3e6cb;
            border-radius: 8px;
            width: 80%;
            max-width: 400px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
            font-weight: 600;
            font-size: 16px;
            text-align: center;
            position: relative;
        }

        /* Close button */
        .close {
            color: #155724;
            position: absolute;
            top: 10px;
            right: 15px;
            font-size: 24px;
            font-weight: bold;
            cursor: pointer;
        }

        /* Blur effect on background */
        .blurred {
            filter: blur(3px);
            pointer-events: none;
            user-select: none;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var modal = document.getElementById('successModal');
            var closeBtn = document.getElementById('closeModal');
            var content = document.querySelector('.dashboard-content');

            closeBtn.onclick = function () {
                modal.style.display = 'none';
                content.classList.remove('blurred');
            };

            window.onclick = function (event) {
                if (event.target == modal) {
                    modal.style.display = 'none';
                    content.classList.remove('blurred');
                }
            };
        });
    </script>

</body>
</html>
