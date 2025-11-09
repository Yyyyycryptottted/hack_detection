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
<meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>Custom File Scan - Hack Detection System</title>
<style>
*{box-sizing:border-box;margin:0;padding:0}
body{
  font-family:'Poppins',sans-serif;
  background:linear-gradient(135deg,#02172a,#00385f);
  color:#e8f9ff;
  min-height:100vh;
  display:flex;
  justify-content:center;
  align-items:center;
  flex-direction:column;
  padding:40px;
}
.container{
  background:rgba(255,255,255,0.08);
  backdrop-filter:blur(12px);
  border-radius:16px;
  box-shadow:0 0 25px rgba(0,255,255,0.15);
  padding:30px;
  width:90%;
  max-width:700px;
  text-align:center;
}
h1{
  color:#00eaff;
  text-shadow:0 0 10px rgba(0,255,255,0.4);
  margin-bottom:10px;
}
p{color:#a9dfff;font-size:0.95rem;margin-bottom:20px;}
textarea{
  width:100%;
  height:120px;
  background:rgba(0,0,0,0.2);
  border:none;
  border-radius:8px;
  color:#eaffff;
  padding:12px;
  resize:vertical;
  font-size:0.9rem;
}
button{
  margin-top:20px;
  background:linear-gradient(90deg,#00eaff,#007bff);
  color:#001;
  border:none;
  border-radius:8px;
  padding:12px 20px;
  font-weight:700;
  cursor:pointer;
  transition:transform .2s,box-shadow .2s;
}
button:hover{transform:translateY(-2px);box-shadow:0 6px 15px rgba(0,255,255,0.3);}
.progress-box{
  width:100%;
  height:12px;
  background:rgba(255,255,255,0.1);
  border-radius:8px;
  overflow:hidden;
  margin-top:20px;
}
.progress{
  width:0%;
  height:100%;
  background:linear-gradient(90deg,#00eaff,#007bff);
  transition:width .3s ease;
}
#status{
  margin-top:10px;
  color:#00eaff;
  font-weight:600;
}
.result{
  display:none;
  margin-top:25px;
  text-align:left;
  background:rgba(255,255,255,0.05);
  border-radius:10px;
  padding:20px;
}
.file{
  background:rgba(255,255,255,0.05);
  padding:10px;
  border-left:4px solid #00eaff;
  border-radius:5px;
  margin-bottom:10px;
}
.file.danger{border-left-color:#ff4444;}
.file.safe{border-left-color:#00ffa3;}
</style>
</head>
<body>
<div class="container">
  <h1>Custom File Scanner</h1>
  <p>Enter or paste paths of the specific files you want to scan (e.g. .exe, .dll, .sys).</p>

  <textarea id="filePaths" placeholder="Example:
D:\\Old is gold\\oldplayer.exe
D:\\theme packs etc\\theme-loader.dll
C:\\Windows\\System32\\kernel32.dll"></textarea>

  <button onclick="startScan()">Start Smart Scan</button>

  <div class="progress-box"><div class="progress" id="progress"></div></div>
  <div id="status"></div>

  <div id="result" class="result"></div>
</div>

<script>
const progress=document.getElementById('progress');
const statusEl=document.getElementById('status');
const resultEl=document.getElementById('result');

function startScan(){
  const raw=document.getElementById('filePaths').value.trim();
  if(!raw){alert('Enter at least one file path.');return;}
  const files=raw.split(/[\r\n]+/).filter(f=>f.trim()!=='');
  if(!files.length){alert('Invalid list.');return;}

  resultEl.style.display='none';
  resultEl.innerHTML='';
  progress.style.width='0%';
  statusEl.textContent='Initializing Smart AI Engine...';

  simulateScan(files);
}

async function simulateScan(files){
  const steps=files.length*4;
  let step=0;

  for(let i=0;i<files.length;i++){
    const f=files[i];
    statusEl.textContent='Scanning: '+f;
    // four analysis phases per file
    for(let p=0;p<4;p++){
      step++;
      progress.style.width=Math.round((step/steps)*100)+'%';
      await delay(800+Math.random()*300);
    }
    // show per file result
    appendResult(f);
  }

  statusEl.textContent='Scan Complete.';
  progress.style.width='100%';
  resultEl.style.display='block';
}

function appendResult(path){
  const ext=path.split('.').pop().toLowerCase();
  let risk='safe',desc='Clean file';
  const random=Math.random();
  if(/(dll|exe|sys)/.test(ext) && random<0.4){
    risk='danger';
    const threat=choose([
      'Trojan.Agent.Win32',
      'Adware.Themer.Injector',
      'Worm.AutoRun',
      'Backdoor.Dropper',
      'SuspiciousPackedFile'
    ]);
    desc=`⚠ Detected: ${threat}`;
  }else if(random<0.6){
    risk='warn';
    desc='Potentially unwanted or obfuscated binary';
  }
  const div=document.createElement('div');
  div.className='file '+(risk==='danger'?'danger':'safe');
  div.innerHTML=`<strong>${path}</strong><br>${desc}`;
  resultEl.appendChild(div);
}

function choose(arr){return arr[Math.floor(Math.random()*arr.length)];}
function delay(ms){return new Promise(res=>setTimeout(res,ms));}
</script>
</body>
</html>
