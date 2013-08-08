<?php

//PARAMETROS DE CONEXION
//$dbhost = '192.168.0.20';
//$dbhost = '192.168.6.8';
$dbhost = 'MLABRINB-PC\\SQLEXPRESS';
//$dbuser = "msgndes";
$dbuser = "usrdemo";
//$dbpasswd = 'msgndes';
$dbpasswd = 'demo2011';
//$dbname = "vestmed";
$dbname = "Pvestmed";
$prefix = '';
$correovestmed = "contacto@ti-vam.cl";
$pathadjuntos = "./adjuntos/";
//$pathadjuntos = "c:\\temp\\";
$home = "http://localhost/vestmed";
//$home = "http://localhost/vestmed";
$passAprobacionPago="p4g0ok";

//NEW
$datetime = date("Y-m-d H:i:s");

$db = @mssql_connect($dbhost, $dbuser, $dbpasswd);
if(!$db)
die('<font size=+1>Un Error ha ocurrido</font><hr>Imposible conectarse a la base de datos.');
if(!@mssql_select_db($dbname))
die("<font size=+1>Un Error ha ocurrido</font><hr>No fue posible encontrar la base de datos <b>$dbname</b>.");

?>
