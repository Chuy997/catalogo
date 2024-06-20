<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

function getDbConnection() {
    $servername = "localhost";
    $dbname = "CatalogoATO";

    if (isset($_SESSION['user_role']) && $_SESSION['user_role'] == 'admin') {
        $username = "panda";
        $password = "password_admin";
    } else {
        $username = "operator";
        $password = "password_consulta";
    }

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    return $conn;
}
?>
