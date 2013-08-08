<?php
ini_set('display_errors', '0');
session_start();

if (!isset($_SESSION['usuario'])) {
    if (!isset($_POST["usuario"])) header("Location: ../index.php");
    $_SESSION['usuario'] = $_POST["usuario"];     
}
$UsrId = (isset($_SESSION['usuario'])) ? $_SESSION['usuario'] : "";

include("global_cot.php");

$OkOpc = false;
$sp = mssql_query("vm_seg_usr_opcmodweb '$UsrId'",$db);
while (($row = mssql_fetch_array($sp))) 
    if ($row["Id_Mod"] == 1 && $row["ID_Opc"] == 1 && strtoupper($row['CodUsr']) == strtoupper($UsrId)) {
        $OkOpc = true;
        break;
    }
mssql_free_result($sp);

if (!$OkOpc) {
    header ("Location:../index.php");
    exit(0);
}

$Cod_Cot = (isset($_GET['cot'])) ? ok($_GET['cot']) : 0;
$accion = (isset($_GET['accion'])) ? $_GET['accion'] : "";

$result = mssql_query("vm_getfol_cot $Cod_Cot",$db);
if (($row = mssql_fetch_array($result))) {
    $Fec_Cre = date("Ymd", strtotime($row['FecCre']));
    $FolMsg  = $row['FolMsg'];
    
    $asunto = "vm_hdr_msg '$Fec_Cre', $FolMsg";
    $sp = mssql_query("vm_hdr_msg '$Fec_Cre', $FolMsg",$db);
    if (($row = mssql_fetch_array($sp))) $asunto = utf8_encode ($row['AsuMsg']);
    
    $UltOrgMsg = 0;

    if (isset($_POST['input'])) {
        $input = utf8_decode($_POST['input']);
        $desmsg = ok($_POST['dfDesMsg']);
        $sp = mssql_query("vm_ins_detmsg '$Fec_Cre', $FolMsg, '$input', 0, '$desmsg'",$db);
	$ActualizarPadre = true;
    }

}

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

	$num_doc   = $row['Num_Doc'];
	$cod_suc   = $row['Cod_Suc'];
	$dir_suc   = $row['Dir_Suc'];
	$cod_cmn   = $row['Cod_Cmn'];
	$cod_cdd   = $row['Cod_Cdd'];
	$cod_per   = $row['Cod_Per'];
	$fon_ctt   = $row['Fon_Ctt'];
	$mail_ctt  = $row['Mail_Ctt'];
	$cod_pre   = $row['Cod_Pre'];
	$obs_cot   = ($row['Obs_Cot'] == "_NONE" ? "" : $row['Obs_Cot']);

	$result = mssql_query("vm_cmn_s $cod_cmn", $db);
	if (($row = mssql_fetch_array($result))) $nom_cmn = $row['Nom_Cmn'];
	
	$result = mssql_query("vm_cdd_s $cod_cdd", $db);
	if (($row = mssql_fetch_array($result))) $nom_cdd = $row['Nom_Cdd'];
	
	$result = mssql_query("vm_suc_s $cod_clt, $cod_suc", $db);
	if (($row = mssql_fetch_array($result))) $nom_suc = $row['Nom_Suc'];

	$result = mssql_query("vm_ctt_s $cod_clt, $cod_suc", $db);
	while(($row = mssql_fetch_array($result)))
		if ($row['Cod_Per'] == $cod_per) $nom_ctt = $row['Pat_Per']." ".$row['Mat_Per'].", ".$row['Nom_Per'];

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
/*
$ActualizarPadre = false;
if ($accion == "consulta") {
	$ActualizarPadre = true;
	$cod_per = 0;
	$consulta = str_replace("\'", "''", $_POST['consulta']);
	$result = mssql_query("vm_i_cna $Cod_Cot, $cod_clt, $cod_per, '$consulta'",$db);

        include("avisonewmensaje.php");

        $asunto       = "Vestmed Ltda.| Tienes un Nuevo Mensaje Pendiente";

        enviar_mail ($mail_ctt, $asunto, $cuerpo_mail, "HTML");
}

if ($accion == "respuesta") {
	$ActualizarPadre = true;
	$cod_per = 0;
	//$result= mssql_query("vm_s_per_tipdoc 1, '$UsrId'", $db);
	//if (($row = mssql_fetch_array($result))) $cod_per = $row['Cod_Per'];
	$Fol_Cna = $_GET['folio'];
	$respuesta = (isset($_POST['respuesta'.$Fol_Cna])) ? ($_POST['respuesta'.$Fol_Cna]) : "";
	$respuesta = str_replace("\'", "''", $respuesta);
	//echo "vm_i_rescna $Cod_Cot, $cod_clt, $cod_per, $Fol_Cna, '$respuesta'";
	$result = mssql_query("vm_i_rescna $Cod_Cot, $cod_clt, $cod_per, $Fol_Cna, '$respuesta'",$db);
}
*/
?>
<!DOCTYPE html PUBLIC "-//W3C//Dtd XHTML 1.0 transitional//EN" "http://www.w3.org/tr/xhtml1/Dtd/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Catalogo - Vestmed Vestuario M&eacute;dico</title>
<LINK href="../Include/estilos.css" type="text/css" rel="stylesheet" />
<link href="../css/layout.css" type="text/css" rel="stylesheet" />
<link href="../css/headers.css" type="text/css" rel="stylesheet" />
<link rel="stylesheet" type="text/css" media="screen" href="../dropdown/css/uvumi-dropdown.css" />
<link href="../css/clearfix.css" type="text/css" rel="stylesheet" />
<!-- Lytebox Includes //-->
<script type="text/javascript" src="../lytebox/lytebox.js"></script>
<link rel="stylesheet" type="text/css" href="../lytebox/lytebox.css" media="screen" />
<!-- Lytebox Includes //-->

