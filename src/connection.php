<?php
/*
	Build by eulr @ eulr.mx
	hola@eulr.mx
	V1.0
*/
require 'DBHandler.php';
	class Connection extends DBHandler {

		function connect($db){
			//$conn = mysqli_connect($servername, $username, $password, $db);
			$conn = new mysqli($servername, $username, $password, $db);
			// Check connection
			if (!$conn) {
				$this->ERRORS = mysqli_connect_error();
				return -1;
			}

			return $conn;
		}
		public function disconect($db){
			$db->close();
		}

		public function oracle_connect($db){
			$connection_string = "localhost/XE"; // HOST TO CONNECT
			$username = "root"; // ORACLE USERNAME
			$password = ""; // ORACLE PASSWORD

			$conn = oci_connect($username, $password, $connection_string);
			if (!$conn) {
			    $this->ERRORS = oci_error();
			    //trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
			}
			return $conn;
		}
	}
?>
