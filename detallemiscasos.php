<?php
//Obtengo los datos de conexion de la base de datos
ini_set('display_errors', '0');
session_start();
include("config.php");

$Cod_Per = 0;
$Cod_Clt = 0;
$RutClt = "";
$xis = 0;
$EsCliente = false;
$bExisteUsr = false;
if (isset($_SESSION['CodPer'])) $Cod_Per = intval($_SESSION['CodPer']);
if (isset($_SESSION['CodClt'])) $Cod_Clt = intval($_SESSION['CodClt']);

$fecha = ok($_POST['dfFecha']);
$folio = ok($_POST['dfFolio']);
$persiana = ok($_POST['dfPersiana']);

$asunto = "vm_hdr_msg '$fecha', $folio";
$sp = mssql_query("vm_hdr_msg '$fecha', $folio",$db);
if (($row = mssql_fetch_array($sp))) $asunto = $row['AsuMsg'];

$UltOrgMsg = 0;

if (isset($_POST['input'])) {
    $input = utf8_decode($_POST['input']);
    $desmsg = ok($_POST['dfDesMsg']);
    $sp = mssql_query("vm_ins_detmsg '$fecha', $folio, '$input', $Cod_Per, '$desmsg'",$db);
}

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Registro - Vestmed Vestuario M&eacute;dico</title>
<link href="css/layout.css" type="text/css" rel="stylesheet" />
<link href="css/clearfix.css" type="text/css" rel="stylesheet" />
<script type="text/javascript" src="js/mootools-1.2.1-core-yc.js"></script>
<script type="text/javascript" src="js/mootools-1.2-more.js"></script>
<script type="text/javascript" src="dropdown/js/dropdown.js"> </script>
<link rel="stylesheet" type="text/css" media="screen" href="dropdown/css/uvumi-dropdown.css" />

<link href="Include/estilos.css" type="text/css" rel="stylesheet" />
<script type="text/javascript" language="JavaScript" src="Include/SoloNumeros.js"></script>
<script type="text/javascript" language="JavaScript" src="Include/ValidarDataInput.js"></script>
<script type="text/javascript" language="JavaScript" src="Include/validarRut.js"></script>
<script type="text/javascript" language="JavaScript" src="Include/fngenerales.js"></script>

<link href="meson/css/itunes.css" type="text/css" rel="stylesheet" />

<link href="http://code.jquery.com/ui/1.9.0/themes/redmond/jquery-ui.css" rel="stylesheet" />
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.8/jquery.js"></script>
<script src="http://code.jquery.com/ui/1.9.0/jquery-ui.min.js"></script>

<link type="text/css" rel="stylesheet" href="css/mensajes.css" />
<script type="text/javascript" src="js/faq.js"></script>

<link rel="stylesheet" type="text/css" href="cotizador/CLEditor1_3_0/jquery.cleditor.css" />
<script type="text/javascript" src="cotizador/CLEditor1_3_0/jquery.cleditor.min.js"></script>

