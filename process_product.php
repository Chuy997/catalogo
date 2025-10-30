<?php
include 'db.php';

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

    // Verificar y crear el directorio uploads si no existe
    if (!is_dir('uploads')) {
        mkdir('uploads', 0777, true);
    }

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

    $chassis_pn_image = uploadFile('Chassis_PN_Image', ['image/jpeg', 'image/png']);
    $main_board_pn_image = uploadFile('Main_Board_PN_Image', ['image/jpeg', 'image/png']);
    $power_board_pn_image = uploadFile('Power_Board_PN_Image', ['image/jpeg', 'image/png']);
    $power_connector_pn_image = uploadFile('Power_Connector_PN_Image', ['image/jpeg', 'image/png']);
    $fan_pn_image = uploadFile('Fan_PN_Image', ['image/jpeg', 'image/png']);
    $cabinet_pn_image = uploadFile('Cabinet_PN_Image', ['image/jpeg', 'image/png']);
    $instructivo = uploadFile('Instructivo', ['application/pdf']);
    $instructivo_a = uploadFile('Instructivo_A', ['application/pdf']);

    // Insertar nuevo producto con imágenes opcionales y PDF
    $sql = "INSERT INTO $table 
                (Model, Chassis_PN, Main_Board_PN, Power_Board_PN, Power_Connector_PN, Fan_PN, Cabinet_PN, Chassis_PN_Image, Main_Board_PN_Image, Power_Board_PN_Image, Power_Connector_PN_Image, Fan_PN_Image, Cabinet_PN_Image, Instructivo, Instructivo_A) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);

    $stmt->bind_param(
        "sssssssssssssss",
        $model,
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
        $instructivo_a
    );

    if ($stmt->execute()) {
        echo "Producto agregado correctamente.";
        header('Location: index.php');
        exit;
    } else {
        echo "Error al agregar el producto: " . $conn->error;
    }

    $stmt->close();
    $conn->close();
} else {
    echo "Método no permitido.";
}
?>
