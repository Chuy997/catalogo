<?php
include 'header.php';
include 'db.php';

$conn = getDbConnection();
$sql = "SELECT * FROM BBU";
$result = $conn->query($sql);
?>

<h1>BBU Productos</h1>

<!-- Barra de búsqueda para filtrar modelos -->
<input type="text" id="search" placeholder="Buscar en cualquier campo..." onkeyup="filterTable()">

<table id="productTable">
    <thead>
        <tr>
            <th>Model</th>
            <th>Chassis PN</th>
            <th>Main Board PN</th>
            <th>Power Board PN</th>
            <th>Power Connector PN</th>   
            <th>Chassis PN Image</th>
            <th>Main Board PN Image</th>
            <th>Power Board PN Image</th>
            <th>Power Connector PN Image</th>
            <th>SOP Pruebas</th>
            <th>SOP Ensamble</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo $row['Model']; ?></td>
                <td><?php echo $row['Chassis_PN']; ?></td>
                <td><?php echo $row['Main_Board_PN']; ?></td>
                <td><?php echo $row['Power_Board_PN']; ?></td>
                <td><?php echo $row['Power_Connector_PN']; ?></td>
                <td><?php echo $row['Chassis_PN_Image'] ? '<img src="' . $row['Chassis_PN_Image'] . '" class="thumbnail" onclick="expandImage(this)">' : 'No Image'; ?></td>
                <td><?php echo $row['Main_Board_PN_Image'] ? '<img src="' . $row['Main_Board_PN_Image'] . '" class="thumbnail" onclick="expandImage(this)">' : 'No Image'; ?></td>
                <td><?php echo $row['Power_Board_PN_Image'] ? '<img src="' . $row['Power_Board_PN_Image'] . '" class="thumbnail" onclick="expandImage(this)">' : 'No Image'; ?></td>
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

<style>
    header {
    background-color: rgba(31, 31, 31, 0.5); /* Fondo con transparencia */
    width: 100%;
    padding: 20px 0;
    
    }
    body {
    background-image: url('red.jpg');
    background-size: cover; /* Asegura que la imagen cubra toda la pantalla */
    background-position: center; /* Centra la imagen en la pantalla */
    background-repeat: no-repeat; /* Evita que la imagen se repita */
    color: #ffffff;
    font-family: 'Roboto', Arial, sans-serif;
    margin: 0;
    padding: 0;
    display: flex;
    flex-direction: column;
    align-items: center;
    min-height: 100vh;
    }
table {
    width: 100%;
    border-collapse: collapse;
}

th, td {
    border: 1px solid #ddd;
    padding: 16px; /* Aumenta el padding para hacer las filas más grandes */
    text-align: left;
}

th {
    background-color: #333;
    color: white;
}

tr:hover {
    background-color: #333;
}

.thumbnail {
    width: 100px; /* Ajusta el ancho de las miniaturas */
    height: 100px; /* Ajusta la altura automáticamente */
    cursor: pointer;
    object-fit: contain; /* Ajusta la imagen dentro del contenedor sin recorte */
}

.modal {
    display: none;
    position: fixed;
    z-index: 1;
    padding-top: 60px;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    overflow: auto;
    background-color: rgb(0,0,0);
    background-color: rgba(0,0,0,0.9);
}

.modal-content {
    margin: auto;
    display: block;
    max-width: 90%; /* Ajusta el tamaño de la imagen */
    max-height: 90%; /* Asegúrate de que la imagen se ajuste completamente */
    object-fit: contain; /* Ajusta la imagen dentro del contenedor */
}

.close {
    position: absolute;
    top: 15px;
    right: 35px;
    color: #f1f1f1;
    font-size: 40px;
    font-weight: bold;
    transition: 0.3s;
}

.close:hover,
.close:focus {
    color: #bbb;
    text-decoration: none;
    cursor: pointer;
}
footer {
    text-align: center;
    padding: 10px 0;
    background-color: rgba(18, 18, 18, 0.0); /* Fondo oscuro con transparencia */
    color: white;
    width: 100%;
    margin-top: auto;
    
    /* Posicionar el footer al final del contenido, pero no fijo al fondo de la página */
}
</style>

<?php include 'footer.php'; ?>
