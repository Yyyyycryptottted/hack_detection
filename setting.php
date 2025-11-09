<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once 'config.php';

// Load language
$languagePath = __DIR__ . '/includes/language_loader.php';
if (file_exists($languagePath)) include($languagePath);

// Load theme
$themePath = __DIR__ . '/includes/theme_loader.php';
if (file_exists($themePath)) include($themePath);

$username = htmlspecialchars($_SESSION['username'] ?? "User");
$theme = $_SESSION['theme'] ?? 'dark';

// Handle password change
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['new_password'])) {
  $newPassword = trim($_POST['new_password']);
  if (!empty($newPassword) && isset($_SESSION['username'])) {
    $stmt = $conn->prepare("UPDATE users SET password=? WHERE username=?");
    $stmt->bind_param("ss", $newPassword, $_SESSION['username']);
    $stmt->execute();
    $stmt->close();

    echo "<script>
      document.addEventListener('DOMContentLoaded', ()=>{
        const alertBox=document.createElement('div');
        alertBox.innerHTML='✅ Password Updated Successfully';
        alertBox.style.cssText='position:fixed;top:50%;left:50%;transform:translate(-50%,-50%);background:rgba(0,20,20,0.8);color:#00ffa3;font-weight:600;padding:20px 40px;border-radius:10px;box-shadow:0 0 25px rgba(0,255,200,0.3);text-shadow:0 0 15px #00ffa3;z-index:9999;opacity:0;transition:opacity 0.6s ease;font-size:1.2rem;';
        document.body.appendChild(alertBox);
        setTimeout(()=>alertBox.style.opacity='1',100);
        setTimeout(()=>alertBox.style.opacity='0',1500);
        setTimeout(()=>window.location.href='homepage.php',2500);
      });
    </script>";
    exit;
  }
}

// Save toggle state
if (isset($_GET['notify'])) {
  $_SESSION['notify'] = ($_GET['notify'] === 'on');
  header("Location: setting.php");
  exit();
}
$notificationsEnabled = $_SESSION['notify'] ?? false;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo $lang['settings']; ?> - Hack Detection System</title>

<style>
*{margin:0;padding:0;box-sizing:border-box;}
body{
  font-family:'Poppins',sans-serif;
  min-height:100vh;
  overflow-x:hidden;
  transition:background 0.8s ease,color 0.8s ease;
}

