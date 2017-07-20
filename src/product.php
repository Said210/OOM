<?php
    require_once 'PHPrecord.php';

    class Products extends PHPrecord{
        function __construct(){
            $this->model_name = "products";
            $this->private = [];
        }
    }
    $PRODUCTS = new Products(); // Optional, but cool.
?>
