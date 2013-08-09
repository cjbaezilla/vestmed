<?php
//Obtengo los datos de conexion de la base de datos
//ini_set('display_errors', '0');
session_start();
include("config.php");

$Cod_Per = 0;
$Cod_Clt = 0;
if (isset($HTTP_GET_VARS["idu"])) {
	$Cod_Per = intval($HTTP_GET_VARS["idu"]);
    $_SESSION['CodPer'] = $Cod_Per;     
}
else if (isset($_SESSION['CodPer'])) 
	$Cod_Per = intval($_SESSION['CodPer']);
	
if (isset($HTTP_GET_VARS["idc"])) {
	$Cod_Clt = intval($HTTP_GET_VARS["idc"]);
    $_SESSION['CodClt'] = $Cod_Clt;     
}
else if (isset($_SESSION['CodClt'])) 
	$Cod_Clt = intval($_SESSION['CodClt']);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Vestmed - Vestuario Medico | Todo en Delantales, Poleras, Pantalones, Scrubs, Zuecos, Accesorios, Etc...</title>
<meta name="keywords" content="vestuario medico, ropa medica, delantal, delantales, uniformes clinicos, uniformes medicos, delantales para medico, ropa medica, ropa medica, uniformes enfermera, unifromes matrona, vestuario medico"/>
<meta name="description" content="Uniformes y ropa para profesionales de la salud: Delantales poleras, pantalones, zuecos, zapatos, accesorios, solo las mejores marcas: Landau Uniforms, Cherokee Uniforms, UrbaneScrubs, Barco Uniforms, Bamers. Solicite sus productos en linea!"/>
<meta name="robots" content="noodp, noydir"/>
<meta name="author" content="Vestmed Ltda."/>
<meta name="language" content="es"/>
<meta name="documentcountrycode" content="cl"/>
<meta name="google-site-verification" content="d7rA7B2vU2O1cjxRn3EQkYvLPojCNuvwjf8TE1bqE1U" />


<link href="css/layout.css" type="text/css" rel="stylesheet" />
<script type="text/javascript" src="js/mootools-1.2.1-core-yc.js"></script>
<script type="text/javascript" src="js/mootools-1.2-more.js"></script>
<script type="text/javascript" src="dropdown/js/dropdown.js"> </script>

<link type="text/css" rel="stylesheet" href="css/shadowbox.css"/>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.8/jquery.js"></script>
<script src="http://code.jquery.com/ui/1.9.0/jquery-ui.min.js"></script>
<script type="text/javascript" src="js/shadowbox.js"></script>


<link rel="stylesheet" type="text/css" media="screen" href="dropdown/css/uvumi-dropdown.css" />
<!--[if IE]>
<link rel="stylesheet" type="text/css" media="screen" href="dropdown/css/uvumi-dropdown-ie.css" />
<![endif]-->
<script language="JavaScript" src="Include/ValidarDataInput.js" type="text/javascript" ></script>
<script language="JavaScript" src="Include/SoloNumeros.js" type="text/javascript" ></script>
<script language="JavaScript" src="Include/validarRut.js" type="text/javascript" ></script>
<script type="text/javascript">
    new UvumiDropdown('dropdown-scliente');
</script>
<script type="text/javascript">
    var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
    document.write(unescape("%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E"));
</script>
    
<script type="text/javascript">
    try {
        var pageTracker = _gat._getTracker("UA-689052-1");
        pageTracker._trackPageview();
    } catch(err) {}
</script>
    
<script type="text/javascript"> 
    Shadowbox.init({ 
            language: "es", 
             players: ['img', 'html', 'iframe', 'qt', 'wmp', 'swf', 'flv'],
             modal: true
         }); 
</script>    
<!--script type="text/javascript">  
$(document).ready(function(){  
    setTimeout(function() {  
        Shadowbox.open({  
            content:    '<iframe src="avisoferiado.php" width="480px" height="350"></ifreame>',  
            player:     "html",  
            title:      "",  
            width:      492,  
            height:     367  
        });  
    }, 50);  
});  
</script-->    
</head>  
<body>
<div id="body">
	<div id="header"></div>
    <div class="menu">
    	<div id="home-selected" class="selected">home</div>
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
		if ($Cod_Per == 0) { 
	?>
    <ul id="usuario_registro">
		<?php echo solicitar_login(); ?>
    </ul>
	<?php }
		  else {
	?>
    <ul id="usuario_registro">
		<?php 	echo display_login($Cod_Per, $Cod_Clt, $db, 0); ?>
    </ul>
	
	<?php 
		}
	?>
    <div id="work">
    	<div id="back-home">
        	<table>
            	<tr>
                	<td><a id="calidad-telas" href="telas-bioshield.htm"></a></td>
                    <td><a id="nuestras-marcas" href="marcas.htm"></a></td>
                </tr>
                <tr>
                	<td><a id="catalogo-online" href="catalogo.php"></a></td>
                    <td><a id="como-cotizar" href="como-cotizar.htm"></a></td>
                </tr>
            </table>
            <div class="intra">
                <a href="index2.htm"><img src="images/contacto/intra.png" alt=""></img></a>
            </div>
            <div id="planthome"></div>
            <a id="showroom" rev="width: 530px; height: 460px; border: 0 none; scrolling: auto;" title="" rel="lyteframe[mapa]" href="mapa.php">Direccion</a>
		</div>
		<div id="footer"></div>
	</div>
</div>
<script language="javascript" type="text/javascript">
	var f1;	
	f1 = document.F1;	
</script>

<script type="text/javascript" src="http://201.238.209.162/clickheat/js/clickheat.js"></script>
<noscript><p><a href="http://www.labsmedia.com/clickheat/index.html">Open source traffic analysis</a></p></noscript>
<script type="text/javascript">
    <!--clickHeatSite = '';clickHeatGroup = 'index';clickHeatServer = 'http://201.238.209.162/clickheat/click.php';initClickHeat(); //-->
</script>

</body>
</html>
