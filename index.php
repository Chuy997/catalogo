<?php
require_once 'db.php';

// Asumiendo que tienes una función para obtener los nombres de las tablas
$tables = ['OSN', 'RTN', 'NE8000', 'BBU', 'S12700E', 'EA5800', 'MA58', 'ATN', 'DC908', 'E66', 'NE40E','ONT'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Página Principal</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <nav>
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="add_product.php">Agregar Producto</a></li>
                <li><a href="update_product.php">Actualizar Producto</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </nav>
    </header>
    <main>
        <h1>Catalogo ATO</h1>
        <div class="product-grid">
            <?php foreach ($tables as $table): ?>
                <div class="product-item">
                    <a href="<?php echo strtolower($table); ?>.php"><?php echo $table; ?></a>
                </div>
            <?php endforeach; ?>
        </div>
    </main>
</body>
</html>
<?php
require_once 'footer.php'
?>