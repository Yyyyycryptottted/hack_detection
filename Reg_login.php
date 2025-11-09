<?php
session_start();
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    require 'config.php';
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username && $password) {
        $sql = "SELECT * FROM users WHERE username='$username'";
        $result = $conn->query($sql);
        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            if ($password === $row['password']) {
                $_SESSION['username'] = $username;
                echo "<div class='overlay success'>
                        <div class='msgbox'>
                          <div class='icon'>✅</div>
                          <div class='title'>Access Granted</div>
                          <div class='sub'>Redirecting...</div>
                        </div>
                      </div>
                      <script>setTimeout(()=>{window.location='homepage.php';},1500);</script>";
            } else {
                echo "<div class='overlay error'>
                        <div class='msgbox'>
                          <div class='icon'>❌</div>
                          <div class='title'>Invalid Password</div>
                          <div class='sub'>Try Again</div>
                        </div>
                      </div>
                      <script>setTimeout(()=>{document.querySelector('.overlay').remove();},1200);</script>";
            }
        } else {
            echo "<div class='overlay error'>
                    <div class='msgbox'>
                      <div class='icon'>⚠️</div>
                      <div class='title'>User Not Found</div>
                      <div class='sub'>Redirecting to Register...</div>
                    </div>
                  </div>
                  <script>setTimeout(()=>{window.location='registration.php';},1500);</script>";
        }
    } else {
        echo "<p class='alert'>Please fill in all fields.</p>";
    }
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Hack Detection System - Login</title>
<style>
*{margin:0;padding:0;box-sizing:border-box}
body{
  font-family:'Poppins',sans-serif;
  background:radial-gradient(circle at 50% 50%,#021e2f 0%,#000913 100%);
  height:100vh;
  display:flex;
  align-items:center;
  justify-content:center;
  overflow:hidden;
  color:#eafaff;
}
body::before{
  content:'';position:absolute;inset:0;
  background-image:
    linear-gradient(90deg,rgba(0,255,255,.08)1px,transparent 1px),
    linear-gradient(180deg,rgba(0,255,255,.08)1px,transparent 1px);
  background-size:60px 60px;
  animation:grid 30s linear infinite;
}
@keyframes grid{from{background-position:0 0,0 0}to{background-position:60px 60px,60px 60px}}
.container{
  position:relative;z-index:1;width:90%;max-width:420px;
  background:rgba(255,255,255,.06);border:1px solid rgba(0,255,255,.15);
  border-radius:16px;padding:40px 30px;backdrop-filter:blur(12px);
  box-shadow:0 0 35px rgba(0,255,255,.12);animation:fadeInUp 1s ease;
}
@keyframes fadeInUp{from{opacity:0;transform:translateY(40px)}to{opacity:1;transform:translateY(0)}}
h1{
  text-align:center;color:#00eaff;font-size:1.8em;
  text-shadow:0 0 15px rgba(0,255,255,.4);margin-bottom:10px;
  animation:glow 2s infinite alternate;
}
@keyframes glow{from{text-shadow:0 0 10px #00eaff}to{text-shadow:0 0 25px #00eaff}}
label{
  display:block;color:#a9c8d6;font-size:.9em;margin-top:15px;
}
input[type=text],input[type=password]{
  width:100%;padding:12px 10px;margin-top:5px;border:none;
  border-bottom:2px solid rgba(255,255,255,.25);background:transparent;
  color:#fff;font-size:1em;outline:none;transition:border-color .3s,box-shadow .3s;
}
input:focus{border-bottom-color:#00eaff;box-shadow:0 4px 14px rgba(0,230,255,.25);}
input[type=submit]{
  margin-top:25px;width:100%;background:linear-gradient(90deg,#00eaff,#007bff);
  border:none;border-radius:8px;padding:12px;color:#001;font-weight:700;
  cursor:pointer;transition:transform .2s,box-shadow .2s;
}
input[type=submit]:hover{transform:translateY(-3px);box-shadow:0 8px 24px rgba(0,255,255,.3);}
.alert{
  color:#ff5757;text-align:center;font-weight:600;margin-bottom:10px;animation:fade .5s ease;
}
@keyframes fade{from{opacity:0}to{opacity:1}}
p{text-align:center;margin-top:15px;color:#a0c4d4;}
a{color:#00eaff;text-decoration:none}
a:hover{text-decoration:underline}
.overlay{
  position:fixed;inset:0;display:flex;align-items:center;justify-content:center;
  backdrop-filter:blur(4px);z-index:10;animation:fade .5s ease;
}
.msgbox{
  background:rgba(0,0,0,.7);border:1px solid rgba(0,255,255,.3);
  border-radius:14px;padding:30px 25px;text-align:center;color:#fff;
  box-shadow:0 0 30px rgba(0,255,255,.15);transform:scale(.9);animation:pop .5s ease forwards;
}
@keyframes pop{from{opacity:0;transform:scale(.9)}to{opacity:1;transform:scale(1)}}
.msgbox .icon{font-size:40px;margin-bottom:8px}
.msgbox .title{font-size:1.3em;font-weight:700;margin-bottom:4px}
.msgbox .sub{color:#b8e9f0;font-size:.95em}
@media(max-width:480px){.container{padding:30px 20px}h1{font-size:1.5em}}
</style>
</head>
<body>
<div class="container">
  <h1>Hack Detection System</h1>
  <form method="post">
    <label for="username">Username:</label>
    <input type="text" id="username" name="username" required>

    <label for="password">Password:</label>
    <input type="password" id="password" name="password" required>

    <input type="submit" value="Login">
  </form>
  <p>Not registered? <a href="registration.php">Register here</a></p>
</div>
</body>
</html>
