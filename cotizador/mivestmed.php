<?php
//Obtengo los datos de conexion de la base de datos
ini_set('display_errors', '1');
session_start();

if (!isset($_SESSION['usuario'])) {
    if (!isset($_POST["usuario"])) header("Location: ../index.php");
    $_SESSION['usuario'] = $_POST["usuario"];     
}
$UsrId = (isset($_SESSION['usuario'])) ? $_SESSION['usuario'] : "";

include("global_cot.php");

$OkOpc = false;
$sp = mssql_query("vm_seg_usr_opcmodweb '$UsrId'",$db) or die ("error sql, vm_seg_usr_opcmodweb '$UsrId'");
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

$result = mssql_query ("vm_msgsinres_count", $db)
						or die ("No se pudo leer mensajes sin lectura");
if (($row = mssql_fetch_array($result))) $tot_reg = $row["tot_reg"];
mssql_free_result($result);
/*
$result = mssql_query ("vm_cna_sin_res_ctt_cot", $db)
						or die ("No se pudo leer datos del cliente");
if (($row = mssql_fetch_array($result))) $tot_cnactt = $row["tot_cna"];
mssql_free_result($result);

$result = mssql_query ("vm_cna_sin_res_cot", $db)
						or die ("No se pudo obtener datos de los mensajes");
if (($row = mssql_fetch_array($result))) $tot_cna = $row["tot_cna"];
mssql_free_result($result);
*/

$result = mssql_query ("vm_tot_consinres", $db)
						or die ("No se pudo obtener datos de las cotizaciones");
if (($row = mssql_fetch_array($result))) $tot_cot = $row["tot_cot"];
mssql_free_result($result);

$result = mssql_query ("vm_tot_vtasinut", $db)
						or die ("No se pudo obtener datos de las ventas");
if (($row = mssql_fetch_array($result))) $tot_vta = $row["tot_vta"];
mssql_free_result($result);

$result = mssql_query ("vm_tot_sinftoctl", $db)
						or die ("No se pudo obtener datos de los productos sin fotos");
if (($row = mssql_fetch_array($result))) $tot_sinfto = $row["tot_sinfto"];
mssql_free_result($result);

$result = mssql_query ("vm_tot_sinftocol", $db)
						or die ("No se pudo obtener datos de los colores sin fotos");
if (($row = mssql_fetch_array($result))) $tot_sinftocol = $row["tot_sinfto"];
mssql_free_result($result);

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

    function Show_Mensajes ()
    {
        f2.action = "mismensajes.php";
        f2.submit();
    }
    
    function Show_Cotizaciones ()
    {
        f2.action = "mensajes.php?accion=12";
        f2.submit();
    }

    function Show_Contactos ()
    {
        f2.action = "mensajes.php?accion=11";
        f2.submit();
    }

    function Show_CotSinResp ()
    {
        f2.action = "escritorio_cot.php";
        f2.submit();
    }

    function Show_VtaPorAut ()
    {
        f2.action = "ventas.php";
        f2.submit();
    }

    function Show_FotCat ()
    {
        f2.action = "sinftocat.php";
        f2.submit();
    }

    function Show_FotCol ()
    {
        f2.action = "sinftocol.php";
        f2.submit();
    }
</script>
</head>

<body>
<div id="body">
	<div id="header"></div>
    <ul id="usuario_registro">
		<?php 	echo display_usr($UsrId, 0, "MiVestmed", $db); ?>
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
            <form ID="F2" method="POST" name="F2" ACTION="">
            <h1>Mensajes Recibidos</h1>
            <table BORDER="0" CELLSPACING="1" CELLPADDING="3" width="650" ALIGN="center">
            <tr>
                <td width="100%" VALIGN="TOP" class="dato" style="PADDING-TOP: 10px">Sin Leer: (<a href="javascript:Show_Mensajes();"><?php echo $tot_reg ?></a>)</td>
            </tr>
            <!--tr>
                <td width="100%" VALIGN="TOP" class="dato">Msg Cotizaciones: (<a href="javascript:Show_Cotizaciones();"><?php echo $tot_cna; ?></a>)</td>
            </tr-->
            </table>
            <br /> <br />
            <h1>Cotizaciones</h1>
            <table BORDER="0" CELLSPACING="1" CELLPADDING="3" width="650" ALIGN="center">
            <tr>
                <td width="100%" VALIGN="TOP" class="dato" style="PADDING-TOP: 10px">Sin responder: (<a href="javascript:Show_CotSinResp();"><?php echo $tot_cot ?></a>)</td>
            </tr>
            </table>
            <br /> <br />
            <h1>Ventas</h1>
            <table BORDER="0" CELLSPACING="1" CELLPADDING="3" width="650" ALIGN="center">
            <tr>
                <td width="100%" VALIGN="TOP" class="dato" style="PADDING-TOP: 10px">Por Autorizar: (<a href="javascript:Show_VtaPorAut();"><?php echo $tot_vta ?></a>)</td>
            </tr>
            </table>
            <br /> <br />
            <h1>Fotos Faltantes</h1>
            <table BORDER="0" CELLSPACING="1" CELLPADDING="3" width="650" ALIGN="center">
            <tr>
                <td width="100%" VALIGN="TOP" class="dato" style="PADDING-TOP: 10px">Cat&aacute;logo: (<a href="javascript:Show_FotCat();"><?php echo $tot_sinfto ?></a>)</td>
            </tr>
            <tr>
                <td width="100%" VALIGN="TOP" class="dato" style="PADDING-TOP: 10px">Colores: (<a href="javascript:Show_FotCol();"><?php echo $tot_sinftocol ?></a>)</td>
            </tr>
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
