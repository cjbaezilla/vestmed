<?php
//Obtengo los datos de conexion de la base de datos
ini_set('display_errors', '0');
session_start();
include("config.php");

$Cod_Per = 0;
$Cod_Clt = 0;
$cod_cot = 0;
if (isset($_SESSION['CodPer'])) $Cod_Per = intval($_SESSION['CodPer']);
if (isset($_SESSION['CodClt'])) $Cod_Clt = intval($_SESSION['CodClt']);
if (isset($_SESSION['CodCot'])) $cod_cot = intval($_SESSION['CodCot']);
else if (isset($_GET['cot'])) {
    $cod_cot = intval($_GET['cot']);
    $_SESSION['CodCot'] = $cod_cot; 
}
$Tip_Usr = "C";
$result = mssql_query ("vm_get_tipusr $Cod_Clt, $Cod_Per", $db) 
          or die ("No se pudo leer datos del usuario <br>"."vm_get_tipusr $Cod_Clt, $Cod_Per");
if (($row = mssql_fetch_array($result))) $Tip_Usr = $row["Tip_Usr"];

//Obtengo informacion relacionada al producto
$p_grpprd = ok($_GET['producto']);  
$p_title = ok($_GET['title']); 
$pagina = isset($_GET['pagina']) ? ok($_GET['pagina']) : 1;


$select_prod = mssql_query("vm_strinv_prodinfo '".$p_grpprd."'", $db);
$totalrows = mssql_num_rows($select_prod);

if($totalrows==0)
      echo "El producto seleccionado ya no est&aacute; en nuestra base de datos";
