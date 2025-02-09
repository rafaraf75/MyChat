<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Sprawdzenie, czy użytkownik jest zalogowany
if (isset($_SESSION['user_name'])) {
    $logged_in_user = $_SESSION['user_name'];
} else {
    $logged_in_user = "Guest";
}
?>

<!-- Nawigacja główna -->
<nav class="navbar navbar-expand-sm bg-dark navbar-dark">
<a class="navbar-brand" href="home.php"><?php echo htmlspecialchars($logged_in_user); ?></a>
    <ul class="navbar-nav">
        <li><a href="account_setting.php">Settings</a></li>

        <!-- Link do wylogowania -->
        <li><a href="logout.php">Logout</a></li>
    </ul>
</nav>
