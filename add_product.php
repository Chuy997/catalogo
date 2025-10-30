<?php
require_once 'db.php';

// --- CONFIGURACIÓN ---
$TABLES = ['atn','bbu','dc908','e66','ea5800','ma58','usg','ne8000','ont','osn','rtn','s12700e'];
$TEXT_FIELDS = ['Chassis_PN','Main_Board_PN','Power_Board_PN','Power_Connector_PN','Fan_PN','Cabinet_PN'];
$FILE_FIELDS = [
  'Chassis_PN_Image'      => ['image/jpeg','image/png'],
  'Main_Board_PN_Image'   => ['image/jpeg','image/png'],
  'Power_Board_PN_Image'  => ['image/jpeg','image/png'],
  'Power_Connector_PN_Image'=> ['image/jpeg','image/png'],
  'Fan_PN_Image'          => ['image/jpeg','image/png'],
  'Cabinet_PN_Image'      => ['image/jpeg','image/png'],
  'Instructivo'           => ['application/pdf'],
  'Instructivo_A'         => ['application/pdf'],
];

// --- CSRF Token ---
session_start();
if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(32));
$csrf = $_SESSION['csrf'];
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Agregar Producto</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body{background:#0b0e11;color:#f1f5f9}
    .navbar{background:#0a0d10}
    .navbar .navbar-brand{color:#ffffff}
    .card{background:#111418;border-color:#3a424b}
    .form-control,.form-select{background:#0c1014;color:#f1f5f9;border-color:#46505c}
    .form-control::placeholder{color:#c7cdd4}
    .form-control:focus,.form-select:focus{border-color:#6aa1ff;box-shadow:0 0 0 .25rem rgba(106,161,255,.25)}
    .btn-primary{background:#4c8dff;border:0}
    .btn-primary:hover{background:#3b7cff}
    .btn-outline-warning{border-color:#ffbf47;color:#ffbf47}
    .btn-outline-warning:hover{background:#ffbf47;color:#111418}
    label{color:#eaf2ff;font-weight:600}
    .card-header{color:#f8fafc;border-bottom:1px solid #3a424b}
    .field-row{display:grid;grid-template-columns:180px 1fr;gap:.75rem;align-items:center}
    @media (max-width: 992px){.field-row{grid-template-columns:1fr;}}
    .sticky-actions{position:sticky;bottom:0;background:#0b0e11cc;padding:.75rem;border-top:1px solid #3a424b;backdrop-filter:blur(4px)}
  </style>
</head>
<body>
<nav class="navbar navbar-dark mb-4">
  <div class="container">
    <span class="navbar-brand">Catálogo · Agregar Producto</span>
  </div>
</nav>

<div class="container mb-5">
  <form class="card shadow-lg" action="process_product.php" method="post" enctype="multipart/form-data">
    <div class="card-header">
      <h3 class="mb-0">Nuevo Producto</h3>
      <p class="small text-secondary mb-0">Complete los campos necesarios. Los campos vacíos se omitirán.</p>
    </div>
    <div class="card-body">

      <input type="hidden" name="csrf" value="<?=$csrf?>">

      <!-- Tabla -->
      <div class="mb-3">
        <label for="table" class="form-label">Seleccione la Tabla</label>
        <select name="table" id="table" class="form-select" required>
          <option value="">Seleccione una tabla...</option>
          <?php foreach ($TABLES as $t): ?>
            <option value="<?=$t?>"><?=strtoupper($t)?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <!-- Modelo -->
      <div class="mb-3">
        <label for="Model" class="form-label">Modelo</label>
        <input type="text" id="Model" name="Model" class="form-control" placeholder="Ej. ATN 9800" required>
      </div>

      <hr class="my-4">

      <!-- Campos de texto -->
      <?php foreach($TEXT_FIELDS as $f): ?>
        <div class="mb-3 field-row">
          <label class="form-label"><?=$f?></label>
          <input class="form-control" type="text" name="<?=$f?>" placeholder="(opcional)">
        </div>
      <?php endforeach; ?>

      <hr class="my-4">

      <!-- Archivos -->
      <?php foreach($FILE_FIELDS as $f=>$types): ?>
        <div class="mb-3">
          <label class="form-label"><?=$f?> <?=in_array('application/pdf',$types,true)?'(PDF)':'(PNG/JPG)'?></label>
          <input class="form-control" type="file" name="<?=$f?>" <?=in_array('application/pdf',$types,true)?'accept="application/pdf"':'accept="image/*"'?>>
        </div>
      <?php endforeach; ?>

    </div>
    <div class="sticky-actions text-end">
      <button type="submit" class="btn btn-primary px-4">Agregar Producto</button>
      <a href="index.php" class="btn btn-outline-warning ms-2">Volver</a>
    </div>
  </form>
</div>

</body>
</html>
