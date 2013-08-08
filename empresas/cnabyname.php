<?php
ini_set('display_errors', '0');
session_start();
include("../config.php");

if (!isset($_SESSION['usuario'])) header("Location: index.php");

$nom_clt = isset($_POST['nom_clt'])  ? ok($_POST['nom_clt']) : "";

$query = "";
if ($nom_clt != "") 
    $query = "vm_selper_s 1, NULL, NULL, NULL, NULL, NULL, '$nom_clt'";

//echo $query;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
        <link href="../css/layout.css" type="text/css" rel="stylesheet" />
        <link href="css/itunes.css" type="text/css" rel="stylesheet" />
        <title>Consulta Histórico Compras</title>
        <script language="JavaScript" src="../Include/ValidarDataInput.js" type="text/javascript" ></script>
        <script language="JavaScript" src="../Include/SoloNumeros.js" type="text/javascript" ></script>
        <script language="JavaScript" src="../Include/validarRut.js" type="text/javascript" ></script>
        <!--script type="text/javascript" src="../js/jquery-1.3.2.min.js"></script-->
	<!--link href="http://code.jquery.com/ui/1.9.0/themes/ui-darkness/jquery-ui.css" rel="stylesheet"-->
	<link href="http://code.jquery.com/ui/1.9.0/themes/redmond/jquery-ui.css" rel="stylesheet" />
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.8/jquery.js"></script>
	<script src="http://code.jquery.com/ui/1.9.0/jquery-ui.min.js"></script>
        
	<!-- keyboard widget css & script (required) -->
	<link href="css/keyboard.css" rel="stylesheet" />
	<script src="js/jquery.keyboard.js"></script>

	<!-- keyboard extensions (optional) -->
	<script src="js/jquery.mousewheel.js"></script>
        
        <script type="text/javascript">
            var $j = jQuery.noConflict();
            
            $j(function(){
                    $j('#dfPatPer').keyboard();
                    $j('#dfManPer').keyboard();
                    $j('#dfNomPer').keyboard();
                    $j('#dfNomClt').keyboard();
            });
                        
            $j(document).ready
            (
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
            
            function filterPro(obj)
            {
                var form = document.getElementById('F2');
                form.codpro.value = obj.value;
                $j("form#searchPro").submit();
            }
            
            function llenarEsp(obj)
            {
                var form = document.getElementById('F2');
                form.codesp.value = obj.value;
            }
            
            function Home() {
                var form = document.getElementById('F2');
                form.action = "principal.php";
                form.submit();
            }
            
            function blurPN(obj,caso) {
                var form = document.getElementById('F2');
                if (caso == 1) form.pat_per.value = obj.value;
                if (caso == 2) form.mat_per.value = obj.value;
                if (caso == 3) form.nom_per.value = obj.value;
            }

            function blurPJ(obj) {
                var form = document.getElementById('F2');
                form.nom_clt.value = obj.value;
            }
            
            function CheckBusqueda(form) {
                var form = document.getElementById('F2');
                var ok = false;
                
                if (form.pat_per.value != "") ok = true;
                if (form.mat_per.value != "") ok = true;
                if (form.nom_per.value != "") ok = true;
                if (form.nom_clt.value != "") ok = true;
                if (form.codpro.value != "") ok = true;
                if (form.codesp.value != "") ok = true;
                
                if (!ok) {
                    alert ('Debe ingresar un valor en la busqueda');
                    return false;
                }
                
                return true;
            }
            
            function Send(rut) {
                var form = document.getElementById('F2');
                form.dfrutclt.value = rut;
                form.action = "nventas.php";
                form.submit();
            }
        </script>
    </head>
    <body style="margin-left: 200px; margin-right: 200px">
        <h2>Busqueda</h2>
        <table WIDTH="100%" BORDER="0" CELLSPACING="5" CELLPADDING="1" ALIGN="center">
        <tr>
                <td class="dato">Raz&oacute;n Social</td>
                <td align="left"><input name="dfNomClt" id="dfNomClt" size="80" maxLength="120" class="textfieldv2" value="" style="TEXT-trANSFORM: uppercase"  onblur="blurPJ(this)" /></td>
        </tr>
        <tr>
            <td align="left" colspan="2">
            </td>
        </tr>
        <tr>
            <td colspan="2" align="right">
                <form ID="F2" method="POST" name="F2" ACTION="<?php echo $_SERVER['PHP_SELF']; ?>" onsubmit="return CheckBusqueda(this)">
                <input type="hidden" id="nom_clt" name="nom_clt" value="" />
                <input type="hidden" id="dfrutclt" name="dfrutclt" value="" />
                <input type="button" name="Volver" value="Volver" class="btn" onclick="javascript:Home()" />
                <input type="submit" name="Buscar"  value="Buscar" class="btn" />
                </form>
            </td>
        </tr>
        </table>
        <div id="resultado" style="width: 100%">
<?php 
	if ($query != "") { 
?>
        <table WIDTH="100%" BORDER="0" CELLSPACING="5" CELLPADDING="1" ALIGN="center">
	<tr>
		<td STYLE="TEXT-ALIGN: center">
			<fieldset class="label_left_right_top_bottom">
			<legend>Resultado Busqueda</legend>
				<table WIDTH="95%" BORDER="0" CELLSPACING="1" CELLPADDING="1" ALIGN="center" class="tabular">
<?php 
		$iTotPrd = 0;
		$result = mssql_query ($query, $db)	or die ("No se pudo leer datos del Cliente"."<BR>".$query);
		while ($row = mssql_fetch_array($result)) {
			if ($row['Cod_TipPer'] == 1) {
				if ($iTotPrd == 0) {
?>
                                <thead>
				<tr class="tabular">
					<th class="tabular" width="10%" align="middle">Rut</th>
					<th class="tabular" width="40%" align="middle">Nombre</th>
					<th class="tabular" width="15%" align="middle">Profesion</th>
					<th class="tabular" width="15%" align="middle">Especialidad</th>
					<th class="tabular" width="20%" align="middle">mail</th>
				</tr>
                                </thead>
                                <tbody>
<?php
				}
?>
		<tr class="tabular">
		<td class="tabular" align="right" style="PADDING-RIGHT: 5px" ><a href="javascript:Send('<?php echo $row['Num_Doc'] ?>')"><?php  echo formatearRut($row['Num_Doc']); ?></a></td>
		<td class="tabular" align="left" style="padding-left:3px"><?php echo utf8_encode(trim($row['Pat_Per']." ".$row['Mat_Per']).", ".$row['Nom_Per']); ?></td>
		<td class="tabular" align="left" style="padding-left:3px"><?php echo $row['Nom_Pro'] ?></td>
		<td class="tabular" align="left" style="padding-left:3px"><?php echo $row['Nom_Esp'] ?></td>
		<td class="tabular" align="left" style="padding-left:3px"><?php echo $row['Mail_Ctt'] ?></td>
		</tr>
<?php 
			} else {
				if ($iTotPrd == 0) {
?>
                                <thead>
				<tr class="tabular">
					<th class="tabular" width="15%" align="middle">Rut</th>
					<th class="tabular" width="65%" align="middle">Raz&oacute;n Social</th>
					<th class="tabular" width="20%" align="middle">Giro</th>
				</tr>
                                </thead>
                                    <tbody>
<?php
				}
?>
		<tr class="tabular">
		<td class="tabular" align="right" style="PADDING-RIGHT: 5px" ><a href="javascript:Send('<?php echo $row['Num_Doc'] ?>')"><?php  echo formatearRut($row['Num_Doc']); ?></a></td>
		<td class="tabular" align="left" style="padding-left:3px"><?php echo utf8_encode(trim($row['RznSoc_Per'])); ?></td>
		<td class="tabular" align="left" style="padding-left:3px"><?php echo trim($row['Gro_Per']); ?></td>
		</tr>
<?php 
			}
			$iTotPrd++;
		}
?>
                                    </tbody>
				</table>
			</fieldset>
		</td>
	</tr>
        </table>
<?php 
	}
?>
            
        </div>
    </body>
</html>
