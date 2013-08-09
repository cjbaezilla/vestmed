<?php
//Encabezados
include("config.php");

$param=ok($_POST['param_filter']);
header("Content-type: text/xml");
header("Cache-Control: no-cache");

//Generar el XML
echo "<?xml version=\"1.0\"?>\n";
echo "<response>\n";
switch($_POST['search_type'])
{
    case "mca":
        //Seleccionar las lineas
        $sp = mssql_query("vm_linmca_s '', '".($param=="_ALL"?'':$param)."'",$db);
        while($row = mssql_fetch_array($sp))
        {
            spitXml($row[1],$row[2]);
        }
        break;
		
    case "grppat":
        //Seleccionar los patrones
        $param_addin=ok($_POST['param_grppat']);
        $sp = mssql_query("vm_pat_mca_grppat '".$param."','".$param_addin."'",$db);
        while($row = mssql_fetch_array($sp))
        {
            spitXml($row[1],$row[0]);
        }
        break;
		
    case "nat":
        $sp = mssql_query("vm_grppat_nat '".$param."'", $db);
        while($row = mssql_fetch_array($sp))
        {
            spitXml($row[1],$row[0]);
        }
        break;
		
    case "sex":
        $param_addin=ok($_POST['param_grppat']);
        $sp = mssql_query("vm_sex_grppat_nat '".$param."','".$param_addin."'", $db);
        while($row = mssql_fetch_array($sp))
        {
            spitXml($row[1],$row[0]);
        }
        break;
	
	case "sty":
		$sp = mssql_query("vm_strinv_prodcat '', '', '', '', '$param'", $db);
        while($row = mssql_fetch_array($sp))
        {
            spitXml($row['grpprd_id'],$row['grpprd_title']);
        }
        break;

    case "msg":
        $cod_clt = ok($_POST['param_clt']);
        $pagina = ok($_POST['param_pag']);
        $Tip_Bus = ok($_POST['param_bus']);
        $desde = ($pagina-1)*5 + 1;
        $hasta = $desde + 4;
        $sp = mssql_query("vm_s_msj_per $cod_clt, '$Tip_Bus', $desde, $hasta", $db);
        while($row = mssql_fetch_array($sp))
        {
            echo "\t<filter>\n";
            echo "\t\t<folio>".$row['Folio']."</folio>\n";
            echo "\t\t<foliodis>".$row['FolioDis']."</foliodis>\n";
            echo "\t\t<tipo>".$row['Tipo']."</tipo>\n";
            echo "\t\t<fecha>".date("d/m/Y", strtotime($row['Fecha']))."</fecha>\n";
            echo "\t\t<ctd>".$row['Qty']."</ctd>\n";
            echo "\t\t<ctdsinlec>".$row['QtySinLec']."</ctdsinlec>\n";
            echo "\t</filter>\n";
        }
        break;
		
    case "msgcot":
        $cod_clt = ok($_POST['param_clt']);
        $cod_cot = ok($_POST['param_cot']);
        $tip_bus = ok($_POST['param_bus']);
        $orden   = ok($_POST['param_ord']);
        $sp = mssql_query("vm_newmsg_clt $cod_clt, $orden, '$tip_bus', $cod_cot", $db);
        while($row = mssql_fetch_array($sp))
        {
            echo "\t<filter>\n";
            echo "\t\t<fecfmtcot>".$row['FecFmt_Cot']."</fecfmtcot>\n";
            echo "\t\t<codcot>".$row['Cod_Cot']."</codcot>\n";
            echo "\t\t<numcot>".$row['Num_Cot']."</numcot>\n";
            echo "\t\t<ctd>".$row['Ctd']."</ctd>\n";
            echo "\t\t<tnepdt>".$row['Tne_Pen']."</tnepdt>\n";
            echo "\t\t<feccot>".date("Ymd h:i:s", strtotime($row['Fec_Cot']))."</feccot>\n";
            echo "\t</filter>\n";
        }
        break;

    case "msgcotves":
        $cod_cot = ok($_POST['param_cot']);
        $tip_bus = ok($_POST['param_bus']);
        $orden   = ok($_POST['param_ord']);
        $cod_per = ($_POST['param_per'] == "" ? 0 : ok($_POST['param_per']));
        $sp = mssql_query("vm_newmsg_cot $orden, '$tip_bus', $cod_cot, $cod_per", $db);
        while($row = mssql_fetch_array($sp))
        {
            echo "\t<filter>\n";
            echo "\t\t<row>".$row['Row']."</row>\n";
            echo "\t\t<fecfmtcot>".$row['FecFmt_Cot']."</fecfmtcot>\n";
            echo "\t\t<codcot>".$row['Cod_Cot']."</codcot>\n";
            echo "\t\t<numcot>".$row['Num_Cot']."</numcot>\n";
            echo "\t\t<nomclt>".utf8_encode($row['Nom_Clt'])."</nomclt>\n";
            echo "\t\t<tnepdt>".$row['Tne_Pen']."</tnepdt>\n";
            //echo "\t\t<codclt>".$row['Cod_Clt']."</codclt>\n";
            echo "\t\t<numdoc>".formatearRut($row['Num_Doc'])."</numdoc>\n";
            echo "\t\t<ctd>".$row['Ctd']."</ctd>\n";
            echo "\t\t<ctdsinlec>".$row['CtdSinLec']."</ctdsinlec>\n";
            //echo "\t\t<feccot>".date("Ymd h:i:s", strtotime($row['Fec_Cot']))."</feccot>\n";
            echo "\t</filter>\n";
        }
        break;

    case "msgctt":
        $cod_clt = ok($_POST['param_clt']);
        $cod_fol = ok($_POST['param_fol']);
        $tip_bus = ok($_POST['param_bus']);
        $orden   = ok($_POST['param_ord']);
        $sp = mssql_query("vm_msgctt_clt $cod_clt, $orden, '$tip_bus', $cod_fol", $db);
        while($row = mssql_fetch_array($sp))
        {
                        echo "\t<filter>\n";
                        echo "\t\t<feccna>".$row['Fec_Ctt']."</feccna>\n";
                        echo "\t\t<folctt>".$row['Fol_CttWeb']."</folctt>\n";
                        echo "\t\t<tipcna>".$row['Tip_Cna']."</tipcna>\n";
                        echo "\t\t<ctd>".$row['Ctd']."</ctd>\n";
                        echo "\t\t<tnepdt>".$row['Tne_Pen']."</tnepdt>\n";
                        echo "\t</filter>\n";
        }
        break;

    case "msgcttves":
        $cod_fol = ok($_POST['param_fol']);
        $orden   = ok($_POST['param_ord']);
        $tip_bus = ok($_POST['param_bus']);
        $cod_per = ($_POST['param_per'] == "" ? 0 : ok($_POST['param_per']));
        $sp = mssql_query("vm_msgctt_cot $orden, '$tip_bus', $cod_fol, $cod_per", $db);
        while($row = mssql_fetch_array($sp))
        {
            echo "\t<filter>\n";
            echo "\t\t<row>".$row['Row']."</row>\n";
            echo "\t\t<feccna>".$row['Fec_Ctt']."</feccna>\n";
            echo "\t\t<folctt>".$row['Fol_CttWeb']."</folctt>\n";
            echo "\t\t<tipcna>".$row['Tip_Cna']."</tipcna>\n";
            echo "\t\t<tnepdt>".$row['Tne_Pen']."</tnepdt>\n";
            echo "\t\t<numdoc>".$row['Num_Doc']."</numdoc>\n";
            echo "\t\t<numdocfmt>".formatearRut($row['Num_Doc'])."</numdocfmt>\n";
            echo "\t\t<nomclt>".utf8_encode($row['Nom_Clt'])."</nomclt>\n";
            echo "\t\t<ctd>".$row['Ctd']."</ctd>\n";
            echo "\t\t<ctdsinlec>".$row['CtdSinLec']."</ctdsinlec>\n";
            echo "\t</filter>\n";
        }
        break;

    case "findper":
        $pat_per = ok($_POST['param_pat']);
        $mat_per = ok($_POST['param_mat']);
        $nom_per = ok($_POST['param_nom']);
        $nom_clt = ok($_POST['param_clt']);
        $num_doc = ok($_POST['param_rut']);
        $num_doc = str_replace (".", "", $num_doc);
        $num_doc = str_replace ("-", "", $num_doc);
        $num_doc = substr($num_doc, 0, -1)."-".substr($num_doc, -1);


        $query = "";
        if ($pat_per != "" || $mat_per != "" || $nom_per != "")
                $sp = mssql_query("vm_selper_s 2, '$pat_per', '$mat_per', '$nom_per'");
        else if ($nom_clt != "")
                $sp = mssql_query("vm_selper_s 1, NULL, NULL, NULL, NULL, NULL, '$nom_clt'");
        else if ($num_doc != "")
                $sp = mssql_query("vm_selper_s 5, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '$num_doc'");
        //$sp = mssql_query($query, $db);
        while($row = mssql_fetch_array($sp))
        {
            echo "\t<filter>\n";
            echo "\t\t<codper>".$row['Cod_Per']."</codper>\n";
            echo "\t\t<numdoc>".$row['Num_Doc']."</numdoc>\n";
            echo "\t\t<numdocfmt>".formatearRut($row['Num_Doc'])."</numdocfmt>\n";
            echo "\t\t<nombre>".utf8_encode($row['Nom_Clt'])."</nombre>\n";
            echo "\t\t<nompro>".utf8_encode($row['Nom_Pro'])."</nompro>\n";
            echo "\t\t<nomesp>".utf8_encode($row['Nom_Esp'])."</nomesp>\n";
            echo "\t\t<mailctt>".$row['Mail_Ctt']."</mailctt>\n";
            echo "\t\t<groper>".$row['Gro_Per']."</groper>\n";
            echo "\t\t<tipper>".$row['Cod_TipPer']."</tipper>\n";
            echo "\t</filter>\n";
        }
        break;

    case "getper":
        $sp = mssql_query("vm_per_s $param", $db);
        if (($row = mssql_fetch_array($sp))) {
            echo "\t<filter>\n";
            echo "\t\t<numdocfmt>".formatearRut($row['Num_Doc'])."</numdocfmt>\n";
            echo "\t\t<nombre>".utf8_encode($row['Nom_Clt'])."</nombre>\n";
            echo "\t\t<nompro>".utf8_encode($row['Nom_Pro'])."</nompro>\n";
            echo "\t\t<nomesp>".utf8_encode($row['Nom_Esp'])."</nomesp>\n";
            echo "\t\t<sexo>".$row['Sex']."</sexo>\n";
            echo "\t\t<groper>".$row['Gro_Per']."</groper>\n";
            echo "\t\t<tipper>".$row['Cod_TipPer']."</tipper>\n";
            echo "\t</filter>\n";
        }
        break;

   case "cnamsj":
        /* Consultas realizadas */
        $tot_cnaclt = 0;
        $tot_sinrespemp = 0;
        $Cod_Per = ok($_POST['param_codper']);
        $result = mssql_query("vm_tot_cnares $param");
        if (($row = mssql_fetch_array($result))) {
            $tot_cnaclt    = $row["tot_cna"];
            //$bOkRespuesta = ($tot_sinrespemp == 0) ? true : false;
        }

        $result = mssql_query("vm_tot_cnasinres $param");
        if (($row = mssql_fetch_array($result))) {
            $tot_cnasinres = $row["tot_cna"];
            //$tot_sinresemp = $row["tot_sinres"];
            //$tot_sinresclt = $row["tot_sinresclt"];
            //$bOkRespuesta = ($tot_sinrespemp == 0) ? true : false;
        }
        
        /* Consultas realizadas por Vestmed al Usuario */
        //$tot_cnaemp = 0;
        //$tot_sinresclt = 0;
        //$result = mssql_query("vm_totcna_totres $param, 0");
        //if (($row = mssql_fetch_array($result))) {
        //    $tot_cnaemp = $row["tot_cna"];
        //    $tot_sinresclt = $row["tot_sinres"];
        //}

        echo "\t<filter>\n";
        echo "\t\t<cnaclt>".$tot_cnaclt."</cnaclt>\n";
        echo "\t\t<sinresemp>".$tot_cnasinres."</sinresemp>\n";
        //echo "\t\t<cnaemp>".$tot_cnaemp."</cnaemp>\n";
        echo "\t\t<sinresclt>".$tot_cnasinres."</sinresclt>\n";
        echo "\t</filter>\n";
        break;
		
    case "odc":
        $cod_clt  = ok($_POST['param_clt']);
        $sze_page = intval(ok($_POST['param_sze']));
        $pagina   = intval(ok($_POST['param_pag']));

        $desde    = ($pagina-1)*$sze_page + 1;
        $hasta    = $desde + $sze_page - 1;

        $sp = mssql_query("vm_s_cot_tracking $cod_clt, NULL, $desde, $hasta", $db);
        
        while($row = mssql_fetch_array($sp))
        {
            echo "\t<filter>\n";
            echo "\t\t<codcot>".$row['Cod_Cot']."</codcot>\n";
            echo "\t\t<numcot>".$row['Num_Cot']."</numcot>\n";
            echo "\t\t<codnvt>".$row['Cod_Odc']."</codnvt>\n";
            echo "\t\t<fecnvt>".fechafmt($row['Fec_Nvt'])."</fecnvt>\n";
            echo "\t\t<arcadj>".$row['Arc_Adj']."</arcadj>\n";
            echo "\t\t<numtrnbco>".$row['Num_TrnBco']."</numtrnbco>\n";
            echo "\t\t<totcna>".$row['Tot_Cna']."</totcna>\n";
            echo "\t\t<totsinres>".$row['Tot_SinRes']."</totsinres>\n";
            echo "\t</filter>\n";
        }
        break;
    case "popupmsg":
	$cod_cot = intval(ok($_POST['param_cot']));
	$qty_msj    = 0;
        $qty_sinlec = 0;
        $result = mssql_query("vm_s_msj_cot $cod_cot");
        if (($row = mssql_fetch_array($result))) {
            $qty_msj    = $row['Qty'];
            $qty_sinlec = $row['QtySinLec'];
            $qty_sinleccot = $row['QtySinLecCot'];
        }
        echo "\t<filter>\n";
        echo "\t\t<qty_msj>".$qty_msj."</qty_msj>\n";
        echo "\t\t<qty_sinlec>".$qty_sinlec."</qty_sinlec>\n";
        echo "\t\t<qty_sinleccot>".$qty_sinleccot."</qty_sinleccot>\n";
        echo "\t\t<cod_cot>".$cod_cot."</cod_cot>\n";
        echo "\t</filter>\n";
        break;
        
    case "formapago":
        $cod_clt = ok($_POST['param_clt']);
        $cod_perfct = ok($_POST['param_perfct']);
		$tip_docsii = intval(ok($_POST['param_docsii']));
        echo "\t<filter>\n";

        if ($tip_docsii == 2) {
                $result = mssql_query("vm_s_rutfct $cod_clt, $cod_perfct");
                if (($row = mssql_fetch_array($result))) {
                        $Num_DocFct = $row['Num_Doc'];
                        $Nom_CltFct = utf8_encode($row['Nom_Clt']);
                        $Dir_Fct = utf8_encode($row['Dir_Fct']);
                }
        }
        echo "\t\t<Num_DocFct>".$Num_DocFct."</Num_DocFct>\n";
        echo "\t\t<Nom_CltFct>".$Nom_CltFct."</Nom_CltFct>\n";
        echo "\t\t<Dir_Fct>".$Dir_Fct."</Dir_Fct>\n";
        echo "\t\t<tip_docsii>".$tip_docsii."</tip_docsii>\n";
        echo "\t</filter>\n";
        break;
        
    case "pago":
        $IVA = 0.0;
        $result = mssql_query("vm_getfolio_s 'IVA'",$db);
        if (($row = mssql_fetch_array($result))) $IVA = $row['Tbl_fol'] / 10000.0;

        $Cod_Cot= intval(ok($_POST['param_filter']));
        $mto_odc= intval(ok($_POST['param_mtoodc'])); 
        $val_dsp= intval(ok($_POST['param_valdsp'])); 
        $result = mssql_query("sp_s_balance_pgoodc $Cod_Cot",$db);
        echo "\t<filter>\n";
        if (($row = mssql_fetch_array($result))) {
                //$mto_odc+=($mto_odc * $IVA);
                $tot_pagado = $row['pagado'];
                $tot_x_pagar = $row['por_pagar'];
                $balance = $row['balance'];

                echo "\t\t<mto_odc>".number_format($mto_odc,0,',','.')."</mto_odc>\n";
                echo "\t\t<tot_pagado>".number_format($tot_pagado,0,',','.')."</tot_pagado>\n";
                echo "\t\t<tot_x_pagar>".number_format($tot_x_pagar,0,',','.')."</tot_x_pagar>\n";
                echo "\t\t<balancefmt>".number_format($balance,0,',','.')."</balancefmt>\n";
                echo "\t\t<balance>".$balance."</balance>\n";
        }
        echo "\t</filter>\n";
        break;

    case "dirfct":
        $cod_trn = intval(ok($_POST['param_filter']));
        $cod_clt = intval(ok($_POST['param_clt']));
        $cod_cot = intval(ok($_POST['param_cot']));
		
        $IVA = 0.0;
        $result = mssql_query("vm_getfolio_s 'IVA'",$db);
        if (($row = mssql_fetch_array($result))) $IVA = $row['Tbl_fol'] / 10000.0;
		
        echo "\t<filter>\n";
        echo "\t\t<codtrn>".$cod_trn."</codtrn>\n";
        $result = mssql_query("vm_s_cothdr $cod_cot",$db);
        if (($row = mssql_fetch_array($result))) {
            $dir_suc = utf8_encode($row['Dir_Suc']);
            $cod_suc = $row['Cod_Suc'];
            $cod_crr = $row['Cod_Crr'];
            $cod_svccrr = $row['Cod_SvcCrr'];
            $cod_sucdsp = $row['Cod_SucDsp'];
            $dir_sucdsp = utf8_encode($row['Dir_SucDsp']);
            $val_dsp = $row['Val_Dsp'] + ($row['Val_Dsp'] * $IVA);
            //$val_dsp = $row['Val_Dsp'];
            $cod_odc = $row['Cod_Odc'];
            echo "\t\t<isdsp>".$row['is_dsp']."</isdsp>\n";
            echo "\t\t<arcadj>".$row['ArcFis_Adj']."</arcadj>\n";
			echo "\t\t<val_dsp>".$val_dsp."</val_dsp>\n";
            $result = mssql_query("vm_suc_s $cod_clt, $cod_suc", $db);
            if (($row = mssql_fetch_array($result))) {
                echo "\t\t<nomsuc>".utf8_encode($row['Nom_Suc'])."</nomsuc>\n";
                echo "\t\t<dirsuc>".$dir_suc."</dirsuc>\n";
                echo "\t\t<nomcmn>".utf8_encode($row['Nom_Cmn'])."</nomcmn>\n";
                echo "\t\t<nomcdd>".utf8_encode($row['Nom_Cdd'])."</nomcdd>\n";
            }

            $result = mssql_query("vm_suc_s $cod_clt, $cod_sucdsp", $db);
            if (($row = mssql_fetch_array($result))) {
                echo "\t\t<nomsucdsp>".utf8_encode($row['Nom_Suc'])."</nomsucdsp>\n";
                echo "\t\t<dirsucdsp>".$dir_sucdsp."</dirsucdsp>\n";
                echo "\t\t<nomcmndsp>".utf8_encode($row['Nom_Cmn'])."</nomcmndsp>\n";
                echo "\t\t<nomcdddsp>".utf8_encode($row['Nom_Cdd'])."</nomcdddsp>\n";
            }

            $result = mssql_query("vm_CrrCmb $cod_crr", $db);
            if (($row = mssql_fetch_array($result))) {
                echo "\t\t<nomcrr>".utf8_encode($row['Des_Crr'])."</nomcrr>\n";
            }

            $result = mssql_query("vm_SvcCrrCmb $cod_crr, $cod_svccrr", $db);
            if (($row = mssql_fetch_array($result))) {
                echo "\t\t<nomsvccrr>".utf8_encode($row['Des_SvcCrr'])."</nomsvccrr>\n";
            }
            
            $result = mssql_query("vm_mtoodc $cod_odc",$db);
            if (($row = mssql_fetch_array($result))) {
                $mto_odc = $row['Mto_Nvt'] + $row['Prc_Dsp'];
                echo "\t\t<mtoodc>".number_format($mto_odc,0,',','.')."</mtoodc>\n";
            }
            
        }
        echo "\t</filter>\n";
        break;
        
   case "getdsp":
        $IVA = 0.0;
        $result = mssql_query("vm_getfolio_s 'IVA'",$db);
        if (($row = mssql_fetch_array($result))) $IVA = $row['Tbl_fol'] / 10000.0;
        $factor = 1.0 + $IVA;

        $result = mssql_query("vm_prcsvc",$db);
        while (($row = mssql_fetch_array($result))) {
            echo "\t<filter>\n";
            echo "\t\t<codcrr>".$row['Cod_Crr']."</codcrr>\n";
            echo "\t\t<codsvc>".$row['Cod_SvcCrr']."</codsvc>\n";
            echo "\t\t<codrgn>".$row['Cod_Rgn']."</codrgn>\n";
            echo "\t\t<descrr>".utf8_encode($row['Des_Crr'])."</descrr>\n";
            echo "\t\t<dessvc>".utf8_encode($row['Des_SvcCrr'])."</dessvc>\n";
            echo "\t\t<nomrgn>".utf8_encode($row['Nom_Rgn'])."</nomrgn>\n";
            echo "\t\t<tramo1>".number_format($row['tramo1']*$factor,0,',','.')."</tramo1>\n";
            echo "\t\t<tramo2>".number_format($row['tramo2']*$factor,0,',','.')."</tramo2>\n";
            echo "\t\t<tramo3>".number_format($row['tramo3']*$factor,0,',','.')."</tramo3>\n";
            echo "\t\t<tramo4>".number_format($row['tramo4']*$factor,0,',','.')."</tramo4>\n";
            echo "\t\t<tramo5>".number_format($row['tramo5']*$factor,0,',','.')."</tramo5>\n";
            echo "\t\t<adicional>".number_format($row['adicional']*$factor,0,',','.')."</adicional>\n";
            echo "\t</filter>\n";            
        }
        break;
        
    case "flglec":
        $fecha = $_POST['param_fec'];
        $folio = $_POST['param_fol'];
        $secue = $_POST['param_sec'];
        $sp = mssql_query("vm_flag_leido '$fecha', $folio, '$secue'",$db);
        spitXml("0","OK");
        break;
        
}
echo "</response>";

function spitXml($code,$value)
{
    $code = str_replace("\"",'',$code);
    $code = str_replace("\'",'',$code);
    $code = str_replace("&",'&amp;',$code);
    $value = str_replace("\"",'',$value);
    $value = str_replace("\'",'',$value);
    $value = str_replace("&",'&amp;',$value);
    echo "\t<filter>\n";
    echo "\t\t<code>".$code."</code>\n";
    echo "\t\t<value>".$value."</value>\n";
    echo "\t</filter>\n";
}
?>