<!--for example lang

<?php
session_start();
session_destroy();

$basePath = dirname($_SERVER['PHP_SELF'], 2); // go 2 folders up (from /pages/logout.php)
header("Location: $basePath/index.php");
exit();