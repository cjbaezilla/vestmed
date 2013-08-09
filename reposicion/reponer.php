<?php
//Obtengo los datos de conexion de la base de datos
ini_set('display_errors', '0');
session_start();
include("config.php");

$UsrId = (isset($_SESSION['UsrId'])) ? $UsrId = $_SESSION['UsrId'] : "NO DEFINIDO";
$Perfil = (isset($_SESSION['Perfil'])) ? $Perfil = $_SESSION['Perfil'] : "";
if (isset($_GET['acc'])) {
	if (ok($_GET['acc']) == "delete") {
		$Cod_Prd = ok($_GET['prd']);
		$Fec_Odr = ok($_POST['Fec_Odr']);
		$Num_Odr = intval(ok($_POST['Num_Odr']));
		$result = mssql_query("vm_odr_det_d $Num_Odr, '$Fec_Odr', '$Cod_Prd'", $db);	
	}
}
$Num_Odr = (isset($_GET['cod']) ? intval(ok($_GET['cod'])) : intval(ok($_POST['Num_Odr'])));
$Fec_Odr = (isset($_GET['fec']) ? ok($_GET['fec']) : ok($_POST['Fec_Odr']));

$fechahoy = date("Ymd", time());
$Estado = "1";
$result = mssql_query("vm_odrhdr_s $Num_Odr, '$Fec_Odr'", $db);
if ($row = mssql_fetch_array($result)) $Estado = $row['Est_Odr'];
$Est_Odr = $Estado;
if ($Estado == "3") $Estado = "all";

$titulo = array("1" => "Reposiciones Pendientes","2" => "Ordenes de Reposici&oacute;n","all" => "Ordenes de Reposici&oacute;n","3" => "Ordenes de Reposici&oacute;n");
$link = array("1" => "oreponer.php?est=$Estado","2" => "oreponer.php?est=all","all" => "oreponer.php?est=$Estado","3" => "oreponer.php?est=$Estado");

if (isset($_GET['acc'])) 
	if (ok($_GET['acc']) == "historico") {
		$titulo["all"] = "Hist&oacute;rico";
		$link["all"] = "historicorep.php";
	}
	
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Catalogo - Vestmed Vestuario M&eacute;dico</title>
<link href="../css/layout.css" type="text/css" rel="stylesheet" />
<script type="text/javascript" src="../js/mootools-1.2.1-core-yc.js"></script>
<script type="text/javascript" src="../js/mootools-1.2-more.js"></script>

<link rel="stylesheet" type="text/css" media="all" href="../Include/calendar-green.css" title="win2k-cold-1" />
<script type="text/javascript" src="../Include/calendar.js"></script>
<script type="text/javascript" src="../Include/calendar-es.js"></script>
<script type="text/javascript" src="../Include/calendar-setup.js"></script>
<script language="JavaScript" src="../Include/ValidarDataInput.js"></script>

