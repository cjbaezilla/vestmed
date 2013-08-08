<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
include("../../connect.php");
//include("../global_cot.php");

function formatearRut($rut){
	$aRut   = split("-", $rut);
	return formatearMillones($aRut[0])."-".$aRut[1];
}
function formatearMillones($nNmb){
	$sRes = "";
	for ($j, $i = strlen($nNmb) - 1, $j = 0; $i >= 0; $i--, $j++)
		$sRes = substr($nNmb,$i,1).(($j > 0) && ($j % 3 == 0)? ".": "").$sRes;
	return $sRes;
}

$IVA = 0.0;
$result = mssql_query("vm_getfolio_s 'IVA'",$db);
if (($row = mssql_fetch_array($result))) $IVA = $row['Tbl_fol'] / 10000.0;


$output = array(
        "iTotalRecords" => 0,
        "iTotalDisplayRecords" => 0,
        "aaSorting" => array(),
        "aaData" => array()
);


$result = mssql_query("vm_s_cot_vta", $db);
while ($row = mssql_fetch_array($result)) {
    $row2 = array();
    
    //$row2[0] = date("d/m/Y", strtotime($row['Fec_Cot']));
    $row2[0] = $row['Fec_Cot'];
    $row2[1] = "<a href='javascript:ver_preview(".$row['Cod_Cot'].")'>".$row['Num_Cot']."</a>";
    $row2[2] = "<a href='javascript:ver_previewodc(".$row['Cod_Cot'].")'>".$row['Cod_Odc']."</a>";
    $row2[3] = formatearRut($row['Num_Doc']);
    if ($row["Cod_TipPer"] == 1) {
        $nombre = utf8_encode (trim($row['Pat_Per'])." ".trim($row['Mat_Per']).", ".trim($row['Nom_Per']));
        $nombre_corto = utf8_encode(trim($row['Nom_Per'])." ".trim($row['Pat_Per']));
        $row2[4] = $nombre;
    }
    else
        $row2[4] = $row['RznSoc_Per'];
    /*
    if ($row['Cod_Iva'] == 1) {
        $mto_odc = ($row['Mto_Odc'] + $row['Prc_Dsp']) / (1.0 + $IVA);
        $val_des = ($row['Mto_Odc'] * $row['Val_Des'] / 100) / (1.0 + $IVA);
        $prc_dsp = $row['Prc_Dsp'] / (1.0 + $IVA);
        $mto_tot = $mto_odc - $val_des - $prc_dsp;
    }
    else {
    */
        $mto_odc = ($row['Mto_Odc'] + $row['Prc_Dsp']);
        $val_des = ($row['Mto_Odc'] * $row['Val_Des'] / 100);
        $prc_dsp = $row['Prc_Dsp'];
        $mto_tot = $mto_odc - $val_des - $prc_dsp;
    //}
    $row2[5] = number_format($mto_odc,0,',','.');
    $row2[6] = $row['Arc_Adj'] != " " ? "OK" : "PENDIENTE";
    
    $output['aaData'][] = $row2;
    $output['iTotalRecords']++;
}
$output['aaSorting'][] = array(2, 'asc');
$output['iTotalDisplayRecords'] = $output['iTotalRecords'];

echo json_encode($output);
?>
