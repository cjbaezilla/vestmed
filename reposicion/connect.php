<?php

//PARAMETROS DE CONEXION
//$dbhost = '192.168.0.20';
//$dbhost = '192.168.6.8';
$dbhost = 'MLABRIN\\SQLEXPRESS';
//$dbuser = "msgndes";
$dbuser = "usrwebvestmed";
//$dbpasswd = 'msgndes';
$dbpasswd = 'microweb2009';
//$dbname = "VestmedProd";
$dbname = "vestmed";
$prefix = '';
$correovestmed = "mlabrinb@vtr.net;mlabrin@labcor.cl";
//$pathadjuntos = "./adjuntos/";
$pathadjuntos = "C:\\Windows\\Temp\\";
$home = "http://localhost/vestuariomedico";
//$home = "http://localhost/vestmed";

//NEW
$datetime = date("Y-m-d H:i:s");
$db = @mssql_connect($dbhost, $dbuser, $dbpasswd);
if(!$db)
die('<font size=+1>An Error Occurred</font><hr>Unable to connect to the database.');
if(!@mssql_select_db($dbname))
die("<font size=+1>An Error Occurred</font><hr>Unable to find the database <b>$dbname</b>.");

?>
