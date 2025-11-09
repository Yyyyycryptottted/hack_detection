<?php
if (session_status() === PHP_SESSION_NONE) session_start();

// Default theme is dark
if (!isset($_SESSION['theme'])) {
    $_SESSION['theme'] = 'dark';
}

// Switch theme via GET parameter (?theme=light or ?theme=dark)
if (isset($_GET['theme'])) {
    $theme = $_GET['theme'] === 'light' ? 'light' : 'dark';
    $_SESSION['theme'] = $theme;
    // redirect back without GET param
    $ref = strtok($_SERVER['REQUEST_URI'], '?');
    header("Location: $ref");
    exit();
}
?>
