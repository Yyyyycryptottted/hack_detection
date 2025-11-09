<?php
// custom_defender_scan.php
// Runs Windows Defender scans on paths you supply (one per line).
// WARNING: This executes PowerShell on the host. Read the notes in the file header above before running.

session_start();
if (!isset($_SESSION['username'])) {
    // Optional: enforce your login flow
    // header('Location: login.php'); exit;
    $_SESSION['username'] = 'localuser';
}
$username = htmlspecialchars($_SESSION['username']);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'scan') {
    // AJAX endpoint for running Defender scans
    header('Content-Type: application/json; charset=utf-8');

    $raw = trim($_POST['paths'] ?? '');
    if ($raw === '') {
        echo json_encode(['ok'=>false,'error'=>'No paths provided']);
        exit;
    }

    // Split and normalize
    $lines = preg_split("/\r\n|\n|\r/", $raw);
    $paths = [];
    foreach ($lines as $ln) {
        $p = trim($ln);
        if ($p !== '') $paths[] = $p;
    }
    if (count($paths) === 0) {
        echo json_encode(['ok'=>false,'error'=>'No valid paths found']);
        exit;
    }

    // Validation
    $badPattern = '/(crack|keygen|patch|piracy|warez|torrent)/i';
    $allowed = [];
    $rejected = [];
    foreach ($paths as $p) {
        if (!preg_match('#^[A-Za-z]:\\\\#', $p)) {
            $rejected[] = ['path'=>$p,'reason'=>'Not an absolute Windows path'];
            continue;
        }
        if (preg_match($badPattern, $p)) {
            $rejected[] = ['path'=>$p,'reason'=>'Disallowed keywords (crack/keygen/patch)'];
            continue;
        }
        $allowed[] = $p;
    }

    if (count($allowed) === 0) {
        echo json_encode(['ok'=>false,'error'=>'No allowed paths to scan','rejected'=>$rejected]);
        exit;
    }

    // Helper to run powershell. Returns array with exit and output lines.
    function run_powershell_cmd($psCmd) {
        // Compose a safe command
        $full = 'powershell.exe -NoProfile -ExecutionPolicy Bypass -Command ' . escapeshellarg($psCmd);
        // Capture both stdout and stderr
        exec($full . " 2>&1", $out, $code);
        return ['exit'=>$code, 'output'=>$out];
    }

    $out = [];
    // Update signatures first (best-effort)
    $out['update'] = run_powershell_cmd('Update-MpSignature');

    $out['scans'] = [];
    foreach ($allowed as $p) {
        // Run custom scan
        // Note: Start-MpScan -ScanPath accepts files or folders. It runs synchronously.
        $ps = "Start-MpScan -ScanPath " . escapeshellarg($p) . " -Verbose";
        $scanRes = run_powershell_cmd($ps);

        // Collect also a compact status snapshot
        $statusRes = run_powershell_cmd('Get-MpComputerStatus | Select-Object AMServiceEnabled,RealTimeProtectionEnabled,AntispywareEnabled,AntivirusSignatureLastUpdated | ConvertTo-Json -Compress');

        $out['scans'][] = [
            'path'=>$p,
            'command'=>$ps,
            'scan'=> $scanRes,
            'status' => $statusRes
        ];
    }

    // Return JSON
    echo json_encode(['ok'=>true,'allowed'=>$allowed,'rejected'=>$rejected,'results'=>$out], JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES);
    exit;
}
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width,initial-scale=1" />
<title>Custom Defender Scan — Hack-o-Meter</title>
<style>
:root{--bg:#02172a;--card:rgba(255,255,255,0.06);--accent:#00eaff;--danger:#ff4444;--safe:#00ffa3}
*{box-sizing:border-box;margin:0;padding:0}
body{font-family:Inter,Segoe UI,Roboto,Arial,sans-serif;background:linear-gradient(135deg,var(--bg),#00385f);color:#e8f9ff;min-height:100vh;display:flex;align-items:flex-start;justify-content:center;padding:28px}
.container{width:100%;max-width:980px;background:var(--card);backdrop-filter:blur(12px);border-radius:14px;padding:22px;box-shadow:0 10px 40px rgba(0,0,0,0.6)}
.header{display:flex;justify-content:space-between;align-items:center;margin-bottom:12px}
.header h1{color:var(--accent);font-size:1.2rem}
.controls{display:flex;gap:10px;align-items:center}
textarea{width:100%;height:120px;border-radius:8px;border:none;padding:12px;background:rgba(0,0,0,0.2);color:#eaffff;resize:vertical}
.row{display:flex;gap:12px;margin-top:12px}
.col{flex:1}
.btn{background:linear-gradient(90deg,var(--accent),#007bff);color:#001;border:none;padding:10px 14px;border-radius:8px;font-weight:700;cursor:pointer}
.btn.ghost{background:transparent;border:1px solid rgba(255,255,255,0.08);color:#cfeff6}
.stage{margin-top:16px;padding:14px;border-radius:10px;background:rgba(0,0,0,0.14)}
.progress-bar{height:12px;background:rgba(255,255,255,0.08);border-radius:8px;overflow:hidden}
.progress{height:100%;width:0;background:linear-gradient(90deg,var(--accent),#00ffa3);transition:width .35s}
.status{margin-top:8px;color:var(--accent);font-weight:700}
.results{margin-top:18px;display:grid;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));gap:12px}
.card{background:rgba(0,0,0,0.12);padding:14px;border-radius:10px}
.file-name{font-weight:800;color:#fff;margin-bottom:6px}
.meta{font-size:0.9rem;color:#bfefff;margin-bottom:10px}
.hackometer{width:90px;height:90px;display:inline-block;vertical-align:middle}
.score{font-weight:900;font-size:1.05rem;margin-left:8px;vertical-align:middle}
.badge{display:inline-block;padding:6px 8px;border-radius:999px;font-weight:700}
.badge.safe{background:var(--safe);color:#002}
.badge.low{background:#5a7;color:#032}
.badge.moderate{background:#f5a623;color:#321}
.badge.high{background:var(--danger);color:#fff}
.detect-list{margin-top:8px}
.detect-item{background:rgba(255,255,255,0.03);padding:8px;border-radius:6px;margin-bottom:6px;font-size:0.9rem}
.suggestions{margin-top:10px;background:rgba(255,255,255,0.03);padding:10px;border-radius:8px;font-size:0.9rem}
.footer{margin-top:16px;text-align:right;color:#9fcddb}
.note{margin-top:10px;color:#cfeef0;font-size:0.92rem}
.warning{
  background:rgba(255, 170, 0, 0.15);
  border-left:4px solid #ffaa00;
  color:#ffcc66;
  padding:10px;
  border-radius:8px;
  margin-top:10px;
  display:none;
}
pre.output{background:#071826;color:#bfefff;padding:10px;border-radius:8px;max-height:220px;overflow:auto;font-size:0.86rem}
@media(max-width:780px){.row{flex-direction:column}}
</style>
</head>
<body>
<div class="container" role="main" aria-labelledby="title">
  <div class="header">
    <h1 id="title">Custom Defender Scan — Hack-o-Meter</h1>
    <div class="controls">
      <button class="btn ghost" onclick="loadExample()">Load Example</button>
      <button class="btn" id="startBtn" onclick="beginScan()">Run Defender Scan</button>
    </div>
  </div>

  <label style="font-weight:700">Enter absolute Windows paths (one per line). Example:</label>
  <textarea id="fileInput" placeholder="D:\Games\The Witcher 3 - Complete Edition\REDprelauncher.exe&#10;D:\Games\The Witcher 3 - Complete Edition\sqlite.dll"></textarea>

  <div id="fileWarning" class="warning"></div>

  <div class="row">
    <div class="col">
      <div class="stage">
        <div class="progress-bar" aria-hidden="true"><div class="progress" id="globalProgress"></div></div>
        <div class="status" id="stageText">Idle</div>
        <div class="note">This will call Windows Defender locally. Server must have permission to run Defender cmdlets.</div>
      </div>
    </div>
    <div style="width:320px">
      <div class="stage">
        <strong>Legend</strong>
        <div style="margin-top:8px" class="note">
          <div><span class="badge high">HIGH</span> — likely malicious</div>
          <div><span class="badge moderate">MODERATE</span> — suspicious</div>
          <div><span class="badge low">LOW</span> — low risk</div>
          <div><span class="badge safe">SAFE</span> — no issues</div>
        </div>
      </div>
    </div>
  </div>

  <div id="results" class="results" aria-live="polite"></div>

  <div class="footer">Requested by <strong><?php echo $username ?></strong></div>
</div>

<script>
function loadExample(){
  document.getElementById('fileInput').value = `D:\\Games\\The Witcher 3 - Complete Edition\\REDprelauncher.exe
D:\\Games\\The Witcher 3 - Complete Edition\\sqlite.dll`;
}

function beginScan(){
  const raw = document.getElementById('fileInput').value.trim();
  if(!raw){ alert('Paste at least one absolute Windows path.'); return; }

  // Basic client-side validation and show skipped file warning
  const lines = raw.split(/\r\n|\n|\r/).map(s=>s.trim()).filter(Boolean);
  const validExt = /\.(exe|dll|sys|scr|bat|com)$/i;
  const invalids = lines.filter(f => !validExt.test(f));
  const warningBox = document.getElementById('fileWarning');
  if(invalids.length > 0){
    warningBox.style.display = "block";
    warningBox.innerHTML = "⚠ These files have non-executable extensions and will be sent for Defender scan anyway (server may skip):<br>" + invalids.map(f=>`• ${f}`).join('<br>');
  } else {
    warningBox.style.display = "none";
  }

  // Clear UI and show initial state
  document.getElementById('results').innerHTML = '';
  document.getElementById('globalProgress').style.width = '4%';
  document.getElementById('stageText').textContent = 'Updating Defender signatures...';

  // Send AJAX request to server to run scans
  const form = new FormData();
  form.append('action','scan');
  form.append('paths', raw);

  fetch(window.location.href, { method: 'POST', body: form })
    .then(r => r.json())
    .then(data => {
      if(!data.ok){
        alert('Scan failed: ' + (data.error || 'Unknown error'));
        console.error(data);
        return;
      }
      renderResults(data);
    })
    .catch(err => {
      alert('Request error. Check server logs and permissions.');
      console.error(err);
    });
}

function renderResults(data){
  const results = data.results;
  const scans = results.scans || [];
  const container = document.getElementById('results');
  container.innerHTML = '';

  // Show update output
  const updCard = document.createElement('div'); updCard.className = 'card';
  updCard.innerHTML = `<div class="file-name">Update-MpSignature (definitions update)</div><div><pre class="output">${escapeHtml((results.update.output || []).join("\n"))}</pre></div>`;
  container.appendChild(updCard);

  // For each scan show output and attempt to interpret
  scans.forEach((s,i) => {
    const card = document.createElement('div'); card.className = 'card';
    const path = s.path;
    const outLines = (s.scan.output || []).join("\n");
    const exit = s.scan.exit;
    // Interpret: look for keywords indicating detection
    const lc = outLines.toLowerCase();
    let category = 'safe'; // safe/low/moderate/high
    let summary = 'No threats reported by Defender.';
    if (lc.indexOf('threat') !== -1 || lc.indexOf('detected') !== -1 || lc.indexOf('quarantin') !== -1 || exit !== 0) {
      category = 'high';
      summary = 'Defender reported suspicious or malicious findings. See raw output below.';
    } else if (lc.indexOf('unknown') !== -1 || lc.indexOf('suspicious') !== -1 || lc.indexOf('warning') !== -1) {
      category = 'moderate';
      summary = 'Defender returned warnings/suspicious indicators. Review output.';
    } else {
      category = 'safe';
    }

    // Hack-o-meter score heuristic (simple)
    let score = 12;
    if (category === 'high') score = 92;
    else if (category === 'moderate') score = 64;
    else score = 12;

    card.innerHTML = `
      <div class="file-name">${escapeHtml(path)}</div>
      <div class="meta">Exit code: ${exit} &middot; Interpreted category: <strong>${category.toUpperCase()}</strong></div>
      <div style="display:flex;align-items:center">
        ${createHackometer(score)}
        <div style="margin-left:10px"><div class="score">${score}</div><div id="badge-${i}"></div></div>
      </div>
      <div class="detect-list">
        <div class="detect-item"><strong>Summary:</strong> ${escapeHtml(summary)}</div>
      </div>
      <div style="margin-top:10px"><strong>Raw Defender output:</strong><pre class="output">${escapeHtml(outLines)}</pre></div>
    `;
    container.appendChild(card);

    // Suggestions block
    const suggest = document.createElement('div');
    suggest.className = 'suggestions';
    if (category === 'high') {
      suggest.innerHTML = `<strong>Suggested actions</strong><ul><li>Disconnect machine from network if possible</li><li>Quarantine the file using Defender or remove it</li><li>Collect logs and submit sample to sandbox or vendor</li><li>Run full system scan</li></ul>`;
    } else if (category === 'moderate') {
      suggest.innerHTML = `<strong>Suggested actions</strong><ul><li>Run a full Defender scan</li><li>Cross-check file hash on VirusTotal</li><li>Monitor for unusual behavior</li></ul>`;
    } else {
      suggest.innerHTML = `<strong>Suggested actions</strong><ul><li>No immediate action required</li><li>Keep AV signatures up to date</li><li>Run periodic full scans</li></ul>`;
    }
    container.appendChild(suggest);

    // update global progress
    const progressPercent = Math.round(((i+1)/scans.length)*100);
    document.getElementById('globalProgress').style.width = progressPercent + '%';
    document.getElementById('stageText').textContent = `Scanned ${i+1} of ${scans.length}`;
  });

  // Show rejected list if any
  if (data.rejected && data.rejected.length) {
    const rej = document.createElement('div'); rej.className = 'card';
    rej.innerHTML = `<div class="file-name">Rejected paths</div><div><pre class="output">${escapeHtml(JSON.stringify(data.rejected, null, 2))}</pre></div>`;
    container.appendChild(rej);
  }

  document.getElementById('stageText').textContent = 'All scans finished';
  document.getElementById('globalProgress').style.width = '100%';
}

/* Small helpers for client UI */
function escapeHtml(s){ return String(s).replace(/[&<>"']/g, m=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[m])); }
function createHackometer(score){
  const size=90; const stroke=10;
  const r=(size-stroke)/2; const circ=2*Math.PI*r; const offset=circ*(1-score/100);
  return `<svg class="hackometer" viewBox="0 0 ${size} ${size}" width="${size}" height="${size}"><defs><linearGradient id="g${score}" x1="0%" y1="0%" x2="100%" y2="0%"><stop offset="0%" stop-color="#00eaff"/><stop offset="100%" stop-color="#00ffa3"/></linearGradient></defs><circle cx="${size/2}" cy="${size/2}" r="${r}" stroke="rgba(255,255,255,0.06)" stroke-width="${stroke}" fill="none"></circle><circle cx="${size/2}" cy="${size/2}" r="${r}" stroke="url(#g${score})" stroke-width="${stroke}" stroke-linecap="round" fill="none" stroke-dasharray="${circ}" stroke-dashoffset="${offset}" transform="rotate(-90 ${size/2} ${size/2})"></circle></svg>`;
}
</script>
</body>
</html>
