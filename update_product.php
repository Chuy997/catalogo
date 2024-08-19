<?php
require_once 'header.php';
require_once 'db.php';

$conn = getDbConnection();

// Obtener la lista de tablas para el dropdown
$tables = ['atn', 'bbu', 'dc908', 'e66', 'ea5800', 'ma58', 'ne40e', 'ne8000', 'ont', 'osn', 'rtn', 's12700e'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $table = $_POST['table'];
    $model = $_POST['Model'];

    if (in_array($table, $tables)) {
        // Consultar la información actual del modelo seleccionado
        $sql = "SELECT * FROM $table WHERE Model = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('s', $model);
        $stmt->execute();
        $result = $stmt->get_result();
        $current_data = $result->fetch_assoc();

        if ($result->num_rows > 0) {
            $fields = [];
            $params = [];
            $types = '';

            // Recoger los datos del formulario y agregar solo los campos que han sido proporcionados
            if (!empty($_POST['Chassis_PN'])) {
                $fields[] = "Chassis_PN = ?";
                $params[] = $_POST['Chassis_PN'];
                $types .= 's';
            }
            if (!empty($_POST['Main_Board_PN'])) {
                $fields[] = "Main_Board_PN = ?";
                $params[] = $_POST['Main_Board_PN'];
                $types .= 's';
            }
            if (!empty($_POST['Power_Board_PN'])) {
                $fields[] = "Power_Board_PN = ?";
                $params[] = $_POST['Power_Board_PN'];
                $types .= 's';
            }
            if (!empty($_POST['Power_Connector_PN'])) {
                $fields[] = "Power_Connector_PN = ?";
                $params[] = $_POST['Power_Connector_PN'];
                $types .= 's';
            }
            if (!empty($_POST['Fan_PN'])) {
                $fields[] = "Fan_PN = ?";
                $params[] = $_POST['Fan_PN'];
                $types .= 's';
            }
            if (!empty($_POST['Cabinet_PN'])) {
                $fields[] = "Cabinet_PN = ?";
                $params[] = $_POST['Cabinet_PN'];
                $types .= 's';
            }

            // Subir archivos o mantener los existentes si no se proporcionan nuevos
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

            if ($chassis_pn_image = uploadFile('Chassis_PN_Image', ['image/jpeg', 'image/png'])) {
                $fields[] = "Chassis_PN_Image = ?";
                $params[] = $chassis_pn_image;
                $types .= 's';
            }
            if ($main_board_pn_image = uploadFile('Main_Board_PN_Image', ['image/jpeg', 'image/png'])) {
                $fields[] = "Main_Board_PN_Image = ?";
                $params[] = $main_board_pn_image;
                $types .= 's';
            }
            if ($power_board_pn_image = uploadFile('Power_Board_PN_Image', ['image/jpeg', 'image/png'])) {
                $fields[] = "Power_Board_PN_Image = ?";
                $params[] = $power_board_pn_image;
                $types .= 's';
            }
            if ($power_connector_pn_image = uploadFile('Power_Connector_PN_Image', ['image/jpeg', 'image/png'])) {
                $fields[] = "Power_Connector_PN_Image = ?";
                $params[] = $power_connector_pn_image;
                $types .= 's';
            }
            if ($fan_pn_image = uploadFile('Fan_PN_Image', ['image/jpeg', 'image/png'])) {
                $fields[] = "Fan_PN_Image = ?";
                $params[] = $fan_pn_image;
                $types .= 's';
            }
            if ($cabinet_pn_image = uploadFile('Cabinet_PN_Image', ['image/jpeg', 'image/png'])) {
                $fields[] = "Cabinet_PN_Image = ?";
                $params[] = $cabinet_pn_image;
                $types .= 's';
            }
            if ($instructivo = uploadFile('Instructivo', ['application/pdf'])) {
                $fields[] = "Instructivo = ?";
                $params[] = $instructivo;
                $types .= 's';
            }
            if ($instructivo_a = uploadFile('Instructivo_A', ['application/pdf'])) {
                $fields[] = "Instructivo_A = ?";
                $params[] = $instructivo_a;
                $types .= 's';
            }

            if (!empty($fields)) {
                // Construir la consulta SQL con los campos dinámicos
                $sql = "UPDATE $table SET " . implode(', ', $fields) . " WHERE Model = ?";
                $params[] = $model;
                $types .= 's';

                $stmt = $conn->prepare($sql);
                $stmt->bind_param($types, ...$params);

                if ($stmt->execute()) {
                    echo "Producto actualizado correctamente.";
                    header('Location: index.php');
                    exit;
                } else {
                    echo "Error al actualizar el producto: " . $conn->error;
                }

                $stmt->close();
            } else {
                echo "No se han proporcionado datos para actualizar.";
            }
        } else {
            echo "Modelo no encontrado en la tabla seleccionada.";
        }
        $stmt->close();
    } else {
        echo "Tabla seleccionada no es válida.";
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Actualizar Producto</title>
    <link rel="stylesheet" href="styles.css">
    <script>
        function loadModels() {
            var table = document.getElementById("table").value;
            var modelSelect = document.getElementById("Model");

            // Limpiar el campo de selección de modelos
            modelSelect.innerHTML = "<option value=''>Cargando...</option>";

            var xhr = new XMLHttpRequest();
            xhr.open("POST", "get_models.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.onreadystatechange = function() {
                if (xhr.readyState === XMLHttpRequest.DONE && xhr.status === 200) {
                    var models = JSON.parse(xhr.responseText);
                    modelSelect.innerHTML = "";

                    models.forEach(function(model) {
                        var option = document.createElement("option");
                        option.value = model;
                        option.text = model;
                        modelSelect.appendChild(option);
                    });
                }
            };
            xhr.send("table=" + table);
        }
    </script>
</head>
<body>
    <main>
        <h1>Actualizar Producto</h1>
        <form action="update_product.php" method="post" enctype="multipart/form-data">
            <label for="table">Seleccione la Tabla:</label>
            <select name="table" id="table" required onchange="loadModels()">
                <?php
                foreach ($tables as $table) {
                    echo "<option value=\"$table\">$table</option>";
                }
                ?>
            </select><br>

            <label for="Model">Seleccione el Modelo:</label>
            <select name="Model" id="Model" required>
                <option value="">Seleccione primero una tabla</option>
            </select><br>

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

            <label for="Instructivo_A">Instructivo Ensamble (PDF):</label>
            <input type="file" id="Instructivo_A" name="Instructivo_A" accept="application/pdf"><br>

            <button type="submit">Actualizar Producto</button>
        </form>
    </main>
</body>
</html>
