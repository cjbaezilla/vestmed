<?php
//Encabezados
include("config.php");
header("Content-type: text/xml");
header("Cache-Control: no-cache");

$param=ok($_POST['param_filter']);
//Generar el XML
echo "<?xml version=\"1.0\"?>\n";
echo "<response>\n";
switch($_POST['search_type'])
{
    case "cdd":
        //Seleccionar las lineas
        $sp = mssql_query("vm_cddcmn_s NULL, ".($param=="_ALL"?'':$param),$db);
        while($row = mssql_fetch_array($sp))
        {
            spitXml($row['Cod_Cdd'],utf8_encode($row['Nom_Cdd']));
        }
        break;
}
echo "</response>";

function spitXml($code,$value)
{
    echo "\t<filter>\n";
    echo "\t\t<code>".$code."</code>\n";
    echo "\t\t<value>".$value."</value>\n";
    echo "\t</filter>\n";
}
/*echo "<?xml version=\"1.0\"?>\n<response><filter><code>1</code><value>VALOR 1</value></filter><filter><code>2</code><value>VALOR 2</value></filter></response>";*/
?>