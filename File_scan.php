<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Scan Results - Hack Detection System</title>

<style>
/* RESET */
*{margin:0;padding:0;box-sizing:border-box}
body{
  font-family:'Poppins',sans-serif;
  background:#000;
  color:#fff;
  overflow-x:hidden;
  min-height:100vh;
  display:flex;
  justify-content:center;
  align-items:center;
  position:relative;
}

/* ======= BACKGROUND ======= */
body::before{
  content:"";
  position:absolute;
  inset:0;
  background:radial-gradient(circle at center,#001022 0%,#000812 80%);
  z-index:-3;
}
body::after{
  content:"";
  position:absolute;
  inset:0;
  background:
    repeating-linear-gradient(45deg,rgba(0,255,255,0.05) 0,rgba(0,255,255,0.05) 2px,transparent 2px,transparent 20px),
    repeating-linear-gradient(-45deg,rgba(0,255,255,0.05) 0,rgba(0,255,255,0.05) 2px,transparent 2px,transparent 20px);
  animation:moveGrid 10s linear infinite;
  z-index:-2;
  opacity:.7;
}
@keyframes moveGrid{
  0%{background-position:0 0,0 0}
  100%{background-position:200px 200px,-200px 200px}
}

/* ======= CONTAINER ======= */
.container{
  background:rgba(255,255,255,0.08);
  backdrop-filter:blur(20px);
  border:1px solid rgba(0,255,255,0.1);
  border-radius:20px;
  padding:40px 30px;
  width:90%;
  max-width:600px;
  text-align:center;
  box-shadow:0 0 25px rgba(0,255,255,0.15);
  animation:fadeIn 1.2s ease forwards;
}
h1{
  font-size:1.8rem;
  color:#00eaff;
  text-shadow:0 0 12px rgba(0,255,255,0.4);
  margin-bottom:20px;
}

/* ======= ALERT BOX ======= */
.alert{
  margin:20px 0;
  padding:15px;
  border-radius:10px;
  font-weight:600;
  text-align:center;
  animation:fadeUp 1s ease;
}
.alert.success{
  color:#00ffcc;
  background:rgba(0,255,180,0.1);
  border:1px solid rgba(0,255,200,0.3);
  box-shadow:0 0 12px rgba(0,255,200,0.25);
}
.alert.danger{
  color:#ff5b5b;
  background:rgba(255,60,60,0.08);
  border:1px solid rgba(255,80,80,0.3);
  box-shadow:0 0 12px rgba(255,80,80,0.25);
}

/* ======= FILE LIST ======= */
.file-list{
  text-align:left;
  margin:20px 0;
  font-size:0.95rem;
}
.file-item{
  display:flex;
  justify-content:space-between;
  align-items:center;
  background:rgba(255,255,255,0.05);
  border-radius:8px;
  padding:10px 15px;
  margin-bottom:10px;
  animation:fadeUp 0.6s ease;
}
.file-item span{
  color:#fff;
}
.file-item button{
  background:linear-gradient(90deg,#ff4747,#d60000);
  border:none;
  border-radius:8px;
  color:#fff;
  font-weight:700;
  padding:6px 14px;
  cursor:pointer;
  transition:transform .2s,box-shadow .2s;
}
.file-item button:hover{
  transform:translateY(-2px);
  box-shadow:0 6px 15px rgba(255,60,60,0.3);
}

/* ======= BUTTONS ======= */
button, .back-button button{
  background:linear-gradient(90deg,#00eaff,#007bff);
  color:#001;
  border:none;
  border-radius:10px;
  font-weight:700;
  padding:12px 20px;
  cursor:pointer;
  transition:transform .3s,box-shadow .3s;
  width:100%;
  margin-top:15px;
}
button:hover, .back-button button:hover{
  transform:translateY(-3px);
  box-shadow:0 8px 25px rgba(0,255,255,0.3);
}

/* ======= FOOTER ======= */
footer{
  margin-top:25px;
  font-size:0.9rem;
  color:#a9c7d6;
}

/* ======= ANIMATIONS ======= */
@keyframes fadeIn{
  from{opacity:0;transform:translateY(30px)}
  to{opacity:1;transform:none}
}
@keyframes fadeUp{
  from{opacity:0;transform:translateY(20px)}
  to{opacity:1;transform:translateY(0)}
}
</style>
</head>
<body>

<div class="container">
<h1>Selective Scan Results</h1>

<div class="results">
<?php
// Suspicious patterns
$suspiciousPatterns = [
  '/base64_decode/i','/eval\(/i','/shell_exec\(/i',
  '/system\(/i','/php_uname\(/i','/curl_exec\(/i',
  '/exec\(/i','/passthru\(/i','/popen\(/i','/proc_open\(/i'
];

function scanFiles($files,$patterns){
  $suspicious=[];
  foreach($files['tmp_name'] as $i=>$tmp){
    if(file_exists($tmp)){
      $content=@file_get_contents($tmp);
      if($content!==false){
        foreach($patterns as $p){
          if(preg_match($p,$content)){
            $suspicious[]=$files['name'][$i];
            break;
          }
        }
      }
    }
  }
  return $suspicious;
}

if($_SERVER['REQUEST_METHOD']==='POST' && isset($_FILES['files'])){
  $suspicious=scanFiles($_FILES['files'],$suspiciousPatterns);
  if(!empty($suspicious)){
    echo "<div class='alert danger'>⚠️ Suspicious files detected:</div>";
    echo "<div class='file-list'>";
    foreach($suspicious as $f){
      echo "<div class='file-item'><span>".htmlspecialchars($f)."</span>
      <form method='post' action='delete_file.php' style='display:inline;'>
        <input type='hidden' name='file' value='".htmlspecialchars($f)."'>
        <button type='submit'>Delete</button>
      </form></div>";
    }
    echo "</div>";
  } else {
    echo "<div class='alert success'>✅ No suspicious files detected.<br>Your system is clean.</div>";
  }
} else {
  echo "<div class='alert danger'>No files were scanned. Please upload again.</div>";
}
?>
</div>

<div class="back-button">
<form action="homepage.php" method="get">
<button type="submit">← Back to Homepage</button>
</form>
</div>

<footer>
  <p>&copy; <?php echo date('Y'); ?> Hack Detection System | All Rights Reserved</p>
</footer>
</div>

</body>
</html>
