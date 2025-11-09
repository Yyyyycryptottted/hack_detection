<?php
session_start();
if (!isset($_SESSION['username'])) {
  header("Location: login.php");
  exit();
}

// Prevent browser caching (so page can't reappear via back button)
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
$username = htmlspecialchars($_SESSION['username']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title> Scanning - HD Alert</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">

<style>
*{margin:0;padding:0;box-sizing:border-box}
body{
  font-family:'Poppins',sans-serif;
  background: radial-gradient(circle at 30% 20%,#00172a,#000913);
  height:100vh;
  display:flex;
  flex-direction:column;
  align-items:center;
  justify-content:center;
  color:#e8f9ff;
  overflow:hidden;
  animation:bgShift 6s ease-in-out infinite alternate;
}
@keyframes bgShift{
  0%{background-position:0 0}
  100%{background-position:100% 100%}
}

.container{
  text-align:center;
  background:rgba(255,255,255,0.05);
  border:1px solid rgba(255,255,255,0.1);
  border-radius:20px;
  padding:60px 40px;
  width:90%;
  max-width:480px;
  backdrop-filter:blur(20px);
  box-shadow:0 0 40px rgba(0,255,255,0.1);
  animation:fadeUp 1s ease;
}
@keyframes fadeUp{
  from{opacity:0;transform:translateY(30px)}
  to{opacity:1;transform:translateY(0)}
}
.container h2{
  font-size:1.8rem;
  color:#00eaff;
  margin-bottom:25px;
  letter-spacing:0.5px;
}

/* Circular Progress */
.circle{
  position:relative;
  width:180px;
  height:180px;
  border-radius:50%;
  background:conic-gradient(#00eaff 0deg,#004d73 0deg);
  display:flex;
  align-items:center;
  justify-content:center;
  margin:0 auto 30px;
  box-shadow:0 0 25px rgba(0,255,255,0.2), inset 0 0 20px rgba(0,255,255,0.1);
}
.circle::before{
  content:"";
  position:absolute;
  width:140px;
  height:140px;
  background:#001826;
  border-radius:50%;
  z-index:1;
}
.circle span{
  position:relative;
  font-size:2rem;
  font-weight:700;
  color:#00eaff;
  z-index:2;
}
.glow{
  position:absolute;
  inset:0;
  border-radius:50%;
  background:radial-gradient(circle,rgba(0,238,255,0.2) 0%,transparent 70%);
  animation:rotateGlow 3s linear infinite;
}
@keyframes rotateGlow{
  from{transform:rotate(0deg)}
  to{transform:rotate(360deg)}
}

.message{
  font-size:1.1rem;
  color:#a8e8ff;
  margin-top:10px;
  animation:pulse 1.4s infinite ease-in-out;
}
@keyframes pulse{
  0%,100%{opacity:1}
  50%{opacity:0.6}
}

/* Progress Bar */
.scan-bar{
  width:100%;
  height:6px;
  background:rgba(255,255,255,0.1);
  border-radius:3px;
  overflow:hidden;
  margin-top:25px;
}
.scan-bar-inner{
  width:0%;
  height:100%;
  background:linear-gradient(90deg,#00eaff,#0088ff);
  border-radius:3px;
  transition:width 0.2s linear;
}

/* Footer */
footer{
  position:absolute;
  bottom:20px;
  font-size:0.9rem;
  color:#6bbfd9;
  text-align:center;
  width:100%;
}
</style>
</head>

<body>
  <div class="container">
    <h2>Scanning</h2>
    <div class="circle" id="progressCircle">
      <div class="glow"></div>
      <span id="percent">0%</span>
    </div>
    <div class="message" id="statusText">Initializing Scan Engine...</div>
    <div class="scan-bar"><div class="scan-bar-inner" id="barInner"></div></div>
  </div>

  <footer>
    <p>© <?php echo date('Y'); ?> HD Alert | Secure System Verification in Progress...</p>
  </footer>

<script>
// Prevent reloading this page when using Back button
if (performance.navigation.type === 2) {
  window.location.replace("malware_select.php");
}

// Animate circular progress
let percent = 0;
const percentEl = document.getElementById("percent");
const circle = document.getElementById("progressCircle");
const statusEl = document.getElementById("statusText");
const barInner = document.getElementById("barInner");

const messages = [
  "Initializing Scan Engine...",
  "Analyzing Directories...",
  "Verifying System Integrity...",
  "Checking Malware Signatures...",
  "Optimizing Detection Modules...",
  "Finalizing Security Scan..."
];

let msgIndex = 0;
let interval = setInterval(() => {
  percent += 1;
  if (percent > 100) {
    clearInterval(interval);
    statusEl.textContent = "Scan Complete. Redirecting...";
    setTimeout(() => {
      window.location.href = "full_scan.php";
    }, 1000);
  } else {
    percentEl.textContent = percent + "%";
    circle.style.background = `conic-gradient(#00eaff ${percent * 3.6}deg,#004d73 ${percent * 3.6}deg)`;
    barInner.style.width = percent + "%";
    if (percent % 17 === 0 && msgIndex < messages.length - 1) {
      msgIndex++;
      statusEl.textContent = messages[msgIndex];
    }
  }
}, 40); // total ~4 seconds
</script>

</body>
</html>
