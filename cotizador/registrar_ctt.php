<?php
//Obtengo los datos de conexion de la base de datos
ini_set('display_errors', '0');
session_start();
include("../config.php");

$Cod_Per = 0;
$Cod_Clt = 0;
$RutClt = "";
$RutPer = "";
$accion = "nuevo";
$xis = 0;
$EsCliente = false;
$bExisteUsr = false;
$flagReadOnlyClt = false;
$flagReadOnlySuc = false;
$flagReadOnlyCtt = false;
if (isset($_SESSION['CodPer'])) $Cod_Per = intval($_SESSION['CodPer']);
if (isset($_SESSION['CodClt'])) $Cod_Clt = intval($_SESSION['CodClt']);
$ret = isset($_GET['ret']) ? ok($_GET['ret']) : 0;
if (isset($_GET['clt'])) {
	$RutClt = ok($_GET['clt']);
	if (!strrpos($RutClt,"-")) $RutClt = substr($RutClt, 0, -1)."-".substr($RutClt, -1);
	$xis     = intval(ok($_GET['xis']));
	$Cod_Suc = isset($_GET['suc']) ? ok($_GET['suc']) : 0;
	$Cod_Cot = isset($_GET['cot']) ? ok($_GET['cot']) : 0;
	$RutPer = isset($_POST['dfRutUsrIn']) ? ok($_POST['dfRutUsrIn']) : ok($_GET['ctt']);
	if ($xis == 1) { // Caso existe como Persona en Vestmed
		$doc_id = 1;
		//$query = "vm_s_per_tipdoc ".$doc_id.", '".$RutClt."'";
		$result = mssql_query ("vm_s_per_tipdoc ".$doc_id.", '".$RutClt."'", $db)	or die ("No se pudo leer datos del Cliente");
		if ($row = mssql_fetch_array($result)) {
			$cod_tipper = $row["Cod_TipPer"];
			$Cod_Clt 	= $row["Cod_Clt"];
			$id_per	 	= $row["Cod_Per"];
			mssql_free_result($result); 
			if ($Cod_Clt != "") { // Si es cliente
				$EsCliente = true;
				$CodPro = -1;
				$CodEsp = -1;
				$accion = "newsuc";
				//$query = "vm_suc_s ".$Cod_Clt.", ".$Cod_Suc;
				$result = mssql_query ("vm_suc_s ".$Cod_Clt.", ".$Cod_Suc, $db)
								or die ("No se pudo leer datos de la Sucursal (".$Cod_Clt.")");
				if ($row = mssql_fetch_array($result)) {
					$DirSuc = $row["Dir_Suc"];
					$NomSuc = $row['Nom_Suc'];
					$CodCmn = $row["Cod_Cmn"];
					$CodCdd = $row["Cod_Cdd"];
					$FonSuc = $row["Fon_Suc"];
					$FaxSuc = $row["Fax_Suc"];
					mssql_free_result($result); 
					$flagReadOnlySuc = true;
				}
				$accion = "newctt";
				if ($RutPer != "") {
					$RutPer = str_replace(".", "", strtoupper($RutPer));
					$RutPer = str_replace("-", "", strtoupper($RutPer));
					$RutPer = substr($RutPer, 0, -1)."-".substr($RutPer, -1);
					//$query = "vm_s_per_tipdoc ".$doc_id.", '".$RutPer."'";
					$result = mssql_query ("vm_s_per_tipdoc ".$doc_id.", '".$RutPer."'", $db)	or die ("No se pudo leer datos del Cliente");
					if ($row = mssql_fetch_array($result)) {
						$CodPer = $row['Cod_Per'];
						$AppPat = $row['Pat_Per'];
						$AppMat = $row['Mat_Per'];
						$NomPer = $row['Nom_Per'];
						$CodSex = $row['Sex'];
						mssql_free_result($result); 
					
						$result = mssql_query ("vm_ctt_s $Cod_Clt, $Cod_Suc", $db)
									or die ("No se pudo leer datos del Contacto (".$CodPer.")");
						while ($row = mssql_fetch_array($result)) {
							if ($row['Cod_Per'] == $CodPer) {
								$MailCt = $row['Mail_Ctt'];
								$CelCtt = $row['Cel_Ctt'];
								$FonSuc = $row['Fon_Ctt'];
								mssql_free_result($result); 
								$flagReadOnlyCtt = true;
								break;
							}
						}
					}
				}
				
				$keychars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
				$length = 16;
				// RANDOM KEY GENERATOR
				$randkey = "";
				$max=strlen($keychars)-1;
				for ($i=0;$i<$length;$i++) $randkey .= substr($keychars, rand(0, $max), 1);
				$random = 1;
			}
			
			//$query = "vm_pro_s ".$CodPro;
			$result = mssql_query ("vm_pro_s ".$CodPro, $db)
							or die ("No se pudo leer Codigo de Profesion");
			if ($row = mssql_fetch_array($result))	$NomPro = $row["Nom_Pro"];
			mssql_free_result($result); 
			
			//$query = "vm_esp_s ".$CodEsp;
			$result = mssql_query ("vm_esp_s ".$CodEsp, $db)
							or die ("No se pudo leer Codigo de Especialidad");
			if ($row = mssql_fetch_array($result))	$NomEsp = $row["Nom_Esp"];
			mssql_free_result($result); 

		}
	}
}	
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
<script type="text/javascript">
new UvumiDropdown('dropdown-scliente');
</script>
<LINK href="../Include/estilos.css" type=text/css rel=stylesheet>
<script language="JavaScript" src="../Include/SoloNumeros.js"></script>
<script language="JavaScript" src="../Include/ValidarDataInput.js"></script>
<script language="JavaScript" src="../Include/fngenerales.js"></script>
<script language="JavaScript" src="../Include/validarRut.js"></script>
<script type="text/javascript" src="../js/jquery-1.3.2.min.js"></script>
<script type="text/javascript">
	var $j = jQuery.noConflict();
    $j(document).ready
	(
		//$j(":input:first").focus();
		
		function()
		{
	        $j("form#searchCmn").submit(function(){
				$j.post("../ajax-search-cdd.php",{
					search_type: "cdd",
					param_filter: $j("#cmn").val()
				}, function(xml) {
					listLinCdd(xml);
				});return false;
		    });
			
	        $j("form#searchPro").submit(function(){
				$j.post("../ajax-search-esp.php",{
					search_type: "esp",
					param_filter: $j("#pro").val()
				}, function(xml) {
					listLinEsp(xml);
				});return false;
		    });
			
	        //$j("form#searchPer").submit(function(){
			//	$j.post("../ajax-search-per.php",{
			//		search_type: "clt",
			//		param_filter: $j("#dfRutUsrIn").val()
			//	}, function(xml) {
			//		listLinPer(xml);
			//	});return false;
		    //});
			
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
	
    function filterPer(obj)
    {
		//alert("filterPer");
		if (obj.value != "") {
			//if (validarRutCompleto('dfRutUsrIn')) $j("form#searchPer").submit();
			if (validarRutCompleto('dfRutUsrIn')) f4.submit();
			else {
			  alert("Rut invalido. Intente nuevamente");
			  //$('dfRutUsrIn').focus();
			}
		}
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

    function listLinPer(xml)
    {
		var	xisper = false;
		//alert("listLinPer");
		
        $j("filter",xml).each(
			function(id) {
	            filter=$j("filter",xml).get(id);
				//alert($j("code",filter).text() + "=" + $j("value",filter).text());
	            if ($j("code",filter).text() == "rutfmt") $j("#dfRutUsrIn").val($j("value",filter).text());
	            if ($j("code",filter).text() == "rut")    $j("#dfRutUsr").val($j("value",filter).text());
	            if ($j("code",filter).text() == "patper") {
					$j("#dfAppPatIn").val($j("value",filter).text());
					$j("#dfAppPat").val($j("value",filter).text());
				}
	            if ($j("code",filter).text() == "matper") {
					$j("#dfAppMatIn").val($j("value",filter).text());
					$j("#dfAppMat").val($j("value",filter).text());
				}
	            if ($j("code",filter).text() == "nomper") {
					$j("#dfNomUsrIn").val($j("value",filter).text());
					$j("#dfNomUsr").val($j("value",filter).text());
				}
	            if ($j("code",filter).text() == "sexo") f3.rbSexoIn[parseInt($j("value",filter).text())-1].checked = true;
				xisper = true;
	        }
		);
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
   <div id="work">
			<div id="back-registro">
            	<div style="width:765px; margin:0 auto 0 160px; padding-top:10px;">
                
				<TABLE WIDTH="80%" BORDER="0" style="text-align:left;">
					<TR>
					  <TD width="80%" VALIGN="top">
						 <TABLE border="0" cellpadding="1" cellspacing="0" width="100%" >
							<TR><TD colspan=2 CLASS="etiqueta">Datos del Contacto</TD></TR>
							<TR>		
							   <TD  CLASS="etiqueta" WIDTH="185">RUT:&nbsp;</TD>
							   <TD CLASS="etiqueta" >
							   <form method="post" id="searchPer" name="searchPer" action="registrar_ctt.php?clt=<?php echo $RutClt; ?>&xis=1&suc=<?php echo $Cod_Suc; ?>&acc=newctt&ret=<?php echo $ret; ?>&cot=<?php echo $Cod_Cot; ?>">
								 <INPUT name="dfRutUsrIn" id="dfRutUsrIn" size="12" maxLength="12" class="textfieldv2" style="TEXT-TRANSFORM: uppercase" <?php if (!$flagReadOnlyCtt) { ?>onblur="filterPer(this)" onKeyPress="javascript:return soloRUT(event)"<?php } ?>  value="<?php if ($RutPer != "") echo formatearRut($RutPer); ?>" <?php if ($flagReadOnlyCtt) echo "readOnly" ?> />
							   </form>
							   </TD>
							</TR>
							<TR>
								<TD CLASS="etiqueta">Apellido Paterno:&nbsp;</TD>
								<TD>
									<INPUT name="dfAppPatIn" id="dfAppPatIn" size="30" maxLength="80" class="textfieldv2" style="TEXT-TRANSFORM: uppercase" onchange="llenarCampo(this)" value="<?php echo $AppPat; ?>" /></TD>
							</TR>
                            <TR>
								<TD CLASS="etiqueta">Materno:&nbsp;</TD>
								<TD>
									<INPUT name="dfAppMatIn" id="dfAppMatIn" size="30" maxLength="80" class="textfieldv2" style="TEXT-TRANSFORM: uppercase" onchange="llenarCampo(this)" value="<?php echo $AppMat; ?>" />
									</TD>
							</TR>
							<TR>
								<TD CLASS="etiqueta">Nombres:&nbsp;</TD>
								<TD><INPUT name="dfNomUsrIn" id="dfNomUsrIn" size="60" maxLength="80" class="textfieldv2" style="TEXT-TRANSFORM: uppercase" onchange="llenarCampo(this)" value="<?php echo $NomPer; ?>" /></TD>
							</TR>
							<?php if ($xis >= 0) { ?>
							<TR>
								<TD CLASS="etiqueta">Tipo de Contacto:&nbsp;</TD>
								<TD>
										<?php if ($xis == 0) { ?>
										<form id="searchEsp" name="searchEsp">
										<select id="esp" name="esp" class="textfieldv2" onChange="llenarEsp(this)">
											<option selected value="_NONE">Seleccione Tipo Contacto</option>
											<?php //Seleccionar las ciudades
											$CodPro = -1; 
											$sp = mssql_query("vm_esppro_s -1",$db);
											while($row = mssql_fetch_array($sp))
											{
												?>
												<option value="<?php echo $row['Cod_Esp'] ?>"><?php echo $row['Nom_Esp'] ?></option>
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
							    <form id="F3" name="F3">
								<TD>
								<INPUT id="rbSexoIn" name="rbSexoIn" type="radio" onclick="llenarCampo(this)" value="1" <?php if ($CodSex == 1) echo "checked"; ?>>&nbsp;<strong>Mujer</strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
								<INPUT id="rbSexoIn" name="rbSexoIn" type="radio" onclick="llenarCampo(this)" value="2" <?php if ($CodSex == 2) echo "checked"; ?>>&nbsp;<strong>Hombre</strong>
								</TD>
								</form>
							</TR>
							<TR>		
							   <TD CLASS="etiqueta">Tel&eacute;fono:&nbsp;<SPAN class="dator">*</SPAN></TD>
							   <TD>
								 <INPUT name="dfTelefonoUsrIn" id="dfTelefonoUsrIn" size="12" maxLength="15" class="textfieldv2" onKeyPress="SoloNumeros(this)"  onchange="llenarCampo(this)" value="<?php echo $FonSuc; ?>" /></TD>
							</TR>
                            <TR>		
							   <TD CLASS="etiqueta">M&oacute;vil:&nbsp;<SPAN class="dator">*</SPAN></TD>
							   <TD>
								 <INPUT name="dfMovilUsrIn" id="dfMovilUsrIn" size="12" maxLength="15" class="textfieldv2" onKeyPress="SoloNumeros(this)" onchange="llenarCampo(this)" value="<?php echo $CelCtt; ?>" />
							   </TD>
							</TR>
							<TR>
								<TD CLASS="etiqueta">E-Mail:&nbsp;</TD>
								<TD><INPUT name="dfemailIn" id="dfemailIn" size="60" maxLength="80" class="textfieldv2" style="TEXT-TRANSFORM: none" onchange="llenarCampo(this)" value="<?php echo $MailCt; ?>" /></TD>
							</TR>
							<TR>		
							   <TD CLASS="etiqueta">Contrase&ntilde;a:&nbsp;<SPAN class="dator">**</SPAN></TD>
							   <TD>
								 <INPUT type="password" name="dfPasswordIn" size="15" maxLength="30" class="textfieldv2" onchange="llenarCampo(this)" value="<?php echo $randkey; ?>" ReadOnly />
							   </TD>
							</TR>
                            <TR>		
							   <TD CLASS="etiqueta">Repita Contrase&ntilde;a:&nbsp;<SPAN class="dator">***</SPAN></TD>
							   <TD>
								<INPUT type="password" name="dfPassword2In" size="15" maxLength="30" class="textfieldv2" onchange="llenarCampo(this)" value="<?php echo $randkey; ?>" ReadOnly />
							   </TD>
							</TR>
							<TR>
								<TD CLASS ="label_top" colspan="2">&nbsp;</TD>
							</TR>
							<TR>
								<TD colspan="2" CLASS="dato">
                                <span style="float:left; width:30px;color:#6abfbf;">*</span>
									<span style="float:left;">Incluya c&oacute;digo de &aacute;rea para tel&eacute;fonos fuera de la Capital </span>
								</TD>
							</TR>
							<TR>
								<TD colspan="2" CLASS="dato"><span style="float:left; width:30px;color:#6abfbf;">**</span>
									<span style="float:left;">Ingrese una clave a su elecci&oacute;n (Debe tener entre 6 y 30 caracteres de largo).</span>
									</TD>
							</TR>
							<TR>
								<TD colspan="2" CLASS="dato"><span style="float:left; width:30px;color:#6abfbf;">**</span>
									<span style="float:left;">Reingrese su clave a modo de verificaci&oacute;n</span>
									</TD>
							<?php } ?>
							<TR><TD colspan="2" CLASS="dato">&nbsp;</TD></TR>
							</TR>
						  </TABLE>
					  </TD>   
					</TR>
					<TR><TD>
					<form ID="F2" method="post" name="F2" action="ing_contacto.php?ret=<?php echo $ret; ?>"
						  <?php if (!$bExisteUsr) echo "onsubmit=\"return checkDataFichaUsr(this,".$cod_tipper.")\""; ?> AUTOCOMPLETE="on">
						  <TABLE border="0" cellpadding="0" cellspacing="0" width="100%">
							<TR>
								<TD class="datoc" width="20%">&nbsp;</TD>
								<TD class="datoc" width="20%">
									<input type="submit" name="Enviar" value="Enviar" class="btn">
								</TD>
								<TD class="datoc" width="20%">
									<input type="BUTTON" name="Volver" value="Cerrar" class="btn"
										   onClick="javascript:window.close()">
								</TD>
								<TD class="datoc" width="20%">
								<?php if ($ret == 1) { ?>
								&nbsp;
								<?php } else { ?>
									<input type="BUTTON" name="Volver" value="Volver" class="btn"
										   onClick="javascript:history.back()">
								<?php } ?>
								</TD>
							</TR>
						  </TABLE>
						  <INPUT type="hidden" name="dfRutClt" value="<?php echo $RutClt; ?>">
						  <INPUT type="hidden" name="dfCodSuc" value="<?php echo $Cod_Suc; ?>">
						  <INPUT type="hidden" name="dfRutUsr" id="dfRutUsr" value="<?php echo $RutPer; ?>">
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
						  <INPUT type="hidden" name="dfPassword" value="<?php echo $randkey; ?>">
						  <INPUT type="hidden" name="dfPassword2" value="<?php echo $randkey; ?>">
						  <INPUT type="hidden" name="random" value="<?php echo $random; ?>">
						  <INPUT type="hidden" name="dfCodCot" value="<?php echo $Cod_Cot; ?>">
					</form>
					</TD></TR>
				</TABLE>
            </div>
        </div>
    </div>
    <div id="footer"></div>
</div>
<script language="javascript">
	var f1;	
	var f2;
	var f3;
	var f4;
	
	f1 = document.F1;	
	f2 = document.F2;
	f3 = document.F3;
	f4 = document.searchPer;
<?php
  if (isset($_GET['accion'])) {
	$page  = "nueva_cot.php?rut=".(($cod_tipper == 2) ? $RutClt : $RutPer);
	if ($Cod_Suc > 0) $page .= "&suc=".$Cod_Suc."&ctt=".$Cod_Ctt."&cot=".$Cod_Cot;
    echo "	parent.opener.document.F2.action=\"".$page."\"\n";
    echo "	parent.opener.document.F2.submit();\n";
	echo "	window.close();\n";
  }
?>
</script>
</body>
</html>