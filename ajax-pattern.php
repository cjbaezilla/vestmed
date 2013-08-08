<?php
//Encabezados
include("config.php");
header("Content-type: text/xml");
header("Cache-Control: no-cache");

$IVA = 0.0;
$result = mssql_query("vm_getfolio_s 'IVA'",$db);
if (($row = mssql_fetch_array($result))) $IVA = $row['Tbl_fol'] / 10000.0;

$p_dsg = ok($_POST['id_dsg']);
$p_pat = ok($_POST['id_pat']);
$p_grpprd = ok($_POST['id_grpprd']);
$p_keypat = ok($_POST['key_pat']);
$p_cot = isset($_POST['cod_cot']) ? ok($_POST['cod_cot']) : 0;
$prf_oft = 0;

if($p_cot != 0)
    $sizes = mssql_query("vm_strinv_szepat_cot '".$p_pat."'".",'".$p_dsg."', $p_cot, '$p_grpprd'",$db);
else if($p_pat=='_ALL')
    $sizes = mssql_query("vm_strinv_szedsg '$p_dsg', '$p_grpprd'",$db);
else
    $sizes = mssql_query("vm_strinv_szepat '$p_pat','$p_dsg','$p_grpprd'",$db);

$i=0;
?>
<?php
$xml_size="<tabsze>";
$tot_prd = 0;
while ($row = mssql_fetch_array($sizes)) {
    $xml_size.="<size>";
    $xml_size.="<code>".$row["Cod_Sze"]."</code>";
    $xml_size.="<val>".$row["Val_Sze"]."</val>";
    $xml_size.="<stock>".intval($row["Stock"])."</stock>";
    if ($p_cot > 0)
        $xml_size.="<ctd>".$row["Val_Ctd"]."</ctd>";
    $xml_size.="</size>";
    $prf_oft = $row["PrcOfert"];
    $prf_oft += $prf_oft * $IVA;
    $prf_oft = round($prf_oft);
    $tot_prd += intval($row["Stock"]);
}
$xml_size.="</tabsze>";
?>
<?php

//Generar el XML
echo "<?xml version=\"1.0\"?>\n";
echo "<response>\n";
echo "\t<selpat>"."\n";
echo "\t\t<code>".$p_pat."</code>\n";
echo "\t\t<desc>".$p_keypat."</desc>\n";
echo "\t\t<precio>".$prf_oft."</precio>\n";
echo "\t\t<stock>".$tot_prd."</stock>\n";
echo "\t</selpat>"."\n";
echo $xml_size;

echo "</response>";
?>