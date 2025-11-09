<?php
if (session_status() === PHP_SESSION_NONE) session_start();

// Load global language (optional)
$languagePath = __DIR__ . '/includes/language_loader.php';
if (file_exists($languagePath)) include($languagePath);

// Load global theme system
$themePath = __DIR__ . '/includes/theme_loader.php';
if (file_exists($themePath)) include($themePath);

$theme = $_SESSION['theme'] ?? 'dark';

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}
$username = htmlspecialchars($_SESSION['username']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Help & Support - Hack Detection System</title>

<style>
/* ===== RESET ===== */
* {margin:0; padding:0; box-sizing:border-box;}
body {
  font-family: 'Poppins', sans-serif;
  min-height: 100vh;
  overflow-x: hidden;
  display: flex;
  flex-direction: column;
  align-items: center;
  transition: background 1.2s ease, color 1.2s ease;
}

/* ===== DARK MODE ===== */
body.dark {
  background: linear-gradient(135deg, #02172a, #00385f);
  color: #e8f9ff;
}
body.dark header {
  backdrop-filter: blur(20px) saturate(160%);
  background-color: rgba(0, 30, 54, 0.6);
  border-bottom: 1px solid rgba(255, 255, 255, 0.15);
  box-shadow: 0 2px 15px rgba(0, 255, 255, 0.15);
}
body.dark .container {
  background: rgba(255,255,255,0.08);
  color: #fff;
  box-shadow: 0 0 25px rgba(0,255,255,0.15);
}
body.dark .faq-item {
  background: rgba(0,0,0,0.3);
  color: #cfeef0;
}
body.dark .faq-item:hover {
  background: rgba(0,255,255,0.1);
}
body.dark footer {
  background: #001e36;
  color: #a9c7d6;
}

/* ===== LIGHT MODE ===== */
body.light {
  background: linear-gradient(135deg, #f5f9ff, #ffffff);
  color: #222;
}
body.light header {
  background: #ffffffcc;
  color: #000;
  border-bottom: 1px solid #e0e0e0;
  box-shadow: 0 2px 15px rgba(0,0,0,0.1);
}
body.light .container {
  background: #ffffffcc;
  color: #000;
  border: 1px solid #e1e1e1;
  box-shadow: 0 4px 25px rgba(0,0,0,0.08);
}
body.light .faq-item {
  background: #f8faff;
  color: #333;
  border: 1px solid #e0e0e0;
}
body.light .faq-item:hover {
  background: #e9f4ff;
}
body.light footer {
  background: #f8faff;
  color: #333;
}

/* ===== FLOATING THEME TRANSITION EFFECT ===== */
body::after {
  content: "";
  position: fixed;
  inset: 0;
  pointer-events: none;
  background: radial-gradient(circle at center, rgba(255,255,255,0.08), transparent 60%);
  opacity: 0;
  animation: floatTransition 2s ease-in-out forwards;
}
@keyframes floatTransition {
  0% {opacity: 0;}
  50% {opacity: 0.4; transform: scale(1.05);}
  100% {opacity: 0;}
}

/* ===== HEADER ===== */
header {
  position: fixed;
  top: 0; left: 0;
  width: 100%;
  padding: 15px 40px;
  display: flex;
  justify-content: space-between;
  align-items: center;
  z-index: 999;
  transition: all 0.6s ease;
}
header h1 {
  color: #00eaff;
  font-size: 1.6rem;
  font-weight: 700;
  letter-spacing: 0.5px;
}
nav ul {
  display: flex;
  gap: 25px;
  list-style: none;
}
nav a {
  text-decoration: none;
  font-weight: 600;
  position: relative;
  color: inherit;
  transition: color 0.4s ease;
}
nav a:hover { color: #00eaff; }
nav a::after {
  content: "";
  position: absolute;
  width: 0;
  height: 2px;
  bottom: -3px;
  left: 0;
  background: #00eaff;
  transition: width 0.3s ease;
}
nav a:hover::after { width: 100%; }

/* ===== HERO ===== */
.hero {
  width: 100%;
  text-align: center;
  padding: 120px 20px 140px;
  position: relative;
  background: linear-gradient(135deg, rgba(0,30,54,0.8), rgba(0,60,100,0.7));
  overflow: hidden;
  color: #fff;
}
body.light .hero {
  background: linear-gradient(135deg, #eaf4ff, #d9ecff);
  color: #003366;
}
.hero h2 {
  font-size: 2.4rem;
  color: #00eaff;
  text-shadow: 0 0 20px rgba(0,255,255,0.4);
  animation: fadeUp 1.2s ease;
}
.hero p {
  font-size: 1.1rem;
  margin-top: 10px;
  animation: fadeUp 1.4s ease;
}

/* ===== CONTAINER ===== */
.container {
  border-radius: 16px;
  padding: 40px;
  max-width: 900px;
  width: 90%;
  margin-top: -60px;
  animation: slideUp 1.2s ease;
}
.container h3 {
  color: #00eaff;
  font-size: 1.4rem;
  margin-bottom: 10px;
}
.container p, .container li {
  line-height: 1.6;
  margin-bottom: 14px;
  font-size: 0.95rem;
}
.container a {
  color: #00aaff;
  text-decoration: none;
  font-weight: 600;
}
.container a:hover {
  text-decoration: underline;
}

/* ===== FAQ ===== */
.faq {
  margin-top: 30px;
}
.faq-item {
  border-radius: 10px;
  padding: 15px 20px;
  transition: 0.3s;
  margin-bottom: 15px;
}
.faq-item h4 {
  font-size: 1rem;
  color: #00eaff;
  margin-bottom: 6px;
}

/* ===== FOOTER ===== */
footer {
  margin-top: 60px;
  padding: 30px 10px;
  text-align: center;
  font-size: 0.9em;
  width: 100%;
}
footer a {
  color: #00eaff;
  text-decoration: none;
}
footer a:hover { text-decoration: underline; }

/* ===== ANIMATIONS ===== */
@keyframes fadeUp {
  from {opacity: 0; transform: translateY(40px);}
  to {opacity: 1; transform: translateY(0);}
}
@keyframes slideUp {
  from {opacity: 0; transform: translateY(50px);}
  to {opacity: 1; transform: translateY(0);}
}

/* ===== RESPONSIVE ===== */
@media (max-width: 600px) {
  header h1 { font-size: 1.3rem; }
  nav ul { gap: 15px; }
  .hero h2 { font-size: 2rem; }
  .container { padding: 25px; }
}
</style>
</head>

<body class="<?php echo $theme; ?>">

<header>
  <h1>Hack Detection System</h1>
  <nav>
    <ul>
      <li><a href="homepage.php">Home</a></li>
      <li><a href="setting.php">Settings</a></li>
      <li><a href="malware_select.php">Alerts</a></li>
      <li><a href="help.php" style="color:#00eaff;">Help</a></li>
      <li><a href="logout.php" style="color:#ff6666;">Logout</a></li>
    </ul>
  </nav>
</header>

<section class="hero">
  <h2>Need Assistance, <?php echo $username; ?>?</h2>
  <p>Your cybersecurity companion is always here to help.</p>
</section>

<div class="container">
  <h3>Getting Started</h3>
  <p>Welcome to the Hack Detection System. Use the navigation menu to explore your dashboard, manage your settings, and monitor system threats in real-time.</p>

  <h3>Setting Up Security Alerts</h3>
  <p>To configure alerts, go to the <a href="setting.php">Settings</a> page. Enable real-time notifications to receive immediate warnings about potential breaches or unusual activity.</p>

  <h3>Understanding Your Dashboard</h3>
  <p>The <a href="homepage.php">Dashboard</a> provides an overview of your system’s health, recent scans, and detected threats.</p>

  <h3>Troubleshooting</h3>
  <p>If something isn’t working as expected, try these steps:</p>
  <ul>
    <li>Ensure your internet connection is stable.</li>
    <li>Check if your antivirus definitions are updated.</li>
    <li>Clear your browser cache and reload the page.</li>
    <li>Contact support if the issue persists.</li>
  </ul>

  <div class="faq">
    <h3>Frequently Asked Questions</h3>
    <div class="faq-item">
      <h4>How often should I scan my system?</h4>
      <p>It’s recommended to perform a full scan weekly and quick scans daily for better protection.</p>
    </div>
    <div class="faq-item">
      <h4>How do I update my virus definitions?</h4>
      <p>Go to <a href="setting.php">Settings</a> → Update Frequency → Choose “Daily”.</p>
    </div>
    <div class="faq-item">
      <h4>How can I contact support?</h4>
      <p>You can reach us 24/7 at <a href="mailto:Hdalert@gmail.com">Hdalert@gmail.com</a>.</p>
    </div>
  </div>

  <h3>Contact Support</h3>
  <p>Need personalized help? Email our support team. We respond within 24 hours.</p>
</div>

<footer>
  <p>&copy; <?php echo date("Y"); ?> Hack Detection System | Built for Cyber Protection.</p>
  <p>Contact: <a href="mailto:Hdalert@gmail.com">Hdalert@gmail.com</a></p>
</footer>

</body>
</html>
