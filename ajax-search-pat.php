<?php
//Encabezados
include("config.php");
header("Content-type: text/xml");
header("Cache-Control: no-cache");

$cod_sty=ok($_POST['param_sty']);
//Generar el XML
echo "<?xml version=\"1.0\"?>\n";
echo "<response>\n";
switch($_POST['search_type'])
{
    case "pat":
	case "mca":
        //Seleccionar los colores
		if ($_POST['search_type'] == "pat") {
			$cod_mca = "";
			$sp = mssql_query("vm_dsgsty_s '$cod_sty'",$db);
			while ($row = mssql_fetch_array($sp))
			{
				if ($cod_mca == "") $cod_mca = $row['Cod_Mca'];
				spitXml("detalle",$row['Cod_Dsg'],$row['Cod_Mca']);
			}
		}
		else $cod_mca = ok($_POST['param_mca']);
		
        $sp = mssql_query("vm_keypat_dsg '$cod_sty', '$cod_mca'",$db);
        while($row = mssql_fetch_array($sp))
        {
            spitXml("filter",$row['Cod_Pat'],$row['Key_Pat']);
        }
        break;

    case "sze":
        //Seleccionar los tamaños
		$cod_pat=ok($_POST['param_pat']);
        $sp = mssql_query("vm_valsze_dsg '$cod_sty','$cod_pat'",$db);
        while($row = mssql_fetch_array($sp))
        {
            spitXml("filter",$row['Cod_Sze'],$row['Val_Sze']);
        }
        break;

    case "prd":
        //Seleccionar las lineas
		$cod_pat=ok($_POST['param_pat']);
		$cod_sze=ok($_POST['param_sze']);
        $sp = mssql_query("vm_valsze_dsg '$cod_sty','$cod_pat'",$db);
        while($row = mssql_fetch_array($sp))
        {
            spitXml("filter",$row['Cod_Prd'],$row['Val_Sze']);
        }
        break;
}
echo "</response>";

function spitXml($tag, $code,$value)
{
    echo "\t<".$tag.">\n";
    echo "\t\t<code>".$code."</code>\n";
    echo "\t\t<value>".$value."</value>\n";
    echo "\t</".$tag.">\n";
}
/*echo "<?xml version=\"1.0\"?>\n<response><filter><code>1</code><value>VALOR 1</value></filter><filter><code>2</code><value>VALOR 2</value></filter></response>";*/
?>