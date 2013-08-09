<?php
//Obtengo los datos de conexion de la base de datos
ini_set('display_errors', '0');
session_start();
include("config.php");

$Cod_Per = 0;
$Cod_Clt = 0;
$RutClt = "";
$RutPer = "";
$xis = 0;
$EsCliente = false;
$bExisteUsr = false;
if (isset($_SESSION['CodPer'])) $Cod_Per = intval($_SESSION['CodPer']);
if (isset($_SESSION['CodClt'])) $Cod_Clt = intval($_SESSION['CodClt']);
if (isset($_GET['clt'])) {
	$RutClt = ok($_GET['clt']);
	$xis    = intval(ok($_GET['xis']));
	$CodPerCtt = 0;
	if ($xis == 1) { // Caso existe como Persona en Vestmed
		$doc_id = 1;
		//$query = "vm_s_per_tipdoc ".$doc_id.", '".$RutClt."'";
		$result = mssql_query ("vm_s_per_tipdoc ".$doc_id.", '".$RutClt."'", $db)	or die ("No se pudo leer datos del Cliente");
		if ($row = mssql_fetch_array($result)) {
			$cod_tipper = $row["Cod_TipPer"];
			$Cod_Clt = $row["Cod_Clt"];
			$id_per	 = $row["Cod_Per"];
			mssql_free_result($result); 

			if ($Cod_Clt != "") { // Si es cliente
				$EsCliente = true;
				$bPrimero = true;
				$bXisCtt = false;
				//$query = "vm_s_cttclt ".$Cod_Clt;
				$result = mssql_query ("vm_s_cttclt ".$Cod_Clt, $db)
								or die ("No se pudo leer datos del Contacto (".$Cod_Clt.")");
				while ($row = mssql_fetch_array($result))
					if ($bPrimero || $row["Pwd_Web"] != "") {
					    $CodPerCtt = $row["Cod_Per"];
						$RutPer    = $row["Num_Doc"];
						$AppPat    = utf8_encode($row["Pat_Per"]);
						$AppMat    = utf8_encode($row["Mat_Per"]);
						$NomPer    = utf8_encode($row["Nom_Per"]);
						$CodPro    = $row["Cod_Pro"];
						$CodEsp    = $row["Cod_Esp"];
						$CodSex    = $row["Sex"];
						$DirSuc    = utf8_encode($row["Dir_Suc"]);
						$CodCmn    = $row["Cod_Cmn"];
						$CodCdd    = $row["Cod_Cdd"];
						$FonSuc    = $row["Fon_Suc"];
						$FaxSuc    = $row["Fax_Suc"];
						$CelCtt    = $row["Cel_Ctt"];
						$MailCt    = $row["Mail_Ctt"];
						$clave 	   = $row["Pwd_Web"];
						$bPrimero  = false;
						$bXisCtt   = true;
						if ($clave != "") {
							$bExisteUsr = true;
							break;
						}
					}
			}
			else { // Existe como persona pero no es cliente
				//$query = "vm_per_s ".$id_per;
				$result = mssql_query ("vm_per_s ".$id_per, $db)
								or die ("No se pudo leer datos de la Persona (".$id_per.")");
				if ($row = mssql_fetch_array($result)) {
					$RutPer = $row["Num_Doc"];
					$AppPat = utf8_encode($row["Pat_Per"]);
					$AppMat = utf8_encode($row["Mat_Per"]);
					$NomPer = utf8_encode($row["Nom_Per"]);
					$CodSex = $row["Sex"];
					mssql_free_result($result);
				}
			}
			mssql_free_result($result); 
			
			//$query = "vm_pro_s ".$CodPro;
			$result = mssql_query ("vm_pro_s ".$CodPro, $db)
							or die ("No se pudo leer Codigo de Profesion");
			if ($row = mssql_fetch_array($result))	$NomPro = utf8_encode($row["Nom_Pro"]);
			mssql_free_result($result); 
			
			//$query = "vm_esp_s ".$CodEsp;
			$result = mssql_query ("vm_esp_s ".$CodEsp, $db)
							or die ("No se pudo leer Codigo de Especialidad");
			if ($row = mssql_fetch_array($result))	$NomEsp = utf8_encode($row["Nom_Esp"]);
			mssql_free_result($result); 

			$NomCmn = "";
			//$query = "vm_cmn_s ".$CodCmn;
			$result = mssql_query ("vm_cmn_s ".$CodCmn, $db)
							or die ("No se pudo leer Codigo de la Comuna");
			if ($row = mssql_fetch_array($result))	$NomCmn = utf8_encode($row["Nom_Cmn"]);
			mssql_free_result($result); 
			
			$NomCdd = "";
			//$query = "vm_cdd_s ".$CodCdd;
			$result = mssql_query ("vm_cdd_s ".$CodCdd, $db)
							or die ("No se pudo leer Codigo de la Ciudad");
			if ($row = mssql_fetch_array($result))	$NomCdd = utf8_encode($row["Nom_Cdd"]);
			mssql_free_result($result); 
			
		}
		else {
			// Resultado incoherente puesto que se informó que existía 
		}
	}
	else { // Caso NO existe como Persona en Vestmed
	    $campos = split ("-", $RutClt);
		$nRut = intval($campos[0]);
		if ($nRut > 50000000) {
			$CodPro = "-1";
			$cod_tipper = 2;
		}
		else {
			$RutPer = $RutClt;
			$RutClt = "";
			$CodPro = "";
			$cod_tipper = 1;
		}
	}
}	
?>
<!DOCTYPE html PUBLIC "-//W3C//Dtd XHTML 1.0 transitional//EN" "http://www.w3.org/tr/xhtml1/Dtd/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Catalogo - Vestmed Vestuario M&eacute;dico</title>
<link href="css/layout.css" type="text/css" rel="stylesheet" />
<script type="text/javascript" src="js/mootools-1.2.1-core-yc.js"></script>
<script type="text/javascript" src="js/mootools-1.2-more.js"></script>
<script type="text/javascript" src="dropdown/js/dropdown.js"> </script>
<link rel="stylesheet" type="text/css" media="screen" href="dropdown/css/uvumi-dropdown.css" />
<!--[if IE]>
<link rel="stylesheet" type="text/css" media="screen" href="dropdown/css/uvumi-dropdown-ie.css" />
<![endif]-->
<script type="text/javascript">
new UvumiDropdown('dropdown-scliente');
</script>
<LINK href="Include/estilos.css" type=text/css rel=stylesheet>
<script language="JavaScript" src="Include/SoloNumeros.js"></script>
<script language="JavaScript" src="Include/ValidarDataInput.js"></script>
<script language="JavaScript" src="Include/fngenerales.js"></script>
<script language="JavaScript" src="Include/validarRut.js"></script>
<script type="text/javascript" src="js/jquery-1.3.2.min.js"></script>
<script type="text/javascript">
	var $j = jQuery.noConflict();
    $j(document).ready
	(
		//$j(":input:first").focus();
		
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
			
	        $j("form#searchPro").submit(function(){
				$j.post("ajax-search-esp.php",{
					search_type: "esp",
					param_filter: $j("#pro").val()
				}, function(xml) {
					listLinEsp(xml);
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
	
    function filterPro(obj)
    {
		f2.codpro.value = obj.value;
        //$j("#.codpro").val(obj.value);
        $j("form#searchPro").submit();
    }
	
    function llenarCdd(obj)
    {
		f2.codcdd.value = obj.value;
		//$j("#.codcdd").val(obj.value);
    }
	
    function llenarEsp(obj)
    {
		f2.codesp.value = obj.value;
		//$j("#.codesp").val(obj.value);
    }
	
    function listLinCdd(xml)
    {
        options="<select id=\"cdd\" name=\"cdd\" class=\"textfieldv2\" onChange=\"llenarCdd(this)\">\n";
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

    function listLinEsp(xml)
    {
        options="<select id=\"esp\" name=\"esp\" class=\"textfieldv2\" onChange=\"llenarEsp(this)\">\n";
        options+="<option selected value=\"_NONE\">Seleccione una Especialidad</option>\n";
        $j("filter",xml).each(
			function(id) {
	            filter=$j("filter",xml).get(id);
	            options+= "<option value=\""+$j("code",filter).text()+"\">"+$j("value",filter).text()+"</option>\n";
	        }
		);
        options+="</select>";
        $j("#esp").replaceWith(options);
    }

	function llenarCampo(obj) {
		var campo;
		
		campo=obj.name.substring(0,obj.name.length-2);
		eval("f2."+campo).value = obj.value;
	}
</script>
</head>
<body>
<div id="body">
	<div id="header"></div>
    <div id="menu-noselect" class="menu">
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
                            <a href="tracking-ordenes.htm">tracking de &Oacute;rdenes</a>
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
		<?php 	echo display_login($Cod_Per, $Cod_Clt, $db); ?>
    </ul>
	
	<?php 
		}
	?>
   <div id="work">
		<div id="back-registro">
			<div style="width:765px; margin:0 auto 0 160px; padding-top:10px;">
			<table WIDTH="80%" BORDER="0" style="text-align:left;">
				<tr>
				  <td width="80%" VALIGN="top">
					 <table border="0" cellpadding="1" cellspacing="0" width="100%" >
						<tr><td colspan="2"><h1 class="borde-abajo titulo-registro">Formulario <span>de registro</span></h1></td></tr>
						<tr><td colspan="2"><span class="titulo-pequeno">Registrate y podras disfrutar de todos los beneficios  que te entrega VESTMED online.</td></tr>
						<tr>
						   <td CLASS="etiqueta" WIDTH="185">RUT:</td>
						   <td>
							 <INPUT name="dfRutUsrIn" id="dfRutUsrIn" size="12" maxLength="12" class="textfieldv2" style="TEXT-trANSFORM: uppercase" onblur="formatearRut('dfRutUsrIn','dfRutUsr')" value="<?php if ($RutPer != "") echo formatearRut($RutPer); ?>" <?php if ($cod_tipper==1) echo "readOnly" ?>>&nbsp;Ej: 99999999-X
						   </td>
						</tr>
						<tr>
							<td CLASS="etiqueta">Apellido Paterno:</td>
							<td>
								<INPUT name="dfAppPatIn" size="30" maxLength="80" class="textfieldv2" style="TEXT-trANSFORM: uppercase" onchange="llenarCampo(this)" value="<?php echo $AppPat; ?>" <?php if ($xis==1) echo "readOnly" ?> /></td>
						</tr>
						<tr>
							<td CLASS="etiqueta">Apellido Materno:</td>
							<td>
								<INPUT name="dfAppMatIn" size="30" maxLength="80" class="textfieldv2" style="TEXT-trANSFORM: uppercase" onchange="llenarCampo(this)" value="<?php echo $AppMat; ?>" <?php if ($xis==1) echo "readOnly" ?> />
								</td>
						</tr>
						<tr>
							<td CLASS="etiqueta">Nombres:</td>
							<td><INPUT name="dfNomUsrIn" size="60" maxLength="80" class="textfieldv2" style="TEXT-trANSFORM: uppercase" onchange="llenarCampo(this)" value="<?php echo $NomPer; ?>" <?php if ($xis==1) echo "readOnly" ?> /></td>
						</tr>
						<?php if ($CodPerCtt == 0 or ($xis == 1 and !$EsCliente)) { ?>
						<tr>
							<td CLASS="etiqueta">Profesi&oacute;n:&nbsp;</td>
							<td >
								<table WIDTH="100%" BORDER="0" align="center" cellpadding="0" cellspacing="0" >
								<tr>
									<?php if ($CodPerCtt == 0 or ($xis == 1 and !$EsCliente)) { ?>
									<form id="searchPro" name="searchPro">
									<td align="left">
									<select id="pro" name="pro" class="textfieldv2" onChange="filterPro(this)">
										<option selected value="_NONE">Seleccione una Profesi&oacute;n</option>
										<?php //Seleccionar las ciudades
										$sp = mssql_query("vm_pro_s ".$CodPro, $db);
										while($row = mssql_fetch_array($sp))
										{
											?>
											<option value="<?php echo $row['Cod_Pro'] ?>"><?php echo utf8_decode($row['Nom_Pro']) ?></option>
											<?php
										}
										?>
									</select>
									</td>
									</form>
									<?php } else { ?>
									<td align="left">
									<INPUT name="dfNomPro" size="20" maxLength="20" class="textfieldv2" style="TEXT-trANSFORM: uppercase" readOnly value="<?php echo $NomPro; ?>">
									</td>
									<?php } ?>
									

								</tr>
								</table>
							</td>
						</tr>
						<tr>
							<td CLASS="etiqueta">Especialidad:</td>
							<td >
								<table WIDTH="100%" BORDER="0" align="center" cellpadding="0" cellspacing="0" >
								<tr>
									<?php if ($CodPerCtt == 0 or ($xis == 1 and !$EsCliente)) { ?>
									<form id="searchEsp" name="searchEsp">
									<td>
									<select id="esp" name="esp" class="textfieldv2" onChange="llenarEsp(this)">
										<option selected value="_NONE">Seleccione una Especialidad</option>
										<?php //Seleccionar las ciudades
										$sp = mssql_query("vm_esppro_s 0",$db);
										while($row = mssql_fetch_array($sp))
										{
											?>
											<option value="<?php echo $row['Cod_Esp'] ?>"><?php echo utf8_decode($row['Nom_Esp']) ?></option>
											<?php
										}
										?>
									</select>
									</td>
									</form>
									<?php } else { ?>
									<td align="left">
									<INPUT name="dfNomEsp" size="20" maxLength="20" class="textfieldv2" style="TEXT-trANSFORM: uppercase" readOnly value="<?php echo $NomEsp; ?>">
									</td>
									<?php } ?>
								</tr>
								</table>
							</td>
						</tr>
						<tr>
							<td CLASS="etiqueta">Sexo:&nbsp;</td>
							<td>
							<INPUT id="rbSexoIn" name="rbSexoIn" type="radio" onclick="llenarCampo(this)" value="2" <?php if ($CodSex == 2) echo "checked"; ?> <?php if ($xis==1) echo "readOnly" ?>>&nbsp;<strong>Hombre</strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
							<INPUT id="rbSexoIn" name="rbSexoIn" type="radio" onclick="llenarCampo(this)" value="1" <?php if ($CodSex == 1) echo "checked"; ?> <?php if ($xis==1) echo "readOnly" ?>>&nbsp;<strong>Mujer</strong>
							</td>
						</tr>
						<tr>
							<td CLASS="etiqueta">Direcci&oacute;n:&nbsp;</td>
							<td><INPUT name="dfDireccionIn" size="60" maxLength="80" class="textfieldv2" style="TEXT-trANSFORM: uppercase" onchange="llenarCampo(this)" value="<?php echo $DirSuc; ?>" /></td>
						</tr>
						<tr>
							<td CLASS="etiqueta">Comuna:&nbsp;</td>
							<td>
								<table WIDTH="100%" BORDER="0" align="center" cellpadding="0" cellspacing="0" >
								<tr>
									<?php if ($CodPerCtt == 0 or ($xis == 1 and !$EsCliente)) { ?>
									<form id="searchCmn">
									<td align="left">
									<select id="cmn" name="cmn" class="textfieldv2" onChange="filterCmn(this)">
										<option selected value="_NONE">Seleccione una Comuna</option>
										<?php //Seleccionar las ciudades
										$sp = mssql_query("vm_cmn_s",$db);
										while($row = mssql_fetch_array($sp))
										{
											?>
											<option value="<?php echo $row['Cod_Cmn'] ?>"><?php echo utf8_encode($row['Nom_Cmn']) ?></option>
											<?php
										}
										?>
									</select>
									</td>
									</form>
									<?php } else { ?>
									<td align="left">
									<INPUT name="dfNomCmn" size="20" maxLength="20" class="textfieldv2" style="TEXT-trANSFORM: uppercase" readOnly value="<?php echo $NomCmn; ?>">
									</td>
									<?php } ?>
									
								</tr>
								</table>
							</td>
						</tr>
						<tr>
							<td CLASS="etiqueta">Ciudad:&nbsp;</td>
							<td>
								<table WIDTH="100%" BORDER="0" align="center" cellpadding="0" cellspacing="0" >
								<tr>

									<?php if ($CodPerCtt == 0 or ($xis == 1 and !$EsCliente)) { ?>
									<form id="searchCdd" name="searchCdd">
									<td>
									<select id="cdd" name="cdd" class="textfieldv2" onChange="llenarCdd(this)">
										<option selected value="_NONE">Seleccione una Ciudad</option>
										<?php //Seleccionar las ciudades
										$sp = mssql_query("vm_cddcmn_s NULL, 0",$db);
										while($row = mssql_fetch_array($sp))
										{
											?>
											<option value="<?php echo $row['Cod_Cdd'] ?>"><?php echo utf8_encode($row['Nom_Cdd']) ?></option>
											<?php
										}
										?>
									</select>
									</td>
									</form>
									<?php } else { ?>
									<td align="left">
									<INPUT name="dfNomCdd" size="20" maxLength="20" class="textfieldv2" style="TEXT-trANSFORM: uppercase" readOnly value="<?php echo $NomCdd; ?>">
									</td>
									<?php } ?>
								</tr>
								</table>
							</td>
						</tr>
						<tr>
						   <td CLASS="etiqueta">Tel&eacute;fono de contacto&nbsp;:<SPAN class="dator">*</SPAN></td>
						   <td>
							 <INPUT name="dfTelefonoUsrIn" size="12" maxLength="15" class="textfieldv2" onKeyPress="SoloNumeros(this)"  onchange="llenarCampo(this)" value="<?php echo $FonSuc; ?>" />
							</td>
							</tr>
						 <tr>
							<td class="etiqueta">FAX: <SPAN class="dator">*</SPAN></td>
							<td>
							 <INPUT name="dfFaxUsrIn" size="12" maxLength="15" class="textfieldv2" onKeyPress="SoloNumeros(this)" onchange="llenarCampo(this)" value="<?php echo $FaxSuc; ?>" /></td>
						</tr>
						<tr>
							<td class="etiqueta">M&oacute;vil&nbsp;:</td>
							<td>
							<INPUT name="dfMovilUsrIn" size="12" maxLength="15" class="textfieldv2" onKeyPress="SoloNumeros(this)" onchange="llenarCampo(this)" value="<?php echo $CelCtt; ?>" />
							</td>
						</tr>
						<tr>
							<td CLASS="etiqueta">e-mail:&nbsp;</td>
							<td><INPUT name="dfemailIn" size="60" maxLength="80" class="textfieldv2" style="TEXT-trANSFORM: none" onchange="llenarCampo(this)" value="<?php echo $MailCt; ?>" /></td>
						</tr>
						<tr>
						   <td CLASS="etiqueta">Contrase&ntilde;a:<SPAN class="dator">**</SPAN></td>
						   <td>
							 <INPUT type="password" name="dfPasswordIn" size="15" maxLength="30" class="textfieldv2" onchange="llenarCampo(this)" /></td>
						</tr>
						<tr>
						   <td CLASS="etiqueta">Repita Contrase&ntilde;a:<SPAN class="dator">***</SPAN></td>
						   <td>
							
							 <INPUT type="password" name="dfPassword2In" size="15" maxLength="30" class="textfieldv2" onchange="llenarCampo(this)"> </td>
						</tr>
						<tr>
							<td CLASS ="label_top" colspan="2">&nbsp;</td>
						</tr>
						<tr>
							<td colspan="2" CLASS="dato"><span style="float:left; width:30px;color:#6abfbf;">*</span>
								<span style="float:left;">Incluya c&oacute;digo de &aacute;rea para tel&eacute;fonos fuera de la Capital</span>
							</td>
						</tr>
						<tr>
							<td colspan="2" CLASS="dato"><span style="float:left; width:30px;color:#6abfbf;">**</span>
								<span style="float:left;">Ingrese una clave a su elecci&oacute;n (Debe tener entre 6 y 30 caracteres de largo).</span></td>
						</tr>
						<tr>
							<td colspan="2" CLASS="dato"><span style="float:left; width:30px;color:#6abfbf;">***</span>
								<span style="float:left;">Reingrese su clave a modo de verificaci&oacute;n</span></td>
						</tr>
						<?php } ?>
						<?php if ($EsCliente && $bExisteUsr) { ?>
						<tr><td colspan="2" CLASS="dato">&nbsp;</td></tr>
						<tr><td colspan="2" CLASS="dato" style="TEXT-ALIGN:left"><strong style="display: block; width: 500px;">
							Usted ya se encuentra registrado como usuario de Vestmed. Si desea recuperar su contrase&ntilde;a
							favor presione la opci&oacute;n &lt;Enviar Clave&gt;, y se la enviaremos a su correo <?php echo $MailCt; ?>.
							Si desea cambiar de usuario favor contactarse con Vestmed</strong><BR><BR>
						</td></tr>
						<?php } ?>
						<?php if ($EsCliente && !$bExisteUsr) { ?>
							<?php if (trim($MailCt) != '') { ?>
						<tr><td colspan="2" CLASS="dato">&nbsp;</td></tr>
						<tr><td colspan="2" CLASS="dato" style="TEXT-ALIGN:left"><strong>
							Usted ya se encuentra registrado como usuario de Vestmed. Sin embargo no tiene clave de acceso al
							sitio. Favor presione la tecla &lt;Enviar&gt; y le enviaremos su clave al correo
							<?php echo $MailCt; ?>.</strong><BR><BR>
						</td></tr>
							<?php } else { ?>
						<tr>
							<td CLASS="etiqueta">e-mail:&nbsp;</td>
							<td><INPUT name="dfemailIn" size="60" maxLength="80" class="textfieldv2" style="TEXT-trANSFORM: none" onchange="llenarCampo(this)" value="<?php echo $MailCt; ?>" /></td>
						</tr>
						<tr><td colspan="2" CLASS="dato" style="TEXT-ALIGN:left; PADDING-TOP: 10px"><strong>
							Usted ya se encuentra registrado como usuario de Vestmed. Sin embargo no tiene clave de acceso al
							sitio y tampoco tenemos registrado un e-mail. Favor indiquenos un e-mail donde enviarle la clave y
							a continuaci&oacute;n presione la tecla &lt;Enviar&gt;.</strong><BR><BR>
						</td></tr>
							<?php } ?>
						<?php } ?>
					  </table>
				  </td>
				</tr>
				<tr><td>
				<form ID="F2" method="post" name="F2" 
					  action=<?php if (!$bExisteUsr) echo "\"ing_fichausr.php\""; else echo "\"enviarclave.php\""; ?>
					  <?php if (!$bExisteUsr) echo "onsubmit=\"return checkDataFichaUsr(this,".$cod_tipper.")\""; ?> AUTOCOMPLETE="on">
					  <table border="0" cellpadding="0" cellspacing="0" width="100%">
						<tr>
							<td class="datoc" width="20%">&nbsp;</td>
							<td class="datoc" width="20%">
								<?php if (!$bExisteUsr) { ?>
								<input type="submit" name="Enviar" value="Enviar" class="btn">
								<?php } else { ?>
								<input type="submit" name="EnviarPwd" value="Enviar Clave" class="btn">
								<?php } ?>
							</td>
							<td class="datoc" width="20%">
								<input type="BUTTON" name="Limpiar" value="Limpiar" class="btn"
										onClick="javascript:volverMain('registrarse.php')">
								</td>
							<td class="datoc" width="20%">
								<input type="BUTTON" name="Volver" value="Cerrar" class="btn"
									   onClick="javascript:volverMain('catalogo.php')">
							</td>
							<td class="datoc" width="20%">&nbsp;</td>
						</tr>
					  </table>
					  <INPUT type="hidden" name="dfRutClt" value="<?php echo $RutClt; ?>">
					  <INPUT type="hidden" name="dfRutUsr" id="dfRutUsr" value="<?php echo $RutPer; ?>">
					  <?php if ($CodPerCtt == 0 or ($xis == 1 and !$EsCliente)) { ?>
					  <INPUT type="hidden" name="dfAppPat" value="<?php echo $AppPat; ?>">
					  <INPUT type="hidden" name="dfAppMat" value="<?php echo $AppMat; ?>">
					  <INPUT type="hidden" name="dfNomUsr" value="<?php echo $NomPer; ?>">
					  <INPUT type="hidden" name="dfDireccion" value="<?php echo $DirSuc; ?>">
					  <INPUT type="hidden" name="dfTelefonoUsr" value="<?php echo $FonSuc; ?>">
					  <INPUT type="hidden" name="dfFaxUsr" value="<?php echo $FaxSuc; ?>">
					  <INPUT type="hidden" name="dfMovilUsr" value="<?php echo $CelCtt; ?>">
					  <INPUT type="hidden" name="dfemail" value="<?php echo $MailCt; ?>">
					  <INPUT type="hidden" name="codcmn" value="<?php echo $CodCmn; ?>">
					  <INPUT type="hidden" name="codcdd" value="<?php echo $CodCdd; ?>">
					  <INPUT type="hidden" name="codpro" value="<?php echo $CodPro; ?>">
					  <INPUT type="hidden" name="codesp" value="<?php echo $CodEsp; ?>">
					  <INPUT type="hidden" name="rbSexo" value="<?php echo $CodSex; ?>">
					  <INPUT type="hidden" name="dfPassword">
					  <INPUT type="hidden" name="dfPassword2">
					  <?php }
							if ($EsCliente && !$bExisteUsr && trim($MailCt) == '') {
					  ?>
					  <INPUT type="hidden" name="dfemail" value="">
					  <?php } ?>
				</form>
				</td></tr>
			</table>
			</div>
        </div>
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