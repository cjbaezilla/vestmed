<?php
//Obtengo los datos de conexion de la base de datos
ini_set('display_errors', '0');
session_start();

if (!isset($_SESSION['usuario'])) {
    if (!isset($_POST["usuario"])) header("Location: ../index.php");
    $_SESSION['usuario'] = $_POST["usuario"];     
}
$UsrId = strtoupper((isset($_SESSION['usuario'])) ? $_SESSION['usuario'] : "");

include("global_cot.php");

$OkOpc = false;
$sp = mssql_query("vm_seg_usr_opcmodweb '$UsrId'",$db) or die ("error sql, vm_seg_usr_opcmodweb '$UsrId'");
//echo "vm_seg_usr_opcmodweb '$UsrId'<br>";
while (($row = mssql_fetch_array($sp))) {
    //echo "[".$row["Id_Mod"]."] [".$row["ID_Opc"]."] [".$row['CodUsr']."]<br>";
    if ($row["Id_Mod"] == 1 && $row["ID_Opc"] == 9 && strtoupper($row['CodUsr']) == strtoupper($UsrId)) {
        $OkOpc = true;
        break;
    }
}
mssql_free_result($sp);

if (!$OkOpc) {
    //header ("Location:../index.php");
    //header ("Location:mivestmed.php");
    exit(0);
}


$Cod_Cot = (isset($_GET['cot'])) ? ok($_GET['cot']) : 0;
$folctt = (isset($_GET['folctt'])) ? ok($_GET['folctt']) : 0;

$accion = (isset($_GET['accion'])) ? $_GET['accion'] : "";
$Tip_Bus = (isset($_POST['tipo_bus']) ? ok($_POST['tipo_bus']) : 'P');
$Cod_PerBus = (isset($_POST['cod_per']) ? ok($_POST['cod_per']) : 0);

$ActualizaPadre = false;

