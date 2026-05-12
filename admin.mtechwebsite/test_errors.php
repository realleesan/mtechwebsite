<?php
/**
 * test_errors.php - Scan lỗi 500 (CHI DOC - KHONG SUA CODE)
 * Truy cap: http://admin.truongvinalogistics.com.vn/test_errors.php
 */
$baseDir = __DIR__;

// ── 1. Scan merge conflict markers ──────────────────────────────────────────
function scanConflicts(string $dir): array {
    $found    = [];
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir,
        RecursiveDirectoryIterator::SKIP_DOTS));
    foreach ($iterator as $f) {
        if (!$f->isFile()) continue;
        if (!in_array($f->getExtension(), ['php','html','js','css'])) continue;
        if ($f->getFilename() === 'test_errors.php') continue;

        $raw = file_get_contents($f->getPathname());
        // Đếm thực sự các conflict block (mỗi block có đúng 1 marker <<<<<<< )
        $count = preg_match_all('/^<{7} /m', $raw);
        if ($count > 0) {
            $lines = explode("\n", $raw);
            $nums  = [];
            foreach ($lines as $i => $line) {
                if (preg_match('/^(<{7} |={7}$|>{7} )/', $line)) {
                    $nums[] = $i + 1;
                }
            }
            $rel = str_replace([$dir . DIRECTORY_SEPARATOR, $dir . '/'], '', $f->getPathname());
            $rel = str_replace('\\', '/', $rel);
            $found[] = ['file' => $rel, 'blocks' => $count, 'lines' => $nums];
        }
    }
    return $found;
}

// ── 2. Kiểm tra PHP token (không dùng exec) ──────────────────────────────────
function checkTokens(string $dir): array {
    $errors   = [];
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir,
        RecursiveDirectoryIterator::SKIP_DOTS));
    foreach ($iterator as $f) {
        if (!$f->isFile() || $f->getExtension() !== 'php') continue;
        if ($f->getFilename() === 'test_errors.php') continue;

        $src = file_get_contents($f->getPathname());
        // Nếu có conflict marker → PHP sẽ lỗi parse
        if (preg_match('/^<{7} /m', $src)) {
            $rel = str_replace([$dir . DIRECTORY_SEPARATOR, $dir . '/'], '', $f->getPathname());
            $rel = str_replace('\\', '/', $rel);
            $errors[] = ['file' => $rel, 'reason' => 'Chứa Git conflict marker → PHP Parse Error'];
        }
    }
    return $errors;
}

