<?php
//Obtengo los datos de conexion de la base de datos
ini_set('display_errors', '0');
session_start();
include("config.php");

$UsrId   = (isset($_SESSION['UsrId'])) ? $UsrId = $_SESSION['UsrId'] : "";
$Perfil  = (isset($_SESSION['Perfil'])) ? $Perfil = $_SESSION['Perfil'] : "";
$idmsg   = intval($HTTP_GET_VARS["idmsg"]);
$Cod_Cot = intval($HTTP_GET_VARS["id"]);

$result = mssql_query("vm_s_cothdr $Cod_Cot",$db);
if ($row = mssql_fetch_array($result)) {
	$Arc_Adj    = $row['arc_Adj'];
	$Num_TrnBco = $row['Num_TrnBco'];
	$cod_clt    = $row['Cod_Clt'];
	$cod_per    = $row['Cod_Per'];
}

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
<!-- Lytebox Includes //-->
<script type="text/javascript" src="lytebox/lytebox.js"></script>
<script type="text/javascript" src="js/jquery-1.3.2.min.js"></script>
<link rel="stylesheet" type="text/css" href="lytebox/lytebox.css" media="screen" />
<!-- Lytebox Includes //-->
</head>

<body>
<div id="body">
	<div id="header"></div>
    <div class="menu"id="menu-noselect">
    	<a id="home" href="index.htm">home</a>
    	<a id="empresa" href="empresa.htm">empresa</a>
        <a id="marcas" href="marcas.htm">marcas</a>
        <a id="telas" href="telas.htm">telas</a>
        <a id="bordados" href="bordados.htm">bordados</a>
        <a id="despachos" href="despachos.htm">despachos</a>
        <a id="clientes" href="clientes.htm">clientes</a>
         <div id="servicio-cliente-selected" style="z-index:1000;padding-top:0px;" class="selected">
        <ul id="dropdown-scliente" class="dropdown">
                <li>
                    <a class="normal" href="servicio-cliente.htm">servicio al cliente</a>
                    <ul>
                       <li>
                            <a href="faq.htm">Faq</a>
                        </li>
                        <li>
                            <a href="como-tomar-medidas.htm">C&oacute;mo Tomar Medidas</a>
                        </li>
                        <li>
                            <a href="despachos.htm">Despachos</a>
                        </li>
                        <li>
                            <a href="clean-care.htm">Clean & Care</a>
                        </li>
                        <li>
                            <a href="tracking-ordenes.htm">Tracking de &Oacute;rdenes</a>
                        </li>
                        <li>
                            <a href="como-cotizar.htm">C&oacute;mo Cotizar</a>
                        </li>
                       
                        <li>
                            <a href="politicas-privacidad.htm">Pol&iacute;ticas de Privacidad</a>
                        </li>
                    </ul>
                </li>
            </ul>		
        </div>
        <a id="catalogo" href="catalogo.php">catalogo</a>
        <a id="contacto" href="contacto.htm">contacto</a>
  
  	</div>
	<?php 
		if ($cod_per == 0) { 
	?>
    <ul id="usuario_registro">
        <form ID="F1" AUTOCOMPLETE="off" method="POST" name="F1">
    	<li class="back-verde registro"><a href="registrarse.php">REGISTRARSE</a></li>
        <li class="olvido"><a href="javascript:EnviarClave()">OLVID&Oacute; SU CLAVE?</a></li>
        <li class="back-verde"><a href="javascript:ValidarLogin()">ENTRAR</a></li>
        <li class="back-verde inputp"><input type="password" name="dfclave"/></li>
        <li class="back-verde">CONTRASE&Ntilde;A</li>
        <li class="back-verde inputp"><input type="" name="rut" id="rut" onblur="formatearRut('rut','dfrut')"></li>
        <li class="back-verde">RUT</li>
		<input type="hidden" name="dfrut" id="dfrut" />
		</form>
    </ul>
	<?php }
		  else {
	?>
    <ul id="usuario_registro">
		<?php 	echo display_login($cod_per, $cod_clt, $db); ?>
    </ul>
	
	<?php 
		}
	?>
    <div id="work">
		<div id="back-registro3">
			<img src="images/registro/mihistorial.png" style="top:60px;" class="titulo-principal-avisos" />
           	<div style="width:765px; height:auto; margin:0 auto 0 100px; padding-top:10px;">
<form ID="F2" AUTOCOMPLETE="off" method="POST" name="F2" ACTION="index.php" />
				<TABLE border="0" cellpadding="1" cellspacing="0" width="70%" align="center">
				<TR>
				<TD><H1>ORDEN DE COMPRA ENVIADA</H2>
				</TD>
				</TR>
				<TR>
				<TD class="bienvenida" style="TEXT-ALIGN:left; PADDING-TOP: 50px" height="340px">
				<?php if ($Arc_Adj != '' Or $Num_TrnBco != null) { ?>
				Su orden ha sido recibida satisfactoriamente ser&aacute; procesada una vez confirmado el pago. Usted podr&aacute; 
				revisar en todo	momento estado de su pedido a trav&eacute;s de la opci&oacute;n Ordenes del men&uacute; de su cuenta
				<?php } else { ?>
				Su orden ha sido recibida satisfactoriamente, hemos detectado que no se ha completado toda la informaci&oacute;n 
				referente al pago, usted podr&aacute; completar esta informaci&oacute;n a trav&eacute;s de la opci&oacute;n Ordenes del 
				men&uacute; de su cuenta
				<?php } ?>
				</TD>
				</TR>
				<TR><TD class="datoj" style="PADDING-TOP: 50px">
					<p class"datoc">
					  <input type="submit" name="Cerrar" value=" Cerrar " class="bnt">
					</p></td>
				</TR>
				</TABLE>
</form>
			</div>
		</div>
	</div>
	<div id="footer"></div>

</body>
<script language="javascript">
	var f1 = document.F1;	
	var f2 = document.F2;
</script>
</html>
