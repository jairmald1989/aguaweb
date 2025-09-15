<?php session_start();
if(!isset($_SESSION['id'])){
	echo '<script>windows: location="index.php"</script>';
	
	}
?>
<?php
$session=$_SESSION['id'];
include 'db.php';
$result = mysqli_query($conn,"SELECT * FROM user where id= '$session'");
while($row = mysqli_fetch_array($result))
  {
  $sessionname=$row['name'];

  }
?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<link href="src/facebox.css" media="screen" rel="stylesheet" type="text/css" />
<link rel="stylesheet" type="text/css" href="css/bootstrap/dist/css/bootstrap.css"/>
<link rel="stylesheet" type="text/css"  href="css/bootstrap/dist/css/bootstrap.min.css" />
<link rel="stylesheet" type="text/css" href="css/bootstrap-theme.css" />
<link rel="stylesheet" type="text/css" href="css/bootstrap-theme.min.css" />
<link href="src/facebox.css" media="screen" rel="stylesheet" type="text/css" />
<script src="css/bootstrap/dist/js/jquery.js"></script>
<script src="css/bootstrap/dist/js/bootstrap.min.js"></script>
<script src="lib/jquery.js" type="text/javascript"></script>
<script src="src/facebox.js" type="text/javascript"></script>
  <script type="text/javascript">
	jQuery(document).ready(function($) {
	  $('a[rel*=facebox]').facebox({
		loadingImage : 'src/loading.gif',
		closeImage   : 'src/closelabel.png'
	  })
	})
	
  </script>
<script src="js/application.js" type="text/javascript" charset="utf-8"></script>	
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Billing System</title>
<style type="text/css">
#wrapper{
 width:100%;
 margin:0 auto;
 border:3px solid rgba(0,0,0,0);
-webkit-border-radius:5px;
-moz-border-radius:5px;
 border-radius:5px;
-webkit-box-shadow:0 0 18px rgba(0,0,0,0.4);
-moz-box-shadow:0 0 18px rgba(0,0,0,0.4);
 box-shadow:0 0 18px rgba(0,0,0,0.4);
 margin-top:2%;
 padding:10px;
 height:550px;
}
#header { width:900px; height:100px;}
table th {background:#999;}
#form {
width:400px;
float:left;
 border:3px solid rgba(0,0,0,0);
-webkit-border-radius:5px;
-moz-border-radius:5px;
 border-radius:5px;
-webkit-box-shadow:0 0 18px rgba(0,0,0,0.4);
-moz-box-shadow:0 0 18px rgba(0,0,0,0.4);
 box-shadow:0 0 18px rgba(0,0,0,0.4);
 margin-top:5%;
	
}
#ryt {
float:right;
 border:3px solid rgba(0,0,0,0);
-webkit-border-radius:5px;
-moz-border-radius:5px;
 border-radius:5px;
-webkit-box-shadow:0 0 18px rgba(0,0,0,0.4);
-moz-box-shadow:0 0 18px rgba(0,0,0,0.4);
 box-shadow:0 0 18px rgba(0,0,0,0.4);
 margin-top:5%;
}
#header ul li{
	list-style:none;
	float:left; margin-top:30px; margin-left:10px;}
</style>
</head>

<body>
<div class="container">
<div id="wrapper">
  <h1><center><b>Water Billing System</b></center></h1>
  <div style="color:#F00; font-size:12px; margin-left:900px;"> 
  <span><?php echo $sessionname;?></span><a href="logout.php"><span class="btn btn-danger  glyphicon glyphicon-log-out">&nbsp;Logout</span></a>
  </div>
  <ul class="nav nav-pills">
    <li><a href="billing.php"><span class="glyphicon glyphicon-home"></span>&nbsp;Home</a></li>
    <li><a href="bill.php"><span class="glyphicon glyphicon-usd"></span>&nbsp;Billing</a></li>
    <li><a href="user.php"><span class="glyphicon glyphicon-user"></span>&nbsp;Users</a></li>
    <li class="btn btn-default btn-xs"><a href="clients.php"><span class="glyphicon glyphicon-list"></span>&nbsp;Clients</a></li>
  </ul>