<script>
    $(document).ready(
        function() {
            $("#input").cleditor({width:"99%"})[0].focus();
            
            $("form#ActualizaFlag").submit(function(){
                    $.post("ajax-search.php",{
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
        return confirm('Confirma que desea enviar el mensaje ?');
    }
    
    function Volver() {
        f2.action = "vermismensajes.php";
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

<!--[if IE]>
<link rel="stylesheet" type="text/css" media="screen" href="dropdown/css/uvumi-dropdown-ie.css" />
<![endif]-->
<script type="text/javascript">
new UvumiDropdown('dropdown-scliente');

function popwindow(ventana){
   window.open(ventana,"Anexos",'toolbar=0,location=0,scrollbars=yes,titlebar=no,menubar=no,resizable=0,left=100,top=100,width=640,height=385')
}

</script>
<!-- Lytebox Includes //-->
<script type="text/javascript" src="lytebox/lytebox.js"></script>
<link rel="stylesheet" type="text/css" href="lytebox/lytebox.css" media="screen" />
<!-- Lytebox Includes //-->
</head>
<body>
    <div id="body">
        <div id="header"></div>
        <div class="menu"id="menu-noselect">
            <a id="home" href="index.htm">home</a>
            <a id="empresa" href="empresa.htm">empresa</a>
            <a id="marcas" href="marcas.htm">marcas</a>
            <a id="telas" href="telas.htm">telas</a>
            <a id="bordados" href="bordados.htm">bordados</a>
            <a id="despachos" href="despachos.htm">despachos</a>
            <a id="clientes" href="clientes.htm">clientes</a>
            <div id="servicio-cliente-selected" style="z-index:1000;padding-top:0px;" class="selected">
            <ul id="dropdown-scliente" class="dropdown">
                    <li>
                        <a class="normal" href="servicio-cliente.htm">servicio al cliente</a>
                        <ul>
                           <li>
                                <a href="faq.htm">Faq</a>
                            </li>
                            <li>
                                <a href="como-tomar-medidas.htm">C&oacute;mo Tomar Medidas</a>
                            </li>
                            <li>
                                <a href="despachos.htm">Despachos</a>
                            </li>
                            <li>
                                <a href="clean-care.htm">Clean & Care</a>
                            </li>
                            <li>
                                <a href="tracking-ordenes.htm">Tracking de &Oacute;rdenes</a>
                            </li>
                            <li>
                                <a href="como-cotizar.htm">C&oacute;mo Cotizar</a>
                            </li>

                            <li>
                                <a href="politicas-privacidad.htm">Pol&iacute;ticas de Privacidad</a>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
            <a id="catalogo" href="catalogo.php">catalogo</a>
            <a id="contacto" href="contacto.htm">contacto</a>
        </div>
	<?php
            if ($Cod_Per == 0) {
	?>
        <ul id="usuario_registro">
            <form ID="F1" method="POST" name="F1" action="">
                <li class="back-verde registro"><a href="registrarse.php">REGISTRARSE</a></li>
                <li class="olvido"><a href="javascript:EnviarClave()">OLVID&Oacute; SU CLAVE?</a></li>
                <li class="back-verde"><a href="javascript:ValidarLogin()">ENTRAR</a></li>
                <li class="back-verde inputp"><input type="password" name="dfclave"/></li>
                <li class="back-verde">CONTRASE&Ntilde;A</li>
                <li class="back-verde inputp"><input type="" name="rut" id="rut" onblur="formatearRut('rut','dfrut')" /></li>
                <li class="back-verde">RUT</li>
                <input type="hidden" name="dfrut" id="dfrut" />
            </form>
        </ul>
        <?php }
            else {
        ?>
        <ul id="usuario_registro">
            <?php echo display_login($Cod_Per, $Cod_Clt, $db, 0); ?>
        </ul>
	<?php
		}
	?>
        <div id="work">
            <div id="back-registro3">
                <div style="width:765px; height: auto; margin:0 auto 0 100px; padding-top:10px; padding-bottom: 10px">
                    
                <h2><?php echo utf8_encode($asunto); ?></h2>
		<ul id="faq">
                    <?php
                    $sp = mssql_query("vm_det_msg '$fecha', $folio", $db);
                    while (($row = mssql_fetch_array($sp))) {
                        if ($row['OrgMsg'] == $Cod_Per || $row['FlgLecDet'] == 1) {
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
                        $UltOrgMsg = ($row['OrgMsg'] == 0) ? $row['OrgMsg'] : $UltOrgMsg;
                    }
                    ?>
		</ul>
                <form id="F2" name="F2" method="POST" action="detallemiscasos.php" onsubmit="return CheckMensaje(this)">
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
                
                </div>
            </div>
        </div>
	<div id="footer"></div>
    </div>
<script type="text/javascript" language="javascript">
	var f1;
	var f2;

	f1 = document.F1;
	f2 = document.F2;

        $("#areaeditor").hide();
        $("#enviar").hide();
</script>
</body>
</html>
