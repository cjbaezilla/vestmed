<?php
//Encabezados
include("config.php");
header("Content-type: text/xml");
header("Cache-Control: no-cache");

$IVA = 0.0;
$result = mssql_query("vm_getfolio_s 'IVA'",$db);
if (($row = mssql_fetch_array($result))) $IVA = $row['Tbl_fol'] / 10000.0;

$p_dsg = ok($_POST['id_dsg']);
$p_sze = ok($_POST['id_sze']);
$p_grpprd = ok($_POST['id_grpprd']);
$p_cot = isset($_POST['cod_cot']) ? ok($_POST['cod_cot']) : 0;
$prf_oft = 0;

//if($p_cot != 0)
//    $sizes = mssql_query("vm_strinv_szepat_cot '".$p_pat."'".",'".$p_dsg."', $p_cot, '$p_grpprd'",$db);
//else
if ($p_sze == '_ALL')
    $sizes = mssql_query("vm_strcol_prod '$p_grpprd'",$db);
else
    $sizes = mssql_query("vm_strinv_patsze '$p_sze','$p_dsg','$p_grpprd'",$db);

$i=0;
?>
<?php
$xml_size="<tabpat>";
$tot_prd = 0;
while ($row = mssql_fetch_array($sizes)) {
    $xml_size.="<pat>";
    $xml_size.="<code>".$row["Cod_Pat"]."</code>";
    $xml_size.="<key>".$row["Key_Pat"]."</key>";
    $xml_size.="<des>".str_replace("&", "&amp;", $row["Des_Pat"])."</des>";
    $xml_size.="<stock>".intval($row["Stock"])."</stock>";
    if ($p_cot > 0)
        $xml_size.="<ctd>".$row["Val_Ctd"]."</ctd>";
    $prf_oft = $row["PrcOfert"];
    $prf_oft += $prf_oft * $IVA;
    $prf_oft = round($prf_oft);
    $xml_size.="<oferta>".$prf_oft."</oferta>";
    $xml_size.="</pat>";
    
    $tot_prd += intval($row["Stock"]);
}
$xml_size.="</tabpat>";
?>
<?php

//Generar el XML
echo "<?xml version=\"1.0\"?>\n";
echo "<response>\n";
echo "\t<selsze>"."\n";
echo "\t\t<code>".$p_sze."</code>\n";
echo "\t\t<stock>".$tot_prd."</stock>\n";
echo "\t</selsze>"."\n";
echo $xml_size;

echo "</response>";
?>