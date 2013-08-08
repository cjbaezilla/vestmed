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

$persiana = (isset($_POST['dfPersiana']) ? intval(ok($_POST['dfPersiana'])) : 0);

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
<link href="../meson/css/itunes.css" type="text/css" rel="stylesheet" />
<!--[if IE]>
<link rel="stylesheet" type="text/css" media="screen" href="dropdown/css/uvumi-dropdown-ie.css" />
<![endif]-->
<script language="JavaScript" src="../Include/ValidarDataInput.js"></script>
<script language="JavaScript" src="../Include/SoloNumeros.js"></script>
<script language="JavaScript" src="../Include/validarRut.js"></script>
<LINK href="../Include/estilos.css" type="text/css" rel="stylesheet" />
<script type="text/javascript" src="js/AccionesMenu.js"></script>

<script type="text/javascript">
    new UvumiDropdown('dropdown-scliente');

    function MarcarTodos(form,nombrecheckbox) {
       var i;
       for (i=0; i<form.elements.length; i++) {
            if (form.elements[i].name == nombrecheckbox)
                    form.elements[i].checked = true;
       }
    }

    function DesMarcarTodos(form,nombrecheckbox) {
       var i;
       for (i=0; i<form.elements.length; i++) {
            if (form.elements[i].name == nombrecheckbox)
                    form.elements[i].checked = false;
       }
    }
</script>
<link href="http://code.jquery.com/ui/1.9.0/themes/redmond/jquery-ui.css" rel="stylesheet" />
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.8/jquery.js"></script>
<script src="http://code.jquery.com/ui/1.9.0/jquery-ui.min.js"></script>

<style type="text/css" title="currentStyle">
        @import "../DataTables-1.9.4/media/css/demo_page.css";
        @import "../DataTables-1.9.4/media/css/jquery.dataTables_themeroller.css";
        @import "../DataTables-1.9.4/examples/examples_support/themes/smoothness/jquery-ui-1.8.4.custom.css";
</style>
<script type="text/javascript" language="javascript" src="../DataTables-1.9.4/media/js/jquery.dataTables.js"></script>

<script type="text/javascript">
    //*************************************
    var $j = jQuery.noConflict();
    
    function Vermensaje(folio, persiana) {
        var arrFolio = folio.split(' ');
        $j("#dfFecha").val(arrFolio[0]);
        $j("#dfFolio").val(arrFolio[1]);
        $j("#dfPersiana").val(persiana);
        f2.action = "detallecaso.php";
        f2.submit();
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
</head>

<body>
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
	<tr><td>
		<H1>Nuevos Mensajes Recibidos</H1>
                
<div id="accordion">
    <h3>No Le&iacute;dos</h3>
    <div>
        <div id="reporte1">
            <table align="center" width="100%" id="noleidos" class="display" >
                <thead>
                    <tr>
                        <th width="5%"><Sec</th>
                        <th width="22%">Fecha</th>
                        <th width="35%">Origen</th>
                        <th width="35%">Asunto</th>
                        <th width="3%">Ctd</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                $sp = mssql_query("vm_msgsinlec",$db);
                while (($row = mssql_fetch_array($sp))) {
                ?>
                <tr>
                    <td VALIGN="middle" onclick="Vermensaje('<?php echo date("Ymd", strtotime($row['FecCre']))." ".$row['FolMsg'];?>', 0)"><?php echo $row['row']; ?></td>
                    <td VALIGN="middle" onclick="Vermensaje('<?php echo date("Ymd", strtotime($row['FecCre']))." ".$row['FolMsg'];?>', 0)"><?php echo date("d/m/Y H:i", strtotime($row['FecCre'])); ?></td>
                    <td VALIGN="middle" onclick="Vermensaje('<?php echo date("Ymd", strtotime($row['FecCre']))." ".$row['FolMsg'];?>', 0)"><?php echo utf8_encode($row['OrgMsg']); ?></td>
                    <td VALIGN="middle" onclick="Vermensaje('<?php echo date("Ymd", strtotime($row['FecCre']))." ".$row['FolMsg'];?>', 0)"><?php echo utf8_encode($row['AsuMsg']); ?></td>
                    <td VALIGN="middle" ALIGN="center" onclick="Vermensaje('<?php echo date("Ymd", strtotime($row['FecCre']))." ".$row['FolMsg'];?>', 0)"><?php echo $row['TotMsg']; ?></td>
                </tr>
                <?php
                }
                ?>
                </tbody>
            </table>
        </div>
    </div>        
    <h3>Todos los dem&aacute;s</h3>
    <div>
        <div id="reporte2">
            <table align="center" width="100%" cellpadding="2" class="display" id="otros">
                <thead>
                    <tr>
                        <th width="5%">Sec</th>
                        <th width="23%">Fecha</th>
                        <th width="45%">Origen</th>
                        <th width="27%">Asunto</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                $sp = mssql_query("vm_msgconlec",$db);
                while (($row = mssql_fetch_array($sp))) {
                ?>
                <tr>
                    <td VALIGN="middle" onclick="Vermensaje('<?php echo date("Ymd", strtotime($row['FecCre']))." ".$row['FolMsg'];?>', 0)"><?php echo $row['row']; ?></td>
                    <td VALIGN="middle" onclick="Vermensaje('<?php echo date("Ymd", strtotime($row['FecCre']))." ".$row['FolMsg'];?>', 0)"><?php echo date("d/m/Y H:i", strtotime($row['FecCre'])); ?></td>
                    <td VALIGN="middle" onclick="Vermensaje('<?php echo date("Ymd", strtotime($row['FecCre']))." ".$row['FolMsg'];?>', 0)"><?php echo utf8_encode($row['OrgMsg']); ?></td>
                    <td VALIGN="middle" onclick="Vermensaje('<?php echo date("Ymd", strtotime($row['FecCre']))." ".$row['FolMsg'];?>', 0)"><?php echo utf8_encode($row['AsuMsg']); ?></td>
                </tr>
                <?php
                }
                ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
                
        <form id="F2" name="F2" method="POST">
            <input type="hidden" name="dfFecha" id="dfFecha" value="" />
            <input type="hidden" name="dfFolio" id="dfFolio" value="" />
            <input type="hidden" name="dfPersiana" id="dfPersiana" value="" />
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
</script>

</body>
</html>
