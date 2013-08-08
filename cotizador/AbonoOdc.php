<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
ini_set('display_errors', '0');
session_start();
include("global_cot.php");

$Cod_Odc = intval(ok($_POST['param_filter']));
$MtoAbn = $_POST['param_abn'];

mssql_query("sp_u_pgoodc $Cod_Odc, $MtoAbn", $db);

?>
