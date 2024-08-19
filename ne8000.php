<?php
// Incluye los archivos de encabezado y base de datos
include 'header.php';
include 'db.php';

// Conexión a la base de datos
$conn = getDbConnection();
$sql = "SELECT * FROM NE8000";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NE8000 Productos</title>
    <style>
        /* Estilos para centrar la tabla */
        .table-container {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-top: 20px; /* Ajusta el margen superior según sea necesario */
        }

        /* Estilos para la tabla */
        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 16px; /* Aumenta el padding para hacer las filas más grandes */
            text-align: center;
        }

        th {
            background-color: #333;
            color: white;
        }

        tr:hover {
            background-color: #333;
            color: white; /* Cambia el color del texto al pasar el cursor */
        }

        /* Estilos para las miniaturas de imágenes */
        .thumbnail {
            width: 100px; /* Ajusta el ancho de las miniaturas */
            height: 100px; /* Ajusta la altura automáticamente */
            cursor: pointer;
            object-fit: contain; /* Ajusta la imagen dentro del contenedor sin recorte */
        }

        /* Estilos para el modal */
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
    </style>
</head>
<body>
    <h1>NE8000 Productos</h1>

    <!-- Barra de búsqueda para filtrar modelos -->
    <input type="text" id="search" placeholder="Buscar en cualquier campo..." onkeyup="filterTable()">

    <div class="table-container">
        <table id="productTable">
            <thead>
                <tr>
                    <th>Model</th>
                    <th>Chassis PN</th>
                    <th>Main Board PN</th>
                    <th>Power Board PN</th>
                    <th>Power Connector PN</th>
                    <th>Fan PN</th>            
                    <th>Chassis PN Image</th>
                    <th>Main Board PN Image</th>
                    <th>Power Board PN Image</th>
                    <th>Power Connector PN Image</th>
                    <th>Fan PN Image</th>
                    <th>Instructivo</th>
                    <th>Instructivo Ensamble</th>
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
                        <td><?php echo $row['Fan_PN']; ?></td>
                        <td><?php echo $row['Chassis_PN_Image'] ? '<img src="' . $row['Chassis_PN_Image'] . '" class="thumbnail" onclick="expandImage(this)">' : 'No Image'; ?></td>
                        <td><?php echo $row['Main_Board_PN_Image'] ? '<img src="' . $row['Main_Board_PN_Image'] . '" class="thumbnail" onclick="expandImage(this)">' : 'No Image'; ?></td>
                        <td><?php echo $row['Power_Board_PN_Image'] ? '<img src="' . $row['Power_Board_PN_Image'] . '" class="thumbnail" onclick="expandImage(this)">' : 'No Image'; ?></td>
                        <td><?php echo $row['Power_Connector_PN_Image'] ? '<img src="' . $row['Power_Connector_PN_Image'] . '" class="thumbnail" onclick="expandImage(this)">' : 'No Image'; ?></td>
                        <td><?php echo $row['Fan_PN_Image'] ? '<img src="' . $row['Fan_PN_Image'] . '" class="thumbnail" onclick="expandImage(this)">' : 'No Image'; ?></td>
                        <td><?php echo $row['Instructivo'] ? '<a href="' . $row['Instructivo'] . '" target="_blank">Ver Instructivo</a>' : 'No Instructivo'; ?></td>
                        <td><?php echo $row['Instructivo_A'] ? '<a href="' . $row['Instructivo_A'] . '" target="_blank">Ver Instructivo</a>' : 'No Instructivo'; ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <!-- Modal para expandir imagen -->
    <div id="modal" class="modal" onclick="closeModal()">
        <span class="close">&times;</span>
        <img class="modal-content" id="modalImage">
    </div>

    <script>
        // Función para filtrar la tabla según el texto ingresado en la barra de búsqueda
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

        // Función para expandir la imagen en un modal
        function expandImage(img) {
            var modal = document.getElementById("modal");
            var modalImg = document.getElementById("modalImage");
            modal.style.display = "block";
            modalImg.src = img.src;
        }

        // Función para cerrar el modal
        function closeModal() {
            var modal = document.getElementById("modal");
            modal.style.display = "none";
        }
    </script>
</body>
</html>

<?php include 'footer.php'; ?>
