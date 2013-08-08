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
$contexto = isset($_GET['contexto']) ? $_GET['contexto'] : "";
if (isset($_SESSION['CodPer'])) $Cod_Per = intval($_SESSION['CodPer']);
if (isset($_SESSION['CodClt'])) $Cod_Clt = intval($_SESSION['CodClt']);
if (isset($_GET['clt'])) {
	$RutClt = ok($_GET['clt']);
	if (!strrpos($RutClt,"-")) $RutClt = substr($RutClt, 0, -1)."-".substr($RutClt, -1);
	$xis     = intval(ok($_GET['xis']));
	$Cod_Suc = isset($_GET['suc']) ? ok($_GET['suc']) : 0;
	$Cod_Ctt = isset($_GET['ctt']) ? ok($_GET['ctt']) : 0;
	$Cod_Cot = isset($_GET['cot']) ? ok($_GET['cot']) : 0;
	if ($xis == 1) { // Caso existe como Persona en Vestmed
		$doc_id = 1;
		//$query = "vm_s_per_tipdoc ".$doc_id.", '".$RutClt."'";
		$result = mssql_query ("vm_s_per_tipdoc ".$doc_id.", '".$RutClt."'", $db)	or die ("No se pudo leer datos del Cliente");
		if ($row = mssql_fetch_array($result)) {
			$cod_tipper = $row["Cod_TipPer"];
			$Cod_Per	= $row['Cod_Per'];
			if ($cod_tipper == 2) { // Persona Juridica
				$RutPer 	  = "";
				$NombreClt    = $row["RznSoc_Per"];
				$NombreFanClt = $row["NomFan_Per"];
			}
			else { // Persona Natural
				$RutPer 	  = $row["Num_Doc"];
				$NombreClt    = "";
				$NombreFanClt = "";
				$AppPat = $row["Pat_Per"];
				$AppMat = $row["Mat_Per"];
				$NomPer = $row["Nom_Per"];
			}
			$web     = $row["www_Per"];
			$Cod_Clt = $row["Cod_Clt"];
			$id_per	 = $row["Cod_Per"];
			mssql_free_result($result); 

			if ($Cod_Clt != "") { // Si es cliente
				$EsCliente = true;
				$CodPro = -1;
				$CodEsp = -1;
				$accion = "newsuc";
				if ($Cod_Suc > 0) {
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
						$flagReadOnlySuc = true;
					}
					$accion = "newctt";
				}

				$keychars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
				$length = 16;
				// RANDOM KEY GENERATOR
				$randkey = "";
				$max=strlen($keychars)-1;
				for ($i=0;$i<$length;$i++) $randkey .= substr($keychars, rand(0, $max), 1);
				$random = 1;
			}
			else { // No es cliente ==> No puede ser Persona Juridica, por lo tanto asumimos Natural
				//$query = "vm_per_s ".$id_per;
				$result = mssql_query ("vm_per_s ".$id_per, $db)
								or die ("No se pudo leer datos de la Persona (".$id_per.")");
				if ($row = mssql_fetch_array($result)) {
					$RutPer = $row["Num_Doc"];
					$AppPat = $row["Pat_Per"];
					$AppMat = $row["Mat_Per"];
					$NomPer = $row["Nom_Per"];
					$CodPro = $row["Cod_Pro"];
					$CodEsp = $row["Cod_Esp"];
					$CodSex = $row["Sex"];
					mssql_free_result($result);
					
					//$query = "vm_usrweb_ctt_s ".$id_per;
					$result = mssql_query ("vm_usrweb_ctt_s ".$id_per, $db)
									or die ("No se pudo leer datos del Contacto (".$id_per.")");
					if ($row = mssql_fetch_array($result)) {
						$DirSuc = $row["Dir_Suc"];
						$CodCmn = $row["Cod_Cmn"];
						$CodCdd = $row["Cod_Cdd"];
						$FonSuc = $row["Fon_Ctt"];
						$FaxSuc = $row["Fax_Suc"];
						$CelCtt = $row["Cel_Ctt"];
						$MailCt = $row["Mail_Ctt"];
					}
					mssql_free_result($result);
				}
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

			$NomCmn = "";
			//$query = "vm_cmn_s ".$CodCmn;
			$result = mssql_query ("vm_cmn_s ".$CodCmn, $db)
							or die ("No se pudo leer Codigo de la Comuna");
			if ($row = mssql_fetch_array($result))	$NomCmn = $row["Nom_Cmn"];
			mssql_free_result($result); 
			
			$NomCdd = "";
			//$query = "vm_cdd_s ".$CodCdd;
			$result = mssql_query ("vm_cdd_s ".$CodCdd, $db)
							or die ("No se pudo leer Codigo de la Ciudad");
			if ($row = mssql_fetch_array($result))	$NomCdd = $row["Nom_Cdd"];
			mssql_free_result($result); 
			
			//$bExisteUsr = false;
			//$query = "vm_s_usrweb ".$doc_id.", '".$RutPer."'";
			//$result = mssql_query ($query, $db)
			//				or die ("No se pudo leer datos del usuario");
			//if ($row = mssql_fetch_array($result)) {
			//	$bExisteUsr = true;
			//	$clave 	    = $row["Pwd_web"];
			//}
			//mssql_free_result($result); 
		}
		else {
			// Resultado incoherente puesto que se informó que existía 
		}
	}
	else { // Caso NO existe como Persona en Vestmed
		$accion = "newclt";
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
		$keychars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
		$length = 16;
		// RANDOM KEY GENERATOR
		$randkey = "";
		$max=strlen($keychars)-1;
		for ($i=0;$i<$length;$i++) $randkey .= substr($keychars, rand(0, $max), 1);
		$random = 1;
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
   <div id="work">
    	
<?php if (isset($HTTP_GET_VARS["clt"])) { ?>
			<div id="back-registro">
            	<div style="width:765px; margin:0 auto 0 160px; padding-top:10px;">
                
				<TABLE WIDTH="80%" BORDER="0" style="text-align:left;">
					<TR>
					  <TD width="80%" VALIGN="top">
						 <TABLE border="0" cellpadding="1" cellspacing="0" width="100%" >
<?php if ($cod_tipper == 2) { ?>
							<TR>		
							   <TD CLASS="etiqueta" WIDTH="185">RUT:&nbsp;</TD>
							   <TD>
								 <INPUT name="dfRutCltIn" id="dfRutCltIn" size="15" maxLength="30" class="textfieldv2" style="TEXT-TRANSFORM: uppercase" value="<?php echo formatearRut($RutClt); ?>" readOnly>
							   </TD>
							</TR>
							<TR>
								<TD  CLASS="etiqueta">Raz&oacute;n Social:&nbsp;</TD>
								<TD><INPUT name="dfNombreIn" size="60" maxLength="80" class="textfieldv2" style="TEXT-TRANSFORM: uppercase" onchange="llenarCampo(this)" value="<?php echo $NombreClt; ?>" <?php if ($xis==1) echo "readOnly" ?>></TD>
							</TR>
							<TR>		
							   <TD CLASS="etiqueta">Nombre de Fantas&iacute;a:&nbsp;</TD>
							   <TD>
								 <INPUT name="dfNombreFantasiaIn" size="60" maxLength="80" class="textfieldv2" style="TEXT-TRANSFORM: uppercase" onchange="llenarCampo(this)" value="<?php echo $NombreFanClt; ?>" <?php if ($xis==1) echo "readOnly" ?>>
							   </TD>
							</TR>
							<TR>		
							   <TD CLASS="etiqueta">P&aacute;gina Web:&nbsp;</TD>
							   <TD>
								 <INPUT name="dfWebIn" size="40" maxLength="80" class="textfieldv2" onchange="llenarCampo(this)" value="<?php echo $web; ?>" <?php if ($xis==1) echo "readOnly" ?>>&nbsp;
								 <span class=dato>Ej: www.vestmed.cl</span>
							   </TD>
							</TR>
							<?php if ($xis >= 0) { ?>
							<TR>
								<TD CLASS ="label_top" colspan="2">&nbsp;</TD>
							</TR>
							
							<TR><TD colspan=2 CLASS="etiqueta">Datos de la Sucursal</TD></TR>
							<TR>		
							   <TD CLASS="etiqueta">Nombre:&nbsp;</TD>
							   <TD><INPUT name="dfNomSucIn" size="30" maxLength="30" class="textfieldv2" style="TEXT-TRANSFORM: uppercase" onchange="llenarCampo(this)" value="<?php echo $NomSuc; ?>" <?php if ($flagReadOnlySuc) echo "readOnly" ?>></TD>
							</TR>
							<TR>		
							   <TD CLASS="etiqueta">Direcci&oacute;n:&nbsp;</TD>
							   <TD><INPUT name="dfDireccionIn" size="60" maxLength="80" class="textfieldv2" style="TEXT-TRANSFORM: uppercase" onchange="llenarCampo(this)" value="<?php echo $DirSuc; ?>" <?php if ($flagReadOnlySuc) echo "readOnly" ?>></TD>
							</TR>
							<TR>
								<TD CLASS="etiqueta">Comuna:&nbsp;</TD>
                                	<?php if ($xis >= 0 And $Cod_Suc == 0) { ?>
                                    <TD align="left">
										<form id="searchCmn">
										
										<select id="cmn" name="cmn" class="textfieldv2" onChange="filterCmn(this)">
											<option selected value="_NONE">Seleccione una Comuna</option>
											<?php //Seleccionar las ciudades
											$sp = mssql_query("vm_cmn_s",$db);
											while($row = mssql_fetch_array($sp))
											{
												?>
												<option value="<?php echo $row['Cod_Cmn'] ?>"<?php if ($row['Cod_Cmn'] == $CodCmn) echo " selected"; ?>><?php echo $row['Nom_Cmn'] ?></option>
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
                                </Tr>
                                <tr>
                                	
                                    <Td class="etiqueta">Ciudad:&nbsp;</Td>
										
										<TD><?php if ($xis >= 0 And $Cod_Suc == 0) { ?>
										<form id="searchCdd" name="searchCdd">
										<select id="cdd" name="cdd" class="textfieldv2" onChange="llenarCdd(this)">
											<option selected value="_NONE">Seleccione una Ciudad</option>
											<?php //Seleccionar las ciudades
											$sp = mssql_query("vm_cddcmn_s NULL, $CodCmn",$db);
											while($row = mssql_fetch_array($sp))
											{
												?>
												<option value="<?php echo $row['Cod_Cdd'] ?>"<?php if ($row['Cod_Cdd'] == $CodCdd) echo " selected"; ?>><?php echo $row['Nom_Cdd'] ?></option>
												<?php
											}
											?>
										</select>
										</TD>
										</form>
										<?php } else { ?>
										<INPUT name="dfNomCdd" size="20" maxLength="20" class="textfieldv2" style="TEXT-TRANSFORM: uppercase" readOnly value="<?php echo $NomCdd; ?>">
										</TD>										
										<?php } ?>
							</TR>
							<TR>		
							   <TD CLASS="etiqueta">Tel&eacute;fono&nbsp;<SPAN class=dator>(1)</SPAN>:&nbsp;</TD>
							   <TD align="left">
										 <INPUT name="dfTelefonoSucIn" size="12" maxLength="15" class="textfieldv2" onKeyPress="SoloNumeros(this)"  onchange="llenarCampo(this)" value="<?php echo $FonSuc; ?>" <?php if ($flagReadOnlySuc) echo "readOnly" ?>>
										</TD>
                             </TR>
							<TR>
                                <TD CLASS="etiqueta"><span class=dato>FAX&nbsp;<SPAN class=dator>(1)</SPAN>:</span>&nbsp;</TD>
                                <TD align="left">
                               
                                     <INPUT name="dfFaxSucIn" size="12" maxLength="15" class="textfieldv2" onKeyPress="SoloNumeros(this)" onchange="llenarCampo(this)" value="<?php echo $FaxSuc; ?>" <?php if ($flagReadOnlySuc) echo "readOnly" ?>></TD>
							</TR>
							<?php } ?>
							<TR>
								<TD CLASS ="label_top" colspan="2">&nbsp;</TD>
							</TR>
							
							<TR><TD colspan=2 CLASS="etiqueta">Datos del Contacto</TD></TR>
							<TR>		
							   <TD  CLASS="etiqueta" WIDTH="185">RUT:&nbsp;</TD>
							   <TD CLASS="etiqueta" >
								 <INPUT name="dfRutUsrIn" id="dfRutUsrIn" size="12" maxLength="12" class="textfieldv2" style="TEXT-TRANSFORM: uppercase" onblur="formatearRut('dfRutUsrIn','dfRutUsr')" value="<?php if ($RutPer != "") echo formatearRut($RutPer); ?>" />
							   </TD>
							</TR>
							<TR>
								<TD CLASS="etiqueta">Apellido Paterno:&nbsp;</TD>
								<TD>
									<INPUT name="dfAppPatIn" size="30" maxLength="80" class="textfieldv2" style="TEXT-TRANSFORM: uppercase" onchange="llenarCampo(this)" value="<?php echo $AppPat; ?>" <?php if ($flagReadOnlyCtt) echo "readOnly" ?>></TD>
							</TR>
                            <TR>
								<TD CLASS="etiqueta">Materno:&nbsp;</TD>
								<TD>
									<INPUT name="dfAppMatIn" size="30" maxLength="80" class="textfieldv2" style="TEXT-TRANSFORM: uppercase" onchange="llenarCampo(this)" value="<?php echo $AppMat; ?>" <?php if ($flagReadOnlyCtt) echo "readOnly" ?>>
									</TD>
							</TR>
                           
							<TR>
								<TD CLASS="etiqueta">Nombres:&nbsp;</TD>
								<TD><INPUT name="dfNomUsrIn" size="60" maxLength="80" class="textfieldv2" style="TEXT-TRANSFORM: uppercase" onchange="llenarCampo(this)" value="<?php echo $NomPer; ?>" <?php if ($flagReadOnlyCtt) echo "readOnly" ?>></TD>
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
								<TD>
								<INPUT id="rbSexoIn" name="rbSexoIn" type="radio" onclick="llenarCampo(this)" value="2" <?php if ($CodSex == 2) echo "checked"; ?> <?php if ($flagReadOnlyCtt) echo "readOnly" ?>>&nbsp;<strong>Hombre</strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
								<INPUT id="rbSexoIn" name="rbSexoIn" type="radio" onclick="llenarCampo(this)" value="1" <?php if ($CodSex == 1) echo "checked"; ?> <?php if ($flagReadOnlyCtt) echo "readOnly" ?>>&nbsp;<strong>Mujer</strong>
								</TD>
							</TR>
							<TR>		
							   <TD CLASS="etiqueta">Tel&eacute;fono de contacto&nbsp;:&nbsp;<SPAN class="dator">*</SPAN></TD>
							   <TD>
								 <INPUT name="dfTelefonoUsrIn" size="12" maxLength="15" class="textfieldv2" onKeyPress="SoloNumeros(this)"  onchange="llenarCampo(this)" value="<?php echo $FonSuc; ?>" <?php if ($flagReadOnlyCtt) echo "readOnly" ?>></TD>
							</TR>
                            <TR>		
							   <TD CLASS="etiqueta">Fax:&nbsp;<SPAN class="dator">*</SPAN></TD>
							   <TD>
								 <INPUT name="dfFaxUsrIn" size="12" maxLength="15" class="textfieldv2" onKeyPress="SoloNumeros(this)" onchange="llenarCampo(this)" value="<?php echo $FaxSuc; ?>" <?php if ($flagReadOnlyCtt) echo "readOnly" ?>>
							   </TD>
							</TR>
                            <TR>		
							   <TD CLASS="etiqueta">M&oacute;vil:&nbsp;<SPAN class="dator">*</SPAN></TD>
							   <TD>
								 <INPUT name="dfMovilUsrIn" size="12" maxLength="15" class="textfieldv2" onKeyPress="SoloNumeros(this)" onchange="llenarCampo(this)" value="<?php echo $CelCtt; ?>" <?php if ($flagReadOnlyCtt) echo "readOnly" ?>>
							   </TD>
							</TR>
							<TR>
								<TD CLASS="etiqueta">E-Mail:&nbsp;</TD>
								<TD><INPUT name="dfemailIn" size="60" maxLength="80" class="textfieldv2" style="TEXT-TRANSFORM: none" onchange="llenarCampo(this)" value="<?php echo $MailCt; ?>" <?php if ($flagReadOnlyCtt) echo "readOnly" ?>></TD>
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
<?php } else { ?>
							<TR><TD colspan=2><h1 class="borde-abajo titulo-registro">Formulario <span>de registro</span></h1></TD></TR>
                            <TR><TD colspan=2><span class="titulo-pequeno">Registrate y podras disfrutar de todos los beneficios  que te entrega VESTMED online.</TD></TR>
							<TR>		
							   <TD CLASS="etiqueta" WIDTH="185">RUT:</TD>
							   <TD>
								 <INPUT name="dfRutUsrIn" id="dfRutUsrIn" size="12" maxLength="12" class="textfieldv2" style="TEXT-TRANSFORM: uppercase" onblur="formatearRut('dfRutUsrIn','dfRutUsr')" value="<?php if ($RutPer != "") echo formatearRut($RutPer); ?>" <?php if ($cod_tipper==1) echo "readOnly" ?>>&nbsp;Ej: 99999999-X
							   </TD>
							</TR>
							<TR>
								<TD CLASS="etiqueta">Apellido Paterno:</TD>
								<TD>
									<INPUT name="dfAppPatIn" size="30" maxLength="80" class="textfieldv2" style="TEXT-TRANSFORM: uppercase" onchange="llenarCampo(this)" value="<?php echo $AppPat; ?>" /></TD>
							</TR>
                            <TR>
								<TD CLASS="etiqueta">Apellido Materno:</TD>
								<TD>
									<INPUT name="dfAppMatIn" size="30" maxLength="80" class="textfieldv2" style="TEXT-TRANSFORM: uppercase" onchange="llenarCampo(this)" value="<?php echo $AppMat; ?>" />
									</TD>
							</TR>
							<TR>
								<TD CLASS="etiqueta">Nombres:</TD>
								<TD><INPUT name="dfNomUsrIn" size="60" maxLength="80" class="textfieldv2" style="TEXT-TRANSFORM: uppercase" onchange="llenarCampo(this)" value="<?php echo $NomPer; ?>" /></TD>
							</TR>
							<?php if ($xis >= 0) { ?>
							<TR>
								<TD CLASS="etiqueta">Profesi&oacute;n:&nbsp;</TD>
								<TD >
									<TABLE WIDTH="100%" BORDER="0" align="center" cellpadding="0" cellspacing="0" >
									<TR>
										<?php if ($xis >= 0) { ?>
										<form id="searchPro" name="searchPro">
										<TD align="left">
										<select id="pro" name="pro" class="textfieldv2" onChange="filterPro(this)">
											<option selected value="_NONE">Seleccione una Profesi&oacute;n</option>
											<?php //Seleccionar las ciudades
											$sp = mssql_query("vm_pro_s", $db);
											while($row = mssql_fetch_array($sp))
											{
												?>
												<option value="<?php echo $row['Cod_Pro'] ?>"<?php if ($row['Cod_Pro'] == $CodPro) echo " selected"; ?>><?php echo $row['Nom_Pro'] ?></option>
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
									</TABLE>
								</TD>
							</TR>
                            <TR>
								<TD CLASS="etiqueta">Especialidad:</TD>
								<TD >
									<TABLE WIDTH="100%" BORDER="0" align="center" cellpadding="0" cellspacing="0" >
									<TR>
										<?php if ($xis >= 0) { ?>
										<form id="searchEsp" name="searchEsp">
										<TD>
										<select id="esp" name="esp" class="textfieldv2" onChange="llenarEsp(this)">
											<option selected value="_NONE">Seleccione una Especialidad</option>
											<?php //Seleccionar las ciudades
											$sp = mssql_query("vm_esppro_s $CodPro",$db);
											while($row = mssql_fetch_array($sp))
											{
												?>
												<option value="<?php echo $row['Cod_Esp'] ?>"<?php if ($row['Cod_Esp'] == $CodEsp) echo " selected"?>><?php echo $row['Nom_Esp'] ?></option>
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
									</TABLE>
								</TD>
							</TR>
							<TR>
								<TD CLASS="etiqueta">Sexo:&nbsp;</TD>
								<TD>
								<INPUT id="rbSexoIn" name="rbSexoIn" type="radio" onclick="llenarCampo(this)" value="2" <?php if ($CodSex == 2) echo "checked"; ?>>&nbsp;<strong>Hombre</strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
								<INPUT id="rbSexoIn" name="rbSexoIn" type="radio" onclick="llenarCampo(this)" value="1" <?php if ($CodSex == 1) echo "checked"; ?>>&nbsp;<strong>Mujer</strong>
								</TD>
							</TR>
							<TR>
								<TD CLASS="etiqueta">Direcci&oacute;n:&nbsp;</TD>
								<TD><INPUT name="dfDireccionIn" size="60" maxLength="80" class="textfieldv2" style="TEXT-TRANSFORM: uppercase" onchange="llenarCampo(this)" value="<?php echo $DirSuc; ?>" /></TD>
							</TR>
							<TR>
								<TD CLASS="etiqueta">Comuna:&nbsp;</TD>
								<TD>
									<TABLE WIDTH="100%" BORDER="0" align="center" cellpadding="0" cellspacing="0" >
									<TR>
										<?php if ($xis >= 0) { ?>
										<form id="searchCmn">
										<TD align="left">
										<select id="cmn" name="cmn" class="textfieldv2" onChange="filterCmn(this)">
											<option selected value="_NONE">Seleccione una Comuna</option>
											<?php //Seleccionar las ciudades
											$sp = mssql_query("vm_cmn_s",$db);
											while($row = mssql_fetch_array($sp))
											{
												?>
												<option value="<?php echo $row['Cod_Cmn'] ?>"<?php if ($row['Cod_Cmn'] == $CodCmn) echo " selected"; ?>><?php echo $row['Nom_Cmn'] ?></option>
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
									</TABLE>
								</TD>
							</TR>
                            <TR>
								<TD CLASS="etiqueta">Ciudad:&nbsp;</TD>
								<TD>
									<TABLE WIDTH="100%" BORDER="0" align="center" cellpadding="0" cellspacing="0" >
									<TR>

										<?php if ($xis >= 0) { ?>
										<form id="searchCdd" name="searchCdd">
										<TD>
										<select id="cdd" name="cdd" class="textfieldv2" onChange="llenarCdd(this)">
											<option selected value="_NONE">Seleccione una Ciudad</option>
											<?php //Seleccionar las ciudades
											$sp = mssql_query("vm_cddcmn_s NULL, $CodCmn",$db);
											while($row = mssql_fetch_array($sp))
											{
												?>
												<option value="<?php echo $row['Cod_Cdd'] ?>"<?php if ($row['Cod_Cdd'] == $CodCdd) echo " selected"; ?>><?php echo $row['Nom_Cdd'] ?></option>
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
									</TABLE>
								</TD>
							</TR>
							<TR>		
							   <TD CLASS="etiqueta">Tel&eacute;fono de contacto&nbsp;:<SPAN class="dator">*</SPAN></TD>
							   <TD>
								 <INPUT name="dfTelefonoUsrIn" size="12" maxLength="15" class="textfieldv2" onKeyPress="SoloNumeros(this)"  onchange="llenarCampo(this)" value="<?php echo $FonSuc; ?>" />
                                </TD>
                                </TR>
                             <tr>
                             	<td class="etiqueta">FAX: <SPAN class="dator">*</SPAN></td> 
                                <td>
                                 <INPUT name="dfFaxUsrIn" size="12" maxLength="15" class="textfieldv2" onKeyPress="SoloNumeros(this)" onchange="llenarCampo(this)" value="<?php echo $FaxSuc; ?>" /></TD>
							</TR>
                            <tr>
                            	<td class="etiqueta">M&oacute;vil&nbsp;:</td>
                                <td>
                                <INPUT name="dfMovilUsrIn" size="12" maxLength="15" class="textfieldv2" onKeyPress="SoloNumeros(this)" onchange="llenarCampo(this)" value="<?php echo $CelCtt; ?>" />
                                </td>
                            </tr>
							<TR>
								<TD CLASS="etiqueta">e-mail:&nbsp;</TD>
								<TD><INPUT name="dfemailIn" size="60" maxLength="80" class="textfieldv2" style="TEXT-TRANSFORM: none" onchange="llenarCampo(this)" value="<?php echo $MailCt; ?>" /></TD>
							</TR>
							<TR>		
							   <TD CLASS="etiqueta">Contrase&ntilde;a:<SPAN class="dator">**</SPAN></TD>
							   <TD>
								 <INPUT type="password" name="dfPasswordIn" size="15" maxLength="30" class="textfieldv2" onchange="llenarCampo(this)" value="<?php echo $randkey; ?>" ReadOnly /></TD>
							</TR>
                            <TR>		
							   <TD CLASS="etiqueta">Repita Contrase&ntilde;a:<SPAN class="dator">***</SPAN></TD>
							   <TD>
								 <INPUT type="password" name="dfPassword2In" size="15" maxLength="30" class="textfieldv2" onchange="llenarCampo(this)" value="<?php echo $randkey; ?>" ReadOnly /> </TD>
							</TR>
							<TR>
								<TD CLASS ="label_top" colspan="2">&nbsp;</TD>
							</TR>
							<TR>
								<TD colspan="2" CLASS="dato"><span style="float:left; width:30px;color:#6abfbf;">*</span>
									<span style="float:left;">Incluya c&oacute;digo de &aacute;rea para tel&eacute;fonos fuera de la Capital</span>
								</TD>
							</TR>
							<TR>
								<TD colspan="2" CLASS="dato"><span style="float:left; width:30px;color:#6abfbf;">**</span>
									<span style="float:left;">Ingrese una clave a su elecci&oacute;n (Debe tener entre 6 y 30 caracteres de largo).</span></TD>
							</TR>
							<TR>
								<TD colspan="2" CLASS="dato"><span style="float:left; width:30px;color:#6abfbf;">***</span>
									<span style="float:left;">Reingrese su clave a modo de verificaci&oacute;n</span></TD>
							<?php } ?>
							<TR><TD colspan="2" CLASS="dato">&nbsp;</TD></TR>
<?php } ?>
							</TR>
						  </TABLE>
					  </TD>   
					</TR>
					<TR><TD>
					<?php if ($accion == "newctt") { ?>
					<form ID="F2" method="post" name="F2" action="ing_contacto.php"
					<?php } else if ($accion == "newsuc") { ?>
					<form ID="F2" method="post" name="F2" action="ing_sucursal.php"
					<?php } else { ?>
					<form ID="F2" method="post" name="F2" action="ing_fichausr.php"
					<?php } ?>
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
								<TD class="datoc" width="20%">&nbsp;</TD>
							</TR>
						  </TABLE>
						  <INPUT type="hidden" name="dfRutClt" value="<?php echo $RutClt; ?>">
						  <?php if ($xis >= 0) { ?>
						  <INPUT type="hidden" name="dfNombre" value="<?php echo $NombreClt; ?>">
						  <INPUT type="hidden" name="dfNombreFantasia" value="<?php echo $NombreFanClt; ?>">
						  <INPUT type="hidden" name="dfWeb" value="<?php echo $web; ?>">
						  <INPUT type="hidden" name="dfNomSuc" value="<?php echo $NomSuc; ?>">
						  <INPUT type="hidden" name="dfCodSuc" value="<?php echo $Cod_Suc; ?>">
						  <INPUT type="hidden" name="dfTelefonoSuc" value="<?php echo $FonSuc; ?>">
						  <INPUT type="hidden" name="dfFaxSuc" value="<?php echo $FonSuc; ?>">
						  <?php } ?>
						  <INPUT type="hidden" name="dfRutUsr" id="dfRutUsr" value="<?php echo $RutPer; ?>">
						  <?php if ($xis >= 0) { ?>
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
						  <?php } ?>
					</form>
					</TD></TR>
				</TABLE>
<?php } else { ?>
			<div id="back-avisos">
        	 <img src="../images/registro/registro.png" class="titulo-principal-avisos" />
             <img src="../images/registro/imagen3.png" id="imagen-registro" />
            	<div>
				<form ID="F2" class="form_registros" AUTOCOMPLETE="off" method="POST" name="F2" ACTION="busqueda-cliente.php?contexto=<?php echo $contexto; ?>" onsubmit="return checkDataInscripcion(this)">
               	<TABLE border="0" cellpadding="1" cellspacing="0" width="70%">
				<TR>
					<TD colspan="2"><H1 class="borde-abajo">REGISTRO</H1></TD>
				</TR>
                <TR>
					<TD colspan="2"><h2 class="borde-abajo texto-pequeno" style="margin-top:20px;">Elegir tipo de cliente</H2></TD>
				</TR>
				<TR>
				<TD width="125" style="TEXT-ALIGN: LEFT; PADDING-TOP: 10px; font-size:13px;">
					<strong>INSTITUCIONAL</strong>&nbsp;
                </TD>
                <td><INPUT id="rbTipoClt" name="rbTipoClt" type="radio"  value="1"></td>
                </TR>
                <TR>
                <td style="TEXT-ALIGN: LEFT; PADDING-TOP: 10px; font-size:13px;">
                    <strong>USUARIO</strong>&nbsp;
					
				</TD>
                <td><INPUT id="rbTipoClt" name="rbTipoClt" type="radio" value="2"></td>
				</TR>
                <TR>
					<TD  colspan="2"><h2 class="borde-abajo texto-pequeno" style="margin-top:40px;">Ingresar Rut</H2></TD>
				</TR>

                <TR>
				<TD colspan="2" style="TEXT-ALIGN: LEFT; PADDING-TOP: 10px; PADDING-LEFT: 5px">
					<span style="float:left; margin-right:5px; font-size:15px; font-weight:bold; line-height:23px;">RUT:</span> <INPUT name="dfRutCltIn" id="dfRutCltIn" size="12" maxLength="12" onKeyPress="javascript:return soloRUT(event)" class="textfieldb" onblur="formatearRut('dfRutCltIn','dfRutClt')" />
				</TD>
				</TR>
				<TR><TD class="datoj"  colspan="2" style="PADDING-TOP: 20px; TEXT-ALIGN: left;" colspan="2">
					<input type="submit" name="Enviar" class="btn" value="Continuar" />
					<?php if ($contexto == "mnu") { ?>
					<input type="button" name="Cerrar" class="btn" value="Cerrar" onclick="window.close()" />
					<?php } ?>
					<INPUT type="HIDDEN" name="dfRutClt" id="dfRutClt" />
				</TD></TR>
				</TABLE>
                </form>
				<!--<TABLE border="0" cellpadding="1" cellspacing="0" width="70%" align="center">
				<TR>
					<TD colspan="2"><H1>INSCRIPCION</H2></TD>
				</TR>
				<form ID="F2" AUTOCOMPLETE="off" method="POST" name="F2" ACTION="busqueda-cliente.php" onsubmit="return checkDataInscripcion(this)">
				<TR>
				<TD valign="top" class="bienvenida" style="TEXT-ALIGN: RIGHT; PADDING-TOP: 10px">
					Tipo de Cliente :
				</TD>
				<TD class="bienvenida" style="TEXT-ALIGN: LEFT; PADDING-TOP: 10px">
					<INPUT id="rbTipoClt" name="rbTipoClt" type="radio" class="button2" value="1">&nbsp;<strong>Institucional</strong>
					<Br><Br>
					<INPUT id="rbTipoClt" name="rbTipoClt" type="radio" class="button2" value="2">&nbsp;<strong>Persona Natural</strong>
				</TD>
				</TR>
				<TR>
				<TD valign="top" class="bienvenida" style="TEXT-ALIGN: RIGHT; PADDING-TOP: 10px">
					Rut :
				</TD>
				<TD class="bienvenida" style="TEXT-ALIGN: LEFT; PADDING-TOP: 10px; PADDING-LEFT: 5px">
					<INPUT name="dfRutCltIn" id="dfRutCltIn" size="12" maxLength="12" onKeyPress="javascript:return soloRUT(event)" class="textfieldb" onblur="formatearRut('dfRutCltIn','dfRutClt')" />
				</TD>
				</TR>
				<TR><TD class="datoj" style="PADDING-TOP: 20px; TEXT-ALIGN: right" colspan="2">
					<input type="submit" name="Enviar" value=" Continuar " class="button2">
					<INPUT type="HIDDEN" name="dfRutClt" id="dfRutClt" />
				</TD></TR>
				</form>
				</TABLE>-->
<?php } ?>
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
<?php
  if (isset($_GET['accion'])) {
	if (ok($_GET['accion']) == "close")
		$page  = "nueva_cot.php?rut=".(($cod_tipper == 2) ? $RutClt : $RutPer);
	else if (ok($_GET['accion']) == "closeedt")
		$page = "escritorio_edtclt.php?clt=".(($cod_tipper == 2) ? $RutClt : $RutPer);
	if ($Cod_Suc > 0) $page .= "&suc=".$Cod_Suc."&ctt=".$Cod_Ctt;
	echo "	parent.opener.document.F2.action=\"".$page."\"\n";
    echo "	parent.opener.document.F2.submit();\n";
	echo "	window.close();\n";
  }
?>
</script>
</body>
</html>