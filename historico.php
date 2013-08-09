<?php
//Obtengo los datos de conexion de la base de datos
ini_set('display_errors', '0');
session_start();
include("config.php");

$Cod_Per = 0;
if (isset($_SESSION['CodPer'])) $Cod_Per = intval($_SESSION['CodPer']);
if ($Cod_Per > 0) {
	$result = mssql_query ("vm_per_s ".$Cod_Per, $db)
							or die ("No se pudo leer datos del usuario");
	if ($row = mssql_fetch_array($result)) {
		$tip_doc = $row["Cod_TipDoc"];
		$num_doc = $row["Num_Doc"];
	}
	mssql_free_result($result); 
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title></title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
    <link href="css/tallas.css" type="text/css" rel="stylesheet" />
	<LINK href="Include/estilos.css" type="text/css" rel="stylesheet" />
</head>

<body bgcolor="#c1f4e5" style="margin-top:5px;">
<form ID="F1" method="post" name="F1" AUTOCOMPLETE="on">
<TABLE WIDTH="100%" BORDER="0" CELLSPACING="0" CELLPADDING="1">
<TR>
	<TD class="titulo_tabla" align="middle">Nota</TD>
	<TD class="titulo_tabla" align="middle">Fecha</TD>
	<TD class="titulo_tabla" align="middle">Style</TD>
	<TD class="titulo_tabla" colspan="2" align="middle">Patr&oacute;n</TD>
	<TD class="titulo_tabla" align="middle">Talla</TD>
</TR>
<?php
    $j = 0;
	$iTotPrd = 0;
	$tip_doc = 1;
	
    $result = mssql_query("vm_hisusr $tip_doc, '$num_doc'", $db);
    while ($row = mssql_fetch_array($result)) {
		echo "<TR>\n";
		if ($j == 0) {
		    $clase1 = "label_left_right";
			$clase2 = "dato3";
		}
		else {
			$clase1 = "label333";
			$clase2 = "dato33";
		}
        echo "   <TD class=\"".$clase1."\" style=\"TEXT-ALIGN: center \">".$row['Cod_Nvt']."</TD>\n";
        echo "   <TD class=\"".$clase2."\" style=\"TEXT-ALIGN: center\">".$row['Fec_Nvt']."</TD>\n";
        echo "   <TD class=\"".$clase2."\" style=\"TEXT-ALIGN: left\">".$row['Cod_Sty']."-".$row['Nom_Dsg']."</TD>\n";
        echo "   <TD class=\"".$clase2."\" style=\"TEXT-ALIGN: left; BORDER-RIGHT: none\"><img src=\"".printimg_addr("img_pattern",$row["Cod_Pat"])."\" height=\"25px\" width=\"25px\"></TD>\n";
        echo "   <TD class=\"".$clase2."\" style=\"TEXT-ALIGN: left\">".$row['Key_Pat']." ".$row['Des_Pat']."</TD>\n";
        echo "   <TD class=\"".$clase2."\" style=\"TEXT-ALIGN: left\">".$row['Val_Sze']."</TD>\n";
		echo "</TR>\n";
        $j = 1 - $j;
		$iTotPrd++;
	}
    mssql_free_result($result);
?>
<TR>
    <TD colspan="6" style="PADDING-TOP: 10px; TEXT-ALIGN: right" class="label_top"></TD>
</TR>
</TABLE>
</FORM>
<script language="javascript">
	var f1;
	
	f1 = document.F1;
</script>
</body>
</html>
