<?php
//Encabezados
include("../config.php");
header("Content-type: text/xml");
header("Cache-Control: no-cache");
echo "<?xml version=\"1.0\"?>\n";
echo "<response>\n";

switch($_POST['search_type'])
{
   case "emp":
        $Cod_Clt = ok($_POST['id_clt']);
        $Cod_Nvt = ok($_POST['id_nvt']);
        $Cod_Kit = ok($_POST['id_kit']);

        $query = "vm_kitprd_prd_s ".$Cod_Clt.", ".$Cod_Nvt.", ".$Cod_Kit;
        $result = mssql_query($query, $db) or die ('error en sql (1003)');
        while ($row = mssql_fetch_array($result)) {
            echo "\t<filter>\n";
            PutValor ('mca', $row['Cod_Mca']);
            PutValor ('dsg', $row['Cod_Dsg']);
            PutValor ('sty', $row['Cod_Sty']);
            PutValor ('pat', $row['Cod_Pat']);
            PutValor ('key', $row['Key_Pat']);
            PutValor ('des', $row['Des_Prd']);
            PutValor ('prd', $row['Cod_GrpPrd']);
            echo "\t</filter>\n";
        }
        break;
        
   case "sty":
        $Cod_Mca = ok($_POST['id_mca']);
        $query = "vm_strinv_prodcat '".$Cod_Mca."'";
        $result = mssql_query($query, $db) or die ('error en sql (1004)');
        while ($row = mssql_fetch_array($result)) {
            echo "\t<filter>\n";
            PutValor ('sty', $row['style']);
            PutValor ('grp', $row['grpprd_id']);
            echo "\t</filter>\n";
        }
       break;
       
   case "pat":
        $Cod_GrpPrd = ok($_POST['id_grpprd']);
        $query = "vm_strcol_prod '".$Cod_GrpPrd."'";
        $result = mssql_query($query, $db) or die ('error en sql (1005)');
        while ($row = mssql_fetch_array($result)) {
            echo "\t<filter>\n";
            PutValor ('key', $row['Cod_Pat']);
            PutValor ('des', $row['Des_Pat']);
            echo "\t</filter>\n";
        }
       break;
       
   case "sze":
        $Cod_GrpPrd = ok($_POST['id_grpprd']);
        $ArrGrp = split('-', $Cod_GrpPrd);
        $p_dsg = $ArrGrp[0];
        $Cod_Pat = ok($_POST['id_pat']);
        $query = "vm_strinv_szepat '$Cod_Pat','$p_dsg','$Cod_GrpPrd'";
        $result = mssql_query($query, $db) or die ('error en sql (1006)');
        while ($row = mssql_fetch_array($result)) {
            echo "\t<filter>\n";
            PutValor ('sze', $row['Cod_Sze']);
            PutValor ('val', $row['Val_Sze']);
            PutValor ('prc', $row['PrcNormal']);
            echo "\t</filter>\n";
        }
       break;
}
echo "</response>";

function PutValor($code,$value)
{
    echo "\t\t<".$code.">".$value."</".$code.">\n";
}

?>