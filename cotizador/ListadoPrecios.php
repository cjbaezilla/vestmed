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

$Cod_Sty = isset($_POST['txtStyle']) ? $_POST['txtStyle'] : "";
$Cod_Pat = "";
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
    
    function limpia() {
        $j("#txtStyle").val('');
        $j("form#searchSty").submit();
    }
    
    function CheckStyle() {
        if ($j("#txtStyle").val() == "") {
            alert('Debe indicar un Style');
            return false;
        }
        $j("form#searchSty").submit();
    }
    
    function MostrarPrecios(id_grp) {
        $j("#producto").val(id_grp);
        $j("form#frmConsultar").submit();
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
                <h2>Listado de Precios</h2>
		<fieldset class="label_left_right_top_bottom">
		<legend>Filtro</legend>
			  <form id="searchSty" name="searchSty" method="POST" action="ListadoPrecios.php">
			  <table cellpadding="2" cellspacing="0" width="100%" height="40px" border="0">
				<tr>
				  <td width="50%" valign="top" style="text-align: center" class="label">
						<b>Style: </b><input name="txtStyle" id="txtStyle" maxlength="30" size="10" class="textfield_m" value="<?php echo $Cod_Sty; ?>" tabindex="1" />
				  </td>
				  <td width="50%" valign="top" style="text-align: center" class="label">
					<input type="button" name="consultar" value="Consultar" tabindex="2" onclick="CheckStyle()" class="btn" style="width:93px;" />
					<input type="button" name="limpiar" value="Limpiar" tabindex="3" onclick="limpia()" class="btn" style="width:93px;" />
				  </td>
				</tr>
			  </table>
			  </form>
		</fieldset>
                <div id="StyleSeleccionados">
		<fieldset class="label_left_right_top_bottom">
		<legend>Productos Seleccionados</legend>
                    <div id="productos-catalogo" class="clearfix">
			  <table cellpadding="1" cellspacing="1" width="100%" border="0" align="center">
				<tr>
				  <?php 
					$columnas = 0;
					$grp_id = "";
					$grp_first = "";
					if ($Cod_Sty != "") {
						$result = mssql_query("vm_strinv_prodcat '', '', '', '', '', '$Cod_Sty'",$db);
						While ($row = mssql_fetch_array($result)) {
							$columnas++;
							$largotitle  = 17;
							$grp_id      = $row['grpprd_id'];
							$Cod_Dsg	 = $row['dsg_iddsg'];
							$titlelarge  = $row["grpprd_title"];
							$cod_mca	 = $row["dsg_marca"];
							$title  	 = $titlelarge;
							if (strlen($titlelarge) > $largotitle) {
								$title=substr($titlelarge,0,$largotitle);
								$title.="...";
							}
							if ($columnas == 1 Or $grp_id == $IdGrp) {
								$grp_sel = $grp_id;
								$title_sel = $titlelarge;
							}
                                  ?>
				  <td align="center" class="fila">
					<div style="height:260px;">
						<div style="border: 1px solid rgb(233, 233, 233); margin: 10px auto 0 auto; width: 140px; height:220px; background:#FFF; overflow:hidden;">
						<img src="<?php echo printimg_addr("img1_grupo",$grp_id) ?>" width="144" title="<?php echo $titlelarge; ?>" class="cursor image-producto" onclick="MostrarPrecios('<?php echo $grp_id ?>')" />
						</div>
                                            <div class="descripcion-producto"><?php echo $title ?></div>
                                            <div class="descripcion-producto"><?php echo $cod_mca ?></div>
					</div>
				  </td>	 
				  <?php
						}
					}
					if ($columnas > 0) 
					  for ($i = 0; $i < (2-$columnas); $i++) {
				  ?>
					  <td align="center" class="fila">
						<div style="height:230px;">
							<div style="border: 1px solid rgb(233, 233, 233); margin: 10px auto 0 auto; width: 144px; height:220px; background:#FFF; overflow:hidden;">
							</div>
							<div class="descripcion-producto"></div>
						</div>
					  </td>	 
				  <?php } 
				    else { ?>
				  <td>&nbsp;</td>
				  <?php } ?>
				</tr>
			  </table>
                    </div>
		</fieldset>
                </div>
                
		<form ID="frmConsultar" method="post" name="frmConsultar" ACTION="ListadoPreciosDet.php">
                    <input type="hidden" id="producto" name="producto" value="" />
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
	f1 = document.F1;
</script>


</body>
</html>
