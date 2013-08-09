<?php
//Obtengo los datos de conexion de la base de datos
ini_set('display_errors', '0');
session_start();

if (!isset($_SESSION['usuario'])) {
    if (!isset($_POST["usuario"])) header("Location: ../index.php");
    $_SESSION['usuario'] = $_POST["usuario"];     
}
$UsrId = (isset($_SESSION['usuario'])) ? $_SESSION['usuario'] : "";

include("global_cot.php");

$OkOpc = false;
$sp = mssql_query("vm_seg_usr_opcmodweb '$UsrId'",$db);
while (($row = mssql_fetch_array($sp))) 
    if ($row["Id_Mod"] == 1 && $row["ID_Opc"] == 1 && strtoupper($row['CodUsr']) == strtoupper($UsrId)) {
        $OkOpc = true;
        break;
    }
mssql_free_result($sp);

if (!$OkOpc) {
    header ("Location:../index.php");
    exit(0);
}

$Cod_Cot = (isset($_GET['cot'])) ? ok($_GET['cot']) : 0;
$Rut_Per = (isset($_GET['rut'])) ? ok($_GET['rut']) : "";
if (isset($_GET['opc'])) $_SESSION['opcion'] = $_GET['opc'];

$today 		= date("d/m/Y");
$Est_Est   	= "1";
$Est_Des 	= "Pendiente";
$Val_Des 	= 0;
$dir_suc 	= "NO TIENE";
$cod_crr    = 0;
$cod_svccrr = 0;
$cod_tipsvc = -1;
$cod_sucdsp = 0;
$cod_cmndsp = 0;
$cod_cdddsp = 0;
$val_dsp    = 0;
$dir_sucdsp = "";
$cod_pre    = 1;
$Cod_Iva    = 1;

$result = mssql_query("vm_getfol_cot $Cod_Cot",$db);
if (($row = mssql_fetch_array($result))) {
    $Fec_Cre = date("Ymd", strtotime($row['FecCre']));
    $FolMsg  = $row['FolMsg'];
}

$result = mssql_query("select dateadd(dd,15,getdate()) Fec_Ini",$db);
if (($row = mssql_fetch_array($result))) $Fec_Cie = date("d/m/Y", strtotime($row['Fec_Ini']));

if ($Cod_Cot > 0) {
    $result = mssql_query("vm_s_cothdr $Cod_Cot",$db);
    if (($row = mssql_fetch_array($result))) {
        $fec_cot   = $row['Fec_Cot'];
        $num_cot   = $row['Num_Cot'];
        $cod_clt   = $row['Cod_Clt'];
        $cod_tipper   = $row['Cod_TipPer'];
        if ($cod_tipper == 1)
            $nom_clt = utf8_encode (trim($row['Pat_Per']." ".$row['Mat_Per']).", ".trim($row['Nom_Per']));
        else
            $nom_clt = utf8_encode (trim($row['RznSoc_Per']));
        $num_doc    = $row['Num_Doc'];
        $cod_suc    = $row['Cod_Suc'];
        $dir_suc    = $row['Dir_Suc'];
        $cod_cmn    = $row['Cod_Cmn'];
        $cod_cdd    = $row['Cod_Cdd'];
        $cod_rgn    = $row['Cod_Rgn'];
        $cod_per    = $row['Cod_Per'];
        $cod_pre    = $row['Cod_Pre'];
        $obs_cot    = ($row['Obs_Cot'] == "_NONE" ? "" : $row['Obs_Cot']);
        $cod_sucfct = $row['Cod_SucFct'];
        $cot_peso   = $row['Cot_Peso'];
        $cot_esta   = $row['Cot_Estatura'];
        $flg_ter    = $row['Cot_FlgTer'];
        $is_dsp     = $row['is_dsp'];
        if ($is_dsp == 1) {
            $cod_crr   = $row['Cod_Crr'];
            $cod_svccrr = $row['Cod_SvcCrr'];
            $cod_tipsvc = ($row['Cod_TipSvcCrr'] == null ? -1 : $row['Cod_TipSvcCrr']);
            $cod_sucdsp = $row['Cod_SucDsp'];
            $cod_cmndsp = $row['Cod_CmnDsp'];
            $cod_cdddsp = $row['Cod_CddDsp'];
            $dir_dsp    = utf8_encode($row['Dir_SucDsp']);
            //$val_dsp    = $row['Val_Dsp'];
            $kilos      = $row['Val_PsoMax'];            
        }

        $Cod_Iva   = 2;
        $Val_Usd   = "";
        $Cod_Cri   = 2;
        $Fec_Cie   = "";
        $Val_Pro   = "";
        $Obs_Res   = "";
        $Val_Des   = 0;
        $fon_ctt   = "";
        $mail_ctt  = "";
        $bXis_Resp = false;
        $xisctt    = false;

        $result = mssql_query("vm_suc_s $cod_clt, $cod_sucfct",$db);
        if (($row = mssql_fetch_array($result))) {
            $cod_cddfct = $row['Cod_Cdd'];
            $cod_cmnfct = $row['Cod_Cmn'];
            $dir_sucfct = utf8_encode($row['Dir_Suc']);
            $result = mssql_query("vm_ctt_s $cod_clt, $cod_sucfct, $cod_per",$db);
            if (($row = mssql_fetch_array($result))) {
                $fon_ctt    = $row['Fon_Ctt'];
                $mail_ctt   = $row['Mail_Ctt'];
                $xisctt     = true;
            }
        }

        if ($is_dsp == 1) {
            $result = mssql_query("vm_suc_s $cod_clt, $cod_sucdsp",$db);
            if (($row = mssql_fetch_array($result))) 
            {
                $tip_cmndsp = $row['Tip_Cmn'];
                $codrgn = $row['Cod_Rgn'];
            }
            
            //$sp = mssql_query("vm_SvcCrr_Prc_s ".$cod_crr.",".$cod_svccrr.",".$codrgn.",".$kilos, $db);
            //if (($row = mssql_fetch_array($result))) $val_dsp = $row['Prc_Dsp'];          
        }

        $result = mssql_query("vm_s_rescot $Cod_Cot, $cod_clt, $cod_per",$db);
        if (($row = mssql_fetch_array($result))) {
            $Cod_Iva = $row['Cod_Iva'];
            $Val_Usd = $row['Val_Usd'];
            $Cod_Cri = $row['Cod_Cri'];
            $Fec_Cie = date("d/m/Y", strtotime($row['Fec_Cie']));
            $Val_Pro = $row['Val_Pro'];
            $Obs_Res = $row['Obs_Res'];
            $Val_Des = ($row['Val_Des'] == null ? 0 : $row['Val_Des']);
            $Est_Res = $row['Est_Res'];

            $bXis_Resp = true;
        }
    }
}
else if ($Rut_Per != "") {
	$doc_id = 1;
	//$query = "vm_s_per_tipdoc ".$doc_id.", '".$Rut_Per."'";
	$result = mssql_query ("vm_s_per_tipdoc ".$doc_id.", '".$Rut_Per."'", $db)	or die ("No se pudo leer datos del Cliente");
	if (($row = mssql_fetch_array($result))) {
		$cod_per = $row['Cod_Per'];
		$num_doc = $row['Num_Doc'];
		$cod_tipper = $row['Cod_TipPer'];
		if ($cod_tipper == "1") 
                    $nom_clt = utf8_encode (trim($row['Pat_Per'])." ".trim($row['Mat_Per']).", ".trim($row['Nom_Per']));
		else
                    $nom_clt = utf8_encode (trim($row['RznSoc_Per']));
		$cod_clt = $row["Cod_Clt"];
		if ($cod_clt != "") { // Si es cliente
			$bPrimero = true;
			$cod_suc = null;
			//$query = "vm_suc_s ".$cod_clt;
			$result = mssql_query ("vm_suc_s ".$cod_clt, $db)
							or die ("No se pudo leer datos de la Sucursal (".$cod_clt.")");
			while (($row = mssql_fetch_array($result))) {
				if (trim($row['Nom_Suc']) != 'MIGRACION') {
					$cod_sucfct  = $row['Cod_Suc'];
					$fon_ctt     = $row['Fon_Suc'];
					$dir_sucfct  = utf8_encode ($row["Dir_Suc"]);
					$cod_cmnfct  = $row["Cod_Cmn"];
					$cod_cddfct  = $row["Cod_Cdd"];
					if (!isset($_GET['suc'])) {
						mssql_free_result($result); 
						break;
					}
					else if ($cod_sucfct == ok($_GET['suc'])) {
						mssql_free_result($result); 
						break;
					}
				}
			}
			if ($cod_sucfct != null) {
				$cod_ctt = isset($_GET['ctt']) ? intval(ok($_GET['ctt'])) : null;
				//$query = "vm_ctt_s ".$cod_clt.",".$cod_sucfct;
				$result = mssql_query ("vm_ctt_s ".$cod_clt.",".$cod_sucfct, $db)
								or die ("No se pudo leer datos del Contacto (".$cod_clt.",".$cod_sucfct.")");
				while (($row = mssql_fetch_array($result))) {
					$fon_ctt  = $row["Fon_Ctt"];
					$mail_ctt = $row["Mail_Ctt"];
					if (!isset($_GET['ctt'])) {
						mssql_free_result($result); 
						break;
					}
					else if ($row['Cod_Per'] == ok($_GET['ctt'])) {
						$cod_per = $row['Cod_Per'];
						mssql_free_result($result); 
						break;
					}
				}
			}
		}
	}
}

$IVA = 0.0;
$result = mssql_query("vm_getfolio_s 'IVA'",$db);
if (($row = mssql_fetch_array($result))) $IVA = $row['Tbl_fol'] / 10000.0;

/* Consultas realizadas por el Usuario a Vestmed */
$tot_cnaclt = 0;
$result = mssql_query("vm_totcna_totres $Cod_Cot, $cod_per");
if (($row = mssql_fetch_array($result))) {
    $tot_cnaclt    = $row["tot_cna"];
    $tot_sinresemp = $row["tot_sinres"];
    $bOkRespuesta = ($tot_sinrespemp == 0) ? true : false;
}
	
/* Consultas realizadas por Vestmed al Usuario */
$tot_cnaemp = 0;
$result = mssql_query("vm_totcna_totres $Cod_Cot, 0");
if (($row = mssql_fetch_array($result))) {
    $tot_cnaemp = $row["tot_cna"];
    $tot_sinresclt = $row["tot_sinres"];
}
	
/* Nuevo */
$tot_cnacot = 0;
$result = mssql_query("vm_tot_cnares $Cod_Cot");
if (($row = mssql_fetch_array($result))) $tot_cnacot = $row["tot_cna"];
	
$tot_cnasinres = 0;
$result = mssql_query("vm_tot_cnasinres $Cod_Cot");
if (($row = mssql_fetch_array($result))) $tot_cnasinres = $row["tot_cna"];



