<?php 
	
	include 'koneksi.php';

	if (isset($_GET['Del'])) {
		// code...
		$id_alternatif = $_GET['Del'];
		$query = "DELETE FROM data_training WHERE id_data_training = '".$id_alternatif."'";
		$result = mysqli_query($conn, $query);

		if ($result) {
			// code...
			header("Location:data_training.php");
		}else {
			echo "Please Check Again";
		}
	}
?>