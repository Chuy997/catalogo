<?php
require_once 'header.php';
require_once 'db.php';

$conn = getDbConnection();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $table = $_POST['table'];
    $model = $_POST['Model'];
    $chassis_pn = $_POST['Chassis_PN'];
    $main_board_pn = $_POST['Main_Board_PN'];
    $power_board_pn = $_POST['Power_Board_PN'];
    $power_connector_pn = $_POST['Power_Connector_PN'];
    $fan_pn = $_POST['Fan_PN'];
    $cabinet_pn = $_POST['Cabinet_PN'];

    // Manejar las imágenes y el PDF
    function uploadFile($fieldName, $allowedTypes) {
        if (isset($_FILES[$fieldName]) && $_FILES[$fieldName]['error'] == UPLOAD_ERR_OK) {
            $fileType = mime_content_type($_FILES[$fieldName]['tmp_name']);
            if (in_array($fileType, $allowedTypes)) {
                $fileName = basename($_FILES[$fieldName]['name']);
                $filePath = 'uploads/' . $fileName;
                if (move_uploaded_file($_FILES[$fieldName]['tmp_name'], $filePath)) {
                    return $filePath;
                }
            }
        }
        return null;
    }

    // Subir archivos
    $chassis_pn_image = uploadFile('Chassis_PN_Image', ['image/jpeg', 'image/png']);
    $main_board_pn_image = uploadFile('Main_Board_PN_Image', ['image/jpeg', 'image/png']);
    $power_board_pn_image = uploadFile('Power_Board_PN_Image', ['image/jpeg', 'image/png']);
    $power_connector_pn_image = uploadFile('Power_Connector_PN_Image', ['image/jpeg', 'image/png']);
    $fan_pn_image = uploadFile('Fan_PN_Image', ['image/jpeg', 'image/png']);
    $cabinet_pn_image = uploadFile('Cabinet_PN_Image', ['image/jpeg', 'image/png']);
    $instructivo = uploadFile('Instructivo', ['application/pdf']);

    // Actualizar el producto existente basado en el modelo
    $sql = "UPDATE $table SET 
                `Chassis PN` = ?, 
                `Main Board PN` = ?, 
                `Power Board PN` = ?, 
                `Power Connector PN` = ?, 
                `Fan PN` = ?, 
                `Cabinet PN` = ?,
                `Chassis PN Image` = IFNULL(?, `Chassis PN Image`),
                `Main Board PN Image` = IFNULL(?, `Main Board PN Image`),
                `Power Board PN Image` = IFNULL(?, `Power Board PN Image`),
                `Power Connector PN Image` = IFNULL(?, `Power Connector PN Image`),
                `Fan PN Image` = IFNULL(?, `Fan PN Image`),
                `Cabinet PN Image` = IFNULL(?, `Cabinet PN Image`),
                `Instructivo` = IFNULL(?, `Instructivo`)
            WHERE `Model` = ?";
    $stmt = $conn->prepare($sql);

    $stmt->bind_param(
        "ssssssssssssss",
        $chassis_pn,
        $main_board_pn,
        $power_board_pn,
        $power_connector_pn,
        $fan_pn,
        $cabinet_pn,
        $chassis_pn_image,
        $main_board_pn_image,
        $power_board_pn_image,
        $power_connector_pn_image,
        $fan_pn_image,
        $cabinet_pn_image,
        $instructivo,
        $model
    );

    if ($stmt->execute()) {
        echo "Producto actualizado correctamente.";
        header('Location: index.php');
        exit;
    } else {
        echo "Error al actualizar el producto: " . $conn->error;
    }

    $stmt->close();
    $conn->close();
} else {
    echo "";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Actualizar Producto</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <main>
        <h1>Actualizar Producto</h1>
        <form action="update_product.php" method="post" enctype="multipart/form-data">
            <label for="table">Seleccione la Tabla:</label>
            <select name="table" id="table" required>
                <option value="OSN">OSN</option>
                <option value="RTN">RTN</option>
                <option value="NE8000">NE8000</option>
                <option value="BBU">BBU</option>
                <option value="S12700E">S12700E</option>
                <option value="EA5800">EA5800</option>
                <option value="MA58">MA58</option>
                <option value="ATN">ATN</option>
                <option value="DC908">DC908</option>
                <option value="E66">E66</option>
                <option value="NE40E">NE40E</option>
                <option value="ONT">ONT</option>
                
            </select><br>

            <label for="Model">Model:</label>
            <input type="text" id="Model" name="Model" required><br>

            <label for="Chassis_PN">Chassis PN:</label>
            <input type="text" id="Chassis_PN" name="Chassis_PN"><br>
            <label for="Chassis_PN_Image">Chassis PN Image:</label>
            <input type="file" id="Chassis_PN_Image" name="Chassis_PN_Image"><br>

            <label for="Main_Board_PN">Main Board PN:</label>
            <input type="text" id="Main_Board_PN" name="Main_Board_PN"><br>
            <label for="Main_Board_PN_Image">Main Board PN Image:</label>
            <input type="file" id="Main_Board_PN_Image" name="Main_Board_PN_Image"><br>

            <label for="Power_Board_PN">Power Board PN:</label>
            <input type="text" id="Power_Board_PN" name="Power_Board_PN"><br>
            <label for="Power_Board_PN_Image">Power Board PN Image:</label>
            <input type="file" id="Power_Board_PN_Image" name="Power_Board_PN_Image"><br>

            <label for="Power_Connector_PN">Power Connector PN:</label>
            <input type="text" id="Power_Connector_PN" name="Power_Connector_PN"><br>
            <label for="Power_Connector_PN_Image">Power Connector PN Image:</label>
            <input type="file" id="Power_Connector_PN_Image" name="Power_Connector_PN_Image"><br>

            <label for="Fan_PN">Fan PN:</label>
            <input type="text" id="Fan_PN" name="Fan_PN"><br>
            <label for="Fan_PN_Image">Fan PN Image:</label>
            <input type="file" id="Fan_PN_Image" name="Fan_PN_Image"><br>

            <label for="Cabinet_PN">Cabinet PN:</label>
            <input type="text" id="Cabinet_PN" name="Cabinet_PN"><br>
            <label for="Cabinet_PN_Image">Cabinet PN Image:</label>
            <input type="file" id="Cabinet_PN_Image" name="Cabinet_PN_Image"><br>

            <label for="Instructivo">Instructivo (PDF):</label>
            <input type="file" id="Instructivo" name="Instructivo" accept="application/pdf"><br>

            <button type="submit">Actualizar Producto</button>
        </form>
    </main>
</body>
</html>
