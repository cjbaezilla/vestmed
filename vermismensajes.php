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

$Cod_Cot = (isset($_GET['cot']) ? intval(ok($_GET['cot'])) : 0);

$persiana = (isset($_POST['dfPersiana']) ? intval(ok($_POST['dfPersiana'])) : 0);
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

<link href="http://code.jquery.com/ui/1.9.0/themes/redmond/jquery-ui.css" rel="stylesheet" />
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.8/jquery.js"></script>
<script src="http://code.jquery.com/ui/1.9.0/jquery-ui.min.js"></script>

<style type="text/css" title="currentStyle">
    @import "DataTables-1.9.4/media/css/demo_page.css";
    @import "DataTables-1.9.4/media/css/jquery.dataTables_themeroller.css";
    @import "DataTables-1.9.4/examples/examples_support/themes/smoothness/jquery-ui-1.8.4.custom.css";
</style>
<script type="text/javascript" language="javascript" src="DataTables-1.9.4/media/js/jquery.dataTables.js"></script>

<script type="text/javascript">
    //*************************************
    var $j = jQuery.noConflict();
 
    function Nuevo_Msg(caso,id) {
            f2.action = "mismensajes.php?accion="+caso+"&id="+id;
            f2.submit();
    }

    function Salir(caso) {
            f2.action = "mismensajes.php?accion="+caso;
            f2.submit();
    }

    function checkDataForm(form,cot) {
        if (cot == 0)
            if (form.numcot.value == "_NONE")
            {
                    alert ("Debe indicar una cotizaci\u00f3n ...");
                    return false;
            }

        if (form.consulta.value == "") {
                alert("Debe ingresar una consulta ...");
                return false;
        }

        if (form.consulta.value.length > 1000)
        {
                alert("El mensaje debe contener a los mas 1.000 caracteres.");
                return false;
        }
        return true;
    }

    function checkDataNewMsgForm(form) {
        if (form.tipcna.value == "_NONE")
            {
                alert("Debe seleccionar un Tipo de Consulta");
                return false;
            }

        if (form.tipcna.value == "0" && form.numcot.value == "")
        {
                alert ("Debe indicar una cotizaci\u00f3n ...");
                return false;
        }

        if (form.consulta.value == "") {
                alert("Debe ingresar una consulta ...");
                return false;
        }

        if (form.consulta.value.length > 1000)
        {
                alert("El mensaje debe contener a los mas 1.000 caracteres.");
                return false;
        }
        return true;
    }

    function GoOpcion(pagina)
    {
        document.toolBar.action = pagina;
        document.toolBar.submit();
    }

</script>
<script>
    $j(function() {
        $j( '#noleidos' ).dataTable({    
            "aaSorting": [[ 0, "asc" ]],
            "oLanguage": {      
                "oPaginate": {        
                    "sFirst": "Primera",
                    "sNext": "Siguiente",
                    "sPrevious": "Anterior",
                    "sLast": "Ultima"
                },
                "sInfo": "Registros: _TOTAL_. Viendo del _START_ al _END_",
                "sEmptyTable": "No existen registros a mostrar",
                "sSearch": "Filtro de Busqueda:",
                "sLengthMenu": "Mostrando _MENU_ registros por página",
                "sInfoFiltered": "(filtrados desde _MAX_ registros)"
            },
            "bJQueryUI": true,
            "sPaginationType": "full_numbers"
        });
        $j( '#otros' ).dataTable({    
            "aaSorting": [[ 0, "asc" ]],
            "oLanguage": {      
                "oPaginate": {        
                    "sFirst": "Primera",
                    "sNext": "Siguiente",
                    "sPrevious": "Anterior",
                    "sLast": "Ultima"
                },
                "sInfo": "Registros: _TOTAL_. Viendo del _START_ al _END_",
                "sEmptyTable": "No existen registros a mostrar",
                "sSearch": "Filtro de Busqueda:",
                "sLengthMenu": "Mostrando _MENU_ registros por página",
                "sInfoFiltered": "(filtrados desde _MAX_ registros)"
            },
            "bJQueryUI": true,
            "sPaginationType": "full_numbers"
        });
    });
    
</script>

<!--[if IE]>
<link rel="stylesheet" type="text/css" media="screen" href="dropdown/css/uvumi-dropdown-ie.css" />
<![endif]-->
<script type="text/javascript">
new UvumiDropdown('dropdown-scliente');

function popwindow(ventana){
   window.open(ventana,"Anexos",'toolbar=0,location=0,scrollbars=yes,titlebar=no,menubar=no,resizable=0,left=100,top=100,width=640,height=385')
}

