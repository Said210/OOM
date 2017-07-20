<?php
	
	require_once 'connection.php';

	/**
	* PHP Record
	*/
	class PHPrecord{
		public $model_name = ""; 					// Set when extended
		// public $db = "optiexpress";  				// Database Model
		public $db = "mocla";
		public $before_save = null; 				// Action required before save record
		public $query = ""; 						// Query String
		public $private = [];  						// Array of private attributes
		public $attr = [];							// Array of attributes

		public function all($full_object = true){
			return $this->select("*")->run();
		}

		public function find($value, $key = "id", $full = true){
			if (!isset($value)) {
				throw new Exception('\$attributes or \$keys not set at find');
			}
			return $this->select()->where("$key = ".var_export($value, true)."")->run($full);
		}

		public function find_by($attributes = [], $keys = [], $glue = "AND", $full = true){
			if ($attributes == [] || $keys == []) {
				throw new Exception('\$attributes or \$keys not set at find by');
			}else{
				$where = "";
				for ($i=0; $i < count($keys); $i++) { 
					$where .= "$attributes[$i] = ".var_export($keys[$i], true)." $glue ";
				}
				$where = substr($where, 0, -1*(strlen($glue)+1));
				return $this->select()->where($where)->run($full);
			}
		}

		public function where($where = ""){
			if (!isset($where)) { throw new Exception('\$where not set at where'); }

			if (strpos($this->query, 'WHERE') === false) { // IF WHERE IS NOT SET YET 
				echo "WHERE NOT SETTED YET ";
				$this->query .= " WHERE $where  ";
			}else{
				echo "WHERE SETTED ALREADY ";
				$this->query = preg_replace('/WHERE (\s+|\w*|\=+|\"*\'*\(*)*(  )/i', "WHERE $where  ", $this->query);
			}
			return $this;
		}

		public function limit($limit = 1){
			if (!isset($limit)) { throw new Exception('\$limit not set at limit'); }

			if (strpos($this->query, 'LIMIT') === false) { // IF LIMIT IS NOT SET YET 
				echo "LIMIT NOT SETTED YET ";
				$this->query .= " LIMIT $limit  ";
			}else{
				echo "LIMIT SETTED ALREADY ";
				$this->query = preg_replace('/LIMIT (\s+|\d*|\=+|\"*\'*\(*)*(  )/i', "LIMIT $limit  ", $this->query);
			}
			return $this;
		}

		public function order($order_by, $dir = "DESC"){
			if (!isset($order_by)) { throw new Exception('\$order_by not set at order'); }

			if (strpos($this->query, 'ORDER') === false) { // IF ORDER IS NOT SET YET 
				echo "ORDER NOT SETTED YET ";
				$this->query .= " ORDER by $order_by $dir   ";
			}else{
				echo "ORDER SETTED ALREADY ";
				$this->query = preg_replace('/ORDER (\s+|\d*|\=+|\"*\'*\(*)*(  )/i', "ORDER by $order_by $dir  ", $this->query);
			}
			return $this;
		}

		public function join($join, $self_prop, $join_prop = "id"){
			if (!isset($join)) { throw new Exception('\$join not set at join'); }
			if (!isset($self_prop)) { throw new Exception('\$self_prop not set at join'); }
			$self_prop = (strpos($self_prop, ".") === false) ? $this->model_name.".".$self_prop : $self_prop ;
			$this->query .= " JOIN $join ON ".$self_prop." = ".$join.".".$join_prop."  ";
			return $this;
		}

		public function group($group){
			if (!isset($group)) { throw new Exception('\$group not set at group'); }

			if (strpos($this->query, 'GROUP') === false) { // IF GROUP IS NOT SET YET 
				echo "GROUP NOT SETTED YET ";
				$this->query .= " GROUP BY $group  ";
			}else{
				echo "GROUP SETTED ALREADY ";
				$this->query = preg_replace('/GROUP (\s+|\d*|\=+|\"*\'*\(*)*(  )/i', "GROUP $group  ", $this->query);
			}
			return $this;
		}

		public function select($attributes = "*"){
			if (strpos($this->query, 'SELECT') === false) { // IF SELECT IS NOT SET YET 
				echo "SELECT NOT SETTED YET ";
				$this->query = "SELECT $attributes FROM $this->model_name  ";
			}else{
				echo "SELECT SETTED ALREADY ";
				$this->query = preg_replace('/SELECT ((\w+)|\,*|\s|\**) FROM ((\w+)|\,*|\s)+(  )/i', "SELECT $attributes FROM $this->model_name  ", $this->query);
			}
			return $this;
		}
		public function from($from){
			if (strpos($this->query, 'FROM') === false) { // IF SELECT IS NOT SET YET 
				echo "FROM NOT SETTED YET ";
				$this->query = "FROM $from  ";
			}else{
				echo "FROM SETTED ALREADY ";
				$this->query = preg_replace('/FROM ((\w+)|\,*|\s|\**)(  )/i', "FROM $from  ", $this->query);
			}
			return $this;
		}

		public function update(){
			$conn = Connection::connect($this->db);
			$sql = "UPDATE ".$this->model_name." set";
			foreach ($this->attr as $k => $v) {
				$sql .= " $k = ".var_export($v, true)." ";
			}
			$this->query = $sql;
			return $this;
		}

		public function save($approved = false){
			$conn = Connection::connect($this->db);
			$array_of_values = [];
			$keys = implode(",", array_keys($this->attr));
			foreach ($this->attr as $attr => $value) {
				array_push($array_of_values, preg_replace("/'/", "\"", $value, true));
				$values .= " ".preg_replace("/'/", "\"", var_export($value, true)).",";
			}
			$values =  substr($values, 0, -1);
			$ins_query = "INSERT INTO $this->model_name($keys) VALUES($values);";
			var_dump($ins_query);
			if ($this->before_save == null || $approved == true) {
				$result = $conn->query($ins_query);
				var_dump($result);
				if(!$result){
					echo mysqli_error($conn)."<br><b>".$sql."</b><br><i>".var_export($this->attr, true)."</i><hr>";
					$r = mysqli_error($conn);	}
				$r = $this->find_by(array_keys($this->attr), $array_of_values);
				// var_export($conn->insert_id);
				return $r[count($r)-1];
			}else{
				$this->save($this->before_save());
			}	
		}		
		
		public function run($full_obj = true){
			$conn = Connection::connect($this->db);
			$result__ = $conn->query($this->query);
			$result = [];
			$r = [];
			var_dump("*------------------------->".$this->query."<--------------------*");
			if ($conn->connect_errno) {
			    printf("Falló la conexión: %s\n", $conn->connect_error);
			    exit();
			}
			if (is_bool($result__)) { 		// If could not fetch any result
				return $result__;
			}
			while ($row = $result__->fetch_assoc()) {
				array_push($result, $row);
		    }

		    for ($i=0; $i < count($result); $i++) { 
		    	$obj = $this->factory(get_class($this));
		    	for ($j=0; $j < count($result[$i]); $j++) {
		    		if (array_search(key($result[$i]), $this->private) === false) {
						$obj->attr[key($result[$i])] = $result[$i][key($result[$i])];
					}
		    		next($result[$i]);
		    	}
		    	
		    	array_push($r, ($full_obj) ? $obj : $obj->attr);
		    }
		    $this->chill();
			return $r;
		}

		public function delete(){
			if (strpos($this->query, 'DELETE') === false) { // IF DELETE IS NOT SET YET 
				echo "DELETE NOT SETTED YET ";
				$this->query = "DELETE FROM $this->model_name  ";
			}else{
				echo "DELETE SETTED ALREADY ";
				$this->query = preg_replace('/DELETE FROM ((\w+)|\,*|\s)+(  )/i', "DELETE FROM $this->model_name  ", $this->query);
			}
			return $this;
		}

		function factory($name, $params="", $model_name_local=""){ // CREATE AND RETURNS OBJECTS OF A CERTAIN CLASS
			$name = ($model_name_local!="") ? $model_name_local : $name;
			if ($params == "") {
				$init = "return new ".$name."();";
			}else{
				$init = "return new ".$name."({$params});";
			}
			return eval($init);
		}
		
		/**
			* factory_f 
			*
			* Creates an instance of PHPrecord with certain model_name
			*
			* @param (string) (model_name_local) the name of the table
			* @return (PHPrecord) 
		*/
		public static function factory_f($model_name_local){ // Force Factory
			$r = new PHPrecord();
			$r->model_name = $model_name_local;
			return $r;
		}

		public function generateRandomString($length = 10) {
		    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		    $charactersLength = strlen($characters);
		    $randomString = '';
		    for ($i = 0; $i < $length; $i++) {
		        $randomString .= $characters[rand(0, $charactersLength - 1)];
		    }
		    return $randomString;
		}

		public function chill(){ $this->query = ""; }
	}

	// $p->query("SELECT products.id,
	// 				 products.name,
	// 				 products.image,
	// 				 products.price,
	// 				 brands.description as brand
	// 			FROM products
	// 			JOIN brands ON products.brand = brands.id
	// 			WHERE products.visible = 1",false);
	

	// $p->select("products.id, products.name, products.price, brands.description as brand")
	//   ->join("brands","brand")
	//   ->where("products.visible = 1");
?>





	