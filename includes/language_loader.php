<?php
// Safe session start
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Default language
if (!isset($_SESSION['lang'])) {
    $_SESSION['lang'] = 'en';
}

// Allow override via ?lang=xx
if (isset($_GET['lang'])) {
    $_SESSION['lang'] = preg_replace('/[^a-z]/', '', $_GET['lang']); // sanitize
}

// Determine which language file to load
$langCode = $_SESSION['lang'];
$langFile = __DIR__ . "/../languages/{$langCode}.php";

// Load selected language, fallback to English
if (file_exists($langFile)) {
    $lang = include $langFile;
} else {
    $lang = include __DIR__ . "/../languages/en.php";
}
?>
