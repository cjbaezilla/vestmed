<?php
//Obtengo los datos de conexion de la base de datos
ini_set('display_errors', '0');
session_start();
include("global_cot.php");
$contexto = ok($_GET['contexto']);
$pat_per = isset($_POST['pat_per'])  ? ok($_POST['pat_per']) : "";
$mat_per = isset($_POST['mat_per'])  ? ok($_POST['mat_per']) : "";
$nom_per = isset($_POST['nom_per'])  ? ok($_POST['nom_per']) : "";
$nom_clt = isset($_POST['nom_clt'])  ? ok($_POST['nom_clt']) : "";
$codpro  = isset($_POST['codpro'])   ? ok($_POST['codpro']) : 0;
$codesp  = isset($_POST['codesp'])   ? ok($_POST['codesp']) : 0;
$num_fct = isset($_POST['dfNumFct']) ? ok($_POST['dfNumFct']) : 0;

$query = "";
if ($pat_per != "" || $mat_per != "" || $nom_per != "") 
	$query = "vm_selper_s 2, '$pat_per', '$mat_per', '$nom_per'";
else if ($nom_clt != "") 
	$query = "vm_selper_s 1, NULL, NULL, NULL, NULL, NULL, '$nom_clt'";
else if ($codpro != 0) 
	$query = "vm_selper_s 3, NULL, NULL, NULL, $codpro, $codesp";
