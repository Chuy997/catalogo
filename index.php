<?php
require_once 'db.php';

// Asumiendo que tienes una función para obtener los nombres de las tablas
$tables = ['OSN', 'RTN', 'NE8000', 'BBU', 'S12700E', 'EA5800', 'MA58', 'ATN', 'DC908', 'E66', 'USG','ONT'];

// Si hay un código SW enviado por POST, realizar la validación
$validationMessage = '';
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['sw_code'])) {
    $sw_code = $_POST['sw_code'];
    $conn = getDbConnection(); // Función definida en db.php

    // Cambiar a la base de datos software_db
    $conn->select_db('software_db');

    // Consulta a la tabla 'software' en la base de datos 'software_db'
    $sql = "SELECT product FROM software WHERE sw_code = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $sw_code);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // SW code encontrado
        $row = $result->fetch_assoc();
        $validationMessage = '<span class="success-message">Código SW encontrado. Producto: ' . $row['product'] . '</span>';
    } else {
        // SW code no encontrado
        $validationMessage = '<span class="error-message">Código SW no encontrado.</span>';
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catalogo ATO</title>
    <link rel="stylesheet" href="stylesa.css">
</head>
<body>
    <header>
        <nav>
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="add_product.php">Agregar Producto</a></li>
                <li><a href="update_product.php">Actualizar Producto</a></li>
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

        <!-- Formulario de validación de SW code 
        <div class="validation-form">
            <form method="POST" action="index.php">
                <label for="sw_code">Ingrese SW code:</label>
                <input type="text" id="sw_code" name="sw_code" required>
                <button type="submit">Validacion</button>
            </form>
            <?php
            // Mostrar el mensaje de validación si existe
            if (!empty($validationMessage)) {
                echo $validationMessage;
            }
            ?>
        </div> -->
    </main>
</body>
</html>
<?php
require_once 'footer.php';
?>
