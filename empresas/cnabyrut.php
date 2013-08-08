<?php
ini_set('display_errors', '0');
session_start();
include("../config.php");

if (!isset($_SESSION['usuario'])) header("Location: index.php");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
        <link href="../css/layout.css" type="text/css" rel="stylesheet" />
        <title>Consulta Histórico Compras</title>
        <script language="JavaScript" src="../Include/ValidarDataInput.js" type="text/javascript" ></script>
        <script language="JavaScript" src="../Include/SoloNumeros.js" type="text/javascript" ></script>
        <script language="JavaScript" src="../Include/validarRut.js" type="text/javascript" ></script>
        <!--script type="text/javascript" src="../js/jquery-1.3.2.min.js"></script-->
	<!--link href="http://code.jquery.com/ui/1.9.0/themes/ui-darkness/jquery-ui.css" rel="stylesheet"-->
	<link href="http://code.jquery.com/ui/1.9.0/themes/redmond/jquery-ui.css" rel="stylesheet" />
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.8/jquery.js"></script>
	<script src="http://code.jquery.com/ui/1.9.0/jquery-ui.min.js"></script>
        
	<!-- keyboard widget css & script (required) -->
	<link href="css/keyboard.css" rel="stylesheet" />
	<script src="js/jquery.keyboard.js"></script>

	<!-- keyboard extensions (optional) -->
	<script src="js/jquery.mousewheel.js"></script>
        
        <script type="text/javascript">
            var $j = jQuery.noConflict();
            
            $j(function(){
                    $j('#rut').keyboard({
                         layout : 'custom',
                         usePreview: false,
                         customLayout: {
                           'default' : [
                            '0 1 2 3 4 5',
                            '6 7 8 9 K -',
                            '{bksp} {accept} {cancel}'
                           ]
                         },
                         restrictInput : true,
                         preventPaste : true, 
                         autoAccept : true
                    });
            });
            
            $j(document).ready(function(){
                $j("form#consulta").submit(function(){
                    $j.post("../ajax-search-per.php",{
                            search_type: "per",
                            param_filter: $j("#rut").val()
                        }, function(xml) {
                        MostrarListado(xml);
                    });
                    return false;
                });
            });
            
            function MostrarListado(xml) {
                var cod_clt = 0;
                $j("filter",xml).each(
                    function(id) {
                        filter=$j("filter",xml).get(id);
                        if ($j("code",filter).text() == "rut") $j("#dfrutclt").val($j("value",filter).text());
                        if ($j("code",filter).text() == "codclt") cod_clt = parseInt($j("value",filter).text());
                    }
		);
                if (cod_clt == 0) {
                    alert ('Rut ingresado no existe como cliente');
                    return;
                }
                $j("form#listado").submit();                
            }
            
            function CheckData() {
                var form = document.getElementById('consulta');
                if (form.rut.value == "") {
                    alert ('Favor ingrese el rut de la persona que desea consultar su historico de compras');
                    form.rut.focus();
                    return false;
                }
                //rutBlur('rut','dfrut');
                if (!validarRutCompleto('rut')) {
                    alert ('Rut ingresado incorrecto');
                    form.rut.focus();
                    return false;
                }
                $j("form#consulta").submit();                
            }
            
            function Home() {
                var form = document.getElementById('consulta');
                form.action = "principal.php";
                form.submit();
            }
        </script>
    </head>
    <body>
        <form id="consulta" method="POST">
            <table border="0" width="300" align="center" cellpadding="3px" cellspacing="3">
                <tbody>
                    <tr>
                        <td>Rut</td>
                        <td>
                            <!--input type="hidden" name="dfrut" id="dfrut" /-->
                            <!--input name="rut" id="rut" onblur="rutBlur('rut','dfrut')" onKeyPress="javascript:return soloRUT(event)" tabIndex="1" /-->
                            <input name="rut" id="rut" tabIndex="1" style="TEXT-trANSFORM: uppercase" />
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" align="left">
                            <input type="button" value="Consultar" name="enviar" onclick="CheckData()" style="width: 100px" />
                            <input type="button" value="Volver" name="volver" onclick="Home()" style="width: 100px" />
                        </td>
                    </tr>
                </tbody>
            </table>
        </form>
        <form id="listado" method="POST" action="nventas.php">
                <input type="hidden" name="dfrutclt" id="dfrutclt" />
        </form>
    </body>
</html>
