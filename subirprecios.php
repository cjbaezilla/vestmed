<?php
//Obtengo los datos de conexion de la base de datos
ini_set('display_errors', '0');
include("config.php");

$archivo = $_FILES['documento']['name'];
if ($archivo != "") {
    $fileupload = $pathadjuntos."precios.xls";
    if (!move_uploaded_file($_FILES['documento']['tmp_name'], $fileupload)){
       echo $_FILES['documento']['tmp_name']."<BR>".$fileupload."<BR>";
       echo "Ocurri&oacute; alg&uacute;n error al subir el fichero. No pudo guardarse.";
       exit(0);
    } 
    //echo $fileupload."<br>";
} 
        
$IVA = 0.0;
$result = mssql_query("vm_getfolio_s 'IVA'",$db);
if (($row = mssql_fetch_array($result))) $IVA = $row['Tbl_fol'] / 10000.0;

require_once 'phpExcelReader/Excel/reader.php';


// ExcelFile($filename, $encoding);
$data = new Spreadsheet_Excel_Reader();


// Set output Encoding.
$data->setOutputEncoding('CP1251');

/***
* if you want you can change 'iconv' to mb_convert_encoding:
* $data->setUTFEncoder('mb');
*
**/

/***
* By default rows & cols indeces start with 1
* For change initial index use:
* $data->setRowColOffset(0);
*
**/



/***
*  Some function for formatting output.
* $data->setDefaultFormat('%.2f');
* setDefaultFormat - set format for columns with unknown formatting
*
* $data->setColumnFormat(4, '%.3f');
* setColumnFormat - set format for column (apply only to number fields)
*
**/

$data->read($fileupload);

/*


 $data->sheets[0]['numRows'] - count rows
 $data->sheets[0]['numCols'] - count columns
 $data->sheets[0]['cells'][$i][$j] - data from $i-row $j-column

 $data->sheets[0]['cellsInfo'][$i][$j] - extended info about cell
    
    $data->sheets[0]['cellsInfo'][$i][$j]['type'] = "date" | "number" | "unknown"
        if 'type' == "unknown" - use 'raw' value, because  cell contain value with format '0.00';
    $data->sheets[0]['cellsInfo'][$i][$j]['raw'] = value if cell without format 
    $data->sheets[0]['cellsInfo'][$i][$j]['colspan'] 
    $data->sheets[0]['cellsInfo'][$i][$j]['rowspan'] 
*/

error_reporting(E_ALL ^ E_NOTICE);

$Crr_Ant = 0;
$salida = "<ROOT>\n";
for ($i = 1; $i <= $data->sheets[0]['numRows']; $i++) {
    if ($i > 1) {
	for ($j = 1; $j <= $data->sheets[0]['numCols']; $j++) {
            switch($j) {
                case 2:
                    $Crr = $data->sheets[0]['cells'][$i][$j];
                    if ($Crr_Ant == 0) $Crr_Ant = $Crr;
                    if ($Crr_Ant != $Crr) {
                        $salida.="</ROOT>";
                        //$sql = "vm_impcrr '$salida'";
                        $result = mssql_query("vm_impcrr '$salida'", $db);
                        //echo $sql."<BR>";
                        $salida = "<ROOT>\n";
                        $Crr_Ant = $Crr;
                    }
                    $salida.="<record Crr=\"".$Crr."\"";
                    break;
                case 4:
                    $salida.=" Svc=\"".$data->sheets[0]['cells'][$i][$j]."\"";
                    break;
                case 6:
                    $salida.=" Rgn=\"".$data->sheets[0]['cells'][$i][$j]."\"";
                    break;
                case 7:
                case 8:
                case 9:
                case 10:
                case 11:
                case 12:
                    $valor = floatval($data->sheets[0]['cells'][$i][$j]) / (1.0 + $IVA);
                    $salida.=" T".($j-6)."=\"".$valor."\"";
                    break;
            }
	}
	$salida.=" />\n";
    }
}

$salida.="</ROOT>";
//$sql = "vm_impcrr '$salida'";
$result = mssql_query("vm_impcrr '$salida'",$db);
//echo $sql."<BR>";

header("Location: cotizador/pdespacho.php"); 
?>
