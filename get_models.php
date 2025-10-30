<?php
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['table'])) {
    $conn = getDbConnection();
    $table = $_POST['table'];

    $stmt = $conn->prepare("SELECT Model FROM $table");
    $stmt->execute();
    $result = $stmt->get_result();

    $models = [];
    while ($row = $result->fetch_assoc()) {
        $models[] = $row['Model'];
    }

    echo json_encode($models);
    $stmt->close();
    $conn->close();
}
?>
