<?php
//Obtengo los datos de conexion de la base de datos
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
    if ($row["Id_Mod"] == 1 && $row["ID_Opc"] == 9 && strtoupper($row['CodUsr']) == strtoupper($UsrId)) {
        $OkOpc = true;
        break;
    }
mssql_free_result($sp);

if (!$OkOpc) {
    header ("Location:../index.php");
    exit(0);
}

$fecha = ok($_POST['dfFecha']);
$folio = ok($_POST['dfFolio']);

$asunto = "vm_hdr_msg '$fecha', $folio";
$sp = mssql_query("vm_hdr_msg '$fecha', $folio",$db);
if (($row = mssql_fetch_array($sp))) $asunto = $row['AsuMsg'];

$UltOrgMsg = 0;

if (isset($_POST['input'])) {
    $input = utf8_decode($_POST['input']);
    $desmsg = ok($_POST['dfDesMsg']);
    $sp = mssql_query("vm_ins_detmsg '$fecha', $folio, '$input', 0, '$desmsg'",$db);
}

$persiana = ok($_POST['dfPersiana']);

?>

<!DOCTYPE html PUBLIC "-//W3C//Dtd XHTML 1.0 transitional//EN" "http://www.w3.org/tr/xhtml1/Dtd/xhtml1-transitional.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Catalogo - Vestmed Vestuario M&eacute;dico</title>
<link href="../css/layout.css" type="text/css" rel="stylesheet" />
<script type="text/javascript" src="../js/mootools-1.2.1-core-yc.js"></script>
<script type="text/javascript" src="../js/mootools-1.2-more.js"></script>
<script type="text/javascript" src="../dropdown/js/dropdown.js"> </script>
<link rel="stylesheet" type="text/css" media="screen" href="../dropdown/css/uvumi-dropdown.css" />
<link href="../meson/css/itunes.css" type="text/css" rel="stylesheet" />
<!--[if IE]>
<link rel="stylesheet" type="text/css" media="screen" href="dropdown/css/uvumi-dropdown-ie.css" />
<![endif]-->
<script language="JavaScript" src="../Include/ValidarDataInput.js"></script>
<script language="JavaScript" src="../Include/SoloNumeros.js"></script>
<script language="JavaScript" src="../Include/validarRut.js"></script>
<LINK href="../Include/estilos.css" type="text/css" rel="stylesheet" />
<script type="text/javascript" src="js/AccionesMenu.js"></script>

<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.8/jquery.js"></script>
<script src="http://code.jquery.com/ui/1.9.0/jquery-ui.min.js"></script>

<link type="text/css" rel="stylesheet" href="css/mensajes.css" />
<script type="text/javascript" src="js/faq.js"></script>

<link rel="stylesheet" type="text/css" href="CLEditor1_3_0/jquery.cleditor.css" />
<script type="text/javascript" src="CLEditor1_3_0/jquery.cleditor.min.js"></script>

<script type="text/javascript">
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
                            CambiaColor(xml);
                    });
                    return false;
            });
        }

    );
    
    function CheckMensaje(form) {
        return confirm('Confirma que desea enviar el mensaje al Cliente ?');
    }
    
    function Volver() {
        f2.action = "mismensajes.php";
        f2.submit();
    }
    
    function ProcesoLocal(id_mensaje) {
        var arrMensaje = id_mensaje.split("-");
        if (parseInt(arrMensaje[1]) == 0) {
            $("#dfSecMsg").val(arrMensaje[0]);
            $("form#ActualizaFlag").submit();
        }
    }
    
    function CambiaColor (xml) {
        
    }    
    
    function MostratEditor() {
        $("#areamostrar").hide();
        $("#areaeditor").show();
        $("#enviar").show();
        $("#input").cleditor()[0].refresh();
        $("#input").cleditor()[0].focus();
    }
</script>
</head>
<div id="body">
	<div id="header"></div>
    <ul id="usuario_registro">
		<?php 	echo display_usr($UsrId, $Perfil, "Mensajes", $db); ?>
    </ul>
    <div id="work">
<?php formar_topbox ("100%%","center"); ?>
<P align="left"><strong>Escritorio</strong></P>
<P align="center">
<table WIDTH="100%" BORDER="0" CELLSPACING="0" CELLPADDING="1" ALIGN="center">
<tr>
<td width="170px" align="left" valign="top">
	<?php echo display_mnuizq($UsrId, $db); ?>
</td>
<td width="1%">&nbsp;</td>
<td  valign="top" class="label_left_right_top_bottom" STYLE="PADDING-LEFT:20px; PADDING-RIGHT:20px; TEXT-ALIGN:left">
	<table WIDTH="100%" BORDER="0" CELLSPACING="0" CELLPADDING="1" ALIGN="center">
	<tr><td style="padding-bottom: 10px">
    
                <h2><?php echo utf8_encode($asunto); ?></h2>
		<ul id="faq">
                    <?php
                    $sp = mssql_query("vm_det_msg '$fecha', $folio", $db);
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
                <form id="F2" name="F2" method="POST" action="detallecaso.php" onsubmit="return CheckMensaje(this)">
                <input type="hidden" name="dfFecha" id="dfFecha" value="<?php echo $fecha ?>" />
                <input type="hidden" name="dfFolio" id="dfFolio" value="<?php echo $folio ?>" />
                <input type="hidden" name="dfPersiana" id="dfPersiana" value="<?php echo $persiana ?>" />
                <input type="hidden" name="dfDesMsg" id="dfDesMsg" value="<?php echo $UltOrgMsg ?>" />
                <div id="areamostrar">Pinche aqu&iacute; para <a href="javascript:MostratEditor()">Responder</a></div>
                <div id="areaeditor"><textarea id="input" name="input"></textarea></div>
                <div style="text-align: right; padding-top: 10px">
                    <input class="btn" type="button" value="Volver" name="volver" id="volver" onclick="Volver()" />
                    <input class="btn" type="submit" value="Enviar" name="enviar" id="enviar" />
                </div>
                </form>
                
                <form id="ActualizaFlag" method="POST">
                <input type="hidden" name="dfSecMsg" id="dfSecMsg" value="" />
                </form>
                
	</td></tr>
	</table>
</td>
</tr>
</table>
<?php formar_bottombox (); ?>
    </div>
    <div id="footer"></div>
</div>

<script type="text/javascript">
	var f1;
	var f2;
	f1 = document.F1;
	f2 = document.F2;
        $("#areaeditor").hide();
        $("#enviar").hide();
</script>

</body>
</html>
