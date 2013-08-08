<?php
//Obtengo los datos de conexion de la base de datos
ini_set('display_errors', '0');
session_start();
include("config.php");

$UsrId = (isset($_SESSION['UsrId'])) ? $UsrId = $_SESSION['UsrId'] : "";
$Perfil = (isset($_SESSION['Perfil'])) ? $Perfil = $_SESSION['Perfil'] : "";
$Num_Odc = (isset($_GET['cod']) ? intval(ok($_GET['cod'])) : intval(ok($_POST['Num_Odc'])));
$Fec_Odc = (isset($_GET['fec']) ? ok($_GET['fec']) : ok($_POST['Fec_Odc']));
if (isset($_GET['acc'])) {
	if (ok($_GET['acc']) == "delete") {
		$Cod_Prd = ok($_GET['prd']);
		$result = mssql_query("vm_odc_det_d $Num_Odc, '$Fec_Odc', '$Cod_Prd'", $db);	
	}
}

$Est_Odc = "1";
$result = mssql_query("vm_odchdr_s $Num_Odc, '$Fec_Odc'", $db);
if ($row = mssql_fetch_array($result)) $Est_Odc = $row['Est_Odc'];

$titulo = array("1" => "Ordenes de Compra","2" => "Hist&oacute;rico");
$link = array("1" => "ocompra.php","2" => "historicorep.php");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Catalogo - Vestmed Vestuario M&eacute;dico</title>
<link href="css/layout.css" type="text/css" rel="stylesheet" />
<script type="text/javascript" src="js/mootools-1.2.1-core-yc.js"></script>
<script type="text/javascript" src="js/mootools-1.2-more.js"></script>
<script language="JavaScript" src="Include/ValidarDataInput.js"></script>

