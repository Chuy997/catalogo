<?php
include 'header.php';
include 'db.php';

$conn = getDbConnection();

// Obtener las tablas y sus modelos
$tables = ['OSN', 'RTN', 'NE8000', 'BBU', 'S12700E', 'EA5800', 'MA58', 'ATN', 'DC908', 'E66', 'NE40E', 'ONT'];
$products = [];

foreach ($tables as $table) {
    $sql = "SELECT id, Model FROM $table";
    $result = $conn->query($sql);
    while ($row = $result->fetch_assoc()) {
        $products[] = ['table' => $table, 'id' => $row['id'], 'model' => $row['Model']];
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seleccionar Producto</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <main>
        <h1>Seleccionar Producto y Modelo a Actualizar</h1>
        <form action="update_product.php" method="get">
            <label for="product">Seleccione el Producto y Modelo:</label>
            <select name="id_table" id="product" required>
                <?php foreach ($products as $product): ?>
                    <option value="<?php echo $product['id'] . '_' . $product['table']; ?>">
                        <?php echo $product['table'] . ' - ' . $product['model']; ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <button type="submit">Seleccionar</button>
        </form>
    </main>
</body>
</html>
