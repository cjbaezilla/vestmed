<?
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
$accion = (isset($_GET['accion'])) ? intval(ok($_GET['accion'])) : 0;
$idmsg = (isset($_GET['id'])) ? intval(ok($_GET['id'])) : 0;

$Cod_Cot = (isset($_GET['cot']) ? intval(ok($_GET['cot'])) : 0);
$Tip_Bus = (isset($_POST['tipo_bus']) ? ok($_POST['tipo_bus']) : 'P');

if ($Cod_Per > 0) {
	$result = mssql_query ("vm_per_s ".$Cod_Per, $db)
							or die ("No se pudo leer datos del usuario (".$Cod_Per.")");
	if (($row = mssql_fetch_array($result))) {
		$tipo = $row["Cod_TipPer"];
		$sex_ctt = $row["Sex"];
		$tip_doc = $row["Cod_TipDoc"];
		$num_doc = $row["Num_Doc"];
		$nombre  = $row["Pat_Per"]." ".$row["Mat_Per"]." ".$row["Nom_Per"];
		$nom_itt = ""; $email = ""; $fono = ""; 
		
		mssql_free_result($result); 
		
		$result = mssql_query ("vm_usrweb_ctt_s ".$Cod_Per, $db)
								or die ("No se pudo leer datos del usuario contacto (".$Cod_Per.")");
								
		while ($row = mssql_fetch_array($result)) {
			if ($row['Nom_Suc'] != 'MIGRACION') {
				$email = $row['Mail_Ctt'];
				$fono = $row['Fon_Ctt'];
				break;
			}
		}
		
	}
	mssql_free_result($result); 
	
	$result = mssql_query ("vm_cna_sin_res_ctt ".$Cod_Clt, $db)
							or die ("No se pudo leer datos del cliente");
	if (($row = mssql_fetch_array($result))) $tot_cnactt = $row["tot_cna"];
	mssql_free_result($result); 

	$result = mssql_query ("vm_cna_sin_res ".$Cod_Clt, $db)
							or die ("No se pudo leer datos del cliente");
	if (($row = mssql_fetch_array($result))) $tot_cna = $row["tot_cna"];
	mssql_free_result($result); 
}

