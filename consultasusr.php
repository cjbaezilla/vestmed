<?php
//Obtengo los datos de conexion de la base de datos
//ini_set('display_errors', '0');
session_start();
include("config.php");

function display_mnu($UsrId, $Paso, $Cod_TipPer, $Cod_Cot, $link) {
	$linea ="<form ID=\"F1\" AUTOCOMPLETE=\"off\" method=\"POST\" name=\"F1\">\n";
	$linea.="<li class=\"back-verde\"><a href=\"javascript:window.close()\">Salir</a></li>\n";
	$linea.="<li class=\"back-verde\"><a href=\"javascript:window.print()\">Imprimir</a></li>\n";
        $linea.="<li class=\"back-verde\"><a href=\"consultasusr.php?cot=$Cod_Cot&paso=$Paso\">Consultas</a></li>\n";
	$linea.="</form>\n";
	
	return $linea;
}

$caso    = (isset($_GET['caso'])) ? ok($_GET['caso']) : "";
$paso    = 0;
$UsrId   = (isset($_SESSION['UsrId'])) ? $_SESSION['UsrId'] : "";
$paso    = (isset($_GET['paso'])) ? $_GET['paso'] : "0";
$Perfil  = (isset($_SESSION['Perfil'])) ? $_SESSION['Perfil'] : "";
$Cod_Cot = (isset($_GET['cot'])) ? ok($_GET['cot']) : 0;
$accion  = (isset($_GET['accion'])) ? $_GET['accion'] : "";

$result = mssql_query("vm_s_cothdr $Cod_Cot",$db);
if ((($row = mssql_fetch_array($result)))) {
	$fec_cot   = date("d/m/Y", strtotime($row['Fec_Cot']));
	$Num_Cot   = $row['Num_Cot'];
	$Est_Res   = $row['Est_Res'];
	$cod_clt   = $row['Cod_Clt'];
	$cod_tipper = $row['Cod_TipPer'];
	if ($cod_tipper == 1)
		$nom_clt = $row['Pat_Per']." ".$row['Mat_Per'].", ".$row['Nom_Per'];
	else
		$nom_clt = $row['RznSoc_Per'];
        $nom_clt   = utf8_encode($nom_clt);
	$num_doc   = $row['Num_Doc'];
	$cod_suc   = $row['Cod_Suc'];
	$dir_suc   = utf8_encode($row['Dir_Suc']);
	$cod_cmn   = $row['Cod_Cmn'];
	$cod_cdd   = $row['Cod_Cdd'];
	$cod_per   = $row['Cod_Per'];
	$fon_ctt   = $row['Fon_Ctt'];
	$mail_ctt  = $row['Mail_Ctt'];
	$cod_pre   = $row['Cod_Pre'];
	$obs_cot   = ($row['Obs_Cot'] == "_NONE" ? "" : $row['Obs_Cot']);

	$result = mssql_query("vm_cmn_s $cod_cmn", $db);
	if (($row = mssql_fetch_array($result))) $nom_cmn = utf8_encode($row['Nom_Cmn']);
	
	$result = mssql_query("vm_cdd_s $cod_cdd", $db);
	if (($row = mssql_fetch_array($result))) $nom_cdd = utf8_encode($row['Nom_Cdd']);
	
	$result = mssql_query("vm_suc_s $cod_clt, $cod_suc", $db);
	if (($row = mssql_fetch_array($result))) $nom_suc = utf8_encode($row['Nom_Suc']);

	$result = mssql_query("vm_ctt_s $cod_clt, $cod_suc", $db);
	while($row = mssql_fetch_array($result))
		if ($row['Cod_Per'] == $cod_per) $nom_ctt = utf8_encode($row['Pat_Per']." ".$row['Mat_Per'].", ".$row['Nom_Per']);

	$TieneResp = false;
	$result = mssql_query("vm_s_rescot $Cod_Cot, $cod_clt, $cod_per",$db);
	if (($row = mssql_fetch_array($result))) {
		$Cod_Iva  = $row['Cod_Iva'];
		$Val_Usd  = $row['Val_Usd'];
		$Cod_Cri  = $row['Cod_Cri'];
		$Fec_Cie  = date("d/m/Y", strtotime($row['Fec_Cie']));
		$Val_Pro  = $row['Val_Pro'];
		$Obs_Res  = $row['Obs_Res'];
		$Val_DesG = $row['Val_Des'];
		$TieneResp = true;
	}
}

