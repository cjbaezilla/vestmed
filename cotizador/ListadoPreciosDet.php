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
    if ($row["Id_Mod"] == 1 && $row["ID_Opc"] == 11 && $row['CodUsr'] == $UsrId) {
        $OkOpc = true;
        break;
    }
mssql_free_result($sp);

if (!$OkOpc) {
    header ("Location:../index.php");
    exit(0);
}

$p_grpprd = ok($_POST['producto']);
$p_pat = isset($_GET['pat']) ? $_GET['pat'] : "";
$p_sze = isset($_GET['sze']) ? $_GET['sze'] : "";

$result = mssql_query("vm_strinv_prodinfo '".$p_grpprd."'", $db);
if (($row = mssql_fetch_array($result))) {
    $cod_sty = $row['style'];
    $cod_mca = $row['marca'];
    $cod_grppat = $row['Cod_GrpPat'];    
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
<script type="text/javascript" src="../js/jquery-1.3.2.min.js"></script>
<link href="css/itunes.css" type="text/css" rel="stylesheet" />
<script language="javascript">
    function ReCargar()
    {
        var frm = document.getElementById('frmInforme');
        var pat = frm.codpat.value;
        var sze = frm.codsze.value;

        frm.action = '<?php echo $_SERVER['PHP_SELF']; ?>?pat=' + pat + '&sze=' + sze;
        frm.submit();
    }
</script>
</head>
<body>
<div id="body">
	<div id="header"></div>
    <ul id="usuario_registro">
		<?php 	echo display_usr($UsrId, $Perfil, "MiVestmed", $db); ?>
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
                <h2>Listado de Precios <?php echo $cod_sty ?></h2>
        <form ID="frmInforme" method="POST" name="frmInforme">
        <table border="1" cellpading="0" cellspacing="0" align="center" class="tabular">
            <thead>
                <tr class="tabular">
                    <th class="tabular">Bodega</th>
                    <th class="tabular">StockP</th>
                    <th class="tabular">producto</th>
                    <th class="tabular">Style</th>
                    <th class="tabular">
                    	<select class="textfield_m" name="codpat" onchange="ReCargar()">
                        	<option value="">KeyPat</option>
                                <?php
            $result = mssql_query ("sp_getstock_filpat '$cod_mca', '$cod_sty', '$cod_grppat', '001', '$hoy'", $db) 
                           or die ("No pudo obtener datos del STOCK");
            while (($row = mssql_fetch_array($result))) {
                                ?>
                        	<option value="<?php echo $row['Cod_Pat'] ?>"<?php if ($row['Cod_Pat'] == $p_pat) echo " selected"; ?>><?php echo $row['Key_Pat'] ?></option>
                                <?php
            }
                                ?>
                        </select>                        
                    </th>
                    <th class="tabular">
                    	<select class="textfield_m" name="codsze" onchange="ReCargar()">
                        	<option value="">ValSze</option>
                                <?php
                                $result = mssql_query ("sp_getstock_filsze '$cod_mca', '$cod_sty', '$cod_grppat', '001', '$hoy'", $db) 
                                               or die ("No pudo obtener datos del STOCK");
                                while (($row = mssql_fetch_array($result))) {
                                ?>
                        	<option value="<?php echo $row['Cod_Sze'] ?>"<?php if ($row['Cod_Sze'] == $p_sze) echo " selected"; ?>><?php echo $row['Val_Sze'] ?></option>
                                <?php
                                }
                                ?>
                        </select>                        
                    </th>
                    <th class="tabular">glosa</th>
                    <th class="tabular">TIPOPRODUCTO</th>
                    <th class="tabular">Stock<br></br>M&iacute;nimo</th>
                    <th class="tabular">Stock<br></br>M&aacute;ximo</th>
                    <th class="tabular">Precio</th>
                    <th class="tabular">Unidad</th>
                </tr>
            </thead>
            <tbody>

        <?php
            $hoy = date('Ymd');
            //echo "sp_getstock '$cod_mca', '$cod_sty', '$cod_grppat', '001', '$hoy', '$p_pat','$p_sze'";
            $result = mssql_query ("sp_getstock '$cod_mca', '$cod_sty', '$cod_grppat', '001', '$hoy', '$p_pat', '$p_sze'", $db) 
                           or die ("No pudo obtener datos del STOCK");
            while (($row = mssql_fetch_array($result))) {
        ?>
                <tr class="tabular">
                    <td class="tabular" align="center"><?php echo $row['Bodega'] ?></td>
                    <td class="tabular" align="center"><?php echo number_format($row['StockP'], 0, ",", ".") ?></td>
                    <td class="tabular"><?php echo $row['producto'] ?></td>
                    <td class="tabular"><?php echo $row['Cod_Sty'] ?></td>
                    <td class="tabular"><?php echo $row['Key_Pat'] ?></td>
                    <td class="tabular"><?php echo $row['Val_Sze'] ?></td>
                    <td class="tabular"><?php echo $row['glosa'] ?></td>
                    <td class="tabular"><?php echo $row['TIPOPRODUCTO'] ?></td>
                    <td class="tabular"><?php echo number_format($row['StockMinimo'], 0, ",", "."); ?></td>
                    <td class="tabular"><?php echo number_format($row['StockMaximo'], 0, ",", "."); ?></td>
                    <td class="tabular"><?php echo number_format($row['precio'], 0, ",", "."); ?></td>
                    <td class="tabular" align="center"><?php echo $row['unidad'] ?></td>
                </tr>                
        <?php                
            }
        ?>
            </tbody>
        </table>
        <input type="hidden" id="producto" name="producto" value="<?php echo $p_grpprd; ?>" />
        </form>
        <table cellpadding="2" cellspacing="0" width="100%" height="40px" border="0">
            <tr>
              <td width="50%" valign="top" style="text-align: right; padding-top: 30px">
		<form ID="frmConsultar" method="post" name="frmConsultar" ACTION="ListadoPrecios.php">
                    <input type="submit" name="volver" value="Volver" tabindex="3" class="btn" style="width:93px;" />
                    <input type="hidden" id="txtStyle" name="txtStyle" value="<?php echo $cod_sty; ?>" />
                </form>
              </td>
            </tr>
        </table>
                
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
	f1 = document.F1;
</script>


</body>
</html>