?>
<!DOCTYPE html PUBLIC "-//W3C//Dtd XHTML 1.0 transitional//EN" "http://www.w3.org/tr/xhtml1/Dtd/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Catalogo - Vestmed Vestuario M&eacute;dico</title>
<link href="../css/layout.css" type="text/css" rel="stylesheet" />
<script type="text/javascript" src="../js/mootools-1.2.1-core-yc.js"></script>
<script type="text/javascript" src="../js/mootools-1.2-more.js"></script>
<script type="text/javascript" src="../dropdown/js/dropdown.js"> </script>
<link rel="stylesheet" type="text/css" media="screen" href="../dropdown/css/uvumi-dropdown.css" />
<!--[if IE]>
<link rel="stylesheet" type="text/css" media="screen" href="dropdown/css/uvumi-dropdown-ie.css" />
<![endif]-->
<script type="text/javascript" src="../Include/ValidarDataInput.js"></script>
<script type="text/javascript" src="../Include/SoloNumeros.js"></script>
<script type="text/javascript" src="../Include/validarRut.js"></script>
<LINK href="../Include/estilos.css" type="text/css" rel="stylesheet" />
<link rel="stylesheet" type="text/css" media="all" href="../Include/calendar-green.css" title="win2k-cold-1" />
<script type="text/javascript" src="../Include/calendar.js"></script>
<script type="text/javascript" src="../Include/calendar-es.js"></script>
<script type="text/javascript" src="../Include/calendar-setup.js"></script>
<script type="text/javascript" src="../js/jquery-1.3.2.min.js"></script>
<script type="text/javascript" src="js/AccionesMenu.js"></script>
<script type="text/javascript">
new UvumiDropdown('dropdown-scliente');
</script>
<script type="text/javascript">
	var $j = jQuery.noConflict();
	var codcdddsp = 0;
	
        $j(document).ready
	(
		//$j(":input:first").focus();
		
		function()
		{
	        $j("form#searchPro").submit(function(){
                        $j.post("../ajax-search-svc.php",{
                                search_type: "svc",
                                param_filter: $j("#pro").val()
                        }, function(xml) {
                                listLinEsp(xml);
                        });return false;
		    });
		
	        $j("form#searchEsp").submit(function(){
                        $j.post("../ajax-search-svc.php",{
                                search_type: "peso",
                                param_filter: $j("#pro").val(),
                                param_codsvc: $j("#esp").val(),
                                param_codcdd: $j("#cmbCddDsp").val(),
                                param_peso: $j("#peso").val()
                        }, function(xml) {
                                listLinSvc(xml);
                        });return false;
		    });
			
	        $j("form#searchCmn").submit(function(){
                        $j.post("../ajax-search-cdd.php",{
                                search_type: "cdd",
                                param_filter: $j("#cmn").val()
                        }, function(xml) {
                                listLinCdd(xml,"");
                        });return false;
		    });
			
	        $j("form#searchCmnDsp").submit(function(){
                        $j.post("../ajax-search-cdd.php",{
                                search_type: "cdd",
                                param_filter: $j("#cmbCmnDsp").val()
                        }, function(xml) {
                                listLinCddDsp(xml,codcdddsp);
                        });return false;
		    });
			
	        $j("form#searchPer").submit(function(){
                        $j.post("../ajax-search-per.php",{
                                search_type: "clt",
                                param_filter: $j("#dfRut").val()
                        }, function(xml) {
                                listLinPer(xml);
                        });return false;
		    });
			
	        $j("form#searchSuc").submit(function(){
                        $j.post("../ajax-search-per.php",{
                                search_type: "suc",
                                param_cliente: $j("#cod_clt").val(),
                                param_filter: $j("#suc").val()
                        }, function(xml) {
                                listLinSuc(xml);
                        });return false;
		    });
			
	        $j("form#searchSucDsp").submit(function(){
                        $j.post("../ajax-search-per.php",{
                                search_type: "suc",
                                param_cliente: $j("#cod_clt").val(),
                                param_filter: $j("#cmbSucDsp").val()
                        }, function(xml) {
                                listLinSucDsp(xml);
                        });return false;
		    });
			
	        $j("form#searchCtt").submit(function(){
                        $j.post("../ajax-search-per.php",{
                                search_type: "ctt",
                                param_cliente: $j("#cod_clt").val(),
                                param_sucursal: $j("#suc").val(),
                                param_filter: $j("#ctt").val()
                        }, function(xml) {
                                listLinCtt(xml);
                        });return false;
		    });
			
                $j("form#searchConsultas").submit(function(){
                        $j.post("../ajax-search.php",{
                                search_type: "cnamsj",
                                param_filter: $j("#cod_cot").val(),
                                param_codper: $j("#cod_per").val()
                        }, function(xml) {
                                RefrescarMensajes(xml);
                        });
                        return false;
                    });

	        //NO SUBMIT TODAVIA $j("form#patternlist-form").submit();
	    }
		
	);
    //TODO: No se esta realizando el post, probablemente debido a que falta el evento submit.
    
    function ActualizarConsultas()
    {
        $j("form#searchConsultas").submit();
    }

    function filterCmn(obj)
    {
        $j("form#searchCmn").submit();
    }
	
    function filterCmnDsp(obj, codcdd)
    {
        f2.cod_cmndsp.value = $j("#cmbCmnDsp").val();
        codcdddsp = codcdd;
        $j("form#searchCmnDsp").submit();
    }
	
    function filterPro(obj)
    {
        f2.cod_crr.value = obj.value;
        $j("#.pro").val(obj.value);
        $j("form#searchPro").submit();
    }
	
    function filterPer(obj)
    {
        if (obj.value != "") {
            if (validarRutCompleto('dfRut')) $j("form#searchPer").submit();
            else {
              alert("Rut invalido. Intente nuevamente");
              $('dfRut').focus();
            }
        }
    }

    function filterSuc(obj)
    {
        if (obj.value != "NewSuc") {
            f2.cod_suc.value = obj.value;
            $j("form#searchSuc").submit();
        }
        else NuevaSuc ('<?php echo $num_doc; ?>');
    }
	
    function filterSucDsp(obj,objfrm)
    {
        $j("#dfDirDsp").val("");
        eval("f2."+objfrm).value = obj.value;
        if (obj.value != "0") {
                $j("form#searchSucDsp").submit();
        }
        else {
                $j('#cmbCmnDsp').removeAttr('disabled');
                $j('#cmbCddDsp').removeAttr('disabled');
        }
    }
	
    function filterCtt(obj)
    {
        if (obj.value != "_NONE") $j("form#searchCtt").submit();
    }
	
    function filterCddDsp(obj) {
            f2.cod_cdddsp.value = obj.value;
            $j("form#searchEsp").submit();
    }

    function RefrescarMensajes(xml)
    {
        var tot_cnaclt=0;
        var tot_sinresemp=0;
        var tot_cnaemp=0;
        var tot_sinresclt=0;
        var total=0;

        $j("filter",xml).each(
            function(id) {
                filter=$j("filter",xml).get(id);
                tot_cnaclt    = parseInt($j("cnaclt",filter).text());
                tot_sinresemp = parseInt($j("sinresemp",filter).text());
                //tot_cnaemp    = parseInt($j("cnaemp",filter).text());
                tot_sinresclt = parseInt($j("sinresclt",filter).text());
            }
	);

        total = tot_cnaclt; // + tot_cnaemp;
        options="<table id=\"tblConsultas\" BORDER=\"0\" CELLSPACING=\"0\" CELLPADDING=\"2\" width=\"100%\" ALIGN=\"center\">\n";
        options+="<tr>\n";
        options+="<td width=\"100\" VALIGN=\"TOP\" class=\"dato10p\">Consultas Realizadas: "+total;
        if (total > 0)
            options+=" (<a href=\"javascript:ver_consultas(<?php echo $Cod_Cot ?>)\">VER</a>)"
        options+="</td>\n";
        options+="</tr>\n";
        options+="<tr>\n";
        options+="<td width=\"100\" VALIGN=\"TOP\" class=\"dato10p\">Consultas Sin Leer: "+tot_sinresemp;
        if (tot_sinresemp > 0)
            options+=" (<a href=\"javascript:ver_consultas(<?php echo $Cod_Cot ?>)\">LEER</a>)"
        options+="</td>\n";
        options+="</tr>\n";
        options+="<tr><td width=\"100\" VALIGN=\"TOP\" class=\"dato10p\">Nueva Consulta: <a href=\"javascript:ver_consultas(<?php echo $Cod_Cot; ?>)\">Aqu&iacute;</a></td></tr>\n";
        options+="</table>\n";

        $j("#tblConsultas").replaceWith(options);
    }

    function listLinEsp(xml)
    {
        options="<select id=\"esp\" name=\"esp\" class=\"textfield\" onchange=\"traspasaToFormCaptura(this,'cod_svccrr')\">\n";
        options+="<option selected value=\"_NONE\">Seleccionar Servicio</option>\n";
        $j("filter",xml).each(
			function(id) {
	            filter=$j("filter",xml).get(id);
	            options+= "<option value=\""+$j("code",filter).text()+"\">"+$j("value",filter).text()+"</option>\n";
	        }
		);
        options+="</select>";
        $j("#esp").replaceWith(options);
    }
	
    function listLinCdd(xml,valini)
    {
		if (valini == "") {
			options="<select id=\"cdd\" name=\"cdd\" class=\"textfield\" onChange=\"llenarCdd(this)\">\n";
			options+="<option selected value=\"_NONE\">Seleccione una Ciudad</option>\n";
			$j("filter",xml).each(
				function(id) {
					filter=$j("filter",xml).get(id);
					options+= "<option value=\""+$j("code",filter).text()+"\">"+$j("value",filter).text()+"</option>\n";
				}
			);
			options+="</select>";
		}
		else 
			options="<input id=\"cdd\" name=\"cdd\" size=\"25\" maxLength=\"25\" class=\"textfield\" value=\"" + valini + "\" ReadOnly />";
			
        $j("#cdd").replaceWith(options);
    }
	
    function listLinCddDsp(xml,valini)
    {
        options="<select id=\"cmbCddDsp\" name=\"cmbCddDsp\" class=\"textfield\" onChange=\"filterCddDsp(this)\">\n";
        options+="<option selected value=\"_NONE\">Seleccione Ciudad</option>\n";
        $j("filter",xml).each(
            function(id) {
                filter=$j("filter",xml).get(id);
                if (valini == $j("code",filter).text()) {
                   options+= "<option value=\""+$j("code",filter).text()+"\" selected>"+$j("value",filter).text()+"</option>\n";
                   f2.cod_cdddsp.value = $j("code",filter).text();
                }
                else
                   options+= "<option value=\""+$j("code",filter).text()+"\">"+$j("value",filter).text()+"</option>\n";
            }
        );
        options+="</select>";
			
        $j("#cmbCddDsp").replaceWith(options);
        if ($j("#cmbSucDsp").val() > 0)
            $j('#cmbCddDsp').attr('disabled','-1');
        else
            $j('#cmbCddDsp').removeAttr('disabled');
        if ($j("#cmbCddDsp").val() != "_NONE") $j("form#searchEsp").submit();

    }

    function listLinPer(xml)
    {
        var	xisper = false;
        var xissuc = false;
		
        $j("filter",xml).each(
			function(id) {
	            filter=$j("filter",xml).get(id);
				//alert($j("code",filter).text() + "=" + $j("value",filter).text());
	            if ($j("code",filter).text() == "rutfmt") $j("#dfRut").val($j("value",filter).text());
	            if ($j("code",filter).text() == "rut")    numdoc = $j("value",filter).text();
	            if ($j("code",filter).text() == "nombre") $j("#dfNomClt").val($j("value",filter).text());
	            if ($j("code",filter).text() == "codclt") $j("#cod_clt").val($j("value",filter).text());
	            if ($j("code",filter).text() == "codper") $j("#cod_per").val($j("value",filter).text());
				xisper = true;
	        }
		);
		
		if (xisper) {
			codsuc = 0;
			options="<select id=\"suc\" name=\"suc\" class=\"textfield\" onChange=\"filterSuc(this)\">\n";
			$j("sucursal",xml).each(
				function(id) {
					sucursal=$j("sucursal",xml).get(id);
					options+= "<option value=\""+$j("code",sucursal).text()+"\">"+$j("value",sucursal).text()+"</option>\n";
					if (codsuc == 0) codsuc = $j("code",sucursal).text();
					xissuc = true;
				}
			);
			if (!xissuc) options+= "<option selected value=\"_NONE\">Seleccione una Sucursal</option>\n";
			options+= "<option value=\"NewSuc\">Nueva Sucursal</option>\n";
			options+= "</select>";
			$j("#suc").replaceWith(options);
			if (SeleccionarEnCombo(codsuc,"suc")) {
				filterSuc($j("#suc"));
				f2.cod_suc.value = codsuc;
			}
			
			options="<select id=\"cmbSucDsp\" name=\"cmbSucDsp\" class=\"textfield\" onchange=\"filterSucDsp(this,'cod_sucdsp')\"<?php if ($is_dsp == 0) echo " DISABLED" ?>>"
			options+="<option value =\"0\">Oficina Carrier</option>";
			$j("sucursal",xml).each(
				function(id) {
					sucursal=$j("sucursal",xml).get(id);
					options+= "<option value=\""+$j("code",sucursal).text()+"\">"+$j("value",sucursal).text()+"</option>\n";
				}
			);
			options+= "<option value=\"NewSuc\">Nueva Sucursal</option>\n";
			options+= "</select>";
			$j("#cmbSucDsp").replaceWith(options);
			
			options="<input type=\"button\" id=\"Nuevo\" name=\"Nuevo\" value=\"Editar\" class=\"button2\" onclick=\"EditarCliente('";
			options+=numdoc;
			options+="')\">";
			$j("#Nuevo").replaceWith(options);
			
		}
		else {
                    $j("#dfNomClt").val("");
                    $j("#cod_clt").val("");
                    $j("#cod_per").val("");
                    $j("#dfDir").val("");
                    $j("#dfemail").val("");
                    $j("#dfFono").val("");
                    $j("#dfCmn").val("");
                    $j("#dfCdd").val("");
			
                    options="<select id=\"suc\" name=\"suc\" class=\"textfield\" onChange=\"filterSuc(this)\">\n";
                    options+="<option selected value=\"_NONE\">Seleccione una Sucursal</option>\n";
                    options+="</select>";
                    $j("#suc").replaceWith(options);

                    options="<select id=\"ctt\" name=\"ctt\" class=\"textfield\" onChange=\"filterCtt(this)\">\n";
                    options+="<option selected value=\"_NONE\">Seleccione un Contacto</option>\n";
                    options+="</select>";
                    $j("#ctt").replaceWith(options);

                    //options="<select id=\"cdd\" name=\"cdd\" class=\"textfield\" onChange=\"llenarCdd(this)\">\n";
                    //options+="<option selected value=\"_NONE\">Seleccione una Ciudad</option>\n";
                    //options+="</select>";
                    //$j("#cdd").replaceWith(options);

                    //SeleccionarEnCombo("_NONE",'cmn');

                    options="<input type=\"button\" id=\"Nuevo\" name=\"Nuevo\" value=\"Nuevo\" class=\"button2\" onclick=\"NuevoCliente()\">";
                    $j("#Nuevo").replaceWith(options);

                    if (confirm("El rut ingresado NO existe.\nDesea crearlo en estos momentos ?"))
                            popwindow("registrarse.php?clt="+$j("#dfRut").val()+"&xis=0",600)
		}
    }

    function listLinSuc(xml)
    {
		var xisctt = false;
		var numdoc = "";
		
        $j("filter",xml).each(
                function(id) {
	            filter=$j("filter",xml).get(id);
                    //alert($j("code",filter).text() + "=" + $j("value",filter).text());
	            if ($j("code",filter).text() == "dirsuc") $j("#dfDir").val($j("value",filter).text());
	            if ($j("code",filter).text() == "fonsuc") $j("#dfFono").val($j("value",filter).text());
	            if ($j("code",filter).text() == "nomcmn") $j("#dfCmn").val($j("value",filter).text());
	            if ($j("code",filter).text() == "nomcdd") $j("#dfCdd").val($j("value",filter).text());
	            if ($j("code",filter).text() == "numdoc") numdoc = $j("value",filter).text();
	        }
		);
		if (numdoc != "") NuevaSuc (numdoc);
		else {
			codctt = 0;
			options="<select id=\"ctt\" name=\"ctt\" class=\"textfield\" onChange=\"filterCtt(this)\">\n";
			//options+="<option selected value=\"_NONE\">Seleccione un Contacto</option>\n";
			$j("contacto",xml).each(
				function(id) {
					contacto=$j("contacto",xml).get(id);
					options+= "<option value=\""+$j("code",contacto).text()+"\">"+$j("value",contacto).text()+"</option>\n";
					if (codctt == 0) codctt = $j("code",contacto).text();
					xisctt = true;
				}
			);
			if (!xisctt) options+= "<option selected value=\"_NONE\">Seleccione un Contacto</option>\n";
			options+= "<option value=\"NewCtt\">Nuevo Contacto</option>\n";
			options+= "</select>";
			$j("#ctt").replaceWith(options);
			if (SeleccionarEnCombo(codctt,"ctt")) filterCtt($j("#ctt"));
		}
    }

    function listLinSucDsp(xml)
    {
        var numdoc = "";
        $j("filter",xml).each(
		function(id) {
	            filter=$j("filter",xml).get(id);
				//alert($j("code",filter).text() + "=" + $j("value",filter).text());
	            if ($j("code",filter).text() == "dirsuc") {
					$j("#dfDirDsp").val($j("value",filter).text());
					f2.dir_dsp.value = $j("value",filter).text();
				}
	            else if ($j("code",filter).text() == "codcmn") {
					codcmn = parseInt($j("value",filter).text());
					f2.cod_cmndsp.value = $j("value",filter).text();
				}
	            else if ($j("code",filter).text() == "codcdd") {
					codcdd = parseInt($j("value",filter).text());
					f2.cod_cdddsp.value = $j("value",filter).text();
				}
	            else if ($j("code",filter).text() == "numdoc") numdoc = $j("value",filter).text();
				else if ($j("code",filter).text() == "tipcmn") {
					tipcmn = $j("value",filter).text();
					if (tipcmn == 0) $j("#tipo_despacho").hide("slow");
					else $j("#tipo_despacho").show("slow");
				}
	        }
		);
		if (numdoc != "") NuevaSuc (numdoc);
		else if (codcmn > 0) {
			if (SeleccionarEnCombo(codcmn,"cmbCmnDsp")) filterCmnDsp($j("form#cmbCmnDsp"), codcdd);
			if ($j("#cmbSucDsp").val() > 0)
				$j('#cmbCmnDsp').attr('disabled','-1');
			else 
				$j('#cmbCmnDsp').removeAttr('disabled');
		}
    }
	
    function listLinCtt(xml)
    {
        var codsuc = 0;
        var numdoc = "";
        //alert("listLinCtt");
        $j("filter",xml).each(
            function(id) {
                filter=$j("filter",xml).get(id);
                if ($j("code",filter).text() == "mail") $j("#dfemail").val($j("value",filter).text());
                if ($j("code",filter).text() == "fonctt") $j("#dfFono").val($j("value",filter).text());
                if ($j("code",filter).text() == "numdoc") numdoc = $j("value",filter).text();
                if ($j("code",filter).text() == "codsuc") codsuc = $j("value",filter).text();
            }
            );
            if (numdoc != "") NuevoCtt (numdoc,codsuc);
    }
	
    function listLinSvc(xml)
    {
        //alert("listLinSvc");
        $j("filter",xml).each(
        function(id) {
            filter=$j("filter",xml).get(id);
            //alert($j("code",filter).text()+"="+$j("value",filter).text());
            if ($j("code",filter).text() == "costo") {
                f2.val_dsp.value = $j("value",filter).text().replace(".", "");
                valordsp = parseFloat(f2.val_dsp.value);
                valordsp += valordsp * <?php echo $IVA ?>;
                $j("#dfValDsp").val("$ "+FormatNumero(Math.round(valordsp).toString()));
                f2.val_dsp.value = valordsp;
                if ($j("value",filter).text() == "0") {
                    //$j("#dfValDsp").removeAttr('readonly');
                    //$j("#dfValDsp").val("");
                    alert ("Favor indique otro tipo servicio en el despacho pues el servicio seleccionado\nno est\u00e1 disponible en la regi\u00f3n");
                }
                else $j("#dfValDsp").attr('readonly');
            }
        });
    }
	
    function SeleccionarEnCombo(codigo,obj) {
        var i=0;
        var combo = document.getElementById(obj);
        var cantidad = combo.length;

        for (i = 0; i < cantidad; i++) {
            if (combo[i].value == codigo) {
                combo[i].selected = true;
                return true;
            }
        }
            return false;
    }
	
    function llenarCampo(obj) {
        var campo;

        campo=obj.name.substring(0,obj.name.length-2);
        eval("f2."+campo).value = obj.value;
    }

    function FormatNumero (cNumero) {
        var cNumeroFmt;
        var i = 0;
        var j = 0;

        cNumeroFmt = "";
        for (i = cNumero.length-1; i >= 0; --i) {
           cNumeroFmt = cNumero.substring(i,i+1) + cNumeroFmt;
           if (++j == 3) {
               cNumeroFmt = "." + cNumeroFmt;
               j = 0;
           }
        }
        if (cNumeroFmt.substring(0,1) == ".")
        cNumeroFmt = cNumeroFmt.substring (1,cNumeroFmt.length);

        return cNumeroFmt;
    }

    function calculaprecio(obj,largo) {
            var idcampo;

            pglobal = (f2.dfDescto.value == "" ? 0 : f2.dfDescto.value);
            idcampo = obj.name.substring(largo,obj.name.length);
            porcentaje = eval("f2.dfDcto"+idcampo).value;
            cantidad = eval("f2.dfCtd"+idcampo).value * (1 - eval("f2.dfFlgStk"+idcampo).value);
            descuento = cantidad * eval("f2.dfPrc"+idcampo).value * (porcentaje / 100.0);
            eval("f2.dfNeto"+idcampo).value = cantidad * eval("f2.dfPrc"+idcampo).value - descuento;
            descuento = eval("f2.dfNeto"+idcampo).value * pglobal / 100.0;
            eval("f2.dfNeto"+idcampo).value = parseInt(eval("f2.dfNeto"+idcampo).value - descuento + 0.5);
            eval("f2.dfNeto"+idcampo).value = FormatNumero(eval("f2.dfNeto"+idcampo).value);
            calcularneto();
    }


    function calculartotales() {
        var i;

        for (i=0; i<f2.totprd.value; ++i)
            if (eval("f2.dfPrc"+i).value != "") {
                oDscto = eval("f2.dfDcto"+i);
                calculaprecio(oDscto,6);
            }
        calcularneto();
    }

    function calcularneto() {
            var neto = 0;

            for (i=0; i<f2.totprd.value; ++i)
                    if (eval("f2.dfPrc"+i).value != "") {
                            valor = eval("f2.dfNeto"+i).value.replace(/[.]/gi,'');
                            neto = neto + parseInt(valor);
                    }

            if ($j("#iva").val() == "1") {
                    f2.dfNeto.value = neto;
                    f2.dfIva.value = 0.0;
                    f2.dfTotal.value = parseInt(f2.dfNeto.value) + parseInt(f2.dfIva.value);
            }
            else {
                    f2.dfNeto.value = neto;
                    f2.dfIva.value = parseInt(f2.p_iva.value * neto + 0.5);
                    f2.dfTotal.value = parseInt(f2.dfNeto.value) + parseInt(f2.dfIva.value);
            }
            f2.dfNeto.value = FormatNumero(f2.dfNeto.value);
            f2.dfIva.value = FormatNumero(f2.dfIva.value);
            f2.dfTotal.value = FormatNumero(f2.dfTotal.value);
    }

    function popwindow(ventana,ancho,altura){
       window.open(ventana,"Anexos","toolbar=0,location=0,scrollbars=yes,resizable=1,left=60,top=40,width="+ancho+",height="+altura);
    }

    function ver_preview(cot) {
            popwindow("preview.php?cot="+cot,800,600)
    }

    function ver_consultas(cot) {
            popwindow("consultas.php?cot="+cot,800,600)
    }

    function agregarProductos(cot) {
            <?php if ($Cod_Cot > 0) { ?>
            popwindow("agregar_prd.php?cot="+cot,800,600)
            <?php } else { ?>
            if (f2.cod_per.value == "")
                    alert("Falta identificar al cliente ...");
            else if (confirm("Antes de agregar productos debe guardar la Cotizacion.\nDesea guardarla en estos momentos ?"))
                    f2.submit();
            <?php } ?>
    }

    function agregarDespacho(cot) {
            popwindow("despacho.php?cot="+cot,800,600)
    }

    function modificarProductos(cot,grp,sty,pat) {
            popwindow("agregar_prd.php?cot="+cot+"&idgrp="+grp+"&sty="+sty+"&pat="+pat,700,453)
    }

    function cerrar_cot(cot) {
        if (f2.dfValPro.value == 0) {
            alert("Debe ingresar una probabilidad ...");
            return false;
        }
        if (f2.dfFecCie.value == "") {
            alert("Favor ingrese la Fecha de Cierre ...");
            return false;
        }
        if (f2.dfFlgTer.value == 0) {
            if (f2.dfPesoPer.value == 0) {
                alert("Debe indicar el peso del Cliente ...");
                return false;
            }
            if (f2.dfEstaturaPer.value == 0) {
                alert("Debe indicar la estatura del Cliente ...");
                return false;
            }
        }
        if (f2.is_dsp.value == 1) {
            if (f2.cod_crr.value == 0) {
                alert("Debe indicar el carrier ...");
                return false;
            }
            if (f2.cod_svccrr.value == 0) {
                alert("Debe indicar el Servicio del Carrier ...");
                return false;
            }
            if (f2.cod_cmndsp.value == 0) {
                alert("Debe indicar la comuna del despacho ...");
                return false;
            }
            if (f2.cod_cdddsp.value == 0) {
                alert("Debe indicar la ciudad del despacho ...");
                return false;
            }
            if (f2.val_dsp.value == "0") {
                alert ("Favor indique otro tipo servicio en el despacho pues el servicio seleccionado\nno est\u00e1 disponible en la regi\u00f3n");
                return false;            
            }
            if ($j("#tipo_despacho").is(':visible')) {
                if (f2.dfTipSvrCrr.value == -1) {
                    alert("Debe indicar si el despacho sera al domicilio o a la sucursal del carrier...");
                    return false;
                }                
            }
        }
        if (!bListoParaCerrar) {
            alert("Favor debe ingresar una respuesta a la Observaci\u00f3n \nrealizada por el Cliente...");
            return false;
        }
        f2.action="resp_cot.php?act=C";
        f2.submit();
    }

    function eliminarProductos(cot) {
        for (i=0; i<f2.elements.length; i++)
            if (f2.elements[i].name == "seleccionadof[]")
                if (f2.elements[i].checked)
                    if (confirm("Favor confirmar eliminacion de los productoos seleccionados\nde la cotizacion")) {
                            f2.action="resp_cot.php?act=D";
                            f2.submit();
                            return true;
                    }
            alert("Utilice los checkbox para indicar que productos\ndesea eliminar");
    }

    function CheckCliente(form) {
        if (form.cod_per.value == "") {
                alert("Falta identificar al cliente ...");
                return false;
        }
        if (form.is_dsp.value == 1)
            if (form.val_dsp.value == "0") {
                alert ("Favor indique otro tipo servicio en el despacho pues el servicio seleccionado\nno est\u00e1 disponible en la regi\u00f3n");
                return false;            
            }
        return true;
    }

    function volver() {
            f2.action = "escritorio_cot.php";
            f2.submit();
    }

    function BuscarCliente(contexto) {
            //popwindow("busqueda.php?contexto="+contexto,800,600)
            f1.action = "escritorio_bus.php?opc=clt";
            f1.submit();
    }

    function NuevoCliente() {
            popwindow("registrarse.php",800,600)
    }

    function EditarCliente(numdoc) {
            var cod_suc = 0;

            cod_suc = $j("#suc").val();
            popwindow("editar.php?clt="+numdoc+"&xis=1&suc="+cod_suc,800,600)
    }

    function NuevaSuc(numdoc) {
            popwindow("registrar_suc.php?clt="+numdoc+"&xis=1&acc=newsuc",800,600)
    }

    function NuevoCtt(numdoc,codsuc) {
            popwindow("registrar_ctt.php?clt="+numdoc+"&xis=1&suc="+codsuc+"&acc=newctt&cot=<?php echo $Cod_Cot; ?>",800,600)
    }

    function EditarCtt(numdoc) {
            var codsuc = 0;

            if ($j("#ctt").val() == "NewCtt" || $j("#ctt").val() == "_NONE")
                    alert("Debe seleccionar un contacto");
            else {
                    codsuc = $j("#suc").val();
                    popwindow("registrarse.php?clt="+numdoc+"&xis=1&suc="+codsuc+"&acc=newctt",800,600)
            }

    }

    function recalcular(obj) {
        var i = 0;
        var factor = <?php echo 1.0 + $IVA; ?>;
        var oPrecio;
        //var oDscto;
        //var oCtd;

        f2.dfIVA.value = obj.value;
        for (i = 0; i < $j("#totprd").val(); ++i) {
           if ($j("#dfPrc"+i).val() != "") {
                oPrecio = $j("#dfPrc"+i).val();
                //oDscto = eval("f2.dfDcto"+i).value;
                //oCtd = eval("f2.dfCtd"+i).value;
                if (obj.value == 1)
                    $j("#dfPrc"+i).val(parseInt(oPrecio * factor + 0.5));
                else
                    $j("#dfPrc"+i).val(parseInt(oPrecio / factor + 0.5));
                //calculaprecio(oDscto,6);
            }
        }
        calculartotales();

    }

    function mnuCliente() {
        f1.action = "escritorio_cot.php?opc=clt";
        f1.submit();
    }

    function mnuVestmed() {
        f1.action = "escritorio_cot.php?opc=";
        f1.submit();
    }

    function ver_historial() {
        popwindow("historial_cna.php?cot=<?php echo $Cod_Cot; ?>&clt=<?php echo $cod_clt ?>",500,250)
    }

    function traspasaToFormCaptura(obj,objfrm) {
        eval("f2."+objfrm).value = obj.value;
        if (objfrm == "cod_svccrr") $j("form#searchEsp").submit();
    }

    function HabilitaCarrier(obj,objfrm) {
        var habilitar = true;

        objcrr = document.getElementById("pro");
        objsvc = document.getElementById("esp");
        objsuc = document.getElementById("cmbSucDsp");
        objcmn = document.getElementById("cmbCmnDsp");
        objcdd = document.getElementById("cmbCddDsp");
        objdir = document.getElementById("dfDirDsp");
        objprc = document.getElementById("dfValDsp");
        if (obj.value == 0) habilitar = false;
        else habilitar = true;
        objcrr.disabled = !habilitar;
        objsvc.disabled = !habilitar;
        objsuc.disabled = !habilitar;
        objcmn.disabled = !habilitar;
        objcdd.disabled = !habilitar;
        objdir.disabled = !habilitar;
        objprc.disabled = !habilitar;

        if (habilitar) eval("f2."+objfrm).value = 1;
        else eval("f2."+objfrm).value = 0;
    }

    function EliminarCot () {
        if (confirm("Favor confirmar eliminacion de la cotizacion")) {
            f2.action="resp_cot.php?act=DELCOT";
            f2.submit();
        }
    }

    function RegSinStock(obj,item) {
        var objrel;

        objrel = eval("f2.dfPrc"+item);
        if (obj.checked)
            eval("f2.dfFlgStk"+item).value = "1";
        else
            eval("f2.dfFlgStk"+item).value = "0";
        calculaprecio(objrel,5);
    }

    function llenarDatosPersona(obj,caso) {
        if (caso == 1)
            f2.dfPesoPer.value = obj.value;
        else if (caso == 2)
            f2.dfEstaturaPer.value = obj.value;
        else if (caso == 3)
            if (obj.checked)
               f2.dfFlgTer.value = 1;
            else
               f2.dfFlgTer.value = 0;
    }

    function SetTipSvrCrr(obj) {
        f2.dfTipSvrCrr.value = obj.value;
    }

    function SetPrecio(obj)
    {
        alert(obj.value);
        f2.dfPrecio.value = obj.value;
    }
