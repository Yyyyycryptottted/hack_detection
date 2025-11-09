<?php
$directoryToScan = 'C:/wamp/www/hd test'; // Specify the directory you want to scan
$suspiciousPatterns = array(
'/base64_decode/i', // Base64 decoding
'/eval\(/i', // Eval function
'/shell_exec\(/i', // Shell execution
'/system\(/i', // System command execution
'/php_uname\(/i', // Fetch server details
'/curl_exec\(/i', // CURL execution
'/exec\(/i', // Execute system command
'/passthru\(/i', // Execute external program
'/popen\(/i', // Opens process file pointer
'/proc_open\(/i' // Process open
);
function scanDirectory($directory, $patterns) {
$suspiciousFiles = array();
// Ensure the directory exists
if (!is_dir($directory)) {
throw new Exception("The specified directory does not exist.");
}
$files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory));
foreach ($files as $file) {
if ($file->isFile()) {
$fileContent = @file_get_contents($file->getRealPath()); // Suppress errors if the file cannot be read
if ($fileContent !== false) {
foreach ($patterns as $pattern) {
if (preg_match($pattern, $fileContent)) {
$suspiciousFiles[] = $file->getRealPath();
break;
}
}
} else {
echo "Could not read file: " . $file->getRealPath() . "\n";
}
}
}
return $suspiciousFiles;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_file'])) {
$fileToDelete = $_POST['delete_file'];
if (file_exists($fileToDelete)) {
if (unlink($fileToDelete)) {
echo "File $fileToDelete deleted successfully.<br>";
} else {
echo "Error deleting file $fileToDelete.<br>";
}
} else {
echo "File $fileToDelete does not exist.<br>";
}
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Hack Detection Alert System</title>
<style>
body {
font-family: Arial, sans-serif;
background-image: url('https://e0.pxfuel.com/wallpapers/975/785/desktop-wallpaper-electronic-background-dark-electronic.jpg'); /* Replace with your image path */
background-size: cover;
background-position: center;
background-repeat: no-repeat;
display: flex;
justify-content: center;
align-items: center;
height: 100vh;
margin: 0;
}
.container {
background-color: rgba(255, 255, 255, 0.9);
padding: 40px;
border-radius: 10px;
box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
max-width: 600px;
width: 100%;
text-align: center;
}
h1 {
color: #333;
margin-bottom: 20px;
}
.alert {
color: red;
font-weight: bold;
margin-bottom: 20px;
}
button {
background-color: #007bff;
color: white;
border: none;
padding: 10px 20px;
border-radius: 5px;
cursor: pointer;
margin-top: 20px;
box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
}
button:hover {
background-color: #0056b3;
}
.file-list {
text-align: left;
margin: 20px 0;
}
.file-list form {
display: inline-block;
margin-left: 10px;
}
.back-button {
margin-top: 20px;
}
</style>
</head>
<body>
<div class="container">
<h1>Hack Detection Alert System</h1>
<?php
try {
$suspiciousFiles = scanDirectory($directoryToScan, $suspiciousPatterns);
if (!empty($suspiciousFiles)) {
echo "<div class='alert'>Suspicious files detected:</div>";
echo "<div class='file-list'>";
foreach ($suspiciousFiles as $file) {
echo htmlspecialchars($file);
echo '<form method="post">
<input type="hidden" name="delete_file" value="' . htmlspecialchars($file) . '">
<button type="submit">Delete</button>
</form><br>';
}
echo "</div>";
} else {
echo "<div class='alert'>No suspicious files detected.</div>";
}
} catch (Exception $e) {
echo "<div class='alert'>Error: " . $e->getMessage() . "</div>";
}
?>
<div class="back-button">
<form action="homepage.php" method="get">
<button type="submit">Go Back to Homepage</button>
</form>
</div>
</div>
</body>
</html>