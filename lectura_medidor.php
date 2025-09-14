<p><h1 align="center">Ingreso de Lectura de Medidor</h1></p>
<form method="post" action="lectura_medidor1.php">
    <div class="form-group">
        <label for="cliente">Cliente:</label>
        <select name="owners_id" class="form-control" required="required">
            <option value="">Seleccionar Cliente</option>
            <?php
            include 'db.php';
            $result = mysqli_query($conn, "SELECT * FROM owners ORDER BY lname, fname");
            while($row = mysqli_fetch_array($result)) {
                echo "<option value='".$row['id']."'>".$row['lname']." ".$row['fname']." - ".$row['mi']."</option>";
            }
            ?>
        </select>
    </div>
    
    <div class="form-group">
        <label for="lectura_anterior">Lectura Anterior:</label>
        <input type="text" name="lectura_anterior" class="form-control" id="lectura_anterior" readonly />
    </div>
    
    <div class="form-group">
        <label for="lectura_actual">Lectura Actual:</label>
        <input type="text" name="lectura_actual" class="form-control" required="required" />
    </div>
    
    <div class="form-group">
        <label for="fecha">Fecha de Lectura:</label>
        <input type="date" name="fecha" class="form-control" value="<?php echo date('Y-m-d'); ?>" required="required" />
    </div>
    
    <br />
    <input type="submit" name="guardar" value="Guardar Lectura" class="btn btn-success form-control"/>
</form>

<script>
// JavaScript para cargar la lectura anterior cuando se selecciona un cliente
document.querySelector('select[name="owners_id"]').addEventListener('change', function() {
    var clienteId = this.value;
    if (clienteId) {
        // Hacer una petición AJAX para obtener la lectura anterior
        var xhr = new XMLHttpRequest();
        xhr.open('GET', 'get_lectura_anterior.php?id=' + clienteId, true);
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4 && xhr.status === 200) {
                document.getElementById('lectura_anterior').value = xhr.responseText;
            }
        };
        xhr.send();
    } else {
        document.getElementById('lectura_anterior').value = '';
    }
});
</script>