<LINK href="../Include/estilos.css" type="text/css" rel="stylesheet" />
<script type="text/javascript" src="../js/jquery-1.3.2.min.js"></script>
<script type="text/javascript">
	var $j = jQuery.noConflict();
    $j(document).ready
	(
		function()
		{
	        $j("form#searchSty").submit(function(){
				$j.post("../ajax-search-pat.php",{
					search_type: "pat",
					param_sty: $j("#txtStyle").val()
				}, function(xml) {
					listLinPat(xml,1);
				});return false;
		    });
			
	        $j("form#searchMca").submit(function(){
				$j.post("../ajax-search-pat.php",{
					search_type: "mca",
					param_sty: $j("#txtStyle").val(),
					param_mca: $j("#cmbMca").val()
				}, function(xml) {
					listLinPat(xml,2);
				});return false;
		    });
			
	        $j("form#searchColor").submit(function(){
				$j.post("../ajax-search-pat.php",{
					search_type: "sze",
					param_sty: $j("#txtStyle").val(),
					param_pat: $j("#cmbColor").val()
				}, function(xml) {
					listLinSze(xml);
				});return false;
		    });

	        $j("form#searchSze").submit(function(){
				$j.post("../ajax-search-pat.php",{
					search_type: "prd",
					param_sty: $j("#txtStyle").val(),
					param_pat: $j("#cmbColor").val(),
					param_sze: $j("#cmbSize").val()
				}, function(xml) {
					listLinPrd(xml);
				});return false;
		    });
	        //NO SUBMIT TODAVIA $j("form#patternlist-form").submit();
	    }
		
	);
    //TODO: No se esta realizando el post, probablemente debido a que falta el evento submit.
	
    function blurEstilo(obj)
    {
		f3.dfCod_Sty.value = obj.value;
        $j("form#searchSty").submit();
    }

    function blurMca(obj)
    {
		f3.dfCod_Mca.value = obj.value;
		$j("form#searchMca").submit();
    }
	
    function blurTalla(obj)
    {
		f3.dfCod_Pat.value = obj.value;
        $j("form#searchColor").submit();
    }

    function blurSize(obj)
    {
		f3.dfCod_Sze.value = obj.value;
		//$j("form#searchSze").submit();
    }

    function blurQty(obj)
    {
		f3.dfCod_Qty.value = obj.value;
    }
	
    function listLinPat(xml,caso)
    {
		var i=0;
		if (caso == 1) {
			optionsmca="<select id=\"cmbMca\" name=\"cmbMca\" class=\"textfield\" onChange=\"blurMca(this)\" tabindex=\"15\">\n";
			$j("detalle",xml).each(
				function(id) {
					filter=$j("detalle",xml).get(id);
					if (i == 0) {
						f3.dfCod_Mca.value = $j("value",filter).text();
						optionsmca+= "<option value=\""+$j("value",filter).text()+"\" selected>"+$j("value",filter).text()+"</option>\n";
					}
					else
						optionsmca+= "<option value=\""+$j("value",filter).text()+"\">"+$j("value",filter).text()+"</option>\n";
					i++;
				}
			);
			optionsmca+="</select>";
			$j("#cmbMca").replaceWith(optionsmca);
		}
		
        options="<select id=\"cmbColor\" name=\"cmbColor\" class=\"textfield\" onChange=\"blurTalla(this)\" tabindex=\"16\">\n";
        options+="<option selected value=\"_NONE\">NONE</option>\n";
        $j("filter",xml).each(
			function(id) {
	            filter=$j("filter",xml).get(id);
	            options+= "<option value=\""+$j("code",filter).text()+"\">"+$j("value",filter).text()+"</option>\n";
	        }
		);
        options+="</select>";
        $j("#cmbColor").replaceWith(options);
    }
	
    function listLinSze(xml)
    {
        options="<select id=\"cmbSize\" name=\"cmbSize\" class=\"textfield\" onChange=\"blurSize(this)\" tabindex=\"17\">\n";
        options+="<option selected value=\"_NONE\">NONE</option>\n";
        $j("filter",xml).each(
			function(id) {
	            filter=$j("filter",xml).get(id);
	            options+= "<option value=\""+$j("code",filter).text()+"\">"+$j("value",filter).text()+"</option>\n";
	        }
		);
        options+="</select>";
        $j("#cmbSize").replaceWith(options);
    }
	
	function DelPrd (codprd) {
		if (confirm("Favor confirmar la eliminacion del producto\nde la Orden de Reposicion")) {
			f4.action = "reponer.php?acc=delete&prd="+codprd;
			f4.submit();
		}
	}
	
	function compra() {
		//alert("compra");
		if (checkDataFichaOdr(f3,2)) {
			f3.action = "reponer_ins.php?flag=1";
			f3.submit();
		}
	}
</script>
</head>

<body>
<div id="body">
	<div id="header"></div>
    <ul id="usuario_registro">
		<?php 	echo display_usr($UsrId, $Perfil, $db); ?>
    </ul>
    <div id="work">