<LINK href="Include/estilos.css" type="text/css" rel="stylesheet" />
<script type="text/javascript" src="js/jquery-1.3.2.min.js"></script>
<script type="text/javascript">
	var $j = jQuery.noConflict();
    $j(document).ready
	(
		function()
		{
	        $j("form#searchSty").submit(function(){
				$j.post("ajax-search-pat.php",{
					search_type: "pat",
					param_sty: $j("#txtStyle").val()
				}, function(xml) {
					listLinPat(xml,1);
				});return false;
		    });
			
	        $j("form#searchMca").submit(function(){
				$j.post("ajax-search-pat.php",{
					search_type: "mca",
					param_sty: $j("#txtStyle").val(),
					param_mca: $j("#cmbMca").val()
				}, function(xml) {
					listLinPat(xml,2);
				});return false;
		    });
			
	        $j("form#searchColor").submit(function(){
				$j.post("ajax-search-pat.php",{
					search_type: "sze",
					param_sty: $j("#txtStyle").val(),
					param_pat: $j("#cmbColor").val()
				}, function(xml) {
					listLinSze(xml);
				});return false;
		    });

	        $j("form#searchSze").submit(function(){
				$j.post("ajax-search-pat.php",{
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
		$j("form#searchSze").submit();
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
		if (confirm("Favor confirmar la eliminacion del producto\nde la Orden de Compra")) {
			f4.action = "detcompra.php?acc=delete&prd="+codprd;
			f4.submit();
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
<P align="left" style="PADDING-LEFT: 5px"><strong><a href="<?php echo $link[$Est_Odc] ?>"><?php echo $titulo[$Est_Odc] ?></a></strong> / Detalle</P>
<P align="center">
<TABLE WIDTH="90%" BORDER="0" CELLSPACING="0" CELLPADDING="1" ALIGN="center">
<tr><td>
	<H2 style="TEXT-ALIGN: center">Orden de Compra <?php echo $Num_Odc; ?> de Fecha <?php echo fechafmt($Fec_Odc); ?></H2>
	<TABLE WIDTH="95%" BORDER="0" CELLSPACING="0" CELLPADDING="1" ALIGN="right">
	<tr><td colspan="7"><span class="encabezado1">Compras Normales :</span></td></tr>
	<form ID="F4" AUTOCOMPLETE="off" method="POST" name="F4" action="cerrar_compra.php" />
	<?php
		$j = 0;
		$Mca = "";
		$iTotPrd = 0;
		$tip_doc = 1;
		$afecha = split('/', $fecha);
		$fechain = $afecha[2].$afecha[1].$afecha[0];
		
		$xis = 0;
		$xisNull = false;
		$XisLineas = false;
		$numcol = 6; 

		$result = mssql_query("vm_odc_det_s $Num_Odc, '$Fec_Odc', '0'", $db);
		while ($row = mssql_fetch_array($result)) {
			if ($Mca != $row['Cod_Mca']) {
				if ($Mca != "") echo "<TR><TD colspan=\"$numcol\" style=\"PADDING-TOP: 10px; TEXT-ALIGN: right\" class=\"label_top\">&nbsp;</TD></TR>\n";
				$Mca = $row['Cod_Mca'];
				?>
				<TR>
					<TD class="titulo_tabla" width="100%" colspan="<?php echo $numcol ?>" align="middle">Marca: <?php echo $Mca; ?></TD>
				</TR>
				<TR>
					<TD class="titulo_tabla" align="middle">Style</TD>
					<TD class="titulo_tabla" align="middle">Patr&oacute;n</TD>
					<TD class="titulo_tabla" align="middle">Talla</TD>
					<TD class="titulo_tabla" align="middle">Cantidad<BR>Solicitada</TD>
					<TD class="titulo_tabla" align="middle" colspan="2">&nbsp;</TD>
				</TR>
			<?
			}
			echo "<TR>\n";
			if ($j == 0) {
				$clase1 = "label_left_right";
				$clase2 = "dato3";
				$clase3 = "textfieldRO2";
			}
			else {
				$clase1 = "label333";
				$clase2 = "dato33";
				$clase3 = "textfieldRO22";
			}
			if ($row['Ctd_Rep'] == -1) $xisNull = true;
			echo "   <TD width=\"10%%\" class=\"".$clase1."\" style=\"TEXT-ALIGN: center\">".$row['Cod_Sty']."</TD>\n";
			echo "   <TD width=\"10%%\" class=\"".$clase2."\" style=\"TEXT-ALIGN: center\">".$row['Key_Pat']."</TD>\n";
			echo "   <TD width=\"10%%\" class=\"".$clase2."\" style=\"TEXT-ALIGN: center\">".$row['Val_Sze']."</TD>\n";
			echo "   <TD width=\"20%%\" class=\"".$clase2."\" style=\"TEXT-ALIGN: center\">\n";
			//echo "<INPUT style=\"BORDER-BOTTOM: 1px; BORDER-LEFT: 1px; BORDER-TOP: 1px; BORDER-RIGHT: 1px; TEXT-ALIGN: center\" size=\"6\" class=\"".$clase3."\" name=\"dfRO".$row['Cod_Prd']."\" border=\"0\" value=\"".($row['Ctd_Prd']==0?"Manual":$row['Ctd_Prd'])."\" readOnly></TD>\n";
			echo ($row['Ctd_Prd'] == 0 ? "Manual" : $row['Ctd_Prd'])."</TD>\n";
			echo "   <TD width=\"50%%\" colspan=\"3\" class=\"".$clase2."\" style=\"TEXT-ALIGN: LEFT; PADDING-LEFT: 5px\">\n";
			if ($Est_Odc == "1")
				echo "<a href=\"grilla.php?prd=".$row['Cod_Prd']."&fec=$Fec_Odc&cod=$Num_Odc&filter=3".($row['Ctd_Prd'] == 0 ? "&tipo=1" : "")."\">Edit</a> / <a href=\"javascript:DelPrd('".$row['Cod_Prd']."')\">Delete</a></TD>\n";
			else
				echo "&nbsp;";
			echo "</TR>\n";
			$j = 1 - $j;
			$iTotPrd++;
		}
		mssql_free_result($result);
		if ($iTotPrd > 0) $XisLineas = true;
	?>
	<?php if ($iTotPrd > 0) { ?>
	<TR>
		<TD colspan="<?php echo $numcol ?>" style="PADDING-TOP: 10px; PADDING-BOTTOM: 10px; TEXT-ALIGN: right" class="label_top">&nbsp;</TD>
		</TD>
	</TR>
	<?php } else { ?>
		<TR>
			<TD colspan="<?php echo $numcol ?>" style="PADDING-TOP: 10px; TEXT-ALIGN: CENTER; PADDING-BOTTOM: 10px" class="label_left_right_top_bottom">
			NO EXISTEN SOLICITUDES DE COMPRA
			</TD>
		</TR>
		<TR>
			<TD colspan="<?php echo $numcol ?>" style="PADDING-TOP: 10px; PADDING-BOTTOM: 10px; TEXT-ALIGN: right">&nbsp;</TD>
		</TR>
	<?php } ?>
	<tr><td colspan="<?php echo $numcol ?>"><span class="encabezado1">Compras Reservadas :</span></td></tr>
	<?php
		$j = 0;
		$Mca = "";
		$iTotPrd = 0;
		$tip_doc = 1;
		$afecha = split('/', $fecha);
		$fechain = $afecha[2].$afecha[1].$afecha[0];
		
		$xis = 0;

		$result = mssql_query("vm_odc_det_s $Num_Odc, '$Fec_Odc', '1'", $db);
		while ($row = mssql_fetch_array($result)) {
			if ($Mca != $row['Cod_Mca']) {
				if ($Mca != "") echo "<TR><TD colspan=\"$numcol\" style=\"PADDING-TOP: 10px; TEXT-ALIGN: right\" class=\"label_top\">&nbsp;</TD></TR>\n";
				$Mca = $row['Cod_Mca'];
				?>
				<TR>
					<TD class="titulo_tabla" width="100%" colspan="<?php echo $numcol ?>" align="middle">Marca: <?php echo $Mca; ?></TD>
				</TR>
				<TR>
					<TD class="titulo_tabla" align="middle">Style</TD>
					<TD class="titulo_tabla" align="middle">Patr&oacute;n</TD>
					<TD class="titulo_tabla" align="middle">Talla</TD>
					<TD class="titulo_tabla" align="middle">Cantidad<BR>Solicitada</TD>
					<TD class="titulo_tabla" align="middle" colspan="2">&nbsp;</TD>
				</TR>
			<?
			}
			echo "<TR>\n";
			if ($j == 0) {
				$clase1 = "label_left_right";
				$clase2 = "dato3";
				$clase3 = "textfieldRO2";
			}
			else {
				$clase1 = "label333";
				$clase2 = "dato33";
				$clase3 = "textfieldRO22";
			}
			if ($row['Ctd_Rep'] == -1) $xisNull = true;;
			echo "   <TD width=\"10%%\" class=\"".$clase1."\" style=\"TEXT-ALIGN: center\">".$row['Cod_Sty']."</TD>\n";
			echo "   <TD width=\"10%%\" class=\"".$clase2."\" style=\"TEXT-ALIGN: center\">".$row['Key_Pat']."</TD>\n";
			echo "   <TD width=\"10%%\" class=\"".$clase2."\" style=\"TEXT-ALIGN: center\">".$row['Val_Sze']."</TD>\n";
			echo "   <TD width=\"20%%\" class=\"".$clase2."\" style=\"TEXT-ALIGN: center;\">\n";
			echo $row['Ctd_Prd']."</TD>\n";
			echo "   <TD width=\"50%%\" colspan=\"2\" class=\"".$clase2."\" style=\"TEXT-ALIGN: LEFT; PADDING-LEFT: 5px\">\n";
			if ($Est_Odc == "1")
				echo "<a href=\"grilla.php?prd=".$row['Cod_Prd']."&fec=$Fec_Odc&cod=$Num_Odc&filter=3\">Edit</a> / <a href=\"javascript:DelPrd('".$row['Cod_Prd']."')\">Delete</a></TD>\n";
			else
				echo "&nbsp;";
			echo "</TR>\n";
			$j = 1 - $j;
			$iTotPrd++;
		}
		mssql_free_result($result);
		if ($iTotPrd > 0) $XisLineas = true;
	?>
	<?php if ($iTotPrd > 0) { ?>
	<TR>
		<TD colspan="<?php echo $numcol ?>" style="PADDING-TOP: 10px; PADDING-BOTTOM: 10px; TEXT-ALIGN: right" class="label_top">
			<?php if ($Est_Odc == "1") { ?>
			<input type="submit" name="SendToCompra" value="Cerrar Orden de Compra" tabindex="20" class="button2" style="width:220px;" />
			<input name="Fec_Odc" type="hidden" id="Fec_Odc" value="<?php echo $Fec_Odc ?>" />
			<input name="Num_Odc" type="hidden" id="Num_Odc" value="<?php echo $Num_Odc ?>" />
			<?php } else echo "&nbsp;"; ?>
		</TD>
	</TR>
	<?php } else { ?>
		<TR>
			<TD colspan="<?php echo $numcol ?>" style="PADDING-TOP: 10px; TEXT-ALIGN: CENTER; PADDING-BOTTOM: 10px" class="label_left_right_top_bottom">
			NO EXISTEN SOLICITUDES DE COMPRA
			</TD>
		</TR>
		<TR>
			<TD colspan="<?php echo $numcol ?>" style="PADDING-TOP: 10px; PADDING-BOTTOM: 10px; TEXT-ALIGN: right">
			<?php if ($Est_Odc == "1" && $XisLineas) { ?>
			<input type="submit" name="SendToCompra" value="Cerrar Orden de Compra" tabindex="20" class="button2" style="width:220px;" />
			<input name="Fec_Odc" type="hidden" id="Fec_Odc" value="<?php echo $Fec_Odc ?>" />
			<input name="Num_Odc" type="hidden" id="Num_Odc" value="<?php echo $Num_Odc ?>" />
			<?php } else echo "&nbsp;"; ?>
			</TD>
		</TR>
	<?php } ?>
	
	</form>
	<?php if ($Est_Odc == "1") { ?>	
	<TR>
		<TD colspan="<?php echo $numcol ?>">
		<fieldset>
		<legend>
			Agregar Nuevos Items a la Orden :&nbsp;
		</legend>
          <table cellpadding="2" cellspacing="0" width="100%px" height="40px">
            <tr>
              <td style="width: 70px;" class="label">
                Style:
              </td>
              <form id="searchSty">
			  <td style="width: 100px;">
				<input name="txtStyle" id="txtStyle" maxlength="30" size="10" class="textfield" onblur="blurEstilo(this)" tabindex="14" /><br />
			  </td>
			  </form>
              <td style="width: 70px;" class="label">
                Marca:
              </td>
			  <form id="searchMca">
              <td style="width: 100px;">
				<!--input name="txtColor" type="text" id="txtColor" maxlength="30" size="10" class="textfield" onfocus="javascript:clearSuggest('styleSuggest');" onkeyup="javascript:getColors(document.aspnetForm.ctl00_ContentPlaceHolder1_txtStyle.value,this.value)" tabindex="16" /><br /-->
				<select id="cmbMca" name="cmbMca" class="textfield" onChange="blurMca(this)" tabindex="15">
					<option selected value="_NONE">NONE</option>
				</select>
              </td>
			  </form>
              <td style="width: 70px;" class="label">
                Color:
              </td>
			  <form id="searchColor">
              <td style="width: 100px;">
				<!--input name="txtColor" type="text" id="txtColor" maxlength="30" size="10" class="textfield" onfocus="javascript:clearSuggest('styleSuggest');" onkeyup="javascript:getColors(document.aspnetForm.ctl00_ContentPlaceHolder1_txtStyle.value,this.value)" tabindex="16" /><br /-->
				<select id="cmbColor" name="cmbColor" class="textfield" onChange="blurTalla(this)" tabindex="16">
					<option selected value="_NONE">NONE</option>
				</select>
              </td>
			  </form>
              <td class="label" style="width: 50px;">
                Size*:
              </td>
			  <form id="searchSze">
              <td style="width: 75px;">
				<select id="cmbSize" name="cmbSize" class="textfield" onChange="blurSize(this)" tabindex="17">
					<option selected value="_NONE">NONE</option>
				</select>
              </td>
			  </form>
              <td class="label" style="width: 60px;">

                Quantity*:
              </td>
			  <form ID="F3" AUTOCOMPLETE="off" method="POST" name="F3" ACTION="reponer_ins.php?filter=3" onsubmit="return checkDataFichaOdr(this,3)">
              <td style="width: 75px;">
                <input name="dfCod_Qty" type="text" id="dfCod_Qty" size="5" value="" class="textfield" tabindex="18" />
                <input name="dfCod_Mca" type="hidden" id="dfCod_Mca" value="" />
                <input name="dfCod_Sty" type="hidden" id="dfCod_Sty" value="" />
                <input name="dfCod_Pat" type="hidden" id="dfCod_Pat" value="" />
                <input name="dfCod_Sze" type="hidden" id="dfCod_Sze" value="" />
                <input name="dfNum_Odc" type="hidden" id="dfNum_Odc" value="<?php echo $Num_Odc ?>" />
                <input name="dfFec_Mov" type="hidden" id="dfFec_Mov" value="<?php echo $Fec_Odc ?>" />
              </td>
              <td>
                <input type="submit" name="cmdOrderDetail" value="Agregar Items" tabindex="20" class="button2" style="width:93px;" />
              </td>
			  </form>
            </tr>

            <tr>
              <td colspan="9" align="center" class="labelBig" style="TEXT-ALIGN: center">
				* Opcional(dejar vac&iacute;o para ver la grilla Style Color)
              </td>
            </tr>
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
<!-- script que define y configura el calendario-->
</body>
</html>