function Vermensaje(folio, persiana) {
    var arrFolio = folio.split(' ');
    $j("#dfFecha").val(arrFolio[0]);
    $j("#dfFolio").val(arrFolio[1]);
    $j("#dfPersiana").val(persiana)
    f2.action = "detallemiscasos.php";
    f2.submit();
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
                    
<div id="accordion">
    <h3>No Le&iacute;dos</h3>
    <div>
        <p>
            <table align="center" width="100%" cellpadding="2" id="noleidos" class="display">
                <thead>
                    <tr class="tabular">
                        <th class="tabular" ALIGN="center">Sec</th>
                        <th class="tabular">Origen</th>
                        <th class="tabular">Asunto</th>
                        <th class="tabular">Fecha</th>
                        <th class="tabular" ALIGN="center">No<br>Leidos</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                $sp = mssql_query("vm_msgsinlec $Cod_Per",$db);
                while (($row = mssql_fetch_array($sp))) {
                ?>
                <tr class="tabular">
                    <td class="tabular" VALIGN="middle" ALIGN="center" onclick="Vermensaje('<?php echo date("Ymd", strtotime($row['FecCre']))." ".$row['FolMsg'];?>', 0)"><?php echo $row['row']; ?></td>
                    <td class="tabular" VALIGN="middle" ALIGN="left" onclick="Vermensaje('<?php echo date("Ymd", strtotime($row['FecCre']))." ".$row['FolMsg'];?>', 0)"><?php echo utf8_encode($row['OrgMsg']); ?></td>
                    <td class="tabular" VALIGN="middle" ALIGN="left" onclick="Vermensaje('<?php echo date("Ymd", strtotime($row['FecCre']))." ".$row['FolMsg'];?>', 0)"><?php echo utf8_encode($row['AsuMsg']); ?></td>
                    <td class="tabular" VALIGN="middle" ALIGN="left" onclick="Vermensaje('<?php echo date("Ymd", strtotime($row['FecCre']))." ".$row['FolMsg'];?>', 0)"><?php echo date("d/m/Y H:i", strtotime($row['FecCre'])); ?></td>
                    <td class="tabular" VALIGN="middle" ALIGN="center" onclick="Vermensaje('<?php echo date("Ymd", strtotime($row['FecCre']))." ".$row['FolMsg'];?>', 0)"><?php echo $row['TotMsg']; ?></td>
                </tr>
                <?php
                }
                ?>
                </tbody>
            </table>
        </p>
    </div>
    <h3>Todos los dem&aacute;s</h3>
    <div>
        <p>
            <table align="center" width="100%" cellpadding="2" class="display" id="otros">
                <thead>
                    <tr class="tabular">
                        <th class="tabular" ALIGN="center">Sec</th>
                        <th class="tabular">Origen</th>
                        <th class="tabular">Asunto</th>
                        <th class="tabular">Fecha</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                $sp = mssql_query("vm_msgconlec $Cod_Per",$db);
                while (($row = mssql_fetch_array($sp))) {
                ?>
                <tr class="tabular">
                    <td class="tabular" VALIGN="middle" ALIGN="center" onclick="Vermensaje('<?php echo date("Ymd", strtotime($row['FecCre']))." ".$row['FolMsg'];?>', 0)"><?php echo $row['row']; ?></td>
                    <td class="tabular" VALIGN="middle" onclick="Vermensaje('<?php echo date("Ymd", strtotime($row['FecCre']))." ".$row['FolMsg'];?>', 1)"><?php echo utf8_encode($row['OrgMsg']); ?></td>
                    <td class="tabular" VALIGN="middle" onclick="Vermensaje('<?php echo date("Ymd", strtotime($row['FecCre']))." ".$row['FolMsg'];?>', 1)"><?php echo utf8_encode($row['AsuMsg']); ?></td>
                    <td class="tabular" VALIGN="middle" onclick="Vermensaje('<?php echo date("Ymd", strtotime($row['FecCre']))." ".$row['FolMsg'];?>', 1)"><?php echo date("d/m/Y H:i", strtotime($row['FecCre'])); ?></td>
                </tr>
                <?php
                }
                ?>
                </tbody>
            </table>
        <form id="F2" name="F2" method="POST">
            <input type="hidden" name="dfFecha" id="dfFecha" value="" />
            <input type="hidden" name="dfFolio" id="dfFolio" value="" />
            <input type="hidden" name="dfPersiana" id="dfPersiana" value="" />
        </form>
        </p>
    </div>
</div>
                    
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

</script>
</body>
</html>