<?php formar_topbox ("100%%","center"); ?>
<P align="left"><strong><a href="<?php echo $link[$Estado]; ?>"><?php echo $titulo[$Estado]; ?></a></strong> / Detalle</P>
<P align="center">
<TABLE WIDTH="90%" BORDER="0" CELLSPACING="0" CELLPADDING="1" ALIGN="center">
<tr><td>
	<H2 style="TEXT-ALIGN: center">Orden de Reposici&oacute;n <?php echo $Num_Odr; ?> de Fecha <?php echo fechafmt($Fec_Odr); ?></H2>
	<TABLE WIDTH="95%" BORDER="0" CELLSPACING="0" CELLPADDING="1" ALIGN="right">
	<form ID="F4" AUTOCOMPLETE="off" method="POST" name="F4" action="<?php if ($Estado == 1) echo "ocompra_ins.php"; else echo "oreponer.php?est=2"; ?>">
	<?php
		$j = 0;
		$Mca = "";
		$iTotPrd = 0;
		$tip_doc = 1;
		$afecha = split('/', $fecha);
		$fechain = $afecha[2].$afecha[1].$afecha[0];
		if ($Est_Odr == "3") $numcol = 7; else $numcol = 6;
		
		$xis = 0;
		$result = mssql_query("vm_odr_det_s $Num_Odr, '$Fec_Odr'", $db);
		while ($row = mssql_fetch_array($result)) {
			if ($Perfil == 2 || ($Perfil == 1 && $row['Ctd_Prd'] > 0)) {
				if ($Mca != $row['Cod_Mca']) {
					if ($Mca != "") echo "<TR><TD colspan=\"$numcol\" style=\"PADDING-TOP: 10px; TEXT-ALIGN: right\" class=\"label_top\">&nbsp;</TD></TR>\n";
					$Mca = $row['Cod_Mca'];
					?>
					<TR>
						<TD class="titulo_tabla" width="100%" colspan="<?php echo $numcol; ?>" align="middle">Marca: <?php echo $Mca; ?></TD>
					</TR>
					<TR>
						<TD class="titulo_tabla" width="10%" align="middle">Style</TD>
						<TD class="titulo_tabla" width="10%" align="middle">Patr&oacute;n</TD>
						<TD class="titulo_tabla" width="10%" align="middle">Talla</TD>
						<TD class="titulo_tabla" width="10%" align="middle">Cantidad<BR>Solicitada</TD>
						<TD class="titulo_tabla" width="15%" align="middle">Reservado</TD>
						<?php if ($Est_Odr == "3") { ?>
						<TD class="titulo_tabla" width="15%" align="middle">Cantidad<BR>Repuesta</TD>
						<TD class="titulo_tabla" width="30%" align="middle">&nbsp;</TD>
						<?php } else { ?>
						<TD class="titulo_tabla" width="45%" align="middle">&nbsp;</TD>
						<?php } ?>
					</TR>
				<?
				}
				echo "<TR>\n";
				if ($j == 0) {
					$clase1 = "label_left_right";
					$clase2 = "dato3";
				}
				else {
					$clase1 = "label333";
					$clase2 = "dato33";
				}
				echo "   <TD class=\"".$clase1."\" style=\"TEXT-ALIGN: center\">".$row['Cod_Sty']."</TD>\n";
				echo "   <TD class=\"".$clase2."\" style=\"TEXT-ALIGN: center\">".$row['Key_Pat']."</TD>\n";
				echo "   <TD class=\"".$clase2."\" style=\"TEXT-ALIGN: center\">".$row['Val_Sze']."</TD>\n";
				echo "   <TD class=\"".$clase2."\" style=\"TEXT-ALIGN: center\">".$row['Ctd_Prd']."</TD>\n";
				echo "   <TD class=\"".$clase2."\" style=\"TEXT-ALIGN: center\">".($row['Ctd_Rsv'] == 0 ? "NO" : $row['Ctd_Rsv'])."</TD>\n";
				if ($Est_Odr == "3")
					echo "   <TD class=\"".$clase2."\" style=\"TEXT-ALIGN: center\">".($row['Ctd_Rep'] == 0 ? "Sin Existencia" : $row['Ctd_Rep'])."</TD>\n";
				
				if ($Est_Odr == "1")
					echo "   <TD class=\"".$clase2."\" style=\"TEXT-ALIGN: left\"><a href=\"grilla.php?prd=".$row['Cod_Prd']."&fec=$Fec_Odr&cod=$Num_Odr\">Edit</a> / <a href=\"javascript:DelPrd('".$row['Cod_Prd']."')\">Delete</a></TD>\n";
				else
					echo "   <TD class=\"".$clase2."\" style=\"TEXT-ALIGN: left\">&nbsp;</TD>\n";
				echo "</TR>\n";
				$j = 1 - $j;
				$iTotPrd++;
			}
		}
		mssql_free_result($result);
	?>
	<TR>
		<TD colspan="<?php echo $numcol; ?>" style="PADDING-TOP: 10px; TEXT-ALIGN: right" class="label_top">
		<?php if ($Estado == "1") { ?>
			<?php if ($iTotPrd > 0) { ?>
			<input type="submit" name="SendToCompra" value="Enviar Orden" tabindex="20" class="button2" style="width:153px;" />
			<?php } ?>
			<input name="Fec_Odr" type="hidden" id="Fec_Odr" value="<?php echo $Fec_Odr ?>" />
			<input name="Num_Odr" type="hidden" id="Num_Odr" value="<?php echo $Num_Odr ?>" />
		<?php } ?>
		</TD>
	</TR>
	</form>
	<?php if ($Est_Odr == "1") { ?>
	<TR>
		<TD colspan="6">
		<fieldset>
		<legend>
			Agregar Nuevos Items a la Orden :&nbsp;
		</legend>
          <table cellpadding="2" cellspacing="0" width="100%px" height="40px" border="0">
            <tr>
              <td style="width: 50px; PADDING-BOTTOM: 20px;" class="label">
                Style:
              </td>
              <form id="searchSty">
			  <td style="width: 100px; PADDING-BOTTOM: 20px;">
				<input name="txtStyle" id="txtStyle" maxlength="30" size="10" class="textfield" onblur="blurEstilo(this)" tabindex="14" /><br />
			  </td>
			  </form>
              <td style="width: 50px; PADDING-BOTTOM: 20px;" class="label">
                Marca:
              </td>
			  <form id="searchMca">
              <td style="width: 100px; PADDING-BOTTOM: 20px;">
				<!--input name="txtColor" type="text" id="txtColor" maxlength="30" size="10" class="textfield" onfocus="javascript:clearSuggest('styleSuggest');" onkeyup="javascript:getColors(document.aspnetForm.ctl00_ContentPlaceHolder1_txtStyle.value,this.value)" tabindex="16" /><br /-->
				<select id="cmbMca" name="cmbMca" class="textfield" onChange="blurMca(this)" tabindex="15">
					<option selected value="_NONE">NONE</option>
				</select>
              </td>
			  </form>
              <td style="width: 50px; PADDING-BOTTOM: 20px;" class="label">
                Color:
              </td>
			  <form id="searchColor">
              <td style="width: 100px; PADDING-BOTTOM: 20px;">
				<!--input name="txtColor" type="text" id="txtColor" maxlength="30" size="10" class="textfield" onfocus="javascript:clearSuggest('styleSuggest');" onkeyup="javascript:getColors(document.aspnetForm.ctl00_ContentPlaceHolder1_txtStyle.value,this.value)" tabindex="16" /><br /-->
				<select id="cmbColor" name="cmbColor" class="textfield" onChange="blurTalla(this)" tabindex="16">
					<option selected value="_NONE">NONE</option>
				</select>
              </td>
			  </form>
              <td class="label" style="width: 50px; PADDING-BOTTOM: 20px;">
                Size*:
              </td>
			  <form id="searchSze">
              <td style="width: 100px; PADDING-BOTTOM: 20px;">
				<select id="cmbSize" name="cmbSize" class="textfield" onChange="blurSize(this)" tabindex="17">
					<option selected value="_NONE">NONE</option>
				</select>
              </td>
			  </form>
              <td class="label" style="width: 60px; PADDING-BOTTOM: 20px;">
                Quantity*:
              </td>
              <td style="width: 75px; PADDING-BOTTOM: 20px;">
                <input name="txtQty" type="text" id="txtQty" size="5" value="" class="textfield" tabindex="18" onblur="blurQty(this)" />
              </td>
            </tr>
		    <form ID="F3" AUTOCOMPLETE="off" method="POST" name="F3" ACTION="reponer_ins.php" onsubmit="return checkDataFichaOdr(this,2)">
            <tr>
              <td colspan="6" align="center" class="labelBig" style="TEXT-ALIGN: center">
				* Opcional(dejar vac&iacute;o para ver la grilla Style Color)
              </td>
			  <td colspan="4" align="right">
                <input type="submit" name="cmdOrderDetail" value="Agregar Items" tabindex="20" class="button2" style="width:93px;" />
                <input name="dfCod_Mca" type="hidden" id="dfCod_Mca" value="" />
                <input name="dfCod_Sty" type="hidden" id="dfCod_Sty" value="" />
                <input name="dfCod_Pat" type="hidden" id="dfCod_Pat" value="" />
                <input name="dfCod_Sze" type="hidden" id="dfCod_Sze" value="" />
                <input name="dfCod_Qty" type="hidden" id="dfCod_Qty" value="" />
                <input name="dfFec_Mov" type="hidden" id="dfFec_Mov" value="<?php echo $Fec_Odr ?>" />
				<input name="dfNum_Odr" type="hidden" id="dfNum_Odr" value="<?php echo $Num_Odr ?>" />
			  </td>
            </tr>
			</form>
          </table>
		</fieldset>
		</TD>
	</TR>
	<?php } ?>
	</TABLE>
</td></tr>
</table>
</p>
<?php formar_bottombox (); ?>
    </div>
    <div id="footer"></div>
</div>
<script language="javascript">
	var f1;	
	var f3;	
	var f4;	
	f1 = document.F1;
	f3 = document.F3;
	f4 = document.F4;
	
</script>
</body>
</html>
