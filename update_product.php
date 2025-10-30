<?php
require_once 'db.php';

// ---------- CONFIG ----------
$TABLES = ['atn','bbu','dc908','e66','ea5800','ma58','usg','ne8000','ont','osn','rtn','s12700e'];
$TEXT_FIELDS = ['Chassis_PN','Main_Board_PN','Power_Board_PN','Power_Connector_PN','Fan_PN','Cabinet_PN'];
$FILE_FIELDS = [
  'Chassis_PN_Image'        => ['image/jpeg','image/png'],
  'Main_Board_PN_Image'     => ['image/jpeg','image/png'],
  'Power_Board_PN_Image'    => ['image/jpeg','image/png'],
  'Power_Connector_PN_Image'=> ['image/jpeg','image/png'],
  'Fan_PN_Image'            => ['image/jpeg','image/png'],
  'Cabinet_PN_Image'        => ['image/jpeg','image/png'],
  'Instructivo'             => ['application/pdf'],
  'Instructivo_A'           => ['application/pdf'],
];

// ---------- HELPERS ----------
function csrf_start() {
  if (session_status() !== PHP_SESSION_ACTIVE) session_start();
  if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(32));
  return $_SESSION['csrf'];
}
function csrf_check($token) {
  if (session_status() !== PHP_SESSION_ACTIVE) session_start();
  return isset($_SESSION['csrf']) && hash_equals($_SESSION['csrf'], $token);
}
function bad_request($msg){ http_response_code(400); echo $msg; exit; }
function is_valid_table($t, $whitelist){ return in_array($t, $whitelist, true); }

function append_distinct_csv($old, $new) {
  $old = trim((string)$old);
  $new = trim((string)$new);
  if ($new === '') return $old;
  $parts = array_filter(array_map('trim', explode(',', $old)), fn($v)=>$v!=='');
  $newParts = array_filter(array_map('trim', explode(',', $new)), fn($v)=>$v!=='');
  $set = [];
  foreach (array_merge($parts, $newParts) as $p) { $set[strtoupper($p)] = $p; } // unique CI
  return implode(', ', array_values($set));
}

function sanitize_filename($name) {
  $name = preg_replace('/[^\w\.\-\s]/u', '_', $name);
  return preg_replace('/\s+/', '_', $name);
}

function save_uploaded($fieldName, $allowedTypes) {
  if (!isset($_FILES[$fieldName]) || $_FILES[$fieldName]['error'] !== UPLOAD_ERR_OK) return [null, null];
  $finfo = new finfo(FILEINFO_MIME_TYPE);
  $mime  = $finfo->file($_FILES[$fieldName]['tmp_name']);
  if (!in_array($mime, $allowedTypes, true)) return [null, "Tipo no permitido ($mime) en $fieldName"];
  if ($_FILES[$fieldName]['size'] > 30*1024*1024) return [null, "Archivo demasiado grande en $fieldName (máx 30MB)"];

  $base = sanitize_filename(pathinfo($_FILES[$fieldName]['name'], PATHINFO_FILENAME));
  $ext  = strtolower(pathinfo($_FILES[$fieldName]['name'], PATHINFO_EXTENSION));
  $destDir = __DIR__ . '/uploads';
  if (!is_dir($destDir)) mkdir($destDir, 0755, true);

  $final = $base . '__' . date('Ymd_His') . '__' . bin2hex(random_bytes(4)) . '.' . $ext;
  $destPath = $destDir . '/' . $final;
  if (!move_uploaded_file($_FILES[$fieldName]['tmp_name'], $destPath)) {
    return [null, "No se pudo mover el archivo de $fieldName"];
  }
  return ['uploads/'.$final, null]; // ruta relativa
}

// ---------- INIT ----------
$conn = getDbConnection();
$csrf = csrf_start();

// AJAX modelos (puedes seguir usando get_models.php si prefieres)
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' && ($_POST['__ajax__'] ?? '') === 'models') {
  $table = $_POST['table'] ?? '';
  if (!is_valid_table($table, $TABLES)) bad_request('Tabla inválida');
  $stmt = $conn->prepare("SELECT Model FROM `$table` ORDER BY Model ASC");
  $stmt->execute();
  $res = $stmt->get_result();
  $out = [];
  while ($row = $res->fetch_assoc()) $out[] = $row['Model'];
  header('Content-Type: application/json'); echo json_encode($out); exit;
}

// Carga de datos actuales para vista previa
$current = null;
$selectedTable = $_GET['table'] ?? $_POST['table'] ?? $TABLES[0];
$selectedModel = $_GET['Model'] ?? $_POST['Model'] ?? '';