<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.8/jquery.js"></script>
<script src="http://code.jquery.com/ui/1.9.0/jquery-ui.min.js"></script>

<link type="text/css" rel="stylesheet" href="css/mensajes.css" />
<script type="text/javascript" src="js/faq.js"></script>

<link rel="stylesheet" type="text/css" href="CLEditor1_3_0/jquery.cleditor.css" />
<script type="text/javascript" src="CLEditor1_3_0/jquery.cleditor.min.js"></script>


<script type="text/javascript">
        //new UvumiDropdown('dropdown-scliente');
        $(document).ready(
            function() {
                $("#input").cleditor({width:"99%"})[0].focus();

                $("form#ActualizaFlag").submit(function(){
                        $.post("../ajax-search.php",{
                                search_type: "flglec",
                                param_filter: "",
                                param_fec: $("#dfFecha").val(),
                                param_fol: $("#dfFolio").val(),
                                param_sec: $("#dfSecMsg").val()
                        }, function(xml) {
                                ActualizaPadre();
                        });
                        return false;
                });
            }

        );
    

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
		f2.action = "consultas.php?cot=<?php echo $Cod_Cot; ?>&folio="+folio+"&accion=respuesta<?php if ($caso != "") echo "&caso=".$caso."&paso=".$paso; ?>";
		f2.submit();
	}
        
        function ProcesoLocal(id_mensaje) {
            var arrMensaje = id_mensaje.split("-");
            if (parseInt(arrMensaje[1]) == 0) {
                $("#dfSecMsg").val(arrMensaje[0]);
                $("form#ActualizaFlag").submit();
            }
        }

        function ActualizaPadre()
        {
            parent.opener.ActualizarConsultas();
        }

        function MostratEditor() {
            $("#areamostrar").hide();
            $("#areaeditor").show();
            $("#enviar").show();
            $("#input").cleditor()[0].refresh();
            $("#input").cleditor()[0].focus();
        }

        function CheckMensaje(form) {
            return confirm('Confirma que desea enviar el mensaje al Cliente ?');
        }
