<?php
	//Obtengo los datos de conexion de la base de datos
	ini_set('display_errors', '0');
	session_start();
	include("config.php");

	$cod_cot = 0;
	if (isset($_SESSION['CodCot'])) $cod_cot = intval($_SESSION['CodCot']);
	if (!isset($_POST['accion'])) {
		$accion = ok($_GET['accion']); 
		
		if ($accion == "delete")
			foreach ($_POST as $key => $value) {
				if ($key == "seleccionadof")
					foreach ($value as $cod_sec) {
						//echo "vm_d_cotweb ".$cod_cot.", ".$cod_sec;
						$result = mssql_query("vm_d_cotweb $cod_cot, $cod_sec", $db)
							 or die ("No se pudo efectuar operacion en Tabla de Cotizaciones");
					}
			}         
		else if ($accion == "update") {
			$cod_sec = ok($_GET['sec']);
			foreach ($_POST as $key => $value) 
				if ($key == "dfCtd".$cod_sec) $cantidad = intval($value);
			
			$result = mssql_query("vm_u_cotweb $cod_cot, $cod_sec, $cantidad", $db)
				 or die ("No se pudo efectuar operacion en Tabla de Cotizaciones");
				 
		}
		else if ($accion == "deldetalle") {
			foreach ($_POST as $key => $value) 
				if ($key == "dfEliminados") $campos = split (";", $value);

			for ($i = 0; $i < count($campos)-1; $i++)
				$result = mssql_query("vm_d_cotweb $cod_cot, $campos[$i]", $db)
					or die ("No se pudo efectuar operacion en Tabla de Cotizaciones");
				 
		}
		mssql_close ($db);
		if ($accion != "deldetalle")
			header("Location: cotizaciones.php");
		else {
			$prd = ok($_GET['producto']); 
			$ttl = ok($_GET['title']); 
			
			//header("Location: detalle-cotizacion.php?producto=".$prd."&title=".$ttl);
			header("Location: detalle-cotizacion.php");
		}
	}
	else {
		$accion   = ok($_POST['accion']);
		$cod_sec  = ok($_POST['sec']);
		$cantidad = ok($_POST['ctd']);
		
		$result = mssql_query("vm_u_cotweb $cod_cot, $cod_sec, $cantidad", $db)
			 or die ("No se pudo efectuar operacion en Tabla de Cotizaciones");

		header("Content-type: text/xml");
		header("Cache-Control: no-cache");
		echo "<?xml version=\"1.0\"?>\n";
		echo "<response>\n";
		$result = mssql_query("vm_calpeso_cot $cod_cot", $db);
		if ($row = mssql_fetch_array($result)) {
            spitXml("Peso",$row['peso']);
		}
		echo "</response>\n";
	}

	function spitXml($code,$value)
	{
		echo "\t<filter>\n";
		echo "\t\t<code>".$code."</code>\n";
		echo "\t\t<value>".$value."</value>\n";
		echo "\t</filter>\n";
	}
	
?>