<?php
//Obtengo los datos de conexion de la base de datos
ini_set('display_errors', '0');
session_start();
include("config.php");

$Cod_Per = 0;
$Cod_Clt = 0;
$RutClt = "";
$xis = 0;
$EsCliente = false;
$bExisteUsr = false;
if (isset($_SESSION['CodPer'])) $Cod_Per = intval($_SESSION['CodPer']);
if (isset($_SESSION['CodClt'])) $Cod_Clt = intval($_SESSION['CodClt']);
if ($Cod_Per > 0) {
	$result = mssql_query ("vm_per_s ".$Cod_Per, $db)
							or die ("No se pudo leer datos del usuario");
	if ($row = mssql_fetch_array($result)) {
		$tip_doc = $row["Cod_TipDoc"];
		$num_doc = $row["Num_Doc"];
	}
	mssql_free_result($result); 
}

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Registro - Vestmed Vestuario M&eacute;dico</title>
<link href="css/layout.css" type="text/css" rel="stylesheet" />
<link href="css/clearfix.css" type="text/css" rel="stylesheet" />
<script type="text/javascript" src="js/mootools-1.2.1-core-yc.js"></script>
<script type="text/javascript" src="js/mootools-1.2-more.js"></script>
<script type="text/javascript" src="dropdown/js/dropdown.js"> </script>
<link rel="stylesheet" type="text/css" media="screen" href="dropdown/css/uvumi-dropdown.css" />

<LINK href="Include/estilos.css" type=text/css rel=stylesheet>
<script language="JavaScript" src="Include/SoloNumeros.js"></script>
<script language="JavaScript" src="Include/ValidarDataInput.js"></script>
<script language="JavaScript" src="Include/validarRut.js"></script>
<script language="JavaScript" src="Include/fngenerales.js"></script>
<script type="text/javascript" src="js/jquery-1.3.2.min.js"></script>
<script type="text/javascript">
	var $j = jQuery.noConflict();
    $j(document).ready
	(
		
		function()
		{
	        $j("form#searchCmn").submit(function(){
				$j.post("ajax-search-cdd.php",{
					search_type: "cdd",
					param_filter: $j("#cmn").val()
				}, function(xml) {
					listLinCdd(xml);
				});return false;
		    });
			
	        //NO SUBMIT TODAVIA $j("form#patternlist-form").submit();
	    }
		
	);
    //TODO: No se esta realizando el post, probablemente debido a que falta el evento submit.
    

    function filterCmn(obj)
    {
		f2.codcmn.value = obj.value;
        //$j("#codcmn").val(obj.value);
        $j("form#searchCmn").submit();
    }
	

    function llenarCdd(obj)
    {
		f2.codcdd.value = obj.value;
		//$j("#.codcdd").val(obj.value);
    }
	

    function listLinCdd(xml)
    {
        options="<select id=\"cdd\" name=\"cdd\" class=\"textfield\" onChange=\"llenarCdd(this)\">\n";
        options+="<option selected value=\"_NONE\">Seleccione una Ciudad</option>\n";
        $j("filter",xml).each(
			function(id) {
	            filter=$j("filter",xml).get(id);
	            options+= "<option value=\""+$j("code",filter).text()+"\">"+$j("value",filter).text()+"</option>\n";
	        }
		);
        options+="</select>";
        $j("#cdd").replaceWith(options);
    }

	function llenarCampo(obj) {
		var campo;
		
		campo=obj.name.substring(0,obj.name.length-2);
		eval("f2."+campo).value = obj.value;
	}
	
	function popwindow(ventana){
	   window.open(ventana,"Anexos",'toolbar=0,location=0,scrollbars=yes,resizable=1,left=60,top=40,width=830,height=600')
	}
	
	function ver_preview(cot) {
		//popwindow("cotizador/preview.php?cot="+cot)
		f2.action="preview_cot.php?cot="+cot;
		f2.submit();
	}
	
	function no_disponible() {
		alert("Cotizaci\u00F3n a\u00FAn no se encuentra disponible");
	}
</script>
<!--[if IE]>
<link rel="stylesheet" type="text/css" media="screen" href="dropdown/css/uvumi-dropdown-ie.css" />
<![endif]-->
<script type="text/javascript">
new UvumiDropdown('dropdown-scliente');

