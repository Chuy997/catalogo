<?php
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_table_name = $_POST['new_table_name'];
    $conn = getDbConnection();

    // Crear la nueva tabla en la base de datos
    $sql = "CREATE TABLE IF NOT EXISTS `$new_table_name` (
        id INT AUTO_INCREMENT PRIMARY KEY,
        Model VARCHAR(100) NOT NULL,
        `Chassis PN` VARCHAR(100) DEFAULT NULL,
        `Chassis PN Image` BLOB DEFAULT NULL,
        `Main Board PN` VARCHAR(100) DEFAULT NULL,
        `Main Board PN Image` BLOB DEFAULT NULL,
        `Power Board PN` VARCHAR(100) DEFAULT NULL,
        `Power Board PN Image` BLOB DEFAULT NULL,
        `Power Connector PN` VARCHAR(100) DEFAULT NULL,
        `Power Connector PN Image` BLOB DEFAULT NULL,
        `Fan PN` VARCHAR(100) DEFAULT NULL,
        `Fan PN Image` BLOB DEFAULT NULL,
        `Cabinet PN` VARCHAR(100) DEFAULT NULL,
        `Cabinet PN Image` BLOB DEFAULT NULL
    )";
    
    if ($conn->query($sql) === TRUE) {
        echo "Nueva tabla creada exitosamente.";
        
        // Crear el archivo PHP para la nueva tabla
        $new_php_content = "<?php\ninclude 'header.php';\n\n\$conn = getDbConnection();\n\$sql = \"SELECT * FROM $new_table_name\";\n\$result = \$conn->query(\$sql);\n?>\n\n<h1>$new_table_name Productos</h1>\n<table>\n    <thead>\n        <tr>\n            <th>Model</th>\n            <th>Chassis PN</th>\n            <th>Main Board PN</th>\n            <th>Power Board PN</th>\n            <th>Power Connector PN</th>\n            <th>Fan PN</th>\n            <th>Cabinet PN</th>\n        </tr>\n    </thead>\n    <tbody>\n        <?php while (\$row = \$result->fetch_assoc()): ?>\n            <tr>\n                <td><?php echo \$row['Model']; ?></td>\n                <td><?php echo \$row['Chassis PN']; ?></td>\n                <td><?php echo \$row['Main Board PN']; ?></td>\n                <td><?php echo \$row['Power Board PN']; ?></td>\n                <td><?php echo \$row['Power Connector PN']; ?></td>\n                <td><?php echo \$row['Fan PN']; ?></td>\n                <td><?php echo \$row['Cabinet PN']; ?></td>\n            </tr>\n        <?php endwhile; ?>\n    </tbody>\n</table>\n\n<?php include 'footer.php'; ?>";

        $new_php_file = fopen("$new_table_name.php", "w");
        fwrite($new_php_file, $new_php_content);
        fclose($new_php_file);

        // Actualizar index.php para incluir el nuevo producto
        $index_content = file_get_contents('index.php');
        $new_link = "<a href=\"$new_table_name.php\">$new_table_name</a>\n";
        $index_content = str_replace('</div>', $new_link . '</div>', $index_content);
        file_put_contents('index.php', $index_content);
        
    } else {
        echo "Error al crear la tabla: " . $conn->error;
    }

    $conn->close();
}
?>
