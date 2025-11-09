<?php
session_start();
require 'config.php'; 

$alert_html = '';
$overlay_html = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        $alert_html = "<p id='fieldAlert' class='alert error'>Please fill in all fields.</p>";
    } else {
        $stmt = $conn->prepare("SELECT id, username, password FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $res = $stmt->get_result();

        if ($res && $res->num_rows > 0) {
            $row = $res->fetch_assoc();
            $stored = $row['password'];

            $valid = false;
            if ($stored !== null && $stored !== '') {
                if (password_verify($password, $stored) || $password === $stored) {
                    $valid = true;
                }
            }

            if ($valid) {
                $_SESSION['username'] = $row['username'];
                $overlay_html = "
                <div class='overlay success' id='overlay'>
                  <div class='message-box'>
                    <div class='icon'>✅</div>
                    <div class='title'>Login Successful</div>
                    <div class='sub'>Redirecting to homepage...</div>
                  </div>
                </div>
                <script>setTimeout(()=>{window.location.href='homepage.php';},1400);</script>";
            } else {
                $overlay_html = "
                <div class='overlay error' id='overlay'>
                  <div class='message-box'>
                    <div class='icon'>❌</div>
                    <div class='title'>Invalid Password</div>
                    <div class='sub'>Please try again</div>
                  </div>
                </div>
                <script>setTimeout(()=>{document.getElementById('overlay').remove();},1400);</script>";
            }
        } else {
            $overlay_html = "
            <div class='overlay error' id='overlay'>
              <div class='message-box'>
                <div class='icon'>⚠️</div>
                <div class='title'>No User Found</div>
                <div class='sub'>Redirecting to registration...</div>
              </div>
            </div>
            <script>setTimeout(()=>{window.location.href='registration.php';},1600);</script>";
        }

        $stmt->close();  // close once
        $conn->close();  // close connection once
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Hack Detection System - Login</title>
<style>
/* identical visual format to other HD pages */
*{box-sizing:border-box;margin:0;padding:0}
html,body{height:100%}
body{
  font-family:'Poppins',sans-serif;
  background:linear-gradient(135deg,#041423 0%,#001827 100%);
  display:flex;align-items:center;justify-content:center;
  color:#e6f7ff;padding:24px;
}
.container{
  width:100%;max-width:420px;
  background:rgba(255,255,255,0.06);
  border:1px solid rgba(0,230,255,0.08);
  backdrop-filter:blur(10px);
  padding:34px;border-radius:14px;
  box-shadow:0 12px 40px rgba(0,0,0,0.45);
  animation:cardIn .8s cubic-bezier(.2,.9,.25,1) forwards;
}
.header{text-align:center;margin-bottom:18px}
.header h1{
  color:#00e5ff;font-size:1.6rem;margin-bottom:6px;
  text-shadow:0 0 12px rgba(0,230,255,0.18);
}
.header p{color:#bcd7e6;font-size:.95rem}
form{margin-top:12px}
label{display:block;color:#a9c7d6;font-size:.92rem;margin-bottom:6px}
.input{
  width:100%;padding:12px 10px;margin-bottom:14px;
  border-radius:8px;border:1px solid rgba(255,255,255,0.06);
  background:rgba(255,255,255,0.02);color:#eaf7ff;
}
.input:focus{
  box-shadow:0 8px 24px rgba(0,200,255,0.06);
  border-color:rgba(0,230,255,0.36);
}
.submit{
  width:100%;padding:12px;border-radius:10px;border:none;
  cursor:pointer;font-weight:700;
  background:linear-gradient(90deg,#00e5ff,#007bff);
  color:#001;box-shadow:0 10px 30px rgba(0,130,255,0.12);
}
.footer{margin-top:12px;text-align:center;color:#a9c7d6;font-size:.92rem}
.footer a{color:#aeefff;text-decoration:none}
.alert{
  margin:10px 0 14px;padding:10px 12px;border-radius:8px;
  font-weight:600;text-align:center;
}
.alert.error{
  color:#ff5b5b;background:rgba(255,80,80,0.06);
  border:1px solid rgba(255,90,90,0.12);
}
.overlay{
  position:fixed;inset:0;display:flex;align-items:center;justify-content:center;
  backdrop-filter:blur(4px);z-index:999;
}
.overlay .message-box{
  background:rgba(0,0,0,0.6);border-radius:12px;
  padding:28px 26px;text-align:center;color:#fff;
  box-shadow:0 8px 30px rgba(0,0,0,0.6);
  transform:scale(.9);animation:pop .5s cubic-bezier(.2,.9,.25,1) forwards;
}
.message-box .icon{font-size:46px;margin-bottom:8px}
.message-box .title{font-size:1.25rem;font-weight:700;margin-bottom:6px}
.message-box .sub{color:#cfeef0;font-weight:600;opacity:.95}
@keyframes cardIn{from{opacity:0;transform:translateY(30px)}to{opacity:1;transform:none}}
@keyframes pop{from{transform:scale(.9);opacity:0}to{transform:scale(1);opacity:1}}
</style>
</head>
<body>

<div class="container">
  <div class="header">
    <h1>Hack Detection System</h1>
    <p>Securely monitor and detect potential hacks</p>
  </div>

  <?php echo $alert_html; ?>

  <form method="post">
    <label for="username">Username</label>
    <input id="username" name="username" class="input" type="text" required>
    <label for="password">Password</label>
    <input id="password" name="password" class="input" type="password" required>
    <input type="submit" class="submit" value="Login">
  </form>

  <div class="footer">
    <p>Not registered? <a href="registration.php">Create an account</a></p>
  </div>
</div>

<?php echo $overlay_html; ?>

</body>
</html>
