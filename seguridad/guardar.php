<?php
ini_set('display_errors', '0');
session_start();
include("../config.php");

if (!isset($_SESSION['usuario'])) header("Location: index.php");

$xml = "<?xml version=\"1.0\" encoding=\"iso-8859-1\"?>";
$xml.= "<parametro";
$xml.= " Usr=\"".ok($_POST["dfusr"])."\">";
foreach ($_POST as $key => $value) {
    //echo $key." --> ".$value."<BR>";  
    if ($key == "permiso")
        foreach ($value as $index => $opcmod) {
            //echo "&nbsp;".$index." --> ".$opcmod."<BR>";    
            $valor = split("_", $opcmod);
            $xml .= "<permiso Mod=\"".$valor[0]."\" Opc=\"".$valor[1]."\" />";
        }
}
$xml .= "</parametro>";

$result = mssql_query("vm_seg_i_opcmodweb '$xml'",$db) or die('Error al tratar de actualizar los permisos');

header( 'Location: setpermisos.php?usr='.$_POST["dfusr"] );

?>
