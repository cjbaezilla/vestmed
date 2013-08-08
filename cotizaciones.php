<?php
//Obtengo los datos de conexion de la base de datos
ini_set('display_errors', '1');
session_start();
include("config.php");

$cod_cot = 0;
if (isset($_SESSION['CodCot'])) $cod_cot = intval($_SESSION['CodCot']);
//$p_grpprd = ok($_GET['producto']);
//$p_title = ok($_GET['title']);

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title></title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
    <link href="css/tallas.css" type="text/css" rel="stylesheet" />
    <LINK href="Include/estilos.css" type="text/css" rel="stylesheet" />
    <LINK href="css/layout.css" type="text/css" rel="stylesheet" />
    <script type="text/javascript" src="js/mootools-1.2.1-core-yc.js"></script>
    <script type="text/javascript" src="Include/SoloNumeros.js"></script>
    <script type="application/javascript">
   // window.addEvent('domready', function(){
		
	//})
    </script>
	<script type="text/javascript">
	function CheckOut() {
		alert("CheckOut");
	}

	function UpdateCantidad(obj,sec) {
		f1.action = "updcotizaciones.php?accion=update&sec="+sec;
		f1.submit();
	}
	
	function Eliminar() {
		var ok = 0;
		
		for (i=0; i<f1.elements.length; i++) {
		   if (f1.elements[i].name == "seleccionadof[]")
			  if (f1.elements[i].checked)
				 ok++;
		}

		if (ok==0) 
			alert ("Debe seleccionar un producto a eliminar ...");
			
		else {
			f1.action = "updcotizaciones.php?accion=delete";
			f1.submit();
		}
	}

	function actualizarCantidad(){
		if(parent.$('cant_prods')){
			var total = 0;
			$$('.textfield_m').each(function(x){
				total += x.get('value').toInt();
			})
			parent.$('cant_prods').set('html', total.toInt());
			if(total.toInt() <= 0){
				parent.$('carro_productos').setStyle('display', 'none');
				$('btn_check').setStyle('display', 'none');
			}else{
				parent.$('carro_productos').setStyle('display', 'inline');
				$('btn_check').setStyle('display', 'block');
			}
		}
	}
	</script>
</head>

<body bgcolor="#c1f4e5" style="margin-top:5px;">
<div style="height:270px; overflow:auto;">
<form ID="F1" method="post" name="F1" AUTOCOMPLETE="on">
<TABLE WIDTH="100%" BORDER="0" CELLSPACING="0" CELLPADDING="1">
<TR>
	<TD class="titulo_tabla" align="middle">Producto</TD>
	<TD class="titulo_tabla" align="middle">Marca</TD>
	<TD class="titulo_tabla" align="middle">Linea</TD>
	<TD class="titulo_tabla" align="middle">Talla</TD>
	<TD class="titulo_tabla" align="middle">Patr&oacute;n</TD>
	<TD class="titulo_tabla" align="middle">Cantidad</TD>
	<TD class="titulo_tabla" align="middle">&nbsp;</TD>
</TR>
<?php
    $j = 0;
    $iTotPrd = 0;
    $result = mssql_query("vm_s_cotweb $cod_cot", $db);
    while ($row = mssql_fetch_array($result)) {
        echo "<TR>\n";
        if ($j == 0) {
            $clase1 = "";
                $clase2 = "";
        }
        else {
                $clase1 = "";
                $clase2 = "";
        }
        echo "   <TD class=\"".$clase1."\" style=\"TEXT-ALIGN: center \">".$row['cod_sty']."-".$row['Nom_Dsg']."</TD>\n";
        echo "   <TD class=\"".$clase2."\" style=\"TEXT-ALIGN: center\">".$row['cod_mca']."</TD>\n";
        echo "   <TD class=\"".$clase2."\" style=\"TEXT-ALIGN: center\">".$row['des_linmca']."</TD>\n";
        echo "   <TD class=\"".$clase2."\" style=\"TEXT-ALIGN: center\">".$row['val_sze']."</TD>\n";
        echo "   <TD class=\"".$clase2."\" style=\"TEXT-ALIGN: center\">".$row['key_pat']." ".$row['des_pat']."</TD>\n";
        echo "   <TD class=\"".$clase2."\" style=\"TEXT-ALIGN: center\">";
        echo "<INPUT name=\"dfCtd".$row["cod_sec"]."\" size=\"3\" maxLength=\"3\" class=\"textfield_m\" onKeyPress=\"SoloNumeros(this)\" onchange=\"UpdateCantidad(this,".$row["cod_sec"].")\" value=\"".$row['cot_ctd']."\"></TD>\n";
        echo "   <TD class=\"".$clase2."\" style=\"TEXT-ALIGN: center\">";
        echo "   <INPUT type=\"checkbox\" class=\"dato\" style=\"height: 14px\" name=\"seleccionadof[]\" value=".$row["cod_sec"]."></TD>\n";
        echo "</TR>\n";
        $j = 1 - $j;
        $iTotPrd++;
    }
    mssql_free_result($result);
?>
<TR>
    <TD colspan="7" style="PADDING-TOP: 10px; TEXT-ALIGN: right" class="label_top"></TD>
</TR>
<TR>
    <TD colspan="7" class="normal" style="TEXT-ALIGN: right">
        <input type="button" value="Eliminar Seleccionados" onclick="Eliminar();" class="btn2" />
    </TD>
</TR>
</TABLE>
</FORM>
</div>
<div>
<input type="button" style="float:left;" class="btn2" value="Continuar Cotizando" onclick="parent.location.href='catalogo.php'" />
<input type="button" style="float:left;" class="btn2" id="btn_check" value="Cerrar Cotizaci&oacute;n" onclick="parent.location.href='detalle-cotizacion.php'" />
<!---<input type="button" style="float:right;" class="btn" value="Volver" onclick="location.href=''" /-->
</div>
<script language="javascript">
    var f1;

    f1 = document.F1;
	
    actualizarCantidad();
</script>
</body>
</html>
