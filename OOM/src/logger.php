<?php
    /*
        Build by eulr @ eulr.mx
        hola@eulr.mx
        v0.1.3-alpha
    */
    class Logger{
        public $ENV = "test";
        public $PATH = "";
        
        function __construct($P_env = "development", $time_zone='America/Mexico_City'){
            date_default_timezone_set($time_zone);
            $this->ENV = $P_env;
        }
        
        function get_date(){
            return date('l jS \of F Y h:i:s A');
        }
        
        function get_file_name(){
            return $this->PATH.$this->ENV."_log.txt";
        }
        
        function log($log, $space = true){
            $actual = file_get_contents($this->get_file_name());
            $actual_link = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
            if($space){
                $actual .= "$actual_link: [".$this->get_date().": \t ".var_export($log, true)."\n------------------------------------\n";
            }else{
                $actual .= "\n".var_export($log, true)."\n";
            }
            file_put_contents($this->get_file_name(), $actual);
        }
        
    }
?>