if (is_valid_table($selectedTable, $TABLES) && $selectedModel !== '') {
  $stmt = $conn->prepare("SELECT * FROM `$selectedTable` WHERE Model = ?");
  $stmt->bind_param('s', $selectedModel);
  $stmt->execute();
  $rs = $stmt->get_result();
  $current = $rs->fetch_assoc() ?: null;
  $stmt->close();
}

// ---------- POST: UPDATE ----------
$flash = null; $flashType = 'success';

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' && ($_POST['__action__'] ?? '') === 'update') {
  if (!csrf_check($_POST['csrf'] ?? '')) bad_request('CSRF token inválido');

  $table = $_POST['table'] ?? '';
  $model = trim($_POST['Model'] ?? '');
  if (!is_valid_table($table, $TABLES)) bad_request('Tabla inválida');
  if ($model === '') bad_request('Model es requerido');

  // Trae current row para merge
  $stmt = $conn->prepare("SELECT * FROM `$table` WHERE Model = ?");
  $stmt->bind_param('s', $model);
  $stmt->execute();
  $res = $stmt->get_result();
  $row = $res->fetch_assoc();
  $stmt->close();
  if (!$row) { $flash='Modelo no encontrado en la tabla seleccionada.'; $flashType='danger'; goto render; }

  $fieldsSet = []; $params = []; $types = '';

  // ---- SIMPLIFICACIÓN: selector global de modo (text fields) ----
  // 'add' | 'replace' (default 'replace')
  $globalMode = $_POST['__global_mode__'] ?? 'replace';
  if ($globalMode !== 'add') $globalMode = 'replace';

  foreach ($TEXT_FIELDS as $f) {
    $newVal = trim($_POST[$f] ?? '');
    $mode   = $_POST[$f.'_mode'] ?? 'auto'; // 'auto' | 'add' | 'replace'
    $effMode = ($mode === 'auto') ? $globalMode : ($mode === 'add' ? 'add' : 'replace');
    if ($newVal !== '') {
      $val = ($effMode === 'add') ? append_distinct_csv($row[$f] ?? '', $newVal) : $newVal;
      $fieldsSet[] = "`$f` = ?";
      $params[] = $val; $types .= 's';
    }
  }

  // ---- SIMPLIFICACIÓN: checkbox maestro para reemplazar archivos ----
  $replaceAllFiles = isset($_POST['__files_replace_all__']) && $_POST['__files_replace_all__'] === '1';

  foreach ($FILE_FIELDS as $f => $allowed) {
    $replaceThis = $replaceAllFiles || (isset($_POST[$f.'_replace']) && $_POST[$f.'_replace'] === '1');
    if ($replaceThis && isset($_FILES[$f]) && $_FILES[$f]['error'] !== UPLOAD_ERR_NO_FILE) {
      [$path, $err] = save_uploaded($f, $allowed);
      if ($err) { $flash = $err; $flashType='danger'; goto render; }
      if ($path !== null) {
        $fieldsSet[] = "`$f` = ?";
        $params[] = $path; $types .= 's';
      }
    }
  }

  if (count($fieldsSet) > 0) {
    $sql = "UPDATE `$table` SET " . implode(', ', $fieldsSet) . " WHERE Model = ?";
    $params[] = $model; $types .= 's';
    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$params);
    if ($stmt->execute()) {
      $flash = 'Producto actualizado correctamente.'; $flashType='success';
    } else {
      $flash = 'Error al actualizar: '.$conn->error; $flashType='danger';
    }
    $stmt->close();

    // recarga datos
    $stmt = $conn->prepare("SELECT * FROM `$table` WHERE Model = ?");
    $stmt->bind_param('s', $model);
    $stmt->execute();
    $current = $stmt->get_result()->fetch_assoc() ?: null;
    $stmt->close();
  } else {
    $flash = 'No proporcionaste cambios.'; $flashType='warning';
  }
}

