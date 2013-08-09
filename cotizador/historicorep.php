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
    if ($row["Id_Mod"] == 1 && $row["ID_Opc"] == 1 && strtoupper($row['CodUsr']) == strtoupper($UsrId)) {
        $OkOpc = true;
        break;
    }
mssql_free_result($sp);

if (!$OkOpc) {
    header ("Location:../index.php");
    exit(0);
}

if (isset($_GET['opc'])) $_SESSION['opcion'] = $_GET['opc'];


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/tr/xhtml1/DTD/xhtml1-transitional.dtd">
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
<link href="../Include/estilos.css" type="text/css" rel="stylesheet" />
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
	function ver_preview(cot) {
		popwindow("preview.php?cot="+cot,800,600)
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
<!--script type="text/javascript" language="javascript" src="../DataTables-1.9.4/media/js/jquery.js"></script-->
<script type="text/javascript" language="javascript" src="../DataTables-1.9.4/media/js/jquery.dataTables.js"></script>
<script>
    $(function() {
        $( '#historico' ).dataTable({    
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
		<?php 	echo display_usr($UsrId, $Perfil, "Cotizaciones", $db); ?>
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
                <h2>Cotizaciones Hist&oacute;ricas</h2>
		<form ID="F2" method="POST" name="F2" ACTION="">
		<table id="historico" WIDTH="100%" class="display">
                <thead>
                    <tr>
                            <th width="10%" align="middle">Fecha</th>
                            <th width="10%" align="middle">Numero</th>
                            <th width="15%" align="middle">Rut</th>
                            <th width="55%" align="middle" style="text-align: left">Nombre</th>
                            <th width="10%" align="left">Canal</th>
                    </tr>
                </thead>
                <tbody>
		<?php
			$j = 0;
			$iTotPrd = 0;
			$result = mssql_query("vm_s_cot_his", $db);
			while ($row = mssql_fetch_array($result)) {
				if ($row["Cod_TipPer"] == 1)
                                    $nombre = utf8_encode ($row['Pat_Per']." ".$row['Mat_Per'].", ".$row['Nom_Per']);
                                else
                                    $nombre = utf8_encode($row['RznSoc_Per']);
                ?>
				<tr>
				<td style="TEXT-ALIGN: center "><?php echo date("d/m/Y", strtotime($row['Fec_Cot'])); ?></td>
				<td style="TEXT-ALIGN: center"><a href="javascript:ver_preview('<?php echo $row['Cod_Cot']; ?>')"><?php echo $row['Num_Cot'] ?></a></td>
				<td style="TEXT-ALIGN: right; PADDING-RIGHT: 10px"><?php echo formatearRut($row['Num_Doc']); ?></td>
                                <td style="TEXT-ALIGN: left"><?php echo $nombre; ?></td>
				<td style="TEXT-ALIGN: left"><?php echo $row['canal']; ?></td>
				</tr>
                <?php
				$iTotPrd++;
			}
			mssql_free_result($result);
		?>
		<?php if ($iTotPrd == 0) { ?>
		<tr>
			<td colspan="5" style="PADDING-TOP: 10px; TEXT-ALIGN: CENTER; PADDING-BOTTOM: 10px">
			NO EXISTEN COTIZACIONES PENDIENTES. SI DESEA AGREGAR UNA PINCHE <A HREF="reponer_ins.php?filter=5">AQU&Iacute;</A>
			</td>
		</tr>
		<?php } ?>
                </tbody>
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
<script language="javascript">
	var f1;
	var f2;
	f1 = document.F1;
	f2 = document.F2;
</script>


</body>
</html>
