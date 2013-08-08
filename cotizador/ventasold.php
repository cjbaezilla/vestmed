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
    if ($row["Id_Mod"] == 1 && $row["ID_Opc"] == 4 && strtoupper($row['CodUsr']) == strtoupper($UsrId)) {
        $OkOpc = true;
        break;
    }
mssql_free_result($sp);

if (!$OkOpc) {
    header ("Location:../index.php");
    exit(0);
}

if (isset($_GET['odc'])) {
    $cod_odc = ok($_GET['odc']);
    $result = mssql_query("vm_aut_odc $cod_odc", $db) or die ("No se pudo autorizar orden de compra");;
}

$IVA = 0.0;
$result = mssql_query("vm_getfolio_s 'IVA'",$db);
if (($row = mssql_fetch_array($result))) $IVA = $row['Tbl_fol'] / 10000.0;


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
<style>
    .izquierda {text-align: left}
    .centro {text-align: center}
    .derecha {text-align: right}
</style>

<!--[if IE]>
<link rel="stylesheet" type="text/css" media="screen" href="dropdown/css/uvumi-dropdown-ie.css" />
<![endif]-->
<script type="text/javascript" src="../Include/ValidarDataInput.js"></script>
<script type="text/javascript" src="../Include/SoloNumeros.js"></script>
<script type="text/javascript" src="../Include/validarRut.js"></script>
<link href="../Include/estilos.css" type="text/css" rel="stylesheet" />
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

	function ver_preview(cot) {
            popwindow("preview.php?cot="+cot,800,600)
	}

	function ver_previewodc(cot) {
            //popwindow("previewodc.php?cot="+cot,800,600)
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
        
            function InicializaTabla(tabla, scrollY, targetSort, targetLeft, targetCenter, targetRight, source) {
                $( '#' + tabla ).dataTable({ 
                    "aoColumnDefs": [
                        { "bSortable": false, "aTargets": targetSort },
                        { "sClass" : "izquierda", "aTargets": targetLeft },
                        { "sClass" : "centro", "aTargets": targetCenter },
                        { "sClass" : "derecha", "aTargets": targetRight },
                    ],
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
                    "bFilter": false,
                    "bSort": true,
                    "bProcessing": true,
                    "sAjaxSource": source
                });     
            }
        
</script>
</head>

<body>
<div id="body">
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
		<table WIDTH="95%" BORDER="0" CELLSPACING="0" CELLPADDING="1" ALIGN="right">
		<tr>
			<td class="titulo_tabla" width="10%" align="middle">Fecha</td>
                        <td class="titulo_tabla" width="5%"  align="middle">Cot</td>
                        <td class="titulo_tabla" width="10%" align="middle">N&uacute;mero</td>
			<td class="titulo_tabla" width="12%" align="middle">Rut</td>
			<td class="titulo_tabla" width="23%" align="middle" style="text-align: left">Nombre</td>
                        <td class="titulo_tabla" width="10%" align="left" style="text-align: center">Total<br />Compra</td>
                        <td class="titulo_tabla" width="10%" align="left" style="text-align: right">Descto</td>
                        <td class="titulo_tabla" width="10%" align="left" style="text-align: right">Desp.</td>
                        <td class="titulo_tabla" width="10%" align="left" style="text-align: right; padding-right: 3px">Total</td>
		</tr>
		<?php
			$j = 0;
			$iTotPrd = 0;
			$result = mssql_query("vm_s_cot_vta", $db);
			while ($row = mssql_fetch_array($result)) {
				echo "<tr>\n";
				if ($j == 0) {
					$clase1 = "";
					$clase2 = "";
				}
				else {
					$clase1 = "";
					$clase2 = "";
				}
				echo "   <td class=\"".$clase1."\" style=\"TEXT-ALIGN: center; \">".date("d/m/Y", strtotime($row['Fec_Cot']))."</td>\n";
				echo "   <td class=\"".$clase2."\" style=\"TEXT-ALIGN: center; \"><a href=\"javascript:ver_preview(".$row['Cod_Cot'].")\">".$row['Num_Cot']."</a></td>\n";
				echo "   <td class=\"".$clase2."\" style=\"TEXT-ALIGN: center; \"><a href=\"javascript:ver_previewodc(".$row['Cod_Cot'].")\">".$row['Cod_Odc']."</a></td>\n";
				echo "   <td class=\"".$clase2."\" style=\"TEXT-ALIGN: right; PADDING-RIGHT: 10px\">".formatearRut($row['Num_Doc'])."</td>\n";
				if ($row["Cod_TipPer"] == 1) {
                                    $nombre = utf8_encode ($row['Pat_Per']." ".$row['Mat_Per'].", ".$row['Nom_Per']);
                                    $nombre_corto = utf8_encode($row['Nom_Per']." ".$row['Pat_Per']);
                                    echo "   <td class=\"".$clase2."\" style=\"TEXT-ALIGN: left\" title=\"$nombre\">".$nombre_corto."</td>\n";
                                }
				else
					echo "   <td class=\"".$clase2."\" style=\"TEXT-ALIGN: left\">".substr(utf8_encode($row['RznSoc_Per']),0,20)."</td>\n";
                                /*
                                if ($row['Arc_Adj'] != " ") {
                                    echo "   <td class=\"".$clase2."\" style=\"TEXT-ALIGN: center \">";
                                    echo "<a href=\"javascript:ver_comprobante('".$row['ArcFis_Adj']."')\">SI</a>";
                                    echo "</td>\n";
                                    echo "   <td class=\"".$clase2."\" style=\"TEXT-ALIGN: center\">";
                                    echo "<a href=\"javascript:aut_compra('".$row['Cod_Odc']."')\"><img src=\"../images/check.gif\" alt=\"\" title=\"Autorizar Pago\"/></a>";
                                    echo "</td>\n";
                                }
                                else {
                                    echo "   <td class=\"".$clase2."\" style=\"TEXT-ALIGN: center\">";
                                    echo "NO";
                                    echo "</td>\n";
                                    echo "   <td class=\"".$clase2."\" style=\"TEXT-ALIGN: center\">";
                                    echo "&nbsp;";
                                    echo "</td>\n";
                                }
                                */
                                if ($row['Cod_Iva'] == 1) {
                                    $mto_odc = ($row['Mto_Odc'] + $row['Prc_Dsp']) / (1.0 + $IVA);
                                    $val_des = ($row['Mto_Odc'] * $row['Val_Des'] / 100) / (1.0 + $IVA);
                                    $prc_dsp = $row['Prc_Dsp'] / (1.0 + $IVA);
                                    $mto_tot = $mto_odc - $val_des - $prc_dsp;
                                }
                                else {
                                    $mto_odc = ($row['Mto_Odc'] + $row['Prc_Dsp']);
                                    $val_des = ($row['Mto_Odc'] * $row['Val_Des'] / 100);
                                    $prc_dsp = $row['Prc_Dsp'];
                                    $mto_tot = $mto_odc - $val_des - $prc_dsp;
                                }
				echo "   <td class=\"".$clase2."\" style=\"TEXT-ALIGN: right;\">".number_format($mto_odc,0,',','.')."</td>\n";
				echo "   <td class=\"".$clase2."\" style=\"TEXT-ALIGN: right;\">".number_format($val_des,0,',','.')."</td>\n";
				echo "   <td class=\"".$clase2."\" style=\"TEXT-ALIGN: right;\">".number_format($prc_dsp,0,',','.')."</td>\n";
				echo "   <td class=\"".$clase2."\" style=\"TEXT-ALIGN: right;\">".number_format($mto_tot,0,',','.')."</td>\n";


				echo "</tr>\n";
				$j = 1 - $j;
				$iTotPrd++;
			}
			mssql_free_result($result);
		?>
		<?php if ($iTotPrd == 0) { ?>
		<tr>
			<td colspan="7" style="PADDING-TOP: 10px; TEXT-ALIGN: CENTER; PADDING-BOTTOM: 10px" class="label_left_right_bottom">
			NO EXISTEN VENTAS PENDIENTES DE APROBAR.
			</td>
		</tr>
		
		<?php } else { ?>
		<tr>
			<td colspan="7" style="PADDING-TOP: 10px; TEXT-ALIGN: right" class="label_top">
				<!--input type="submit" name="Enviar" value=" Enviar a la Orden de Reposici&oacute;n " class="button2" /-->
			</td>
		</tr>
		<?php } ?>
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