$ActualizarPadre = false;
if ($accion == "consulta") {
	$ActualizarPadre = true;
	$cod_per = 0;
	$result= mssql_query("vm_s_per_tipdoc 1, '$UsrId'", $db);
	if (($row = mssql_fetch_array($result))) $cod_per = $row['Cod_Per'];
	$consulta = str_replace("\'", "''", utf8_decode($_POST['consulta']));
	//echo "vm_i_cna $Cod_Cot, $cod_clt, $cod_per, '$consulta'";
	$result = mssql_query("vm_i_cna $Cod_Cot, $cod_clt, $cod_per, '$consulta'",$db);
}
if ($accion == "respuesta") {
	$ActualizarPadre = true;
	$cod_per = 0;
	$result= mssql_query("vm_s_per_tipdoc 1, '$UsrId'", $db);
	if (($row = mssql_fetch_array($result))) $cod_per = $row['Cod_Per'];
	$Fol_Cna = $_GET['folio'];
	$respuesta = (isset($_POST['respuesta'.$Fol_Cna])) ? ($_POST['respuesta'.$Fol_Cna]) : "";
	$respuesta = str_replace("\'", "''", utf8_decode($respuesta));
	//echo "vm_i_rescna $Cod_Cot, $cod_clt, $cod_per, $Fol_Cna, '$respuesta'";
	$result = mssql_query("vm_i_rescna $Cod_Cot, $cod_clt, $cod_per, $Fol_Cna, '$respuesta'",$db);
}

?>
<!DOCTYPE html PUBLIC "-//W3C//Dtd XHTML 1.0 transitional//EN" "http://www.w3.org/tr/xhtml1/Dtd/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Catalogo - Vestmed Vestuario M&eacute;dico</title>
<link href="Include/estilos.css" type="text/css" rel="stylesheet" />
<link href="css/layout.css" type="text/css" rel="stylesheet" />
<link href="css/headers.css" type="text/css" rel="stylesheet" />
<link rel="stylesheet" type="text/css" media="screen" href="dropdown/css/uvumi-dropdown.css" />
<link href="css/clearfix.css" type="text/css" rel="stylesheet" />
<!-- Lytebox Includes //-->
<script type="text/javascript" src="lytebox/lytebox.js"></script>
<link rel="stylesheet" type="text/css" href="lytebox/lytebox.css" media="screen" />
<!-- Lytebox Includes //-->
<script type="text/javascript">
    //new UvumiDropdown('dropdown-scliente');

	function popwindow(ventana,altura){
	   window.open(ventana,"Anexos","toolbar=0,location=0,scrollbars=yes,resizable=1,left=60,top=40,width=800,height="+altura);
	}
	
	function ver_producto(cod_prd) {
            popwindow("preview_prd.php?prd="+cod_prd,400)
	}
	
	function CheckRespuesta(form) {
            if (form.consulta.value == "") {
                    alert("Debe ingresar una consulta");
                    return false;
            }
            if (form.consulta.value.length > 1000) {
                    alert("Comentario no puede tener mas de 1.000 caracteres");
                    return false;
            }
            return true;
	}
	
	function Enviar_Res(folio) {
            if (eval('f2.respuesta'+folio).value == "") {
                    alert("Debe ingresar una respuesta");
                    return false;
            }
            if (eval('f2.respuesta'+folio).value.length > 1000) {
                    alert("Comentario no puede tener mas de 1.000 caracteres");
                    return false;
            }
            f2.action = "consultasusr.php?cot=<?php echo $Cod_Cot; ?>&folio="+folio+"&accion=respuesta&paso=<?php echo $paso; ?>";
            f2.submit();
	}

        function ActualizaPadre()
        {
            parent.opener.ActualizarConsultas();
        }

