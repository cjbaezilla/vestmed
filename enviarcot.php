<?php
	//Obtengo los datos de conexion de la base de datos
	ini_set('display_errors', '0');
	session_start();
	include("config.php");

	$Cod_Per = 0;
	$Cod_Clt = 0;
	$cod_cot = 0;
	if (isset($_SESSION['CodPer'])) $Cod_Per = intval($_SESSION['CodPer']);
	if (isset($_SESSION['CodClt'])) $Cod_Clt = intval($_SESSION['CodClt']);
	if (isset($_SESSION['CodCot'])) $cod_cot = intval($_SESSION['CodCot']);

	foreach ($_POST as $key => $value) {
		//echo $key." --> ".$value."<BR>";
		if ($key == "bordado1")      $bordado1     = ($value == "_NONE") ? 0 : intval($value);
		if ($key == "bordado2")      $bordado2     = ($value == "_NONE") ? 0 : intval($value);
		if ($key == "despacho")      $despacho     = ($value == "_NONE") ? 0 : intval($value);
		if ($key == "dfCrr")         $dfCrr        = ($value == "_NONE") ? "null" : intval($value);
		if ($key == "dfCrrSvc")      $dfCrrSvc     = ($value == "_NONE") ? "null" : intval($value);
		if ($key == "dfSuc")         $Cod_Suc      = ($value == "_NONE") ? "null" : intval($value);
		if ($key == "dfDirSuc")      $dfDirSuc     = ($value == "_NONE") ? "" : str_replace("\'", "''", utf8_decode ($value));
		if ($key == "dfPersonas")    $dfPersonas   = ($value == "_NONE") ? "null" : intval($value);
		if ($key == "dfPrecios")     $dfPrecios    = ($value == "_NONE") ? "null" : intval($value);
		if ($key == "dfComentario")  $dfComentario = str_replace("\'", "''", utf8_decode($value));
		if ($key == "dfVal_Pso")     $dfVal_Pso    = floatval($value);
		if ($key == "dfSucFct")      $Cod_SucFct   = ($value == "_NONE") ? "null" : intval($value);
		if ($key == "dfValTipSvc")   $dfValTipSvc  = ($value == "_NONE") ? "null" : intval($value);
		if ($key == "dfPesoPer")     $peso         = intval($value);
		if ($key == "dfEstaturaPer") $estatura     = intval($value);
		if ($key == "dfFlgTer")      $flgter       = intval($value);
	}
	$dfDirSuc = "No Determinada";
	$Cod_Cmn = -1;
	$Cod_Cdd = -1;		
	if ($Cod_Suc > 0) {
            $result = mssql_query("vm_suc_s $Cod_Clt, $Cod_Suc", $db);
            if (($row = mssql_fetch_array($result))) {
                $dfDirSuc = str_replace("'", "''", $row["Dir_Suc"]);
                $Cod_Cmn = $row["Cod_Cmn"];
                $Cod_Cdd = $row["Cod_Cdd"];
            }
            mssql_free_result($result);
	}
	
	$query = "vm_i_cotsvcweb $cod_cot, $bordado1, $bordado2, $despacho, $dfCrr, $dfCrrSvc, $Cod_Cmn, $Cod_Cdd, $Cod_Suc, '$dfDirSuc', $dfPersonas, $dfPrecios, '$dfComentario', $dfVal_Pso, $Cod_SucFct, $dfValTipSvc, $peso, $estatura, $flgter";
	$result = mssql_query("vm_i_cotsvcweb $cod_cot, $bordado1, $bordado2, $despacho, $dfCrr, $dfCrrSvc, $Cod_Cmn, $Cod_Cdd, $Cod_Suc, '$dfDirSuc', $dfPersonas, $dfPrecios, '$dfComentario', $dfVal_Pso, $Cod_SucFct, $dfValTipSvc, $peso, $estatura, $flgter",$db) 
                              or die ("No se pudo insertar servicios en la cotizacion<BR>".$query);
	if (($row = mssql_fetch_array($result))) $num_cot = $row['Num_Cot'];

	$cuerpo_mail  = cuerpo_cotizacion (51, $cod_cot, $db);
	$asunto       = "Pedido de Cotizacion Nro ".$num_cot; 
	$correos = split(";", $correovestmed);
	foreach ($correos as $key => $destinatario)
	    enviar_mail ($destinatario, $asunto, $cuerpo_mail, "HTML");
	
	mssql_close ($db);

	header("Location: aviso.php?id=".$Cod_Per."&idmsg=4");
?>
