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
<link rel="stylesheet" type="text/css" media="all" href="../Include/calendar-green.css" title="win2k-cold-1" />
<script type="text/javascript" src="../Include/calendar.js"></script>
<script type="text/javascript" src="../Include/calendar-es.js"></script>
<script type="text/javascript" src="../Include/calendar-setup.js"></script>
<script type="text/javascript" src="js/AccionesMenu.js"></script>
<script type="text/javascript">
	new UvumiDropdown('dropdown-scliente');

	function vuelve() {
            f2.action = "mivestmed.php?";
            f2.submit();
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
                <h2>Productos sin Fotos en Cat&aacute;logo</h2>
		<form ID="F2" method="POST" name="F2" ACTION="">
		<table WIDTH="90%" BORDER="0" CELLSPACING="0" CELLPADDING="1" ALIGN="right">
		<tr>
			<td class="titulo_tabla" width="20%" style="text-align: left">Marca</td>
                        <td class="titulo_tabla" width="10%" style="text-align: left">Style</td>
                        <td class="titulo_tabla" width="60%" style="text-align: left">Nombre</td>
			<td class="titulo_tabla" width="10%" style="text-align: center">Color</td>
		</tr>
		<?php
			$j = 0;
			$iTotPrd = 0;
			$result = mssql_query("vm_det_sinftocol", $db);
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
				echo "   <td class=\"".$clase1."\" style=\"TEXT-ALIGN: left; \">".$row['Cod_Mca']."</td>\n";
				echo "   <td class=\"".$clase2."\" style=\"TEXT-ALIGN: left; \">".$row['Cod_Sty']."</td>\n";
				echo "   <td class=\"".$clase2."\" style=\"TEXT-ALIGN: left; \">".utf8_encode($row['Nom_Dsg'])."</td>\n";
				echo "   <td class=\"".$clase2."\" style=\"TEXT-ALIGN: center; \">".$row['Key_Pat']."</td>\n";

				echo "</tr>\n";
				$iTotPrd++;
			}
			mssql_free_result($result);
		?>
		<?php if ($iTotPrd == 0) { ?>
		<tr>
			<td colspan="4" style="PADDING-TOP: 10px; TEXT-ALIGN: CENTER; PADDING-BOTTOM: 10px" class="label_left_right_bottom">
			NO EXISTEN COLORES SIN FOTOS EN EL CATALOGO.
			</td>
		</tr>
		
		<?php } else { ?>
		<tr>
			<td colspan="4" style="PADDING-TOP: 10px; TEXT-ALIGN: right" class="label_top">
			</td>
		</tr>
		<?php } ?>
                    <tr><td colspan="4" style="padding-top: 10px; text-align: right"><input type="button" value="Volver" name="Volver" class="btn" onclick="vuelve()" /></td></tr>
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
