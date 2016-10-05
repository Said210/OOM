<?php
/*
	Build by eulr @ eulr.mx
	hola@eulr.mx
    V 0.6b
*/
	require_once 'connection.php';
    require_once 'logger.php';
	
	class OOM{
		public $model_name = "";
		public $db = "optiexpress";
		public $before_save = null;
		/*
		* $private is an array of strings of the values 
		* that you don't want to return every time you query the class
		* IT ONLY WORKS ON ALL, FIND, FIND_BY AND LIKE 
		* ¡I WOULDN'T USE IT YET! It's not as good or trustable as it should
		*/
		private $private = [];
		public $attr = [];

		/**
			* all 
			*
			* Returns "all" entries of that model
			*
			* @param (array of strings) (params) MySQL modifiers (WHERE, LIMIT, SORT, OFFSET...)
			* @return (Array of OOM)
		*/

		function all($params = [], $full_obj = true){
			$connection = new Connection();
			$conn = $connection->connect($this->db);
			$r = [];
			$result = [];
			$query = "SELECT * FROM ".$this->model_name;

			while ($current = current($params)) {
				$query .= " ".key($params)." ";
				$query .= is_numeric($current) ? $current : " \"$current\"";
			    next($params);
			}
			$Logger = new Logger();
			$Logger->log($query);

			$result__ = $conn->query($query);
			while ($row = $result__->fetch_assoc()) {
				if (!array_search($row, $this->private)) {
					array_push($result, $row);
				}
		    }

		    for ($i=0; $i < count($result); $i++) {
		    	$obj__ = "return new ".get_class($this)."();";
		    	$obj =  eval($obj__);
		    	for ($j=0; $j < count($result[$i]); $j++) {
		    	
		    		$obj->attr[key($result[$i])] = $result[$i][key($result[$i])];
		    		//echo key($result[$i])."<br>";
		    		next($result[$i]);
		    	}
		    	if($full_obj){
		    	    array_push($r, $obj);
                }else{
                    array_push($r, $obj->attr);
                }
		    }
			return $r;
		}
		/**
			* find 
			*
			* Find a record with certain id
			*
			* @param (string) (id) Entry id
			* @return (OOM::something)
		*/
		function find($id, $full_obj = true){
			$connection = new Connection();
			$conn = $connection->connect($this->db);
			$logger = new Logger();
			if (is_numeric($value)) {
				$result = $conn->query("SELECT * FROM ".$this->model_name." WHERE id=".$id);
			}else{
				$result = $conn->query("SELECT * FROM ".$this->model_name." WHERE id='".$id."';");
			}
			
			while($obj = $result->fetch_object()){
				$z = (array) $obj;
			}
			
			foreach ($z as $row) {
				if (array_search($row, $this->private)) {
					$row = "";
				}
			}

			$this->attr = $z;

            if($full_obj){
			     return $this;
            }else{
                return $this->attr;
            }
		}


		/**
			* get_many 
			*
			* Get all items that match the id or the given query
			*
			* @param (array) (ids_) Values
			* @param (array) ($c) key for the query
			* @return (Array of OOM::something)
		*/
		function get_many($ids_, $c = null){
			$ids = split(",", $ids_);
			$query = "";
			for ($i=0; $i < count($ids); $i++) {
				$query .= ($c == null) ? "id = '".$ids[$i]."' OR " : $c." = '".$ids[$i]."' OR " ;
			}
			$query = substr($query, 0, -3);

			$result = $this->where($query);
			
			return $result;
		}


		/**
			* fetch 
			*
			* Gets 
			*
			* @param (string) (name) Name of the attr (If it's not the same as object use $model_name_local)
			* @param (string) (model_name_local) Name of the table
			* @param (string) (find_by) Field that must match 
			* @return (Array of OOM::something)
		*/

		function fetch($name, $model_name_local = "", $find_by="id"){

			if ($model_name_local != "") {
				$item = $this->factory_f($model_name_local);
			}else{
				$item = $this->factory($name);
			}
			//var_export($this->attr[$name]);
			return $item->find_by("{$find_by}", $this->attr[$name]);
		}

		

		/**
			* factory 
			*
			* Creates an instance of OOM with certain model_name
			*
			* @param (string) (name) It's the name of the class you wanna instance
			* @param (string) (params) Parameters for the new object
			* @param (string) (model_name_local) Name of the table
			* @return (OOM::something)
		*/

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
			* Creates an instance of OOM with certain model_name
			*
			* @param (string) (model_name_local) the name of the table
			* @return (OOM) 
		*/
		public static function factory_f($model_name_local){ // Force Factory
			$r = new OOM();
			$r->model_name = $model_name_local;
			return $r;
		}


		/**
			* update 
			*
			* Update a registry
			*
			* @param (string) (where) MySQL query
			* @return (OOM) instance
		*/
		function update($where = ""){
			$Logger = new Logger();
			$connection = new Connection();
			$conn = $connection->connect($this->db);

			$sql = "UPDATE ".$this->model_name." set";
			foreach ($this->attr as $k => $v) {
				if ($k != "id") {
					if (is_numeric($v)) {
						$sql .= " $k = $v,";
					}else{
						$sql .= " $k = '$v',";
					}
				}
			}
			$sql = rtrim($sql, ",");
			if ($where == "") {
				$sql .= " where id = ".$this->attr['id'];
			}else{
				$sql .= " where $where";
			}
			$Logger->log($sql);
			$result = $conn->query($sql);
			return $result;
		}

		function find_by($attr, $value, $full_obj = true){
            $Logger = new Logger();
			$connection = new Connection();
			$conn = $connection->connect($this->db);
			$r = [];
			$result = [];
			if (is_numeric($value)) {
				$result__ = $conn->query("SELECT * FROM ".$this->model_name." WHERE ".$attr." = ".$value.";");
			}else{
				$result__ = $conn->query("SELECT * FROM ".$this->model_name." WHERE ".$attr." = '".$value."';");
			}

			while ($row = $result__->fetch_assoc()) {
		        if (!array_search($row, $this->private)) {
					array_push($result, $row);
				}
		    }

		    for ($i=0; $i < count($result); $i++) {
		    	$obj__ = "return new ".get_class($this)."();";
		    	$obj =  eval($obj__);
		    	for ($j=0; $j < count($result[$i]); $j++) {
		    		$obj->attr[key($result[$i])] = $result[$i][key($result[$i])];
		    		next($result[$i]);
		    	}
                if($full_obj){
		    	    array_push($r, $obj);
                }else{
                    array_push($r, $obj->attr);
                }
		    }
		    $Logger->log($r);
			return $r;
		}


		function like($attr, $value, $full_obj = true){
            $Logger = new Logger();
			$connection = new Connection();
			$conn = $connection->connect($this->db);
			$r = [];
			$result = [];
			if (is_numeric($attr)) {
				$result__ = $conn->query("SELECT * FROM ".$this->model_name." WHERE ".$attr." LIKE %".$value."%;");
			}else{
				$result__ = $conn->query("SELECT * FROM ".$this->model_name." WHERE ".$attr." LIKE '%".$value."%';");
			}
			while ($row = $result__->fetch_assoc()) {
		        if (!array_search($row, $this->private)) {
					array_push($result, $row);
				}
		    }

		    for ($i=0; $i < count($result); $i++) {
		    	$obj__ = "return new ".get_class($this)."();";
		    	$obj =  eval($obj__);
		    	for ($j=0; $j < count($result[$i]); $j++) {
		    		$obj->attr[key($result[$i])] = $result[$i][key($result[$i])];
		    		next($result[$i]);
		    	}
                if($full_obj){
		    	    array_push($r, $obj);
                }else{
                    array_push($r, $obj->attr);
                }
		    }
		    $Logger->log($r);
			return $r;
		}

		function where($value, $full_obj = true){
			$connection = new Connection();
			$logger = new Logger();
			$conn = $connection->connect($this->db);
			$r = [];
			$result = [];
			$logger->log("SELECT * FROM ".$this->model_name." WHERE ".$value);
			$result__ = $conn->query("SELECT * FROM ".$this->model_name." WHERE ".$value.";");

			while ($row = $result__->fetch_assoc()) {
		        array_push($result, $row);
		    }

		    for ($i=0; $i < count($result); $i++) {
		    	$obj__ = "return new ".get_class($this)."();";
		    	$obj =  eval($obj__);
		    	for ($j=0; $j < count($result[$i]); $j++) {
		    		$obj->attr[key($result[$i])] = $result[$i][key($result[$i])];
		    		//echo key($result[$i])."<br>";
		    		next($result[$i]);
		    	}
		    	if($full_obj){
		    	    array_push($r, $obj);
                }else{
                    array_push($r, $obj->attr);
                }
		    }
			
			return $r;
		}
		function query($sql, $full_obj = true){
			$connection = new Connection();
			$logger = new Logger();
			$conn = $connection->connect($this->db);
			$r = [];
			$result = [];
			$logger->log("OOM:Query "+ $sql);
			$result__ = $conn->query($sql);

			while ($row = $result__->fetch_assoc()) {
		        array_push($result, $row);
		    }

		    for ($i=0; $i < count($result); $i++) {
		    	$obj__ = "return new ".get_class($this)."();";
		    	$obj =  eval($obj__);
		    	for ($j=0; $j < count($result[$i]); $j++) {
		    		$obj->attr[key($result[$i])] = $result[$i][key($result[$i])];
		    		//echo key($result[$i])."<br>";
		    		next($result[$i]);
		    	}
		    	if($full_obj){
		    	    array_push($r, $obj);
                }else{
                    array_push($r, $obj->attr);
                }
		    }
			
			return $r;
		}


		function create($json){
			$connection = new Connection();
			$conn = $connection->connect($this->db);
			$array = json_decode($json, true);
			$sql = "INSERT INTO ".$this->model_name."(%keys%) VALUES (%values%);";

			while ($current = current($array)) {
				$keys .= key($array).",";
				if (is_numeric($current)) {
					$values .= $current.",";
				}else{
					$values .= "\"".$current."\",";
				}
			    next($array);
			}
			$keys = substr_replace($keys, "", -1);
			$values = substr_replace($values, "", -1);

			$sql = str_replace("%keys%", $keys, $sql);
			$sql = str_replace("%values%", $values, $sql);

			return $conn->query($sql);

		}

		function save($validated=false, $done=-1){
			$Logger = new Logger();
			if($validated || $this->before_save == null){
				$connection = new Connection();
				$conn = $connection->connect($this->db);
				$array = $this->attr;
				$sql = "INSERT INTO ".$this->model_name."(%keys%) VALUES (%values%);";

				while ($current = current($array)) {
					$keys .= key($array).",";
					if (is_numeric($current)) {
						$values .= $current.",";
					}else{
						$values .= "\"".preg_replace('/\s$/',"",$current)."\",";
					}
				    next($array);
				}
				$keys = substr_replace($keys, "", -1);
				$values = substr_replace($values, "", -1);

				$sql = str_replace("%keys%", $keys, $sql);
				$sql = str_replace("%values%", $values, $sql);
				$sql = preg_replace('/\n/', "", $sql);
				$sql = preg_replace('/\s$/', "", $sql);

				//echo $sql."<br>"; 
				$r = $conn->query($sql);
				$Logger->log($sql);
				$Logger->log("sql", false);
				if(!$r){ echo mysqli_error($conn)."<br><b>".$sql."</b><br><i>".var_dump($this->attr)."</i><hr>"; $r = mysqli_error($conn);}
			}
			if (!$validated && $done == -1 && $this->before_save != null) {
				$Logger->log("OOM@SAVE:296 middle step(!$validated, $done, $this->before_save)");
				$this->save($this->before_save(), 1);
			}
            if($done == 1 && !$validated){
                $Logger->log("Couldnt save ". var_export($this, true));
                return false;
            }
            return true;
		}

		function drop($query=''){
			$Logger = new Logger();
			$connection = new Connection();
			$conn = $connection->connect($this->db);
			$sql = ($query == '') ? "DELETE FROM ".$this->model_name." WHERE id = ".$this->attr['id'].";" : "DELETE FROM ".$this->model_name." WHERE ".$query.";";
			$Logger->log($sql);
			return $conn->query($sql);
		}
		
		function sum($column, $where=''){
			$connection = new Connection();
			$conn = $connection->connect($this->db);
			if($where == ''){
				$sql = "SELECT SUM(".$column.") as result FROM ".$this->model_name;
			}else{
				$sql = "SELECT SUM(".$column.") as result FROM ".$this->model_name." WHERE ".$where.";";
			}
			
			$result = $conn->query($sql);
			while($obj = $result->fetch_object()){
				$z = (array) $obj;
			}
			return $z['result'];
		}
		


		//SELECT * FROM product ORDER BY id DESC;
		function last($or='', $where = '', $full_obj = true){
			$connection = new Connection();
			$conn = $connection->connect($this->db);
			$sql = "SELECT * FROM $this->model_name ";
			$sql .= ($where=='') ? "" : "WHERE $where ";
			$sql .= ($or=='') ? "ORDER BY id DESC LIMIT 1 " : "ORDER BY $or DESC LIMIT 1 ";
			$result__ = $conn->query($sql);
			$result = [];
			while($obj = $result__->fetch_object()){
				$z = (array) $obj;
			}

			while ($current = current($z)) {
				if (array_search(key($z), $this->private)) {
					$result[key($z)] = $current;
				}
			    next($z);
			}
			$r = $this->factory_f($this->model_name);
			$r->attr = $z;
			return $r;
		}

		function first($or=''){
			$connection = new Connection();
			$conn = $connection->connect($this->db);
			$sql = ($or=='') ? "SELECT * FROM $this->model_name ORDER BY id ASC LIMIT 1" : "SELECT * FROM $this->model_name ORDER BY $or ASC LIMIT 1";
			$result__ = $conn->query($sql);
			$result = [];
			while($obj = $result__->fetch_object()){
				$z = (array) $obj;
			}

			while ($current = current($z)) {
				$result[key($z)] = $current;
			    next($z);
			}
			return $result;
		}


		function generateRandomString($length = 10) {
		    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		    $charactersLength = strlen($characters);
		    $randomString = '';
		    for ($i = 0; $i < $length; $i++) {
		        $randomString .= $characters[rand(0, $charactersLength - 1)];
		    }
		    return $randomString;
		}

		function unvar_dump($str) {
		    if (strpos($str, "\n") === false) {
		        //Add new lines:
		        $regex = array(
		            '#(\\[.*?\\]=>)#',
		            '#(string\\(|int\\(|float\\(|array\\(|NULL|object\\(|})#',
		        );
		        $str = preg_replace($regex, "\n\\1", $str);
		        $str = trim($str);
		    }
		    $regex = array(
		        '#^\\040*NULL\\040*$#m',
		        '#^\\s*array\\((.*?)\\)\\s*{\\s*$#m',
		        '#^\\s*string\\((.*?)\\)\\s*(.*?)$#m',
		        '#^\\s*int\\((.*?)\\)\\s*$#m',
		        '#^\\s*bool\\(true\\)\\s*$#m',
		        '#^\\s*bool\\(false\\)\\s*$#m',
		        '#^\\s*float\\((.*?)\\)\\s*$#m',
		        '#^\\s*\[(\\d+)\\]\\s*=>\\s*$#m',
		        '#\\s*?\\r?\\n\\s*#m',
		    );
		    $replace = array(
		        'N',
		        'a:\\1:{',
		        's:\\1:\\2',
		        'i:\\1',
		        'b:1',
		        'b:0',
		        'd:\\1',
		        'i:\\1',
		        ';'
		    );
		    $serialized = preg_replace($regex, $replace, $str);
		    $func = create_function(
		        '$match',
		        'return "s:".strlen($match[1]).":\\"".$match[1]."\\"";'
		    );
		    $serialized = preg_replace_callback(
		        '#\\s*\\["(.*?)"\\]\\s*=>#',
		        $func,
		        $serialized
		    );
		    $func = create_function(
		        '$match',
		        'return "O:".strlen($match[1]).":\\"".$match[1]."\\":".$match[2].":{";'
		    );
		    $serialized = preg_replace_callback(
		        '#object\\((.*?)\\).*?\\((\\d+)\\)\\s*{\\s*;#',
		        $func,
		        $serialized
		    );
		    $serialized = preg_replace(
		        array('#};#', '#{;#'),
		        array('}', '{'),
		        $serialized
		    );

		    return unserialize($serialized);
		}
        
        

	}

?>