</script>
<?php
	switch ($paso) {
	case 0:
		$page = "preview_cot.php?cot=".$Cod_Cot;
		break;
	case 1:
		$page = "ordendecompra.php?cot=".$Cod_Cot;
		break;
	case 2:
		$page = "pagar.php?cot=".$Cod_Cot;
		break;
	}
	if ($ActualizarPadre and $Est_Res < 9) {
		echo "<script type=\"text/javascript\">\n";
		//echo "	parent.opener.document.F2.action=\"".$page."\"\n";
		//echo "	parent.opener.document.F2.submit();\n";
                echo "  ActualizaPadre()";
		echo "</script>\n";
	}
?>
</head>
<body>
<div id="body" style="width:100%">
	<!--div id="header"></div-->
    <ul id="usuario_registro">
		<?php 	echo display_mnu($UsrId, $paso, $cod_tipper, $Cod_Cot, $db); ?>
    </ul>
	<div id="work">

<?php formar_topbox ("100%%","center"); ?>
<p align="center">
<table BORDER="0" CELLSPACING="1" CELLPADDING="0" width="100%">
<tr>
	<td width="60%" VALIGN="TOP" ROWSPAN="2" HEIGHT="37"><img SRC="logo.gif" width="235" HEIGHT="130" alt="0" /></td>
	<td class="dato" style="text-align: right; FONT-SIZE: 2.5em; FONT-FAMILY: Arial, Verdana;" width="40%" VALIGN="bottom" COLSPAN="2"><b>COTIZACI&Oacute;N <?php echo $Num_Cot; ?></b></td>
</tr>
<tr>
	<td class="dato" style="text-align: right" width="40%" VALIGN="TOP" COLSPAN="2" HEIGHT="65"><b>Fecha: </b><?php echo $fec_cot ?></td>
</tr>
<tr>
	<td width="60%" VALIGN="TOP" class="dato5p12s" style="padding-top: 20px;"><B>Cliente: <?php echo $nom_clt ?></B></td>
	<td width="40%" VALIGN="TOP" COLSPAN="2" style="padding-top: 20px">&nbsp;</td>
</tr>
<tr>
	<td width="60%" VALIGN="TOP" class="dato5p">Rut: <?php echo formatearRut($num_doc) ?></td>
	<td width="40%" VALIGN="TOP" COLSPAN="2">&nbsp;</td>
</tr>
<tr>
	<td width="60%" VALIGN="TOP" class="dato5p">Sucursal: <?php echo $nom_suc ?></td>
	<td width="40%" VALIGN="TOP" COLSPAN="2" class="dato">Contacto: <?php echo $nom_ctt ?></td>
</tr>
<tr>
	<td width="60%" VALIGN="TOP" class="dato5p">Direcci&oacute;n: <?php echo $dir_suc ?></td>
	<td width="40%" VALIGN="TOP" COLSPAN="2" class="dato">Tel&eacute;fono: <?php echo $fon_ctt ?></td>
</tr>
<tr>
	<td width="60%" VALIGN="TOP" class="dato5p">Ciudad: <?php echo $nom_cdd ?></td>
	<td width="40%" VALIGN="TOP" COLSPAN="2" class="dato">Fax: <?php echo $fon_ctt ?></td>
</tr>
<tr>
	<td width="60%" VALIGN="TOP" class="dato5p">Comuna: <?php echo $nom_cmn ?></td>
	<td width="40%" VALIGN="TOP" COLSPAN="2" class="dato">Email: <?php echo $mail_ctt; ?></td>
</tr>
<tr>
	<td width="60%" VALIGN="TOP">&nbsp;</td>
	<td width="40%" VALIGN="TOP" COLSPAN="2">&nbsp;</td>
</tr>
<tr>
	<td VALIGN="TOP" COLSPAN="3" class="dato5p12s"><B>Consultas</B></td>
