<?php
session_start();

// Load language
$languagePath = __DIR__ . '/includes/language_loader.php';
if (file_exists($languagePath)) include($languagePath);

// Load theme
$themePath = __DIR__ . '/includes/theme_loader.php';
if (file_exists($themePath)) include($themePath);

$theme = $_SESSION['theme'] ?? 'dark';

// Directory to scan
$directoryToScan = 'C:\xampp\htdocs\hack_detection';

$suspiciousPatterns = array(
  '/base64_decode/i',
  '/eval\(/i',
  '/shell_exec\(/i',
  '/system\(/i',
  '/php_uname\(/i',
  '/curl_exec\(/i',
  '/exec\(/i',
  '/passthru\(/i',
  '/popen\(/i',
  '/proc_open\(/i'
);

function scanDirectory($directory, $patterns) {
  $suspiciousFiles = array();
  if (!is_dir($directory)) throw new Exception("The specified directory does not exist.");
  $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory));
  foreach ($files as $file) {
    if ($file->isFile()) {
      $fileContent = @file_get_contents($file->getRealPath());
      if ($fileContent !== false) {
        foreach ($patterns as $pattern) {
          if (preg_match($pattern, $fileContent)) {
            $suspiciousFiles[] = $file->getRealPath();
            break;
          }
        }
      }
    }
  }
  return $suspiciousFiles;
}

