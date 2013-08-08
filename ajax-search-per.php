<?php
//Encabezados
include("config.php");
header("Content-type: text/xml");
header("Cache-Control: no-cache");

$param=ok($_POST['param_filter']);
//Generar el XML
echo "<?xml version=\"1.0\"?>\n";
echo "<response>\n";
switch($_POST['search_type'])
{
    case "per":
    case "clt":
        //Seleccionar las lineas
        $param = str_replace(".", "", $param);
        $param = str_replace("-", "", $param);
        $param = substr($param,0,strlen($param)-1)."-".substr($param,strlen($param)-1,1);
        $sp = mssql_query("vm_s_per_tipdoc 1, '$param'",$db);
        if(($row = mssql_fetch_array($sp)))
        {
            spitXml("tipper", $row['Cod_TipPer']);
            if ($row['Cod_TipPer'] == 1) {
                    spitXml("nombre",utf8_encode($row['Pat_Per']." ".$row['Mat_Per']." ".$row['Nom_Per']));
                    spitXml("nomper",trim(utf8_encode($row['Nom_Per'])));
                    spitXml("apppat",trim(utf8_encode($row['Pat_Per'])));
                    spitXml("appmat",trim(utf8_encode($row['Mat_Per'])));
            }
            else {
                    spitXml("nombre",trim(utf8_encode($row['RznSoc_Per'])));
                    spitXml("nomfan",trim(utf8_encode($row['NomFan_Per'])));
            }
            spitXml("sexo",$row['Sex']);
            spitXml("rut",$row['Num_Doc']);
            spitXml("rutfmt",formatearRut($row['Num_Doc']));
            spitXml("codclt",$row['Cod_Clt']);
            spitXml("codper",$row['Cod_Per']);
            $cod_clt = $row['Cod_Clt'];
            mssql_free_result($sp);

            if ($cod_clt != null) {
                    $sp = mssql_query("vm_suc_s $cod_clt", $db);
                    while ($row = mssql_fetch_array($sp)) {
                            spitXmlSuc($row['Cod_Suc'],  utf8_encode($row['Nom_Suc']));
                    }
                    mssql_free_result($sp);
            }
        }
        else {
                spitXml("rut",$param);
                spitXml("rutfmt",formatearRut($param));
                //spitXml("tipper", $param);
                $token = split("-", $param);
                if (intval($token[0]) < 50000000)
                        spitXml("tipper", "1");
                else
                        spitXml("tipper", "2");
        }

        break;

    case "usr":
        //Datos del Usuario
        $ok = false;
        $clave = isset($_POST['param_clave']) ? ok($_POST['param_clave']) : "";
        $sp = mssql_query("vm_sel_usrseg '$param'",$db);
        echo "\t<filter>\n";
        if(($row = mssql_fetch_array($sp)))
        {
            echo "\t\t<descripcion>".$row['DESCRIPCION']."</descripcion>\n";
            if ($row['PWD'] == $clave)
                echo "\t\t<estado>1</estado>\n";
            else
                echo "\t\t<estado>2</estado>\n";
            mssql_free_result($sp);
            $ok = true;
        }
        else {
            echo "\t\t<descripcion></descripcion>\n";
            echo "\t\t<estado>0</estado>\n";
        }
        echo "\t</filter>\n";
        if ($ok) {
            $sp = mssql_query("vm_seg_usr_opcmodweb '$param'",$db);
            while (($row = mssql_fetch_array($sp))) 
                if ($row["CodUsr"] != ' ') spitXmlSeg($row["Id_Mod"],$row["ID_Opc"]);
            mssql_free_result($sp);
        }
        break;
        
    case "suc":
        $cod_clt=ok($_POST['param_cliente']);
        if ($param == "NewSuc") {
                spitXml("codclt",$cod_clt);
                $sp = mssql_query("vm_cli_s $cod_clt", $db);
                if (($row = mssql_fetch_array($sp)))
                {
                        spitXml("numdoc",$row['Num_Doc']);
                        mssql_free_result($sp); 
                }
        }
        else {
                $sp = mssql_query("vm_suc_s $cod_clt, $$param", $db);
                if (($row = mssql_fetch_array($sp)))
                {
                        spitXml("dirsuc",utf8_encode($row['Dir_Suc']));
                        spitXml("fonsuc",$row['Fon_Suc']);
                        spitXml("codcmn",$row['Cod_Cmn']);
                        spitXml("nomcmn",utf8_encode($row['Nom_Cdd']));
                        spitXml("codcdd",$row['Cod_Cdd']);
                        spitXml("nomcdd",utf8_encode($row['Nom_Cdd']));
                        spitXml("tipcmn",utf8_encode($row['Tip_Cmn']));
                        mssql_free_result($sp); 

                        $sp = mssql_query("vm_ctt_s $cod_clt, $$param", $db);
                        while ($row = mssql_fetch_array($sp)) {
                                spitXmlCtt($row['Cod_Per'],utf8_encode($row['Pat_Per']." ".$row['Mat_Per'].", ".$row['Nom_Per']));
                        }
                        mssql_free_result($sp); 
                }
        }
        break;
                
    case "ctt":
        $cod_clt=ok($_POST['param_cliente']);
        $cod_suc=ok($_POST['param_sucursal']);
        if ($param == "NewCtt") {
                spitXml("codclt",$cod_clt);
                spitXml("codsuc",$cod_suc);
                $sp = mssql_query("vm_cli_s $cod_clt", $db);
                if (($row = mssql_fetch_array($sp)))
                {
                        spitXml("numdoc",$row['Num_Doc']);
                        mssql_free_result($sp); 
                }
        }
        else {
                $sp = mssql_query("vm_cttper_s $cod_clt, $cod_suc, $param", $db);
                if (($row = mssql_fetch_array($sp)))
                {
                        spitXml("mail",$row['Mail_Ctt']);
                        spitXml("fonctt",$row['Fon_Ctt']);
                        mssql_free_result($sp); 
                }
        }
        break;
		
    case "fct":
        $cod_per=ok($_POST['param_persona']);
        $sp = mssql_query("vm_s_rutfct $param, $cod_per", $db);
        if (($row = mssql_fetch_array($sp)))
        {
                spitXml("numdoc",$row['Num_Doc']);
                spitXml("nomclt", utf8_encode($row['Nom_Clt']));
                spitXml("nomfan", utf8_encode($row['NomFan_Per']));
                spitXml("dirfct", utf8_encode($row['Dir_Fct']));
                spitXml("nomcmn", utf8_encode($row['Nom_Cmn']));
                spitXml("nomcdd", utf8_encode($row['Nom_Cdd']));
                spitXml("fonfct", $row['Fon_Fct']);
                spitXml("faxfct", $row['Fax_Fct']);
                spitXml("webfct", $row['Web_Fct']);
                mssql_free_result($sp); 
        }
        break;
		
    case "lstfct":
        $sp = mssql_query("vm_s_rutfct $param", $db);
        while($row = mssql_fetch_array($sp))
        {
            echo "\t<filter>\n";
            echo "\t\t<codper>".$row['Cod_Per']."</codper>\n";
            echo "\t\t<numdoc>".$row['Num_Doc']."</numdoc>\n";
            echo "\t\t<nomclt>".utf8_encode($row['Nom_Clt'])."</nomclt>\n";
            echo "\t</filter>\n";
        }
        break;

}
echo "</response>";

function spitXml($code,$value)
{
    echo "\t<filter>\n";
    echo "\t\t<code>".$code."</code>\n";
    echo "\t\t<value>".$value."</value>\n";
    echo "\t</filter>\n";
}

function spitXmlSuc($code,$value)
{
    echo "\t<sucursal>\n";
    echo "\t\t<code>".$code."</code>\n";
    echo "\t\t<value>".$value."</value>\n";
    echo "\t</sucursal>\n";
}

function spitXmlCtt($code,$value)
{
    echo "\t<contacto>\n";
    echo "\t\t<code>".$code."</code>\n";
    echo "\t\t<value>".$value."</value>\n";
    echo "\t</contacto>\n";
}

function spitXmlSeg($code,$value)
{
    echo "\t<permiso>\n";
    echo "\t\t<modulo>".$code."</modulo>\n";
    echo "\t\t<opcion>".$value."</opcion>\n";
    echo "\t</permiso>\n";
}

/*echo "<?xml version=\"1.0\"?>\n<response><filter><code>1</code><value>VALOR 1</value></filter><filter><code>2</code><value>VALOR 2</value></filter></response>";*/
?>