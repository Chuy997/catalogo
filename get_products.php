<?php
include 'db.php';

if (isset($_GET['table'])) {
    $table = $_GET['table'];
    $conn = getDbConnection();
    
    $sql = "SELECT id, Model FROM $table";
    $result = $conn->query($sql);
    
    $products = [];
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
    
    echo json_encode($products);
    
    $conn->close();
}
?>
