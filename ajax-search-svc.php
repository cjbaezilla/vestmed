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
    case "svc":
        //Seleccionar las lineas
        $sp = mssql_query("vm_SvcCrrCmb ".$param, $db);
        while($row = mssql_fetch_array($sp))
        {
            spitXml($row['Cod_SvcCrr'],utf8_encode($row['Des_SvcCrr']));
        }
        break;
		
    case "con":
        //Seleccionar las lineas
        $codsvc = ok($_POST['param_codsvc']);
        $codclt = ok($_POST['param_codclt']);
        $codsuc = ok($_POST['param_codsuc']);
        $peso = ok($_POST['param_peso']);
        $sp = mssql_query("vm_SvcCrrCmb ".$param.",".$codsvc, $db);
        while($row = mssql_fetch_array($sp))
        {
            spitXml("condiciones",utf8_encode($row['Con_SvcCrr']));
        }
		
        $sp = mssql_query("vm_suc_s $codclt, $codsuc", $db);
        if (($row = mssql_fetch_array($sp))) $codrgn = $row["Cod_Rgn"];
		
        $sp = mssql_query("vm_SvcCrr_Prc_s ".$param.",".$codsvc.",".$codrgn.",".$peso, $db);
        while($row = mssql_fetch_array($sp))
        {
            //if ($peso <= $row["Pes_Max"]) {
                spitXml("costo",$row['Prc_Dsp']);
                mssql_free_result($sp);
                break;
            //}
        }
		
        break;
		
    case "peso":
        $codsvc = ok($_POST['param_codsvc']);
        $codcdd = ok($_POST['param_codcdd']);
        $peso   = ok($_POST['param_peso']);

        $sp = mssql_query("vm_cdd_s ".$codcdd, $db);
        if ($row = mssql_fetch_array($sp)) $codrgn = $row['Cod_Rgn'];
		
        $sp = mssql_query("vm_SvcCrr_Prc_s ".$param.",".$codsvc.",".$codrgn.",".$peso, $db);
        while($row = mssql_fetch_array($sp))
        {
            //if ($peso < $row["Pes_Max"]) {
                    spitXml("costo",number_format($row['Prc_Dsp'],0,',','.'));
                    mssql_free_result($sp);
                    break;
            //}
        }
        break;
        
    case "svcprd":
        if (intVal($param) > 0) {
            spitXml("0","Dr");
            spitXml("1","Dra");
            spitXml("2","Sr");
            spitXml("3","Sra");
            
            $sp = mssql_query("vm_fnttxt_cmb",$db);
            while (($row = mssql_fetch_array($sp))) 
                spit2Xml ("font", $row['Cod_FntTxt'],  utf8_encode ($row['Des_FntTxt']));
            
            $sp = mssql_query("vm_colfont_cmb",$db);
            while (($row = mssql_fetch_array($sp))) 
                spit2Xml ("color", $row['Cod_ColFont'], utf8_encode ($row['Des_ColFont']));
            
            $sp = mssql_query("vm_fntlgo_cmb_alt",$db);
            while (($row = mssql_fetch_array($sp))) 
                spit2Xml ("logo", $row['Cod_FntLgo'], utf8_encode ($row['Des_FntLgo']));
            
        }
        break;
		
    case "addsvc":
        $cod_prd  = $param;
        $cod_dsg  = ok($_POST['id_dsg']);
        $cod_pat  = ok($_POST['id_pat']);
        $val_sze  = ok($_POST['param_sze']);
        $cantidad = ok($_POST['param_dfctd']);
        $cod_cot  = ok($_POST['cod_cot']);
        $cod_clt  = ok($_POST['param_clt']);
        $cod_per  = ok($_POST['param_per']);
        $xml_srv  = $_POST['param_xmlsrv'];
        $xml_srv  = str_replace("[", "\"", $xml_srv);
        $xml_srv  = str_replace("]", "\"", $xml_srv);
        
        $prc_prd = 0.0;
        $result = mssql_query ("vm_s_dsg '$cod_dsg'", $db) or die ("<filter><code>1</code><value>No pudo obtener datos de DSG</value></filter>");
	if (($row = mssql_fetch_array($result))) {
            $cod_mca = $row["Cod_Mca"];
            $cod_sty = $row["Cod_Sty"];

            $result = mssql_query ("vm_strinv_prodinfo '$cod_prd'", $db) or die ("<filter><code>2</code><value>No pudo obtener datos de PAT</value></filter>");
            if (($row = mssql_fetch_array($result))) {
                $cod_grppat = $row["Cod_GrpPat"];
                $hoy = date('Ymd');
                $result = mssql_query ("BDFlexline..sp_stock '$cod_mca', '$cod_sty', '$cod_grppat', '001', '$hoy'", $db) 
                               or die ("<filter><code>3</code><value>No pudo obtener datos del STOCK</value></filter>");
                
                if (($row = mssql_fetch_array($result))) $prc_prd = $row["precio"];
            }
        }
        
        $parametro  = "<?xml version=\"1.0\" encoding=\"iso-8859-1\"?>";
        $parametro .= "<parametro";
        $parametro .= PutParametro("cod_cot", $cod_cot);
        $parametro .= PutParametro("cod_per", $cod_per);
        $parametro .= PutParametro("cod_clt", $cod_clt);
        $parametro .= PutParametro("cot_ctd", $cantidad);
        $parametro .= PutParametro("cod_dsg", $cod_dsg);
        $parametro .= PutParametro("val_sze", $val_sze);
        $parametro .= PutParametro("cod_pat", $cod_pat);
        $parametro .= PutParametro("cod_grpprd", $cod_prd);
        $parametro .= PutParametro("prc_prd", $prc_prd);
        $parametro .= ">";
        $parametro .= str_replace("null", "", utf8_decode($xml_srv));
        $parametro .= "</parametro>";
        
        //echo $parametro;
        
        $result = mssql_query ("vm_i_cotweb_svc '".$parametro."'", $db)
                or die ("<filter><code>4</code><value>No se pudo agregar a la cotizacion</value></filter>");
        
	if (($row = mssql_fetch_array($result))) $cod_cot = $row["cod_cot"];
	
	if (!isset($_SESSION['CodCot'])) $_SESSION['CodCot'] = $cod_cot;     
        
        spitXml ("0", $cod_cot);
        
        break;
        
    case "addptl":
        $cod_clt = ok($_POST['param_clt']);
        $cod_per = ok($_POST['param_per']);
        $cod_pre = ok($_POST['param_dfpre']);
        $cod_fnt = ok($_POST['param_dffnt']);
        $cod_col = ok($_POST['param_dfcol']);
        $linea1  = utf8_decode(ok($_POST['param_dfli1']));
        $linea2  = utf8_decode(ok($_POST['param_dfli2']));
        
        $result = mssql_query ("vm_i_websvcptl $cod_clt, $cod_per, $param, $cod_pre, '$linea1', '$linea2', $cod_fnt, $cod_col", $db)
                  or die ('error sql');
	if (($row = mssql_fetch_array($result))) {
            spitXml ($row['coderr'], $row['msgerr']);
            
            $result = mssql_query ("vm_s_websvcptl $cod_clt, $cod_per", $db);
            while (($row = mssql_fetch_array($result)))
                spit3Xml ($row['cod_ptl'], $row['Cod_Svc'], $row['Cod_PreLin'], $row['Txt1_Brd'], $row['Txt2_Brd'], $row['Cod_FntBrd'], $row['Des_FntTxt'], $row['Cod_ColBrd'], $row['Des_ColFont']); 
        }
        break;
        
    case "selptl":
        $cod_clt = ok($_POST['param_clt']);
        $cod_per = ok($_POST['param_per']);
        $result = mssql_query ("vm_s_websvcptl $cod_clt, $cod_per, $param", $db);
        while (($row = mssql_fetch_array($result)))
            spit3Xml ($row['cod_ptl'], $row['Cod_Svc'], $row['Cod_PreLin'], $row['Txt1_Brd'], $row['Txt2_Brd'], $row['Cod_FntBrd'], $row['Des_FntTxt'], $row['Cod_ColBrd'], $row['Des_ColFont']); 
        
        spitXml("0","Dr");
        spitXml("1","Dra");
        spitXml("2","Sr");
        spitXml("3","Sra");

        $sp = mssql_query("vm_fnttxt_cmb",$db);
        while (($row = mssql_fetch_array($sp))) 
            spit2Xml ("font", $row['Cod_FntTxt'],  utf8_encode ($row['Des_FntTxt']));

        $sp = mssql_query("vm_colfont_cmb",$db);
        while (($row = mssql_fetch_array($sp))) 
            spit2Xml ("color", $row['Cod_ColFont'], utf8_encode ($row['Des_ColFont']));

        $sp = mssql_query("vm_fntlgo_cmb_alt",$db);
        while (($row = mssql_fetch_array($sp))) 
            spit2Xml ("logo", $row['Cod_FntLgo'], utf8_encode ($row['Des_FntLgo']));
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

function spit2Xml($filtro, $code,$value)
{
    echo "\t<".$filtro.">\n";
    echo "\t\t<code>".$code."</code>\n";
    echo "\t\t<value>".$value."</value>\n";
    echo "\t</".$filtro.">\n";
}

function spit3Xml($codptl, $codsvc, $codpre, $linea1, $linea2, $codfnt, $desfnt, $codcol, $descol)
{
    echo "\t<servicios>\n";
    echo "\t\t<codptl>".$codptl."</codptl>\n";
    echo "\t\t<codsvc>".$codsvc."</codsvc>\n";
    echo "\t\t<codpre>".$codpre."</codpre>\n";
    echo "\t\t<linea1>".utf8_encode($linea1)."</linea1>\n";
    echo "\t\t<linea2>".utf8_encode($linea2)."</linea2>\n";
    echo "\t\t<codfnt>".$codfnt."</codfnt>\n";
    echo "\t\t<desfnt>".utf8_encode($desfnt)."</desfnt>\n";
    echo "\t\t<codcol>".$codcol."</codcol>\n";
    echo "\t\t<descol>".utf8_encode($descol)."</descol>\n";
    echo "\t</servicios>\n";
}
?>