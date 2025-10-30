<?php
function getDbConnection() {
    $servername = "localhost";
    $username   = "jmuro";         // Usuario unificado
    $password   = "Monday.03";     // Su contraseña
    $dbname     = "catalogoato";   // Nombre exacto en minúsculas

    // Crear la conexión
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Establecer zona horaria de la sesión
    // Establecer zona horaria de la sesión a UTC−06:00
$conn->query("SET time_zone = '-06:00'");


    // Verificar la conexión
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    return $conn;
}
?>
