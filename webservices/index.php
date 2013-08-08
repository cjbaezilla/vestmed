<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title></title>
    </head>
    <body>
        <?php
            // Crear el cliente colocando la ruta URL donde se encuentra el servicio
            $cliente = new SoapClient(null, array('location' => 'http://localhost/vestmed/webservices/servicio.php',
                                                  'uri' => 'urn:webservices', )); // Llamar al método como si fuera del cliente
            echo $cliente->multiplica(5,4);
        ?>
    </body>
</html>
