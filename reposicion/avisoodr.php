<?php
//Obtengo los datos de conexion de la base de datos
ini_set('display_errors', '0');
session_start();
include("config.php");

$UsrId = (isset($_SESSION['UsrId'])) ? $UsrId = $_SESSION['UsrId'] : "";
$Perfil = (isset($_SESSION['Perfil'])) ? $Perfil = $_SESSION['Perfil'] : "";
$idmsg = intval($HTTP_GET_VARS["idmsg"]);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
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
<link rel="stylesheet" type="text/css" media="all" href="../Include/calendar-green.css" title="win2k-cold-1" />
<script type="text/javascript" src="../Include/calendar.js"></script>
<script type="text/javascript" src="../Include/calendar-es.js"></script>
<script type="text/javascript" src="../Include/calendar-setup.js"></script>
<script type="text/javascript">
new UvumiDropdown('dropdown-scliente');
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
<P align="center">
			<?php
				switch ($idmsg) {
				case 1:
					$Est_Odr = "";
					$Num_Odr = ok($_GET['cod']);
					$Fec_Odr = ok($_GET['fec']);
					$result = mssql_query("vm_odrhdr_s $Num_Odr, '$Fec_Odr'", $db);
					if ($row = mssql_fetch_array($result)) $Est_Odr = $row['Est_Odr'];
			?>
					<TABLE border="0" cellpadding="1" cellspacing="0" width="70%" align="center">
					<TR>
						<TD><H1>AVISO</H2>
						</TD>
					</TR>
					<TR>
						<TD class="bienvenida" height="350px" valign="top">
						<?php if ($Est_Odr == "2") { ?>
						Su orden ha sido enviada exitosamente a Reposici&oacute;n y ser&aacute; procesada a la brevedad.
						<?php } else if ($Est_Odr == "3") { ?>
						Su orden ha sido enviada exitosamente a Compra y ser&aacute; procesada a la brevedad.
						<?php } else { ?>
						Su orden ha tenido problemas. Favor contactarse con el departamento t&eacute;cnico.
						<?php } ?>
						</TD>
					</TR>
					</TABLE>
			<?php
					break;
				
				case 2:
			?>
					<TABLE border="0" cellpadding="1" cellspacing="0" width="70%" align="center">
					<TR>
						<TD><H1>AVISO</H2>
						</TD>
					</TR>
					<TR>
						<TD class="bienvenida" height="350px" valign="top">
						Su orden se ha dado por cerrada exitosamente. 
						</TD>
					</TR>
					</TABLE>
			
			<?php
				}
			?>
</p>
<?php formar_bottombox (); ?>
    </div>
    <div id="footer"></div>
</div>
<script language="javascript">
	var f1;	
	f1 = document.F1;
</script>
</body>
</html>
