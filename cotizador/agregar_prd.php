<?php 
ini_set('display_errors', '0');
session_start();
include("global_cot.php");

$Cod_Cot = isset($_GET['cot']) ? ok($_GET['cot']) : 0;
$Cod_Sty = isset($_POST['txtStyle']) ? ok($_POST['txtStyle']) : ok($_GET['sty']);
$IdGrp   = isset($_GET['idgrp']) ? ok($_GET['idgrp']) : 0;
$Cod_Pat = isset($_GET['pat']) ? ok($_GET['pat']) : "";
$accion  = isset($_GET['accion']) ? ok($_GET['accion']) : "";

if ($accion == 'I' or $accion == 'U') {
	$Cod_Pat    = $_POST['dfCod_Pat'];
	$Cod_Dsg    = $_POST['dfCod_Dsg'];
	$Cod_Sty	= $_POST['dfCod_Sty'];
	$IdGrp		= $_POST['dfCod_GrpPrd'];
	$Tallas	    = split(";", $_POST['dfTallas']);
	foreach ($Tallas as $key => $value) {
		$Token   = split("=", $value);
		$Token2  = split("-", $Token[0]);
		$Cod_Sze = intval($Token2[1]);
		$Val_Ctd = intval($Token[1]);
                
                $prc_prd = 0.0;
                $result = mssql_query ("vm_s_dsg '$Cod_Dsg'", $db) or die ("No pudo obtener datos de DSG");
                if (($row = mssql_fetch_array($result))) {
                    $cod_mca = $row["Cod_Mca"];
                    $cod_sty = $row["Cod_Sty"];

                    $result = mssql_query ("vm_s_pat '$Cod_Dsg', '$Cod_Pat'", $db) or die ("No pudo obtener datos de PAT");
                    if (($row = mssql_fetch_array($result))) {
                        $cod_grppat = $row["Cod_GrpPat"];
                        $hoy = date('Ymd');
                        $result = mssql_query ("BDFlexline.dbo.sp_stock '$cod_mca', '$cod_sty', '$cod_grppat', '001', '$hoy'", $db) 
                                       or die ("No pudo obtener datos del STOCK<br>BDFlexline.flexline.sp_stock '$cod_mca', '$cod_sty', '$cod_grppat', '001', '$hoy'");
                        //$result = mssql_query ("sp_stock '$cod_mca', '$cod_sty', '$cod_grppat', '001', '$hoy'", $db) 
                        //               or die ("No pudo obtener datos del STOCK");

                        if (($row = mssql_fetch_array($result))) $prc_prd = $row["precio"];
                    }
                }
                
                
                //echo "vm_i_Res_CotPrd $Cod_Cot, '$IdGrp', $Cod_Dsg, $Cod_Pat, $Cod_Sze, $Val_Ctd, $prc_prd"."<BR>";
		$result  = mssql_query("vm_i_Res_CotPrd $Cod_Cot, '$IdGrp', $Cod_Dsg, $Cod_Pat, $Cod_Sze, $Val_Ctd, $prc_prd", $db);
	}
	$Cod_Pat = "";
}
$Cod_Dsg = "";

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<HTML>
<HEAD>
<title>Agregar Productos Cotizacion <?php echo $Cod_Cot; ?></title>
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
<script language="JavaScript" src="../Include/ValidarDataInput.js"></script>
<script language="JavaScript" src="../Include/SoloNumeros.js"></script>
<script language="JavaScript" src="../Include/validarRut.js"></script>
<LINK href="../Include/estilos.css" type="text/css" rel="stylesheet" />
<script type="text/javascript" src="../js/jquery-1.3.2.min.js"></script>
<script type="text/javascript">
        var $j = jQuery.noConflict();
        $j(document).ready(function(){
            $j("form#patternlist-form").submit(function(){
                $j.post("../ajax-pattern.php",{
                            id_dsg: $j("#dfCod_Dsg").val(),
                            id_pat: $j("#dfCod_Pat").val(),
                            key_pat: $j("#dfKey_Pat").val(),
                            id_grpprd: $j("#dfCod_GrpPrd").val(),
                            cod_cot: <?php echo $Cod_Cot ?>
                        }, function(xml) {
                    listPatterns(xml);
                });
                return false;
            });
        });

        function selectPattern(par_pat, par_key)
        {
			//alert("selectPattern");
            $j("#dfCod_Pat").val(par_pat);
            $j("#dfKey_Pat").val(par_key);
            $j("form#patternlist-form").submit();
        }
		
        function listPatterns(xml) {
			var i;
			var columnas;
			
            $j("selpat",xml).each(function(id) {
                selpat = $j("selpat",xml).get(id);
				$j("#img-estampado-sel").replaceWith("<li id=\"img-estampado-sel\"><img src=\"../imagedisplay.php?name=img_pattern&filter="+$j("code",selpat).text()+"\" height=\"80px\" width=\"80px\" /></li>");
                $j("#descrip-est").replaceWith("<div id=\"descrip-est\">"+$j("desc",selpat).text().replace("_BR_","<br />")+"</div>");
            });
			
			columnas = 0;
			f2.dfTallas.value = "";
			newtable = "<table id=\"tblSze\" name=\"tblSze\" cellpadding=\"2\" cellspacing=\"0\" width=\"100%\" height=\"40px\" border=\"0\">\n";
            $j("size",xml).each(function(id) {
				if (columnas == 0) newtable+="<TR>\n";
				if (columnas == 10) {
					newtable+="</TR><TR>\n";
					columnas = 0;
				}
				columnas++;
                selsze = $j("size",xml).get(id);
				newtable+="<td width=\"30px\" align=\"center\" class=\"dato\" style=\"text-align: center\">\n";
				newtable+=$j("val",selsze).text()+"<BR>";
				newtable+="<input name=\"txtQty-"+$j("code",selsze).text()+"\" id=\"txtQty-"+$j("code",selsze).text()+"\" maxlength=\"5\" size=\"5\" class=\"textfield\" value=\""+$j("ctd",selsze).text()+"\" onblur=\"blurQty(this)\" />\n";
				newtable+="</td>\n";
				if (f2.dfTallas.value == "") f2.dfTallas.value = "txtQty-"+$j("code",selsze).text()+"="+$j("ctd",selsze).text();
				else f2.dfTallas.value += ";"+"txtQty-"+$j("code",selsze).text()+"="+$j("ctd",selsze).text();
            });
			for (i=columnas; i<10; i++) newtable+="<td width=\"30px\">&nbsp;</td>\n";
			newtable+="</table>";
			$j("#tblSze").replaceWith(newtable);
        }
		
		function blurQty(obj) {
			var i;
			var j;
			var largo = 0;

			aToken = f2.dfTallas.value.split(";");
			largo = aToken.length;
			
			f2.dfTallas.value = "";
			for (i=0; i<largo; i++) {
				aToken2 = aToken[i].split("=");
				if (aToken2[0] == obj.name) {
					aToken2[1] = obj.value;
					aToken[i] = aToken2[0]+"="+aToken2[1];
				}
				if (f2.dfTallas.value == "") f2.dfTallas.value = aToken[i];
				else f2.dfTallas.value += ";"+aToken[i];
			}
		}
