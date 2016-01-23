<?php
/*
	Build by eulr @ eulr.mx
	hola@eulr.mx
	v0.0.1-alpha
*/
	class Connection{


		function connect($db){
			$servername = "127.0.0.1";
			$username = "root";
			$password = "";

			// Create connection
			$conn = mysqli_connect($servername, $username, $password, $db);

			// Check connection
			if (!$conn) {
			    die("Connection failed:".var_dump($conn));
			} 

			return $conn;
		}
	}
?>