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
<style>
    .izquierda {text-align: left}
    .centro {text-align: center}
    .derecha {text-align: right}
</style>

<script type="text/javascript">
    new UvumiDropdown('dropdown-scliente');

    function MarcarTodos(form,nombrecheckbox) {
       var i;
       for (i=0; i<form.elements.length; i++) {
            if (form.elements[i].name === nombrecheckbox)
                    form.elements[i].checked = true;
       }
    }

    function DesMarcarTodos(form,nombrecheckbox) {
       var i;
       for (i=0; i<form.elements.length; i++) {
            if (form.elements[i].name === nombrecheckbox)
                    form.elements[i].checked = false;
       }
    }
    
    function ver_preview(cot) {
        popwindow("preview.php?cot="+cot,800,600);
    }

    function ver_previewodc(cot) {
        f2.action = "previewodc.php?cot="+cot;
        f2.submit();
    }

    function ver_comprobante(comprobante) {
        popwindow('<?php echo "../".$pathadjuntos; ?>'+comprobante,800,600);
    }

    function aut_compra(cod_odc) {
        if (confirm("Confirma autorizaci\u00f3n del pago ?")) {
            f2.action = "ventas.php?odc="+cod_odc;
            f2.submit();
        }
    }    
    
    /* Define two custom functions (asc and desc) for string sorting */
    function InicializaTabla(tabla, scrollY, targetSort, targetLeft, targetCenter, targetRight, source) {
        $j( '#' + tabla ).dataTable({ 
            "aoColumnDefs": [
                { "bSortable": false, "aTargets": [1,2,3,5] },
                { "sClass" : "izquierda", "aTargets": [4] },
                { "sClass" : "centro", "aTargets": [0,1,2,6] },
                { "sClass" : "derecha", "aTargets": [3,5] }
            ],
            "aoColumns":[
                { "sType": 'string-case'},
                null,
                null,
                null,
                null,
                null,
                null
            ],
            "aaSorting":[[0,"desc"]],
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
            "sScrollY": scrollY,
            "bScrollCollapse": true,
            "bPaginate": false,
            "bJQueryUI": true,
            "bFilter": true,
            "bSort": true,
            //"bProcessing": true,
            "sAjaxSource": source,
            "sDom": 'fT<"clear">tip',
            "oTableTools": {
                "sSwfPath": "../DataTables-1.9.4/media/swf/copy_csv_xls_pdf.swf"
            }
        });     
    }
    
</script>
<link href="http://code.jquery.com/ui/1.9.0/themes/redmond/jquery-ui.css" rel="stylesheet" />
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.8/jquery.js"></script>
<script src="http://code.jquery.com/ui/1.9.0/jquery-ui.min.js"></script>

<style type="text/css" title="currentStyle">
        @import "../DataTables-1.9.4/media/css/demo_page.css";
        @import "../DataTables-1.9.4/media/css/jquery.dataTables_themeroller.css";
        @import "../DataTables-1.9.4/examples/examples_support/themes/smoothness/jquery-ui-1.8.4.custom.css";
        @import "../DataTables-1.9.4/media/css/TableTools.css";
</style>
<script type="text/javascript" language="javascript" src="../DataTables-1.9.4/media/js/jquery.dataTables.js"></script>
<script type="text/javascript" language="javascript" src="../DataTables-1.9.4/extras/TableTools/media/js/ZeroClipboard.js"></script>
<script type="text/javascript" language="javascript" src="../DataTables-1.9.4/extras/TableTools/media/js/TableTools.js"></script>
<script type="text/javascript">
    //*************************************
    var $j = jQuery.noConflict();
    
    jQuery.fn.dataTableExt.oSort['string-case-asc']  = function(xi,yi) {
        var Arr1 = xi.split('/');
        var Arr2 = yi.split('/');
        var x = Arr1[2] + Arr1[1] + Arr1[0];
        var y = Arr2[2] + Arr2[1] + Arr2[0];
        return ((x < y) ? -1 : ((x > y) ?  1 : 0));
    };

    jQuery.fn.dataTableExt.oSort['string-case-desc'] = function(xi,yi) {
        var Arr1 = xi.split('/');
        var Arr2 = yi.split('/');
        var x = Arr1[2] + Arr1[1] + Arr1[0];
        var y = Arr2[2] + Arr2[1] + Arr2[0];
        return ((x < y) ?  1 : ((x > y) ? -1 : 0));
    };
    
    $j(function() {
        var targeSort = new Array (5);
        var targetClassLeft = new Array (4);
        var targetClassCenter = new Array (0,1,2,6);
        var targetClassRight = new Array (3,5);
        InicializaTabla ("tblOrdenes", 440, targeSort, targetClassLeft, targetClassCenter, targetClassRight, "source/ordenes.php");
    });
    
</script>
</head>

</head>

<body>
<div id="body" style="width: 99%">
	<div id="header"></div>
    <ul id="usuario_registro">
		<?php 	echo display_usr($UsrId, $Perfil, "Ventas", $db); ?>
    </ul>
    <div id="work">
<?php formar_topbox ("100%%","center"); ?>
<p align="left"><strong>Escritorio</strong></p>
<p align="center">
<table WIDTH="100%" BORDER="0" CELLSPACING="0" CELLPADDING="1" ALIGN="center">
<tr>
<td width="170px" align="left" valign="top">
	<?php echo display_mnuizq($UsrId, $db); ?>
</td>
<td width="1%">&nbsp;</td>
<td  valign="top" class="label_left_right_top_bottom" STYLE="PADDING-LEFT:20px; PADDING-RIGHT:20px; TEXT-ALIGN:left">
	<table WIDTH="100%" BORDER="0" CELLSPACING="0" CELLPADDING="1" ALIGN="center">
	<tr><td>
                <h2>Ordenes de Compras Recibidas</h2>
		<form ID="F2" method="POST" name="F2" ACTION="">
		<table id="tblOrdenes" WIDTH="95%" BORDER="0" CELLSPACING="0" CELLPADDING="1" ALIGN="right" class="display">
                    <thead>
                        <tr>
                            <th class="titulo_tabla" width="10%" align="middle">Fecha</th>
                            <th class="titulo_tabla" width="5%"  align="middle">Cot</th>
                            <th class="titulo_tabla" width="10%" align="middle">N&uacute;mero</th>
                            <th class="titulo_tabla" width="10%" align="middle">Rut</th>
                            <th class="titulo_tabla" width="45%" align="middle" style="text-align: left">Nombre</th>
                            <th class="titulo_tabla" width="10%" align="right" style="text-align: center">Total Compra<br />(+ Despacho)</th>
                            <th class="titulo_tabla" width="10%" align="center" style="text-align: center">Comprobante<br/>Despacho</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
		</table>
		</form>
	</td></tr>
	</table>
</td>
</tr>
</table>
</p>
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
