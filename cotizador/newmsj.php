<?php
//Obtengo los datos de conexion de la base de datos
ini_set('display_errors', '0');
session_start();
include("global_cot.php");
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Catalogo - Vestmed Vestuario Médico</title>
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
<link rel="stylesheet" type="text/css" media="all" href="../Include/calendar-green.css" title="win2k-cold-1" />
<script type="text/javascript" src="../Include/calendar.js"></script>
<script type="text/javascript" src="../Include/calendar-es.js"></script>
<script type="text/javascript" src="../Include/calendar-setup.js"></script>
<script type="text/javascript" src="js/AccionesMenu.js"></script>
<script type="text/javascript">
	new UvumiDropdown('dropdown-scliente');

	function MarcarTodos(form,nombrecheckbox) {
	   for (i=0; i<form.elements.length; i++) {
		if (form.elements[i].name == nombrecheckbox)
			form.elements[i].checked = true;
	   }	
	}

	function DesMarcarTodos(form,nombrecheckbox) {
	   for (i=0; i<form.elements.length; i++) {
		if (form.elements[i].name == nombrecheckbox)
			form.elements[i].checked = false;
	   }	
	}
</script>
<script type="text/javascript" src="../js/jquery-1.3.2.min.js"></script>
</head>

<body>
<div id="body">
	<div id="header"></div>
    <ul id="usuario_registro">
		<?php 	echo display_usr('cotizador', 'cotizador', $db); ?>
    </ul>
    <div id="work">
<?php formar_topbox ("100%%","center"); ?>
<P align="left"><strong>Escritorio</strong></P>
<P align="center">
<TABLE WIDTH="100%" BORDER="0" CELLSPACING="0" CELLPADDING="1" ALIGN="center">
<tr>
<td width="15%" align="left" valign="top">
	<?php echo display_mnuizq(); ?>
</td>
<td width="1%">&nbsp;</td>
<td  valign="top" class="label_left_right_top_bottom" STYLE="PADDING-LEFT:20px; PADDING-RIGHT:20px; TEXT-ALIGN:left">
	<TABLE WIDTH="100%" BORDER="0" CELLSPACING="0" CELLPADDING="1" ALIGN="center">
	<tr><td>
		<H1>Nuevos Mensaje</H1>
		<table border="0" summary="">
	<tr>
		<td></td>
		<td></td>
	</tr>
	<tr>
		<td colspan="2"></td>
	</tr>
	<tr>
		<td></td>
		<td></td>
	</tr>
	<tr>
		<td></td>
		<td></td>
	</tr>
</table>
	</td></tr>
	</TABLE>
</td>
</tr>
</table>
</p>
<?php formar_bottombox (); ?>
    </div>
    <div id="footer"></div>
</div>
<script language="javascript">
	var f1;
	var f2;
	f1 = document.F1;
	f2 = document.F2;
</script>


</body>
</html>