</script>
<script type="text/javascript">
	new UvumiDropdown('dropdown-scliente');
</script>
<script type="text/javascript">
function MarcarTodosPat(obj) {
	for (i=0; i<f2.elements.length; i++) 
   	  if (f2.elements[i].name == "patrones[]")
   		 f2.elements[i].checked = obj.checked;
}

function MarcarTodosSze(obj) {
	for (i=0; i<f2.elements.length; i++) 
   	  if (f2.elements[i].name == "tallas[]")
   		 f2.elements[i].checked = obj.checked;
}

function cambiarstyle(idgrp) {
	f4.action = "agregar_prd.php?cot=<?php echo $Cod_Cot; ?>&idgrp="+idgrp;
	f4.submit();
}
</script>
</HEAD>
<BODY bgcolor="#ffffff" text="#000000" link="#0000ff" vlink="#800080" alink="#ff0000">
<?php formar_topbox ("100%%","center"); ?>
<P ALIGN="center">
<TABLE WIDTH="100%" BORDER="0" CELLSPACING="0" CELLPADDING="1">
<TR>
	<TD class="dato" colspan="2">
		<fieldset class="label_left_right_top_bottom">
		<legend>Filtro</legend>
			  <form id="searchSty" name="searchSty" method="POST" action="agregar_prd.php?cot=<?php echo $Cod_Cot; ?>">
			  <table cellpadding="2" cellspacing="0" width="100%" height="40px" border="0">
				<tr>
				  <td width="50%" valign="top" style="text-align: center" class="label">
						<b>Style: </b><input name="txtStyle" id="txtStyle" maxlength="30" size="10" class="textfield" value="<?php echo $Cod_Sty; ?>" tabindex="1" />
				  </td>
				  <td width="50%" valign="top" style="text-align: center" class="label">
					<?php if ($Cod_Pat == "") { ?>
					<input type="submit" name="consultar" value="Consultar" tabindex="2" class="button2" style="width:93px;" />
					<?php } else { ?>
					&nbsp;
					<?php } ?>
				  </td>
				</tr>
			  </table>
			  </form>
		</fieldset>
	</TD>