// ── 3. Kiểm tra DB ───────────────────────────────────────────────────────────
function checkDB(string $dir): array {
    $env = [];
    $envFile = $dir . '/.env';
    if (!file_exists($envFile)) return ['status'=>'error','msg'=>'.env không tồn tại'];
    foreach (file($envFile, FILE_IGNORE_NEW_LINES|FILE_SKIP_EMPTY_LINES) as $line) {
        if ($line[0] === '#' || strpos($line,'=') === false) continue;
        [$k,$v] = explode('=', $line, 2);
        $env[trim($k)] = trim($v);
    }
    // Hỗ trợ cả DB_PASS và DB_PASSWORD
    $pass = $env['DB_PASSWORD'] ?? $env['DB_PASS'] ?? '';
    try {
        $port = $env['DB_PORT'] ?? '3306';
        $pdo = new PDO(
            "mysql:host={$env['DB_HOST']};port={$port};dbname={$env['DB_NAME']};charset=utf8mb4",
            $env['DB_USER'], $pass,
            [PDO::ATTR_TIMEOUT => 5, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );
        return ['status'=>'ok','msg'=>"OK → host={$env['DB_HOST']} db={$env['DB_NAME']}"];
    } catch (PDOException $e) {
        return ['status'=>'error','msg'=>$e->getMessage()];
    }
}

// ── 4. Kiểm tra controllers ──────────────────────────────────────────────────
function checkControllers(string $dir): array {
    $missing = [];
    $router  = file_get_contents($dir . '/core/router.php');
    preg_match_all("/'([A-Za-z]+Controller)@/", $router, $m);
    foreach (array_unique($m[1]) as $ctrl) {
        if (!file_exists($dir . '/app/controllers/' . $ctrl . '.php')) {
            $missing[] = $ctrl;
        }
    }
    return $missing;
}

// ── Chạy ─────────────────────────────────────────────────────────────────────
$conflicts   = scanConflicts($baseDir);
$tokenErrors = checkTokens($baseDir);
$db          = checkDB($baseDir);
$missingCtrl = checkControllers($baseDir);
$totalIssues = count($conflicts) + ($db['status']==='error'?1:0) + count($missingCtrl);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Error Scanner</title>
<style>
*{box-sizing:border-box;margin:0;padding:0}
body{font-family:'Segoe UI',sans-serif;background:#0f1117;color:#e2e8f0;padding:24px;font-size:14px}
h1{font-size:1.4rem;color:#fff;margin-bottom:4px}
.sub{color:#718096;font-size:.8rem;margin-bottom:20px}
.row{display:flex;gap:10px;flex-wrap:wrap;margin-bottom:20px}
.pill{padding:8px 16px;border-radius:20px;font-weight:700;font-size:.82rem}
.red{background:#742a2a;color:#fc8181;border:1px solid #fc8181}
.green{background:#1a4731;color:#68d391;border:1px solid #68d391}
.yellow{background:#744210;color:#f6ad55;border:1px solid #f6ad55}
.card{background:#1a202c;border:1px solid #2d3748;border-radius:8px;margin-bottom:16px;overflow:hidden}
.card-head{padding:12px 18px;border-bottom:1px solid #2d3748;display:flex;align-items:center;gap:8px;font-weight:600}
.card-body{padding:14px 18px}
.item{background:#2d3748;border-radius:6px;padding:10px 14px;margin-bottom:8px;border-left:3px solid #fc8181}
.item.y{border-left-color:#f6ad55}
.item.g{border-left-color:#68d391}
.fname{color:#fbd38d;font-weight:700;font-size:.85rem;margin-bottom:3px}
.detail{color:#a0aec0;font-size:.8rem;line-height:1.5}
.lnum{color:#68d391;font-size:.78rem;margin-top:3px}
.ok{color:#68d391}.err{color:#fc8181}.warn{color:#f6ad55}
table{width:100%;border-collapse:collapse;font-size:.8rem}
th{background:#2d3748;color:#a0aec0;padding:7px 12px;text-align:left}
td{padding:7px 12px;border-bottom:1px solid #2d3748;color:#e2e8f0;vertical-align:top}
.tag{display:inline-block;padding:1px 7px;border-radius:10px;font-size:.72rem;font-weight:700}
.tag-err{background:#742a2a;color:#fc8181}
.tag-ok{background:#1a4731;color:#68d391}
.root{background:#2d1515;border:1px solid #fc8181;border-radius:8px;padding:14px 18px;margin-bottom:18px}
.root h3{color:#fc8181;margin-bottom:8px}
.root p{font-size:.85rem;line-height:1.7;color:#e2e8f0}
code{background:#1a202c;padding:1px 5px;border-radius:3px;color:#f6ad55;font-size:.8rem}
</style>
</head>
<body>

<h1>🔍 Error Scanner — Admin Panel</h1>
<p class="sub">CHỈ ĐỌC · Không sửa code · <?= date('d/m/Y H:i:s') ?></p>

<div class="row">
  <div class="pill <?= $totalIssues>0?'red':'green' ?>"><?= $totalIssues>0?"⚠ $totalIssues vấn đề":'✓ Không có lỗi' ?></div>
  <div class="pill <?= count($conflicts)>0?'red':'green' ?>">Merge Conflicts: <?= count($conflicts) ?> file</div>
  <div class="pill <?= $db['status']==='ok'?'green':'red' ?>">Database: <?= $db['status']==='ok'?'✓ OK':'✗ Lỗi' ?></div>
  <div class="pill <?= count($missingCtrl)>0?'yellow':'green' ?>">Controllers thiếu: <?= count($missingCtrl) ?></div>
</div>

<?php if (count($conflicts) > 0): ?>
<div class="root">
  <h3>🔴 Nguyên nhân lỗi 500</h3>
  <p>
    <strong><?= count($conflicts) ?> file</strong> chứa <strong>Git Merge Conflict markers</strong>
    (<code>&lt;&lt;&lt;&lt;&lt;&lt;&lt; HEAD</code> / <code>=======</code> / <code>&gt;&gt;&gt;&gt;&gt;&gt;&gt;</code>).
    PHP không parse được → <strong>Fatal Parse Error → HTTP 500</strong> trên toàn bộ trang dùng file đó.
  </p>
  <p style="margin-top:8px">
    <strong>Nguyên nhân gốc:</strong> Commit <code>2b781d4</code> là một <strong>merge commit</strong> —
    khi merge 2 branch, conflict chưa được resolve mà đã commit thẳng lên repository.
    Đây không phải do bạn chỉnh sửa thủ công.
  </p>
</div>
<?php endif; ?>

<!-- MERGE CONFLICTS -->
<div class="card">
  <div class="card-head">💥 Git Merge Conflicts
    <span class="<?= count($conflicts)>0?'err':'ok' ?>">(<?= count($conflicts) ?> file)</span>
  </div>
  <div class="card-body">
    <?php if (empty($conflicts)): ?>
      <p class="ok">✓ Không có conflict</p>
    <?php else: ?>
      <?php foreach ($conflicts as $c): ?>
      <div class="item">
        <div class="fname">📄 <?= htmlspecialchars($c['file']) ?></div>
        <div class="detail">Conflict blocks: <strong class="err"><?= $c['blocks'] ?></strong></div>
        <div class="lnum">Dòng có markers: <?= implode(', ', $c['lines']) ?></div>
      </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>
</div>

<!-- DATABASE -->
<div class="card">
  <div class="card-head">🗄️ Database
    <span class="<?= $db['status']==='ok'?'ok':'err' ?>">(<?= strtoupper($db['status']) ?>)</span>
  </div>
  <div class="card-body">
    <div class="item <?= $db['status']==='ok'?'g':'' ?>">
      <div class="detail <?= $db['status']==='ok'?'ok':'err' ?>"><?= htmlspecialchars($db['msg']) ?></div>
    </div>
  </div>
</div>

<!-- MISSING CONTROLLERS -->
<div class="card">
  <div class="card-head">🎮 Controllers thiếu
    <span class="<?= count($missingCtrl)>0?'warn':'ok' ?>">(<?= count($missingCtrl) ?>)</span>
  </div>
  <div class="card-body">
    <?php if (empty($missingCtrl)): ?>
      <p class="ok">✓ Tất cả controllers tồn tại</p>
    <?php else: ?>
      <?php foreach ($missingCtrl as $c): ?>
      <div class="item y">
        <div class="fname">🎮 <?= htmlspecialchars($c) ?>.php</div>
        <div class="detail warn">File không tồn tại: app/controllers/<?= htmlspecialchars($c) ?>.php</div>
      </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>
</div>

<!-- BẢNG TẤT CẢ PHP FILES -->
<div class="card">
  <div class="card-head">📁 Tất cả PHP files — Trạng thái</div>
  <div class="card-body">
    <table>
      <thead><tr><th>File</th><th>Conflict</th><th>Trạng thái</th></tr></thead>
      <tbody>
      <?php
      $conflictFiles = array_column($conflicts, 'file');
      $iter = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($baseDir, RecursiveDirectoryIterator::SKIP_DOTS));
      foreach ($iter as $f) {
          if (!$f->isFile() || $f->getExtension() !== 'php') continue;
          if ($f->getFilename() === 'test_errors.php') continue;
          $rel = str_replace([$baseDir.DIRECTORY_SEPARATOR, $baseDir.'/'], '', $f->getPathname());
          $rel = str_replace('\\', '/', $rel);
          $hasConflict = in_array($rel, $conflictFiles);
      ?>
        <tr>
          <td style="color:#a0aec0"><?= htmlspecialchars($rel) ?></td>
          <td><?= $hasConflict ? '<span class="tag tag-err">✗ Conflict</span>' : '<span class="tag tag-ok">✓ OK</span>' ?></td>
          <td><?= $hasConflict ? '<span class="tag tag-err">⚠ 500 Risk</span>' : '<span class="tag tag-ok">✓ OK</span>' ?></td>
        </tr>
      <?php } ?>
      </tbody>
    </table>
  </div>
</div>

<p style="color:#4a5568;font-size:.75rem;margin-top:16px;text-align:center">
  ⚠ Xóa file này sau khi debug xong.
</p>
</body>
</html>
