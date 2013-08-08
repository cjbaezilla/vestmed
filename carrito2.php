<?php

	//Obtengo los datos de conexion de la base de datos
	ini_set('display_errors', '1');
	session_start();
	include("config.php");

	$cod_per = 0;
	$cod_clt = 0;
	$cod_cot = 0;
	if (isset($_SESSION['CodPer'])) $cod_per = intval($_SESSION['CodPer']);
	if (isset($_SESSION['CodClt'])) $cod_clt = intval($_SESSION['CodClt']);
	if (isset($_SESSION['CodCot'])) $cod_cot = intval($_SESSION['CodCot']);
	
        echo "POST: <br>";
	foreach ($_POST as $key => $value) {
            echo $key." --> ".$value."<BR>";
	}

?>
