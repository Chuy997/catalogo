<?php
include 'header.php';
include 'db.php';

$conn = getDbConnection();
$sql = "SELECT * FROM ont";
$result = $conn->query($sql);
?>

<h1>ONT Productos</h1>

<!-- Barra de búsqueda para filtrar modelos -->
<input type="text" id="search" placeholder="Buscar en cualquier campo..." onkeyup="filterTable()">

<table id="productTable">
    <thead>
        <tr>
            <th>Model</th>
            <th>ONT PN</th>
            <th>Power Connector PN</th>
            <th>ONT PN Image</th>
            <th>Power Connector PN Image</th>
            <th>SOP Pruebas</th>
            <th>SOP Proceso</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo $row['Model']; ?></td>
                <td><?php echo $row['ONT_PN']; ?></td>
                <td><?php echo $row['Power_Connector_PN']; ?></td>
                <td><?php echo $row['ONT_PN_Image'] ? '<img src="' . $row['Chassis PN Image'] . '" class="thumbnail" onclick="expandImage(this)">' : 'No Image'; ?></td>
                <td><?php echo $row['Power_Connector_PN_Image'] ? '<img src="' . $row['Power_Connector_PN_Image'] . '" class="thumbnail" onclick="expandImage(this)">' : 'No Image'; ?></td>
                <td><?php echo $row['Instructivo'] ? '<a href="' . $row['Instructivo'] . '" target="_blank">Ver Instructivo</a>' : 'No Instructivo'; ?></td>
                <td><?php echo $row['Instructivo_A'] ? '<a href="' . $row['Instructivo_A'] . '" target="_blank">Ver Instructivo</a>' : 'No Instructivo'; ?></td>
            </tr>
        <?php endwhile; 
        ?>
    </tbody>
</table>

<!-- Modal para expandir imagen -->
<div id="modal" class="modal" onclick="closeModal()">
    <span class="close">&times;</span>
    <img class="modal-content" id="modalImage">
</div>

<script>
function filterTable() {
    var input, filter, table, tr, td, i, j, txtValue;
    input = document.getElementById("search");
    filter = input.value.toUpperCase();
    table = document.getElementById("productTable");
    tr = table.getElementsByTagName("tr");
    
    for (i = 1; i < tr.length; i++) {
        tr[i].style.display = "none";
        td = tr[i].getElementsByTagName("td");
        for (j = 0; j < td.length; j++) {
            if (td[j]) {
                txtValue = td[j].textContent || td[j].innerText;
                if (txtValue.toUpperCase().indexOf(filter) > -1) {
                    tr[i].style.display = "";
                    break;
                }
            }
        }
    }
}

function expandImage(img) {
    var modal = document.getElementById("modal");
    var modalImg = document.getElementById("modalImage");
    modal.style.display = "block";
    modalImg.src = img.src;
}

function closeModal() {
    var modal = document.getElementById("modal");
    modal.style.display = "none";
}
</script>

<link rel="stylesheet" href="substyle.css">

<?php include 'footer.php'; ?>
