<?php
session_start();
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
<title>Smart Scan - Hack Detection System</title>
<style>
*{margin:0;padding:0;box-sizing:border-box}
body{
  font-family:'Poppins',sans-serif;
  background:linear-gradient(135deg,#02172a,#00385f);
  color:#e8f9ff;
  min-height:100vh;
  display:flex;
  justify-content:center;
  align-items:center;
}
.container{
  width:90%;
  max-width:650px;
  background:rgba(255,255,255,0.08);
  backdrop-filter:blur(15px);
  border-radius:16px;
  padding:40px;
  text-align:center;
  box-shadow:0 0 25px rgba(0,255,255,0.15);
}
h1{color:#00eaff;text-shadow:0 0 12px rgba(0,255,255,0.4)}
p{color:#b8e8ff;margin-bottom:20px}
.progress-container{
  width:100%;
  background:rgba(255,255,255,0.1);
  border-radius:8px;
  overflow:hidden;
  margin:20px 0;
}
.progress-bar{
  height:20px;width:0;
  background:linear-gradient(90deg,#00eaff,#007bff);
  transition:width 0.4s ease;
}
#scan-status{margin-top:8px;color:#00eaff;font-weight:600}
#scan-path{font-size:0.9rem;color:#a9dfff;margin-top:5px}
#results{
  display:none;
  margin-top:25px;
  text-align:left;
  background:rgba(255,255,255,0.05);
  border-radius:10px;
  padding:20px;
}
.result-item{margin:6px 0;padding:10px;border-left:4px solid #00eaff;border-radius:4px}
.result-item.danger{border-left-color:#ff4444}
.result-item.safe{border-left-color:#00ffa3}
button{
  margin-top:25px;
  background:linear-gradient(90deg,#00eaff,#007bff);
  color:#001;font-weight:700;
  border:none;border-radius:8px;
  padding:12px 24px;cursor:pointer;
  transition:transform 0.3s,box-shadow 0.3s;
}
button:hover{transform:translateY(-2px);box-shadow:0 8px 20px rgba(0,255,255,0.3)}
@keyframes fadeUp{from{opacity:0;transform:translateY(40px)}to{opacity:1;transform:translateY(0)}}
</style>
</head>
<body>
<div class="container">
  <h1>Smart Scan</h1>
  <p>Scanning your selected safe directories for potential threats...</p>

  <div class="progress-container">
    <div class="progress-bar" id="progress-bar"></div>
  </div>

  <div id="scan-status">Initializing Smart Scan...</div>
  <div id="scan-path"></div>

  <div id="results"></div>
  <button id="backButton" style="display:none" onclick="window.location.href='malware_select.php'">Back to Scans</button>
</div>

<script>
const folders = [
  "D:\\\\Old is gold",
  "D:\\\\theme packs etc"
];
const statusMessages = [
  "Checking file integrity...",
  "Analyzing folder structure...",
  "Scanning for executable anomalies...",
  "Inspecting media and theme files...",
  "Verifying hidden files...",
  "Performing behavioral analysis...",
  "Finalizing scan results..."
];

let index=0, progress=0, folderIndex=0;
const pb=document.getElementById("progress-bar");
const status=document.getElementById("scan-status");
const pathEl=document.getElementById("scan-path");
const results=document.getElementById("results");
const backBtn=document.getElementById("backButton");

function simulate(){
  if(index<statusMessages.length){
    progress+=(100/statusMessages.length);
    pb.style.width=progress+"%";
    status.textContent=statusMessages[index];
    pathEl.textContent="Scanning: "+folders[folderIndex];
    index++;
    if(index===Math.ceil(statusMessages.length/2)) folderIndex=1; // switch folder mid-scan
    setTimeout(simulate,1200);
  }else showResults();
}

function showResults(){
  pb.style.width="100%";
  status.textContent="Scan Complete";
  pathEl.textContent="Completed: "+folders.join(" | ");

  // simulated findings
  const hasThreat=Math.random()<0.5;
  if(hasThreat){
    results.innerHTML=`
      <h2 style='color:#ff4444;margin-bottom:10px'>⚠️ Threats Detected</h2>
      <div class='result-item danger'>• Adware.ThemeInjector — Found in D:\\theme packs etc\\theme-loader.exe</div>
      <div class='result-item danger'>• Trojan.MediaDropper — Found in D:\\Old is gold\\oldplayer.exe</div>
    `;
  }else{
    results.innerHTML=`
      <h2 style='color:#00ffa3;margin-bottom:10px'>✅ No Malware Found</h2>
      <div class='result-item safe'>Scanned directories: ${folders.join(" , ")}</div>
      <div class='result-item safe'>System appears safe and clean.</div>
    `;
  }
  results.style.display="block";
  backBtn.style.display="inline-block";
}

window.onload=simulate;
</script>
</body>
</html>