$messages = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_file'])) {
  $fileToDelete = $_POST['delete_file'];
  $realBase = realpath($directoryToScan);
  $realTarget = realpath($fileToDelete);
  if ($realTarget && strpos($realTarget, $realBase) === 0 && file_exists($realTarget)) {
    if (@unlink($realTarget)) {
      $messages[] = ['type' => 'success', 'text' => $lang['deleted'] . ": " . htmlspecialchars($realTarget)];
    } else {
      $messages[] = ['type' => 'error', 'text' => $lang['delete_failed'] . ": " . htmlspecialchars($realTarget)];
    }
  } else {
    $messages[] = ['type' => 'error', 'text' => $lang['invalid_path'] . ": " . htmlspecialchars($fileToDelete)];
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>HD Alert — <?php echo $lang['hack_detection']; ?></title>

<!-- Fonts and Icons -->
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700;900&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>
:root {
  --bg-dark: #030812;
  --accent: #00f3ff;
  --accent-2: #007bff;
  --danger: #ff5252;
  --glass: rgba(255, 255, 255, 0.08);
  --text-light: #d9f9ff;
  --muted: #9aa4b2;
  --radius: 20px;
  font-family: 'Inter', sans-serif;
}

/* ================== GLOBAL ================== */
* { box-sizing: border-box; margin: 0; padding: 0; }
html, body { height: 100%; }

body {
  background: radial-gradient(circle at 20% 20%, rgba(0,255,255,0.12), transparent 40%), 
              radial-gradient(circle at 80% 80%, rgba(0,100,255,0.08), transparent 40%), 
              var(--bg-dark);
  color: var(--text-light);
  display: flex;
  align-items: center;
  justify-content: center;
  overflow: hidden;
  perspective: 1500px;
  animation: bgflow 10s ease-in-out infinite alternate;
}
@keyframes bgflow {
  0% { background-position: 0% 0%; }
  100% { background-position: 100% 100%; }
}

/* ================== APP CONTAINER ================== */
.app {
  display: grid;
  grid-template-columns: 340px 1fr;
  gap: 25px;
  padding: 32px;
  border-radius: var(--radius);
  background: rgba(10, 20, 35, 0.7);
  box-shadow: 0 0 60px rgba(0, 255, 255, 0.08), inset 0 0 25px rgba(0, 255, 255, 0.04);
  backdrop-filter: blur(25px);
  transform-style: preserve-3d;
  transform: rotateX(6deg) rotateY(2deg);
  animation: floatApp 6s ease-in-out infinite alternate;
}
@keyframes floatApp {
  0%,100% { transform: rotateX(5deg) rotateY(2deg) translateY(0); }
  50% { transform: rotateX(7deg) rotateY(-2deg) translateY(-10px); }
}

/* ================== SIDEBAR ================== */
.sidebar {
  background: rgba(255,255,255,0.05);
  border-radius: var(--radius);
  padding: 22px;
  box-shadow: inset 0 0 25px rgba(0,255,255,0.05);
  animation: slideLeft 1s ease forwards;
}
@keyframes slideLeft {
  from { opacity: 0; transform: translateX(-25px); }
  to { opacity: 1; transform: translateX(0); }
}

.logo {
  display: flex; align-items: center; gap: 14px; margin-bottom: 16px;
}
.logo .icon {
  width: 60px; height: 60px; border-radius: 18px;
  background: linear-gradient(135deg, var(--accent), var(--accent-2));
  color: #02121a; font-size: 22px;
  display: flex; align-items: center; justify-content: center;
  box-shadow: 0 0 25px rgba(0,255,255,0.4);
  animation: glowPulse 3s infinite alternate ease-in-out;
}
@keyframes glowPulse {
  0% { box-shadow: 0 0 15px rgba(0,255,255,0.3); }
  100% { box-shadow: 0 0 30px rgba(0,255,255,0.7); }
}

.logo h2 {
  margin: 0;
  font-weight: 700;
  color: var(--accent);
  text-shadow: 0 0 15px rgba(0,255,255,0.5);
}
.logo p { font-size: 13px; color: var(--muted); }

.metric {
  background: rgba(0,255,255,0.07);
  border: 1px solid rgba(0,255,255,0.15);
  border-radius: 14px;
  padding: 14px;
  text-align: center;
  box-shadow: 0 0 15px rgba(0,255,255,0.1);
  transition: 0.3s ease;
}
.metric:hover {
  transform: translateY(-4px);
  box-shadow: 0 0 30px rgba(0,255,255,0.3);
}

/* ================== MAIN CONTENT ================== */
.content {
  background: rgba(255,255,255,0.04);
  border-radius: var(--radius);
  padding: 24px;
  box-shadow: inset 0 0 25px rgba(0,255,255,0.05);
  animation: fadeInUp 1.2s ease forwards;
  opacity: 0;
}
@keyframes fadeInUp {
  from { opacity: 0; transform: translateY(20px); }
  to { opacity: 1; transform: translateY(0); }
}

.header-row {
  display: flex; justify-content: space-between; align-items: center;
  border-bottom: 1px solid rgba(255,255,255,0.1);
  padding-bottom: 12px; margin-bottom: 18px;
}
.header-row h1 {
  color: var(--accent);
  text-shadow: 0 0 8px rgba(0,255,255,0.6);
  font-weight: 700;
  font-size: 1.6em;
}
.header-row p { color: var(--muted); font-size: 0.9em; }

.btn {
  border: none;
  border-radius: 10px;
  padding: 10px 16px;
  font-weight: 600;
  cursor: pointer;
  transition: 0.3s;
}
.btn:hover { transform: translateY(-3px); }
.btn.primary {
  background: linear-gradient(90deg, var(--accent), var(--accent-2));
  color: #001;
  box-shadow: 0 0 20px rgba(0,255,255,0.3);
}
.btn.ghost {
  border: 1px solid rgba(255,255,255,0.2);
  color: var(--text-light);
  background: transparent;
}
.btn.danger {
  background: linear-gradient(90deg,#ff5c5c,#ff3b3b);
  color: #fff;
  box-shadow: 0 0 25px rgba(255,0,0,0.3);
}

/* ================== FILE CARDS ================== */
.scan-panel {
  border-radius: 14px;
  background: rgba(255,255,255,0.05);
  border: 1px solid rgba(255,255,255,0.1);
  padding: 18px;
  max-height: 400px;
  overflow-y: auto;
}
.file {
  display: flex; justify-content: space-between; align-items: center;
  padding: 14px;
  border-radius: 12px;
  background: rgba(255,255,255,0.04);
  border: 1px solid rgba(255,255,255,0.07);
  margin-bottom: 10px;
  transform: translateY(10px);
  opacity: 0;
  animation: fileFloat 0.6s ease forwards;
}
@keyframes fileFloat {
  to { opacity: 1; transform: translateY(0); }
}
.file:hover { transform: scale(1.02); box-shadow: 0 0 20px rgba(0,255,255,0.2); }

.badge {
  width: 45px; height: 45px;
  border-radius: 10px;
  background: rgba(255,80,80,0.1);
  color: var(--danger);
  display: flex; align-items: center; justify-content: center;
  border: 1px solid rgba(255,80,80,0.3);
}

/* ================== FOOTER ================== */
.footer {
  margin-top: 16px;
  color: var(--muted);
  font-size: 13px;
  text-align: center;
  border-top: 1px solid rgba(255,255,255,0.1);
  padding-top: 12px;
  animation: fadeIn 1.2s ease forwards;
}
@keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
</style>
</head>

<body class="<?php echo $theme; ?>">
  <div class="app">
    <aside class="sidebar">
      <div class="logo">
        <div class="icon"><i class="fa-solid fa-shield-halved"></i></div>
        <div>
          <h2>HD Alert</h2>
          <p><?php echo $lang['hack_detection_console']; ?></p>
        </div>
      </div>

      <div class="metric">
        <div class="n"><?php echo count(scanDirectory($directoryToScan, $suspiciousPatterns)); ?></div>
        <div class="l"><?php echo $lang['suspicious']; ?></div>
      </div>

      <div class="metric">
        <div class="n">
          <?php
          $totalFiles = 0;
          $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directoryToScan));
          foreach ($it as $f) if ($f->isFile()) $totalFiles++;
          echo $totalFiles;
          ?>
        </div>
        <div class="l"><?php echo $lang['files_scanned']; ?></div>
      </div>

      <div style="margin-top: 16px;">
        <strong><?php echo $lang['scan_settings']; ?></strong>
        <div style="font-size: 13px; margin-top: 6px;">
          <?php echo $lang['directory']; ?>:
          <div style="margin-top: 6px; background: rgba(255,255,255,0.05); border-radius: 8px; padding: 10px;">
            <?php echo htmlspecialchars($directoryToScan); ?>
          </div>
        </div>
      </div>
    </aside>

    <main class="content">
      <div class="header-row">
        <div>
          <h1><?php echo $lang['scan_results']; ?></h1>
          <p><?php echo $lang['auto_pattern_scan']; ?></p>
        </div>
        <div>
          <button id="exportBtn" class="btn primary"><i class="fa-solid fa-file-export"></i> <?php echo $lang['export']; ?></button>
          <a href="homepage.php" class="btn ghost"><i class="fa-solid fa-house"></i> <?php echo $lang['home']; ?></a>
        </div>
      </div>

      <section class="scan-panel">
        <?php
        try {
          $suspiciousFiles = scanDirectory($directoryToScan, $suspiciousPatterns);
          if ($suspiciousFiles) {
            foreach ($suspiciousFiles as $file) {
              $safeFile = htmlspecialchars($file);
              echo "
              <div class='file'>
                <div class='left'>
                  <div class='badge'><i class='fa-solid fa-fire'></i></div>
                  <div class='meta'>
                    <div class='path' title='{$safeFile}'>{$safeFile}</div>
                    <div class='small'>{$lang['pattern_match']}</div>
                  </div>
                </div>
                <form method='post' onsubmit=\"return confirmDelete(event, '{$safeFile}')\">
                  <input type='hidden' name='delete_file' value='{$safeFile}'>
                  <button type='submit' class='btn danger'><i class='fa-solid fa-trash'></i> {$lang['delete']}</button>
                </form>
              </div>";
            }
          } else {
            echo "<div style='text-align:center; color:var(--muted); padding:30px 0;'>
                    <i class='fa-regular fa-circle-check' style='font-size:22px;'></i><br>{$lang['no_suspicious']}
                  </div>";
          }
        } catch (Exception $e) {
          echo "<div style='text-align:center;color:red;'>{$e->getMessage()}</div>";
        }
        ?>
      </section>

      <div class="footer">
        <?php echo $lang['scan_rules']; ?>: base64_decode, eval, shell_exec, system, curl_exec, exec, passthru, popen, proc_open<br>
        <?php echo $lang['last_scan']; ?>: <?php echo date('Y-m-d H:i:s'); ?>
      </div>
    </main>
  </div>

<script>
function confirmDelete(evt, file) {
  evt.preventDefault();
  if (!confirm("<?php echo $lang['confirm_delete']; ?>\n\n" + file)) return false;
  const btn = evt.target.querySelector('button[type="submit"]');
  btn.disabled = true;
  btn.textContent = '<?php echo $lang['deleting']; ?>...';
  setTimeout(() => evt.target.submit(), 200);
  return false;
}

document.getElementById('exportBtn').addEventListener('click', () => {
  const paths = [...document.querySelectorAll('.path')].map(n => n.textContent.trim());
  if (!paths.length) return alert('<?php echo $lang['no_files_export']; ?>');
  const blob = new Blob([paths.join("\n")], { type: 'text/plain' });
  const a = document.createElement('a');
  a.href = URL.createObjectURL(blob);
  a.download = 'hdalert_report_' + new Date().toISOString().replace(/[:T]/g, '-') + '.txt';
  a.click();
});
</script>
</body>
</html>