if ($Cod_Cot > 0) {	
    $result = mssql_query("vm_s_cothdr $Cod_Cot",$db);
    if (($row = mssql_fetch_array($result))) {
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
        while(($row = mssql_fetch_array($result)))
            if ($row['Cod_Per'] == $cod_per) $nom_ctt = utf8_encode ($row['Pat_Per']." ".$row['Mat_Per'].", ".$row['Nom_Per']);

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
}
else if ($folctt > 0) {
    $result = mssql_query("vm_cttweb_s $folctt", $db);
    if ((($row = mssql_fetch_array($result)))) {
        $num_doc = $row['rut_ctt'];
        $nom_clt = utf8_encode($row['nom_ctt']);
        $fon_ctt = $row['fon_ctt'];
        $email   = $row['email'];
        //$fec_ctt = date("d/m/Y", strtotime($row['fec_ctt']));
        $fec_ctt = $row['fec_ctt_display'];
        $tip_cna = $row['tip_cna'];

        $result = mssql_query("vm_s_per_tipdoc 1, '$num_doc'",$db);
        if ((($row = mssql_fetch_array($result)))) {
            $cod_per = $row['Cod_Per'];
            $cod_clt = $row['Cod_Clt'];
        }
    }
}

if ($accion == "respuesta") {
    $Fol_Cna = $_GET['folio'];
    $respuesta = (isset($_POST['respuesta'.$Fol_Cna])) ? ($_POST['respuesta'.$Fol_Cna]) : "";
    $respuesta = str_replace("\'", "''", $respuesta);
    if ($Cod_Cot > 0) {
        $ActualizarPadre = true;
        $cod_per = 0;
        $result = mssql_query("vm_i_rescna $Cod_Cot, $cod_clt, $cod_per, $Fol_Cna, '$respuesta'",$db);
    }
    else {
        $ActualizarPadre = true;
        $result = mssql_query("vm_i_rescnactt $folctt, $tip_cna, 0, $Fol_Cna, '$respuesta'",$db);
    }
}

if ($accion == 21 or $accion == 22 or $accion == 312 or $accion == 412) {
    if (isset($_GET['cot'])) {
        $Cod_Cot = ok($_GET['cot']);
        $cod_per = 0; // la hace vestmed
        $result = mssql_query("vm_s_cothdr $Cod_Cot",$db);
        if ((($row = mssql_fetch_array($result)))) {
            $cod_clt = $row['Cod_Clt'];
            $mail_ctt = $row['Mail_Ctt'];
        }
        $consulta = str_replace("\'", "''", utf8_decode($_POST['consulta']));
        $result = mssql_query("vm_i_cna $Cod_Cot, $cod_clt, $cod_per, '$consulta'",$db);
        $casomail = 0;
        include("avisonewmensaje.php");
        $asunto       = "Vestmed Ltda.| Tienes un Nuevo Mensaje Pendiente";
        enviar_mail ($mail_ctt, $asunto, $cuerpo_mail, "HTML");
        if ($accion == 312 or $accion == 412) $accion = $accion - 1;
        else $accion = 12;
        
        if ($accion == 411) $ActualizaPadre = true;
    } else if (isset($_GET['folctt'])) {
        $folctt = ok($_GET['folctt']);
        $result = mssql_query("vm_cttweb_s $folctt", $db);
        if ((($row = mssql_fetch_array($result)))) {
            $cod_per = $row['Cod_Per'];
            $mail_ctt = $row['email'];
        }
        $consulta = str_replace("\'", "''", utf8_decode($_POST['consulta']));
        //echo "vm_i_cnactt $folctt, $cod_per, '$consulta'";
        $result = mssql_query("vm_i_cnactt $folctt, $cod_per, '$consulta'", $db);
        $casomail = 1;
        include("avisonewmensaje.php");
        $asunto       = "Vestmed Ltda.| Tienes un Nuevo Mensaje Pendiente";
        enviar_mail ($mail_ctt, $asunto, $cuerpo_mail, "HTML");
        $accion = 11;
    }
}

?>
<!DOCTYPE html PUBLIC "-//W3C//Dtd XHTML 1.0 transitional//EN" "http://www.w3.org/tr/xhtml1/Dtd/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Catalogo - Vestmed Vestuario M&eacute;dico</title>
<link href="../css/layout.css" type="text/css" rel="stylesheet" />
<script type="text/javascript" src="../js/mootools-1.2.1-core-yc.js"></script>
<script type="text/javascript" src="../js/mootools-1.2-more.js"></script>
<script type="text/javascript" src="../dropdown/js/dropdown.js"> </script>
<link rel="stylesheet" type="text/css" media="screen" href="../dropdown/css/uvumi-dropdown.css" />
<!--[if IE]>
<link rel="stylesheet" type="text/css" media="screen" href="dropdown/css/uvumi-dropdown-ie.css" />
<![endif]-->
<script type="text/javascript" src="../Include/ValidarDataInput.js"></script>
<script type="text/javascript" src="../Include/SoloNumeros.js"></script>
<script type="text/javascript" src="../Include/validarRut.js"></script>
<LINK href="../Include/estilos.css" type="text/css" rel="stylesheet" />
<link rel="stylesheet" type="text/css" media="all" href="../Include/calendar-green.css" title="win2k-cold-1" />
<script type="text/javascript" src="../Include/calendar.js"></script>
<script type="text/javascript" src="../Include/calendar-es.js"></script>
<script type="text/javascript" src="../Include/calendar-setup.js"></script>
<script type="text/javascript" src="js/AccionesMenu.js"></script>
<script type="text/javascript">
    new UvumiDropdown('dropdown-scliente');

    function MarcarTodos(form,nombrecheckbox) {
       for (i=0; i<form.elements.length; i++) {
            if (form.elements[i].name == nombrecheckbox)
                    form.elements[i].checked = true;
       }
    }

    function DesMarcarTodos(form,nombrecheckbox) {
       for (i=0; i<form.elements.length; i++) {
            if (form.elements[i].name == nombrecheckbox)
                    form.elements[i].checked = false;
       }
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
        <?php if ($Cod_Cot > 0) { ?>
        f2.action = "mensajes2.php?cot=<?php echo $Cod_Cot; ?>&folio="+folio+"&accion=respuesta";
        <?php } else { ?>
        f2.action = "mensajes2.php?folctt=<?php echo $folctt; ?>&folio="+folio+"&accion=respuesta";
        <?php } ?>
        f2.submit();
    }

    function ActualizaPadre()
    {
        parent.opener.actualizar_qtymsg();
    }

    function Salir(caso) {
        if (caso == 411) {
            window.close();
        }
        else {
            if (caso == 311)
                f2.action = "previewodc.php?cot=<?php echo $Cod_Cot; ?>";
            else
                f2.action = "mensajes.php?accion="+caso;
            f2.submit();
        }
    }
</script>
</head>

<body>
<div id="body">
    <div id="work">
<?php formar_topbox ("100%%","center"); ?>
<table WIDTH="100%" BORDER="0" CELLSPACING="0" CELLPADDING="1" ALIGN="center">
<tr>

<td width="1%">&nbsp;</td>
<td  valign="top" class="label_left_right_top_bottom" STYLE="PADDING-LEFT:20px; PADDING-RIGHT:20px; TEXT-ALIGN:left">
<?php if ($Cod_Cot > 0) { ?>
<table BORDER="0" CELLSPACING="1" CELLPADDING="0" width="100%">
<tr>
	<td width="60%" VALIGN="TOP" ROWSPAN="2" HEIGHT="37"><img SRC="logo.gif" width="235" HEIGHT="130" alt=""></td>
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
	<form ID="F2" method="POST" name="F2" ACTION="mensajes2.php?accion=<?php if ($accion == 311 || $accion == 411) echo $accion+1; else echo "22"; ?>&cot=<?php echo $Cod_Cot; ?>">
	<table BORDER="0" CELLSPACING="1" CELLPADDING="0" width="100%" ALIGN="center">
	<tr>
		<td width="60%" VALIGN="TOP" class="dato10p" colspan="2">
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
                        if ($flg_lec == 0 and $row['Num_Doc'] != "Vestmed") {
                            $boldbegin = "<b>";
                            $boldend = "</b>";
                            $bXisSinLec = TRUE;
                        }
		?>
                <tr>
                        <td align="left" valign="top"><?php echo $boldbegin.$row['Fec_Dis'].$boldend; ?></td>
                        <td align="left" valign="top"><?php echo $boldbegin.$row['Fol_Cna'].$boldend; ?></td>
                        <td align="left" valign="top"><?php echo $boldbegin.($row['Num_Doc'] == "Vestmed" ? $row['Num_Doc'] : "Cliente" ).$boldend; ?></td>
                        <td align="left" valign="top" colspan="2"><?php echo $boldbegin.utf8_encode($row['Det_Cna']).$boldend; ?></td>
                </tr>
		<?php if (trim($row['Det_Res']) != "") { ?>
                <tr>
                        <td align="left" valign="top"><?php echo $row['Fec_ResCnaDis']; ?></td>
                        <td align="left" valign="top"><?php echo $row['Fol_Cna']; ?></td>
                        <td align="left" valign="top"><?php echo ($row['Num_DocRes'] == "Vestmed" ? $row['Num_DocRes'] : "Cliente"  ); ?></td>
                        <td align="left" valign="top" colspan="2"><?php echo $boldbegin.utf8_encode($row['Det_Res']).$boldend; ?></td>
                </tr>
		<?php } else { 
                        $autor = $row['Num_Doc'];
                        if ($autor != "Vestmed") {
		?>
                            <!--tr>
                                    <td align="left" valign="top"><?php echo date("d/m/Y") ?></td>
                                    <td align="left" valign="top"><?php echo $row['Fol_Cna']; ?></td>
                                    <td align="left" valign="top">Vestmed</td>
                                    <td align="left" valign="top"><textarea class="dato" rows="2" cols="80" name="respuesta<?php echo $row['Fol_Cna'] ?>"></textarea></td>
                                    <td align="right" valign="top"><input type="button" name="Responder<?php echo $row['Fol_Cna'] ?>" value="Responder" onclick="javascript:Enviar_Res(<?php echo $row['Fol_Cna'] ?>)" class="btn"></td>
                            </tr-->
		<?php
                        }
                      }
                    }
                    if ($bXisSinLec)
                        $result = mssql_query("vm_mcacot_leidos $Cod_Cot, $cod_clt", $db);
		?>
		</table>
                </td>
	</tr>
        <tr>
            <td width="20%" VALIGN="TOP" class="dato10p" style="padding-top: 30px;"><b>Nuevo Mensaje</b></td>
            <td width="80%" VALIGN="TOP" class="dato" style="padding-top: 30px">
            <textarea class="textfieldv2" rows="5" cols="100" name="consulta"></textarea>
            </td>
        </tr>
	<tr>
            <td width="100%" VALIGN="TOP" colspan="5" align="right" style="padding-top: 30px">
                    <input type="button" name="Volver" value="Volver" class="btn" onclick="Salir(<?php if ($accion == 311 || $accion == 411) echo $accion; else echo "12"; ?>)">&nbsp;&nbsp;
                    <input type="submit" name="Enviar" value=" Enviar " class="btn">
                    <input type="hidden" name="tipo_bus" value="<?php echo $Tip_Bus; ?>">
                    <input type="hidden" name="cod_per" value="<?php echo $Cod_PerBus; ?>">
            </td>
	</tr>
	</table>
	</form>
</td></tr>
</table>
<?php } else { ?>
<form ID="F2" method="POST" name="F2" ACTION="mensajes2.php?accion=21&folctt=<?php echo $folctt; ?>">
<table BORDER="0" CELLSPACING="1" CELLPADDING="5" width="100%" ALIGN="center">
<tr>
	<td width="50%" VALIGN="TOP" ROWSPAN="2" HEIGHT="37"><IMG SRC="logo.gif" width="235" HEIGHT="130" alt=""></td>
	<td class="dato" style="text-align: right; FONT-SIZE: 2.5em; FONT-FAMILY: Arial, Verdana;" width="40%" VALIGN="bottom" COLSPAN="2"><b>CONTACTO <?php echo $folctt; ?></b></td>
</tr>
<tr>
	<td class="dato" style="text-align: right" width="50%" VALIGN="TOP" COLSPAN="2" HEIGHT="65"><b>Fecha: </b><?php echo $fec_ctt ?></td>
</tr>
<tr>
	<td width="50%" valign="top">
		<table border="0" CELLSPACING="1" CELLPADDING="1" width="100%">
		<tr>
			<td width="80"><b>RUT Cliente</b></td>
			<td width="70px">
			<?php echo formatearRut($num_doc);?>	
			</td>
			<td>
			&nbsp;
			</td>
		</tr>
		<tr>
			<td>Nombre</td>
			<td colspan="2">
			<?php echo $nom_clt; ?>	
			</td>
		</tr>
		</table>
	</td>
	<td width="50%" valign="top">
		<table border="0" CELLSPACING="1" CELLPADDING="1" width="100%">
		<tr>
			<td>RUT Contacto</td>
			<td>
				<?php echo formatearRut($num_doc); ?>	
			</td>
		</tr>
		<tr>
			<td>Nombre</td>
			<td>
			<?php echo $nom_clt; ?>	
			</td>
		</tr>
		<tr>
			<td>Tel&eacute;fono</td>
			<td>
			<?php echo $fon_ctt; ?>
			</td>
		</tr>
		<tr>
			<td>Celular</td>
			<td>
			<?php echo "&nbsp;"; ?>
			</td>
		</tr>
		<tr>
			<td>e-mail</td>
			<td>
			<?php echo $email; ?>
			</td>
		</tr>
		</table>
	</td>
</tr>
<tr>
	<td VALIGN="TOP" COLSPAN="2" class="dato5p12s"><B>Consultas</B></td>
</tr>
<tr>
<tr>
	<td VALIGN="TOP" class="dato10p" colspan="2" style="PADDING-BOTTOM:10px">
	<table width="100%" BORDER="0" CELLSPACING="0" CELLPADDING="1" ALIGN="left">
	<tr>
		<td class="titulo_tabla" valign="top" style="text-align: left" width="10%" height="15">Fecha</td>
		<td class="titulo_tabla" valign="top" style="text-align: left" width="5%" height="15">Folio</td>
		<td class="titulo_tabla" valign="top" style="text-align: left" width="10%" height="15">Origen</td>
		<td class="titulo_tabla" valign="top" style="text-align: left" width="65%" height="15">Consulta / Respuesta</td>
		<td class="titulo_tabla" valign="top" style="text-align: left" width="10%" height="15">&nbsp;</td>
	</tr>
	<?php 
                //echo "vm_hiscna_cttcot $folctt, $cod_per";
		$result = mssql_query("vm_hiscna_cttcot $folctt, $cod_per", $db);
		$bOkRespuesta = false;
		$totfilas = 0;
		$folcna = 0;
                $bXisSinLec = false;
		while (($row = mssql_fetch_array($result))) {
                    $totfilas++;
                    //if ($folcna > 0 and $folcna != $row['Fol_Cna'])
                    //    echo "<tr><td colspan=\"5\">&nbsp;</td>\n";
                    $folcna = $row['Fol_Cna'];
                    $boldbegin = "";
                    $boldend = "";
                    $flg_lec = $row['flg_lec'];
                    if ($flg_lec == 0 and $row['Num_Doc'] != "Vestmed") {
                        $boldbegin = "<b>";
                        $boldend = "</b>";
                        $bXisSinLec = TRUE;
                    }
	?>
                    <tr>
                            <td align="left" valign="top"><?php echo $boldbegin.$row['Fec_Dis'].$boldend; ?></td>
                            <td align="left" valign="top"><?php echo $boldbegin.$row['Fol_Cna'].$boldend; ?></td>
                            <td align="left" valign="top"><?php echo $boldbegin.($row['Num_Doc'] == "Vestmed" ? $row['Num_Doc'] : "Cliente" ).$boldend; ?></td>
                            <td align="left" valign="top" colspan="2"><?php echo $boldbegin.utf8_encode($row['Det_Cna']).$boldend; ?></td>
                    </tr>
	<?php if (trim($row['Det_Res']) != "") { ?>
                    <tr>
                            <td align="left" valign="top"><?php echo $row['Fec_ResCnaDis']; ?></td>
                            <td align="left" valign="top"><?php echo $row['Fol_Cna']; ?></td>
                            <td align="left" valign="top"><?php echo ($row['Num_DocRes'] == "Vestmed" ? $row['Num_DocRes'] : "Cliente"  ); ?></td>
                            <td align="left" valign="top" colspan="2"><?php echo $boldbegin.utf8_encode($row['Det_Res']).$boldend; ?></td>
                    </tr>
	<?php } else { 
                    $autor = $row['Num_Doc'];
                    if ($autor != "Vestmed") {
	?>
                    <!--tr>
                        <td align="left" valign="top"><?php echo date("d/m/Y") ?></td>
                        <td align="left" valign="top"><?php echo $row['Fol_Cna']; ?></td>
                        <td align="left" valign="top">Vestmed</td>
                        <td align="left" valign="top"><textarea class="dato" rows="2" cols="80" name="respuesta<?php echo $row['Fol_Cna'] ?>"></textarea></td>
                        <td align="right" valign="top"><input type="button" name="Responder<?php echo $row['Fol_Cna'] ?>" value="Responder" onclick="javascript:Enviar_Res(<?php echo $row['Fol_Cna'] ?>)" class="btn"></td>
                    </tr-->
	<?php
                    }
                  }
                }
                if ($bXisSinLec)
                    $result = mssql_query("vm_mcactt_leidos $folctt, $cod_per", $db);

	?>
        <tr>
            <td colspan="2" VALIGN="TOP" class="dato" style="padding-top: 30px"><b>Nuevo Mensaje</b></td>
            <td colspan="3" VALIGN="TOP" class="dato" style="padding-top: 30px">
            <textarea class="textfieldv2" rows="5" cols="100" name="consulta"></textarea>
            </td>
        </tr>
	<tr><td colspan="5" style="text-align: right; padding-top: 5px">
	<input type="button" name="Volver" value="Volver" class="btn" onclick="Salir(11)">&nbsp;&nbsp;
	<input type="submit" name="Enviar" value=" Enviar " class="btn">
	<input type="hidden" name="tipo_bus" value="<?php echo $Tip_Bus; ?>">
	<input type="hidden" name="cod_per" value="<?php echo $Cod_PerBus; ?>">	
	</td></tr>
	</table>
</tr>
</table>
</form>
<?php } ?>
</td>
</tr>
</table>
<?php formar_bottombox (); ?>
    </div>
</div>
<script type="text/javascript" >
	var f1;
	var f2;
	f1 = document.F1;
	f2 = document.F2;
        <?php if ($ActualizaPadre) { ?>
        ActualizaPadre();
        <?php } ?>
</script>
</body>
</html>
