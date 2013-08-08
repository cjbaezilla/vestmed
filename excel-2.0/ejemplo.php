<?php

require ('Writer.php');

$filename = 'myFile.xls';

$workbook = new Spreadsheet_Excel_Writer();
$workbook->send($filename);

// Look at documentation
$workbook->setVersion(8);
// Temporary files directory
$workbook->setTempDir('../adjuntos');

$worksheet =& $workbook->addWorksheet('Items');
$worksheet->setInputEncoding('UTF-8');
$worksheet->setMargins(0.25);
$worksheet->centerHorizontally(1);

$worksheet->activate();

$format_header =& $workbook->addFormat();
$format_header->setBold();
$format_header->setSize(10);

$worksheet->write(0, 0, 'Code',$format_header);
//$worksheet->setColumn(0, 0, 30);
$worksheet->write(0, 1, 'Title',$format_header);
//$worksheet->setColumn(1, 1, 50);

$format_row =& $workbook->addFormat();
$format_row->setSize(10);

$format_numero =& $workbook->addFormat();
$format_numero->setNumFormat("#.##");

//foreach ($items as $item)
//{
//    $worksheet->writeString($i, 0, $item->code, $format_row);
//    $worksheet->writeString($i, 1, $item->title, $format_row);
//}

for ($i = 1; $i < 10; $i++) {
    $worksheet->writeNumber($i, 0, $i);
    $worksheet->writeString($i, 1, "Item ".$i, $format_row);
}

$workbook->close();

?>