else if ($num_fct != 0) 
	$query = "vm_selper_s 1, NULL, NULL, NULL, NULL, NULL, NULL, $num_fct";
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
<script type="text/javascript" src="../js/jquery-1.3.2.min.js"></script>
<script type="text/javascript">
	new UvumiDropdown('dropdown-scliente');
	
	var $j = jQuery.noConflict();
    $j(document).ready
	(
		//$j(":input:first").focus();
		
		function()
		{
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
	
    function llenarEsp(obj)
    {
		f2.codesp.value = obj.value;
    }
	
	function blurPN(obj,caso) {
		if (caso == 1) f2.pat_per.value = obj.value;
		if (caso == 2) f2.mat_per.value = obj.value;
		if (caso == 3) f2.nom_per.value = obj.value;
	}

	function blurPJ(obj) {
		f2.nom_clt.value = obj.value;
	}

    function filterPro(obj)
    {
		f2.codpro.value = obj.value;
        $j("form#searchPro").submit();
    }
	
	function volver(numdoc) {
	<?php if ($contexto == "mnu" ) { ?>
		parent.opener.document.F2.action="escritorio_edtclt.php?clt="+numdoc;
	<?php } else { ?>
		parent.opener.document.F2.action="nueva_cot.php?rut="+numdoc;
	<?php } ?>
		parent.opener.document.F2.submit();
		window.close();
	}
</script>
</head>

<body>
<div id="body">
	<div id="header"></div>
    <div id="work">
<?php formar_topbox ("100%%","center"); ?>
<P align="center">
<TABLE WIDTH="100%" BORDER="0" CELLSPACING="0" CELLPADDING="1" ALIGN="center">
<tr>
<td  valign="top" STYLE="PADDING-LEFT:20px; PADDING-RIGHT:20px; TEXT-ALIGN:left">
	<TABLE WIDTH="100%" BORDER="0" CELLSPACING="0" CELLPADDING="1" ALIGN="right">
	<tr>
		<td STYLE="TEXT-ALIGN: center">
			<fieldset class="label_left_right_top_bottom">
			<legend>Criterios de Busqueda</legend>
				<TABLE WIDTH="95%" BORDER="0" CELLSPACING="5" CELLPADDING="1" ALIGN="center">
				<tr>
					<td width="100" class="dato">&nbsp;</td>
					<td colspan="2" align="left">
					<table border="0" width="100%"><tr>
					<td width="198px"><b>Apellido Paterno</b></td>
					<td width="198px"><b>Apellido Materno</b></td>
					<td><b>Nombres</b></td></tr></table>
					</td>
				</tr>
				<tr>
					<td width="180" class="dato">Persona Natural</td>
					<td colspan="2" align="left">
					<INPUT name="dfPatPer" id="dfPatPer" size="80" maxLength="80" class="textfieldv2" value="<?php echo $pat_per; ?>" style="TEXT-TRANSFORM: uppercase" onblur="blurPN(this,1)" />
					<INPUT name="dfManPer" id="dfManPer" size="80" maxLength="80" class="textfieldv2" value="<?php echo $mat_per; ?>" style="TEXT-TRANSFORM: uppercase" onblur="blurPN(this,2)" />
					<INPUT name="dfNomPer" id="dfNomPer" size="80" maxLength="80" class="textfieldv2" value="<?php echo $nom_per; ?>" style="TEXT-TRANSFORM: uppercase" onblur="blurPN(this,3)" />
					</td></tr>
				</tr>
				<tr>
					<td class="dato">Persona Juridica</td>
					<td colspan="2" align="left"><INPUT name="dfNomClt" id="dfNomClt" size="80" maxLength="120" class="textfieldv2" value="<?php echo $nom_clt; ?>" style="TEXT-TRANSFORM: uppercase"  onblur="blurPJ(this)" /></td>
				</tr>
				<tr>
					<td class="dato">Profesi&oacute;n y/o Especialidad</td>
					<form id="searchPro" name="searchPro">
					<TD align="left" width="30%">
					<select id="pro" name="pro" class="textfieldv2" onChange="filterPro(this)">
						<option selected value="_NONE">Seleccione una Profesi&oacute;n</option>
						<?php //Seleccionar Profesion
						$sp = mssql_query("vm_pro_s", $db);
						while($row = mssql_fetch_array($sp))
						{
						?>
							<option value="<?php echo $row['Cod_Pro'] ?>"><?php echo $row['Nom_Pro'] ?></option>
						<?php
						}
						?>
					</select>
					</TD>
					</form>
					<form id="searchEsp" name="searchEsp">
					<TD align="left">
					<select id="esp" name="esp" class="textfieldv2" onChange="llenarEsp(this)">
						<option selected value="_NONE">Seleccione una Especialidad</option>
						<?php //Seleccionar las ciudades
						$sp = mssql_query("vm_esppro_s 0",$db);
						while($row = mssql_fetch_array($sp))
						{
							?>
							<option value="<?php echo $row['Cod_Esp'] ?>"><?php echo $row['Nom_Esp'] ?></option>
							<?php
						}
						?>
					</select>
					</TD>
					</form>
				</tr>
				<form ID="F2" AUTOCOMPLETE="off" method="POST" name="F2" ACTION="busqueda.php?contexto=<?php echo $contexto; ?>" onsubmit="return CheckBusqueda(this)">
				<tr>
					<td class="dato">Factura</td>
					<td align="left">
						<INPUT type="hidden" id="pat_per" name="pat_per" value="<?php echo $pat_per; ?>">
						<INPUT type="hidden" id="mat_per" name="mat_per" value="<?php echo $mat_per; ?>">
						<INPUT type="hidden" id="nom_per" name="nom_per" value="<?php echo $nom_per; ?>">
						<INPUT type="hidden" id="nom_clt" name="nom_clt" value="<?php echo $nom_clt; ?>">
						<INPUT type="hidden" id="codpro" name="codpro" value="<?php echo $cod_pro; ?>">
						<INPUT type="hidden" id="codesp" name="codesp" value="<?php echo $cod_esp; ?>">
						<INPUT name="dfNunFct" id="dfNumFct" size="10" maxLength="10" class="textfieldv2" value="<?php echo $num_fct ?>" onKeyPress="javascript:return soloNUMERO(event)" /></td>
					<td align="right">
						<input type="button" name="Cerrar" value="Cerrar" class="btn" onclick="javascript:window.close()">&nbsp;
						<input type="submit" name="Buscar"  value="Buscar" class="btn">
					</td>
				</tr>
				</TABLE>
				</form>
			</fieldset>
		</td>
	</tr>
<?php 
	if ($query != "") { 
?>
	<tr>
		<td STYLE="TEXT-ALIGN: center">
			<fieldset class="label_left_right_top_bottom">
			<legend>Resultado Busqueda</legend>
				<TABLE WIDTH="95%" BORDER="0" CELLSPACING="1" CELLPADDING="1" ALIGN="center">
<?php 
		$iTotPrd = 0;
		$result = mssql_query ($query, $db)	or die ("No se pudo leer datos del Cliente");
		while ($row = mssql_fetch_array($result)) {
			if ($row['Cod_TipPer'] == 1) {
				if ($iTotPrd == 0) {
?>
				<TR>
					<TD class="titulo_tabla" width="12%" align="middle">Rut</TD>
					<TD class="titulo_tabla" width="38%" align="middle">Nombre</TD>
					<TD class="titulo_tabla" width="25%" align="middle">Profesion</TD>
					<TD class="titulo_tabla" width="25%" align="middle">Especialidad</TD>
				</TR>
<?php
				}
?>
		<Tr>
		<td align="right" style="PADDING-RIGHT: 5px" ><a href="javascript:volver('<?php echo $row['Num_Doc'] ?>')"><?php  echo formatearRut($row['Num_Doc']); ?></a></td>
		<td align="left"><?php echo trim($row['Pat_Per']." ".$row['Mat_Per']).", ".$row['Nom_Per']; ?></td>
		<td align="left"><?php echo $row['Nom_Pro'] ?></td>
		<td align="left"><?php echo $row['Nom_Esp'] ?></td>
		</Tr>
<?php 
			} else {
				if ($iTotPrd == 0) {
?>
				<TR>
					<TD class="titulo_tabla" width="12%" align="middle">Rut</TD>
					<TD class="titulo_tabla" width="68%" align="middle">Raz&oacute;n Social</TD>
					<TD class="titulo_tabla" width="20%" align="middle">Giro</TD>
				</TR>
<?php
				}
?>
		<Tr>
		<td align="right" style="PADDING-RIGHT: 5px" ><a href="javascript:volver('<?php echo $row['Num_Doc'] ?>')"><?php  echo formatearRut($row['Num_Doc']); ?></a></td>
		<td align="left"><?php echo trim($row['RznSoc_Per']); ?></td>
		<td align="left"><?php echo trim($row['Gro_Per']); ?></td>
		</Tr>
<?php 
			}
			$iTotPrd++;
		}
?>
				</TABLE>
			</fieldset>
		</td>
	</tr>
<?php 
	}
?>
	</TABLE>
</td>
</tr>
</table>
</p>
<?php formar_bottombox (); ?>
    </div>
    <div id="footer"></div>
</div>
<script language="javascript">
	var f2;
	f2 = document.F2;
</script>
<!-- script que define y configura el calendario-->
</body>
</html>