if ($accion == 21 or $accion == 22) {
	$consulta = str_replace("\'", "''", $_POST['consulta']);
	if ($Cod_Cot == 0) $Cod_Cot = intval(ok($_POST['numcot']));
	if ($Cod_Cot > 0) {
		$result = mssql_query("vm_i_cna $Cod_Cot, $Cod_Clt, $Cod_Per, '$consulta'",$db);
		$accion = 12;
	}
	else {
	    $folio = ok($_GET['folio']);
		$archivo = "";
		$accion = 11;
		/*
		$archivo = $_FILES['documento']['name'];
		if ($archivo != "") {
			$fileupload = $pathadjuntos.$archivo;
			if (!move_uploaded_file($_FILES['documento']['tmp_name'], $fileupload)){
			   echo $_FILES['documento']['tmp_name']."<br>".$fileupload."<br>";
			   echo "Ocurri&oacute; alg&uacute;n error al subir el fichero. No pudo guardarse.";
			   exit(0);
			} 
		}
		*/
		
		$tip_cna = ok($_POST['tipcna']);
		if ($folio == 0) 
                    $result = mssql_query("vm_i_cttweb $tipo, '$nombre', 1, '$num_doc', $sex_ctt, '$nom_itt', '$email', '$fono', $tip_cna, '$consulta ', '$archivo'")
                                              or die ("No pudo actualizar mensaje de contactos");
		else 
                    $result = mssql_query("vm_u_cttweb $folio, 1, '$num_doc', $tip_cna, '$consulta ', '$archivo'")
                                              or die ("No pudo actualizar mensaje de contactos");
			
		//$result = mssql_query($query, $db) or die ("No pudo actualizar mensaje de contactos");
	}
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

<link href="Include/estilos.css" type="text/css" rel="stylesheet" />
<script type="text/javascript" language="JavaScript" src="Include/SoloNumeros.js"></script>
<script type="text/javascript" language="JavaScript" src="Include/ValidarDataInput.js"></script>
<script type="text/javascript" language="JavaScript" src="Include/validarRut.js"></script>
<script type="text/javascript" language="JavaScript" src="Include/fngenerales.js"></script>
<script type="text/javascript" src="js/jquery-1.3.2.min.js"></script>
<script type="text/javascript">
    //*************************************
    var $j = jQuery.noConflict();
    $j(document).ready
	(
		//$j(":input:first").focus();
		
		function()
		{
	        $j("form#searchMsgCot").submit(function(){
				$j.post("ajax-search.php",{
					search_type: "msgcot",
					param_clt: $j("#cod_clt").val(),
					param_cot: $j("#last_cot").val(),
					param_bus: $j("#tipo_bus_cot").val(),
					param_ord: $j("#orden").val()
				}, function(xml) {
					listMsgCot(xml);
				});
				return false;
		    });
			
	        $j("form#searchMsgCtt").submit(function(){
				$j.post("ajax-search.php",{
					search_type: "msgctt",
					param_clt: $j("#cod_clt").val(),
					param_fol: $j("#last_folio").val(),
					param_bus: $j("#tipo_bus_folio").val(),
					param_ord: $j("#ordenfolio").val()
				}, function(xml) {
					listMsgCtt(xml);
				});
				return false;
		    });
	        //NO SUBMIT TODAVIA $j("form#patternlist-form").submit();
	    }
		
	);
        //TODO: No se esta realizando el post, probablemente debido a que falta el evento submit.
	function listMsgCot (xml)
	{
		var tot_filas = 0;
		var atrascot = 0;
		
		options="<table BORDER=\"0\" CELLSPACING=\"1\" CELLPADDING=\"1\" width=\"630\" ALIGN=\"center\" id=\"tblMsgCot\">\n";
		if ($j("#tipo_bus_cot").val() == "P")
		   options+="<tr><td colspan=\"7\" align=\"right\"><a href=\"javascript:listarCot('T')\">TODOS</a> | <a href=\"javascript:listarCot('T')\">ABIERTOS</a> | PENDIENTES</td></tr>\n";
		else if ($j("#tipo_bus_cot").val() == "T")
		   options+="<tr><td colspan=\"7\" align=\"right\">TODOS | <a href=\"javascript:listarCot('A')\">ABIERTOS</a> | <a href=\"javascript:listarCot('P')\">PENDIENTES</a></td></tr>\n";
		else 
		   options+="<tr><td colspan=\"7\" align=\"right\"><a href=\"javascript:listarCot('T')\">TODOS</a> | ABIERTOS | <a href=\"javascript:listarCot('P')\">PENDIENTES</a></td></tr>\n";
                options+="<tr>\n";
		options+="<td width=\"10%\" VALIGN=\"TOP\" ALIGN=\"center\" class=\"titulo_tabla\">Fecha</td>\n";
		options+="<td width=\"10%\" VALIGN=\"TOP\" ALIGN=\"center\" class=\"titulo_tabla\">Grupo</td>\n";
		options+="<td width=\"40%\" VALIGN=\"TOP\" ALIGN=\"left\" class=\"titulo_tabla\">#Cot</td>\n";
		options+="<td width=\"10%\" VALIGN=\"TOP\" ALIGN=\"center\" class=\"titulo_tabla\">Cant.Msg</td>\n";
		options+="<td width=\"10%\" VALIGN=\"TOP\" ALIGN=\"center\" class=\"titulo_tabla\">Historial</td>\n";
		options+="<td width=\"10%\" VALIGN=\"TOP\" ALIGN=\"center\" class=\"titulo_tabla\">Resp</td>\n";
		options+="<td width=\"10%\" VALIGN=\"TOP\" ALIGN=\"center\" class=\"titulo_tabla\">Nuevo</td>\n";
		
		options+="</tr>\n";
                $j("filter",xml).each(
                    function(id) {
                        filter=$j("filter",xml).get(id);
                        //alert ($j("code",filter).text()+"="+$j("value",filter).text());
                        options+= "<tr>\n";
                        options+= "<td align=\"center\" valign=\"top\">"+$j("fecfmtcot",filter).text()+"</td>\n";
                        options+= "<td align=\"center\" valign=\"top\">"+$j("codcot",filter).text()+"</td>\n";
                        options+= "<td align=\"center\" valign=\"top\">Cotizaci\u00f3n # "+$j("numcot",filter).text()+"</td>\n";
                        options+= "<td align=\"center\" valign=\"top\">"+$j("ctd",filter).text()+"</td>\n";

                        lite = "<td align=\"center\" valign=\"top\">\n";
                        lite+= "<a href=\"javascript:popwindow('historico_msj.php?cot="+$j("codcot",filter).text()+"')\">\n";
                        lite+= "<img src=\"images/001_38.gif\" width=\"16px\" height=\"16px\"></a>\n";
                        lite+= "</td>\n";

                        options+=lite;
                        options+="<td align=\"center\" valign=\"top\">";
                        if ($j("tnepdt",filter).text() == "S")
                               options+="<a href=\"javascript:respondercot("+$j("codcot",filter).text()+")\"><img src=\"images/mail.png\" width=\"24px\" height=\"16px\"></a>";
                        else
                               options+="&nbsp;";
                        options+="</td>\n";

                        options+="<td align=\"center\" valign=\"top\">";
                        options+="<a href=\"javascript:Nuevo_Msg(2,"+$j("codcot",filter).text()+")\"><img src=\"images/folder_feed.png\" width=\"16px\" height=\"16px\"></a>";
                        options+="</td>\n";

                        options+= "</tr>\n";
                        codcot = $j("codcot",filter).text();
                        if (atrascot == 0) atrascot = codcot;
                        if ($j("#primera_cot").val() == "0") $j("#primera_cot").val($j("codcot",filter).text());
                        tot_filas++;
                    }
		);
		
		options+="<td style=\"padding-top: 5px\" colspan=\"7\" align=\"right\">\n";
		if (tot_filas >= 18) {
			options+="<input type=\"hidden\" id=\"last_cot\" value=\""+codcot+"\">\n"
		}
		else {
			options+="<input type=\"hidden\" id=\"last_cot\" value=\"_NONE\">\n"
		}	
		
        options+="</table>";
        $j("#tblMsgCot").replaceWith(options);
		$j("#atras_cot").val(atrascot);
	}

	function listMsgCtt (xml)
	{
		var tot_filas = 0;
		var atrasfol = 0;
		var arrTipCna = ["","Informaci\u00f3n del Producto","Reclamos","Contacto Comercial","Solicitud de Catalogos","Informaci\u00f3n de sus Ordenes","Otro"]; 
		
		options="<table BORDER=\"0\" CELLSPACING=\"1\" CELLPADDING=\"1\" width=\"630\" ALIGN=\"center\" id=\"tblMsgCtt\">\n";
		if ($j("#tipo_bus_folio").val() == "P")
		   options+="<tr><td colspan=\"7\" align=\"right\"><a href=\"javascript:listarCtt('T')\">TODOS</a> | PENDIENTES</td></tr>\n";
		else if ($j("#tipo_bus_folio").val() == "T")
		   options+="<tr><td colspan=\"7\" align=\"right\">TODOS | <a href=\"javascript:listarCtt('P')\">PENDIENTES</a></td></tr>\n";
        options+="<tr>\n";
		options+="<td width=\"10%\" VALIGN=\"TOP\" ALIGN=\"center\" class=\"titulo_tabla\">Fecha</td>\n";
		options+="<td width=\"10%\" VALIGN=\"TOP\" ALIGN=\"center\" class=\"titulo_tabla\">Grupo</td>\n";
		options+="<td width=\"40%\" VALIGN=\"TOP\" ALIGN=\"left\" class=\"titulo_tabla\">#Caso</td>\n";
		options+="<td width=\"10%\" VALIGN=\"TOP\" ALIGN=\"center\" class=\"titulo_tabla\">Cant.Msg</td>\n";
		options+="<td width=\"10%\" VALIGN=\"TOP\" ALIGN=\"center\" class=\"titulo_tabla\">Historial</td>\n";
		options+="<td width=\"10%\" VALIGN=\"TOP\" ALIGN=\"center\" class=\"titulo_tabla\">Resp</td>\n";
		options+="<td width=\"10%\" VALIGN=\"TOP\" ALIGN=\"center\" class=\"titulo_tabla\">Nuevo</td>\n";
		options+="</tr>\n";
        $j("filter",xml).each(
			function(id) {
	            filter=$j("filter",xml).get(id);
				//alert ($j("code",filter).text()+"="+$j("value",filter).text());
				options+= "<tr>\n";
	            options+= "<td align=\"center\" valign=\"top\">"+$j("feccna",filter).text()+"</td>\n";
	            options+= "<td align=\"center\" valign=\"top\">"+$j("folctt",filter).text()+"</td>\n";
	            options+= "<td align=\"left\" valign=\"top\">"+arrTipCna[$j("tipcna",filter).text()]+"</td>\n";
	            options+= "<td align=\"center\" valign=\"top\">"+$j("ctd",filter).text()+"</td>\n";
					
				lite = "<td align=\"center\" valign=\"top\">\n";
				lite+= "<a href=\"javascript:popwindow('historico_msj.php?folctt="+$j("folctt",filter).text()+"&clt=<?php echo $Cod_Clt; ?>')\">\n";
				lite+= "<img src=\"images/001_38.gif\" width=\"16px\" height=\"16px\"></a>\n";
				lite+= "</td>\n";
				
				options+=lite;
				options+="<td align=\"center\" valign=\"top\">";
				if ($j("tnepdt",filter).text() == "S")
   				   options+="<a href=\"javascript:responderctt("+$j("folctt",filter).text()+")\"><img src=\"images/mail.png\" width=\"24px\" height=\"16px\"></a>";

				else
				   options+="&nbsp;";
				options+="</td>\n";
				
				options+="<td align=\"center\" valign=\"top\">";
			    options+="<a href=\"javascript:Nuevo_Msg(1,"+$j("folctt",filter).text()+")\"><img src=\"images/folder_feed.png\" width=\"16px\" height=\"16px\"></a>";
				options+="</td>\n";


				options+= "</tr>\n";
				folctt = $j("folctt",filter).text();
				if (atrasfol == 0) atrasfol = folctt;
				if ($j("#primer_folio").val() == "0") $j("#primer_folio").val($j("folctt",filter).text());

				tot_filas++;
	        }
		);
		
		options+="<td style=\"padding-top: 5px\" colspan=\"6\" align=\"right\">\n";
		if (tot_filas >= 18) {
			options+="<input type=\"hidden\" id=\"last_folio\" value=\""+folctt+"\">\n"
		}
		else {
			options+="<input type=\"hidden\" id=\"last_folio\" value=\"_NONE\">\n"
		}	
		
        options+="</table>";
        $j("#tblMsgCtt").replaceWith(options);
		$j("#atras_folio").val(atrasfol);
	}
	
	function Next_MsgCtt() {
		$j("#ordenfolio").val("1");
		if ($j("#last_folio").val() == "_NONE") 
			alert ("No existen mas mensajes que mostrar");
		else
			$j("form#searchMsgCtt").submit();
	}
	
	function Previus_MsgCtt() {
	    $j("#ordenfolio").val("2");
		if ($j("#atras_folio").val() == $j("#primer_folio").val())		
			alert ("No existen mas mensajes que mostrar");
		else {
		    $j("#last_folio").val($j("#atras_folio").val());
			$j("form#searchMsgCtt").submit();
		}
	}
	
	function Next_MsgCot() {
		$j("#orden").val("1");
		if ($j("#last_cot").val() == "_NONE") 
			alert ("No existen mas mensajes que mostrar");
		else
			$j("form#searchMsgCot").submit();
	}
	
	function Previus_MsgCot() {
	    $j("#orden").val("2");
		if ($j("#atras_cot").val() == $j("#primera_cot").val())		
			alert ("No existen mas mensajes que mostrar");
		else {
		    $j("#last_cot").val($j("#atras_cot").val());
			$j("form#searchMsgCot").submit();
		}
	}
	
	function listarCot(caso) {
		$j("#orden").val("1");
		$j("#last_cot").val("10000000");
		$j("#tipo_bus_cot").val(caso);
		$j("#tipo_bus").val(caso);
		$j("#primera_cot").val("0");
		$j("form#searchMsgCot").submit();
	}
	
	function listarCtt(caso) {
		$j("#ordenfolio").val("1");
		$j("#last_folio").val("10000000");
		$j("#tipo_bus_folio").val(caso);
		$j("#tipo_bus").val(caso);
		$j("#primer_folio").val("0");
		$j("form#searchMsgCtt").submit();

	}
	
	//*************************************
	
	function Enviar_Res(folio,cot) {
		if (eval('f2.respuesta'+folio).value == "") {
			alert("Debe ingresar una respuesta");
			return false;
		}
		if (eval('f2.respuesta'+folio).value.length > 1000) {
			alert("El mensaje debe contener a los mas 1.000 caracteres.");
			return false;
		}
		f2.action = "mismensajes.php?cot="+cot+"&folio="+folio+"&accion=respuesta";
		f2.submit();
	}
	
	function filterCot(obj) {
		f2.NumCot.value = obj.value;
	}
	
	function Nuevo_Msg(caso,id) {
		f2.action = "mismensajes.php?accion="+caso+"&id="+id;
		f2.submit();
	}
	
	function Salir(caso) {
		f2.action = "mismensajes.php?accion="+caso;
		f2.submit();
	}
	
	function checkDataForm(form,cot) {
		if (cot == 0)
			if (form.numcot.value == "_NONE")
			{
				alert ("Debe indicar una cotizaci\u00f3n ...");
				return false;
			}
			
		if (form.consulta.value == "") {
			alert("Debe ingresar una consulta ...");
			return false;
		}
		
		if (form.consulta.value.length > 1000)
		{
			alert("El mensaje debe contener a los mas 1.000 caracteres.");
			return false;
		}
		return true;
	}
	
	function Show_Cotizaciones () 
	{
		$j("#formulario_contacto").hide();
		$j("#formulario_cotizaciones").show("slow");
		$j("#btnCotizaciones").removeClass("btn3");
		$j("#btnCotizaciones").addClass("btn4");
		$j("#btnContactos").removeClass("btn4");
		$j("#btnContactos").addClass("btn3");
	}
	
	function Show_Contactos ()
	{
		$j("#formulario_cotizaciones").hide();
		$j("#formulario_contacto").show("slow");
		$j("#btnCotizaciones").removeClass("btn4");
		$j("#btnCotizaciones").addClass("btn3");
		$j("#btnContactos").removeClass("btn3");
		$j("#btnContactos").addClass("btn4");
	}
	
	function respondercot(cot) {
		f2.action = "mismensajes2.php?cot="+cot;
		f2.submit();
	}
	
	function responderctt(folio) {
		f2.action = "mismensajes2.php?folctt="+folio;
		f2.submit();
	}

	
</script>
<!--[if IE]>
<link rel="stylesheet" type="text/css" media="screen" href="dropdown/css/uvumi-dropdown-ie.css" />
<![endif]-->
<script type="text/javascript">
new UvumiDropdown('dropdown-scliente');

function popwindow(ventana){
   window.open(ventana,"Anexos",'toolbar=0,location=0,scrollbars=yes,titlebar=no,menubar=no,resizable=0,left=100,top=100,width=640,height=385')
}
</script>
<!-- Lytebox Includes //-->
<script type="text/javascript" src="lytebox/lytebox.js"></script>
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
		if ($Cod_Per == 0) { 
	?>
    <ul id="usuario_registro">
        <form ID="F1" method="POST" name="F1" action="">
    	<li class="back-verde registro"><a href="registrarse.php">REGISTRARSE</a></li>
        <li class="olvido"><a href="javascript:EnviarClave()">OLVID&Oacute; SU CLAVE?</a></li>
        <li class="back-verde"><a href="javascript:ValidarLogin()">ENTRAR</a></li>
        <li class="back-verde inputp"><input type="password" name="dfclave"/></li>
        <li class="back-verde">CONTRASE&Ntilde;A</li>
        <li class="back-verde inputp"><input type="" name="rut" id="rut" onblur="formatearRut('rut','dfrut')" /></li>
        <li class="back-verde">RUT</li>
		<input type="hidden" name="dfrut" id="dfrut" />
		</form>
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
		<div id="back-registro3">
		<img src="images/registro/mihistorial.png" style="top:60px;" class="titulo-principal-avisos" alt="" />
           	<div style="width:765px; margin:0 auto 0 100px; padding-top:10px;">
			    <?php if ($accion == 0 or $accion == 11 or $accion == 12) { ?>
				<h1>Nuevos Mensajes Recibidos</h1>
				<table BORDER="0" CELLSPACING="1" CELLPADDING="3" width="630" ALIGN="center">
				<tr>
					<td width="100%" VALIGN="TOP" class="dato" style="PADDING-TOP: 10px">Msg Formulario Contacto: (<a href="javascript:listarCtt('P');Show_Contactos();"><?php echo $tot_cnactt; ?></a>)</td>
				</tr>
				<tr>
					<td width="100%" VALIGN="TOP" class="dato">Msg Cotizaciones (<a href="javascript:listarCot('P');Show_Cotizaciones();"><?php echo $tot_cna; ?></a>)</td>
				</tr>
				</table>
				<br />
				<h3>Historial de Mensajes</h3>
				<table BORDER="0" CELLSPACING="1" CELLPADDING="3" width="500" ALIGN="center">
				<tr>
					<td width="50%" VALIGN="TOP" class="dato" style="PADDING-TOP: 10px; TEXT-ALIGN: center">
					<input type="button" name="btnCotizaciones" id="btnCotizaciones" value=" Formulario Cotizaciones " class="<?php echo ($accion == 12) ? "btn4" : "btn3"; ?>" onclick="Show_Cotizaciones()" />
					</td>
					<td width="50%" VALIGN="TOP" class="dato" style="PADDING-TOP: 10px; TEXT-ALIGN: center">
					<input type="button" name="btnContactos" id="btnContactos" value=" Formulario Contacto " class="<?php echo ($accion == 11) ? "btn4" : "btn3"; ?>" onclick="Show_Contactos()" />
					</td>
				</tr>
				</table>
				<div style="position:relative;width:670px;height:470px; overflow:auto; left: 70px; top: 0px;">
					<div id="formulario_contacto">
						<h3>Msg. Formulario Contacto (<?php echo $tot_cnactt; ?>)</h3>
						<form ID="searchMsgCtt" name="searchMsgCtt" action="">
						<div style="position:relative;width:650px;height:390px; overflow:auto; left: 0px; top: 0px;">
						<table BORDER="0" CELLSPACING="1" CELLPADDING="1" width="630" ALIGN="center" id="tblMsgCtt">
						<tr><td colspan="7" align="right">
							<?php if ($Tip_Bus != "T") { ?><a href="javascript:listarCtt('T')">TODOS</a><?php } else { ?>TODOS<?php } ?> | 
							<?php if ($Tip_Bus != "P") { ?><a href="javascript:listarCtt('P')">PENDIENTES</a><?php } else { ?>PENDIENTES<?php } ?>
						</td></tr>
						<tr>
							<td width="10%" VALIGN="TOP" ALIGN="center" class="titulo_tabla">Fecha</td>
							<td width="10%" VALIGN="TOP" ALIGN="center" class="titulo_tabla">Grupo</td>
							<td width="40%" VALIGN="TOP" ALIGN="left"   class="titulo_tabla">#Caso</td>
							<td width="10%" VALIGN="TOP" ALIGN="center" class="titulo_tabla">Cant.Msg</td>
							<td width="10%" VALIGN="TOP" ALIGN="center" class="titulo_tabla">Historial</td>
							<td width="10%" VALIGN="TOP" ALIGN="center" class="titulo_tabla">Resp</td>
							<td width="10%" VALIGN="TOP" ALIGN="center" class="titulo_tabla">Nuevo</td>
						</tr>
								<?php 
									$result = mssql_query("vm_msgctt_clt $Cod_Clt, 1, '$Tip_Bus', 1000000", $db);
									$totfilas = 0;
									$primer_folio = 0;
									$tipo = split ("/", "/Informaci&oacute;n del Producto/Reclamos/Contacto Comercial/Solicitud de Catalogos/Informaci&oacute;n de sus Ordenes/Otro");
									while ($row = mssql_fetch_array($result)) {
										$totfilas++;
										$last_folio = $row['Fol_CttWeb'];
										if ($primer_folio == 0) $primer_folio = $last_folio;
								?>
										<tr>
											<td align="center" valign="top"><?php echo $row['Fec_Ctt']; ?></td>
											<td align="center" valign="top"><?php echo $row['Fol_CttWeb']; ?></td>
											<td align="left" valign="top"><?php echo $tipo[$row['Tip_Cna']]; ?></td>
											<td align="center" valign="top"><?php echo $row['Ctd'] ?></td>
											<td align="center" valign="top">
											<a href="javascript:popwindow('historico_msj.php?folctt=<?php echo $row['Fol_CttWeb']; ?>&clt=<?php echo $Cod_Clt ?>')"><img src="images/001_38.gif" width="16px" height="16px" alt="" /></a>
											</td>
											<td align="center" valign="top">
											<?php if ($row['Tne_Pen'] == 'S') { ?>
											<a href="javascript:responderctt(<?php echo $row['Fol_CttWeb']; ?>)"><img src="images/mail.png" width="24px" height="16px" alt="" /></a>
											<?php } else { ?>
											&nbsp;
											<?php } ?>
											</td>
											<td align="center" valign="top">
											<a href="javascript:Nuevo_Msg(1,<?php echo $row['Fol_CttWeb']; ?>)"><img src="images/folder_feed.png" width="16px" height="16px" alt="" /></a>
											</td>
										</tr>
								<?php
									}
								?>
						<tr>
						<td style="padding-top: 5px" colspan="7" align="right">
						<input type="hidden" id="last_folio" value="<?php echo $last_folio; ?>" />
						</td>
						</tr>
						</table>
						</div>
						<div style="text-align: right; position:relative;width:650px; overflow:auto; left: 0px; top: 0px;">
						<table border="0" CELLSPACING="1" CELLPADDING="1" width="100%" ALIGN="center">
						<tr>
						<td width="50%" align="left">
						<input type="button" name="btnNuevaCtt" id="btnNuevaCtt" value=" Nuevo Caso " class="btn" onclick="javascript:Nuevo_Msg(1,0)">
						<input type="hidden" id="primer_folio" value="<?php echo $primer_folio; ?>">
						<input type="hidden" id="atras_folio" value="<?php echo $primer_folio; ?>">
						<input type="hidden" id="ordenfolio" value="">
						</td>
						<td width="50%" align="right">
						<a href="javascript:Previus_MsgCtt()"><img src="images/arrow1_w.gif"></a> &nbsp; <a href="javascript:Next_MsgCtt()"><img src="images/arrow1_e.gif"></a>
						</td>
						</tr>
						</table>
						</div>				
						</form>
					</div>
					<div id="formulario_cotizaciones">
						<H3>Msg. Cotizaciones (<?php echo $tot_cna; ?>)</H3>
						<form ID="searchMsgCot" name="searchMsgCot">
						<div style="position:relative;width:650px;height:390px; overflow:auto; left: 0px; top: 0px;">
						<table BORDER="0" CELLSPACING="1" CELLPADDING="1" width="630" ALIGN="center" id="tblMsgCot">
						<tr><td colspan="7" align="right">
							<?php if ($Tip_Bus != "T") { ?><a href="javascript:listarCot('T')">TODOS</a><?php } else { ?>TODOS<?php } ?> | 
							<?php if ($Tip_Bus != "A") { ?><a href="javascript:listarCot('A')">ABIERTOS</a><?php } else { ?>ABIERTOS<?php } ?> | 
							<?php if ($Tip_Bus != "P") { ?><a href="javascript:listarCot('P')">PENDIENTES</a><?php } else { ?>PENDIENTES<?php } ?>
							</td></tr>
						<tr>
							<td width="10%" VALIGN="TOP" ALIGN="center" class="titulo_tabla">Fecha</td>
							<td width="10%" VALIGN="TOP" ALIGN="center" class="titulo_tabla">Grupo</td>
							<td width="40%" VALIGN="TOP" ALIGN="center" class="titulo_tabla">#Cot</td>
							<td width="10%" VALIGN="TOP" ALIGN="center" class="titulo_tabla">Cant.Msg</td>
							<td width="10%" VALIGN="TOP" ALIGN="center" class="titulo_tabla">Historial</td>
							<td width="10%" VALIGN="TOP" ALIGN="center" class="titulo_tabla">Resp</td>
							<td width="10%" VALIGN="TOP" ALIGN="center" class="titulo_tabla">Nuevo</td>
						</tr>
								<?php 
									$result = mssql_query("vm_newmsg_clt $Cod_Clt, 1, '$Tip_Bus', 10000000", $db);
									$bOkRespuesta = false;
									$totfilas = 0;
									$primera_fila = 0;
									$primera_cot = 0;
									while ($row = mssql_fetch_array($result)) {
										$last_cot = $row['Cod_Cot'];
										if ($primera_cot == 0) $primera_cot = $last_cot;
										if ($primera_fila == 0) $primera_fila = $last_cot; 
										$totfilas++;
								?>
										<tr>
											<td align="center" valign="top"><?php echo $row['FecFmt_Cot']; ?></td>
											<td align="center" valign="top"><?php echo $row['Cod_Cot']; ?></td>
											<td align="center" valign="top">Cotizaci&oacute;n # <?php echo $row['Num_Cot']; ?></td>
											<td align="center" valign="top"><?php echo $row['Ctd']; ?></td>
											<td align="center" valign="top">
											<a href="javascript:popwindow('historico_msj.php?cot=<?php echo $row['Cod_Cot']; ?>')"><img src="images/001_38.gif" width="16px" height="16px"></a>
											</td>
											<td align="center" valign="top">
											<?php if ($row['Tne_Pen'] == 'S') { ?>
											<a href="javascript:respondercot(<?php echo $row['Cod_Cot']; ?>)"><img src="images/mail.png" width="24px" height="16px"></a>
											<?php } else { ?>
											&nbsp;
											<?php } ?>
											</td>
											<td align="center" valign="top">
											<a href="javascript:Nuevo_Msg(2,<?php echo $row['Cod_Cot']; ?>)"><img src="images/folder_feed.png" width="16px" height="16px"></a>
											</td>
										</tr>
								<?php
									}
								?>
						<tr>
						<td style="padding-top: 5px" colspan="7" align="right">
						<input type="hidden" id="last_cot" value="<?php echo $last_cot; ?>">
						</td>
						</tr>
						</table>
						</div>
						<div style="text-align: right; position:relative;width:650px; overflow:auto; left: 0px; top: 0px;">
						<table border="0" CELLSPACING="1" CELLPADDING="1" width="100%" ALIGN="center">
						<tr>
						<td width="50%" align="left">
						<input type="button" name="btnNuevaCot" id="btnNuevaCot" value=" Nuevo Caso " class="btn" onclick="javascript:Nuevo_Msg(2,0)">
						<input type="hidden" id="atras_cot" value="<?php echo $primera_cot; ?>">
						<input type="hidden" id="orden" value="">
						</td>
						<td width="50%" align="right">
						<a href="javascript:Previus_MsgCot()"><img src="images/arrow1_w.gif"></a> &nbsp; <a href="javascript:Next_MsgCot()"><img src="images/arrow1_e.gif"></a>
						</td>
						</tr>
						</table>
						</div>
						</form>
						<form ID="F2" method="post" name="F2" ACTION="mismensajes.php?accion=1" >
						<input type="hidden" id="cod_clt" value="<?php echo $Cod_Clt; ?>">
						<input type="hidden" id="primera_cot" name="primera_cot" value="<?php echo $primera_cot; ?>">
						<input type="hidden" id="tipo_bus_cot" name="tipo_bus_cot" value="<?php echo $Tip_Bus ?>">
						<input type="hidden" id="tipo_bus_folio" name="tipo_bus_folio" value="<?php echo $Tip_Bus ?>">
						<input type="hidden" id="tipo_bus" name="tipo_bus" value="<?php echo $Tip_Bus ?>">
						</form>
					</div>
				</div>
			    <?php } elseif ($accion == 1) { ?>
				<form ID="F2" method="post" name="F2" ACTION="mismensajes.php?accion=21&folio=<?php echo $idmsg; ?>" onsubmit="return checkDataForm(this,<?php echo $idmsg; ?>)" AUTOCOMPLETE="on" enctype="multipart/form-data" >
				<H1>Nuevo Mensaje</H1>
				<table BORDER="0" CELLSPACING="1" CELLPADDING="5" width="650" height="400" ALIGN="center">
				<tr>
					<td width="30%" VALIGN="TOP" class="dato" style="PADDING-TOP: 20px"><b>Tipo Mensaje</b></td>
					<td width="30%" VALIGN="TOP" class="dato" style="PADDING-TOP: 20px" colspan="2">Msg. Formulario Contacto</td>
				</tr>
				<tr>
					<td width="30%" VALIGN="TOP" class="dato"><b>Grupo Mensaje</b></td>
					<td width="30%" VALIGN="TOP" class="dato">
					<?php if ($idmsg == 0) { ?>
					    Nuevo
					<?php } else { 
							$result = mssql_query("vm_s_cttweb $idmsg", $db);
							if ($row = mssql_fetch_array($result)) {
								echo $idmsg;
								$cod_tipcna = $row['Tip_Cna'];
							}
						  } 
					?>
					</td>
					<td width="40%" VALIGN="TOP" class="dato">&nbsp;</td>
				</tr>
				<tr>
					<td width="30%" VALIGN="TOP" class="dato"><b>Tipo Consulta</b></td>
					<td width="30%" VALIGN="TOP" class="dato">
					<?php if ($idmsg == 0) { ?>
					<select class="select-contacto" name="tipcna">
					<option selected value="_NONE">Seleccione tipo de Consulta</option>
                                        <option value="1">Informaci&oacute;n del Producto</option>
                                        <option value="2">Reclamos</option>
                                        <option value="3">Contacto Comercial</option>
                                        <option value="4">Solicitud de Catalogos</option>
                                        <option value="5">Informaci&oacute;n de sus Ordenes</option>
                                        <option value="6">Otro</option>
					</select>
					<?php } else {
                                                    $aTipCna   = array(1 => "Informaci&oacute;n del Producto",
                                                                                           2 =>  "Reclamos",
                                                                                           3 =>  "Contacto Comercial",
                                                                                           4 =>  "Solicitud de Catalogos",
                                                                                           5 =>  "Informaci&oacute;n de sus Ordenes",
                                                                                           6 =>  "Otro");
                                                        echo $aTipCna[$cod_tipcna];
					?>
						<input type="hidden" name="tipcna" value="<?php echo $cod_tipcna ?>" />
					<?php
					     } 
                                        ?>
					</td>
					<td width="40%" VALIGN="TOP" class="dato">&nbsp;</td>
				</tr>
				<tr>
					<td width="30%" VALIGN="TOP" class="dato"><b>Nuevo Mensaje</b></td>
					<td width="70%" VALIGN="TOP" class="dato" colspan="2">
					<textarea class="textfieldv2" rows="5" cols="100" name="consulta"></textarea>
					</td>
				</tr>
				<tr>
					<td width="100%" VALIGN="TOP" class="dato" colspan="3" style="text-align: right">
					<input type="button" name="Volver" value=" Volver " class="btn" onclick="Salir(11)" />&nbsp;&nbsp;
					<input type="submit" name="Enviar" value=" Enviar " class="btn" />
					<input type="hidden" name="tipo_bus" value="<?php echo $Tip_Bus; ?>" />
					</td>
				</tr>
				</table>
				</form>
			    <?php } elseif ($accion == 2) { ?>
				<form ID="F2" method="post" name="F2" ACTION="mismensajes.php?accion=22&cot=<?php echo $idmsg; ?>" onsubmit="return checkDataForm(this,<?php echo $idmsg; ?>)" AUTOCOMPLETE="on" enctype="multipart/form-data" >
				<H1>Nuevo Mensaje</H1>
				<table BORDER="0" CELLSPACING="1" CELLPADDING="5" width="650" height="400" ALIGN="center">
				<tr>
					<td width="30%" VALIGN="TOP" class="dato" style="PADDING-TOP: 20px"><b>Tipo Mensaje</b></td>
					<td width="30%" VALIGN="TOP" class="dato" style="PADDING-TOP: 20px" colspan="2">Msg. Cotizaciones</td>
				</tr>
				<tr>
					<td width="30%" VALIGN="TOP" class="dato"><b>Cotizaci&oacute;n</b></td>
					<td width="30%" VALIGN="TOP" class="dato">
					<?php if ($idmsg == 0) { ?>
					<select class="select-contacto" name="numcot">
					<option selected value="_NONE">Seleccione Cotizaci&oacute;n</option>
					<?php
						$result = mssql_query("vm_cmbmsj_cot $Cod_Clt", $db);
						while ($row = mssql_fetch_array($result)) echo "<option value=\"".$row['cod_cot']."\">".$row['num_cot']."</option>\n";						
					?>
					</select>
					<?php } else { 
							$result = mssql_query("vm_s_cothdr $idmsg",$db);
							if ($row = mssql_fetch_array($result)) echo $row['Num_Cot'];
						  } 
					?>
					</td>
					<td width="40%" VALIGN="TOP" class="dato">&nbsp;</td>
				</tr>
				<tr>
					<td width="30%" VALIGN="TOP" class="dato"><b>Nuevo Mensaje</b></td>
					<td width="70%" VALIGN="TOP" class="dato" colspan="2">
					<textarea class="textfieldv2" rows="5" cols="100" name="consulta"></textarea>
					</td>
				</tr>
				<tr>
					<td width="100%" VALIGN="TOP" class="dato" colspan="3" style="text-align: right">
					<input type="button" name="Volver" value=" Volver " class="btn" onclick="Salir(12)" />&nbsp;&nbsp;
					<input type="submit" name="Enviar" value=" Enviar " class="btn" />
					<input type="hidden" name="tipo_bus" value="<?php echo $Tip_Bus; ?>" />
					</td>
				</tr>
				</table>
				</form>

				<?php } ?>
				<br />
			</div>
		</div>
	</div>
	<div id="footer"></div>
</div>
<script type="text/javascript" language="javascript">
	var f1;	
	var f2;
	
	f1 = document.F1;	
	f2 = document.F2;
	
    <?php if ($accion == 0) { ?>
	Show_Cotizaciones();
	<?php } elseif ($accion == 12) { ?>
	$j("#formulario_cotizaciones").show();
	$j("#formulario_contacto").hide();
	<?php } elseif ($accion == 11) { ?>
	$j("#formulario_cotizaciones").hide();
	$j("#formulario_contacto").show();
	<?php } ?>
	
	//<?php echo "// Cod_Nvt= $cod_nvt, Fec_Nvt = $fec_nvt"; ?>
</script>
</body>
</html>