render:
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Actualizar producto</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <!-- Bootstrap 5 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    /* Fondo y texto base con alto contraste */
    body{background:#0b0e11;color:#f1f5f9}

    /* Contenedores */
    .navbar{background:#0a0d10}
    .navbar .navbar-brand{color:#ffffff}
    .card{background:#111418;border-color:#3a424b}

    /* ===== TÍTULOS / SUBTÍTULOS / ETIQUETAS ===== */
    h1,h2,h3,h4,h5,h6{color:#ffffff;margin-top:.25rem;margin-bottom:.75rem}
    h1{font-weight:800}
    h2{font-weight:700}
    h3,h4{font-weight:600}
    .card-header{color:#f8fafc;background:#111418;border-bottom:1px solid #3a424b}
    .form-label, label, legend{color:#eaf2ff;font-weight:600}
    .form-check-label{color:#e5e7eb}
    .hint{font-size:.9rem;color:#cbd5e1}

    /* Inputs y selects */
    .form-control, .form-select{
      background:#0c1014;
      color:#f1f5f9;
      border-color:#46505c;
    }
    .form-control::placeholder{color:#c7cdd4}
    .form-control:focus, .form-select:focus{
      color:#f8fafc;
      background:#0c1014;
      border-color:#6aa1ff;
      box-shadow:0 0 0 .25rem rgba(106,161,255,.25);
    }
    .form-select option{background:#0c1014;color:#f1f5f9}

    /* Botones */
    .btn-primary{background:#4c8dff;border:0}
    .btn-primary:hover{background:#3b7cff}
    .btn-outline-warning{border-color:#ffbf47;color:#ffbf47}
    .btn-outline-warning:hover{background:#ffbf47;color:#111418}

    /* Texto */
    .text-muted{color:#d1d5db !important}
    a{color:#9ec5ff}

    /* Tablas / celdas */
    .table-current td{vertical-align:top;color:#e5e7eb}
    .table-current tr td:first-child{color:#e2e8f0;font-weight:600}
    .table> :not(caption)>*>*{background-color:transparent;border-color:#3a424b}

    img.img-fluid.border{border-color:#3a424b !important}

    /* Alertas (dark) */
    .alert-info{color:#eaf2ff;background:#0b2a4a;border-color:#0f3b66}
    .alert-warning{color:#fffbeb;background:#3a2e0a;border-color:#7a5d12}
    .alert-danger{color:#fee2e2;background:#3a0e10;border-color:#7a1c1f}
    .alert-success{color:#e7f8ee;background:#0e2f1f;border-color:#1b5e3d}

    /* Layout de filas de campo */
    .field-row{display:grid;grid-template-columns:180px 1fr 200px;gap:.75rem;align-items:center}
    @media (max-width: 992px){ .field-row{grid-template-columns:1fr;}}

    /* Barra de acciones pegajosa */
    .sticky-actions{
      position:sticky;bottom:0;background:#0b0e11cc;
      padding:.75rem;border-top:1px solid #3a424b;backdrop-filter:blur(4px)
    }
  </style>
</head>
<body>
<nav class="navbar navbar-dark mb-4">
  <div class="container">
    <span class="navbar-brand">Catálogo · Actualizar</span>
  </div>
</nav>

<div class="container mb-5">

  <?php if($flash): ?>
    <div class="alert alert-<?=htmlspecialchars($flashType)?>"><?=htmlspecialchars($flash)?></div>
  <?php endif; ?>

  <div class="card mb-4">
    <div class="card-body">
      <form id="selector" class="row g-3" method="get" action="update_product.php">
        <div class="col-lg-4">
          <label class="form-label">Tabla</label>
          <select class="form-select" name="table" id="table" required>
            <?php foreach($TABLES as $t): ?>
              <option value="<?=$t?>" <?=$t===$selectedTable?'selected':''?>><?=$t?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-lg-6">
          <label class="form-label">Modelo</label>
          <select class="form-select" name="Model" id="Model" required>
            <?php if($selectedModel): ?>
              <option value="<?=$selectedModel?>" selected><?=$selectedModel?></option>
            <?php else: ?>
              <option value="">Seleccione primero una tabla</option>
            <?php endif; ?>
          </select>
          <div class="hint">Se cargan automáticamente los modelos de la tabla elegida.</div>
        </div>
        <div class="col-lg-2 d-flex align-items-end">
          <button class="btn btn-primary w-100" type="submit">Cargar</button>
        </div>
      </form>
    </div>
  </div>

  <?php if($current): ?>
  <div class="row g-4">
    <div class="col-lg-6">
      <div class="card h-100">
        <div class="card-header">Datos actuales</div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-sm table-current">
              <tbody>
                <?php foreach($current as $k=>$v): ?>
                  <tr>
                    <td class="text-muted"><?=$k?></td>
                    <td>
                      <?php if (preg_match('/\.(png|jpg|jpeg)$/i', (string)$v)): ?>
                        <img src="<?=htmlspecialchars($v)?>" alt="" class="img-fluid rounded border" style="max-height:140px">
                        <div class="small mt-1"><?=htmlspecialchars($v)?></div>
                      <?php elseif (preg_match('/\.pdf$/i', (string)$v)): ?>
                        <a href="<?=htmlspecialchars($v)?>" target="_blank" class="badge bg-info text-dark">Abrir PDF</a>
                        <div class="small mt-1"><?=htmlspecialchars($v)?></div>
                      <?php else: ?>
                        <div style="white-space:pre-wrap"><?=htmlspecialchars((string)$v)?></div>
                      <?php endif; ?>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
          <div class="hint">Vista solo lectura para no perder contexto al editar.</div>
        </div>
      </div>
    </div>

    <div class="col-lg-6">
      <form class="card h-100" method="post" action="update_product.php" enctype="multipart/form-data">
        <div class="card-header d-flex flex-wrap gap-3 justify-content-between align-items-center">
          <span>Editar campos</span>

          <!-- ====== CONTROLES SIMPLIFICADOS ====== -->
          <div class="d-flex align-items-center gap-2">
            <label class="form-check-label me-2">Modo global (texto):</label>
            <select class="form-select form-select-sm" name="__global_mode__" id="__global_mode__" style="width:auto">
              <option value="replace" selected>Reemplazar</option>
              <option value="add">Agregar (CSV, sin duplicados)</option>
            </select>
          </div>

          <div class="form-check">
            <input class="form-check-input" type="checkbox" value="1" id="__files_replace_all__" name="__files_replace_all__">
            <label class="form-check-label" for="__files_replace_all__">Reemplazar <b>todos</b> los archivos si subes uno</label>
          </div>
        </div>

        <div class="card-body">
          <input type="hidden" name="csrf" value="<?=$csrf?>">
          <input type="hidden" name="__action__" value="update">
          <input type="hidden" name="table" value="<?=htmlspecialchars($selectedTable)?>">
          <input type="hidden" name="Model" value="<?=htmlspecialchars($selectedModel)?>">

          <?php foreach($TEXT_FIELDS as $f): ?>
            <div class="mb-3">
              <div class="field-row">
                <label class="form-label"><?=$f?></label>
                <input class="form-control" type="text" name="<?=$f?>" placeholder="(dejar vacío para no cambiar)">
                <select class="form-select" name="<?=$f?>_mode" title="Modo por campo">
                  <option value="auto" selected>Auto (hereda global)</option>
                  <option value="add">Agregar</option>
                  <option value="replace">Reemplazar</option>
                </select>
              </div>
              <div class="hint">Actual: <?=htmlspecialchars((string)($current[$f] ?? '—'))?></div>
            </div>
          <?php endforeach; ?>

          <hr class="my-4">

          <?php foreach($FILE_FIELDS as $f=>$types): ?>
            <div class="mb-3">
              <label class="form-label"><?=$f?> <?=in_array('application/pdf',$types,true)?'(PDF)':'(PNG/JPG)'?></label>
              <input class="form-control" type="file" name="<?=$f?>" <?=in_array('application/pdf',$types,true)?'accept="application/pdf"':'accept="image/*"'?>>
              <div class="form-check mt-2">
                <input class="form-check-input file-replace" type="checkbox" value="1" id="<?=$f?>_replace" name="<?=$f?>_replace">
                <label class="form-check-label" for="<?=$f?>_replace">Reemplazar archivo actual (si subes uno).</label>
              </div>
              <div class="hint">Actual: <?=htmlspecialchars((string)($current[$f] ?? '—'))?></div>
            </div>
          <?php endforeach; ?>

        </div>
        <div class="sticky-actions">
          <button class="btn btn-primary">Guardar cambios</button>
          <a class="btn btn-outline-warning ms-2" href="index.php">Volver al inicio</a>
        </div>
      </form>
    </div>
  </div>
  <?php else: ?>
    <div class="alert alert-info">Selecciona tabla y modelo, luego pulsa “Cargar”.</div>
  <?php endif; ?>

</div>

<script>
const tableEl = document.getElementById('table');
const modelEl = document.getElementById('Model');
if (tableEl && modelEl) {
  async function loadModels() {
    const t = tableEl.value;
    modelEl.innerHTML = '<option>Cargando...</option>';
    const form = new FormData();
    form.append('__ajax__','models');
    form.append('table', t);
    const res = await fetch('update_product.php', {method:'POST', body:form});
    const models = await res.json();
    modelEl.innerHTML = '';
    models.forEach(m=>{
      const opt = document.createElement('option');
      opt.value = m; opt.textContent = m;
      if ('<?=addslashes($selectedModel)?>' === m) opt.selected = true;
      modelEl.appendChild(opt);
    });
  }
  tableEl.addEventListener('change', loadModels);
  <?php if(!$selectedModel): ?>loadModels();<?php endif; ?>
}

// Sincroniza checkbox maestro de archivos con los individuales (UX)
const masterFiles = document.getElementById('__files_replace_all__');
if (masterFiles) {
  const fileChecks = document.querySelectorAll('.file-replace');
  masterFiles.addEventListener('change', () => {
    fileChecks.forEach(chk => { chk.checked = masterFiles.checked; });
  });
}
</script>
</body>
</html>
