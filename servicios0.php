<?php
//Obtengo los datos de conexion de la base de datos
ini_set('display_errors', '0');
session_start();
include("config.php");

$cod_cot = 0;
if (isset($_SESSION['CodCot'])) $cod_cot = intval($_SESSION['CodCot']);
if (isset($_SESSION['CodClt'])) $cod_clt = intval($_SESSION['CodClt']);
if (isset($_SESSION['CodPer'])) $Cod_Per = intval($_SESSION['CodPer']);

$result = mssql_query ("vm_per_s ".$Cod_Per, $db) or die ("No se pudo leer datos del usuario (".$Cod_Per.")");
if (($row = mssql_fetch_array($result))) 
    $nombre = utf8_encode($row["Pat_Per"])." ".utf8_encode($row["Mat_Per"]).", ".utf8_encode($row["Nom_Per"]);

$p_grpprd = ok($_GET['producto']);  
$p_cantidad = ok($_GET['cantidad']);
$select_prod = mssql_query("vm_strinv_prodinfo '".$p_grpprd."'", $db);
if (($row = mssql_fetch_array($select_prod))) {
    $grpprd_title = str_replace("#", "'",$row["title"]);
    $dsg_style = $row["style"];       
    $dsg_iddsg = $row["id_dsg"];                
    
    $select_marca = mssql_query("vm_strmrc_nom '".$dsg_iddsg."'",$db);
    if (($rowx = mssql_fetch_array($select_marca))) {
        $linmca_codigo = $rowx["linmca_codigo"];     
        $linmca_descripcion = utf8_encode($rowx["linmca_descripcion"]);
    }
    mssql_free_result($row);    
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title></title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
    
    <link href="http://code.jquery.com/ui/1.9.0/themes/redmond/jquery-ui.css" rel="stylesheet" />
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.8/jquery.js"></script>
    <script src="http://code.jquery.com/ui/1.9.0/jquery-ui.min.js"></script>

    <LINK href="css/servicios.css" type="text/css" rel="stylesheet" />
    <link href="meson/css/itunes.css" type="text/css" rel="stylesheet" />
    <script type="text/javascript" src="js/mootools-1.2.1-core-yc.js"></script>
    <script type="text/javascript" src="Include/SoloNumeros.js"></script>
    <script type="text/javascript">
        var $j = jQuery.noConflict();

            $j(document).ready
            (
            
		function()
		{
                    $j("form#searchSvc").submit(function(){
                            $j.post("ajax-search-svc.php",{
                                    search_type: "svcprd",
                                    param_filter: $j("#codsvc").val()
                            }, function(xml) {
                                    listServicios(xml);
                            });return false;
		    });
                    
                    $j("form#AddSvc").submit(function(){
                            $j.post("ajax-search-svc.php",{
                                    search_type: "addsvc",
                                    param_filter: "<?php echo $p_grpprd; ?>",
                                    id_dsg: $j("#dfDsg").val(),
                                    id_pat: $j("#dfPat").val(),
                                    param_clt: "<?php echo $cod_clt ?>",
                                    param_per: "<?php echo $Cod_Per ?>",
                                    param_sze: $j("#dfSze").val(),
                                    param_dfctd: $j("#dfCtd").val(),
                                    param_xmlsrv: $j("#parametro").val(),
                                    cod_cot: "<?php echo $cod_cot; ?>"
                            }, function(xml) {
                                    RepoblarDetalle(xml);
                            });return false;
		    });
                    
                    $j("form#AddPtl").submit(function(){
                            $j.post("ajax-search-svc.php",{
                                    search_type: "addptl",
                                    param_filter: $j("#dfCodSvc").val() ,
                                    param_dfpre: $j("#dfCodPre").val(),
                                    param_dfli1: $j("#dfTxt1").val(),
                                    param_dfli2: $j("#dfTxt2").val(),
                                    param_clt: "<?php echo $cod_clt ?>",
                                    param_per: "<?php echo $Cod_Per ?>",
                                    param_dffnt: $j("#dfCodFnt").val(),
                                    param_dfcol: $j("#dfCodCol").val()
                            }, function(xml) {
                                    RepoblarPlantillas(xml);
                            });return false;
		    });
                    
                    $j("form#SelPtl").submit(function(){
                            $j.post("ajax-search-svc.php",{
                                    search_type: "selptl",
                                    param_filter: $j("#dfCodPtl").val(),
                                    param_clt: "<?php echo $cod_clt ?>",
                                    param_per: "<?php echo $Cod_Per ?>"
                            }, function(xml) {
                                    SetPlantilla(xml);
                            });return false;
		    });
                    
                }
		
            );
            //TODO: No se esta realizando el post, probablemente debido a que falta el evento submit.
        
        function listServicios(xml) {
            var secprd = $j("#secprd").val();
            var strXML = "<select name=\"cmbPreLine" + secprd + "\" id=\"cmbPreLine" + secprd + "\">";
            $j("filter",xml).each(function(id) {
                filter = $j("filter",xml).get(id);
                strXML += "<option value=\"" + $j("code",filter).text() + "\">" + $j("value",filter).text() + "</option>";
            });
            strXML += "</select>";
            $j("#cmbPreLine"+secprd).replaceWith(strXML);
            
            strXML = "<select name=\"cmbFte" + secprd + "\" id=\"cmbFte" + secprd + "\">";
            $j("font",xml).each(function(id) {
                font = $j("font",xml).get(id);
                strXML += "<option value=\"" + $j("code",font).text() + "\">" + $j("value",font).text() + "</option>";
            });
            strXML += "</select>";
            $j("#cmbFte"+secprd).replaceWith(strXML);
            
            strXML = "<select name=\"cmbColor" + secprd + "\" id=\"cmbColor" + secprd + "\">";
            $j("color",xml).each(function(id) {
                color = $j("color",xml).get(id);
                strXML += "<option value=\"" + $j("code",color).text() + "\">" + $j("value",color).text() + "</option>";
            });
            strXML += "</select>";
            $j("#cmbColor"+secprd).replaceWith(strXML);
            
            if ($j("#cmbSrv"+secprd).val() == 2) {
                $j("#dfLinea1"+secprd).removeAttr("disabled");
                $j("#dfLinea2"+secprd).removeAttr("disabled");
                $j("#dfObs"+secprd).removeAttr("disabled");
                $j("#btnPlantilla"+secprd).removeAttr("disabled");
            }
            else if ($j("#cmbSrv"+secprd).val() == 1) {
                $j("#dfLinea1"+secprd).removeAttr("disabled");
                $j("#dfLinea2"+secprd).attr("disabled", "disabled");
                $j("#dfObs"+secprd).removeAttr("disabled");
                $j("#btnPlantilla"+secprd).removeAttr("disabled");
            }
            else if ($j("#cmbSrv"+secprd).val() == 0) {
                $j("#dfLinea1"+secprd).attr("disabled", "disabled");
                $j("#dfLinea2"+secprd).attr("disabled", "disabled");
                $j("#btnPlantilla"+secprd).attr("disabled", "disabled");
            }
        }
        
        function SelServicio(secprd, obj) {
            $j("#secprd").val(secprd);
            $j("#codsvc").val(obj.value);
            $j("form#searchSvc").submit();
        }
        
        function PutParametro (tag, valor) {
            return " "+tag+"=\""+valor+"\"";
        }
        
        function AddServicio() {
            var totprd = parseInt($j("#dfCtd").val());
            
            var strXML = "";
            for (i = 0; i < totprd; i++) {
                strXML += "<servicios";
                strXML += PutParametro("Cod_Svc", $j("#cmbSrv"+i).val());
                strXML += PutParametro("Txt1_Brd", $j("#dfLinea1"+i).val());
                strXML += PutParametro("Txt2_Brd", $j("#dfLinea2"+i).val());
                strXML += PutParametro("Obs_Brd", $j("#dfObs"+i).val());
                strXML += PutParametro("Cod_FntBrd", $j("#cmbLogo"+i).val());
                strXML += PutParametro("Cod_ColBrd", $j("#cmbColor"+i).val());
                strXML += PutParametro("Cod_Lgo", $j("#cmbLogo"+i).val());
                strXML += " />\n";
            }
            $j("#parametro").val(strXML);
            $j("form#AddSvc").submit();
        }
        
        function AddPlantilla (index) {
            $j("#dfCodSvc").val($j("#cmbSrv"+index.toString()).val());
            $j("#dfCodPre").val($j("#cmbPreLine"+index.toString()).val());
            $j("#dfTxt1").val($j("#dfLinea1"+index.toString()).val());
            $j("#dfTxt2").val($j("#dfLinea2"+index.toString()).val());
            $j("#dfCodFnt").val($j("#cmbFte"+index.toString()).val());
            $j("#dfCodCol").val($j("#cmbColor"+index.toString()).val());
            $j("form#AddPtl").submit();
        }
        
        function RepoblarDetalle(xml) {
            $j("filter",xml).each(function(id) {
                filter = $j("filter",xml).get(id);
                coderr = parseInt($j("code",filter).text());
                if (coderr == 0) {
                    $j("servicios", xml).each(function(id2) {
                        parent.location.href='detalle-producto.php?producto=<?php echo $p_grpprd ?>&pagina=1&cot='+$j("value",filter).text();                    
                    });
                }
            });
        }
        
        function RepoblarPlantillas(xml) {
            $j("filter",xml).each(function(id) {
                filter = $j("filter",xml).get(id);
                coderr = parseInt($j("code",filter).text());
                if (coderr == 0) {
                    var strXML = "<table border=\"0\" width=\"100%\" id=\"tblPtl\" class=\"tabular\" align=\"center\">";
                    strXML += "<thead>";
                    strXML += "<tr class=\"tabular\">";
                    strXML += "<th class=\"tabular\" width=\"14px\">&nbsp;</th>";
                    strXML += "<th class=\"tabular\">Bordado</th>";
                    strXML += "<th class=\"tabular\">Linea 1</th>";
                    strXML += "<th class=\"tabular\">Linea 2</th>";
                    strXML += "<th class=\"tabular\">Font</th>";
                    strXML += "<th class=\"tabular\">Color</th>";
                    strXML += "</tr>";
                    strXML += "</thead>";
                    strXML += "<tbody>";
                    $j("servicios",xml).each(function(id2) {
                        servicios = $j("servicios",xml).get(id2);
                        strXML += "<tr class=\"tabular\">";

                        strXML += "<td class=\"tabular\" style=\"vertical-align: middle\">";
                        strXML += "<input type=\"radio\" name=\"seleccion\" value=\"" + $j("codptl",servicios).text() + " onclick=\"PoblarSvc(this)\" />";
                        strXML += "</td>";

                        strXML += "<td class=\"tabular\" style=\"vertical-align: middle\">";
                        strXML += $j("codptl",servicios).text();
                        strXML += "</td>";

                        strXML += "</tr>";
                    });
                    strXML += "</tbody>";
                    strXML += "</table>";
                }
            });
        }
        
        function PoblarSvc(obj) {
            var codptl = obj.value;
            var index = $j( "#accordion" ).accordion( "option", "active" );
            $j("#dfCodPtl").val(codptl);
            $j("#dfIndex").val(index);
            $j("form#SelPtl").submit();
        }
        
        function SetPlantilla(xml) {
            var index = $j("#dfIndex").val();
            $j("servicios",xml).each(function(id) {
                servicios = $j("servicios",xml).get(id);
                
                $j("#secprd").val(index);
                $j("#codsvc").val($j("codsvc",servicios).text());
                listServicios(xml);            
                
                $j("#cmbSrv"+index+" option[value="+$j("codsvc",servicios).text()+"]").attr("selected",true);
                $j("#cmbPreLine"+index+" option[value="+$j("codsvc",servicios).text()+"]").attr("selected",true);
                $j("#dfLinea1"+index).val($j("linea1",servicios).text());
                $j("#dfLinea2"+index).val($j("linea2",servicios).text());
                $j("#cmbFte"+index+" option[value="+$j("codfnt",servicios).text()+"]").attr("selected",true);
                $j("#cmbColor"+index+" option[value="+$j("codcol",servicios).text()+"]").attr("selected",true);
            });
        }
        
    </script>
    <script>
        $j(function() {
            $j( "#accordion" ).accordion();
        });
    </script> 
    
    
</head>
<body bgcolor="#c1f4e5" style="margin-top:5px;">
    <div style="overflow:auto;">
        <form ID="searchSvc" method="post" name="F1" AUTOCOMPLETE="on">
            <table width="100%">
                <tr>
                    <td style="padding: 10px;" valign="top" colspan="2">
                        <fieldset class="label_left_right_top_bottom">
                                <legend>Producto</legend>
                                <table border="0" width="100%" align="center">
                                    <tbody>
                                        <tr>
                                            <td align="left" width="80px">Usuario:</td>
                                            <td align="left" colspan="3">
                                                <b><?php echo $nombre ?></b>
                                            </td>
                                            <td align="left">L&iacute;nea:</td>
                                            <td align="left">
                                                <b><?php echo $linmca_descripcion ?></b>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td align="left" width="80px">Estilo:</td><td align="left" width="100px"><b><?php echo $dsg_style; ?></b></td>
                                            <td align="left" width="50px">Color:</td><td align="left" width="130px"><b><span id="Pat"></span></b></td>
                                            <td align="left" width="50px">Talla:</td><td align="left" width="130px"><b><span id="Sze"></span></b></td>
                                        </tr>
                                        <tr>
                                            <td align="left">Descripci&oacute;n:</td>
                                            <td colspan="3" align="left">
                                                <b><?php echo $grpprd_title; ?></b>
                                            </td>
                                            <td align="left">Cantidad:</td>
                                            <td align="left">
                                                <b><span id="Ctd"></span></b></b>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                        </fieldset>
                    </td>
                </tr>
                <tr>
                    <td width="50%">
                        <div id="accordion">
                            <?php
                                for ($i = 0; $i < $p_cantidad; $i++) {
                            ?>
                            <h3>Servicios Bordado Producto <?php echo $i+1; ?> de <?php echo $p_cantidad; ?></h3>
                            <div>
                                <p>
                                    <table border="0" width="100%" align="center">
                                        <tr>
                                            <td width="50%" style="padding: 10px;" valign="top">                        
                                                <fieldset class="label_left_right_top_bottom">
                                                        <legend>Bordados</legend>
                                                        <table border="0" width="100%" align="center">
                                                            <tbody>
                                                                <tr>
                                                                    <td width="60px" style="text-align: left">C&oacute;digo</td>
                                                                    <td style="text-align: left">
                                                                        <select name="cmbSrv<?php echo $i ?>" id="cmbSrv<?php echo $i ?>" onchange="SelServicio(<?php echo $i ?>, this)">
                                                                            <?php
                                                            $sp = mssql_query("vm_codsvc_cmb",$db);
                                                            while (($row = mssql_fetch_array($sp))) {
                                                                            ?>
                                                                            <option value="<?php echo $row['Cod_Svc'] ?>"><?php echo $row['Des_Svc']; ?></option>
                                                                            <?php
                                                            }
                                                                            ?>
                                                                        </select>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td style="text-align: left">L1:</td>
                                                                    <td style="text-align: left">
                                                                        <select name="cmbPreLine<?php echo $i ?>" id="cmbPreLine<?php echo $i ?>">
                                                                        </select> 
                                                                        <input type="text" name="dfLinea1<?php echo $i ?>" id="dfLinea1<?php echo $i ?>" value="" size="100" style="width: 195px" disabled="disabled" />
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td style="text-align: left">L2:</td>
                                                                    <td style="text-align: left">
                                                                        <input type="text" name="dfLinea2<?php echo $i ?>" id="dfLinea2<?php echo $i ?>" value="" size="100" style="width: 250px" disabled="disabled" />
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td style="text-align: left">Obs:</td>
                                                                    <td style="text-align: left">
                                                                        <textarea name="dfObs<?php echo $i ?>" id="dfObs<?php echo $i ?>" rows="4" cols="60" disabled="disabled"></textarea>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td style="text-align: left">Fuente:</td>
                                                                    <td style="text-align: left">
                                                                        <select name="cmbFte<?php echo $i ?>" id="cmbFte<?php echo $i ?>">
                                                                        </select>                                                
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td style="text-align: left">Color:</td>
                                                                    <td style="text-align: left">
                                                                        <select name="cmbColor<?php echo $i ?>" id="cmbColor<?php echo $i ?>">
                                                                        </select>                                                
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td style="text-align: left">Valor:</td>
                                                                    <td style="text-align: left">
                                                                        <span id="costo<?php echo $i ?>">$0</span>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td style="text-align: right" colspan="2">
                                                                        <input type="button" value="Guardar como Plantilla" name="btnPlantilla<?php echo $i ?>" id="btnPlantilla<?php echo $i ?>" onclick="AddPlantilla(<?php echo $i ?>)" disabled="disabled"/>
                                                                    </td>
                                                                </tr>
                                                            </tbody>
                                                        </table>

                                                </fieldset>
                                            </td>
                                        </tr>
                                    </table>
                                </p>
                            </div>
                            <?php
                                }
                            ?>
                        </div>
                    </td>
                    <td valign="top">
                            <fieldset class="label_left_right_top_bottom">
                                <legend>Plantillas</legend>
                                <div style="position:relative; width:100%; height:226px; overflow:auto; left: 0px; top: 0px;">
                                <table border="0" width="100%" id="tblPtl" class="tabular" align="center">
                                        <thead>
                                            <tr class="tabular">
                                                <th class="tabular" width="14px">&nbsp;</th>
                                                <th class="tabular">Bordado</th>
                                                <th class="tabular">Linea 1</th>
                                                <th class="tabular">Linea 2</th>
                                                <th class="tabular">Font</th>
                                                <th class="tabular">Color</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php 
                                    $totptl = 0;
                                    $sp = mssql_query("vm_s_websvcptl $cod_clt, $Cod_Per",$db);
                                    while (($row = mssql_fetch_array($sp))) {
                                            ?>
                                            <tr class="tabular">
                                                <td class="tabular" style="vertical-align: middle"><input type="radio" name="seleccion<?php echo $i ?>" value="<?php echo $row['cod_ptl']; ?>" onclick="PoblarSvc(this)" /></td>
                                                <td class="tabular" style="vertical-align: middle"><?php echo utf8_encode($row['Des_Svc']); ?></td>
                                                <td class="tabular" style="vertical-align: middle"><?php echo utf8_encode($row['Txt1_Brd']); ?></td>
                                                <td class="tabular" style="vertical-align: middle"><?php echo utf8_encode($row['Txt2_Brd']); ?></td>
                                                <td class="tabular" style="vertical-align: middle"><?php echo utf8_encode($row['Des_FntTxt']); ?></td>
                                                <td class="tabular" style="vertical-align: middle"><?php echo utf8_encode($row['Des_ColFont']); ?></td>
                                            </tr>
                                            <?php
                                            $totptl++;
                                    }
                                    if ($totptl == 0) {
                                        ?>
                                            <tr class="tabular">
                                                <td class="tabular">&nbsp;</td>
                                                <td class="tabular">&nbsp;</td>
                                                <td class="tabular">&nbsp;</td>
                                                <td class="tabular">&nbsp;</td>
                                                <td class="tabular">&nbsp;</td>
                                                <td class="tabular">&nbsp;</td>
                                            </tr>
                                            <?php
                                    }

                                            ?>
                                        </tbody>
                                </table>
                                </div>
                            </fieldset>
                        
                    </td>
            </table>
            <input type="hidden" id="secprd" name="secprd" value="" />
            <input type="hidden" id="codsvc" name="codsvc" value="" />
            <input type="hidden" id="parametro" name="parametro" value="" />
        </form>
    </div>
    <div>
        <form ID="AddSvc" name="AddSvc" method="post" AUTOCOMPLETE="on" action="carrito2.php">
            <input type="hidden" id="dfDsg" name="dfDsg" value="" />
            <input type="hidden" id="dfPat" name="dfPat" value="" />
            <input type="hidden" id="dfSze" name="dfSze" value="" />
            <input type="hidden" id="dfCtd" name="dfCtd" value="" />
            <!--input type="button" style="float:left;" class="btn" value="Continuar" onclick="parent.location.href='carrito2.php'" /-->
            <input type="button" style="float:left; width: 100px" class="btn" value="Enviar" onclick="AddServicio()" >
        </form>
        <form ID="AddPtl" name="AddPtl" method="post" AUTOCOMPLETE="on">
            <input type="hidden" id="dfCodSvc" name="dfCodSvc" value="" />
            <input type="hidden" id="dfCodPre" name="dfCodPre" value="" />
            <input type="hidden" id="dfTxt1" name="dfTxt1" value="" />
            <input type="hidden" id="dfTxt2" name="dfTxt2" value="" />
            <input type="hidden" id="dfCodFnt" name="dfCodFnt" value="" />
            <input type="hidden" id="dfCodCol" name="dfCodCol" value="" />
        </form>
        <form ID="SelPtl" name="SelPtl" method="post" AUTOCOMPLETE="on">
            <input type="hidden" id="dfCodPtl" name="dfCodPtl" value="" />
            <input type="hidden" id="dfIndex" name="dfIndex" value="" />
        </form>
    </div>
    <script type="text/javascript">
        var ValSze = parent.document.F2.dfSze.value;
        var StrPat = parent.document.F2.dfKeyPat.value;
        var CodPat = parent.document.F2.dfPat.value;
        var StrCtd = parent.document.F2.cantidad.value;
        
        var KeyPat = StrPat.substr(0,StrPat.indexOf("_"));
        
        var Sze = document.getElementById('Sze');
        var Pat = document.getElementById('Pat');
        var Ctd = document.getElementById('Ctd');
        
        Sze.innerHTML = ValSze.toString();
        Pat.innerHTML = KeyPat.toString();
        //Ctd.innerHTML = StrCtd.toString();
        Ctd.innerHTML = "<?php echo $p_cantidad ?>";
        
        var dfDsg = document.getElementById('dfDsg');
        dfDsg.value = "<?php echo $dsg_iddsg; ?>";

        var dfPat = document.getElementById('dfPat');
        dfPat.value = CodPat.toString();
        
        var dfSze = document.getElementById('dfSze');
        dfSze.value = ValSze.toString();        
        
        var dfCtd = document.getElementById('dfCtd');
        //dfCtd.value = StrCtd.toString();  
        dfCtd.value = "<?php echo $p_cantidad ?>";
        
    </script>
</body>
</html>
