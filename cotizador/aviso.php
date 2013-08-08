<?php
//Obtengo los datos de conexion de la base de datos
ini_set('display_errors', '0');
session_start();
include("global_cot.php");

$idmsg = intval($HTTP_GET_VARS["idmsg"]);

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
</head>
<BODY>
<div id="body" style="width:100%">
	<!--div id="header"></div-->
    <ul id="usuario_registro">
		<?php 	//echo display_mnu($UsrId, $cod_tipper, $Cod_Cot, $db); ?>
    </ul>
	<div id="work">

<?php formar_topbox ("100%%","center"); ?>
<P align="center">
				<TABLE border="0" cellpadding="1" cellspacing="0" width="70%" align="center">
				<TR>
				<TD><H1>ORDEN DE COMPRA ENVIADA</H2>
				</TD>
				</TR>
				<TR>
				<TD class="bienvenida" style="TEXT-ALIGN: center">
				Gracias por comprar en <strong>www.vestmed.cl</strong><BR> Su compra ser&aacute despachada
				al lugar que nos indic&oacute; una vez que validemos el pago.
				</TD>
				</TR>
				<TR><TD class="datoj" style="PADDING-TOP: 50px">
					<p class"datoc">
					<form ID="F2" AUTOCOMPLETE="off" method="POST" name="F2" ACTION="javascript:window.close()">
					  <input type="submit" name="Cerrar" value=" Cerrar " class="bnt">
					</form>
					</p></td></Tr>
				</TABLE>
</p>
<?php formar_bottombox (); ?>
    </div>
    <!--div id="footer"></div-->
</div>
<script language="javascript">
	var f1;	
	var f2;
	f1 = document.F1;	
	f2 = document.F2;	
<?php	
    if ($idmsg == 32) {
		$page = "miscotizaciones.php";
		echo "	parent.opener.document.F2.action=\"".$page."\"\n";
		echo "	parent.opener.document.F2.submit();\n";
	}
?>
</script>
</BODY>
</HTML>