</TR>
<TR>
	<TD class="dato" width="50%" valign="top">
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
						<img src="<?php echo printimg_addr("img1_grupo",$grp_id) ?>" width="144" title="<?php echo $titlelarge; ?>" class="cursor image-producto" onclick="cambiarstyle('<?php echo $grp_id ?>')" />
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
	</TD>
	<TD class="dato" width="50%" valign="top">
		<fieldset class="label_left_right_top_bottom">
		<legend>Color Seleccionado</legend>
		<?php
			$result = mssql_query("vm_strcol_prod '$grp_sel'", $db);
			$row = mssql_fetch_array($result);
		?>
			<ul class="estampado-sel clearfix">
				<li id="img-estampado-sel">
					<?php if ($Cod_Pat != "")  { ?>
					<img src="<?php echo printimg_addr("img_pattern",$Cod_Pat) ?>" height="80px" width="80px" />
					<?php } else  { ?>
					<img src="../images/sin_patron.gif" height="80px" width="80px" />
					<?php } ?>
				</li>
				<li id="des-estampado-sel">
					<?php if ($Cod_Pat != "")  { 
                                                $result = mssql_query("vm_pat_s '".$Cod_Pat."'", $db);
                                                $row = mssql_fetch_array($result);
					?>
                                        <div id="descrip-est"><?php echo $row["Key_Pat"] ?><br /><?php echo $row["Des_Pat"] ?></div>
					<?php } else  { ?>
					<div id="descrip-est">Sin patr&oacute;n seleccionado</div>
					<?php } ?>
				</li>
			</ul>
		<?php
			mssql_free_result($result); 
		?>
		</fieldset>
		<fieldset class="label_left_right_top_bottom">
		<legend>Selecci&oacute;n de Tallas</legend>
			<table id="tblSze" name="tblSze" cellpadding="2" cellspacing="0" width="100%" height="40px" border="0">
			<?php
				  $numcolores = 0;
				  $i = 0;
				  $Tallas = "";
				  if ($Cod_Cot > 0)	$result = mssql_query("vm_strinv_szepat_cot '$Cod_Pat', '$Cod_Dsg', $Cod_Cot, '$grp_id'", $db);
				  else $result = mssql_query("vm_strinv_szepat '$Cod_Pat', '$Cod_Dsg', '$grp_id'", $db);
				  While ($row = mssql_fetch_array($result)) {			  
					if ($i == 0) echo "<TR>\n";
					if ($i == 10) {
						echo "</TR><TR>\n";
						$i = 0;
					}
					if ($Cod_Cot > 0) {
						if ($i == 0) $Tallas = "txtQty-".$row['Cod_Sze']."=".$row['Val_Ctd'];
						else $Tallas.=";txtQty-".$row['Cod_Sze']."=".$row['Val_Ctd'];
					}
					$i++; $numcolores++;
			?>
					<td width="30px" align="center" class="dato" style="text-align: center"><?php echo $row['Val_Sze']; ?><BR>
					<input name="txtQty-<?php echo $row['Cod_Sze'] ?>" id="txtQty-<?php echo $row['Cod_Sze'] ?>" maxlength="5" size="5" class="textfield" value="<?php if($Cod_Cot > 0) echo $row['Val_Ctd']; ?>" onblur="blurQty(this)" />
					</td>
			<?php
				  }
				  for ($j = $i; $j < 10; ++$j) echo "<td width=\"30px\">&nbsp;</td>\n";
			?>
			</TR>
			</table>
		</fieldset>
	</TD>
