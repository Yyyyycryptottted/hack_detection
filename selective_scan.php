<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Upload Files for Malware Scan</title>
<style>
body {
font-family: Arial, sans-serif;
background-image: url('https://e0.pxfuel.com/wallpapers/975/785/desktop-wallpaper-electronic-background-dark-electronic.jpg'); /* Replace with the path to your image */
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
max-width: 400px;
width: 100%;
text-align: center;
}
h1, h2 {
color: #333;
margin-bottom: 20px;
}
input[type="file"] {
margin: 10px 0;
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
</style>
</head>
<body>
<div class="container">
<h1>Upload Files for Malware Scan</h1>
<form action="file_scan.php" method="post" enctype="multipart/form-data">
<label for="files">Choose files to scan:</label>
<input type="file" id="files" name="files[]" multiple>
<button type="submit">Scan Files</button>
</form>
</div>
</body>
</html>
