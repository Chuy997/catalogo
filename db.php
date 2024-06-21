<?php
function getDbConnection() {
    $servername = "localhost";
    $username = "panda"; // Cambia a 'panda'
    $password = "password_admin"; // La contraseña de 'panda'
    $dbname = "CatalogoATO";

    // Crear la conexión
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Verificar la conexión
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    return $conn;
}
?>
