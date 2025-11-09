<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Hack Detection - Registration</title>
<style>
*{margin:0;padding:0;box-sizing:border-box}
body{
  font-family:'Poppins',sans-serif;
  height:100vh;
  display:flex;
  align-items:center;
  justify-content:center;
  overflow:hidden;
  background:linear-gradient(135deg,#02131e,#001e32,#003a5c);
  background-size:400% 400%;
  animation:bgFlow 12s ease infinite;
}
@keyframes bgFlow{0%{background-position:0% 50%}50%{background-position:100% 50%}100%{background-position:0% 50%}}
body::before{
  content:'';position:fixed;inset:0;
  background-image:linear-gradient(90deg,rgba(0,255,255,0.08) 1px,transparent 1px),
  linear-gradient(180deg,rgba(0,255,255,0.08) 1px,transparent 1px);
  background-size:60px 60px;animation:gridMove 40s linear infinite;pointer-events:none;
}
@keyframes gridMove{from{background-position:0 0,0 0}to{background-position:60px 60px,60px 60px}}
.container{
  position:relative;z-index:1;background:rgba(255,255,255,0.06);
  backdrop-filter:blur(14px);border-radius:18px;border:1px solid rgba(0,255,255,0.15);
  padding:40px 35px;width:90%;max-width:420px;color:#e9f9ff;
  box-shadow:0 0 30px rgba(0,255,255,0.1);transform:translateY(30px);
  opacity:0;animation:fadeUp 1s forwards;
}
@keyframes fadeUp{to{opacity:1;transform:translateY(0)}}
h1{
  text-align:center;font-size:1.8em;color:#00eaff;text-shadow:0 0 18px rgba(0,255,255,0.5);
  margin-bottom:20px;animation:textGlow 2s infinite alternate;
}
@keyframes textGlow{from{text-shadow:0 0 8px #00eaff}to{text-shadow:0 0 25px #00eaff}}
label{display:block;font-size:.9em;color:#a9cbe6;margin-top:12px;}
input[type="text"],input[type="email"],input[type="password"]{
  width:100%;padding:12px 10px;margin-top:6px;border:none;
  border-bottom:2px solid rgba(255,255,255,0.25);background:transparent;
  color:#fff;font-size:1em;outline:none;transition:border-color .3s, box-shadow .3s;
}
input:focus{border-bottom-color:#00eaff;box-shadow:0 6px 18px rgba(0,230,255,0.15);}
input[type="submit"]{
  width:100%;margin-top:25px;background:linear-gradient(90deg,#00eaff,#007bff);
  color:#001;font-weight:700;border:none;border-radius:8px;padding:12px;cursor:pointer;
  box-shadow:0 6px 20px rgba(0,200,255,0.2);transition:transform .2s, box-shadow .2s;
}
input[type="submit"]:hover{transform:translateY(-3px);box-shadow:0 10px 30px rgba(0,255,255,0.35);}
.container::after{
  content:'';position:absolute;top:-2px;left:-2px;right:-2px;bottom:-2px;
  border-radius:20px;background:linear-gradient(60deg,#00eaff,#007bff,#00ffa3,#00eaff);
  background-size:300% 300%;z-index:-1;filter:blur(20px);opacity:.15;animation:flow 6s linear infinite;
}
@keyframes flow{0%{background-position:0% 50%}50%{background-position:100% 50%}100%{background-position:0% 50%}}
.alert{margin-top:15px;color:#ff6b6b;text-align:center;animation:fadeIn .5s ease;}
@keyframes fadeIn{from{opacity:0}to{opacity:1}}
@media(max-width:480px){.container{padding:30px 20px}h1{font-size:1.5em}}
</style>
</head>
<body>
<div class="container">
<h1>Create Account</h1>

<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  require 'config.php';

  $username = trim($_POST['username'] ?? '');
  $email = trim($_POST['email'] ?? '');
  $password = $_POST['password'] ?? '';

  if ($username && $email && $password) {
    // Directly store the entered password (no hash, as requested)
    $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $username, $email, $password);

    if ($stmt->execute()) {
      echo "<p class='alert' style='color:#00ffa3;'>Registration Successful! Redirecting...</p>";
      echo "<script>setTimeout(()=>{window.location.href='Reg_login.php';},1500);</script>";
    } else {
      echo "<p class='alert'>Username or Email already exists.</p>";
    }

    $stmt->close();
    $conn->close();
  } else {
    echo "<p class='alert'>Please fill out all fields.</p>";
  }
}
?>

<form method="post">
  <label for="username">Username</label>
  <input type="text" id="username" name="username" required>

  <label for="email">Email</label>
  <input type="email" id="email" name="email" required>

  <label for="password">Password</label>
  <input type="password" id="password" name="password" required>

  <input type="submit" value="Register">
</form>
</div>
</body>
</html>
