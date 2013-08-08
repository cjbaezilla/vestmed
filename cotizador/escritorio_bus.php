<?php
//Obtengo los datos de conexion de la base de datos
ini_set('display_errors', '0');
session_start();

if (!isset($_SESSION['usuario'])) {
    if (!isset($_POST["usuario"])) header("Location: ../index.php");
    $_SESSION['usuario'] = $_POST["usuario"];     
}
$UsrId = (isset($_SESSION['usuario'])) ? $_SESSION['usuario'] : "";

include("global_cot.php");

$OkOpc = false;
$sp = mssql_query("vm_seg_usr_opcmodweb '$UsrId'",$db);
while (($row = mssql_fetch_array($sp))) 
    if ($row["Id_Mod"] == 1 && $row["ID_Opc"] == 3 && $row['CodUsr'] == $UsrId) {
        $OkOpc = true;
        break;
    }
mssql_free_result($sp);

if (!$OkOpc) {
    header ("Location:../index.php");
    exit(0);
}

if (isset($_GET['opc'])) $_SESSION['opcion'] = $_GET['opc'];

$pat_per = isset($_POST['pat_per'])  ? ok($_POST['pat_per']) : "";
$mat_per = isset($_POST['mat_per'])  ? ok($_POST['mat_per']) : "";
$nom_per = isset($_POST['nom_per'])  ? ok($_POST['nom_per']) : "";
$nom_clt = isset($_POST['nom_clt'])  ? ok($_POST['nom_clt']) : "";
$codpro  = isset($_POST['codpro'])   ? ok($_POST['codpro']) : 0;
$codesp  = isset($_POST['codesp'])   ? ok($_POST['codesp']) : 0;
$num_fct = isset($_POST['dfNumFct']) ? ok($_POST['dfNumFct']) : 0;
$num_doc = isset($_POST['dfNumDoc']) ? ok($_POST['dfNumDoc']) : "";

$query = "";
if ($pat_per != "" || $mat_per != "" || $nom_per != "") 
	$query = "vm_selper_s 2, '$pat_per', '$mat_per', '$nom_per'";
else if ($nom_clt != "") 
	$query = "vm_selper_s 1, NULL, NULL, NULL, NULL, NULL, '$nom_clt'";
else if ($codpro != 0) {
	$codesp = ($codesp == "" ? "NULL" : $codesp);
	$query = "vm_selper_s 3, NULL, NULL, NULL, $codpro, $codesp";
}
else if ($num_fct != 0) 
	$query = "vm_selper_s 4, NULL, NULL, NULL, NULL, NULL, NULL, $num_fct";
else if ($num_doc != 0) 
	$query = "vm_selper_s 5, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '$num_doc'";

?>
<!DOCTYPE html PUBLIC "-//W3C//Dtd XHTML 1.0 transitional//EN" "http://www.w3.org/tr/xhtml1/Dtd/xhtml1-transitional.dtd">
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
<script type="text/javascript" src="../Include/ValidarDataInput.js"></script>
<script type="text/javascript" src="../Include/SoloNumeros.js"></script>
<script type="text/javascript" src="../Include/validarRut.js"></script>
<link href="../Include/estilos.css" type="text/css" rel="stylesheet" />
<script type="text/javascript" src="../js/jquery-1.3.2.min.js"></script>
<script type="text/javascript" src="js/AccionesMenu.js"></script>
<script type="text/javascript">
	new UvumiDropdown('dropdown-scliente');

	function MarcarTodos(form,nombrecheckbox) {
	   for (i=0; i<form.elements.length; i++) {
		if (form.elements[i].name == nombrecheckbox)
			form.elements[i].checked = true;
	   }	
	}

	function DesMarcarTodos(form,nombrecheckbox) {
	   for (i=0; i<form.elements.length; i++) {
		if (form.elements[i].name == nombrecheckbox)
			form.elements[i].checked = false;
	   }	
	}
	
	function Editar(contexto) {
		//popwindow("busqueda.php?contexto=mnu",600)
		alert ("Debe seleccionar un cliente ...");
	}
	
</script>
<script type="text/javascript">
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

    function blurFct(obj) {
            f2.num_fct.value = obj.value;
    }

    function blurDoc(obj) {
            for(i=0;i<obj.value.length;i++)
                    if(obj.value.charAt(i)=="-") return true;
        obj.value = obj.value.substr(0,obj.value.length-1) + '-' + obj.value.substr(obj.value.length-1,1);
    }
	
    function filterPro(obj)
    {
	f2.codpro.value = obj.value;
        $j("form#searchPro").submit();
    }
