<?php
include 'db.php';

$conn = getDbConnection();
$sql = "SELECT * FROM NE8000";
$result = $conn->query($sql);
?>

<?php include 'header.php'; ?>

<h1>NE8000 Productos</h1>
<table>
    <thead>
        <tr>
            <th>Model</th>
            <th>Chassis PN</th>
            <th>Main Board PN</th>
            <th>Power Board PN</th>
            <th>Power Connector PN</th>
            <th>Fan PN</th>
            <th>Cabinet PN</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo $row['Model']; ?></td>
                <td><?php echo $row['Chassis PN']; ?></td>
                <td><?php echo $row['Main Board PN']; ?></td>
                <td><?php echo $row['Power Board PN']; ?></td>
                <td><?php echo $row['Power Connector PN']; ?></td>
                <td><?php echo $row['Fan PN']; ?></td>
                <td><?php echo $row['Cabinet PN']; ?></td>
            </tr>
        <?php endwhile; ?>
    </tbody>
</table>

<?php include 'footer.php'; ?>