/* ==== DARK MODE ==== */
body.dark {
  background: radial-gradient(circle at 20% 20%, #00131f 0%, #000a14 100%);
  color: #e6faff;
}
body.dark header {
  background: rgba(0, 25, 50, 0.7);
  border-bottom: 1px solid rgba(0,255,255,0.15);
}
body.dark .container {
  background: rgba(0,30,60,0.5);
  border: 1px solid rgba(0,255,255,0.15);
  box-shadow: 0 0 30px rgba(0,255,255,0.2);
}
body.dark button {
  background: linear-gradient(90deg,#00eaff,#007bff);
  color: #001;
}

/* ==== LIGHT MODE ==== */
body.light {
  background: linear-gradient(135deg, #f5fbff, #e6f2ff);
  color: #111;
}
body.light header {
  background: rgba(255,255,255,0.8);
  border-bottom: 1px solid #cde9f7;
}
body.light .container {
  background: rgba(255,255,255,0.85);
  border: 1px solid #cde9f7;
}
body.light button {
  background: linear-gradient(90deg,#007bff,#00eaff);
  color: #fff;
}

/* ==== HEADER ==== */
header {
  position:fixed;
  top:0;left:0;width:100%;
  padding:15px 40px;
  display:flex;
  justify-content:space-between;
  align-items:center;
  z-index:1000;
}
header h1 {
  font-size:1.6rem;
  font-weight:700;
  color:#00eaff;
  text-shadow:0 0 10px #00eaff;
}
nav ul {display:flex;gap:25px;list-style:none;}
nav a {text-decoration:none;color:inherit;font-weight:600;}
nav a:hover {color:#00eaff;}

/* ==== CONTAINER ==== */
.container {
  width:90%;
  max-width:800px;
  border-radius:18px;
  padding:40px;
  margin:140px auto 60px;
  background:rgba(0,0,0,0.2);
  animation:fadeUp 0.9s ease;
}
.container h2 {
  text-align:center;
  font-size:1.8em;
  color:#00eaff;
  text-shadow:0 0 15px rgba(0,255,255,0.5);
  margin-bottom:25px;
}
.setting-group {margin-bottom:20px;}
form label {font-weight:600;display:block;margin-bottom:8px;}
select,input[type="text"],input[type="password"] {
  width:100%;
  padding:10px;
  border-radius:6px;
  border:1px solid rgba(0,255,255,0.3);
  background:transparent;
  color:inherit;
}
button {
  border:none;
  border-radius:8px;
  padding:12px 20px;
  cursor:pointer;
  width:100%;
  font-weight:700;
  transition:transform 0.3s,box-shadow 0.3s;
}
button:hover {
  transform:translateY(-3px);
  box-shadow:0 0 20px rgba(0,255,255,0.4);
}

/* ==== PASSWORD DROPDOWN ==== */
#passwordContainer {
  display:none;
  margin-top:12px;
  animation:fadeDown 0.4s ease;
}
@keyframes fadeDown {
  from {opacity:0;transform:translateY(-10px);}
  to {opacity:1;transform:translateY(0);}
}

/* ==== ALERT ==== */
#saveAlert {
  display:none;
  position:fixed;
  top:50%;left:50%;
  transform:translate(-50%,-50%);
  color:#00ffa3;
  font-weight:600;
  font-size:1.2rem;
  text-shadow:0 0 15px #00ffa3;
  background:rgba(0,20,20,0.8);
  padding:20px 40px;
  border-radius:10px;
  box-shadow:0 0 25px rgba(0,255,200,0.3);
  opacity:0;
  z-index:9999;
  transition:opacity 0.6s ease;
}

/* ==== FOOTER ==== */
footer {
  position:relative;
  bottom:0;
  width:100%;
  text-align:center;
  padding:35px 10px;
  font-size:1rem;
  color:#00eaff;
  text-shadow:0 0 12px rgba(0,255,255,0.6);
  letter-spacing:0.8px;
  background:rgba(0,25,50,0.25);
  border-top:1px solid rgba(0,255,255,0.2);
  box-shadow:0 -2px 20px rgba(0,255,255,0.2);
  backdrop-filter:blur(10px);
  animation:footerGlow 4s ease-in-out infinite alternate;
}
footer a {
  color:#00eaff;
  text-decoration:none;
  font-weight:600;
  transition:color 0.3s;
}
footer a:hover {color:#00ffa3;text-shadow:0 0 10px #00ffa3;}
@keyframes footerGlow {
  from {box-shadow:0 -2px 25px rgba(0,255,255,0.15);}
  to {box-shadow:0 -2px 40px rgba(0,255,255,0.35);}
}

/* ==== ANIMATIONS ==== */
@keyframes fadeUp {from{opacity:0;transform:translateY(40px);}to{opacity:1;transform:translateY(0);}}
</style>
</head>

<body class="<?php echo $theme; ?>">

<header>
  <h1>Hack Detection System</h1>
  <nav>
    <ul>
      <li><a href="homepage.php"><?php echo $lang['home']; ?></a></li>
      <li><a href="setting.php" style="color:#00eaff;"><?php echo $lang['settings']; ?></a></li>
      <li><a href="malware_select.php"><?php echo $lang['alerts']; ?></a></li>
      <li><a href="help.php"><?php echo $lang['help']; ?></a></li>
      <li><a href="logout.php" style="color:#ff6666;"><?php echo $lang['logout']; ?></a></li>
    </ul>
  </nav>
</header>

<div class="container">
  <h2><?php echo $lang['settings_title']; ?></h2>
  <form method="post" onsubmit="return handleSave();">

    <div class="setting-group">
      <label><input type="checkbox"> <?php echo $lang['enable_security_lock']; ?></label>
    </div>

    <div class="setting-group">
      <label>
        <?php echo $lang['enable_notifications']; ?>
        <div class="toggle">
          <input type="checkbox" id="notifySwitch" <?php echo $notificationsEnabled ? 'checked' : ''; ?>>
          <span class="slider"></span>
        </div>
      </label>
    </div>

    <div class="setting-group">
      <label for="language"><?php echo $lang['preferred_language']; ?>:</label>
      <select id="language" onchange="changeLanguage(this.value)">
        <option value="en" <?php if($_SESSION['lang']=='en') echo 'selected'; ?>>English</option>
        <option value="es" <?php if($_SESSION['lang']=='es') echo 'selected'; ?>>Spanish</option>
        <option value="fr" <?php if($_SESSION['lang']=='fr') echo 'selected'; ?>>French</option>
        <option value="de" <?php if($_SESSION['lang']=='de') echo 'selected'; ?>>German</option>
        <option value="jp" <?php if($_SESSION['lang']=='jp') echo 'selected'; ?>>Japanese</option>
      </select>
    </div>

    <!-- CHANGE PASSWORD -->
    <div class="setting-group">
      <button type="button" onclick="togglePasswordBox()">Change Password</button>
      <div id="passwordContainer">
        <input type="password" id="newPassword" name="new_password" placeholder="Enter new password">
        <button type="submit" style="margin-top:10px;">Save Password</button>
      </div>
    </div>

    <div class="setting-group">
      <label for="update"><?php echo $lang['update_frequency']; ?>:</label>
      <select id="update" name="update">
        <option value="daily">Daily</option>
        <option value="weekly">Weekly</option>
        <option value="manual">Manual</option>
      </select>
    </div>

    <div class="setting-group">
      <label><input type="checkbox"> <?php echo $lang['privacy_mode']; ?></label>
    </div>

    <button type="button" onclick="saveSettings()"><?php echo $lang['save_settings']; ?></button>
  </form>
</div>

<div id="saveAlert">✅ <?php echo $lang['save_success']; ?></div>

<footer>
  <p>© <?php echo date("Y"); ?> <strong>Hack Detection System</strong><br>
  <span style="font-size:0.9em;">Empowering AI-driven Cyber Defense</span><br>
  <a href="help.php"><?php echo $lang['help']; ?></a></p>
</footer>

<script>
function changeLanguage(lang){window.location.href="setting.php?lang="+lang;}

function togglePasswordBox(){
  const box=document.getElementById("passwordContainer");
  box.style.display = box.style.display==="none"||box.style.display===""?"block":"none";
}

function saveSettings(){
  const alertBox=document.getElementById("saveAlert");
  alertBox.style.display="block";
  setTimeout(()=>alertBox.style.opacity="1",50);
  setTimeout(()=>alertBox.style.opacity="0",1500);
  setTimeout(()=>window.location.href='homepage.php',2500);
}

function handleSave(){return true;}
</script>
</body>
</html>
