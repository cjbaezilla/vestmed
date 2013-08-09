<?php
//Obtengo los datos de conexion de la base de datos
ini_set('display_errors', '0');
session_start();
include("../config.php");

$p_grpprd = ok($_GET['grpprd']);
$result = mssql_query("vm_strinv_prodinfo '".$p_grpprd."'", $db);
if ($row = mssql_fetch_array($result)) {
	$cod_mca = $row['marca'];
	$cod_dsg = $row['id_dsg'];
	
	$result = mssql_query("vm_tabsze_dsg_s '".$cod_dsg."'", $db);
	if ($row = mssql_fetch_array($result)) {
		$nom_tabsze = str_replace("#", "'", $row['Nom_TabSze']);
		$cod_tabsze = $row['Cod_TabSze'];
	}
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title></title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
    <link href="../css/tallas.css" type="text/css" rel="stylesheet" />
</head>

<body bgcolor="#c1f4e5" style="margin-top:5px;">
	<div class="logo"><img src="../images/marcas/<?php echo strtolower($cod_mca); ?>-logo.png" /></div>
	<table class="tabla-tallas">
    	<caption><?php echo $nom_tabsze; ?></caption>
        <tr>        
            <td>Size</td>
			<?php 
				$result = mssql_query("vm_sze_tabsze_s '".$cod_tabsze."'", $db);
				$bst = 0; $wst = 0; $hip = 0; $cht = 0; $sho = 0;
				$tdBst = "<td>Bust</td>"; $tsWst = "<td>Waist</td>"; $tsHip = "<td>Hip</td>"; $tsCht = "<td>Chest</td>"; $tsSho = "<td>CHI</td>";
				while ($row = mssql_fetch_array($result)) {
					if ($row['EstWeb_Sze'] == 1) {
					   $bst = $bst + (trim($row['MinBst_Sze']) != "" ? 1 : 0);
					   $tsBst = $tsBst."<td>".(trim($row['MinBst_Sze']) != "" ? $row['MinBst_Sze']."-".$row['MaxBst_Sze'] : "&nbsp;")."</td>";
					   
					   $wst = $wst + (trim($row['MinWst_Sze']) != "" ? 1 : 0);
					   $tsWst = $tsWst."<td>".(trim($row['MinWst_Sze']) != "" ? $row['MinWst_Sze']."-".$row['MaxWst_Sze'] : "&nbsp;")."</td>";

					   $hip = $hip + (trim($row['MinHip_Sze']) != "" ? 1 : 0);
					   $tsHip = $tsHip."<td>".(trim($row['MinHip_Sze']) != "" ? $row['MinHip_Sze']."-".$row['MaxHip_Sze'] : "&nbsp;")."</td>";

					   $cht = $cht + (trim($row['MinCht_Sze']) != "" ? 1 : 0);
					   $tsCht = $tsCht."<td>".(trim($row['MinCht_Sze']) != "" ? $row['MinCht_Sze']."-".$row['MaxCht_Sze'] : "&nbsp;")."</td>";
					   
					   $sho = $sho + (trim($row['NumSho_Sze']) != "" ? 1 : 0);
					   $tsSho = $tsSho."<td>".(trim($row['NumSho_Sze']) != "" ? $row['NumSho_Sze'] : "&nbsp;")."</td>";
			?>
            <td><?php echo $row['Val_Sze']; ?></td>
			<?php
					}
				}
			?>
        </tr>
		<?php if ($bst > 0) { ?>
		<tr><?php echo $tsBst; ?></tr>
		<?php } else if ($wst > 0) { ?>
		<tr><?php echo $tsWst; ?></tr>
		<?php } else if ($hip > 0) { ?>
		<tr><?php echo $tsHip; ?></tr>
		<?php } else if ($cht > 0) { ?>
		<tr><?php echo $tsCht; ?></tr>
		<?php } else if ($sho > 0) { ?>
		<tr><?php echo $tsSho; ?></tr>
		<?php } ?>
	</table>
</body>
</html>