function popwindow(ventana){
   window.open(ventana,"Anexos",'toolbar=0,location=0,scrollbars=yes,resizable=1,left=60,top=40,width=830,height=600')
}
</script>
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
		if ($Cod_Per == 0) { 
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
		<?php 	echo display_login($Cod_Per, $Cod_Clt, $db); ?>
    </ul>
	
	<?php 
		}
	?>
    <div id="work">
		<div id="back-registro2">
			<img src="images/registro/mihistorial.png" style="top:60px;" class="titulo-principal-avisos" />
           	<div style="width:765px; margin:0 auto 0 100px; padding-top:10px;">
				<form ID="F2" method="post" name="F2" AUTOCOMPLETE="on">
				<TABLE WIDTH="95%" BORDER="0" align="center" CELLSPACING="0" CELLPADDING="1" style="margin-top:20px;">
				<TR>
					<TD class="titulo_tabla" width="10%" align="center">Cotizaci&oacute;n</TD>
					<TD class="titulo_tabla" width="20%" style="TEXT-ALIGN:left">Estado</TD>
					<TD class="titulo_tabla" width="20%" style="TEXT-ALIGN:left">Fecha</TD>
					<TD class="titulo_tabla" width="20%" style="TEXT-ALIGN:left">Vigencia</TD>
					<TD class="titulo_tabla" width="30%" style="TEXT-ALIGN:left">Opciones</TD>
				</TR>
				<?php
					$j = 0;
					$iTotPrd = 0;
					
					$result = mssql_query("vm_s_cot_cltweb $Cod_Clt", $db);
					while ($row = mssql_fetch_array($result)) {
						echo "<TR>\n";
						if ($j == 0) {
							$clase1 = ""; // "label_left_right";
							$clase2 = ""; // "dato3";
						}
						else {
							$clase1 = ""; //"label333";
							$clase2 = ""; //"dato33";
						}
						switch($row['is_svc1']*10 + $row['is_svc2']) {
						case 0:
							$servicio = "Sin Servicio";
							break;
						case 1:
							$servicio = "Bordado 2 lineas";
							break;
						case 10:
							$servicio = "Bordado 1 lineas";
							break;
						case 11:
							$servicio = "Bordado 1 y 2 lineas";
							break;
						}
						$despacho = ($row['is_dsp'] == 0) ? "sin despacho" : $row['Dir_Suc'];
						//$comentario = ($row['obs_cot'] == "_NONE") ? "&nbsp;" : $row['obs_cot'];
						$fecha = $row['Fec_Cot'];
						$Cod_Cot = $row['Cod_Cot'];
						echo "   <TD class=\"".$clase1."\" style=\"TEXT-ALIGN:center\">".$row['Num_Cot']."</TD>\n";
						//echo "   <TD class=\"".$clase2."\" style=\"TEXT-ALIGN:left\">".$servicio."</TD>\n";
						if ($row['Est_Res'] == "3")
							echo "   <TD class=\"".$clase2."\" style=\"TEXT-ALIGN:left\">Concluida</TD>\n";
						else
							echo "   <TD class=\"".$clase2."\" style=\"TEXT-ALIGN:left\">En Proceso</TD>\n";
						echo "   <TD class=\"".$clase2."\" style=\"TEXT-ALIGN:left\">".fechafmt($fecha)."</TD>\n";
						echo "   <TD class=\"".$clase2."\" style=\"TEXT-ALIGN:left\">".fechafmt($row['Fec_TerVig'])."</TD>\n";
						//echo "   <TD class=\"".$clase2."\" style=\"TEXT-ALIGN:left\"><a href=\"#\" title=\"".$row['Det_Cna']."\">".substr($row['Det_Cna'],0,50)."</a></TD>\n";
						if ($row['Est_Res'] == "3")
							echo "   <TD class=\"".$clase2."\" style=\"TEXT-ALIGN:left\"><a href=\"javascript:ver_preview($Cod_Cot)\">Ver</a></TD>\n";
						else
							echo "   <TD class=\"".$clase2."\" style=\"TEXT-ALIGN:left\"><a href=\"javascript:no_disponible()\">Ver</a></TD>\n";
						echo "</TR>\n";
						$j = 1 - $j;
						$iTotPrd++;
					}
					mssql_free_result($result);
				?>
				<TR>
					<TD colspan="4" style="PADDING-TOP: 10px; TEXT-ALIGN: right" class="label_top">
					&nbsp;
					</TD>
				</TR>
				</TABLE>
				</form>
			</div>
		</div>
	</div>
	<div id="footer"></div>
<script language="javascript">
	var f1;	
	var f2;
	
	f1 = document.F1;	
	f2 = document.F2;
	
	//<?php echo "// Cod_Nvt= $cod_nvt, Fec_Nvt = $fec_nvt"; ?>
</script>
</body>
</html>
