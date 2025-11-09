<?php
if (session_status() === PHP_SESSION_NONE) session_start();

// Load language
$languagePath = __DIR__ . '/includes/language_loader.php';
if (file_exists($languagePath)) include($languagePath);
else {
  $lang = [
    'home' => 'Home',
    'settings' => 'Settings',
    'alerts' => 'Alerts',
    'help' => 'Help',
    'logout' => 'Logout',
    'welcome_title' => 'Welcome to the Hack Detection System',
  ];
}

// Load theme
$themePath = __DIR__ . '/includes/theme_loader.php';
if (file_exists($themePath)) include($themePath);
else $_SESSION['theme'] = 'dark';

if (!isset($_SESSION['username'])) {
  header("Location: Login_page.php");
  exit();
}

$username = htmlspecialchars($_SESSION['username']);
$theme = $_SESSION['theme'] ?? 'dark';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Hack Detection System - <?php echo $lang['home']; ?></title>

<style>
* {margin:0; padding:0; box-sizing:border-box;}
body {
  font-family:'Poppins',sans-serif;
  min-height:100vh;
  overflow-x:hidden;
  transition:background 1.2s ease, color 1.2s ease;
}

/* ==== DARK MODE ==== */
body.dark {
  background: radial-gradient(circle at center, #001220 0%, #000814 100%);
  color: #e9f7ff;
}
body.dark header {
  background: rgba(0, 25, 50, 0.6);
  border-bottom: 1px solid rgba(0, 255, 255, 0.15);
  box-shadow: 0 2px 15px rgba(0, 255, 255, 0.2);
}
body.dark .hero {
  background: linear-gradient(135deg, rgba(0, 30, 60, 0.85), rgba(0, 60, 100, 0.7));
}
body.dark .feature, body.dark .features {
  background: rgba(255, 255, 255, 0.05);
}
body.dark .cta {
  background: linear-gradient(90deg, #001c30, #004f8b);
  color: white;
}
body.dark footer {
  background: #000f1c;
  color: #a9c7d6;
}

/* ==== LIGHT MODE ==== */
body.light {
  background: linear-gradient(135deg, #e9f3ff 0%, #fefefe 100%);
  color: #222;
}
body.light header {
  background: #fff;
  color: #000;
  border-bottom: 1px solid #e1e1e1;
  box-shadow: 0 2px 20px rgba(0, 0, 0, 0.08);
}
body.light .hero {
  background: linear-gradient(135deg, #f5faff, #e0f3ff);
  color: #003366;
}
body.light .feature {
  background: #fff;
  border: 1px solid #e0e0e0;
  color: #000;
}
body.light .features {
  background: #f7fbff;
}
body.light .cta {
  background: linear-gradient(90deg, #007bff, #00aaff);
  color: white;
}
body.light footer {
  background: #f8f9fb;
  color: #333;
}

/* ==== HEADER ==== */
header {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  padding: 15px 40px;
  display: flex;
  justify-content: space-between;
  align-items: center;
  backdrop-filter: blur(20px);
  transition: background 0.6s ease;
  z-index: 999;
}
header h1 {
  font-size: 1.6rem;
  font-weight: 700;
  color: #00eaff;
}
nav ul {
  list-style: none;
  display: flex;
  gap: 25px;
  align-items: center;
}
nav a {
  text-decoration: none;
  font-weight: 600;
  position: relative;
  transition: color 0.3s ease;
  color: inherit;
}
nav a:hover {color: #00eaff;}
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
nav a:hover::after {width: 100%;}

select {
  background: rgba(255,255,255,0.08);
  color: inherit;
  border: 1px solid rgba(255,255,255,0.3);
  border-radius: 6px;
  padding: 4px 6px;
  font-weight: 600;
  transition: all 0.4s ease;
}
select option {
  background: #fff;
  color: #000;
}

/* ==== HERO ==== */
.hero {
  text-align: center;
  padding: 160px 20px 140px;
  backdrop-filter: blur(10px);
  position: relative;
  overflow: hidden;
  transition: background 1.2s ease, color 1.2s ease;
}
.hero h2 {
  font-size: 2.5rem;
  font-weight: 700;
  margin-bottom: 10px;
  color: #00eaff;
  text-shadow: 0 0 15px rgba(0,255,255,0.4);
  animation: fadeUp 1.2s ease;
}
.hero p {
  font-size: 1.2rem;
  margin-bottom: 30px;
  animation: fadeUp 1.6s ease;
}
.hero a {
  text-decoration: none;
  background: linear-gradient(90deg, #00eaff, #007bff);
  color: #001;
  padding: 12px 30px;
  border-radius: 30px;
  font-weight: 700;
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}
.hero a:hover {
  transform: translateY(-3px);
  box-shadow: 0 8px 25px rgba(0,255,255,0.4);
}

/* ==== FEATURES ==== */
.features {
  padding: 80px 10%;
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 30px;
  text-align: center;
}
.feature {
  padding: 25px;
  border-radius: 15px;
  box-shadow: 0 4px 12px rgba(0,0,0,0.1);
  transition: all 0.4s ease;
}
.feature:hover {
  transform: translateY(-6px);
  box-shadow: 0 8px 25px rgba(0,255,255,0.3);
}
.feature img {
  width: 70px;
  margin-bottom: 20px;
}
.feature h3 {
  font-size: 1.2rem;
  margin-bottom: 10px;
}
.feature p {font-size: 0.95rem;}

/* ==== CTA ==== */
.cta {
  padding: 80px 20px;
  text-align: center;
  transition: background 1s ease;
}
.cta h2 {font-size: 2rem; margin-bottom: 15px;}
.cta p {font-size: 1rem; margin-bottom: 30px;}
.cta a {
  background: #fff;
  color: #003d66;
  text-decoration: none;
  padding: 12px 28px;
  border-radius: 25px;
  font-weight: 700;
  transition: all 0.3s;
}
.cta a:hover {background: #00eaff; color: #000; box-shadow: 0 0 20px rgba(0,255,255,0.4);}

/* ==== FOOTER ==== */
footer {
  padding: 40px 10%;
  text-align: center;
}
footer p {font-size: 0.9rem;}
footer a {color: #00eaff; text-decoration: none;}
footer a:hover {text-decoration: underline;}

@keyframes fadeUp {
  from {opacity: 0; transform: translateY(40px);}
  to {opacity: 1; transform: translateY(0);}
}
</style>
</head>

<body class="<?php echo $theme; ?>">
<header>
  <h1>Hack Detection System</h1>
  <nav>
    <ul>
      <li><a href="homepage.php" style="color:#00eaff;"><?php echo $lang['home']; ?></a></li>
      <li><a href="setting.php"><?php echo $lang['settings']; ?></a></li>
      <li><a href="malware_select.php"><?php echo $lang['alerts']; ?></a></li>
      <li><a href="help.php"><?php echo $lang['help']; ?></a></li>
      <li><a href="logout.php" style="color:#ff6666;"><?php echo $lang['logout']; ?></a></li>
      <li>
        <select id="mode" onchange="changeMode(this.value)">
          <option value="dark" <?php if($theme=='dark') echo 'selected'; ?>>Dark</option>
          <option value="light" <?php if($theme=='light') echo 'selected'; ?>>Light</option>
        </select>
      </li>
    </ul>
  </nav>
</header>

<section class="hero">
  <h2><?php echo $lang['welcome_title']; ?>, <?php echo $username; ?> 👋</h2>
  <p>Keep your system threat-free with advanced detection and real-time protection.</p>
  <a href="malware select.php">Run Selective Scan</a>
</section>

<section class="features">
  <div class="feature">
    <img src="https://cdn-icons-png.flaticon.com/512/992/992700.png" alt="Scan Icon">
    <h3>Real-time Monitoring</h3>
    <p>Continuously monitor your system for potential threats and suspicious activity.</p>
  </div>
  <div class="feature">
    <img src="https://cdn-icons-png.flaticon.com/512/942/942751.png" alt="Alert Icon">
    <h3>Instant Alerts</h3>
    <p>Receive instant notifications when suspicious files are detected.</p>
  </div>
  <div class="feature">
    <img src="https://cdn-icons-png.flaticon.com/512/1041/1041916.png" alt="Report Icon">
    <h3>Detailed Reports</h3>
    <p>Get complete threat analysis reports and scanning history.</p>
  </div>
  <div class="feature">
    <img src="https://cdn-icons-png.flaticon.com/512/942/942748.png" alt="Settings Icon">
    <h3>Secure Settings</h3>
    <p>Control your protection levels and privacy settings with ease.</p>
  </div>
</section>

<section class="cta">
  <h2>Stay Protected, Always</h2>
  <p>Advanced hack detection. Real-time protection. One click away.</p>
  <a href="setting.php">Customize Settings</a>
</section>

<footer>
  <p>&copy; <?php echo date("Y"); ?> Hack Detection System | All Rights Reserved</p>
  <p>Designed for cyber protection | <a href="help.php"><?php echo $lang['help']; ?></a></p>
</footer>

<script>
function changeMode(mode) {
  document.body.style.transition = 'none';
  document.body.classList.add('fade');
  setTimeout(() => {
    window.location.href = '?theme=' + mode;
  }, 300);
}
</script>

</body>
</html>
