<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Producto</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <main>
        <h1>Agregar Producto</h1>
        <form action="process_product.php" method="post" enctype="multipart/form-data">
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
            </select><br>

            <label for="Model">Model:</label>
            <input type="text" id="Model" name="Model" required><br>

            <label for="Chassis_PN">Chassis PN:</label>
            <input type="text" id="Chassis_PN" name="Chassis_PN"><br>
            <label for="Chassis_PN_Image">Chassis PN Image:</label>
            <input type="file" id="Chassis_PN_Image" name="Chassis_PN_Image" accept="image/*"><br>

            <label for="Main_Board_PN">Main Board PN:</label>
            <input type="text" id="Main_Board_PN" name="Main_Board_PN"><br>
            <label for="Main_Board_PN_Image">Main Board PN Image:</label>
            <input type="file" id="Main_Board_PN_Image" name="Main_Board_PN_Image" accept="image/*"><br>

            <label for="Power_Board_PN">Power Board PN:</label>
            <input type="text" id="Power_Board_PN" name="Power_Board_PN"><br>
            <label for="Power_Board_PN_Image">Power Board PN Image:</label>
            <input type="file" id="Power_Board_PN_Image" name="Power_Board_PN_Image" accept="image/*"><br>

            <label for="Power_Connector_PN">Power Connector PN:</label>
            <input type="text" id="Power_Connector_PN" name="Power_Connector_PN"><br>
            <label for="Power_Connector_PN_Image">Power Connector PN Image:</label>
            <input type="file" id="Power_Connector_PN_Image" name="Power_Connector_PN_Image" accept="image/*"><br>

            <label for="Fan_PN">Fan PN:</label>
            <input type="text" id="Fan_PN" name="Fan_PN"><br>
            <label for="Fan_PN_Image">Fan PN Image:</label>
            <input type="file" id="Fan_PN_Image" name="Fan_PN_Image" accept="image/*"><br>

            <label for="Cabinet_PN">Cabinet PN:</label>
            <input type="text" id="Cabinet_PN" name="Cabinet_PN"><br>
            <label for="Cabinet_PN_Image">Cabinet PN Image:</label>
            <input type="file" id="Cabinet_PN_Image" name="Cabinet_PN_Image" accept="image/*"><br>

            <label for="Instructivo">Instructivo (PDF):</label>
            <input type="file" id="Instructivo" name="Instructivo" accept="application/pdf"><br>

            <button type="submit">Agregar Producto</button>
        </form>
    </main>
</body>
</html>