else
{
    //Datos del producto
    $row = mssql_fetch_array($select_prod);
    $grpprd_title = str_replace("#", "'",$row["title"]);
    $dsg_style = $row["style"];                 $cod_grppat = $row["cod_grppat"];
    $dsg_image = $row["image"];                 $dsg_marca = $row["marca"];
    $dsg_iddsg = $row["id_dsg"];                $grpprd_descripcion = str_replace("#", "'",utf8_encode($row["grp_desc"]));
    $dsg_name = str_replace("#", "'",utf8_encode($row["nom_dsg"]));
    $p_coddsg = ok($dsg_iddsg);
    //Datos de la marca
    $select_marca = mssql_query("vm_strmrc_nom '".$p_coddsg."'",$db);
    $rowx = mssql_fetch_array($select_marca);
    $mca_nombre = $rowx["mca_nombre"];           $mca_pais = $rowx["mca_pais"];
    $mca_ciudad = $rowx["mca_ciudad"];           $mca_direccion = $rowx["mca_direccion"];
    $mca_zip = $rowx["mca_zip"];                 $mca_fono = $rowx["mca_fono"];
    $mca_fax = $rowx["mca_fax"];                 $mca_web = $rowx["mca_web"];
    $mca_shipping = $rowx["mca_shipping"];       $mca_descripcion = utf8_encode($rowx["mca_descripcion"]);
    $linmca_codigo = $rowx["linmca_codigo"];     $linmca_descripcion = utf8_encode($rowx["linmca_descripcion"]);
    $mat_descripcion = utf8_encode($rowx["mat_descripcion"]); 
    $hoy = date('Ymd');
    mssql_free_result($row);    
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Detalle de Producto - Vestmed Vestuario M&eacute;dico</title>
<link href="css/layout.css" type="text/css" rel="stylesheet" />
<link href="css/clearfix.css" type="text/css" rel="stylesheet" />
<style type="text/css">@import url(milkbox2.2.1/css/milkbox/milkbox.css);</style>
<script type="text/javascript" src="js/mootools-1.2.1-core-yc.js"></script>
<script type="text/javascript" src="js/mootools-1.2-more.js"></script>
<script type="text/javascript" src="dropdown/js/dropdown.js"> </script>
<link rel="stylesheet" type="text/css" media="screen" href="dropdown/css/uvumi-dropdown.css" />
 <!--[if IE]>
   <link rel="stylesheet" type="text/css" media="screen" href="dropdown/css/uvumi-dropdown-ie.css" />
   <![endif]-->
<script language="JavaScript" src="Include/ValidarDataInput.js"></script>
<script language="JavaScript" src="Include/SoloNumeros.js"></script>
<script language="JavaScript" src="Include/validarRut.js"></script>
<script language="JavaScript" src="Include/floater_window.js"></script>
<script language="javascript">
window.addEvent('scroll', function(){
	xx = window.getScroll();
	if(xx.y >= 377) $('seleccion').tween('top', xx.y-377);
	else $('seleccion').tween('top', 0);
});
window.addEvent('domready', function(){
	$('buscar-talla').addEvent('click', function(){
		$('busqueda-centro').addClass('selected-buscar');
		$('wrap-estamp-producto').removeClass('selected-buscar');
	});
	$('buscar-pattern').addEvent('click', function(){
		$('wrap-estamp-producto').addClass('selected-buscar');
		$('busqueda-centro').removeClass('selected-buscar');
	});
});

</script>
<script type="text/javascript">
    new UvumiDropdown('dropdown-scliente');
	
	function EnviarClave() {
	   f1.action = "aviso.php?idmsg=20";
	   f1.submit();
	}
</script>
<!-- Lytebox Includes //-->
<script type="text/javascript" src="lytebox_v5.5/lytebox.js"></script>
<link rel="stylesheet" type="text/css" href="lytebox_v5.5/lytebox.css" media="screen" />
<!-- Lytebox Includes //-->
<!--script type="text/javascript" src="js/jquery-1.3.2.min.js"></script-->
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.8/jquery.js"></script>
<script src="http://code.jquery.com/ui/1.9.0/jquery-ui.min.js"></script>
<script type="text/javascript">
        var $j = jQuery.noConflict();
        
        
        $j(document).ready(function(){
            $j("form#patternlist-form").submit(function(){
                $j.post("ajax-pattern.php",{
                        id_dsg: $j("#id_dsg").val(),
                        id_pat: $j("#id_pat").val(),
                        key_pat: $j("#key_pat").val(),
                        id_grpprd: $j("#dfPrd").val()
                    }, function(xml) {
                    listPatterns(xml);
                });
                return false;
            });

            $j("form#patternlist-form-sze").submit(function(){
                $j.post("ajax-sze-pattern.php",{
                        id_dsg: $j("#id_dsg").val(),
                        id_sze: $j("#id_sze").val(),
                        id_grpprd: $j("#dfPrd").val()
                    }, function(xml) {
                    listColores(xml);
                });
                return false;
            });
            
            <?php
            if($_GET['colorSel']!=null)
            {
                ?>
                selectPattern('<?php echo $_GET['colorSel'] ?>','<?php echo $_GET['desc'] ?>');
                <?php
            }
            else
            {
                ?>
                $j("form#patternlist-form").submit();
                <?php
            }
            ?>
        });

        function selectPattern(par_pat, par_key, oferta, stock)
        {
            if (parseInt(stock) == 0) {
                alert('Color sin stock');
                return false;
            }
            if (f2.dfModo.value == "_NONE") {
                f2.dfModo.value = "COLOR";
            }
            if (f2.dfModo.value == "COLOR" || f2.dfModo.value == "_NONE" || f2.dfModo.value == "LIMPIAR") {
                f2.dfPat.value = par_pat;
                f2.dfKeyPat.value = par_key;
                var loader="<div id=\"wrap-tabla-tallas\"><img src=\"images/ajax-loader.gif\"/> Cargando tallas, espere un momento ...</div>";
                $j("#wrap-tabla-tallas").replaceWith(loader);
                $j("#id_pat").val(par_pat);
                $j("#key_pat").val(par_key);
                $j("form#patternlist-form").submit();
            }
            else {
                var tope = (stock > 30 ? 30 : stock);
                
                wrapPat="<div id=\"wrap-pattern-seleccionado\">\n"+"\t<div>Pattern Seleccionado</div>\n";
                //Si se selecciono el patron generico, o un patron especifico:
                if(par_pat != "_ALL")
                {
                    $j("#img-estampado-sel").replaceWith("<li id=\"img-estampado-sel\"><img src=\"imagedisplay.php?name=img_pattern&filter="+par_pat+"\" height=\"80px\" width=\"80px\" /></li>");
                    wrapPat+="<div style=\"height=80px; width=80px\">";
                    descripcion = par_key.replace("_BR_"," ");
                    if (oferta != 0) 
                    {
                        wrapPat+="<div style=\"position: absolute; background: url(images/catalogo1/PRD_40px.png) no-repeat; width: 40px; height: 40px;\"></div>\n";
                        descripcion += "<br>$ " + formatearMillones(oferta.toString());
                    }
                    wrapPat+="\t<img id=\"pattern\" src=\"imagedisplay.php?square=true&name=img_pattern&filter="+par_pat+"\"  /></div>\n";
                    wrapPat+="<div id=\"pattern-nombre\">"+descripcion+"</div>";
                    $j("#wrap-pattern-seleccionado").replaceWith(wrapPat);
                    
                    var cantidad  = "<select name=\"cantidad\" id=\"cantidad\" onchange=\"CambiarLink('<?php echo $p_grpprd; ?>', this)\">";
                    cantidad += "<option selected=true value=\"1\">1</option>";
                    for (i=2; i<=tope; i++)
                        cantidad += "<option value=\"" + i + "\">" + i + "</option>";
                    cantidad += "</select>";
                    $j("#cantidad").replaceWith(cantidad);
                    
                    f2.dfPat.value = par_pat;                    
                    f2.dfKeyPat.value = par_key;
                }
            }
        }

        function selectSize(par_sze, stock, cod_sze)
        {
            var cantidad;
            var tope;
            if (stock == 0) {
                alert('Talla sin stock');
                return false;
            }
            tope = (stock > 30 ? 30 : stock);
            if (f2.dfModo.value == "_NONE") {
                f2.dfModo.value = "SIZE";
            }
            else if (f2.dfModo.value == "LIMPIAR")
                f2.dfModo.value = "_NONE";
            
            $j("#talla").replaceWith("<div id=\"talla\">"+((par_sze=="_ALL")?"TODAS":par_sze)+"</div>");
            f2.dfSze.value = (par_sze=="_ALL") ? "TODAS" : par_sze;
            f2.dfCodSze.value = (par_sze=="_ALL") ? "TODAS" : cod_sze;
            cantidad  = "<select name=\"cantidad\" id=\"cantidad\">";
            cantidad += "<option selected=true value=\"1\">1</option>";
            for (i=2; i<=tope; i++)
                cantidad += "<option value=\"" + i + "\">" + i + "</option>";
            cantidad += "</select>";
            $j("#cantidad").replaceWith(cantidad);
            
            if (f2.dfModo.value == "SIZE") {
                var strXML = "<ul id=\"listado-estampados\">";
                strXML += "<input type=\"hidden\" id=\"id_dsg\" name=\"id_dsg\" value=\"<?php echo $dsg_iddsg; ?>\" />";
                strXML += "<li><img src=\"images/ajax-loader.gif\" width=\"40px\" height=\"40px\" /> Cargando colores, espere un momento ...</li><br/>";
                strXML += "<li>";
                strXML += "<input type=\"hidden\" id=\"id_pat\" name=\"id_pat\" value=\"_ALL\" />";
                strXML += "<input type=\"hidden\" id=\"key_pat\" name=\"key_pat\" value=\"Sin seleccionar patr&oacute;n\" />";
                strXML += "</li>";
                strXML += "</ul>";
                $j("#listado-estampados").replaceWith(strXML);
                
                strTabSze="<div id=\"wrap-tabla-tallas\"><table><tr><td style=\"border: none; font-weight:bold; font-size:16px; height: 24px; width: 200px; text-align: left\">TALLA: " + par_sze + "</td></tr></table></div>";
                $j("#wrap-tabla-tallas").replaceWith(strTabSze);                
                
                $j("#id_sze").val(cod_sze);
                $j("form#patternlist-form-sze").submit();
            }
        }

        function previewPattern(par_pat, par_key, oferta, stock)
        {
            $j("#descrip-est").replaceWith("<div id=\"descrip-est\">"+par_key.replace("_BR_","<br />")+"</div>");
            if(par_pat!="_ALL") {
                var texto = "<li id=\"img-estampado-sel\"><div style=\"width: 80px; height: 80px\">";
                if (stock == 0)
                    texto += "<div style=\"position: absolute; background: url(images/catalogo1/cross.png); width: 16px; height: 16px;\"></div>";
                else if (oferta > 0)
                    texto += "<div style=\"position: absolute; background: url(images/catalogo1/PRD_40px.png); width: 40px; height: 40px;\"></div>";
                texto += "<img src=\"imagedisplay.php?name=img_pattern&filter="+par_pat+"\" height=\"80px\" width=\"80px\" /></div></li>";
                $j("#img-estampado-sel").replaceWith(texto);
            }
            else
                $j("#img-estampado-sel").replaceWith("<li id=\"img-estampado-sel\"><img src=\"images/sin_patron.gif\" height=\"80px\" width=\"80px\" /></li>");
        }

        function listPatterns(xml) {
            //Patron seleccionado
            $j("selpat",xml).each(function(id) {
                selpat = $j("selpat",xml).get(id);
                wrapPat="<div id=\"wrap-pattern-seleccionado\">\n"+"\t<div>Pattern Seleccionado</div>\n";
                //Si se selecciono el patron generico, o un patron especifico:
                if($j("code",selpat).text()!="_ALL")
                {
                    $j("#img-estampado-sel").replaceWith("<li id=\"img-estampado-sel\"><img src=\"imagedisplay.php?name=img_pattern&filter="+$j("code",selpat).text()+"\" height=\"80px\" width=\"80px\" /></li>");
                    wrapPat+="<div style=\"height=80px; width=80px\">";
                    descripcion = $j("desc",selpat).text().replace("_BR_"," ");
                    if ($j("precio",selpat).text() != "0") 
                    {
                        wrapPat+="<div style=\"position: absolute; background: url(images/catalogo1/PRD_40px.png) no-repeat; width: 40px; height: 40px;\"></div>\n";
                        descripcion += "<br>$ " + formatearMillones($j("precio",selpat).text());
                    }
                    wrapPat+="\t<img id=\"pattern\" src=\"imagedisplay.php?square=true&name=img_pattern&filter="+$j("code",selpat).text()+"\"  /></div>\n";
                    wrapPat+="<div id=\"pattern-nombre\">"+descripcion+"</div>";
                    $j("#wrap-pattern-seleccionado").replaceWith(wrapPat);
                }
                else
                {
                    $j("#img-estampado-sel").replaceWith("<li id=\"img-estampado-sel\"><img src=\"images/sin_patron.gif\" height=\"80px\" width=\"80px\" /></li>");
                    wrapPat+="<div style=\"height=80px; width=80px\">";
                    wrapPat+="\t<img id=\"pattern\" src=\"images/sin_patron.gif\" /></div>\n";
                    wrapPat+="<div id=\"pattern-nombre\">"+"Sin patr&oacute;n"+"</div>";
                    $j("#wrap-pattern-seleccionado").replaceWith(wrapPat);
                }
                $j("#descrip-est").replaceWith("<div id=\"descrip-est\">"+$j("desc",selpat).text().replace("_BR_","<br />")+"</div>");
                $j("#muestra-color").replaceWith("<img src=\"imagedisplay.php?name=img_pattern&filter="+$j("code",selpat).text()+"\" id=\"muestra-color\" />");
                
                tope = parseInt($j("stock",selpat).text()) > 30 ? 30 : parseInt($j("stock",selpat).text());
            });

            if (f2.dfModo.value == "COLOR" || f2.dfModo.value == "_NONE" || f2.dfModo.value == "LIMPIAR") {
                if (f2.dfModo.value == "LIMPIAR") {
                    f2.dfSze.value = "";
                }
                if (f2.dfModo.value == "COLOR") $j("#limpiar").show();

                //Tabla Sizes
                i=0;
                count=0;
                ttal=1;
                fila=1;
                strTabSze="<div id=\"wrap-tabla-tallas\">\n\t<table>\n\t\t<tr>\n";
                $j("size",xml).each(function(id) {
                    selsze = $j("size",xml).get(id);
                    if (parseInt($j("stock",selsze).text()) == 0) color = '#FF0000';
                    else if (parseInt($j("stock",selsze).text()) == 1) color = '#FFFF00';
                    else color = '#33CC33';
                    strTabSze+="\t\t\t<td class=\"sel_talla\" style=\"background-color: " + color + "\" onclick=\"selectSize('"+$j("val",selsze).text()+"'," + $j("stock",selsze).text()+"," + $j("code",selsze).text() + ")\">"+$j("val",selsze).text()+"</td>\n";
                    i++;
                    ttal++;
                    count++;
                    if(i==7)
                    { 
                        strTabSze+="\t\t</tr><tr>"; i=0; ttal=1; 
                        fila++;
                    }
                });
                if(ttal != 7 && fila > 1) for(i=ttal;i<=8; i++) strTabSze+="<td class=\"noclick\"></td>";
                strTabSze+="\t\t</tr>\n\t</table>\n</div>";
                //Chequear si no hay tallas disponibles
                strTabSze=(count==0)?"<div id=\"wrap-tabla-tallas\">No hay tallas disponibles para este color</div>":strTabSze;
                $j("#wrap-tabla-tallas").replaceWith(strTabSze);
                $j("#talla").replaceWith("<div id=\"talla\">"+"NONE"+"</div>");
                
                if (f2.dfModo.value == "LIMPIAR") {
                    var strXML = "<ul id=\"listado-estampados\">";
                    strXML += "<input type=\"hidden\" id=\"id_dsg\" name=\"id_dsg\" value=\"<?php echo $dsg_iddsg; ?>\" />";
                    strXML += "<li><img src=\"images/ajax-loader.gif\" width=\"40px\" height=\"40px\" /> Cargando colores, espere un momento ...</li><br/>";
                    strXML += "<li>";
                    strXML += "<input type=\"hidden\" id=\"id_pat\" name=\"id_pat\" value=\"_ALL\" />";
                    strXML += "<input type=\"hidden\" id=\"key_pat\" name=\"key_pat\" value=\"Sin seleccionar patr&oacute;n\" />";
                    strXML += "</li>";
                    strXML += "</ul>";
                    $j("#listado-estampados").replaceWith(strXML);

                    $j("#id_pat").val('_ALL');
                    $j("#key_pat").val('Sin seleccion');
                    $j("#id_sze").val("_ALL");
                    $j("form#patternlist-form-sze").submit();
                }
            }
            
	    if (f2.dfSze.value != "") selectSize(f2.dfSze.value, tope, f2.dfCodSze.value);
	    else f2.dfSze.value = (count==0)?"_NONE":"_ALL";
        }
        
        function listColores(xml) {
            var strXML = "<ul id=\"listado-estampados\">";
            var j = 0;
            //strXML += "<form id=\"patternlist-form\">";
            strXML += "<input type=\"hidden\" id=\"id_dsg\" name=\"id_dsg\" value=\"<?php echo $dsg_iddsg; ?>\" />";
            $j("pat",xml).each(function(id) {
                selpat = $j("pat",xml).get(id);
                //alert($j("code",selpat).text() + "-" + $j("key",selpat).text() + "-" + $j("stock",selpat).text());
                if (parseInt($j("stock",selpat).text()) > 0) {
                    strXML += "<li><div style=\"width: 50px; height: 50px\">";
                    if ($j("oferta",selpat).text() != "0")
                        strXML += "<div style=\"position: absolute; background: url(images/catalogo1/PRD_20px.png); width: 20px; height: 20px;\"></div>";
                    strXML += "<img src=\"imagedisplay.php?name=img_pattern&filter=" + $j("code",selpat).text() + "\" width=\"50px\" height=\"50px\"";
                    strXML += "     onclick=\"selectPattern('" + $j("code",selpat).text() + "', '" + $j("key",selpat).text() + "_BR_" + $j("des",selpat).text() + "',"+ $j("oferta",selpat).text() + "," + $j("stock",selpat).text() + ")\"";
                    strXML += "     onmouseover=\"previewPattern('" + $j("code",selpat).text() + "', '" + $j("key",selpat).text() + "_BR_" + $j("des",selpat).text() + "',"+ $j("oferta",selpat).text() + "," + $j("stock",selpat).text() + ")\"";
                    strXML += " />";
                    strXML += "</div></li>";

                    if (++j == 11) {
                        strXML += "<br />";
                        j = 0;
                    }
                }
                wrapPat="<div id=\"wrap-pattern-seleccionado\">\n"+"\t<div>Pattern Seleccionado</div>\n";
                $j("#img-estampado-sel").replaceWith("<li id=\"img-estampado-sel\"><img src=\"images/sin_patron.gif\" height=\"80px\" width=\"80px\" /></li>");
                wrapPat+="<div style=\"height=80px; width=80px\">";
                wrapPat+="\t<img id=\"pattern\" src=\"images/sin_patron.gif\" /></div>\n";
                wrapPat+="<div id=\"pattern-nombre\">"+"Sin patr&oacute;n"+"</div>";
                $j("#wrap-pattern-seleccionado").replaceWith(wrapPat);
            });
	    strXML += "<li>";
	    strXML += "<input type=\"hidden\" id=\"id_pat\" name=\"id_pat\" value=\"_ALL\" />";
            strXML += "<input type=\"hidden\" id=\"key_pat\" name=\"key_pat\" value=\"Sin seleccionar patr&oacute;n\" />";
	    strXML += "</li>";
            //strXML += "</form>";
            strXML += "</ul>";
            $j("#listado-estampados").replaceWith(strXML);
            if ($j("#id_sze").val() == "_ALL") 
                f2.dfModo.value = "_NONE";
            else
                $j("#limpiar").show();
        }
        
        function changeImg(patcod) {
            //Patron seleccionado
            $j("#img-estampado-sel").replaceWith("<img src=\"imagedisplay.php?name=img_pattern&filter="+patcod+"\" height=\"80px\" width=\"80px\" />");
            f2.dfPat.value = patcod;
        }

        function limpiarSeleccion(caso) {
            f2.dfModo.value = "LIMPIAR";
            $j("#limpiar").hide();
            
            if (caso == 1) {
                var loader="<div id=\"wrap-tabla-tallas\"><img src=\"images/ajax-loader.gif\"/> Cargando tallas, espere un momento ...</div>";
                $j("#wrap-tabla-tallas").replaceWith(loader);
                $j("#id_pat").val('_ALL');
                $j("#key_pat").val('Sin seleccion');
                $j("form#patternlist-form").submit(); 
            }
            else if (caso == 2) {
                var strXML = "<ul id=\"listado-estampados\">";
                strXML += "<input type=\"hidden\" id=\"id_dsg\" name=\"id_dsg\" value=\"<?php echo $dsg_iddsg; ?>\" />";
                strXML += "<li><img src=\"images/ajax-loader.gif\" width=\"40px\" height=\"40px\" /> Cargando colores, espere un momento ...</li><br/>";
                strXML += "<li>";
                strXML += "<input type=\"hidden\" id=\"id_pat\" name=\"id_pat\" value=\"_ALL\" />";
                strXML += "<input type=\"hidden\" id=\"key_pat\" name=\"key_pat\" value=\"Sin seleccionar patr&oacute;n\" />";
                strXML += "</li>";
                strXML += "</ul>";
                $j("#listado-estampados").replaceWith(strXML);
                
                $j("#id_pat").val('_ALL');
                $j("#key_pat").val('Sin seleccion');
                $j("#id_sze").val("_ALL");
                $j("form#patternlist-form-sze").submit();
             }
             else {
                 
             }
        }

        function myFunctionName() {
            if (confirm('Desea agregar servicios de Bordado a su Cotizaci\u00f3n ?')) {
                
            }
        }        
<?php if ($Cod_Per > 0) { ?>
		function AgregarAlCarrito() {
		    //alert("Patern Seleccionado: "+f2.dfPat.value);
		    if (f2.dfPat.value == "_ALL") {
                        alert("Debe seleccionar un patron para poder cotizar.");
                        return false                        
                    }
		    else if ("<?php echo $Tip_Usr ?>" == "V") {
                        if (confirm('Desea agregar servicios de Bordado a su Cotizaci\u00f3n ?\n\nSi desea agregar, presione Aceptar en caso contrario Cancelar y su cotizaci\u00f3n ser\u00e1 ingresada sin servicios.')) {                        
                            var servicio = document.getElementById('servicio');
                            var cantidad = document.getElementById('cantidad');
                            servicio.href = "servicios.php?producto=<?php echo $p_grpprd; ?>&cantidad="+cantidad.value;
                            return true;
                        }
                        else {
                                f2.action = "carrito.php";
                                f2.submit();
                                return false;
                        }
                    }
                    else {
			    f2.action = "carrito.php";
			    f2.submit();
                            return false;
                    }
		}
<?php } else { ?>
		function AgregarAlCarrito() {
			if (confirm("Para cotizar debe conectarse como usuario.\nSi no esta inscrito favor registrarse.\nDesea continuar ?"))
			{
				f2.action = "aviso.php?idmsg=31";
				f2.submit();
			}
                        return false;
		}
<?php } ?>
</script>
</head>

<body >

<div id="body">

    <div id="header">
   
    </div>
    <div class="menu" id="menu-catalogo">
    	<a id="home" href="index.htm">home</a>
    	<a id="empresa" href="empresa.htm">empresa</a>
        <a id="marcas" href="marcas.htm">marcas</a>
        <a id="telas" href="telas.htm">telas</a>
        <a id="bordados" href="bordados.htm">bordados</a>
        <a id="despachos" href="despachos.htm">despachos</a>
        <a id="clientes" href="clientes.htm">clientes</a>
         <div id="servicio-cliente-selected" style="z-index:1000;padding-top:0px;" class="selected">
        <ul id="dropdown-scliente" class="dropdown">
                <li>
                    <a class="normal" href="servicio-cliente.htm">servicio al cliente</a>
                    <ul>
                       <li>
                            <a href="faq.htm">Faq</a>
                      </li>
                        <li>
                            <a href="como-tomar-medidas.htm">C&oacute;mo Tomar Medidas</a>
                        </li>
                        <li>
                            <a href="despachos.htm">Despachos</a>
                        </li>
                        <li>
                            <a href="clean-care.htm">Clean & Care</a>
                        </li>
                        <li>
                            <a href="tracking-ordenes.htm">Tracking de &Oacute;rdenes</a>
                        </li>
                        <li>
                            <a href="como-cotizar.htm">C&oacute;mo Cotizar</a>
                        </li>
                       
                        <li>
                            <a href="politicas-privacidad.htm">Pol&iacute;ticas de Privacidad</a>
                        </li>
                    </ul>
                </li>
           </ul>		
        </div>
        <div id="catalogo-selected" class="selected"><a id="catalogo2" href="catalogo.php">catalogo</a></div>
        <a id="contacto" href="contacto.htm">contacto</a>
  
  	</div>
	<?php if ($Cod_Per == 0) { ?>
    <ul id="usuario_registro">
		<?php echo solicitar_login(); ?>
    </ul>
	<?php }
		  else {
	?>
    <ul id="usuario_registro">
		<?php 	
                    echo display_login($Cod_Per, $Cod_Clt, $db, $cod_cot); 
                ?>
    </ul>
	
	<?php 
		}
	?>
    <div id="work"><div id="top-detalle-catalogo"></div>
    <div id="navegacion-detalle-catalogo">
	<table border="0" CELLSPACING="0" CELLPADDING="2" width="74%"><tr><td width="80%">
    <?php
		$link = "detalle-producto.php?producto=$p_grpprd&title=$p_title";
		$namelink = $dsg_style;
		agregarLevelSession($link, $namelink, 999);
		DisplayLevelSession("buffer2");
    ?>
	</td><td align="right"><?php DisplayLastSession("Volver", $pagina); ?></td></tr></table>
    </div>
    	<div id="back-catalogo" class="clearfix">
        		
               	<div id="titulo-catalogo"></div>
				<div style="float:left; width:660px; position:relative;" class="clearfix">
            	<div id="wrap-imagen-producto">
                    <div id="imagen-producto">
                        <?php
                        //Obtengo la imagen de la DB
                        //var_dump( getimagesize(printimg_addr("img1_grupo", $p_grpprd)));
                        $tamaño = get_size_image("img1_grupo", $p_grpprd, $db, $ancho, $alto);
                        if($ancho > $alto || $ancho == $alto){
                            $tamano = "width=\"270\"";
                        }elseif($alto > $ancho) $tamano = "height=\"270\"";
                            echo '<img src="'.printimg_addr("img1_grupo", $p_grpprd).'" '. $tamano .'/>';
                        ?>
                    </div>
                </div>
                <div id="wrap-detalle-producto">
                <div class="titulo-producto"><?php echo $grpprd_title; ?></div>
                    <div class="subtitulo-producto">Style <?php echo $dsg_style . ' ' . $dsg_name ?></div>
                    <p class="descripcion-producto">
                        <?php echo $grpprd_descripcion ?>
                    </p>
                    <p class="descripcion-producto">
                        Marca: <?php echo $mca_nombre ?>
                        <br />
                        L&iacute;nea: <?php echo $linmca_descripcion ?>
                    </p>
    
                    <a rev="width: 602px; height: 490px; border: 0 none; scrolling: auto;" title="<?php echo $grpprd_title ?>" rel="lyteframe[imagenes]" href="catalogo/imagenes-producto.php?producto=<?php echo $p_grpprd ?>" id="mas-vista-producto"></a>
	<?php if ($Tip_Usr == "V") { ?>
                    <a rev="width: 850px; height: 490px; border: 0 none; scrolling: auto;" title="<a href='PreciosToExcel.php?producto=<?php echo $p_grpprd ?>'>Exportar a Excel</a>" rel="lyteframe[precios]" href="catalogo/lista-precios.php?producto=<?php echo $p_grpprd ?>" id="listado-precios">Listado de Precios</a>
        <?php } ?>
                </div>
                
                <!--div id="menu-busquedas" class="clearfix">
                    <span>Criterios de B&uacute;squedas:</span>
                    <div id="buscar-talla" class="buscar-talla2"></div>
                    <div id="buscar-pattern" class="buscar-pattern2"></div>
                </div-->
                <div id="busqueda-centro">
                    <div class="estampados-disponibles">
                        <input type="button" onclick="javascript:limpiarSeleccion(1)" id="limpiar" value="Limpiar B&uacute;squeda" style="background-color: red" />
                    </div>
                    <div id="wrap-tabla-tallas">
                        Seleccione un patr&oacute;n
                    </div>
                    <div id="wrap-opciones-tallas">
                        <a rev="width: 600px; height: 250px; border: 0 none; scrolling: auto;" title="Tabla de conversión de tallas" rel="lyteframe[tallas]" href="catalogo/tallas-producto.php?grpprd=<?php echo $p_grpprd; ?>">+ ver tabla de tallas</a>
                        <a href="como-tomar-medidas.htm" target="_blank">+ vea aqui como tomar sus medidas</a>
                    </div>
                    <form id="patternlist-form-sze">
                        <input type="hidden" id="id_sze" name="id_sze" value="" />
                    </form>
                </div>
    
                <div id="wrap-estamp-producto" class=" clearfix">
                <div class="estampados-disponibles">
                </div>
                <!--div class="estampados-disponibles" style="margin-top:0;">Patterns Disponibles</div-->
                    <?php
                        //Obtengo los patrones de la DB
                        $p_dsg = ok($dsg_iddsg);
                        $query = mssql_query("vm_strcol_prod '".$p_grpprd."'", $db);
                        $row = mssql_fetch_array($query);
                    ?>
                    <?php
                        //AJAX: Se tendra el placeholder del patron seleccionado
                    ?>
                    <ul class="estampado-sel clearfix">
                        <li id="img-estampado-sel">
                            <img src="<?php echo printimg_addr("img_pattern",$row["Cod_Pat"]) ?>" height="80px" width="80px" />
                        </li>
                        <li id="des-estampado-sel">
                            <div id="descrip-est"><?php echo $row["Key_Pat"] ?><br /><?php echo $row["Des_Pat"] ?></div>
                        </li>
                    </ul>
                    <form id="patternlist-form">
                    <ul id="listado-estampados">
                    <?php
                        //AJAX: Se tiene el arreglo de patrones
                    ?>
                            <input type="hidden" id="id_dsg" name="id_dsg" value="<?php echo $p_dsg ?>" />
                            <?php
                            $first=false; // Deshabilitamos que muestre el primero
                            for($i=0; $i<12; $i++){
                                if (!$row) break;
                                ?>
                                <li>
                                    <div style="width: 50px; height: 50px">
                                        <?php if ($row['Stock'] == 0) { ?>
                                        <div style="position: absolute; background: url(images/catalogo1/cross.png); width: 16px; height: 16px;"></div>
                                        <?php } else if ($row['PrcOfert'] > 0) { ?>
                                        <div style="position: absolute; background: url(images/catalogo1/PRD_20px.png); width: 20px; height: 20px;"></div>
                                        <?php } ?>
                                        <img onmouseover="previewPattern('<?php echo $row["Cod_Pat"] ?>', '<?php echo $row["Key_Pat"]."_BR_".$row["Des_Pat"] ?>',<?php echo $row['PrcOfert']; ?>, <?php echo $row['Stock']; ?>)" 
                                             onclick="selectPattern('<?php echo $row["Cod_Pat"] ?>', '<?php echo $row["Key_Pat"]."_BR_".$row["Des_Pat"] ?>',<?php echo $row['PrcOfert']; ?>, <?php echo $row['Stock']; ?>)" 
					     src="<?php echo printimg_addr("img_pattern",$row["Cod_Pat"]) ?>" width="50px" height="50px" 
					/>
                                    </div>
                                </li>
                                <?php
    
                                if($i==11){ echo "<br />\n"; $i=0; }
                                //Dejar el primer patron seleccionado
                                if($first==true){
                                    ?>
                                    <input type="hidden" id="id_pat" name="id_pat" value="<?php echo $row["Cod_Pat"] ?>" />
                                    <input type="hidden" id="key_pat" name="key_pat" value="<?php echo $row["Key_Pat"]."_BR_".$row["Des_Pat"] ?>" />
                                    <?php
                                    $pat_first = $row["Cod_Pat"];
                                    $first=false;
                                }
                                $row = mssql_fetch_array($query);
                            }
                            ?>
                            <li>
                                    <?php
                                        $pat_first = "_ALL";
                                    ?>
                                    <!--img onmouseover="previewPattern('_ALL', 'Sin seleccionar patr&oacute;n')" onclick="selectPattern('_ALL', 'Sin seleccionar patr&oacute;n')" src="images/sin_patron.gif" width="50px" height="50px" /-->
                                    <input type="hidden" id="id_pat" name="id_pat" value="<?php echo $pat_first; ?>" />
                                    <input type="hidden" id="key_pat" name="key_pat" value="<?php echo "Sin seleccionar patr&oacute;n" ?>" />
                            </li>
                    </ul>
                    </form>
                    <div></div>
                </div>
                
            <div id="sub-nota"><strong>Importante:</strong> Necesita haber seleccionado la Talla y Pattern <span style="color:#06C7C6; font-weight:bold;">ANTES</span> de agregar el producto</div>
           	</div>
            
          <div style="float:right; width:210px; position:relative;" class="clearfix">
              <ul class="submenu-catalogo" style="margin-top:-25px;">
		<?php
                    //Obtener lineas que son mostrables en el catalogo
                    $sp_header = mssql_query("vm_strinv_header", $db);
                    while($row = mssql_fetch_array($sp_header)){
                        echo "<li><a href=\"catalogo.php?header=".$row['img']."&id=\"".$row['cod'].">".$row['nombre']."</a></li>";
                    }
                ?>
                    <li><a href="catalogo.php?id=">Sales</a></li>
                    <li><a href="busqueda-avanzada.php">Advanced Search</a></li>
              </ul>
              <div id="seleccion" style="top:0;">
  <form ID="F3" method="POST" name="F3">
    <div id="necesita-ayuda" <?php if ($cod_cot > 0) echo "class=\"back-carrito\"";?> >
        <?php 
            if ($cod_cot > 0) { 
                $cantidad = 0;
                $result = mssql_query ("vm_count_cotweb $cod_cot", $db) or die ("No se pudo leer datos de la cotizacion");
                if (($row = mssql_fetch_array($result))) $cantidad = $row['cantidad'];
                mssql_free_result($result); 
		if($cantidad > 0){
        ?>
        <div id="shoppingbag"></div>
        <a id="carro_productos" rev="width: 750px; height: 300px; border: 0 none; scrolling: auto;" title="Lista de Productos a Cotizar" rel="lyteframe[cotizaciones]" href="cotizaciones.php">Productos (<span id="cant_prods"><?php echo $cantidad;?></span>)</a><br>
        <?php 
                } 
            } 
        ?>					
        <a rev="width: 600px; height: 300px; border: 0 none; scrolling: auto;" title="Ayuda" rel="lyteframe[ayuda2]" href="catalogo/ayuda.php">Necesita Ayuda</a>
    </div>
  </form>
  <div id="wrap-talla-seleccionada">
      <div>Talla Seleccionada</div>
      <div id="talla">NONE</div>
  </div>
  <div id="wrap-pattern-seleccionado">
      <div>Pattern Seleccionado</div>
      <div>
          <div style="position: absolute; background: url(images/catalogo1/PRD_20px.png); width: 20px; height: 20px;"></div>
          <img id="pattern" src="images/sin_patron.gif"/>
      </div>
      <div id="pattern-nombre">Sin patr&oacute;n</div>
  </div>
  <form ID="F2" method="POST" name="F2" style="margin-top:7px;">
      <span style="color:#fff;">Cantidad:</span>
      <select name="cantidad" id="cantidad">
        <?php
         echo "\t\t\t\t<option selected=true value=\"1\">1</option>\n";
         for ($i=1;$i<30;$i++)
            echo "\t\t\t\t<option value=\"$i\">$i</option>\n";
         ?>
      </select>
      <div id="agregar">
	<?php if ($Tip_Usr == "V") { ?>
          <a id="servicio" class="lytebox" data-lyte-options="width: 750; height: 490; border: 0 none; scrolling: auto; beforeStart:AgregarAlCarrito" data-title="Especificar Servicios" href="servicios.php?producto=<?php echo $p_grpprd; ?>&cantidad=1"><div id="btn-orden-compra"></div></a>
	<?php } else { ?>
          <a href="javascript:AgregarAlCarrito()"><div id="btn-orden-compra"></div></a>
	<?php } ?>
      </div>
      <INPUT type="hidden" name="dfDsg" value="<?php echo $p_coddsg ?>">
      <INPUT type="hidden" name="dfSze">
      <INPUT type="hidden" name="dfCodSze">
      <INPUT type="hidden" name="dfPat">
      <INPUT type="hidden" name="dfKeyPat">
      <INPUT type="hidden" name="dfModo" id="dfModo" value="_NONE">
      <INPUT type="hidden" name="dfPrd" id="dfPrd" value="<?php echo $p_grpprd; ?>">
      <INPUT type="hidden" name="dfTitle" value="<?php echo $grpprd_title; ?>">
  </form>
</div>
            </div>
            
			
      </div><div id="bottom-detalle-producto"></div>
</div>
    <div id="footer"></div>
</div>
<script type="text/javascript">
    var f1;	
    var f2;
    var f3;

    f1 = document.F1;	
    f2 = document.F2;
    f3 = document.F3;

    f2.dfPat.value = '<?php echo $pat_first; ?>';
    $j("#limpiar").hide();
</script>
</body>
</html>