</script>
</head>

<body>
<div id="body">
    <div id="header"></div>
    <ul id="usuario_registro">
        <?php 	echo display_usr($UsrId, $Perfil, "Clientes", $db); ?>
    </ul>
    <div id="work">
<?php formar_topbox ("100%%","center"); ?>
<p align="left"><strong>Escritorio</strong></p>
<p align="center">
<table WIDTH="100%" BORDER="0" CELLSPACING="0" CELLPADDING="1" ALIGN="center">
<tr>
<td width="170px" align="left" valign="top">
	<?php echo display_mnuizq($UsrId, $db); ?>
</td>
<td width="1%">&nbsp;</td>
<td  valign="top" class="label_left_right_top_bottom" STYLE="PADDING-LEFT:10px; PADDING-RIGHT:10px; TEXT-ALIGN:left">
	<table WIDTH="100%" BORDER="0" CELLSPACING="0" CELLPADDING="1" ALIGN="right">
	<tr><td STYLE="TEXT-ALIGN: left">
		<h2>Busqueda</h2>
		<table WIDTH="99%" BORDER="0" CELLSPACING="5" CELLPADDING="1" ALIGN="center">
		<tr>
			<td width="100" class="dato">&nbsp;</td>
			<td colspan="2" align="left">
			<table border="0" width="100%"><tr>
			<td width="170px"><b>Apellido Paterno</b></td>
			<td width="170px"><b>Apellido Materno</b></td>
			<td><b>Nombres</b></td></tr></table>
			</td>
		</tr>
		<tr>
			<td width="100" class="dato">Persona Natural</td>
			<td colspan="2" align="left">
			<input name="dfPatPer" id="dfPatPer" size="30" maxLength="80" class="textfield_m" value="<?php echo $pat_per; ?>" style="TEXT-trANSFORM: uppercase" onblur="blurPN(this,1)" />
			<input name="dfManPer" id="dfManPer" size="30" maxLength="80" class="textfield_m" value="<?php echo $mat_per; ?>" style="TEXT-trANSFORM: uppercase" onblur="blurPN(this,2)" />
			<input name="dfNomPer" id="dfNomPer" size="30" maxLength="80" class="textfield_m" value="<?php echo $nom_per; ?>" style="TEXT-trANSFORM: uppercase" onblur="blurPN(this,3)" />
			</td>
		</tr>
		<tr>
			<td class="dato">Persona Juridica</td>
			<td colspan="2" align="left"><input name="dfNomClt" id="dfNomClt" size="80" maxLength="120" class="textfield_m" value="<?php echo $nom_clt; ?>" style="TEXT-trANSFORM: uppercase"  onblur="blurPJ(this)" /></td>
		</tr>
		<tr>
			<td class="dato">Profesi&oacute;n</td>
			<td align="left" width="30%">
			<form id="searchPro" name="searchPro" action="">
			<select id="pro" name="pro" class="textfield_m" onChange="filterPro(this)">
				<option selected value="_NONE">Seleccione una Profesi&oacute;n</option>
				<?php //Seleccionar Profesion
				$sp = mssql_query("vm_pro_s", $db);
				while($row = mssql_fetch_array($sp))
				{
				?>
					<option value="<?php echo $row['Cod_Pro'] ?>"><?php echo utf8_encode($row['Nom_Pro']) ?></option>
				<?php
				}
				?>
			</select>
			</form>
			</td>
			<td align="left">
			<form id="searchEsp" name="searchEsp" action="">
			<select id="esp" name="esp" class="textfield_m" onChange="llenarEsp(this)">
				<option selected value="_NONE">Seleccione una Especialidad</option>
				<?php //Seleccionar las ciudades
				$sp = mssql_query("vm_esppro_s 0",$db);
				while($row = mssql_fetch_array($sp))
				{
					?>
					<option value="<?php echo $row['Cod_Esp'] ?>"><?php echo utf8_encode($row['Nom_Esp']) ?></option>
					<?php
				}
				?>
			</select>
			</form>
			</td>
		</tr>
		<form ID="F2" method="POST" name="F2" ACTION="escritorio_bus.php" onsubmit="return CheckBusqueda(this)">
		<tr>
			<td class="dato">RUT</td>
			<td align="left" colspan="2">
				<input type="hidden" id="pat_per" name="pat_per" value="<?php echo $pat_per; ?>">
				<input type="hidden" id="mat_per" name="mat_per" value="<?php echo $mat_per; ?>">
				<input type="hidden" id="nom_per" name="nom_per" value="<?php echo $nom_per; ?>">
				<input type="hidden" id="nom_clt" name="nom_clt" value="<?php echo $nom_clt; ?>">
				<input type="hidden" id="codpro" name="codpro" value="<?php echo $cod_pro; ?>">
				<input type="hidden" id="codesp" name="codesp" value="<?php echo $cod_esp; ?>">
				<input type="hidden" id="num_fct" name="num_fct" value="<?php echo $num_fct; ?>">
				<input name="dfNumDoc" id="dfNumDoc" size="15" maxLength="10" class="textfield_m" value="<?php echo $num_doc ?>" onblur="blurDoc(this)" /></td>
		</tr>
		<tr>
			<td colspan="3" align="right">
			<input type="button" name="Nuevo" value="Nuevo" class="btn" onclick="javascript:NuevoCliente('mnu')">
			<input type="submit" name="Buscar"  value="Buscar" class="btn">
			</td>
		</tr>
		</form>
		</table>
	</td></tr>
