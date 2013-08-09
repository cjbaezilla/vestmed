<?php
//Obtengo los datos de conexion de la base de datos
ini_set('display_errors', '0');
session_start();
include("../config.php");

$Cod_Per = 0;
$Cod_Clt = 0;
$RutClt = "";
$RutPer = "";
$accion = "nuevo";
$xis = 0;
$EsCliente = false;
$bExisteUsr = false;
$flagReadOnlyClt = false;
$flagReadOnlySuc = false;
$flagReadOnlyCtt = false;
if (isset($_SESSION['CodPer'])) $Cod_Per = intval($_SESSION['CodPer']);
if (isset($_SESSION['CodClt'])) $Cod_Clt = intval($_SESSION['CodClt']);
$ret = isset($_GET['ret']) ? ok($_GET['ret']) : 0;
if (isset($_GET['clt'])) {
	$RutClt = ok($_GET['clt']);
	if (!strrpos($RutClt,"-")) $RutClt = substr($RutClt, 0, -1)."-".substr($RutClt, -1);
	$xis     = intval(ok($_GET['xis']));
	$Cod_Suc = isset($_GET['suc']) ? ok($_GET['suc']) : 0;
	if ($xis == 1) { // Caso existe como Persona en Vestmed
		$doc_id = 1;
		//$query = "vm_s_per_tipdoc ".$doc_id.", '".$RutClt."'";
		$result = mssql_query ("vm_s_per_tipdoc ".$doc_id.", '".$RutClt."'", $db)	or die ("No se pudo leer datos del Cliente");
		if (($row = mssql_fetch_array($result))) {
			$cod_tipper = $row["Cod_TipPer"];
			$Cod_Clt 	= $row["Cod_Clt"];
			$id_per	 	= $row["Cod_Per"];
			mssql_free_result($result); 

			if ($Cod_Clt != "") { // Si es cliente
				$EsCliente = true;
				$CodPro = -1;
				$CodEsp = -1;
				$accion = "newsuc";
				//$query = "vm_suc_s ".$Cod_Clt.", ".$Cod_Suc;
				$result = mssql_query ("vm_suc_s ".$Cod_Clt.", ".$Cod_Suc, $db)
								or die ("No se pudo leer datos de la Sucursal (".$Cod_Clt.")");
				if ($row = mssql_fetch_array($result)) {
					$DirSuc = $row["Dir_Suc"];
					$NomSuc = utf8_encode($row['Nom_Suc']);
					$CodCmn = $row["Cod_Cmn"];
					$CodCdd = $row["Cod_Cdd"];
					$FonSuc = $row["Fon_Suc"];
					$FaxSuc = $row["Fax_Suc"];
					mssql_free_result($result); 
					//$flagReadOnlySuc = true;
				}
			}
			
			$NomCmn = "";
			//$query = "vm_cmn_s ".$CodCmn;
			$result = mssql_query ("vm_cmn_s ".$CodCmn, $db)
							or die ("No se pudo leer Codigo de la Comuna");
			if ($row = mssql_fetch_array($result))	$NomCmn = utf8_encode ($row["Nom_Cmn"]);
			mssql_free_result($result); 
			
			$NomCdd = "";
			//$query = "vm_cdd_s ".$CodCdd;
			$result = mssql_query ("vm_cdd_s ".$CodCdd, $db)
							or die ("No se pudo leer Codigo de la Ciudad");
			if ($row = mssql_fetch_array($result))	$NomCdd = utf8_encode ($row["Nom_Cdd"]);
			mssql_free_result($result); 
		}
	}
}	
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/tr/xhtml1/DTD/xhtml1-transitional.dtd">
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
<script type="text/javascript">
new UvumiDropdown('dropdown-scliente');
</script>
<link href="../Include/estilos.css" type="text/css rel=stylesheet" />
<script type="text/javascript" src="../Include/SoloNumeros.js"></script>
<script type="text/javascript" src="../Include/ValidarDataInput.js"></script>
<script type="text/javascript" src="../Include/fngenerales.js"></script>
<script type="text/javascript" src="../Include/validarRut.js"></script>
<script type="text/javascript" src="../js/jquery-1.3.2.min.js"></script>
<script type="text/javascript">
    var $j = jQuery.noConflict();
    $j(document).ready
	(
            //$j(":input:first").focus();

            function()
            {
            $j("form#searchCmn").submit(function(){
                            $j.post("../ajax-search-cdd.php",{
                                    search_type: "cdd",
                                    param_filter: $j("#cmn").val()
                            }, function(xml) {
                                    listLinCdd(xml);
                            });return false;
                });

            //NO SUBMIT TODAVIA $j("form#patternlist-form").submit();
            }
		
	);
    //TODO: No se esta realizando el post, probablemente debido a que falta el evento submit.
    

    function filterCmn(obj)
    {
        f2.codcmn.value = obj.value;
        //$j("#codcmn").val(obj.value);
        $j("form#searchCmn").submit();
    }
	
	
    function llenarCdd(obj)
    {
        f2.codcdd.value = obj.value;
        //$j("#.codcdd").val(obj.value);
    }
	
	
    function listLinCdd(xml)
    {
        options="<select id=\"cdd\" name=\"cdd\" class=\"textfieldv2\" onChange=\"llenarCdd(this)\">\n";
        options+="<option selected value=\"_NONE\">Seleccione una Ciudad</option>\n";
        $j("filter",xml).each(
            function(id) {
                filter=$j("filter",xml).get(id);
                options+= "<option value=\""+$j("code",filter).text()+"\">"+$j("value",filter).text()+"</option>\n";
            }
        );
        options+="</select>";
        $j("#cdd").replaceWith(options);
    }

    function llenarCampo(obj) {
        var campo;

        campo=obj.name.substring(0,obj.name.length-2);
        eval("f2."+campo).value = obj.value;
    }
