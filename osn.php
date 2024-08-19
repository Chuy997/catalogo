<?php
include 'header.php';
include 'db.php';

$conn = getDbConnection();
$sql = "SELECT * FROM OSN";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OSN Productos</title>
    <style>
       /* Estilos para centrar la tabla */
.table-container {
    /* Usamos flexbox para centrar el contenido dentro del contenedor */
    display: flex;
    /* Centra horizontalmente */
    justify-content: center;
    /* Centra verticalmente */
    align-items: center;
    /* Añadimos un margen superior de 20px para separar la tabla del contenido superior */
    margin-top: 20px; /* Ajusta el margen superior según sea necesario */
}

/* Estilos para la tabla */
table {
    /* Hacemos que la tabla ocupe todo el ancho del contenedor */
    width: 100%;
    /* Eliminamos los espacios entre las celdas de la tabla */
    border-collapse: collapse;
}

th, td {
    /* Añadimos un borde a cada celda de la tabla */
    border: 1px solid #ddd;
    /* Aumentamos el espacio interno de las celdas para que sean más grandes y más fáciles de leer */
    padding: 16px; /* Aumenta el padding para hacer las filas más grandes */
    /* Centramos el texto dentro de las celdas */
    text-align: center;
}

th {
    /* Establecemos un color de fondo oscuro para las celdas de encabezado */
    background-color: #333;
    /* Cambiamos el color del texto de las celdas de encabezado a blanco para un buen contraste */
    color: white;
}

tr:hover {
    /* Cambiamos el color de fondo de la fila cuando el usuario pasa el cursor sobre ella */
    background-color: #333;
    /* Cambiamos el color del texto de la fila cuando el usuario pasa el cursor sobre ella */
    color: white; /* Cambia el color del texto al pasar el cursor */
}

/* Estilos para las miniaturas de imágenes */
.thumbnail {
    /* Establecemos el ancho de las imágenes en miniatura */
    width: 100px; /* Ajusta el ancho de las miniaturas */
    /* Ajustamos la altura de las imágenes automáticamente para mantener la proporción */
    height: 100px; /* Ajusta la altura automáticamente */
    /* Cambiamos el cursor a una mano al pasar sobre las imágenes para indicar que son clicables */
    cursor: pointer;
    /* Aseguramos que la imagen se ajuste completamente dentro del contenedor sin recortar */
    object-fit: contain; /* Ajusta la imagen dentro del contenedor sin recorte */
}

/* Estilos para el modal */
.modal {
    /* Ocultamos el modal por defecto */
    display: none;
    /* Posicionamos el modal en la pantalla de manera fija */
    position: fixed;
    /* Aseguramos que el modal esté sobre otros elementos (z-index alto) */
    z-index: 1;
    /* Añadimos un relleno en la parte superior para separar el contenido del borde superior */
    padding-top: 60px;
    /* Posicionamos el modal en la parte superior izquierda de la pantalla */
    left: 0;
    top: 0;
    /* Hacemos que el modal ocupe todo el ancho y altura de la pantalla */
    width: 100%;
    height: 100%;
    /* Hacemos que el contenido desborde si es necesario */
    overflow: auto;
    /* Establecemos un color de fondo negro */
    background-color: rgb(0,0,0);
    /* Añadimos un color de fondo semi-transparente para oscurecer el fondo detrás del modal */
    background-color: rgba(0,0,0,0.9);
}

.modal-content {
    /* Centramos el contenido del modal horizontalmente */
    margin: auto;
    /* Aseguramos que el contenido se muestre como un bloque (una sola imagen) */
    display: block;
    /* Establecemos el tamaño máximo de la imagen al 90% del ancho de la pantalla */
    max-width: 90%; /* Ajusta el tamaño de la imagen */
    /* Establecemos el tamaño máximo de la imagen al 90% de la altura de la pantalla */
    max-height: 90%; /* Asegúrate de que la imagen se ajuste completamente */
    /* Aseguramos que la imagen se ajuste completamente dentro de su contenedor */
    object-fit: contain; /* Ajusta la imagen dentro del contenedor */
}

.close {
    /* Posicionamos el botón de cerrar en la esquina superior derecha del modal */
    position: absolute;
    top: 15px;
    right: 35px;
    /* Establecemos un color claro para el botón de cerrar */
    color: #f1f1f1;
    /* Aumentamos el tamaño de la fuente para que sea más visible */
    font-size: 40px;
    /* Hacemos el texto del botón más grueso */
    font-weight: bold;
    /* Añadimos una transición suave para el cambio de color al pasar el cursor */
    transition: 0.3s;
}

.close:hover,
.close:focus {
    /* Cambiamos el color del botón de cerrar al pasar el cursor sobre él o enfocarlo */
    color: #bbb;
    /* Eliminamos el subrayado en el texto del botón de cerrar */
    text-decoration: none;
    /* Cambiamos el cursor a una mano para indicar que es un botón clicable */
    cursor: pointer;
}

    </style>
</head>
<body>
    <h1>OSN Productos</h1>

    <!-- Barra de búsqueda para filtrar modelos -->
    <input type="text" id="search" placeholder="Buscar en cualquier campo..." onkeyup="filterTable()">

    <div class="table-container">
        <table id="productTable">
            <thead>
                <tr>
                    <th>Model</th>
                    <th>Chassis PN</th>
                    <th>Main Board PN</th>
                    <th>Power Connector PN</th>
                    <th>Cabinet PN</th>
                    <th>Chassis Image</th>
                    <th>Main Board Image</th>
                    <th>Power Connector Image</th>
                    <th>Cabinet Image</th>
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
                        <td><?php echo $row['Power_Connector_PN']; ?></td>
                        <td><?php echo $row['Cabinet_PN']; ?></td>
                        <td><?php echo $row['Chassis_PN_Image'] ? '<img src="' . $row['Chassis_PN_Image'] . '" class="thumbnail" onclick="expandImage(this)">' : 'No Image'; ?></td>
                        <td><?php echo $row['Main_Board_PN_Image'] ? '<img src="' . $row['Main_Board_PN_Image'] . '" class="thumbnail" onclick="expandImage(this)">' : 'No Image'; ?></td>
                        <td><?php echo $row['Power_Connector_PN_Image'] ? '<img src="' . $row['Power_Connector_PN_Image'] . '" class="thumbnail" onclick="expandImage(this)">' : 'No Image'; ?></td>
                        <td><?php echo $row['Cabinet_PN_Image'] ? '<img src="' . $row['Cabinet_PN_Image'] . '" class="thumbnail" onclick="expandImage(this)">' : 'No Image'; ?></td>
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