</TR>
<?php if ($Cod_Pat == "") { ?>
<TR>
    <TD class="dato" colspan="2" style="PADDING-TOP: 10px">
	<fieldset class="label_left_right_top_bottom">
	<legend>Selecci&oacute;n de Colores para <?php echo $title_sel ?>&nbsp;</legend>
        <form id="patternlist-form">
	    <table cellpadding="2" cellspacing="0" width="100%px">
		<?php
			  $numcolores = 0;
			  $i = 0;
			  $result = mssql_query("vm_strcol_prod '$grp_sel'", $db);
			  While ($row = mssql_fetch_array($result)) {				
				if ($numcolores == 0) $pat_cod = $row["Cod_Pat"];
				if ($i == 0) echo "<TR>\n";
				if ($i == 9) {
                                    echo "</TR><TR>\n";
                                    $i = 0;
				}
				$i++; $numcolores++;
		?>
                <td width="65px" align="center">
				<table width="100%" border="0" CELLSPACING="0" CELLPADDING="0">
				<tr>
					<td align="center"><img src="<?php echo printimg_addr("img_pattern",$row["Cod_Pat"]) ?>" height="60px" width="60px" title="<?php echo $row['Des_Pat'] ?>" onclick="selectPattern('<?php echo $row["Cod_Pat"] ?>', '<?php echo $row["Key_Pat"]."_BR_".$row["Des_Pat"] ?>')" /></td>
				</tr>
				<tr>
					<td class="dato" style="text-align: center"><?php echo $row['Key_Pat']; ?></td>
				</tr>
				</table>
			</td>
		<?php
			}
			for ($j = $i; $j < 9; ++$j) echo "<td width=\"65px\">&nbsp;</td>\n";
		?>
		</TR>
	    </table>
		</form>
	</fieldset>
    </TD>
</TR>
<?php } ?>
<form id="F2" name="F2" method="POST" action="agregar_prd.php?cot=<?php echo $Cod_Cot; ?>&accion=<?php echo ($Cod_Pat == "") ? "I" : "U"; ?>">
<TR>
    <TD colspan="2" class="dato" style="PADDING-TOP: 10px">
    </TD>
</TR>
<TR>
	<TD colspan="2" class="datoc" style="TEXT-ALIGN: right; PADDING-TOP: 10px">
		<input type="hidden" id="dfCod_GrpPrd" name="dfCod_GrpPrd" value="<?php echo $grp_sel; ?>">
		<input type="hidden" id="dfCod_Sty" name="dfCod_Sty" value="<?php echo $Cod_Sty; ?>">
		<input type="hidden" id="dfCod_Dsg" name="dfCod_Dsg" value="<?php echo $Cod_Dsg; ?>">
		<input type="hidden" id="dfCod_Pat" name="dfCod_Pat" value="<?php echo $Cod_Pat; ?>">
		<input type="hidden" id="dfKey_Pat" name="dfKey_Pat" value="">
		<input type="hidden" id="dfTallas" name="dfTallas" value="<?php echo $Tallas; ?>">
		<input type="submit" name="cmdOrderDetail" value="<?php echo ($Cod_Pat == "") ? "Agregar Items" : "Actualizar"; ?>" tabindex="20" class="button2" style="width:93px;" />&nbsp;
		<input type="button" name="Cerrar" value=" Cerrar  " class="button2" onclick="javascript:window.close()">
	</TD>
</TR>
</form>
</TABLE>
<?php formar_bottombox (); ?>

<script language="javascript">
	var f1;	
	var f2;
	var f3;
	var f4;
	
	f1 = document.F1;	
	f2 = document.F2;
	f3 = document.F3;
	f4 = document.searchSty;
	
<?php
  if (isset($_GET['accion'])) {
     echo "	parent.opener.document.F2.action=\"nueva_cot.php?cot=$Cod_Cot\";\n";
     echo "	parent.opener.document.F2.submit();\n";
	 if ($accion == 'U') echo "	window.close();\n";
  }
 ?>
</script>
</BODY>
</HTML>

<?php
     mssql_close ($link);
?>