</tr>
<tr>
<td VALIGN="TOP" COLSPAN="3" style="padding-bottom: 10px">
	<form ID="F2" method="POST" name="F2" ACTION="consultasusr.php?cot=<?php echo $Cod_Cot ?>&accion=consulta&paso=<?php echo $paso; ?>" onsubmit="return CheckRespuesta(this)">
	<table BORDER="0" CELLSPACING="1" CELLPADDING="0" width="100%" ALIGN="center">
	<tr>
		<td width="60%" VALIGN="TOP" class="dato10p" colspan="2" style="PADDING-BOTTOM:30px">
		<!--textarea name="comentarios" id="comentarios" cols="80" rows="3" class="dato" ReadOnly><?php echo $obs_cot; ?></textarea></td-->
		<table width="100%" BORDER="0" CELLSPACING="0" CELLPADDING="1" ALIGN="left">
		<tr>
			<td class="titulo_tabla" valign="top" style="text-align: left" width="10%" height="15">Fecha</td>
			<td class="titulo_tabla" valign="top" style="text-align: left" width="5%" height="15">Folio</td>
			<td class="titulo_tabla" valign="top" style="text-align: left" width="10%" height="15">Origen</td>
			<td class="titulo_tabla" valign="top" style="text-align: left" width="65%" height="15">Consulta / Respuesta</td>
			<td class="titulo_tabla" valign="top" style="text-align: left" width="10%" height="15">&nbsp;</td>
		</tr>
		<?php 
			$result = mssql_query("vm_hiscna_cot $Cod_Cot, $cod_clt", $db);
			$bOkRespuesta = false;
			$totfilas = 0;
                        $bXisSinLec = false;
			while (($row = mssql_fetch_array($result))) {
				$totfilas++;
                                $boldbegin = "";
                                $boldend = "";
                                $flg_lec = $row['flg_lec'];
                                if ($flg_lec == 0 and $row['Num_Doc'] == "Vestmed") {
                                    $boldbegin = "<b>";
                                    $boldend = "</b>";
                                    $bXisSinLec = true;
                                }
		?>
				<tr>
					<td align="left" valign="top"><?php echo $row['Fec_Dis']; ?></td>
					<td align="left" valign="top"><?php echo $row['Fol_Cna']; ?></td>
					<td align="left" valign="top"><?php echo ($row['Num_Doc'] == "Vestmed" ? $row['Num_Doc'] : "Cliente" ); ?></td>
					<td align="left" valign="top" colspan="2"><?php echo utf8_encode($row['Det_Cna']); ?></td>
				</tr>
		<?php if (trim($row['Det_Res']) != "") { ?>
				<tr>
					<td align="left" valign="top"><?php echo $row['Fec_ResCnaDis']; ?></td>
					<td align="left" valign="top"><?php echo $row['Fol_Cna']; ?></td>
					<td align="left" valign="top"><?php echo ($row['Num_DocRes'] == "Vestmed" ? $row['Num_DocRes'] : "Cliente"  ); ?></td>
					<td align="left" valign="top" colspan="2"><?php echo utf8_encode($row['Det_Res']); ?></td>
				</tr>
		<?php } else { 
				$autor = $row['Num_Doc'];
				if ($autor == "Vestmed") {
		?>
				<!--tr>
					<td align="left" valign="top"><?php echo date("d/m/Y") ?></td>
					<td align="left" valign="top"><?php echo $row['Fol_Cna']; ?></td>
					<td align="left" valign="top">Cliente</td>
					<td align="left" valign="top"><textarea class="dato" rows="2" cols="80" name="respuesta<?php echo $row['Fol_Cna'] ?>"></textarea></td>
					<td align="right" valign="top"><input type="button" name="Responder<?php echo $row['Fol_Cna'] ?>" value="Responder" onclick="javascript:Enviar_Res(<?php echo $row['Fol_Cna'] ?>)" class="btn"></td>
				</tr-->
		<?php
			    }
			  }
			}
                        if ($bXisSinLec)
                            $result = mssql_query("vm_mcacot_leidos_usr $Cod_Cot, $cod_clt", $db);
		?>
		</table>
	</tr>
	<tr>
		<td width="22%" VALIGN="TOP" class="dato10p"><b>Nueva Consulta:</b></td>
		<td width="78%" VALIGN="TOP"><textarea class="dato" rows="5" cols="80" name="consulta"></textarea></td>
	</tr>
	<tr>
		<td width="100%" VALIGN="TOP" colspan="2" align="right">
			<input type="submit" name="Enviar" value="Enviar" class="btn">&nbsp;
		</td>
	</tr>
	</table>
	</form>
</td></tr>
</table>
</p>
<?php formar_bottombox (); ?>
    </div>
    <!--div id="footer"></div-->
</div>
<script type="text/javascript">
	var f2;
	f2 = document.F2;
</script>
</body>
</HTML>
