<?php
ini_set('display_errors', '0');
session_start();
include("config.php");

$Cod_Per = isset($_SESSION['CodPer']) ? intval($_SESSION['CodPer']) : 0;
$Cod_Clt = isset($_SESSION['CodClt']) ? intval($_SESSION['CodClt']) : 0;

if ($Cod_Per > 0 && $Cod_Clt > 0) {
	$doc_id = 1;
	
	foreach ($_POST as $key => $value) {
		//echo $key." --> ".$value."<BR>";
		if ($key == "dfPasswordOld") $oldclave = $value;
		if ($key == "dfPasswordNew1") $clavenew1 = $value;
		if ($key == "dfRutUsrIn") $RutUsr = str_replace(".", "", $value);
		if ($key == "dfRutClt") $RutClt = str_replace(".", "", $value);
	}
		
	//echo "vm_sndpwd $doc_id, '$RutClt', $doc_id, '$RutUsr'"."<BR>";
	$result = mssql_query ("vm_sndpwd $doc_id, '$RutClt', $doc_id, '$RutUsr'", $db)
							or die ("No se pudo leer datos del usuario");
	if ($row = mssql_fetch_array($result)) {
		$Cod_Clt2 = $row["Cod_Clt"];
		$Cod_Per2 = $row["Cod_Per"];
		$clavehoy = desencriptar($row["Pwd_Web"]);
	}
	mssql_free_result($result); 
	//echo $Cod_Clt2."<BR>".$Cod_Per2."<BR>".$Cod_Per."<BR>".$Cod_Clt;
	
	if ($Cod_Per2 == $Cod_Per && $Cod_Clt2 == $Cod_Clt) {
		if ($clavehoy == $oldclave) {
			$claveenc = encriptar($clavenew1, 30);
			$result = mssql_query ("vm_usrweb_u $Cod_Clt, $Cod_Per, '$claveenc'", $db)
							or die ("No se pudo actualizar la clave del usuario");
			header("Location: micuenta.php?accion=pwd&idmsg=0"); 
		}
		else
			header("Location: micuenta.php?accion=pwd&idmsg=1"); 
	}
	else
		header("Location: micuenta.php?accion=pwd&idmsg=2"); 
}
else
	header("Location: micuenta.php?accion=pwd&idmsg=3"); 
?>