<?php 
	if ($query != "") { 
?>
	<tr>
		<td STYLE="TEXT-ALIGN: center">
			<fieldset class="label_left_right_top_bottom">
			<legend>Resultado Busqueda</legend>
				<table WIDTH="95%" BORDER="0" CELLSPACING="1" CELLPADDING="1" ALIGN="center">
<?php 
		$iTotPrd = 0;
		$result = mssql_query ($query, $db)	or die ("No se pudo leer datos del Cliente"."<BR>".$query);
		while ($row = mssql_fetch_array($result)) {
			if ($row['Cod_TipPer'] == 1) {
				if ($iTotPrd == 0) {
?>
				<tr>
					<td class="titulo_tabla" width="15%" align="middle">Rut</td>
					<td class="titulo_tabla" width="40%" align="middle">Nombre</td>
					<td class="titulo_tabla" width="15%" align="middle">Profesion</td>
					<td class="titulo_tabla" width="15%" align="middle">Especialidad</td>
					<td class="titulo_tabla" width="15%" align="middle">mail</td>
				</tr>
<?php
				}
?>
		<tr>
		<td align="right" style="PADDING-RIGHT: 5px" ><a href="javascript:volver('<?php echo $row['Num_Doc'] ?>')"><?php  echo formatearRut($row['Num_Doc']); ?></a></td>
		<td align="left" style="padding-left:3px"><?php echo utf8_encode(trim($row['Pat_Per']." ".$row['Mat_Per']).", ".$row['Nom_Per']); ?></td>
		<td align="left" style="padding-left:3px"><?php echo $row['Nom_Pro'] ?></td>
		<td align="left" style="padding-left:3px"><?php echo $row['Nom_Esp'] ?></td>
		<td align="left" style="padding-left:3px"><?php echo $row['Mail_Ctt'] ?></td>
		</tr>
<?php 
			} else {
				if ($iTotPrd == 0) {
?>
				<tr>
					<td class="titulo_tabla" width="15%" align="middle">Rut</td>
					<td class="titulo_tabla" width="65%" align="middle">Raz&oacute;n Social</td>
					<td class="titulo_tabla" width="20%" align="middle">Giro</td>
				</tr>
<?php
				}
?>
		<tr>
		<td align="right" style="PADDING-RIGHT: 5px" ><a href="javascript:volver('<?php echo $row['Num_Doc'] ?>')"><?php  echo formatearRut($row['Num_Doc']); ?></a></td>
		<td align="left" style="padding-left:3px"><?php echo utf8_encode(trim($row['RznSoc_Per'])); ?></td>
		<td align="left" style="padding-left:3px"><?php echo trim($row['Gro_Per']); ?></td>
		</tr>
<?php 
			}
			$iTotPrd++;
		}
?>
				</table>
			</fieldset>
		</td>
	</tr>
<?php 
	}
?>
	</table>
</td>
</tr>
</table>
</p>
<?php formar_bottombox (); ?>
    </div>
    <div id="footer"></div>
</div>
<script type="text/javascript">
	var f1;
	var f2;
	f1 = document.F1;
	f2 = document.F2;
</script>


</body>
</html>
