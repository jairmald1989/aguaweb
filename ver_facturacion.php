<p><h1 align="center">Facturación Mensual</h1></p>

<div class="panel panel-info">
    <div class="panel-heading">
        <div class="panel-title">
            <h5>Registros de Facturación Mensual</h5>
            <a rel="facebox" href="facturacion_mensual.php">
                <button class="btn btn-success btn-xs">
                    <span class="glyphicon glyphicon-plus"></span> Generar Nueva Facturación
                </button>
            </a>
        </div>
    </div>
    <div class="panel-body" style="max-height: 400px; overflow-y: auto;">
        
        <?php
        include 'db.php';
        
        // Consulta para obtener los registros de facturación con información del cliente
        $result = mysqli_query($conn, "
            SELECT f.*, o.fname, o.lname, o.mi 
            FROM facturacion_mensual f 
            INNER JOIN owners o ON f.owners_id = o.id 
            ORDER BY f.fecha_emision DESC
        ");
        
        if (mysqli_num_rows($result) > 0) {
            echo "<table class=\"table table-striped\" bgcolor='#fff'>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Cliente</th>
                    <th>Medidor</th>
                    <th>Mes</th>
                    <th>Consumo</th>
                    <th>Monto</th>
                    <th>Fecha Emisión</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>";
            
            while($row = mysqli_fetch_array($result)) {
                $estado = $row['pagada'] ? '<span class="label label-success">Pagada</span>' : '<span class="label label-warning">Pendiente</span>';
                $nombre_completo = $row['lname'] . ', ' . $row['fname'];
                
                echo "<tr>";
                echo "<td>" . $row['id'] . "</td>";
                echo "<td>" . $nombre_completo . "</td>";
                echo "<td>" . $row['mi'] . "</td>";
                echo "<td>" . $row['mes'] . "</td>";
                echo "<td>" . $row['consumo'] . " ml</td>";
                echo "<td>$" . number_format($row['monto'], 2) . "</td>";
                echo "<td>" . date('d/m/Y H:i', strtotime($row['fecha_emision'])) . "</td>";
                echo "<td>" . $estado . "</td>";
                echo "<td>";
                
                if (!$row['pagada']) {
                    echo "<button class=\"btn btn-success btn-xs\" onclick=\"marcarPagada(" . $row['id'] . ")\">
                            <span class=\"glyphicon glyphicon-check\"></span> Marcar Pagada
                          </button> ";
                }
                
                echo "<button class=\"btn btn-info btn-xs\" onclick=\"verDetalle(" . $row['id'] . ")\">
                        <span class=\"glyphicon glyphicon-eye-open\"></span> Ver
                      </button>";
                echo "</td>";
                echo "</tr>";
            }
            echo "</tbody></table>";
        } else {
            echo "<div class=\"alert alert-info\">
                    <strong>No hay registros de facturación.</strong><br>
                    Genere la primera facturación mensual usando el botón \"Generar Nueva Facturación\".
                  </div>";
        }
        ?>
        
    </div>
</div>

<script>
function marcarPagada(facturaId) {
    if (confirm('¿Está seguro de marcar esta factura como pagada?')) {
        var xhr = new XMLHttpRequest();
        xhr.open('POST', 'marcar_pagada.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4 && xhr.status === 200) {
                if (xhr.responseText.trim() === 'success') {
                    alert('Factura marcada como pagada');
                    window.parent.location.reload();
                } else {
                    alert('Error al marcar la factura como pagada');
                }
            }
        };
        xhr.send('factura_id=' + facturaId);
    }
}

function verDetalle(facturaId) {
    // Aquí se podría implementar una ventana modal con más detalles
    alert('Función de detalle - ID: ' + facturaId);
}
</script>