<hr color="#999999" />
<div  style="overflow:scroll; height:350px;">
  <div class="tab-content">
    <div id="home" class="tab-pane fade in active">
    <!-------- home panel ----------------------------->
    <!-----------------modal  ------------->
    

  <!-- Modal -->
  <div class="modal fade" id="myModal" role="dialog">
    <div class="modal-dialog" style="width:400px;">
    
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Water Billing System</h4>
        </div>
        <div class="modal-body">
          <p><?php include "addclient.php"; ?></p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
      </div>
      
    </div>
  </div>
  <!-------------------------- modal ends ---------------------------->
  
  <!-- Import Modal -->
  <div class="modal fade" id="importModal" role="dialog">
    <div class="modal-dialog" style="width:500px;">
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Importar Clientes desde CSV</h4>
        </div>
        <div class="modal-body">
          <div id="import-messages"></div>
          <form id="importForm" enctype="multipart/form-data">
            <div class="form-group">
              <label for="csv_file">Seleccionar archivo CSV:</label>
              <input type="file" name="csv_file" id="csv_file" class="form-control" accept=".csv" required />
              <small class="help-block">El archivo debe tener las columnas: lname, fname, mi, address, contact</small>
            </div>
            <div class="form-group">
              <a href="plantillas/plantilla_clientes.csv" download class="btn btn-info btn-sm">
                <span class="glyphicon glyphicon-download"></span> Descargar plantilla de ejemplo
              </a>
            </div>
            <button type="submit" class="btn btn-success">
              <span class="glyphicon glyphicon-upload"></span> Importar clientes
            </button>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
        </div>
      </div>
    </div>
  </div>
  <!-------------------------- import modal ends ---------------------------->
      
      
         <div class="panel panel-info">
            <div class="panel-heading">
                <div class="panel-title"><h5>System Clients</h5>
                <button type="button" class="btn btn-primary btn-xs" data-toggle="modal" data-target="#myModal"> + Add client</button>
                <button type="button" class="btn btn-success btn-xs" data-toggle="modal" data-target="#importModal">Importar desde Excel</button>
                <a href="deleteclient.php"><button class="btn btn-danger btn-xs">Delete all</button></a>
                </div>
            </div>
            <?php
            // Display import messages if available
            if (isset($_SESSION['import_message'])) {
                $message = $_SESSION['import_message'];
                $type = $_SESSION['import_type'];
                echo '<div class="alert alert-' . $type . ' alert-dismissible">';
                echo '<button type="button" class="close" data-dismiss="alert">&times;</button>';
                echo $message;
                echo '</div>';
                unset($_SESSION['import_message']);
                unset($_SESSION['import_type']);
            }
            ?>
              <div class="panel-body">
            
 <?php
include 'db.php';
$result = mysqli_query($conn,"SELECT * FROM owners");

echo "<table class=\"table\" bgcolor='#fff'>
<tr>
<th>Id</th>
<th>Firstname</th>
<th>Lastname</th>
<th>Mi</th>
<th>Address</th>
<th>Contact</th>
<th>Action</th>
</tr>";

while($row = mysqli_fetch_array($result))
  {
  echo "<tr>";
  echo "<td>" . $row['id'] . "</td>";
  echo "<td>" . $row['fname'] . "</td>";
  echo "<td>" . $row['lname'] . "</td>";
  echo "<td>" . $row['mi'] . "</td>";
  echo "<td>" . $row['address'] . "</td>";
  echo "<td>" . $row['contact'] . "</td>";
 echo "<td><a rel='facebox' href='edit.php?id=".$row['id']."'><button class=\"btn btn-default btn-xs\"><span class=\"glyphicon glyphicon-edit\"></span></button> </a>| ";
 echo "<a rel='facebox' href='del.php?id=".$row['id']."'><button class=\"btn btn-danger btn-xs\"><span class=\"glyphicon glyphicon-trash \"></span></button></td>";
  echo "</tr>";
  }
echo "</table>";

?>

              </div>
           </div>
         </div>
      </div>
    </div>
   <!-----  ######################################### -->
   

</div>
</body>

</html>
 <script src="js/jquery.js"></script>
  <script type="text/javascript">
$(function() {


$(".delbutton").click(function(){

//Save the link in a variable called element
var element = $(this);

//Find the id of the link that was clicked
var del_id = element.attr("id");

//Built a url to send
var info = 'id=' + del_id;
 if(confirm("Sure you want to delete this update? There is NO undo!"))
		  {

 $.ajax({
   type: "GET",
   url: "delete.php",
   data: info,
   success: function(){
   
   }
 });
         $(this).parents(".record").animate({ backgroundColor: "#fbc7c7" }, "fast")
		.animate({ opacity: "hide" }, "slow");

 }

return false;

});

});
</script>

<script>
// Handle CSV import form
$(document).ready(function() {
    $('#importForm').on('submit', function(e) {
        e.preventDefault();
        
        var formData = new FormData();
        var fileInput = $('#csv_file')[0];
        
        if (fileInput.files.length === 0) {
            showImportMessage('Por favor seleccione un archivo CSV.', 'danger');
            return;
        }
        
        formData.append('csv_file', fileInput.files[0]);
        formData.append('import_csv', '1');
        
        // Show loading message
        showImportMessage('Procesando archivo, por favor espere...', 'info');
        
        $.ajax({
            url: 'importar_clientes.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    showImportMessage(response.message, 'success');
                    // Reset form
                    $('#importForm')[0].reset();
                    // Reload page after 2 seconds to show new clients
                    setTimeout(function() {
                        window.location.reload();
                    }, 2000);
                } else {
                    showImportMessage(response.message, 'danger');
                }
            },
            error: function() {
                showImportMessage('Error al procesar el archivo. Intente nuevamente.', 'danger');
            }
        });
    });
    
    function showImportMessage(message, type) {
        var alertClass = 'alert-' + type;
        var html = '<div class="alert ' + alertClass + ' alert-dismissible">' +
                   '<button type="button" class="close" data-dismiss="alert">&times;</button>' +
                   message + '</div>';
        $('#import-messages').html(html);
    }
});
</script>
