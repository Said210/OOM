<?php
/*
	Build by eulr @ eulr.mx
	hola@eulr.mx
    v0.5.5-beta
*/
	require_once 'connection.php';
    require_once 'logger.php';
	class OOM{
		public $model_name = "";
		public $db = "notedice_SS";
		// public $db = "SS";
		public $before_save = null;
		public $attr = [];
        
		function all($params = []){
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
		    	array_push($r, $obj);
		    }
			for($i = 0; $i < count($r); $i++){
				$r[$i] =  $r[$i]->attr;
			}
			return $r;
		}

		function find($id, $full_obj = true){
			$connection = new Connection();
			$conn = $connection->connect($this->db);

			$result = $conn->query("SELECT * FROM ".$this->model_name." WHERE id=".$id);
			while($obj = $result->fetch_object()){
				$z = (array) $obj;
			}

			while ($current = current($z)) {
				$this->attr[key($z)] = $current;
			    next($z);
			}
            if($full_obj){
			     return $this;
            }else{
                return $this->attr;
            }
			//echo var_dump($this);
		}

		function get_many($ids_, $c=null){
			$ids = split(",", $ids_);
			$query = "";
			for ($i=0; $i < count($ids); $i++) {
				$query .= ($c == null) ? "id = '".$ids[$i]."' OR " : $c." = '".$ids[$i]."' OR " ;
			}
			$query = substr($query, 0, -3);

			$result = $this->where($query);
			
			return $result;
		}

		function find_by($attr, $value, $full_obj = true){
            $Logger = new Logger();
			$connection = new Connection();
			$conn = $connection->connect($this->db);
			$r = [];
			$result = [];
			if (is_numeric($attr)) {
				$result__ = $conn->query("SELECT * FROM ".$this->model_name." WHERE ".$attr." = ".$value.";");
			}else{
				$result__ = $conn->query("SELECT * FROM ".$this->model_name." WHERE ".$attr." = '".$value."';");
			}

			while ($row = $result__->fetch_assoc()) {
		        array_push($result, $row);
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
		        array_push($result, $row);
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
			$conn = $connection->connect($this->db);
			$r = [];
			$result = [];
			// echo "SELECT * FROM ".$this->model_name." WHERE ".$value;
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
				if(!$r){ echo mysqli_error($conn)."<br><b>".$sql."</b><br><i>".var_dump($this->attr)."</i><hr>"; $r = mysqli_error($conn);}
			}
			if (!$validated && $done == -1) {
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
		function last($or=''){
			$connection = new Connection();
			$conn = $connection->connect($this->db);
			$sql = ($or=='') ? "SELECT * FROM $this->model_name ORDER BY id DESC LIMIT 1" : "SELECT * FROM $this->model_name ORDER BY $or DESC LIMIT 1";
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
