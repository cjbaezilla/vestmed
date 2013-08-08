<?php
//Obtengo los datos de conexion de la base de datos
ini_set('display_errors', '0');
session_start();
include("config.php");
$lbl_btn = array ("insert" => "Agregar",
                  "update" => "Modificar",
		  "delete" => "Eliminar");

$Cod_Clt = ok($_GET['clt']);
$Cod_Cot = ok($_GET['cot']);
$Tip_Opc = ok($_GET['form']);
$Cod_Per = isset($_GET['per']) ? ok($_GET['per']) : 0;
$accion = isset ($_GET['acc']) ? ok($_GET['acc']) : "";
if ($Cod_Per > 0) {
	//$query = "vm_s_rutfct $Cod_Clt, $Cod_Per";
	$result = mssql_query ("vm_s_rutfct $Cod_Clt, $Cod_Per", $db) or die ("No se pudo leer datos del Rut de Facturaci&oacute;n<br>".$query);
	if (($row = mssql_fetch_array($result))) {
		$Cod_Clt = $row['Cod_Clt'];
		$Rut_Per = $row['Num_Doc'];
		$Nom_Per = utf8_encode($row['Nom_Clt']);
		$Nom_Fan = utf8_encode($row['NomFan_Per']);
		$Dir_Fct = utf8_encode($row['Dir_Fct']);
		$CodCmn  = $row['Cod_Cmn'];
		$CodCdd  = $row['Cod_Cdd'];
		$Tel_Fct = $row['Fon_Fct'];
		$Fax_Fct = $row['Fax_Fct'];
		$Web_Fct = $row['Web_Fct'];
		$Cod_TipPer = $row['Cod_TipPer'];
	}
}
if ($accion == "Agregar" Or $accion == "Modificar") {
	$Cod_Clt = ok($_POST['dfCodClt']);
	$Rut_Per = ok($_POST['dfRutPer']);
	$Nom_Per = ok(utf8_decode($_POST['dfNomPer']));
	$Nom_Fan = ok(utf8_decode($_POST['dfNomFan']));
	$Dir_Fct = ok(utf8_decode($_POST['dfDirFct']));
	$cod_cmn = ok($_POST['codcmn']);
	$cod_cdd = ok($_POST['codcdd']);
	$Tel_Fct = ok($_POST['dfTelFct']);
	$Fax_Fct = ok($_POST['dfFaxFct']);
	$Web_Fct = ok($_POST['dfWebFct']);
	$Cod_TipPer = ok($_POST['dfTipPer']);
	
	$Rut_Per = str_replace(".", "", $Rut_Per);
	$Rut_Per = str_replace("-", "", $Rut_Per);
	$Rut_Per = substr($Rut_Per,0,strlen($Rut_Per)-1)."-".substr($Rut_Per,strlen($Rut_Per)-1,1);
		
	$doc_id = 1;
	//$query = "vm_s_per_tipdoc $doc_id, '$Rut_Per'";
	$result = mssql_query ("vm_s_per_tipdoc $doc_id, '$Rut_Per'", $db) or die ("No se pudo leer datos de la Persona<br>");
	if (!($row = mssql_fetch_array($result))) {
		$result = mssql_query("vm_getfolio 'PER'", $db);
		if (($row = mssql_fetch_array($result))) {
			$Cod_Per	= $row['Tbl_fol'];
			//$query = "vm_per_i $Cod_Per, $Cod_TipPer, $doc_id, '$Rut_Per',NULL, NULL, NULL,'$Nom_Per', '$Nom_Fan', NULL, NULL, NULL, '$Web_Fct', NULL";
			$result = mssql_query("vm_per_i $Cod_Per, $Cod_TipPer, $doc_id, '$Rut_Per',NULL, NULL, NULL,'$Nom_Per', '$Nom_Fan', NULL, NULL, NULL, '$Web_Fct', NULL", $db) or die ("No se pudo actualizar datos de la Persona<br>");
		}
        }
	else $Cod_Per = $row['Cod_Per'];
	
	//$query = "vm_iu_rutfct $Cod_Clt, $Cod_Per, '$Dir_Fct', $cod_cmn, $cod_cdd, '$Tel_Fct', '$Fax_Fct', '$Web_Fct', '$Nom_Per', '$Nom_Fan'";
	$result = mssql_query("vm_iu_rutfct $Cod_Clt, $Cod_Per, '$Dir_Fct', $cod_cmn, $cod_cdd, '$Tel_Fct', '$Fax_Fct', '$Web_Fct', '$Nom_Per', '$Nom_Fan'",$db) or die ("No se pudo actualizar datos de la Factura<br>");
}
else if ($accion == "Eliminar") {
	$Cod_Clt = ok($_POST['dfCodClt']);
	$Rut_Per = ok($_POST['dfRutPer']);

	$Rut_Per = str_replace(".", "", $Rut_Per);
	$Rut_Per = str_replace("-", "", $Rut_Per);
	$Rut_Per = substr($Rut_Per,0,strlen($Rut_Per)-1)."-".substr($Rut_Per,strlen($Rut_Per)-1,1);
	
	$doc_id = 1;
	//$query = "vm_s_per_tipdoc $doc_id, '$Rut_Per'";
	$result = mssql_query ("vm_s_per_tipdoc $doc_id, '$Rut_Per'", $db) or die ("No se pudo leer datos de la Persona<br>");
	if (($row = mssql_fetch_array($result))) {
		$Cod_Per = $row['Cod_Per'];
		//$query = "vm_d_rutfct $Cod_Clt, $Cod_Per";
		$result = mssql_query("vm_d_rutfct $Cod_Clt, $Cod_Per",$db) or die ("No se pudo eliminar los datos de la Factura<br>");
	}
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/tr/xhtml1/DTD/xhtml1-transitional.dtd">
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
<link href="../Include/estilos.css" type="text/css" rel=stylesheet />
<script type="text/javascript" src="Include/SoloNumeros.js"></script>
<script type="text/javascript" src="Include/ValidarDataInput.js"></script>
<script type="text/javascript" src="Include/fngenerales.js"></script>
<script type="text/javascript" src="Include/validarRut.js"></script>
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
			
			$j("form#searchPer").submit(function(){
				$j.post("ajax-search-per.php",{
					search_type: "per",
					param_filter: $j("#rut").val()
				}, function(xml) {
					listLinPer(xml);
				});return false;
		    });
			
	        //NO SUBMIT TODAVIA $j("form#patternlist-form").submit();
	    }
		
	);
    //TODO: No se esta realizando el post, probablemente debido a que falta el evento submit.
    
    function filterPer(obj)
    {
		if (obj.value != "") {
			if (validarRutCompleto('rut')) {
				$j("form#searchPer").submit();
				f2.dfRutPer.value = obj.value;
			}
			else {
			  alert("Rut invalido. Intente nuevamente");
			  $('rut').focus();
			}
		}
    }
	
    function filterCmn(obj)
    {
		f2.codcmn.value = obj.value;
        $j("form#searchCmn").submit();
    }
	
    function llenarCdd(obj)
    {
		f2.codcdd.value = obj.value;
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
	
    function listLinPer(xml)
    {
        $j("filter",xml).each(
			function(id) {
	            filter=$j("filter",xml).get(id);
	            if ($j("code",filter).text() == "rut") $j("#dfrut").val($j("value",filter).text());
	            if ($j("code",filter).text() == "rutfmt") $j("#rut").val($j("value",filter).text());
	            if ($j("code",filter).text() == "nombre") $j("#NomCltFct").val($j("value",filter).text());
	            if ($j("code",filter).text() == "nomfan") $j("#NonFanFct").val($j("value",filter).text());
				if ($j("code",filter).text() == "tipper") {
					f2.dfTipPer.value = $j("value",filter).text();
				    if ($j("value",filter).text() == "1")
						mensaje = "<span id=\"mensaje\" style=\"color: red\">Solo se permiten rut de empresas</span>";
					else
					    mensaje = "<span id=\"mensaje\" style=\"color: red\"></span>";
					$j("#mensaje").replaceWith(mensaje);
				}
	            //if ($j("code",filter).text() == "nomper") $j("#nombre").val($j("value",filter).text());
	            //if ($j("code",filter).text() == "apppat") $j("#apppat").val($j("value",filter).text());
	            //if ($j("code",filter).text() == "appmat") $j("#appmat").val($j("value",filter).text());
				//if ($j("code",filter).text() == "sexo") f1.sexoIn[parseInt($j("value",filter).text())-1].checked = true;
	        }
		);
    }	
	
	function blurCampo(obj,campo) {
		eval("f2."+campo).value = obj.value;
	}
	
	function ValidaDataFct(form) {
		if (form.dfTipPer.value != "2") {
			alert("Solo se permiten rut de empresas");
			return false;
		}
		return true;
	}

        function ActualizaPadre()
        {
            parent.opener.ActualizarDirFacturas();
        }

</script>
</head>
<body>
<div id="body">
   <div id="work">
		<div id="back-registro3">
            <div style="width:605px; margin:0 auto 0 160px; padding-top:3px;">
				<table BORDER="0" CELLSPACING="1" CELLPADDING="1" width="100%">
					<tr>
						<td align="left" width="30%"><b>RUT:</b></td>
						<td align="left">
						<form id="searchPer" action="">
						<input name="rut" id="rut" type="text" class="dato" size="15" onblur="filterPer(this)" onKeyPress="javascript:return soloRUT(event)" value="<?php if ($Cod_Per > 0) echo formatearRut($Rut_Per); ?>"<?php if ($Cod_Per > 0) echo "ReadOnly"  ?> />
						<span id="mensaje" style="color: red"></span>
						<input type="hidden" name="dfrut" id="dfrut" value="<?php if ($Cod_Per > 0) echo $Rut_Per; ?>" />
						</form>
						</td>
					</tr>
					<tr>
						<td><b>Raz&oacute;n Social</b></td>
						<td align="left"><input name="NomCltFct" id="NomCltFct" type="text" class="dato" size="55" style="text-transform:uppercase" onblur="blurCampo(this,'dfNomPer')" value="<?php echo $Nom_Per; ?>" /></td>
					</tr>
					<tr>
						<td><b>Nombre de Fantas&iacute;a</b></td>
						<td align="left"><input name="NonFanFct" id="NonFanFct" type="text" class="dato" size="55" style="text-transform:uppercase" onblur="blurCampo(this,'dfNomFan')" value="<?php echo $Nom_Fan; ?>" /></td>
					</tr>
					<tr>
						<td><b>Direcci&oacute;n Casa Matriz</b></td>
						<td align="left"><input name="DirFctFct" id="DirFctFct" type="text" class="dato" size="55" style="text-transform:uppercase" onblur="blurCampo(this,'dfDirFct')" value="<?php echo $Dir_Fct; ?>" /></td>
					</tr>
					<tr>
						<td><b>Comuna</b></td>
						<td align="left">
                                                    <form id="searchCmn" action="">
                                                    <select id="cmn" name="cmn" class="textfieldv2" onChange="filterCmn(this)">
                                                            <option selected value="_NONE">Seleccione una Comuna</option>
                                                            <?php //Seleccionar las ciudades
                                                            $sp = mssql_query("vm_cmn_s",$db);
                                                            while($row = mssql_fetch_array($sp))
                                                            {
                                                                    ?>
                                                                    <option value="<?php echo $row['Cod_Cmn'] ?>"<?php if ($row['Cod_Cmn'] == $CodCmn) echo " selected"; ?>><?php echo utf8_encode($row['Nom_Cmn']) ?></option>
                                                                    <?php
                                                            }
                                                            ?>
                                                    </select>
                                                    </form>
						</td>
					</tr>
					<tr>
						<td><b>Ciudad</b></td>
						<td align="left">
                                                    <form id="searchCdd" name="searchCdd" action="">
                                                    <select id="cdd" name="cdd" class="textfieldv2" onChange="llenarCdd(this)">
                                                            <option selected value="_NONE">Seleccione una Ciudad</option>
                                                            <?php //Seleccionar las ciudades
                                                            $sp = mssql_query("vm_cddcmn_s NULL, $CodCmn",$db);
                                                            while($row = mssql_fetch_array($sp))
                                                            {
                                                                    ?>
                                                                    <option value="<?php echo $row['Cod_Cdd'] ?>"<?php if ($row['Cod_Cdd'] == $CodCdd) echo " selected"; ?>><?php echo utf8_encode($row['Nom_Cdd']) ?></option>
                                                                    <?php
                                                            }
                                                            ?>
                                                    </select>
                                                    </form>
						</td>
					</tr>
					<tr>
						<td><b>Tel&eacute;fono</b></td>
						<td align="left"><input name="FonFctFct" id="FonFctFct" type="text" class="dato" size="15" onblur="blurCampo(this,'dfTelFct')" value="<?php echo $Tel_Fct; ?>" /></td>
					</tr>
					<tr>
						<td><b>FAX</b></td>
						<td align="left"><input name="FaxFctFct" id="FaxFctFct" type="text" class="dato" size="15" onblur="blurCampo(this,'dfFaxFct')" value="<?php echo $Fax_Fct; ?>" /></td>
					</tr>
					<tr>
						<td><b>P&aacute;gina Web</b></td>
						<td align="left"><input name="WebFctFct" id="WebFctFct" type="text" class="dato" size="35" onblur="blurCampo(this,'dfWebFct')" value="<?php echo $Web_Fct; ?>" /></td>
					</tr>
					<tr><td colspan="2" align="right" style="padding-top: 20px">
					<form ID="F2" method="post" name="F2" action="registrar_cltfct.php?cot=<?php echo $Cod_Cot ?>&acc=<?php echo $lbl_btn[$Tip_Opc] ?>" onsubmit="return ValidaDataFct(this)">
						<table border="0" cellpadding="0" cellspacing="0" width="100%" align="right">
						<tr>
							<td width="60%">&nbsp;</td>
							<td class="datoc" width="20%"><input type="submit" name="Enviar" id="Enviar" value="<?php echo $lbl_btn[$Tip_Opc] ?>" class="btn" /></td>
							<td class="datoc" width="20%"><input type="BUTTON" name="Cerrar" value="Cerrar" class="btn"  onClick="javascript:window.close()" /></td>
						</tr>
					    </table>
						<input type="hidden" name="dfCodClt" value="<?php echo $Cod_Clt ?>" />
						<input type="hidden" name="dfRutPer" value="<?php if ($Cod_Per > 0) echo $Rut_Per ?>" />
						<input type="hidden" name="dfNomPer" value="<?php if ($Cod_Per > 0) echo $Nom_Per ?>" />
						<input type="hidden" name="dfNomFan" value="<?php if ($Cod_Per > 0) echo $Nom_Fan ?>" />
						<input type="hidden" name="dfDirFct" value="<?php if ($Cod_Per > 0) echo $Dir_Fct ?>" />
						<input type="hidden" name="codcmn" value="<?php if ($Cod_Per > 0) echo $CodCmn ?>" />
						<input type="hidden" name="codcdd" value="<?php if ($Cod_Per > 0) echo $CodCdd ?>" />
						<input type="hidden" name="dfTelFct" value="<?php if ($Cod_Per > 0) echo $Tel_Fct; ?>" />
						<input type="hidden" name="dfFaxFct" value="<?php if ($Cod_Per > 0) echo $Fax_Fct; ?>" />
						<input type="hidden" name="dfWebFct" value="<?php if ($Cod_Per > 0) echo $Web_Fct; ?>" />
						<input type="hidden" name="dfTipPer" value="<?php if ($Cod_Per > 0) echo $Cod_TipPer; ?>" />
					</form>
					</td></tr>
				</table>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
	var f1;	
	var f2;
	
	f1 = document.F1;	
	f2 = document.F2;
<?php
	if ($accion == "Agregar" or $accion == "Modificar" or $accion == "Eliminar") {
		//echo "	parent.opener.document.F2.action=\"pagar.php?cot=$Cod_Cot&per=$Cod_Per\";\n";
		//echo "	parent.opener.document.F2.submit();\n";
                echo "  ActualizaPadre();\n";
		echo "	window.close();\n";
	}
?>
</script>
</body>
</html>