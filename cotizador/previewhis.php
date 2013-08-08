<?php
//Obtengo los datos de conexion de la base de datos
ini_set('display_errors', '0');
session_start();
include("global_cot.php");

$UsrId = (isset($_SESSION['UsrIntra'])) ? $UsrId = $_SESSION['UsrIntra'] : "";
$Perfil = (isset($_SESSION['Perfil'])) ? $Perfil = $_SESSION['Perfil'] : "";
$RutClt = (isset($_GET['clt'])) ? ok($_GET['clt']) : "";

$result = mssql_query("vm_s_per_tipdoc 1, '$RutClt'",$db);
if ($row = mssql_fetch_array($result)) {
	$cod_clt   = $row['Cod_Clt'];
	$nom_clt = ($row['Cod_TipPer'] == 1 ? $row['Pat_Per']." ".$row['Mat_Per'].", ".$row['Nom_Per'] : $row['RznSoc_Per']);
	$num_doc   = $row['Num_Doc'];

	$result = mssql_query("vm_suc_s $cod_clt", $db);
	if ($row = mssql_fetch_array($result)) {
		$cod_suc = $row['Cod_Suc'];
		$nom_suc = $row['Nom_Suc'];
		$dir_suc = $row['Dir_Suc'];
		$fax_suc = $row['Fax_Suc'];
		$nom_cdd = $row['Nom_Cdd'];
		$nom_cmn = $row['Nom_Cmn'];
		mssql_free_result($result); 
	}

	$result = mssql_query("vm_ctt_s $cod_clt, $cod_suc", $db);
	if($row = mssql_fetch_array($result)) {
		$nom_ctt = $row['Pat_Per']." ".$row['Mat_Per'].", ".$row['Nom_Per'];
		$fon_ctt = $row['Fon_Ctt'];
		$mail_ctt = $row['Mail_Ctt'];
		mssql_free_result($result); 
	}
	
	$fec_hoy = date('d/m/Y');
	
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Catalogo - Vestmed Vestuario M&eacute;dico</title>
<LINK href="../Include/estilos.css" type="text/css" rel="stylesheet" />
<link href="../css/layout.css" type="text/css" rel="stylesheet" />
<link href="../css/headers.css" type="text/css" rel="stylesheet" />
<link rel="stylesheet" type="text/css" media="screen" href="../dropdown/css/uvumi-dropdown.css" />
<link href="../css/clearfix.css" type="text/css" rel="stylesheet" />
<!-- Lytebox Includes //-->
<script type="text/javascript" src="../lytebox/lytebox.js"></script>
<link rel="stylesheet" type="text/css" href="../lytebox/lytebox.css" media="screen" />
<!-- Lytebox Includes //-->
<script type="text/javascript">
    //new UvumiDropdown('dropdown-scliente');

	function popwindow(ventana,altura){
	   window.open(ventana,"Anexos","toolbar=0,location=0,scrollbars=yes,resizable=1,left=60,top=40,width=800,height="+altura);
	}
	
	function ver_producto(cod_prd) {
		popwindow("preview_prd.php?prd="+cod_prd,400)
	}
</script>
</head>
<BODY>
<div id="body" style="width:100%">
	<!--div id="header"></div-->
	<div id="work">

<?php formar_topbox ("100%%","center"); ?>
<P align="center">
<TABLE BORDER="0" CELLSPACING="1" CELLPADDING="0" width="100%">
<TR>
	<TD width="60%" VALIGN="TOP" ROWSPAN="2" HEIGHT="37"><IMG SRC="logo.gif" width="235" HEIGHT="130"></TD>
	<TD class="dato" style="text-align: right; FONT-SIZE: 2.5em; FONT-FAMILY: Arial, Verdana;" width="40%" VALIGN="bottom" COLSPAN="2"><b>Ficha Cliente <?php echo $cod_clt; ?></b></TD>
</TR>
<TR>
	<TD class="dato" style="text-align: right" width="40%" VALIGN="TOP" COLSPAN="2" HEIGHT="65"><b>Fecha: </b><?php echo $fec_hoy ?></TD>
</TR>
<TR>
	<TD width="60%" VALIGN="TOP" class="dato5p12s" style="padding-top: 20px;"><B>Cliente: <?php echo $nom_clt ?></B></TD>
	<TD width="40%" VALIGN="TOP" COLSPAN="2" style="padding-top: 20px">&nbsp;</TD>
</TR>
<TR>
	<TD width="60%" VALIGN="TOP" class="dato5p">Rut: <?php echo formatearRut($num_doc) ?></TD>
	<TD width="40%" VALIGN="TOP" COLSPAN="2">&nbsp;</TD>
</TR>
<TR>
	<TD width="60%" VALIGN="TOP" class="dato5p">Sucursal: <?php echo $nom_suc ?></TD>
	<TD width="40%" VALIGN="TOP" COLSPAN="2" class="dato">Contacto: <?php echo $nom_ctt ?></TD>
</TR>
<TR>
	<TD width="60%" VALIGN="TOP" class="dato5p">Direcci&oacute;n: <?php echo $dir_suc ?></FONT></TD>
	<TD width="40%" VALIGN="TOP" COLSPAN="2" class="dato">Tel&eacute;fono: <?php echo $fon_ctt ?></TD>
</TR>
<TR>
	<TD width="60%" VALIGN="TOP" class="dato5p">Ciudad: <?php echo $nom_cdd ?></TD>
	<TD width="40%" VALIGN="TOP" COLSPAN="2" class="dato">Fax: <?php echo $fax_suc ?></TD>
</TR>
<TR>
	<TD width="60%" VALIGN="TOP" class="dato5p">Comuna: <?php echo $nom_cmn ?></TD>
	<TD width="40%" VALIGN="TOP" COLSPAN="2" class="dato">Email: <?php echo $mail_ctt; ?></TD>
</TR>
<TR>
	<TD width="60%" VALIGN="TOP">&nbsp;</TD>
	<TD width="40%" VALIGN="TOP" COLSPAN="2">&nbsp;</TD>
</TR>
<TR><TD VALIGN="TOP" COLSPAN="3" style="padding-bottom: 10px">
<TABLE BORDER="0" CELLSPACING="1" CELLPADDING="0" width="100%" ALIGN="center">
	<tr><td>
		<h2>Historico de Compras</h2>
		<TABLE WIDTH="98%" BORDER="0" CELLSPACING="0" CELLPADDING="1" ALIGN="right">
		<?php
			$j = 0;
			$iTotPrd = 0;
			$RutAnt = "";
			$result = mssql_query("vm_hisusr_esc 1, '$RutClt', 1", $db);
			while ($row = mssql_fetch_array($result)) {
				if ($row['Num_Doc'] != $RutAnt) {
					$RutAnt = $row['Num_Doc'];
		?>
		<TR><TD colspan="9" style="PADDING-BOTTOM: 5px"><b>Cliente: </b><?php echo ($row['Cod_TipPer'] == 1 ? $row['Pat_Per'].' '.$row['Mat_Per'].', '.$row['Nom_Per'] : $row['RznSoc_Per']) ?></TD></TR>
		<TR>
			<TD class="titulo_tabla" width="5%" align="left"   style="TEXT-ALIGN: left"># NV</TD>
			<TD class="titulo_tabla" width="10%" align="middle" style="TEXT-ALIGN: center">Fecha</TD>
			<TD class="titulo_tabla" width="15%" align="left"   style="text-align: left">Sucursal</TD>
			<TD class="titulo_tabla" width="10%" align="left"   style="text-align: left">Titulo</TD>
			<TD class="titulo_tabla" width="10%" align="center" style="TEXT-ALIGN: center">Style</TD>
			<TD class="titulo_tabla" width="10%" align="middle" style="TEXT-ALIGN: center">Pattern</TD>
			<TD class="titulo_tabla" width="10%" align="middle" style="TEXT-ALIGN: center">Talla</TD>
			<TD class="titulo_tabla" width="10%" align="middle" style="TEXT-ALIGN: center">Cantidad</TD>
			<TD class="titulo_tabla" width="10%" align="right"   style="text-align:right">Precio<BR>(IVA Incl)</TD>
		</TR>
		<?php
				}
				echo "<TR>\n";
				if ($j == 0) {
					$clase1 = "";
					$clase2 = "";
				}
				else {
					$clase1 = "";
					$clase2 = "";
				}
		?>
			<TD align="left"><?php echo $row['Cod_Nvt']; ?></TD>
			<TD style="TEXT-ALIGN: center"><?php echo $row['Fec_Nvt_Display']; ?></TD>
			<TD style="TEXT-ALIGN: left"><?php echo $row['Nom_Suc'] ?></TD>
			<TD style="TEXT-ALIGN: left"><?php echo $row['Nvt_Tlt'] ?></TD>
			<TD style="TEXT-ALIGN: center"><?php echo $row['Cod_Sty'] ?></TD>
			<TD style="TEXT-ALIGN: center"><?php echo $row['Key_Pat'] ?></TD>
			<TD style="TEXT-ALIGN: center"><?php echo $row['Val_Sze'] ?></TD>
			<TD style="TEXT-ALIGN: center"><?php echo $row['Ctd_Prd'] ?></TD>
			<TD style="TEXT-ALIGN: right"><?php echo formatearMillones($row['Mto_Prd']) ?></TD>
		<?php
				echo "</TR>\n";
				$j = 1 - $j;
				$iTotPrd++;
			}
			mssql_free_result($result);
		?>
		<?php if ($iTotPrd == 0) { ?>
		<TR>
			<TD colspan="9" style="PADDING-TOP: 10px; TEXT-ALIGN: CENTER; PADDING-BOTTOM: 10px" class="label_left_right_bottom">
			NO EXISTEN COMPRAS DEL CLIENTE.
			</TD>
		</TR>
		
		<?php } else { ?>
		<TR>
			<TD colspan="9" style="PADDING-TOP: 10px; TEXT-ALIGN: right" class="label_top">
				<a href="javascript:window.print()">Imprimir</a>
			</TD>
		</TR>
		<?php } ?>
		</TABLE>
	</td></tr>
</TABLE>
</TD></TR>
</TABLE>
</p>
<?php formar_bottombox (); ?>
    </div>
    <!--div id="footer"></div-->
</div>
</BODY>
</HTML>