</script>
<?php
	//$page = "nueva_cot.php?cot=".$Cod_Cot;
	if (!$TieneResp);
	else if ($ActualizarPadre and $Est_Res == 1) {
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
		<?php 	echo display_mnu($UsrId, $cod_tipper, $Cod_Cot, $db); ?>
    </ul>
	<div id="work">

<?php formar_topbox ("100%%","center"); ?>
<p align="center">
<table BORDER="0" CELLSPACING="1" CELLPADDING="0" width="100%">
<tr>
	<td width="60%" VALIGN="TOP" ROWSPAN="2" HEIGHT="37"><img SRC="logo.gif" width="235" HEIGHT="130" alt=""></td>
	<td class="dato" style="text-align: right; FONT-SIZE: 2.5em; FONT-FAMILY: Arial, Verdana;" width="40%" VALIGN="bottom" COLSPAN="2"><b>COTIZACI&Oacute;N <?php echo $Num_Cot; ?></b></td>
</tr>
<tr>
	<td class="dato" style="text-align: right" width="40%" VALIGN="TOP" COLSPAN="2" HEIGHT="65"><b>Fecha: </b><?php echo $fec_cot ?></td>
</tr>
<tr>
    <td width="60%" VALIGN="TOP" class="dato5p12s" style="padding-top: 20px;"><B>Cliente: <?php echo utf8_encode($nom_clt) ?></B></td>
	<td width="40%" VALIGN="TOP" COLSPAN="2" style="padding-top: 20px">&nbsp;</td>
</tr>
<tr>
	<td width="60%" VALIGN="TOP" class="dato5p">Rut: <?php echo formatearRut($num_doc) ?></td>
	<td width="40%" VALIGN="TOP" COLSPAN="2">&nbsp;</td>
</tr>
<tr>
	<td width="60%" VALIGN="TOP" class="dato5p">Sucursal: <?php echo utf8_encode($nom_suc) ?></td>
	<td width="40%" VALIGN="TOP" COLSPAN="2" class="dato">Contacto: <?php echo utf8_encode($nom_ctt) ?></td>
</tr>
<tr>
	<td width="60%" VALIGN="TOP" class="dato5p">Direcci&oacute;n: <?php echo utf8_encode($dir_suc) ?></td>
	<td width="40%" VALIGN="TOP" COLSPAN="2" class="dato">Tel&eacute;fono: <?php echo $fon_ctt ?></td>
</tr>
<tr>
	<td width="60%" VALIGN="TOP" class="dato5p">Ciudad: <?php echo utf8_encode($nom_cdd) ?></td>
	<td width="40%" VALIGN="TOP" COLSPAN="2" class="dato">Fax: <?php echo $fon_ctt ?></td>
</tr>
<tr>
	<td width="60%" VALIGN="TOP" class="dato5p">Comuna: <?php echo utf8_encode($nom_cmn) ?></td>
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
        <ul id="faq">
            <?php
            $sp = mssql_query("vm_det_msg '$Fec_Cre', $FolMsg", $db);
            while (($row = mssql_fetch_array($sp))) {
                if ($row['OrgMsg'] == 0 || $row['FlgLecDet'] == 1) {
                    $estilo = "background:#369 url('img/plus.gif') center left no-repeat;";
                    $leido = 1;
                }
                else {
                    $estilo = "background:red url('img/plus.gif') center left no-repeat;";
                    $leido = 0;
                }
            ?>
                <li>
                    <h3 style="<?php echo $estilo; ?>" id="<?php echo $row['SecMsg']."-".$leido; ?>">
                        <table width="100%">
                            <tr>
                                <td style="text-align: left"><?php echo utf8_encode($row['NomOrgMsg']) ?></td>
                                <td style="text-align: right"><?php echo date("d/m/Y H:i", strtotime($row['FecMsg'])); ?></td>
                            </tr>
                        </table>
                    </h3>
                    <p><?php echo utf8_encode($row['DetMsg']) ?></p>
                </li>
            <?php
                $UltOrgMsg = ($row['OrgMsg'] != 0) ? $row['OrgMsg'] : $UltOrgMsg;
            }
            ?>
        </ul>
        <form id="F2" name="F2" method="POST" action="consultas.php?cot=<?php echo $Cod_Cot; ?>" onsubmit="return CheckMensaje(this)">
        <input type="hidden" name="dfFecha" id="dfFecha" value="<?php echo $Fec_Cre ?>" />
        <input type="hidden" name="dfFolio" id="dfFolio" value="<?php echo $FolMsg ?>" />
        <input type="hidden" name="dfDesMsg" id="dfDesMsg" value="<?php echo $UltOrgMsg ?>" />
        <div id="areamostrar">Pinche aqu&iacute; para <a href="javascript:MostratEditor()">Responder</a></div>
        <div id="areaeditor"><textarea id="input" name="input"></textarea></div>
        <div style="text-align: right; padding-top: 10px">
            <input class="btn" type="submit" value="Enviar" name="enviar" id="enviar" />
        </div>
        </form>
    
    
        <form id="ActualizaFlag" method="POST">
        <input type="hidden" name="dfSecMsg" id="dfSecMsg" value="" />
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
        <?php if ($bXisSinLec) { ?>
        parent.opener.ActualizarConsultas();
        <?php } ?>
        $("#areaeditor").hide();
        $("#enviar").hide();
</script>
</body>
</HTML>
