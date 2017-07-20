# PHPrecord 

Hay 2 formas basicas de usar phprecord,
creando las clases de tu modelo para poder definir
funciones como private (para restringir algun atributo) y
before_save (valida información en tu objeto) 

# Empezando

Configura el archivo connection.php con tus datos de mysql
y configura $db en PHPrecord.php al nombre de tu base de datos

## Crear clase

Creas una clase con los datos de tu base de datos 
```
<?php
    require_once 'PHPrecord.php';

    class Products extends PHPrecord{
        function __construct(){
            $this->model_name = "products";
            $this->private = [];
        }
    }
?>

```

Igual podrías realizar un factory_f sin embargo no es el método recomendado.
```
$p = PHPrecord::factory_f("producs");
```

Ahora podrás instanciar objetos de esa clase y tener acceso los métodos de PHPrecord

# Formación de queries

Donde
```
$p = new Product();
```
### Leer/Obtener información

PHPrecord provee de una forma fácil de realizar tus consultas a tus tablas 
