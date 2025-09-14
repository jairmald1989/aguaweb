<?php
include 'db.php';
$owner_id =$_REQUEST['id'];
	$id = $_POST['id'];
	$lname = $_POST['lname'];
	$fname = $_POST['fname'];
	$mi = $_POST['mi'];
	$address=$_POST['address'] ;
	$contact=$_POST['contact'] ;
	$cedula_ruc=$_POST['cedula_ruc'] ;

	mysqli_query($conn,"UPDATE owners SET id ='$id', lname ='$lname',
		 fname ='$fname',mi ='$mi', address='$address', contact='$contact', cedula_ruc='$cedula_ruc' WHERE id = '$owner_id'");
			

		 echo "<script>windows: location='billing.php'</script>";				
			