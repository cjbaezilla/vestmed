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
if ($Cod_Clt > 0) {
	$doc_id = 1;
	//$query = "vm_cli_s $Cod_Clt";
	$result = mssql_query ("vm_cli_s $Cod_Clt", $db)	or die ("No se pudo leer datos del Cliente");
	if (($row = mssql_fetch_array($result))) {
		$cod_tipper = $row["Cod_TipPer"];
		if ($cod_tipper == 2) { // Persona Juridica
			$NombreClt = utf8_encode($row["RznSoc_Per"]);
			$NombreFanClt = utf8_encode($row["NomFan_Per"]);
		}
		$RutClt = $row["Num_Doc"];
		$web     = $row["www_Per"];
		mssql_free_result($result); 

		if ($Cod_Clt > 0) { // Si es cliente
			//$query = "vm_s_cttclt $Cod_Clt";
			$result = mssql_query ("vm_s_cttclt $Cod_Clt", $db)
							or die ("No se pudo leer datos del Contacto (".$Cod_Clt.")");
			while ($row = mssql_fetch_array($result))
				if ($Cod_Per == $row["Cod_Per"]) {
					$RutPer = $row["Num_Doc"];
					$AppPat = utf8_encode($row["Pat_Per"]);
					$AppMat = utf8_encode($row["Mat_Per"]);
					$NomPer = utf8_encode($row["Nom_Per"]);
					$CodPro = $row["Cod_Pro"];
					$CodEsp = $row["Cod_Esp"];
					$CodSex = $row["Sex"];
					$DirSuc = utf8_encode($row["Dir_Suc"]);
					$CodCmn = $row["Cod_Cmn"];
					$CodCdd = $row["Cod_Cdd"];
					$FonSuc = $row["Fon_Suc"];
					$FaxSuc = $row["Fax_Suc"];
					$FonCtt = $row["Fon_Ctt"];
					$CelCtt = $row["Cel_Ctt"];
					$MailCt = $row["Mail_Ctt"];
					$clave 	= $row["Pwd_Web"];
					break;
				}
			mssql_free_result($result); 
		}
		
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
}	

$accion = isset($_GET['accion']) ? ok($_GET['accion']) : "";
$idmsg = isset($_GET['idmsg']) ? ok($_GET['idmsg']) : "";
$mensaje = array(0 => "Clave cambiada exitosamente",
				 1 => "La clave actual no corresponde a la que se encuentra registrada.",
				 2 => "Inconsistencia entre la Persona conectada y la persona due&ntilde;a de la clave",
				 3 => "No se detect&oacute; quien est&aacute; conectado a la cuenta",
				 4 => "Persona conectada no existe como usuario",
				 5 => "Cambios actualizados correctamente",
				 6 => "No se pudo identificar la Sucursal del Contacto"
				 );
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

<LINK href="Include/estilos.css" type="text/css" rel="stylesheet" />
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
<!--[if IE]>
<link rel="stylesheet" type="text/css" media="screen" href="dropdown/css/uvumi-dropdown-ie.css" />
<![endif]-->
<script type="text/javascript">
new UvumiDropdown('dropdown-scliente');

function popwindow(ventana){
   window.open(ventana,"Anexos",'toolbar=0,location=0,scrollbars=yes,resizable=1,left=60,top=40,width=640,height=480')
}
</script>
</head>

<body>
<div id="body">
	<div id="header"></div>
    <div class="menu" id="menu-noselect">
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
        	 <img src="images/registro/micuenta.png" style="top:60px;" class="titulo-principal-avisos" />
            	<div style="width:765px; margin:0 auto 0 250px; padding-top:10px;">
			<?php if ($accion == "" || $accion == "upd") { ?>
				<TABLE WIDTH="650" BORDER="0" align="left" style="margin-top:15px;">
					<TR>
					  <TD ALIGN="left" width="100%" VALIGN="top">
						 <TABLE border="0" cellpadding="1" cellspacing="0" width="100%" align="center">
<?php if ($cod_tipper == 2) { ?>
							<TR><TD colspan=2 style="font-weight:bold;">Datos de la Instituci&oacute;n</TD></TR>
							<TR>		
							   <TD CLASS="etiqueta" WIDTH="28%">RUT:&nbsp;</TD>
							   <TD WIDTH=%"72%">
								 <INPUT name="dfRutCltIn" id="dfRutCltIn" size="15" maxLength="30" class="textfieldv2" style="TEXT-TRANSFORM: uppercase" value="<?php echo formatearRut($RutClt); ?>" readOnly>
							   </TD>
							</TR>
							<TR>
								<TD CLASS="etiqueta">Raz&oacute;n Social:&nbsp;</TD>
								<TD ><INPUT name="dfNombreIn" size=60 maxLength=80 class="textfieldv2" style="TEXT-TRANSFORM: uppercase" onchange="llenarCampo(this)" value="<?php echo $NombreClt; ?>" readOnly>&nbsp;</TD>
							</TR>
							<TR>		
							   <TD CLASS="etiqueta">Nombre de Fantas&iacute;a:&nbsp;</TD>
							   <TD>
								 <INPUT name="dfNombreFantasiaIn" size="60" maxLength="80" class="textfieldv2" style="TEXT-TRANSFORM: uppercase" onchange="llenarCampo(this)" value="<?php echo $NombreFanClt; ?>" readOnly>
							   </TD>
							</TR>
							<TR>		
							   <TD CLASS="etiqueta">P&aacute;gina Web:&nbsp;</TD>
							   <TD >
								 <INPUT name="dfWebIn" size="40" maxLength="80" class="textfieldv2" onchange="llenarCampo(this)" value="<?php echo $web; ?>" <?php if ($accion == "") echo "readOnly" ?>>&nbsp;
								 <span class=dato>Ej: www.vestmed.cl</span>
							   </TD>
							</TR>
							<TR><TD CLASS ="label_top" colspan="2">&nbsp;</TD></TR>
							<TR><TD colspan="2" CLASS="dato">&nbsp;</TD></TR>
							<TR><TD colspan=2 style="font-weight:bold;">Datos de la Sucursal</TD></TR>
							<TR>		
							   <TD CLASS="etiqueta">Direcci&oacute;n:&nbsp;</TD>
							   <TD ><INPUT name="dfDireccionIn" size="60" maxLength="80" class="textfieldv2" style="TEXT-TRANSFORM: uppercase" onchange="llenarCampo(this)" value="<?php echo $DirSuc; ?>" <?php if ($accion == "") echo "readOnly" ?>>&nbsp;</TD>
							</TR>
							<TR>
								<TD CLASS="etiqueta">Comuna:&nbsp;</TD>
								<?php if ($accion == "upd") { ?>
								<form id="searchCmn">
								<TD align="left">
								<select id="cmn" name="cmn" class="textfieldv2" onChange="filterCmn(this)">
									<option selected value="_NONE">Seleccione una Comuna</option>
									<?php //Seleccionar las ciudades
									$sp = mssql_query("vm_cmn_s",$db);
									while($row = mssql_fetch_array($sp))
									{
										?>
										<option value="<?php echo $row['Cod_Cmn'] ?>"<?php if ($row['Cod_Cmn'] == $CodCmn) echo " selected"?>><?php echo $row['Nom_Cmn'] ?></option>
										<?php
									}
									?>
								</select>
								</TD>
								</form>
								<?php } else { ?>
								<TD align="left">
								<INPUT name="dfNomCmn" size="20" maxLength="20" class="textfieldv2" style="TEXT-TRANSFORM: uppercase" readOnly value="<?php echo $NomCmn; ?>">
								</TD>										
								<?php } ?>
							</TR>
							<TR>
								<TD CLASS="etiqueta">Cuidad:&nbsp;</TD>
								<?php if ($accion == "upd") { ?>
								<form id="searchCdd" name="searchCdd">
								<TD>
								<select id="cdd" name="cdd" class="textfieldv2" onChange="llenarCdd(this)">
									<option selected value="_NONE">Seleccione una Ciudad</option>
									<?php //Seleccionar las ciudades
									$sp = mssql_query("vm_cddcmn_s NULL, $CodCmn",$db);
									while($row = mssql_fetch_array($sp))
									{
										?>
										<option value="<?php echo $row['Cod_Cdd'] ?>"<?php if ($row['Cod_Cdd'] == $CodCdd) echo " selected"?>><?php echo $row['Nom_Cdd'] ?></option>
										<?php
									}
									?>
								</select>
								</TD>
								</form>
								<?php } else { ?>
								<TD align="left">
								<INPUT name="dfNomCdd" size="20" maxLength="20" class="textfieldv2" style="TEXT-TRANSFORM: uppercase" readOnly value="<?php echo $NomCdd; ?>">
								</TD>										
								<?php } ?>
							</TR>
							<TR>		
							   <TD CLASS="etiqueta">Tel&eacute;fono&nbsp;<SPAN class=dator>(1)</SPAN>:&nbsp;</TD>
								<TD align="left">
								 <INPUT name="dfTelefonoSucIn" size="12" maxLength="15" class="textfieldv2" onKeyPress="SoloNumeros(this)"  onchange="llenarCampo(this)" value="<?php echo $FonSuc; ?>" <?php if ($accion == "") echo "readOnly" ?>>
								</TD>
						    </TR>
							<TR>		
							    <TD CLASS="etiqueta">FAX&nbsp;<SPAN class=dator>(1)</SPAN>:&nbsp;</TD>
								<TD align="left">
								 <INPUT name="dfFaxSucIn" size="12" maxLength="15" class="textfieldv2" onKeyPress="SoloNumeros(this)" onchange="llenarCampo(this)" value="<?php echo $FaxSuc; ?>" <?php if ($accion == "") echo "readOnly" ?>>
								</TD>
						    </TR>
							<TR><TD CLASS ="label_top" colspan="2">&nbsp;</TD></TR>
							<TR><TD colspan="2" CLASS="dato">&nbsp;</TD></TR>
							<TR><TD colspan=2 style="font-weight:bold;">Datos del Contacto</TD></TR>
							<TR>		
							   <TD CLASS="etiqueta" WIDTH="28%">RUT:&nbsp;</TD>
							   <TD WIDTH=%"72%">
								 <INPUT name="dfRutUsrIn" size="15" maxLength="15" class="textfieldv2" style="TEXT-TRANSFORM: uppercase" onchange="llenarCampo(this)" value="<?php echo formatearRut($RutPer); ?>" readOnly>
							   </TD>
							</TR>
							<TR>
								<TD CLASS="etiqueta">Apellido Paterno:&nbsp;</TD>
								<TD >
									<INPUT name="dfAppPatIn" size="30" maxLength="80" class="textfieldv2" style="TEXT-TRANSFORM: uppercase" onchange="llenarCampo(this)" value="<?php echo $AppPat; ?>" readOnly>
									</TD>
							</TR>
                            <TR>
								<TD CLASS="etiqueta">Apellido Materno:&nbsp;</TD>
								<TD >
									<INPUT name="dfAppMatIn" size="30" maxLength="80" class="textfieldv2" style="TEXT-TRANSFORM: uppercase" onchange="llenarCampo(this)" value="<?php echo $AppMat; ?>" readOnly>
									</TD>
							</TR>
							<TR>
								<TD CLASS="etiqueta">Nombres:&nbsp;</TD>
								<TD><INPUT name="dfNomUsrIn" size="60" maxLength="80" class="textfieldv2" style="TEXT-TRANSFORM: uppercase" onchange="llenarCampo(this)" value="<?php echo $NomPer; ?>" readOnly>&nbsp;</TD>
							</TR>
							<TR>
								<TD CLASS="etiqueta">Tipo de Contacto:&nbsp;</TD>
								<TD >
										<?php if ($accion == "upd") { ?>
										<form id="searchEsp" name="searchEsp">
										<select id="esp" name="esp" class="textfieldv2" onChange="llenarEsp(this)">
											<option selected value="_NONE">Seleccione Tipo Contacto</option>
											<?php //Seleccionar las ciudades
											$CodPro = -1; 
											$sp = mssql_query("vm_esppro_s -1",$db);
											while($row = mssql_fetch_array($sp))
											{
												?>
												<option value="<?php echo $row['Cod_Esp'] ?>"<?php if ($row['Cod_Esp'] == $CodEsp) echo " selected"?>><?php echo $row['Nom_Esp'] ?></option>
												<?php
											}
											?>
										</select>
										</form>
										<?php } else { ?>
										<INPUT name="dfNomEsp" size="20" maxLength="20" class="textfieldv2" style="TEXT-TRANSFORM: uppercase" readOnly value="<?php echo $NomEsp; ?>">
										<?php } ?>
								</TD>
							</TR>
							<TR>
								<TD CLASS="etiqueta">Sexo:&nbsp;</TD>
								<TD>
								<INPUT id="rbSexoIn" name="rbSexoIn" type="radio" class="button2" onclick="llenarCampo(this)" value="2" <?php if ($CodSex == 2) echo "checked"; ?> <?php if ($accion == "") echo "readOnly" ?>>&nbsp;<strong>Hombre</strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
								<INPUT id="rbSexoIn" name="rbSexoIn" type="radio" class="button2" onclick="llenarCampo(this)" value="1" <?php if ($CodSex == 1) echo "checked"; ?> <?php if ($accion == "") echo "readOnly" ?>>&nbsp;<strong>Mujer</strong>
								</TD>
							</TR>
							<TR>		
							   <TD CLASS="etiqueta">Tel&eacute;fono de contacto&nbsp;<SPAN class=dator>(1)</SPAN>:&nbsp;</TD>
								<TD align="left">
								 <INPUT name="dfTelefonoUsrIn" size="12" maxLength="15" class="textfieldv2" onKeyPress="SoloNumeros(this)"  onchange="llenarCampo(this)" value="<?php echo $FonCtt; ?>" <?php if ($accion == "") echo "readOnly" ?>>&nbsp;&nbsp;
								</TD>
							</TR>
							<TR>
							    <TD CLASS="etiqueta">M&oacute;vil&nbsp;</TD>
								<TD align="left">
								 <INPUT name="dfMovilUsrIn" size="12" maxLength="15" class="textfieldv2" onKeyPress="SoloNumeros(this)" onchange="llenarCampo(this)" value="<?php echo $CelCtt; ?>" <?php if ($accion == "") echo "readOnly" ?>>
								</TD>
							</TR>
							<TR>
								<TD CLASS="etiqueta">e-mail:&nbsp;</TD>
								<TD><INPUT name="dfemailIn" size="60" maxLength="80" class="textfieldv2" style="TEXT-TRANSFORM: none" onchange="llenarCampo(this)" value="<?php echo $MailCt; ?>" <?php if ($accion == "") echo "readOnly" ?>>&nbsp;</TD>
							</TR>
							<?php if ($accion == "") { ?>
							<TR>		
							   <TD CLASS="etiqueta">Contrase&ntilde;a&nbsp;<SPAN class=dator>(2)</SPAN>:&nbsp;</TD>
							   <TD >
								 <INPUT type="password" name="dfPasswordIn" size="15" maxLength="30" class="textfieldv2" onchange="llenarCampo(this)" value="************" ReadOnly>&nbsp;
							   </TD>
							</TR>
							<?php } ?>
							<TR>
								<TD CLASS ="label_top" colspan="2">&nbsp;</TD>
							</TR>
							<TR><TD colspan="2" CLASS="dato">&nbsp;</TD></TR>
<?php } else { ?>
							<TR><TD colspan=2 style="font-weight:bold;">Datos del Cliente</TD></TR>
							<TR>		
							   <TD CLASS="etiqueta" WIDTH="28%">RUT:&nbsp;</TD>
							   <TD WIDTH=%"72%">
								 <INPUT name="dfRutUsrIn" size="15" maxLength="15" class="textfieldv2" style="TEXT-TRANSFORM: uppercase" onchange="llenarCampo(this)" value="<?php echo formatearRut($RutPer); ?>" <?php if ($accion == "") echo "readOnly" ?>>
							   </TD>
							</TR>
							<TR>
								<TD CLASS="etiqueta">Apellido Paterno&nbsp;:&nbsp;</TD>
								<TD >
									<INPUT name="dfAppPatIn" size="30" maxLength="80" class="textfieldv2" style="TEXT-TRANSFORM: uppercase" onchange="llenarCampo(this)" value="<?php echo $AppPat; ?>" <?php if ($accion == "") echo "readOnly" ?>>
									</TD>
							</TR>
                            <TR>
								<TD CLASS="etiqueta">Apellido Materno&nbsp;:&nbsp;</TD>
								<TD >
									<INPUT name="dfAppMatIn" size="30" maxLength="80" class="textfieldv2" style="TEXT-TRANSFORM: uppercase" onchange="llenarCampo(this)" value="<?php echo $AppMat; ?>" <?php if ($accion == "") echo "readOnly" ?>>
									</TD>
							</TR>
							<TR>
								<TD CLASS="etiqueta">Nombres:&nbsp;</TD>
								<TD><INPUT name="dfNomUsrIn" size="60" maxLength="80" class="textfieldv2" style="TEXT-TRANSFORM: uppercase" onchange="llenarCampo(this)" value="<?php echo $NomPer; ?>" <?php if ($accion == "") echo "readOnly" ?>>&nbsp;</TD>
							</TR>
							<TR>
								<TD CLASS="etiqueta">Profesi&oacute;n:&nbsp;</TD>
									
									
										<?php if ($accion == "upd") { ?>
										<form id="searchPro" name="searchPro">
										<TD align="left">
										<select id="pro" name="pro" class="textfieldv2" onChange="filterPro(this)">
											<option selected value="_NONE">Seleccione una Profesi&oacute;n</option>
											<?php //Seleccionar las profeciones
											$sp = mssql_query("vm_pro_s", $db);
											while($row = mssql_fetch_array($sp))
											{
												?>
												<option value="<?php echo $row['Cod_Pro'] ?>"<?php if ($row['Cod_Pro'] == $CodPro) echo " selected" ?>><?php echo utf8_encode($row['Nom_Pro']) ?></option>
												<?php
											}
											?>
										</select>
										</TD>
										</form>
										<?php } else { ?>
										<TD align="left">
										<INPUT name="dfNomPro" size="20" maxLength="20" class="textfieldv2" style="TEXT-TRANSFORM: uppercase" readOnly value="<?php echo $NomPro; ?>">
										</TD>										
										<?php } ?>
                                     </TR>
                                     <tr>
                                     	
										<TD class="etiqueta">Especialidad:&nbsp;</TD>
										<?php if ($accion == "upd") { ?>
										<form id="searchEsp" name="searchEsp">
										<TD>
										<select id="esp" name="esp" class="textfieldv2" onChange="llenarEsp(this)">
											<option selected value="_NONE">Seleccione una Especialidad</option>
											<?php //Seleccionar las ciudades
											$sp = mssql_query("vm_esppro_s $CodPro",$db);
											while($row = mssql_fetch_array($sp))
											{
												?>
												<option value="<?php echo $row['Cod_Esp'] ?>"<?php if ($row['Cod_Esp'] == $CodEsp) echo " selected" ?>><?php echo utf8_encode($row['Nom_Esp']) ?></option>
												<?php
											}
											?>
										</select>
										</TD>
										</form>
										<?php } else { ?>
										<TD align="left">
										<INPUT name="dfNomEsp" size="20" maxLength="20" class="textfieldv2" style="TEXT-TRANSFORM: uppercase" readOnly value="<?php echo $NomEsp; ?>">
										</TD>										
										<?php } ?>
									</TR>
		
							<TR>
								<TD CLASS="etiqueta">Sexo:&nbsp;</TD>
								<TD>
								<INPUT id="rbSexoIn" name="rbSexoIn" type="radio" class="button2" onclick="llenarCampo(this)" value="2" <?php if ($CodSex == 2) echo "checked"; ?> <?php if ($accion == "") echo "readOnly" ?>>&nbsp;<strong>Hombre</strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
								<INPUT id="rbSexoIn" name="rbSexoIn" type="radio" class="button2" onclick="llenarCampo(this)" value="1" <?php if ($CodSex == 1) echo "checked"; ?> <?php if ($accion == "") echo "readOnly" ?>>&nbsp;<strong>Mujer</strong>
								</TD>
							</TR>
							<TR>
								<TD CLASS="etiqueta">Direcci&oacute;n:&nbsp;</TD>
								<TD ><INPUT name="dfDireccionIn" size="60" maxLength="80" class="textfieldv2" style="TEXT-TRANSFORM: uppercase" onchange="llenarCampo(this)" value="<?php echo $DirSuc; ?>" <?php if ($accion == "") echo "readOnly" ?>>&nbsp;</TD>
							</TR>
							<TR>
								<TD CLASS="etiqueta">Comuna:&nbsp;</TD>
								
										<?php if ($accion == "upd") { ?>
										<form id="searchCmn">
										<TD align="left">
										<select id="cmn" name="cmn" class="textfieldv2" onChange="filterCmn(this)">
											<option selected value="_NONE">Seleccione una Comuna</option>
											<?php //Seleccionar las ciudades
											$sp = mssql_query("vm_cmn_s",$db);
											while($row = mssql_fetch_array($sp))
											{
												?>
												<option value="<?php echo $row['Cod_Cmn'] ?>"<?php if ($row['Cod_Cmn'] == $CodCmn) echo "selected" ?>><?php echo utf8_encode($row['Nom_Cmn']) ?></option>
												<?php
											}
											?>
										</select>
										</TD>
										</form>
										<?php } else { ?>
                                     
										<TD align="left">
										<INPUT name="dfNomCmn" size="20" maxLength="20" class="textfieldv2" style="TEXT-TRANSFORM: uppercase" readOnly value="<?php echo $NomCmn; ?>">
										</TD>										
										<?php } ?>
                                          </TR>
                                       <tr>
										<TD class="etiqueta">Ciudad:&nbsp;</TD>
										<?php if ($accion == "upd") { ?>
										<form id="searchCdd" name="searchCdd">
										<TD>
										<select id="cdd" name="cdd" class="textfieldv2" onChange="llenarCdd(this)">
											<option selected value="_NONE">Seleccione una Ciudad</option>
											<?php //Seleccionar las ciudades
											$sp = mssql_query("vm_cddcmn_s NULL, $CodCmn",$db);
											while($row = mssql_fetch_array($sp))
											{
												?>
												<option value="<?php echo $row['Cod_Cdd'] ?>"<?php if ($row['Cod_Cdd'] == $CodCdd) echo "selected" ?>><?php echo utf8_encode($row['Nom_Cdd']) ?></option>
												<?php
											}
											?>
										</select>
										</TD>
										</form>
										<?php } else { ?>
										<TD align="left">
										<INPUT name="dfNomCdd" size="20" maxLength="20" class="textfieldv2" style="TEXT-TRANSFORM: uppercase" readOnly value="<?php echo $NomCdd; ?>">
										</TD>										
										<?php } ?>
									</TR>

							<TR>		
							   <TD CLASS="etiqueta">Tel&eacute;fono de contacto&nbsp;<SPAN class=dator>(1)</SPAN>:&nbsp;</TD>
							   <TD >
								 <INPUT name="dfTelefonoUsrIn" size="12" maxLength="15" class="textfieldv2" onKeyPress="SoloNumeros(this)"  onchange="llenarCampo(this)" value="<?php echo $FonCtt; ?>" <?php if ($accion == "") echo "readOnly" ?>>
							   </TD>
							</TR>
                            <TR>		
							   <TD CLASS="etiqueta">FAX&nbsp;<SPAN class=dator>(1)</SPAN>:</td>
                               <td><INPUT name="dfFaxUsrIn" size="12" maxLength="15" class="textfieldv2" onKeyPress="SoloNumeros(this)" onchange="llenarCampo(this)" value="<?php echo $FaxSuc; ?>" <?php if ($accion == "") echo "readOnly" ?>>   </TD>
							</TR>
                            <TR>		
							   <TD CLASS="etiqueta">M&oacute;vil&nbsp;</TD><td><INPUT name="dfMovilUsrIn" size="12" maxLength="15" class="textfieldv2" onKeyPress="SoloNumeros(this)" onchange="llenarCampo(this)" value="<?php echo $CelCtt; ?>" <?php if ($accion == "") echo "readOnly" ?>>
							   </TD>
							</TR>
							<TR>
								<TD CLASS="etiqueta">e-mail:&nbsp;</TD>
								<TD><INPUT name="dfemailIn" size="60" maxLength="80" class="textfieldv2" style="TEXT-TRANSFORM: none" onchange="llenarCampo(this)" value="<?php echo $MailCt; ?>" <?php if ($accion == "") echo "readOnly" ?>>&nbsp;</TD>
							</TR>
							<TR>		
							   <TD CLASS="etiqueta">Contrase&ntilde;a&nbsp;<SPAN class=dator>(2)</SPAN>:&nbsp;</TD>
							   <TD >
								 <INPUT type="password" name="dfPasswordIn" size="15" maxLength="30" class="textfieldv2" onchange="llenarCampo(this)" value="************" ReadOnly>&nbsp;
							   </TD>
							</TR>
							<TR>
								<TD CLASS ="label_top" colspan="2">&nbsp;</TD>
							</TR>
							<TR><TD colspan="2" CLASS="dato">&nbsp;</TD></TR>
<?php } ?>
						  </TABLE>
					  </TD>   
					</TR>
					<TR><TD>
					<form ID="F2" method="post" name="F2" action="upd_fichausr.php" onsubmit="return checkDataUsuario(this)" AUTOCOMPLETE="on">
						  <TABLE border="0" cellpadding="0" cellspacing="0" width="100%">
							<TR>
								<TD class="datoc" style="TEXT-ALIGN: left">
									<?php if ($accion == "") { ?>
									<input type="BUTTON" name="clave" value="Cambiar Clave" class="btn"
											onClick="javascript:volverMain('micuenta.php?accion=pwd')">&nbsp;
									<input type="BUTTON" name="Volver" style="font-size:11px;" value="Modificar Datos" class="btn"
										   onClick="javascript:volverMain('micuenta.php?accion=upd')">&nbsp;
									<?php } else { ?>
									<input type="SUBMIT" name="actualizar" value="Actualizar" class="btn">&nbsp;
									<input type="BUTTON" name="Volver" value="Volver" class="btn"
										   onClick="javascript:volverMain('micuenta.php')">&nbsp;
									<?php } ?>
									<input type="BUTTON" name="Volver" value="Cerrar" class="btn"
										   onClick="javascript:volverMain('catalogo.php')">
								</TD>
							</TR>
						  </TABLE>
						  <INPUT type="hidden" name="dfRutClt" value="<?php echo $RutClt; ?>">
						  <?php if ($accion == "upd") { ?>
						  <INPUT type="hidden" name="dfNombre" value="<?php echo $NombreClt; ?>">
						  <INPUT type="hidden" name="dfNombreFantasia" value="<?php echo $NombreFanClt; ?>">
						  <INPUT type="hidden" name="dfWeb" value="<?php echo $web; ?>">
						  <INPUT type="hidden" name="dfRutUsr" value="<?php echo $RutPer; ?>">
						  <INPUT type="hidden" name="dfAppPat" value="<?php echo $AppPat; ?>">
						  <INPUT type="hidden" name="dfAppMat" value="<?php echo $AppMat; ?>">
						  <INPUT type="hidden" name="dfNomUsr" value="<?php echo $NomPer; ?>">
						  <INPUT type="hidden" name="dfDireccion" value="<?php echo $DirSuc; ?>">
						  <INPUT type="hidden" name="dfTelefonoUsr" value="<?php echo $FonSuc; ?>">
						  <INPUT type="hidden" name="dfFaxUsr" value="<?php echo $FaxSuc; ?>">
						  <INPUT type="hidden" name="dfTelefonoSuc" value="<?php echo $FonSuc; ?>">
						  <INPUT type="hidden" name="dfFaxSuc" value="<?php echo $FaxSuc; ?>">
						  <INPUT type="hidden" name="dfMovilUsr" value="<?php echo $CelCtt; ?>">
						  <INPUT type="hidden" name="dfemail" value="<?php echo $MailCt; ?>">
						  <INPUT type="hidden" name="codcmn" value="<?php echo $CodCmn; ?>">
						  <INPUT type="hidden" name="codcdd" value="<?php echo $CodCdd; ?>">
						  <INPUT type="hidden" name="codpro" value="<?php echo $CodPro; ?>">
						  <INPUT type="hidden" name="codesp" value="<?php echo $CodEsp; ?>">
						  <INPUT type="hidden" name="rbSexo" value="<?php echo $CodSex; ?>">
						  <INPUT type="hidden" name="dfPassword" value="**********">
						  <INPUT type="hidden" name="dfPassword2" value="**********">
						  <?php } ?>
					</form>
					</TD></TR>
				</TABLE>
			<?php } else if ($accion == "pwd") { ?>
				<TABLE WIDTH="60%" BORDER="0" align="left" style="margin-top:35px;">
					<form ID="F2" method="post" name="F2" action="updclave.php" onsubmit="return checkDataClave(this)" AUTOCOMPLETE="on">
					<TR><TD ALIGN="left" width="100%" VALIGN="top">
						<TABLE border="0" cellpadding="1" cellspacing="0" width="100%" align="left">
							<TR><TD colspan="2" align="center" style="font-size:14px; font-weight:bold; padding-bottom: 50px">&nbsp;Cambiar Contrase&ntilde;a de la Web</TD></TR>
							<TR>		
							   <TD CLASS="etiqueta" WIDTH="190">RUT:&nbsp;</TD>
							   <TD>
								 <INPUT name="dfRutUsrIn" size="15" maxLength="15" class="textfieldv2" style="TEXT-TRANSFORM: uppercase" onchange="llenarCampo(this)" value="<?php echo formatearRut($RutPer); ?>" readOnly>
							   </TD>
							</TR>
							<TR>
								<TD CLASS="etiqueta">Apellido Paterno&nbsp;:&nbsp;</TD>
								<TD >
									<INPUT name="dfAppPatIn" size="30" maxLength="80" class="textfieldv2" style="TEXT-TRANSFORM: uppercase" onchange="llenarCampo(this)" value="<?php echo $AppPat; ?>" <?php if ($xis==1) echo "readOnly" ?>>
                                </TD>
                           </TR>
                           <tr>
                           		<td class="etiqueta">
									Apellido Materno:</td>
                                <td>
                                
                                <INPUT name="dfAppMatIn" size="30" maxLength="80" class="textfieldv2" style="TEXT-TRANSFORM: uppercase" onchange="llenarCampo(this)" value="<?php echo $AppMat; ?>" <?php if ($xis==1) echo "readOnly" ?>>
									</TD>
							</TR>
							<TR>
								<TD CLASS="etiqueta">Nombres:&nbsp;</TD>
								<TD><INPUT name="dfNomUsrIn" size="60" maxLength="80" class="textfieldv2" style="TEXT-TRANSFORM: uppercase" onchange="llenarCampo(this)" value="<?php echo $NomPer; ?>" <?php if ($xis==1) echo "readOnly" ?>>&nbsp;</TD>
							</TR>
							<TR>		
							   <TD CLASS="etiqueta">Contrase&ntilde;a&nbsp; Actual:&nbsp;</TD>
							   <TD >
								 <INPUT type="password" name="dfPasswordOld" size="15" maxLength="30" class="textfieldv2" onchange="llenarCampo(this)" value="">
							   </TD>
							</TR>
							<TR>		
							   <TD CLASS="etiqueta">Contrase&ntilde;a&nbsp; Nueva:&nbsp;</TD>
							   <TD>
								 <INPUT type="password" name="dfPasswordNew1" size="15" maxLength="30" class="textfieldv2" onchange="llenarCampo(this)" value="">
							   </TD>
							</TR>
							<TR>		
							   <TD CLASS="etiqueta">Repita Contrase&ntilde;a&nbsp; Nueva:&nbsp;</TD>
							   <TD >
								 <INPUT type="password" name="dfPasswordNew2" size="15" maxLength="30" class="textfieldv2" onchange="llenarCampo(this)" value="">
							   </TD>
							</TR>
							<TR>
								<TD CLASS ="label_top" colspan="2">&nbsp;</TD>
							</TR>
							<?php if ($idmsg != "") { ?>
							<TR>
								<TD colspan="2" style="PADDING-BOTTOM: 10px" CLASS="<?php echo ($idmsg == "0" ? "titulo_Bordado1" : "titulo_error"); ?>">
									<?php echo $mensaje[intval($idmsg)]; ?>
								</TD>
							</TR>
							<?php } ?>
						</TABLE>
					</TD></TR>
					<TR><TD>
						<TABLE border="0" cellpadding="0" cellspacing="0" width="100%">
						<TR>
							<TD class="datoc" style="TEXT-ALIGN: left">
								<input type="SUBMIT" name="clave" value="Actualizar" class="btn">&nbsp;
								<input type="BUTTON" name="Volver" value="Volver" class="btn"
									   onClick="javascript:volverMain('micuenta.php')">&nbsp;
								<input type="BUTTON" name="Volver" value="Cerrar" class="btn"
									   onClick="javascript:volverMain('catalogo.php')">
								<INPUT type="hidden" name="dfRutClt" value="<?php echo $RutClt; ?>">
							</TD>
						</TR>
						</TABLE>
					</TD></TR>
					</form>
				</TABLE>
			<?php } ?>
			</div>
		</div>
	</div>
	<div id="footer"></div>
<script language="javascript">
	var f1;	
	var f2;
	
	f1 = document.F1;	
	f2 = document.F2;
</script>
</body>
</html>
