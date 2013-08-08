<?php
//Obtengo los datos de conexion de la base de datos
ini_set('display_errors', '0');
session_start();
include("config.php");

$Cod_Cot = (isset($_GET['cot'])) ? ok($_GET['cot']) : 0;
$Fol_Ctt = (isset($_GET['folctt'])) ? ok($_GET['folctt']) : 0;

if ($Cod_Cot > 0) {
	$result = mssql_query("vm_s_cothdr $Cod_Cot",$db);
	if ($row = mssql_fetch_array($result)) {
		$Num_Cot   = $row['Num_Cot'];
		$cod_clt   = $row['Cod_Clt'];		
	}
	
	$Titulo = "Historial de Mensajes para Cotizaci&oacute;n # $Num_Cot";
}
else {
    $Titulo = "Historial de Mensajes para el Contacto # $Fol_Ctt";
    $cod_clt = (isset($_GET['clt'])) ? ok($_GET['clt']) : 0;
}

/*
$result = mssql_query ("vm_cna_sin_res_ctt ".$Cod_Clt, $db)
						or die ("No se pudo leer datos del cliente");
if ($row = mssql_fetch_array($result)) $tot_cnactt = $row["tot_cna"];
mssql_free_result($result); 

$result = mssql_query ("vm_cna_sin_res ".$Cod_Clt, $db)
						or die ("No se pudo leer datos del cliente");
if ($row = mssql_fetch_array($result)) $tot_cna = $row["tot_cna"];
mssql_free_result($result); 
*/

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Catalogo - Vestmed Vestuario M&eacute;dico</title>
<LINK href="Include/estilos.css" type="text/css" rel="stylesheet" />
<link href="css/layout.css" type="text/css" rel="stylesheet" />
<link href="css/headers.css" type="text/css" rel="stylesheet" />
<link rel="stylesheet" type="text/css" media="screen" href="dropdown/css/uvumi-dropdown.css" />
<link href="css/clearfix.css" type="text/css" rel="stylesheet" />
<script type="text/javascript">
    //new UvumiDropdown('dropdown-scliente');

	function popwindow(ventana,altura){
	   window.open(ventana,"Anexos","toolbar=0,location=0,scrollbars=yes,resizable=1,left=60,top=40,width=800,height="+altura);
	}
	
	function ver_producto(cod_prd) {
		popwindow("preview_prd.php?prd="+cod_prd,400)
	}
	
	function CheckRespuesta(form) {
		if (form.consulta.value == "") {
			alert("Debe ingresar una consulta");
			return false;
		}
		if (form.consulta.value.length > 1000) {
			alert("Comentario no puede tener mas de 1.000 caracteres");
			return false;
		}
		return true;
	}
	
	function Enviar_Res(folio) {
		if (eval('f2.respuesta'+folio).value == "") {
			alert("Debe ingresar una respuesta");
			return false;
		}
		if (eval('f2.respuesta'+folio).value.length > 1000) {
			alert("Comentario no puede tener mas de 1.000 caracteres");
			return false;
		}
		f2.action = "consultasusr.php?cot=<?php echo $Cod_Cot; ?>&folio="+folio+"&accion=respuesta&paso=<?php echo $paso; ?>";
		f2.submit();
	}
</script>
</head>
<BODY>
<?php formar_topbox ("100%%","center"); ?>
<P align="center">
<h2 style="text-align: center"><?php echo $Titulo; ?></h2>
<TABLE BORDER="0" CELLSPACING="1" CELLPADDING="0" width="100%" height="302px" ALIGN="center">
<TR>
	<TD width="60%" VALIGN="TOP" class="dato10p" colspan="2" style="PADDING-BOTTOM:10px">	
	<table width="100%" BORDER="0" CELLSPACING="0" CELLPADDING="1" ALIGN="left">
	<tr>
		<td class="titulo_tabla" valign="top" style="text-align: left" width="10%" height="15">Fecha</td>
		<td class="titulo_tabla" valign="top" style="text-align: left" width="5%" height="15">Folio</td>
		<td class="titulo_tabla" valign="top" style="text-align: left" width="10%" height="15">Origen</td>
		<td class="titulo_tabla" valign="top" style="text-align: left" width="65%" height="15">Consulta / Respuesta</td>
		<td class="titulo_tabla" valign="top" style="text-align: left" width="10%" height="15">&nbsp;</td>
	</tr>
	<?php 
	    if ($Cod_Cot > 0)
			$result = mssql_query("vm_hiscna_cot $Cod_Cot, $cod_clt", $db);
		else
			$result = mssql_query("vm_hiscna_ctt $Fol_Ctt, $cod_clt", $db);
		$bOkRespuesta = false;
		$totfilas = 0;
		$folcna = 0;
		while ($row = mssql_fetch_array($result)) {
			$totfilas++;
			if ($folcna > 0 and $folcna != $row['Fol_Cna'])
			    echo "<tr><td colspan=\"5\">&nbsp;</td>\n";
			$folcna = $row['Fol_Cna'];
	?>
			<tr>
				<td align="left" valign="top"><?php echo $row['Fec_Dis']; ?></td>
				<td align="left" valign="top"><?php echo $row['Fol_Cna']; ?></td>
				<td align="left" valign="top"><?php echo ($row['Num_Doc'] == "Vestmed" ? $row['Num_Doc'] : "Cliente" ); ?></td>
				<td align="left" valign="top" colspan="2"><?php echo $row['Det_Cna']; ?></td>
			</tr>
	<?php if (trim($row['Det_Res']) != "") { ?>
			<tr>
				<td align="left" valign="top"><?php echo $row['Fec_ResCnaDis']; ?></td>
				<td align="left" valign="top"><?php echo $row['Fol_Cna']; ?></td>
				<td align="left" valign="top"><?php echo ($row['Num_DocRes'] == "Vestmed" ? $row['Num_DocRes'] : "Cliente"  ); ?></td>
				<td align="left" valign="top" colspan="2"><?php echo $row['Det_Res']; ?></td>
			</tr>
	<?php } else { 
			$autor = $row['Num_Doc'];
			if ($autor == "Vestmed") {
	?>
			<tr>
				<td align="left" valign="top"><?php echo date("d/m/Y") ?></td>
				<td align="left" valign="top"><?php echo $row['Fol_Cna']; ?></td>
				<td align="left" valign="top">Cliente</td>
				<td align="left" valign="top">&nbsp;</td>
				<td align="right" valign="top">&nbsp;</td>
			</tr>
	<?php
			}
		  }
		}
	?>
	<tr><td colspan="4" style="text-align: right; padding-top: 5px"><input type="button" name="Cerrar" value=" Cerrar " class="btn" onclick="javascript:window.close()"></td></tr>
	</table>
</TR>
</TABLE>
</p>
<?php formar_bottombox (); ?>
</BODY>
</HTML>
