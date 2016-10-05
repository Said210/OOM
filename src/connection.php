<?php
/*
	Build by eulr @ eulr.mx
	hola@eulr.mx
	V1.0
*/
	class Connection{


		function connect($db){
			$servername = "[YOUR_HOST]"; // HOST TO CONNECT
			$username = "[YOUR_REMOTE_USERNAME]"; // MYSQL USERNAME
			$password = "[YOUR_REMOTE_PASSWORD]"; // MYSQL PASSWORD
			if ($_SERVER["HTTP_HOST"] == "localhost") { // IF YOU WANT TO CHANGE IT DEPENDING WHERE IT'S RUNNING
				$username = "root";
				$password = "";
			}

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