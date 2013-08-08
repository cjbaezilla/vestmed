<?php

/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

ini_set('display_errors', '0');
session_start();
include("config.php");

require ('excel-2.0/Writer.php');

$filename = 'precios_despacho.xls';

$workbook = new Spreadsheet_Excel_Writer();
$workbook->send($filename);

// Look at documentation
$workbook->setVersion(8);
// Temporary files directory
$workbook->setTempDir('adjuntos');

$worksheet =& $workbook->addWorksheet('Precios');
//$worksheet->setInputEncoding('UTF-8');
$worksheet->setMargins(0.25);
$worksheet->centerHorizontally(1);

$worksheet->activate();

$format_header =& $workbook->addFormat();
$format_header->setBold();
$format_header->setSize(10);

$worksheet->write(0, 0, 'Carrier', $format_header);
$worksheet->write(0, 1, 'CodCrr', $format_header);
$worksheet->write(0, 2, 'Servicio', $format_header);
$worksheet->write(0, 3, 'CodSvc', $format_header);
$worksheet->write(0, 4, 'Region', $format_header);
$worksheet->write(0, 5, 'CodRgn', $format_header);
$worksheet->write(0, 6, '1.5 Kg', $format_header);
$worksheet->write(0, 7, '3 Kg', $format_header);
$worksheet->write(0, 8, '6 Kg', $format_header);
$worksheet->write(0, 9, '10 Kg', $format_header);
$worksheet->write(0,10, '15 Kg', $format_header);
$worksheet->write(0,11, 'Kg Adicional', $format_header);

//$format_row =& $workbook->addFormat();
//$format_row->setSize(10);

//$format_numero =& $workbook->addFormat();
//$format_numero->setNumFormat("#.##");

//foreach ($items as $item)
//{
//    $worksheet->writeString($i, 0, $item->code, $format_row);
//    $worksheet->writeString($i, 1, $item->title, $format_row);
//}

$IVA = 0.0;
$result = mssql_query("vm_getfolio_s 'IVA'",$db);
if (($row = mssql_fetch_array($result))) $IVA = $row['Tbl_fol'] / 10000.0;

$i = 1;
$factor = 1.0 + $IVA;
$result = mssql_query("vm_prcsvc", $db);
while ($row = mssql_fetch_array($result)) {
    $worksheet->writeString($i, 0, $row['Des_Crr']);
    $worksheet->writeNumber($i, 1, $row['Cod_Crr']);

    $worksheet->writeString($i, 2, $row['Des_SvcCrr']);
    $worksheet->writeNumber($i, 3, $row['Cod_SvcCrr']);

    $worksheet->writeString($i, 4, $row['Nom_Rgn']);
    $worksheet->writeNumber($i, 5, $row['Cod_Rgn']);

    $worksheet->writeNumber($i, 6, number_format($row['tramo1']*$factor,0,',',''));
    $worksheet->writeNumber($i, 7, number_format($row['tramo2']*$factor,0,',',''));
    $worksheet->writeNumber($i, 8, number_format($row['tramo3']*$factor,0,',',''));
    $worksheet->writeNumber($i, 9, number_format($row['tramo4']*$factor,0,',',''));
    $worksheet->writeNumber($i,10, number_format($row['tramo5']*$factor,0,',',''));
    
    $worksheet->writeNumber($i,11, number_format($row['adicional']*$factor,0,',',''));
    
    $i++;
}
mssql_free_result($result);


$workbook->close();
?>