</script>
</head>

<body>
<div id="body">
	<div id="header"></div>
    <ul id="usuario_registro">
		<?php 	echo display_usr($UsrId, $Perfil, "Cotizaciones", $db); ?>
    </ul>
    <div id="work">
<?php formar_topbox ("100%%","center"); ?>
<p align="left"><strong>Nueva Cotizaci&oacute;n</strong></p>
<p align="center">
<table WIDTH="100%" BORDER="0" CELLSPACING="0" CELLPADDING="1" ALIGN="center">
<tr>
<td width="170px" align="left" valign="top">
	<?php echo display_mnuizq($UsrId, $db); ?>
</td>
<td width="1%">&nbsp;</td>
<td  valign="top" class="label_left_right_top_bottom" STYLE="PADDING-LEFT:20px; PADDING-RIGHT:20px; TEXT-ALIGN:left">
	<table WIDTH="100%" BORDER="0" CELLSPACING="0" CELLPADDING="1" ALIGN="center">
	<tr><td>
		<h2><?php if ($Cod_Cot == 0) echo "Nueva Cotizaci&oacute;n"; else echo "Cotizaci&oacute;n $num_cot"; ?></h2>
		<table WIDTH="100%" BORDER="0" CELLSPACING="0" CELLPADDING="1" ALIGN="right">
		<tr>
			<td colspan="4">&nbsp;</td>
			<td width="100px" align="right">Fecha</td>
			<td align="left" style="padding-left: 5px">
				<input name="dfFecha" id="dfFecha" size="10" maxLength="10" ReadOnly class="textfield" <?php echo ($Cod_Cot == 0 ? "value=\"$today\"" : "value=\"".date("d/m/Y", strtotime($fec_cot))."\""); ?> />
				<?php if ($Cod_Cot == 0) { ?>
				&nbsp;<a HREF="#"><img src="../images/calendar.gif" border="0" id="lanzadorini" name="lanzadorini" alt="" /></a>
				<?php } ?>
			</td>
		</tr>
		<tr>
                    <td colspan="4">&nbsp;</td>
                    <td align="right" STYLE="PADDING-BOTTOM:5px;">Estado</td>
                    <td align="left" style="padding-left: 5px"><input name="dfEstado" size="20" maxLength="20" class="textfield" value="<?php echo $Est_Des; ?>" ReadOnly /></td>
                </tr>
                <tr>
                    <td colspan="6" STYLE="TEXT-ALIGN: center">
                        <fieldset class="label_left_right_top_bottom">
                                <legend>Datos Cliente</legend>
                                <table WIDTH="100%" BORDER="0" CELLSPACING="5" CELLPADDING="1" ALIGN="center">
                                    <form ID="searchPer" action="">
                                    <tr>
                                            <td width="5%" align="right">RUT</td>
                                            <td width="40%" align="left" STYLE="PADDING-LEFT: 3px" colspan="3">
                                            <input name="dfRut" id="dfRut" size="12" maxLength="12" onblur="filterPer(this)" class="textfield" onKeyPress="javascript:return soloRUT(event)" <?php echo (($Cod_Cot == 0 && $Rut_Per == "") ? "value=\"\"" : "value=\"".formatearRut($num_doc)."\" ReadOnly"); ?> />&nbsp;
                                            <?php if ($Cod_Cot == 0) { ?>
                                            <input type="button" name="Buscar" value="Buscar" class="button2" onclick="BuscarCliente('new')" />&nbsp;
                                            <?php    if ($Rut_Per == "") { ?>
                                            <input type="button" id="Nuevo" name="Nuevo" value="Nuevo" class="button2" onclick="NuevoCliente()" />
                                            <?php    } else { ?>
                                            <input type="button" id="Nuevo" name="Nuevo" value="Editar" class="button2" onclick="EditarCliente('<?php echo $Rut_Per; ?>')" />
                                            <?php    } ?>
                                            <?php } ?>
                                            </td>
                                            <!--td width="10%">&nbsp;</td-->
                                            <td width="10%" align="right">Direcci&oacute;n</td>
                                            <td width="35%" align="left" STYLE="PADDING-LEFT: 3px"><input name="dfDir" id="dfDir" size="50" maxLength="80" class="textfield" <?php echo (($Cod_Cot == 0 && $Rut_Per == "") ? "value=\"\"" : "value=\"".$dir_sucfct."\" ReadOnly"); ?>/></td>
                                    </tr>
                                    </form>
                                    <tr>
                                            <td width="5%" align="right">Cliente</td>
                                            <td width="40%" align="left" STYLE="PADDING-LEFT: 3px" colspan="3"><input name="dfNomClt" id="dfNomClt" size="40" maxLength="120" class="textfield" <?php echo (($Cod_Cot == 0 && $Rut_Per == "") ? "value=\"\"" : "value=\"".$nom_clt."\" ReadOnly"); ?>/></td>
                                            <!--td width="10%">&nbsp;</td-->
                                            <td width="10%" align="right">Comuna</td>
                                            <td width="35%" align="left" STYLE="PADDING-LEFT: 3px">
                                                    <?php //Seleccionar las comunas
                                                    if ($Cod_Cot == 0 && $Rut_Per == "") {
                                                    ?>
                                                            <input name="dfCmn" id="dfCmn" size="20" maxLength="20" class="textfield" value="" ReadOnly />
                                                    <?php }
                                                    else {
                                                        $sp = mssql_query("vm_cmn_s $cod_cmnfct",$db);
                                                        if (($row = mssql_fetch_array($sp))) {
                                                        ?>
                                                            <input name="dfCmn" id="dfCmn" size="20" maxLength="20" class="textfield" <?php echo "value=\"".utf8_encode($row['Nom_Cmn'])."\" ReadOnly"; ?> />
                                                    <?php
                                                        }
                                                    }
                                                    ?>
                                            </td>
                                    </tr>
                                    <tr>
                                            <td width="5%" align="right">Sucursal</td>
                                            <td width="40%" align="left" STYLE="PADDING-LEFT: 3px" colspan="3">
                                                            <?php //Seleccionar las comuna
                                                            if ($Cod_Cot == 0 && $Rut_Per == "") {
                                                            ?>
                                                                    <form id="searchSuc" action="">
                                                                    <select id="suc" name="suc" class="textfield" onChange="filterSuc(this)">
                                                                         <option selected value="_NONE">Seleccione una Sucursal</option>
                                                                    </select>
                                                                    </form>
                                                            <?php
                                                            }
                                                            else {
                                                            ?>
                                                                    <!--input name="dfSuc" size="30" maxLength="30" class="textfield" <?php echo "value=\"".$nom_suc."\" ReadOnly"; ?> /-->
                                                                    <form id="searchSuc" action="">
                                                                    <select id="suc" name="suc" class="textfield" onChange="filterSuc(this)"<?php if($Cod_Cot > 0) echo " DISABLED"; ?>>
                                                                    <?php
                                                                    $sp = mssql_query("vm_suc_s $cod_clt", $db);
                                                                    $xissuc = false;
                                                                    //$cod_suc = null;
                                                                    while ($row = mssql_fetch_array($sp)) {
                                                                            $selected = "";
                                                                            //if (trim($row['Nom_Suc']) != 'MIGRACION' and $cod_suc == null) {
                                                                            if ($row['Cod_Suc'] == $cod_sucfct) {
                                                                                    $selected = " selected";
                                                                                    //$cod_suc = $row['Cod_Suc'];
                                                                            }
                                                                        echo "<option value=\"".$row['Cod_Suc']."\"".$selected.">".utf8_encode($row['Nom_Suc'])."</option>\n";
                                                                            $xissuc = true;
                                                                    }
                                                                    ?>
                                                                    <?php if (!$xissuc) { ?>
                                                                <option selected value="_NONE">Seleccione una Sucursal</option>
                                                                    <?php } ?>
                                                                <option value="NewSuc">Nueva Sucursal</option>
                                                                    </select>
                                                                    </form>
                                                            <?php
                                                            }
                                                            ?>
                                            </td>
                                            <!--td width="10%">&nbsp;</td-->
                                            <td width="10%" align="right">Ciudad</td>
                                            <td width="35%" align="left" STYLE="PADDING-LEFT: 3px">
                                                            <?php //Seleccionar las ciudades
                                                            if ($Cod_Cot == 0 && $Rut_Per == "") {
                                                            ?>
                                                                    <input name="dfCdd" id="dfCdd" size="20" maxLength="20" class="textfield" value="" ReadOnly />
                                                    <?php }
                                                            else {
                                                                    $sp = mssql_query("vm_cdd_s $cod_cddfct",$db);
                                                                    if (($row = mssql_fetch_array($sp))) {
                                                                    ?>
                                                                    <input name="dfCdd" id="dfCdd" size="20" maxLength="20" class="textfield" <?php echo "value=\"".utf8_encode($row['Nom_Cdd'])."\" ReadOnly"; ?> />
                                                            <?php
                                                                    }
                                                            }
                                                            ?>
                                            </td>
                                    </tr>
                                    <tr>
                                            <td width="5%" align="right">Contacto</td>
                                            <td width="40%" align="left" STYLE="PADDING-LEFT: 3px" colspan="3">
                                                            <?php //Seleccionar contacto
                                                            if ($Cod_Cot == 0 && $Rut_Per == "") {
                                                            ?>
                                                                    <form id="searchCtt" action="">
                                                                    <select id="ctt" name="ctt" class="textfield" onChange="filterCtt(this)">
                                                                         <option selected value="_NONE">Seleccione un Contacto</option>
                                                                    </select>
                                                                    </form>
                                                            <?php
                                                            }
                                                            else {
                                                            ?>
                                                                    <!--input name="dfCtt" size="40" maxLength="40" class="textfield" <?php echo "value=\"".$nom_ctt."\" ReadOnly"; ?> /-->
                                                                    <form id="searchCtt" action="">
                                                                    <select id="ctt" name="ctt" class="textfield" onChange="filterCtt(this)"<?php if($Cod_Cot > 0 and $xisctt) echo " DISABLED"; ?>>
                                                            <?php
                                                                    $sp = mssql_query("vm_ctt_s $cod_clt, $cod_sucfct", $db);
                                                                    $xisctt = false;
                                                                    $nom_ctt = $nom_clt;
                                                                    while($row = mssql_fetch_array($sp))
                                                                    {
                                                                            $nom_ctt = trim($row['Pat_Per'])." ".trim($row['Mat_Per']).", ".trim($row['Nom_Per']);
                                                                            $nom_ctt = utf8_encode($nom_ctt);
                                                                            $flagdefault = ($row['Cod_Per'] == $cod_per ? " selected" : "");
                                                                            $xisctt = true;
                                                                    ?>
                                                                        <option value="<?php echo $row['Cod_Per'] ?>"<?php echo $flagdefault; ?>><?php echo $nom_ctt; ?></option>
                                                                    <?php
                                                                    }
                                                            ?>
                                                                    <?php if (!$xisctt) { ?>
                                                                <option selected value="_NONE">Seleccione un Contacto</option>
                                                                    <?php } ?>
                                                                <option value="NewCtt">Nuevo Contacto</option>
                                                                    </select>
                                                                    </form>
                                                            <?php
                                                            }
                                                            ?>
                                            </td>
                                            <!--td width="10%">&nbsp;</td-->
                                            <td width="10%" align="right">Tel&eacute;fono</td>
                                            <td width="35%" align="left" STYLE="PADDING-LEFT: 3px"><input name="dfFono" id="dfFono" size="30" maxLength="10" class="textfield" <?php echo (($Cod_Cot == 0 && $Rut_Per == "") ? "value=\"\"" : "value=\"".$fon_ctt."\" ReadOnly"); ?> /></td>
                                    </tr>
                                    <tr>
                                            <td colspan="4" align="right">&nbsp;</td>
                                            <td width="10%" align="right">e-mail</td>
                                            <td width="35%" align="left" STYLE="PADDING-LEFT: 3px"><input name="dfemail" id="dfemail" size="50" maxLength="50" class="textfield"  <?php echo (($Cod_Cot == 0 && $Rut_Per == "") ? "value=\"\"" : "value=\"".$mail_ctt."\" ReadOnly"); ?> /></td>
                                    </tr>
                                </table>
                        </fieldset>
                    </td>
		</tr>
		<tr>
                    <td colspan="6">&nbsp;</td>
                </tr>
		<tr>
                    <td colspan="6">
			<fieldset class="label_left_right_top_bottom">
                            <legend>Informaci&oacute;n Personal</legend>
                            <table WIDTH="95%" BORDER="0" CELLSPACING="5" CELLPADDING="1" ALIGN="center">
                            <tr>
                                    <td align="right">Peso (kg)</td>
                                    <td align="left">
                                            <select name="pesoPer" class="textfield" onChange="llenarDatosPersona(this,1)">
                                            <option value="0">_NONE</option>
                                            <?php
                                                    for ($pesoPer = 40; $pesoPer <= 120; $pesoPer++) {
                                            ?>
                                            <option value="<?php echo $pesoPer ?>"<?php echo ($pesoPer == $cot_peso ? " selected" : ""); ?>><?php echo $pesoPer ?></option>
                                            <?php
                                                    }
                                            ?>
                                            </select>
                                    </td>
                                    <td align="right">Estatura (cm)</td>
                                    <td align="left">
                                            <select name="estatura" class="textfield" onChange="llenarDatosPersona(this,2)">
                                            <option value="0">_NONE</option>
                                            <?php
                                                    for ($estatura = 140; $estatura <= 200; $estatura++) {
                                            ?>
                                            <option value="<?php echo $estatura ?>"<?php echo ($estatura == $cot_esta ? " selected" : ""); ?>><?php echo $estatura ?></option>
                                            <?php
                                                    }
                                            ?>
                                            </select>
                                    </td>
                                    <td width="23" align="right">
                                    <input type="checkbox", class="dato" style="height: 14px" name="FlgTerIn" id="FlgTerIn"
                                           onclick="llenarDatosPersona(this,3)"<?php echo ($flg_ter == 1 ? " checked" : ""); ?>></td>
                                    <td align="left">Productos para Terceros</td>
                            </tr>
                            </table>
			</fieldset>
                    </td>
                </tr>
		<tr><td colspan="6">&nbsp;</td></tr>
		<tr><td colspan="6">
			<fieldset class="label_left_right_top_bottom">
                            <legend>Despacho</legend>
                            <table WIDTH="95%" BORDER="0" CELLSPACING="5" CELLPADDING="1" ALIGN="center">
                            <tr>
                                    <td><input type="radio" class="dato" style="height: 14px" name="condespacho" value="0" onclick="HabilitaCarrier(this,'is_dsp')"<?php if ($is_dsp == 0) echo " checked" ?>></td>
                                    <td colspan="6" align="left">Sin Despacho</td>
                            </tr>
                            <tr>
                                    <td>
                                        <input type="radio" class="dato" style="height: 14px" name="condespacho" value="1" onclick="HabilitaCarrier(this,'is_dsp')"<?php if ($is_dsp == 1) echo " checked" ?>>
                                    </td>
                                    <td align="left" colspan="2">Despacho</td>
                                    <td align="right">Carrier</td>
                                    <td align="left" STYLE="PADDING-LEFT: 10px;">
                                        <form id="searchPro" name="searchPro" action="">
                                            <select id="pro" name="pro" class="textfield" onChange="filterPro(this)"<?php if ($is_dsp == 0) echo " DISABLED" ?>>
                                            <option value ="0">Seleccionar Carrier</option>
                                            <?php
                                                    $sp = mssql_query("vm_CrrCmb", $db);
                                                    while($row = mssql_fetch_array($sp))
                                                    {
                                            ?>
                                                            <option value="<?php echo $row["Cod_Crr"] ?>"<?php if ($cod_crr == $row["Cod_Crr"]) echo " selected"; ?>><?php echo $row["Des_Crr"] ?></option>
                                            <?php
                                                    }
                                            ?>
                                            </select>
                                        </form>
                                    </td>
                                    <td align="right">Servicio</td>
                                    <td align="left" STYLE="PADDING-LEFT: 10px;">
                                        <form id="searchEsp" name="searchPro" action="">
                                            <select id="esp" name="esp" class="textfield" onchange="traspasaToFormCaptura(this,'cod_svccrr')"<?php if ($is_dsp == 0) echo " DISABLED" ?>>
                                            <option value ="0">Seleccionar Servicio</option>
                                            <?php
                                                    if ($cod_crr > 0) {
                                                        //$query = "vm_SvcCrrCmb $cod_crr";
                                                        $sp = mssql_query("vm_SvcCrrCmb $cod_crr", $db);
                                                    }
                                                    else {
                                                        //$query = "vm_SvcCrrCmb 0";
                                                        $sp = mssql_query("vm_SvcCrrCmb 0", $db);
                                                    }
                                                    //$sp = mssql_query($query, $db);
                                                    while($row = mssql_fetch_array($sp))
                                                    {
                                            ?>
                                                            <option value="<?php echo $row["Cod_SvcCrr"] ?>"<?php if ($cod_svccrr == $row["Cod_SvcCrr"]) echo " selected"; ?>><?php echo $row["Des_SvcCrr"] ?></option>
                                            <?php
                                                    }
                                            ?>
                                            </select>
                                        </form>
                                    </td>
                            </tr>
                            <tr>
                                    <td>&nbsp;</td>
                                    <td align="left" colspan="6">
                                        <form id="searchSucDsp" action="">
                                            Sucursal:
                                            <select id="cmbSucDsp" name="cmbSucDsp" class="textfield" onchange="filterSucDsp(this,'cod_sucdsp')"<?php if ($is_dsp == 0) echo " DISABLED" ?>>
                                            <option value ="0">Oficina Carrier</option>
                                            <?php
                                                    $sp = mssql_query("vm_suc_s $cod_clt", $db);
                                                    while($row = mssql_fetch_array($sp))
                                                    {
                                            ?>
                                            <option value="<?php echo $row["Cod_Suc"] ?>"<?php if ($cod_sucdsp == $row["Cod_Suc"]) echo " selected"; ?>><?php echo utf8_encode($row["Nom_Suc"]); ?></option>
                                            <?php
                                                    }
                                            ?>
                                            <option value="NewSuc">Nueva Sucursal</option>
                                            </select>&nbsp;
                                            <input name="dfDirDsp" id="dfDirDsp" size="85" maxLength="80" class="textfield" value="<?php echo $dir_dsp ?>" onchange="traspasaToFormCaptura(this,'dir_dsp')"<?php if ($is_dsp == 0) echo " DISABLED" ?> />
                                        </form>
                                    </td>
                            </tr>
                            <tr>
                                    <td>&nbsp;</td>
                                    <td align="left">Comuna:</td>
                                    <td align="left">
                                        <form id="searchCmnDsp" action="">
                                            <select id="cmbCmnDsp" name="cmbCmnDsp" class="textfield" onChange="filterCmnDsp(this)"<?php if ($is_dsp == 0 or $cod_sucdsp > 0) echo " DISABLED" ?>>
                                            <option value ="0">Seleccione Comuna</option>
                                            <?php
                                                    $sp = mssql_query("vm_cmn_s", $db);
                                                    while($row = mssql_fetch_array($sp))
                                                    {
                                            ?>
                                            <option value="<?php echo $row["Cod_Cmn"] ?>"<?php if ($cod_cmndsp == $row["Cod_Cmn"]) echo " selected"; ?>><?php echo utf8_encode($row["Nom_Cmn"]); ?></option>
                                            <?php
                                                    }
                                            ?>
                                            </select>
                                        </form>
                                    </td>
                                    <td align="right">Ciudad:</td>
                                    <td align="left" style="padding-left: 5px">
                                            <select id="cmbCddDsp" name="cmbCddDsp" class="textfield" onChange="filterCddDsp(this)"<?php if ($is_dsp == 0 or $cod_sucdsp > 0) echo " DISABLED" ?>>
                                            <option value ="0">Seleccione Ciudad</option>
                                            <?php
                                                    if ($is_dsp == 0) $sp = mssql_query("vm_cddcmn_s 0, 0",$db);
                                                    else $sp = mssql_query("vm_cddcmn_s NULL, $cod_cmndsp",$db);
                                                    //$sp = mssql_query($query, $db);
                                                    while($row = mssql_fetch_array($sp))
                                                    {
                                            ?>
                                            <option value="<?php echo $row["Cod_Cdd"] ?>"<?php if ($cod_cdddsp == $row["Cod_Cdd"]) echo " selected"; ?>><?php echo utf8_encode($row["Nom_Cdd"]); ?></option>
                                            <?php
                                                    }
                                            ?>
                                            </select>&nbsp;
                                    </td>
                                    <td colspan="2" style="text-align: right">Valor (IVA Inc):&nbsp;<input name="dfValDsp" id="dfValDsp" size="8" maxLength="8" class="textfield" value="<?php echo number_format($val_dsp+$val_dsp*$IVA,0,',','.') ?>" READONLY onchange="traspasaToFormCaptura(this,'val_dsp')"<?php if ($is_dsp == 0) echo " DISABLED" ?> /></td>
                            </tr>
							<tr>
							<td colspan="7" style="text-align: right">Peso (kg):&nbsp;<input name="dfKilos" id="dfKilos" size="8" maxLength="8" class="textfield" value="<?php echo number_format($kilos,2,',','.') ?>" READONLY" /></td>
							</tr>
                            <tr>
                                    <td>&nbsp;</td>
                                    <td colspan="6" align="left">
                                            <div id="tipo_despacho">
                                            <table WIDTH="100%" BORDER="0" CELLSPACING="0" CELLPADDING="1" ALIGN="center">
                                                    <tr>
                                                            <td width="10px" style="TEXT-ALIGN:center" valign="top">
                                                                    <img src="../images/warning.png" alt="" />
                                                            </td>
                                                            <td style="TEXT-ALIGN:left; PADDING-LEFT:5px; PADDING-BOTTOM: 5px" colspan="2" valign="top">
                                                            Nuestros registros indican que en el pasado han ocurrido problemas con los despachos a domicilio realizados a la comuna seleccionada.
                                                            Recomendamos que el despacho sea realizado a la Oficina del Carrier m&aacute;s cercana al domicilio en donde ser&aacute; recibido y
                                                            almacenado para su retiro. En caso de concretar una compra, Vestmed debe comunicar&aacute; con el cliente
                                                            para entregarle mayor informaci&oacute;n.
                                                            </td>
                                                    </tr>
                                                    <tr>
                                                            <td width="10px" style="TEXT-ALIGN:center">
                                                                    <input id="rbTipoDsp" name="rbTipoDsp" type="radio" style="border:none" value="0"<?php echo ($cod_tipsvc == 0 ? " checked" : ""); ?> onclick="SetTipSvrCrr(this)" />
                                                            </td>
                                                            <td style="TEXT-ALIGN:left; PADDING-LEFT:5px" colspan="2">Despacho a Domicilio</td>
                                                    </tr>
                                                    <tr>
                                                            <td width="10px" style="TEXT-ALIGN:center">
                                                                    <input id="rbTipoDsp" name="rbTipoDsp" type="radio" style="border:none" value="1" <?php echo ($cod_tipsvc == 1 ? " checked" : ""); ?> onclick="SetTipSvrCrr(this)" />
                                                            </td>
                                                            <td style="TEXT-ALIGN:left; PADDING-LEFT:5px" colspan="2">Despacho a Sucursal del Carrier (Recomendable para localidades distantes/Rurales)</td>
                                                    </tr>
                                            </table>
                                            </div>
                                    </td>
                            </tr>
                            </table>
			</fieldset>
                    </td>
                </tr>
		<tr><td colspan="6">&nbsp;</td></tr>
		<tr>
			<td width="25%" valign="top" STYLE="TEXT-ALIGN: center">
				<fieldset class="label_left_right_top_bottom">
				<legend>Condiciones</legend>
					<table WIDTH="95%" BORDER="0" CELLSPACING="0" CELLPADDING="1" ALIGN="center">
					<tr>
						<td width="40%" align="right" STYLE="PADDING-TOP: 5px">Lista Precio</td>
						<td width="60%" align="left" STYLE="PADDING-LEFT: 10px; PADDING-TOP: 5px">
							<select id="precios" name="precios" class="textfield" onclick="f2.dfPrecio.value = this.value;">
							<option value="1"<?php if ($Cod_Cot > 0 And $cod_pre == 1) echo " selected"; ?>>Minorista</option>
							<option value="2"<?php if ($Cod_Cot > 0 And $cod_pre == 2) echo " selected"; ?>>Mayorista</option>
							</select>
						</td>
					</tr>
					<tr>
						<td align="right" STYLE="PADDING-TOP: 5px">IVA</td>
						<td align="left" STYLE="PADDING-LEFT: 10px; PADDING-TOP: 5px">
							<select id="iva" name="iva" class="textfield" onchange="recalcular(this)">
							<option value="1"<?php if ($Cod_Iva == 1) echo " selected"; ?>>Incluido</option>
							<option value="2"<?php if ($Cod_Iva == 2) echo " selected"; ?>>Mas IVA</option>
							</select>
						</td>
					</tr>
					<tr>
						<td align="right" STYLE="PADDING-TOP: 5px; PADDING-BOTTOM: 5px;">Dolar</td>
						<td align="left" STYLE="PADDING-LEFT: 10px; PADDING-TOP: 5px; PADDING-BOTTOM: 5px;">
						<input name="dfDolar" size="15" maxLength="10" class="textfield" value="<?php echo $Val_Usd; ?>" onchange="f2.dfValUSD.value = this.value;" />
						</td>
					</tr>
					</table>
				</fieldset>
			</td>
			<td width="1%" STYLE="TEXT-ALIGN: center">&nbsp;
			</td>
			<td width="33%" valign="top" STYLE="TEXT-ALIGN: center">
				<fieldset class="label_left_right_top_bottom">
				<legend>Caracter&iacute;sticas</legend>
					<table WIDTH="95%" BORDER="0" CELLSPACING="0" CELLPADDING="1" ALIGN="center">
					<tr>
						<td width="40%" align="right" STYLE="PADDING-TOP: 5px">Criticalidad</td>
						<td width="60%" align="left" STYLE="PADDING-LEFT: 10px; PADDING-TOP: 5px">
							<select id="critic" name="critic" class="textfield" onclick="f2.dfCriticidad.value = this.value;">
							<option value="1"<?php if ($Cod_Cri == 1) echo " selected"; ?>>Urgente</option>
							<option value="2"<?php if ($Cod_Cri == 2) echo " selected"; ?>>Normal</option>
							</select>
						</td>
					</tr>
					<tr>
						<td align="right" STYLE="PADDING-TOP: 5px">F.Cierre</td>
						<td align="left" STYLE="PADDING-LEFT: 10px; PADDING-TOP: 5px">
						<input name="dfFCierre" id="dfFCierre" size="12" maxLength="12" class="textfield" ReadOnly value="<?php echo "$Fec_Cie"; ?>" onchange="f2.dfFecCie.value = this.value;" />&nbsp;
						<a HREF="#"><img src="../images/calendar.gif" border="0" id="lanzadorfin" name="lanzadorfin" alt="" /></a>
						</td>
					</tr>
					<tr>
						<td align="right" STYLE="PADDING-TOP: 5px; PADDING-BOTTOM: 5px;">Probabilidad</td>
						<td align="left" STYLE="PADDING-LEFT: 10px; PADDING-TOP: 5px; PADDING-BOTTOM: 5px;">
						<!--input name="dfProbabilidad" size="15" maxLength="10" class="textfield" value="<?php echo "$Val_Pro"; ?>" /-->
						<select id="dfProbabilidad" name="dfProbabilidad" class="textfield" onclick="f2.dfValPro.value = this.value;">
						<?php
							$p = 0;
							while  ($p <= 100) {
								?>
								<option value="<?php echo $p; ?>"<?php if ($Val_Pro == $p) echo " selected"; ?>><?php echo $p; ?> %</option>
								<?php
								$p+=10;
							}
						?>
						</select>
						</td>
					</tr>
					</table>
				</fieldset>
			</td>
			<td  width="1%" STYLE="TEXT-ALIGN: center">&nbsp;
			</td>
			<td colspan="2" width="25%" valign="top" STYLE="TEXT-ALIGN: center;">
				<fieldset class="label_left_right_top_bottom">
                                    <legend>Consultas</legend>
                                    <form id="searchConsultas" action="">
                                    <table id="tblConsultas" BORDER="0" CELLSPACING="0" CELLPADDING="2" width="100%" ALIGN="center">
                                            <tr>
                                                    <td width="100" VALIGN="TOP" class="dato10p">Consultas Realizadas:
                                                    <?php
                                                            echo $tot_cnacot;
                                                            if (($tot_cnacot) > 0) echo " (<a href=\"javascript:ver_consultas($Cod_Cot)\">VER</a>)";
                                                    ?>
                                                    </td>
                                            </tr>
                                            <tr>
                                                    <td width="100" VALIGN="TOP" class="dato10p">Consultas Sin Leer:
                                                    <?php
                                                            echo $tot_cnasinres;
                                                            if ($tot_cnasinres > 0) echo " (<a href=\"javascript:ver_consultas($Cod_Cot)\">LEER</a>)";
                                                    ?>
                                                    </td>
                                            </tr>
                                            <tr><td width="100" VALIGN="TOP" class="dato10p">Nueva Consulta: <a href="javascript:ver_consultas(<?php echo $Cod_Cot; ?>)">Aqu&iacute;</a></td></tr>
                                    </table>
                                    </form>
				</fieldset>
			</td>
		</tr>
		<tr><td colspan="6">&nbsp;</td></tr>
		<form ID="F2" method="POST" name="F2" ACTION="resp_cot.php" onsubmit="return CheckCliente(this)">
		<tr>
                    <td colspan="6">
                        <fieldset class="label_left_right_top_bottom">
                            <legend>Productos</legend>
                            <table WIDTH="99%" BORDER="0" CELLSPACING="0" CELLPADDING="1" ALIGN="center">
                            <tr>
                                <td align="left">
                                        <input type="button" name="Agregar" value="Agregar" class="button2" onclick="agregarProductos(<?php echo $Cod_Cot ?>)">&nbsp;
                                        <input type="button" name="Eliminar" value="Eliminar" class="button2" onclick="eliminarProductos(<?php echo $Cod_Cot ?>)">&nbsp;
                                </td>
                                <td align="right" colspan="2">
                                        # Cotizaci&oacute;n&nbsp;
                                        <input name="dfCod_CotImp" size="6" maxLength="6" class="textfield" />&nbsp;
                                        <input type="button" name="Importar" value="Importar" class="button2">
                                </td>
                            </tr>
                            </table>
                            <BR>
                            <table WIDTH="99%" BORDER="0" CELLSPACING="0" CELLPADDING="1" ALIGN="center">
                            <tr>
                                    <td class="titulo_tabla" width="5%"  align="middle">&nbsp;</td>
                                    <td class="titulo_tabla" width="10%" align="middle">Marca</td>
                                    <td class="titulo_tabla" width="10%" align="middle">Style</td>
                                    <td class="titulo_tabla" width="10%" align="middle">Patr&oacute;n</td>
                                    <td class="titulo_tabla" width="10%" align="middle">Talla</td>
                                    <td class="titulo_tabla" width="5%" align="middle">Cant.</td>
                                    <td class="titulo_tabla" width="10%" align="middle">Desc.</td>
                                    <td class="titulo_tabla" width="10%" align="middle">P.Unitario</td>
                                    <td class="titulo_tabla" width="5%"  align="middle">Stock</td>
                                    <td class="titulo_tabla" width="15%"  align="middle">&nbsp</td>
                                    <td class="titulo_tabla" width="10%" STYLE="TEXT-ALIGN: right; PADDING-RIGHT: 5px">Total</td>
                            </tr>
                            <?php
                            $j = 0;
                            $suma = 0;
                            $iTotPrd = 0;
                            $peso = 0.0;
                            $result = mssql_query("vm_s_cotdet $Cod_Cot", $db);
                            while (($row = mssql_fetch_array($result))) {
                                    echo "<tr>\n";
                                    if ($j == 0) {
                                            $clase1 = "";
                                            $clase2 = "";
                                    }
                                    else {
                                            $clase1 = "";
                                            $clase2 = "";
                                    }
                                    
                                    $Stock = $precio = 0;
                                    $bodegas = "";
                                    $query  = "sp_getstock '".$row['Cod_Mca']."', ";
                                    $query .= "'".$row['Cod_Sty']."', ";
                                    $query .= "'".$row['Cod_GrpPat']."', ";
                                    $query .= "'001', ";
                                    $query .= "'".date('Ymd')."', ";
                                    $query .= "'".$row['Cod_Pat']."', ";
                                    $query .= "'".$row['Cod_Sze']."'";
                                    $result2 = mssql_query($query, $db);
                                    //echo $query."<br>";
                                    while (($row2 = mssql_fetch_array($result2))) {
                                        $Stock += $row2['Stock'];
                                        $bodegas .= ($row2['Bodega']."=".number_format($row2['Stock'],0)."\n");
                                    }
                                    if ($Stock > 0) {
                                        $precio = ($row['Prc_Nto'] == "" ? "&nbsp;" : formatearMillones($row['Prc_Nto']));
                                        $suma+=$row['Prc_Nto'];
                                    }
                                    ?>
                                
                                
                                    <td class="<?php echo $clase1; ?>" style="TEXT-ALIGN: center ">
                                            <input type="checkbox" class="dato" style="height: 14px" name="seleccionadof[]" value="<?php echo $row["Cod_Sec"]."-".$row['Cod_Prd']; ?>" />
                                            <input type="hidden" name="dfCod<?php echo $iTotPrd; ?>" id="dfCod<?php echo $iTotPrd; ?>" value="<?php echo $row['Cod_Prd']; ?>">
                                            <input type="hidden" name="dfCodSec<?php echo $iTotPrd; ?>" id="dfCodSec<?php echo $iTotPrd; ?>" value="<?php echo $row['Cod_Sec']; ?>">
                                    </td>
                                    <td class="<?php echo $clase2; ?>" style="TEXT-ALIGN: center"><?php echo $row['Cod_Mca']; ?></td>
                                    <td class="<?php echo $clase2; ?>" style="TEXT-ALIGN: center"><a href="javascript:modificarProductos(<?php echo $Cod_Cot.",'".$row['Cod_GrpPrd']."','".$row['Cod_Sty']."','".$row['Cod_Pat']."'"; ?>)" title="Modificar"><?php echo $row['Cod_Sty']; ?></a></td>
                                    <td class="<?php echo $clase2; ?>" style="TEXT-ALIGN: center"><?php echo $row['Key_Pat']; ?></td>
                                    <td class="<?php echo $clase2; ?>" style="TEXT-ALIGN: center"><?php echo $row['Val_Sze']; ?></td>
                                    <td class="<?php echo $clase2; ?>" style="TEXT-ALIGN: center">
                                            <input name="dfCtd<?php echo $iTotPrd; ?>" id="dfCtd<?php echo $iTotPrd; ?>" size="3" maxLength="3" style="TEXT-ALIGN: center" value="<?php echo $row['Cot_Ctd']; ?>" class="textfieldRO3" ReadOnly /></td>
                                    <td class="<?php echo $clase2; ?>" style="TEXT-ALIGN: center">
                                            <input name="dfDcto<?php echo $iTotPrd; ?>" id="dfDcto<?php echo $iTotPrd; ?>" size="3" maxLength="3" style="TEXT-ALIGN: left" value="<?php echo $row['Val_Des']; ?>" class="textfield" onblur="calculaprecio(this,6)" /></td>
                                    <td class="<?php echo $clase2; ?>" style="TEXT-ALIGN: center">
                                            <input name="dfPrc<?php echo $iTotPrd; ?>" id="dfPrc<?php echo $iTotPrd; ?>" size="10" maxLength="10" type="text" value="<?php echo round($row['Prc_Lst']); ?>" class="textfield" onblur="calculaprecio(this,5)" />
                                    </td>
                                    <td class="<?php echo $clase2; ?>" style="TEXT-ALIGN: center">
                                        <?php echo $Stock ?>
                                    </td>
                                    <td class="<?php echo $clase2; ?>" style="TEXT-ALIGN: center" title="<?php echo $bodegas; ?>">
                                        <input type="checkbox" class="dato" style="height: 14px" name="sinstock<?php echo $iTotPrd; ?>" id="sinstock<?php echo $iTotPrd; ?>" value="<?php echo $row["Cod_Sec"]."-".$row['Cod_Prd']; ?>" onclick="RegSinStock(this,<?php echo $iTotPrd; ?>)" <?php if ($row['Flg_SinInv'] == 1) echo "checked"; ?> />&nbsp;Sin Stock
                                        <input name="dfFlgStk<?php echo $iTotPrd; ?>" id="dfFlgStk<?php echo $iTotPrd; ?>" type="hidden" value="<?php echo $row['Flg_SinInv']; ?>" />
                                    </td>
                                    <td class="<?php echo $clase2; ?>" style="TEXT-ALIGN: right">
                                            <input name="dfNeto<?php echo $iTotPrd; ?>" id="dfNeto<?php echo $iTotPrd; ?>" size="12" maxLength="12" style="TEXT-ALIGN: right" value="<?php echo $precio; ?>" class="textfieldRO3" ReadOnly />
                                    </td>
                                    <?php
                                    echo "</tr>";
                                    $j = 1 - $j;
                                    $peso+=$row['Peso'];
                                    $iTotPrd++;
                            }
                            if ($peso > 0) $peso+=0.1;
                            $suma = intval($suma);
                            mssql_free_result($result);
                            ?>
                            <?php if ($iTotPrd == 0) { ?>
                            <tr>
                                    <td colspan="11" style="PADDING-TOP: 10px; TEXT-ALIGN: CENTER; PADDING-BOTTOM: 10px" class="label_left_right_bottom">
                                    NO EXISTEN PRODUCTOS COTIZADOS. SI DESEA AGREGAR UNA PINCHE <A HREF="javascript:agregarProductos(<?php echo $Cod_Cot ?>)">AQU&Iacute;</A>
                                    <input type="hidden" name="totprd" id="totprd" value="<?php echo $iTotPrd; ?>">
                                    <input type="hidden" name="p_iva" id="p_iva" value="<?php echo $IVA; ?>">
                                    <input type="hidden" id="peso" name="peso" value="<?php echo $kilos; ?>">
                                    </td>
                            </tr>
                            <?php } else { ?>
                            <tr>
                                    <td colspan="11">
                                    <input type="hidden" name="totprd" id="totprd" value="<?php echo $iTotPrd; ?>">
                                    <input type="hidden" name="p_iva" id="p_iva" value="<?php echo $IVA; ?>">
                                    <input type="hidden" id="peso" name="peso" value="<?php echo $kilos; ?>">
                                    </td>
                            </tr>
                            <?php } ?>
                            </table>
                            <br>
                            <?php
                                    $impuesto = 0;
                                    $neto = intval($suma + 0.5);
                                    if ($Cod_Iva == 2) {
                                        $impuesto = intval($neto * $IVA + 0.5);
                                    }
                                    $total = $neto + $impuesto;
                            ?>
                            <table WIDTH="99%" BORDER="0" CELLSPACING="0" CELLPADDING="1" ALIGN="center">
                            <tr>
                            <td align="left">Desc (%).:&nbsp;<input name="dfDescto" id="dfDescto" size="5" maxLength="5" class="textfield" onblur="calculartotales()" value="<?php echo "$Val_Des"; ?>" /></td>
                            <td align="right">Neto &nbsp;<input name="dfNeto" id="dfNeto" size="12" maxLength="12" style="TEXT-ALIGN: right" value="<?php echo formatearMillones($neto); ?>" class="textfieldRO3" ReadOnly /></td>
                            </tr>
                            <tr>
                            <td align="left">&nbsp;</td>
                            <td align="right">Iva &nbsp;<input name="dfIva" id="dfIva" size="12" maxLength="12" style="TEXT-ALIGN: right" value="<?php echo formatearMillones($impuesto); ?>" class="textfieldRO3" ReadOnly /></td>
                            </tr>
                            <tr>
                            <td align="left"><input type="button" name="Usuarios" id="Usuarios" value="Usuarios" class="button2"></td>
                            <td align="right">Total &nbsp;<input name="dfTotal" id="dfTotal" size="12" maxLength="12" style="TEXT-ALIGN: right" value="<?php echo formatearMillones($total); ?>" class="textfieldRO3" ReadOnly /></td>
                            </tr>
                            <tr>
                            </tr>
                            </table>
                        </fieldset>
                    </td>
                </tr>
		<tr><td colspan="6" align="right" style="PADDING-TOP: 5px">
			<input type="hidden" id="cod_cot" name="cod_cot" value="<?php echo $Cod_Cot; ?>" />
			<input type="hidden" id="cod_per" name="cod_per" value="<?php echo $cod_per; ?>" />
			<input type="hidden" id="cod_clt" name="cod_clt" value="<?php echo $cod_clt; ?>" />
			<input type="hidden" id="cod_suc" name="cod_suc" value="<?php echo $cod_suc; ?>" />
			
			<input type="hidden" id="is_dsp" name ="is_dsp" value="<?php echo $is_dsp; ?>" />
			<input type="hidden" id="cod_crr" name ="cod_crr" value="<?php echo $cod_crr; ?>" />
			<input type="hidden" id="cod_svccrr" name ="cod_svccrr" value="<?php echo $cod_svccrr; ?>" />
			<input type="hidden" id="cod_sucdsp" name ="cod_sucdsp" value="<?php echo $cod_sucdsp; ?>" />
			<input type="hidden" id="cod_cdddsp" name ="cod_cdddsp" value="<?php echo $cod_cdddsp; ?>" />
			<input type="hidden" id="cod_cmndsp" name ="cod_cmndsp" value="<?php echo $cod_cmndsp; ?>" />
			<input type="hidden" id="dir_dsp" name ="dir_dsp" value="<?php echo $dir_dsp; ?>" />
			<input type="hidden" id="val_dsp" name ="val_dsp" value="<?php echo $val_dsp; ?>" />
			<input type="hidden" id="dfFlgTer" name ="dfFlgTer" value="<?php echo $flg_ter; ?> " />
			<input type="hidden" id="dfPesoPer" name ="dfPesoPer" value="<?php echo $cot_peso; ?>" />
			<input type="hidden" id="dfEstaturaPer" name ="dfEstaturaPer" value="<?php echo $cot_esta; ?>" />
			<input type="hidden" id="dfTipSvrCrr" name="dfTipSvrCrr" value="<?php echo $cod_tipsvc; ?>" />
			<input type="hidden" id="dfPrecio" name="dfPrecio" value="<?php echo $cod_pre; ?>" />
			<input type="hidden" id="dfIVA" name="dfIVA" value="<?php echo $Cod_Iva; ?>" />
			<input type="hidden" id="dfValUSD" name="dfValUSD" value="<?php echo $Val_Usd; ?>" />
                        <input type="hidden" id="dfCriticidad" name="dfCriticidad" value="<?php echo $Cod_Cri ?>" />
                        <input type="hidden" id="dfFecCie" name="dfFecCie" value="<?php echo $Fec_Cie ?>" />
                        <input type="hidden" id="dfValPro" name="dfValPro" value="<?php echo $Val_Pro ?>" />
			
			<?php if ($Cod_Cot > 0) { ?>
			<input type="button" name="Eliminar" value="Eliminar" class="button2" onclick="EliminarCot()">&nbsp;
			<?php } ?>
			<input type="button" name="Cerrar" value="Cerrar" class="button2" onclick="volver()">&nbsp;
			<?php if ($iTotPrd > 0) { ?>
			<input type="button" name="Preview"  value="Preview" class="button2" onclick="ver_preview(<?php echo $Cod_Cot; ?>)">&nbsp;
			<input type="button" name="Enviar"   value="Enviar" class="button2" onclick="cerrar_cot(<?php echo $Cod_Cot; ?>)">
			<?php } ?>
			<input type="submit" name="Guardar"  value="Guardar" class="button2">&nbsp;
		</td></tr>
		</form>
		</table>
	</td></tr>
	</table>
