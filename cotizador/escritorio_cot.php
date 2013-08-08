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
    if ($row["Id_Mod"] == 1 && $row["ID_Opc"] == 2 && strtoupper($row['CodUsr']) == strtoupper($UsrId)) {
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
		<h2>Cotizaciones</h2>
		<form ID="F2" method="POST" name="F2" ACTION="">
		<table WIDTH="90%" BORDER="0" CELLSPACING="0" CELLPADDING="1" ALIGN="right">
		<tr>
			<td class="titulo_tabla" width="10%" align="middle">Fecha</td>
                        <td class="titulo_tabla" width="10%" align="middle">N&uacute;mero</td>
			<td class="titulo_tabla" width="15%" align="middle">Rut</td>
			<td class="titulo_tabla" width="55%" align="middle" style="text-align: left">Nombre</td>
			<td class="titulo_tabla" width="10%" align="left" style="text-align: left">Canal</td>
		</tr>
		<?php
			$j = 0;
			$iTotPrd = 0;
			$result = mssql_query("vm_s_cot_pdt", $db);
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
				echo "   <td class=\"".$clase1."\" style=\"TEXT-ALIGN: center \">".date("d/m/Y", strtotime($row['Fec_Cot']))."</td>\n";
				echo "   <td class=\"".$clase2."\" style=\"TEXT-ALIGN: center \"><a href=\"nueva_cot.php?cot=".$row['Cod_Cot']."\">".$row['Num_Cot']."</a></td>\n";
				echo "   <td class=\"".$clase2."\" style=\"TEXT-ALIGN: right; PADDING-RIGHT: 10px\">".formatearRut($row['Num_Doc'])."</td>\n";
				if ($row["Cod_TipPer"] == 1)
					echo "   <td class=\"".$clase2."\" style=\"TEXT-ALIGN: left\">".utf8_encode ($row['Pat_Per']." ".$row['Mat_Per'].", ".$row['Nom_Per'])."</td>\n";
				else
					echo "   <td class=\"".$clase2."\" style=\"TEXT-ALIGN: left\">".$row['RznSoc_Per']."</td>\n";
				echo "   <td class=\"".$clase2."\" style=\"TEXT-ALIGN: left \">".$row['canal']."</td>\n";
				echo "</tr>\n";
				$j = 1 - $j;
				$iTotPrd++;
			}
			mssql_free_result($result);
		?>
		<?php if ($iTotPrd == 0) { ?>
		<tr>
			<td colspan="5" style="PADDING-TOP: 10px; TEXT-ALIGN: CENTER; PADDING-BOTTOM: 10px" class="label_left_right_bottom">
			NO EXISTEN COTIZACIONES PENDIENTES. SI DESEA AGREGAR UNA PINCHE <A HREF="nueva_cot.php">AQU&Iacute;</A>
			</td>
		</tr>
		
		<?php } else { ?>
		<tr>
			<td colspan="5" style="PADDING-TOP: 10px; TEXT-ALIGN: right" class="label_top">
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
