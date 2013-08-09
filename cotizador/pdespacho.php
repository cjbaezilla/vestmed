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
    if ($row["Id_Mod"] == 1 && $row["ID_Opc"] == 5 && $row['CodUsr'] == $UsrId) {
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

if (isset($_POST['fichero']))
    if ($_POST['fichero'] != "") {
        echo $_POST['fichero']."<br>";
	$archivo = $_FILES['documento']['name'];
        echo $archivo."<br>";
	if ($archivo != "") {
            $fileupload = "../".$pathadjuntos."precios.xls";
            echo $fileupload."<br>";
            if (!move_uploaded_file($_FILES['documento']['tmp_name'], $fileupload)){
               echo $_FILES['documento']['tmp_name']."<BR>".$fileupload."<BR>";
               echo "Ocurri&oacute; alg&uacute;n error al subir el fichero. No pudo guardarse.";
               exit(0);
            } 
	}        
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
<script type="text/javascript" src="../js/jquery-1.3.2.min.js"></script>
<script type="text/javascript">
    var $j = jQuery.noConflict();
    new UvumiDropdown('dropdown-scliente');
        
    $j(document).ready
    (
        function()
        {
            $j("form#F2").submit(function(){
                $j.post("../ajax-search.php",{
                        search_type: "getdsp"
                    }, function(xml) {
                        listValDsp(xml);
                });return false;
            });
            //NO SUBMIT TODAVIA $j("form#patternlist-form").submit();
        }
    );
        
    function popwindow(ventana,left,right,ancho,alto){
       popupActive = window.open(ventana,"Anexos",'toolbar=0,location=0,scrollbars=yes,resizable=1,left='+left+',top='+right+',width='+ancho+',height='+alto)
    }

    function mod_valdsp(Cod_Crr, Cod_SvcCrr, Cod_Rgn) {
        popwindow('upd_valdsp.php?crr='+Cod_Crr+'&svc='+Cod_SvcCrr+"&rgn="+Cod_Rgn, 300, 100, 500, 400);
    }
    
    function listValDsp(xml)
    {
        var i = 0;
        options="<table id=\"tblValDsp\" WIDTH=\"95%\" BORDER=\"0\" CELLSPACING=\"0\" CELLPADDING=\"1\" ALIGN=\"right\">\n";
	options+="<tr>\n";
	options+="<td class=\"titulo_tabla\" width=\"15%\" style=\"text-align: left\">Carrier</td>\n";
	options+="<td class=\"titulo_tabla\" width=\"12%\" style=\"text-align: left\">Servicio</td>\n";
	options+="<td class=\"titulo_tabla\" width=\"20%\" style=\"text-align: left\">Regi&oacute;n</td>\n";
	options+="<td class=\"titulo_tabla\" width=\"8%\" style=\"text-align: right\">Hasta<br>1.5 Kg</td>\n";
	options+="<td class=\"titulo_tabla\" width=\"8%\" style=\"text-align: right\">Hasta<br>3.0 Kg</td>\n";
	options+="<td class=\"titulo_tabla\" width=\"8%\" style=\"text-align: right\">Hasta<br>6.0 Kg</td>\n";
	options+="<td class=\"titulo_tabla\" width=\"8%\" style=\"text-align: right\">Hasta<br>10.0 Kg</td>\n";
	options+="<td class=\"titulo_tabla\" width=\"8%\" style=\"text-align: right\">Hasta<br>15.0 Kg</td>\n";
	options+="<td class=\"titulo_tabla\" width=\"12%\" style=\"text-align: right\">Kilo<br>Adicional</td>\n";
	options+="<td class=\"titulo_tabla\" width=\"9%\" style=\"text-align: right\">&nbsp;</td>\n";
	options+="</tr>\n";
        $j("filter",xml).each(
            function(id) {
                filter=$j("filter",xml).get(id);
                options+="<tr>\n";
                
		options+="<td class=\"\" style=\"TEXT-ALIGN: left; \">"+$j("descrr",filter).text()+"</td>\n";
		options+="<td class=\"\" style=\"TEXT-ALIGN: left; \">"+$j("dessvc",filter).text()+"</td>\n";
		options+="<td class=\"\" style=\"TEXT-ALIGN: left; \">"+$j("nomrgn",filter).text()+"</td>\n";
		options+="<td class=\"\" style=\"TEXT-ALIGN: right; \">"+$j("tramo1",filter).text()+"</td>\n";
		options+="<td class=\"\" style=\"TEXT-ALIGN: right; \">"+$j("tramo2",filter).text()+"</td>\n";
		options+="<td class=\"\" style=\"TEXT-ALIGN: right; \">"+$j("tramo3",filter).text()+"</td>\n";
		options+="<td class=\"\" style=\"TEXT-ALIGN: right; \">"+$j("tramo4",filter).text()+"</td>\n";
		options+="<td class=\"\" style=\"TEXT-ALIGN: right; \">"+$j("tramo5",filter).text()+"</td>\n";
		options+="<td class=\"\" style=\"TEXT-ALIGN: right; \">"+$j("adicional",filter).text()+"</td>\n";
		options+="<td class=\"\" style=\"TEXT-ALIGN: right;\"><a href=\"javascript:mod_valdsp("+$j("codcrr",filter).text()+","+$j("codsvc",filter).text()+","+$j("codrgn",filter).text()+")\"><image src=\"../images/folder_feed.png\" alt=\"\" title=\"Modificar valores\" /></a></td>\n";
                                
                options+="</tr>";
                i++;
            }
        );
        options+="</table>";
        $j("#tblValDsp").replaceWith(options);
    }
    
    function ActualizarDsp() {
        $j("form#F2").submit();
    }
    
    function XisArchivo() {
        var frm = document.getElementById('frmImportar');
        if (frm) {
            if (frm.fichero.value == "") {
                alert("Debe indicar la planilla excel que contiene los precios")
                return false;
            }
        }
        return true;
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
                <h2>Valores Despacho</h2>
		<form ID="frmImportar" method="post" name="frmImportar" ACTION="../subirprecios.php" onsubmit="return XisArchivo();" enctype="multipart/form-data">
		<table id="tblValDsp" WIDTH="95%" BORDER="0" CELLSPACING="0" CELLPADDING="1" ALIGN="right">
                <tr>
                    <td align="left">Importar Precios</td>
                    <td colspan="9" align="left">
                        <input style="width: 400px" type="file" name="documento" id="documento" onchange="fichero.value = this.value" />
			<input type="hidden" name="fichero"/>
                        <input type="submit" value="Importar" name="pbImportar" id="pbImportar" />
                    </td>
                </tr>
                <tr><td colspan="10" align="right"><a href="../SaveExcel.php">Bajar a Excel</a></td></tr>
                </table>
                </form>
		<form ID="F2" name="F2" ACTION="">
		<table id="tblValDsp" WIDTH="95%" BORDER="0" CELLSPACING="0" CELLPADDING="1" ALIGN="right">
		<tr>
			<td class="titulo_tabla" width="15%" style="text-align: left">Carrier</td>
                        <td class="titulo_tabla" width="12%" style="text-align: left">Servicio</td>
                        <td class="titulo_tabla" width="20%" style="text-align: left">Regi&oacute;n</td>
			<td class="titulo_tabla" width="8%" style="text-align: right">Hasta<br>1.5 Kg</td>
			<td class="titulo_tabla" width="8%" style="text-align: right">Hasta<br>3.0 Kg</td>
			<td class="titulo_tabla" width="8%" style="text-align: right">Hasta<br>6.0 Kg</td>
			<td class="titulo_tabla" width="8%" style="text-align: right">Hasta<br>10.0 Kg</td>
			<td class="titulo_tabla" width="8%" style="text-align: right">Hasta<br>15.0 Kg</td>
			<td class="titulo_tabla" width="12%" style="text-align: right">Kilo<br>Adicional</td>
			<td class="titulo_tabla" width="9%" style="text-align: right">&nbsp;</td>
		</tr>
		<?php
			$j = 0;
			$iTotPrd = 0;
                        $factor = 1.0 + $IVA;
			$result = mssql_query("vm_prcsvc", $db);
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
                                
				echo "   <td class=\"".$clase1."\" style=\"TEXT-ALIGN: left; \">".$row['Des_Crr']."</td>\n";
				echo "   <td class=\"".$clase2."\" style=\"TEXT-ALIGN: left; \">".$row['Des_SvcCrr']."</td>\n";
				echo "   <td class=\"".$clase2."\" style=\"TEXT-ALIGN: left; \">".utf8_encode($row['Nom_Rgn'])."</td>\n";
				echo "   <td class=\"".$clase2."\" style=\"TEXT-ALIGN: right;\">".number_format($row['tramo1']*$factor,0,',','.')."</td>\n";
				echo "   <td class=\"".$clase2."\" style=\"TEXT-ALIGN: right;\">".number_format($row['tramo2']*$factor,0,',','.')."</td>\n";
				echo "   <td class=\"".$clase2."\" style=\"TEXT-ALIGN: right;\">".number_format($row['tramo3']*$factor,0,',','.')."</td>\n";
				echo "   <td class=\"".$clase2."\" style=\"TEXT-ALIGN: right;\">".number_format($row['tramo4']*$factor,0,',','.')."</td>\n";
				echo "   <td class=\"".$clase2."\" style=\"TEXT-ALIGN: right;\">".number_format($row['tramo5']*$factor,0,',','.')."</td>\n";
				echo "   <td class=\"".$clase2."\" style=\"TEXT-ALIGN: right;\">".number_format($row['adicional']*$factor,0,',','.')."</td>\n";
				echo "   <td class=\"".$clase2."\" style=\"TEXT-ALIGN: right;\"><a href=\"javascript:mod_valdsp(".$row['Cod_Crr'].",".$row['Cod_SvcCrr'].",".$row['Cod_Rgn'].")\"><image src=\"../images/folder_feed.png\" alt=\"\" title=\"Modificar valores\" /></a></td>\n";

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
