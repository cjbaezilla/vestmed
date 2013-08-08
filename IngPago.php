<?php
//Obtengo los datos de conexion de la base de datos
ini_set('display_errors', '0');
session_start();
include("config.php");

$cod_cot = 0;
$status = "";
if (isset($_GET['cot'])) $cod_cot = intval($_GET['cot']);
if (isset($_GET['status'])) $status = $_GET['status'];

?>
<!DOCTYPE html PUBLIC "-//W3C//Dtd XHTML 1.0 transitional//EN" "http://www.w3.org/tr/xhtml1/Dtd/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title></title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="Include/estilos.css" type="text/css" rel="stylesheet" />
<link href="css/layout.css" type="text/css" rel="stylesheet" />
<script type="text/javascript" src="Include/SoloNumeros.js"></script>
<script language="JavaScript" src="Include/fngenerales.js"></script>
<script type="text/javascript" src="js/jquery-1.3.2.min.js"></script>
<script type="text/javascript">
    function ActualizaPadre()
    {
        parent.opener.ActualizarPago();
        window.close();
    }
    </script>
</head>

<body bgcolor="#c1f4e5" style="margin-top:5px;">
<div style="overflow:auto;">
    <form ID="F2" method="POST" name="F2" action="cotizador/enviaroc.php?cot=<?php echo $cod_cot; ?>&paso=23" onsubmit="return check_file()" enctype="multipart/form-data">
    <table WIDTH="100%" BORDER="0" CELLSPACING="0" CELLPADDING="1" ALIGN="center">
    <tr>
	<td width="100%" style="padding-left: 5px; padding-bottom: 5px; padding-top: 10px; border-top: goldenrod 1px solid; border-right: goldenrod 1px solid; border-left: goldenrod 1px solid;">
	Selecciones su Banco:
	<select id="Bco" name="Bco" class="textfieldv2">
	<option selected value="_NONE">Seleccione Banco</option>
	<?php //Seleccionar las ciudades
	$sp = mssql_query("vm_bco_s",$db);
	while($row = mssql_fetch_array($sp))
	{
		?>
		<option value="<?php echo utf8_encode($row['Nom_Bco']) ?>"><?php echo utf8_encode($row['Nom_Bco']) ?></option>
		<?php
	}
	?>
        </select>
	</td>
    </tr>
    <tr>
	<td width="100%" style="padding-left: 5px; padding-bottom: 5px; padding-top: 10px; border-left: goldenrod 1px solid; border-right: goldenrod 1px solid;">
	Comprobante <span>
                        <input class="file-contacto" type="file" name="documento" id="documento" size="28" onchange="fichero.value = this.value"/>
                        <input type="hidden" name="fichero" id="fichero"/> &nbsp; PDF, doc, docx, xls, xlsx, jpg, gif
                    </span>
	</td>
    </tr>
    <tr>
	<td width="100%" style="padding-left: 5px; padding-bottom: 5px; padding-top: 10px; border-left: goldenrod 1px solid; border-right: goldenrod 1px solid;">
	Monto Transferencia <span>
                        <input class="file-textfieldv2" type="text" name="montoCmp" id="montoCmp" size="10" onKeyPress="return SoloNumeros(event)" />
                    </span>
	</td>
    </tr>
    <tr>
	<td width="100%" style="text-align: right; padding-left: 5px; padding-bottom: 5px; padding-top: 10px; border-left: goldenrod 1px solid; border-right: goldenrod 1px solid; border-bottom: goldenrod 1px solid;">
                <input type="submit" class="btn" name="enviar_arch" value="Enviar" />
                <input type="button" class="btn" value="Salir" onclick="javascript: window.close()" />
	</td>
    </tr>
    </table>
    </form>
</div>
    <script type="text/javascript">
	var f1;
        var f2;

	f1 = document.F1;
	f2 = document.F2;

        <?php if ($status == "ok") { ?>
        ActualizaPadre();
        <?php } ?>

</script>
</body>
</html>
