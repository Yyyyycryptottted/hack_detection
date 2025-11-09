<?php
if (session_status() === PHP_SESSION_NONE) session_start();

// Load language file
$languagePath = __DIR__ . '/includes/language_loader.php';
if (file_exists($languagePath)) include($languagePath);
else {
  $lang = [
    'title' => 'Hack Detection System',
    'description' => 'Analyze, monitor, and secure your network with real-time hack detection and AI-based risk insights.',
    'login' => 'Login',
    'create_account' => 'Create a New Account'
  ];
}

// Fallback check to prevent undefined index
$lang = array_merge([
  'title' => 'Hack Detection System',
  'description' => 'Analyze, monitor, and secure your network with real-time hack detection and AI-based risk insights.',
  'login' => 'Login',
  'create_account' => 'Create a New Account'
], $lang);

// Theme is fixed; can be changed only from settings/homepage
$theme = $_SESSION['theme'] ?? 'dark';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo htmlspecialchars($lang['title']); ?></title>

<style>
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

/* === CYBER SECURITY BACKGROUND === */
body {
  font-family: 'Segoe UI', Arial, sans-serif;
  background: linear-gradient(120deg, #010b1f, #041c2f, #00334e);
  background-size: 400% 400%;
  animation: cyberPulse 10s ease infinite;
  display: flex;
  align-items: center;
  justify-content: center;
  height: 100vh;
  overflow: hidden;
}

/* Moving glowing grid lines */
body::before,
body::after {
  content: '';
  position: absolute;
  top: 0; left: 0;
  width: 100%;
  height: 100%;
  background-image:
    linear-gradient(90deg, rgba(0,255,255,0.08) 1px, transparent 1px),
    linear-gradient(180deg, rgba(0,255,255,0.08) 1px, transparent 1px);
  background-size: 60px 60px;
  animation: gridMove 30s linear infinite;
  pointer-events: none;
  z-index: 0;
}

@keyframes cyberPulse {
  0% { background-position: 0% 50%; }
  50% { background-position: 100% 50%; }
  100% { background-position: 0% 50%; }
}

@keyframes gridMove {
  from { background-position: 0 0, 0 0; }
  to { background-position: 60px 60px, 60px 60px; }
}

/* === CONTAINER === */
.container {
  position: relative;
  z-index: 1;
  background: rgba(255, 255, 255, 0.06);
  border: 1px solid rgba(0, 255, 255, 0.15);
  backdrop-filter: blur(20px);
  border-radius: 16px;
  padding: 40px 35px;
  text-align: center;
  width: 90%;
  max-width: 420px;
  color: #eafcff;
  box-shadow: 0 0 25px rgba(0, 255, 255, 0.12);
  transform: translateY(20px);
  opacity: 0;
  animation: fadeInUp 1.2s ease forwards;
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.container:hover {
  transform: scale(1.02);
  box-shadow: 0 0 45px rgba(0, 255, 255, 0.25);
}

/* === TITLE & TEXT === */
h1 {
  font-size: 1.9em;
  font-weight: 700;
  color: #00eaff;
  margin-bottom: 10px;
  text-shadow: 0 0 15px rgba(0, 255, 255, 0.5);
  letter-spacing: 1px;
  animation: glowText 2.5s ease-in-out infinite alternate;
}

p {
  font-size: 1em;
  color: #c8d9e8;
  margin-bottom: 28px;
  line-height: 1.6;
}

/* === BUTTONS === */
button {
  width: 100%;
  background: linear-gradient(90deg, #00eaff, #007bff);
  color: #fff;
  border: none;
  padding: 12px 16px;
  border-radius: 8px;
  cursor: pointer;
  font-size: 1em;
  font-weight: 600;
  margin-bottom: 15px;
  transition: all 0.3s ease;
  position: relative;
  overflow: hidden;
  letter-spacing: 0.5px;
}

button:hover {
  transform: translateY(-3px);
  box-shadow: 0 0 20px rgba(0, 229, 255, 0.6);
}

button::after {
  content: '';
  position: absolute;
  top: 50%;
  left: 50%;
  width: 6px;
  height: 6px;
  background: rgba(255, 255, 255, 0.6);
  border-radius: 50%;
  transform: translate(-50%, -50%) scale(0);
  opacity: 0;
}

button:active::after {
  transform: translate(-50%, -50%) scale(35);
  opacity: 0.2;
  transition: transform 0.6s ease, opacity 0.8s ease;
}

/* === ANIMATIONS === */
@keyframes fadeInUp {
  from { opacity: 0; transform: translateY(50px); }
  to { opacity: 1; transform: translateY(0); }
}

@keyframes glowText {
  from { text-shadow: 0 0 10px #00eaff; }
  to { text-shadow: 0 0 25px #00eaff; }
}

/* === RESPONSIVE === */
@media (max-width: 600px) {
  .container {
    width: 85%;
    padding: 28px 22px;
  }
  h1 { font-size: 1.5em; }
  p { font-size: 0.95em; }
}
</style>
</head>

<body class="<?php echo htmlspecialchars($theme); ?>">
  <div class="container">
    <h1><?php echo htmlspecialchars($lang['title']); ?></h1>
    <p><?php echo htmlspecialchars($lang['description']); ?></p>

    <form action="Login_page.php" method="post">
      <button type="submit"><?php echo htmlspecialchars($lang['login']); ?></button>
    </form>

    <form action="registration.php" method="post">
      <button type="submit"><?php echo htmlspecialchars($lang['create_account']); ?></button>
    </form>
  </div>
</body>
</html>