</td>
</tr>
</table>
</p>
<?php formar_bottombox (); ?>
    </div>
    <div id="footer"></div>
</div>
<script language="javascript">
	var f1;	
	var f2;
	var bListoParaCerrar = <?php echo ($bOkRespuesta ? "true" : "false"); ?>;
	
	f1 = document.F1;	
	f2 = document.F2;
	
<?php
	if ($is_dsp == 1) {
            //$sql = "vm_SvcCrr_Prc_s ".$cod_crr.",".$cod_svccrr.",".$cod_rgn.",".$kilos;
            //echo "// sql = ".$sql."\n";
            $sp = mssql_query("vm_SvcCrr_Prc_s ".$cod_crr.",".$cod_svccrr.",".$cod_rgn.",".$kilos, $db);
            while($row = mssql_fetch_array($sp))
            {
                //if ($kilos < $row["Pes_Max"]) {
                    echo "\t\$j(\"#dfValDsp\").val('$ ".number_format($row['Prc_Dsp']+$row['Prc_Dsp']*$IVA,0,',','.')."');";
                    echo "\n\t\$j(\"#val_dsp\").val('".round($row['Prc_Dsp']+$row['Prc_Dsp']*$IVA)."');";
                    if ($row['Prc_Dsp'] == 0) {
                        //echo "\n\t\$j(\"#dfValDsp\").removeAttr('readonly');";
                        echo "\n\t\$j(\"#dfValDsp\").val('".number_format($val_dsp+$val_dsp*$IVA,0,',','.')."');";
                        echo "\n\t\$j(\"#val_dsp\").val('".($val_dsp+$val_dsp*$IVA)."');";
                    }
                    mssql_free_result($sp);
                    break;
                //}
            }
            if ($tip_cmndsp == 0) echo "\n\t\$j(\"#tipo_despacho\").hide();\n";
	}
	else echo "\n\t\$j(\"#tipo_despacho\").hide();\n";
?>
    
	calculartotales();
	
</script>
<!-- script que define y configura el calendario-->
<script type="text/javascript">
<?php if ($Cod_Cot == 0) { ?>
Calendar.setup({
	inputField : "dfFecha", // id del campo de texto
	ifFormat : "%d/%m/%Y", // formato de la fecha que se escriba en el campo de texto
	button : "lanzadorini" // el id del bot�n que lanzar� el calendario
});
<?php } ?>
Calendar.setup({
	inputField : "dfFCierre", // id del campo de texto
	ifFormat : "%d/%m/%Y", // formato de la fecha que se escriba en el campo de texto
	button : "lanzadorfin" // el id del bot�n que lanzar� el calendario
});
</script>
</body>
</html>