</script>
</head>
<body>
<div id="body">
   <div id="header"></div>
   <div id="work">
            <div id="back-registro">
            <div style="width:765px; margin:0 auto 0 160px; padding-top:10px;">
                <table WIDTH="80%" BORDER="0" style="text-align:left;">
                        <tr>
                        <td width="80%" VALIGN="top">
                                 <table border="0" cellpadding="1" cellspacing="0" width="100%" >
                                        <tr><td colspan=2 CLASS="etiqueta">Datos de la Sucursal</td></tr>
                                        <tr>		
                                           <td CLASS="etiqueta">Nombre:&nbsp;</td>
                                           <td><input name="dfNomSucIn" size="30" maxLength="30" class="textfieldv2" style="TEXT-TRANSFORM: uppercase" onchange="llenarCampo(this)" value="<?php echo $NomSuc; ?>" <?php if ($flagReadOnlySuc) echo "readOnly" ?>></td>
                                        </tr>
                                        <tr>		
                                           <td CLASS="etiqueta">Direcci&oacute;n:&nbsp;</td>
                                           <td><input name="dfDireccionIn" size="60" maxLength="80" class="textfieldv2" style="TEXT-TRANSFORM: uppercase" onchange="llenarCampo(this)" value="<?php echo $DirSuc; ?>" <?php if ($flagReadOnlySuc) echo "readOnly" ?>></td>
                                        </tr>
                                        <tr>
                                                <td CLASS="etiqueta">Comuna:&nbsp;</td>
                        <?php if ($xis >= 0) { ?>
                        <td align="left">
                            <form id="searchCmn">

                            <select id="cmn" name="cmn" class="textfieldv2" onChange="filterCmn(this)">
                                    <option selected value="_NONE">Seleccione una Comuna</option>
                                    <?php //Seleccionar las ciudades
                                    $sp = mssql_query("vm_cmn_s",$db);
                                    while($row = mssql_fetch_array($sp))
                                    {
                                            ?>
                                    <option value="<?php echo $row['Cod_Cmn'] ?>"<?php if ($row['Cod_Cmn'] == $CodCmn) echo " selected"; ?>><?php echo utf8_encode($row['Nom_Cmn']) ?></option>
                                            <?php
                                    }
                                    ?>
                            </select>
                            </td>
                            </form>
                            <?php } else { ?>
                            <td align="left">
                            <input name="dfNomCmn" size="20" maxLength="20" class="textfieldv2" style="TEXT-TRANSFORM: uppercase" readOnly value="<?php echo $NomCmn; ?>" />
                            </td>										
                            <?php } ?>
                </tr>
                <tr>

                    <td class="etiqueta">Ciudad:&nbsp;</td>

                                                                <td><?php if ($xis >= 0) { ?>
                                                                <form id="searchCdd" name="searchCdd">
                                                                <select id="cdd" name="cdd" class="textfieldv2" onChange="llenarCdd(this)">
                                                                        <option selected value="_NONE">Seleccione una Ciudad</option>
                                                                        <?php //Seleccionar las ciudades
                                                                        $sp = mssql_query("vm_cddcmn_s NULL, $CodCmn",$db);
                                                                        while($row = mssql_fetch_array($sp))
                                                                        {
                                                                                ?>
                                                                                <option value="<?php echo $row['Cod_Cdd'] ?>"<?php if ($row['Cod_Cdd'] == $CodCdd) echo " selected"; ?>><?php echo utf8_encode($row['Nom_Cdd']) ?></option>
                                                                                <?php
                                                                        }
                                                                        ?>
                                                                </select>
                                                                </td>
                                                                </form>
                                                                <?php } else { ?>
                                                                <input name="dfNomCdd" size="20" maxLength="20" class="textfieldv2" style="TEXT-TRANSFORM: uppercase" readOnly value="<?php echo $NomCdd; ?>">
                                                                </td>										
                                                                <?php } ?>
                                        </tr>
                                        <tr>		
                                           <td CLASS="etiqueta">Tel&eacute;fono&nbsp;<SPAN class=dator>(1)</SPAN>:&nbsp;</td>
                                           <td align="left">
                                                                 <input name="dfTelefonoSucIn" size="12" maxLength="15" class="textfieldv2" onKeyPress="SoloNumeros(this)"  onchange="llenarCampo(this)" value="<?php echo $FonSuc; ?>" <?php if ($flagReadOnlySuc) echo "readOnly" ?>>
                                                                </td>
             </tr>
                                        <tr>
                <td CLASS="etiqueta"><span class=dato>FAX&nbsp;<SPAN class=dator>(1)</SPAN>:</span>&nbsp;</td>
                <td align="left">

                     <input name="dfFaxSucIn" size="12" maxLength="15" class="textfieldv2" onKeyPress="SoloNumeros(this)" onchange="llenarCampo(this)" value="<?php echo $FaxSuc; ?>" <?php if ($flagReadOnlySuc) echo "readOnly" ?>></td>
                                        </tr>
                                        <tr>
                                                <td CLASS ="label_top" colspan="2">&nbsp;</td>
                                        </tr>
                                        <tr>
                                                <td colspan="2" CLASS="dato">
                <span style="float:left; width:30px;color:#6abfbf;">(1)</span>
                                                        <span style="float:left;">Incluya c&oacute;digo de &aacute;rea para tel&eacute;fonos fuera de la Capital </span>
                                                </td>
                                        </tr>
                                        <tr><td colspan="2" CLASS="dato">&nbsp;</td></tr>
                                        </tr>
                                  </table>
                          </td>   
                        </tr>
                        <tr><td>
                        <form ID="F2" method="post" name="F2" action="ing_sucursal.php?ret=<?php echo $ret; ?>"
                                  <?php if (!$bExisteUsr) echo "onsubmit=\"return checkDataFichaUsr(this,".$cod_tipper.")\""; ?> AUTOCOMPLETE="on">
                                  <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                        <tr>
                                                <td class="datoc" width="20%">&nbsp;</td>
                                                <td class="datoc" width="20%">
                                                        <input type="submit" name="Enviar" value="Enviar" class="btn">
                                                </td>
                                                <td class="datoc" width="20%">
                                                        <input type="BUTTON" name="Cerrar" value="Cerrar" class="btn"
                                                                   onClick="javascript:window.close()">
                                                </td>
                                                <td class="datoc" width="20%">
                                                <?php if ($ret == 1) { ?>
                                                &nbsp;
                                                <?php } else { ?>
                                                        <input type="BUTTON" name="Volver" value="Volver" class="btn"
                                                                   onClick="javascript:history.back()">
                                                <?php } ?>
                                                </td>
                                        </tr>
                                  </table>
                                  <input type="hidden" name="dfRutClt" value="<?php echo $RutClt; ?>" />
                                  <input type="hidden" name="dfCodSuc" value="<?php echo $Cod_Suc; ?>" />
                                  <input type="hidden" name="dfNomSuc" value="<?php echo $NomSuc; ?>" />
                                  <input type="hidden" name="dfDireccion" value="<?php echo $DirSuc; ?>" />
                                  <input type="hidden" name="dfTelefonoSuc" value="<?php echo $FonSuc; ?>" />
                                  <input type="hidden" name="dfFaxSuc" value="<?php echo $FonSuc; ?>" />
                                  <input type="hidden" name="codcmn" value="<?php echo $CodCmn; ?>" />
                                  <input type="hidden" name="codcdd" value="<?php echo $CodCdd; ?>" />
                        </form>
                        </td></tr>
                </table>
            </div>
        </div>
    </div>
    <div id="footer"></div>
</div>
<script type="text/javascript">
	var f1;	
	var f2;
	var f3;
	
	f1 = document.F1;	
	f2 = document.F2;
<?php
  if (isset($_GET['accion'])) {
	if ($_GET['accion'] == "closeing") {
		if ($page = ""){
			$page = "detalle-cotizacion.php";			
		}else{
			?>
			parent.opener.location.href=parent.opener.location.href;
			<?php
		}
		
	}
	else {
            $page  = "nueva_cot.php?rut=".(($cod_tipper == 2) ? $RutClt : $RutPer);
            if ($Cod_Suc > 0) $page .= "&suc=".$Cod_Suc."&ctt=".$Cod_Ctt;
	}
    echo "	parent.opener.document.F2.action=\"".$page."\"\n";
    echo "	parent.opener.document.F2.submit();\n";
	echo "	window.close();\n";
  }
?>
</script>
</body>
</html>