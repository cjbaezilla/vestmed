<?php
//Obtengo los datos de conexion de la base de datos
ini_set('display_errors', '0');
session_start();
include("config.php");

$Cod_Per = 0;
$cod_cot = 0;
if (isset($_SESSION['CodPer'])) $Cod_Per = intval($_SESSION['CodPer']);
if (isset($_SESSION['CodClt'])) $Cod_Clt = intval($_SESSION['CodClt']);
if (isset($_SESSION['CodCot'])) $cod_cot = intval($_SESSION['CodCot']);
$p_grpprd = ok($_GET['producto']);
$p_title = ok($_GET['title']);
$p_car = "";
$p_srv = "";
$p_dst = "";
$p_suc = "";
$p_nper = "1";
$p_ppre = "1";
$observaciones = "";

$result = mssql_query("vm_cli_s $Cod_Clt", $db);
if ($row = mssql_fetch_array($result)) $Num_Doc = $row['Num_Doc'];

$IVA = 0.0;
$result = mssql_query("vm_getfolio_s 'IVA'",$db);
if ($row = mssql_fetch_array($result)) $IVA = $row['Tbl_fol'] / 10000.0;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Catalogo - Vestmed Vestuario M&eacute;dico</title>
<link href="css/layout.css" type="text/css" rel="stylesheet" />
<script type="text/javascript" src="js/mootools-1.2.1-core-yc.js"></script>
<script type="text/javascript" src="js/mootools-1.2-more.js"></script>
<script type="text/javascript" src="dropdown/js/dropdown.js"> </script>
<link rel="stylesheet" type="text/css" media="screen" href="dropdown/css/uvumi-dropdown.css" />
<!--[if IE]>
<link rel="stylesheet" type="text/css" media="screen" href="dropdown/css/uvumi-dropdown-ie.css" />
<![endif]-->
<script type="text/javascript">
new UvumiDropdown('dropdown-scliente');
</script>
<LINK href="Include/estilos.css" type="text/css" rel="stylesheet">
<script language="JavaScript" src="Include/SoloNumeros.js"></script>
<script language="JavaScript" src="Include/ValidarDataInput.js"></script>
<script language="JavaScript" src="Include/fngenerales.js"></script>
<script type="text/javascript" src="js/jquery-1.3.2.min.js"></script>
<script type="text/javascript">
	var $j = jQuery.noConflict();
        $j(document).ready
	(
		//$j(":input:first").focus();
		
		function()
		{
	        $j("form#searchPro").submit(function(){
				$j.post("ajax-search-svc.php",{
					search_type: "svc",
					param_filter: $j("#pro").val()
				}, function(xml) {
					listLinEsp(xml);
				});return false;
		    });
			
	        $j("form#searchEsp").submit(function(){
				$j.post("ajax-search-svc.php",{
					search_type: "con",
					param_filter: $j("#pro").val(),
					param_codsvc: $j("#esp").val(),
					param_codclt: $j("#dfCodClt").val(),
					param_codsuc: $j("#dfSuc").val(),
					param_peso: $j("#dfPeso").val()
				}, function(xml) {
					listLinSvc(xml);
				});return false;
		    });
			
	        $j("form#detalle").submit(function(){
				$j.post("updcotizaciones.php",{
					accion: "update-ajax",
					sec: $j("#dfSec").val(),
					ctd: $j("#dfCtd").val()
				}, function(xml) {
					ActualizaHidden(xml);
				});return false;
		    });
	        //NO SUBMIT TODAVIA $j("form#patternlist-form").submit();
	    }
		
	);
    //TODO: No se esta realizando el post, probablemente debido a que falta el evento submit.
    
	function popwindow(ventana,altura){
	   window.open(ventana,"Anexos","toolbar=0,location=0,scrollbars=yes,resizable=1,left=60,top=40,width=930,height="+altura);
	}

    function filterPro(obj)
    {
	f5.dfCrr.value = obj.value;
        $j("#pro").val(obj.value);
	$j("#dfCostoDsp").val("$ 0");
        $j("form#searchPro").submit();
    }

    function filterSvc(obj)
    {
        //alert("filterSvc="+obj.value);
        f5.dfCrrSvc.value = obj.value;
        $j("#.esp").val(obj.value);
        //alert("suc="+$j("#dfSuc").val());
        //alert("clt="+$j("#dfCodClt").val());
        //alert("esp="+$j("#esp").val());
        //alert("peso="+$j("#dfPeso").val());
        $j("form#searchEsp").submit();
    }
	
    function llenarEsp(obj)
    {
		f5.dfCrrSvc.value = obj.value;
		$j("#.codesp").val(obj.value);
    }

    function llenarPer(obj)
    {
		//alert("llenarPer="+obj.value);
		f5.dfPersonas.value = obj.value;
	    f5.dfPrecios.value = (obj.value < "3" ? "1" : "2");
	    f6.precios.value = (f5.dfPrecios.value == "1" ? "Minorista" : "Mayorista");
    }

    function llenarPre(obj)
    {
		f5.dfPrecios.value = obj.value;
    }

    function llenarObs(obj)
    {
		f5.dfComentario.value = obj.value;
    }

    function listLinEsp(xml)
    {
        options="<select id=\"esp\" name=\"esp\" class=\"textfieldv2\" onChange=\"filterSvc(this)\">\n";
        options+="<option selected value=\"_NONE\">Seleccione un Servicio</option>\n";
        $j("filter",xml).each(
			function(id) {
	            filter=$j("filter",xml).get(id);
	            options+= "<option value=\""+$j("code",filter).text()+"\">"+$j("value",filter).text()+"</option>\n";
	        }
		);
        options+="</select>";
        $j("#esp").replaceWith(options);
		condiciones.value = "";
    }
	
	function refreshCondiciones (obj)
	{
		f5.dfValTipSvc.value = obj.value;
		if (condiciones.value == "") return;
		if (obj.value == 0)
			condiciones.value = cond_original;
		else
			condiciones.value = cond_original + ". Personal de Vestmed se comunicara con Usted para indicarle la direccion del Carrier para su retiro.";
	}
	
    function listLinSvc(xml)
    {
		var options;
		//alert("listLinSvc");
        $j("filter",xml).each(
			function(id) {
	            filter=$j("filter",xml).get(id);
				//alert($j("code",filter).text()+"="+$j("value",filter).text());
				if ($j("code",filter).text() == "condiciones") {
					condiciones.value = $j("value",filter).text();
					cond_original = condiciones.value;
					if ($j("#rbTipoDsp:checked").val() == 1 && $j("#rbTipoDsp:checked").is(':visible'))
						condiciones.value = condiciones.value + ". Personal de Vestmed se comunicara con Usted para indicarle la direccion del Carrier para su retiro.";
				}
				if ($j("code",filter).text() == "costo") {
					//alert("Costo=["+$j("value",filter).text()+"]");
					//valordsp = parseFloat($j("value",filter).text().replace(".", ""));
					valordsp = parseFloat($j("value",filter).text());
					valordsp += valordsp * <?php echo $IVA ?>;
					$j("#dfCostoDsp").val("$ "+FormatNumero(Math.round(valordsp).toString()));
					if ($j("value",filter).text() == "0") {
                                            $j("#labelPeso").replaceWith("<span id=\"labelPeso\" class=\"dato\">SIN SERVICIO</span>");
                                            $j("#dfCostoDspPrd").val("0");
					}
					else {
                                            $j("#labelPeso").replaceWith("<span id=\"labelPeso\" class=\"dato\"></span>");
                                            $j("#dfCostoDspPrd").val($j("#dfCostoDsp").val());
					}
				}
	        }
		);
	}
	
	function ActualizaHidden(xml)
	{
		//alert("ActualizaHidden");
		
        $j("filter",xml).each(
			function(id) {
	            filter=$j("filter",xml).get(id);
				//alert($j("code",filter).text()+"="+$j("value",filter).text());
				
				if ($j("code",filter).text() == "Peso") {
				    $j("#dfPeso").val($j("value",filter).text());
					//alert($j("input[name='rbTipoSuc']:checked").val());
					if ($j("input[name='rbTipoSuc']:checked").val() != "0") $j("form#searchEsp").submit();
				}
				
	        }
		);
		
	}
	
	function Agregar() {
		f2.action="catalogo.php";
		f2.submit();
	}
	
	function AgregarColores(prd,title) {
		f2.action="detalle-producto.php?producto="+prd+"&title="+title;
		f2.submit();
	}

	function llenarSuc(obj,campo) {
		eval("f5."+campo).value = obj.value;
		
		if (campo != "dfSucFct") {
			if (obj.value == "0")
				$j("#inf_despacho").hide("slow");
			else 
				$j("#inf_despacho").show("slow");
			cmbpro = document.getElementById("pro");
			cmbesp = document.getElementById("esp");
			if (obj.value == "0") {
			   f5.despacho.value = "_NONE";
			   cmbpro.disabled = true;
			   cmbesp.disabled = true;
			   //cmbpro.style.display = "visible";
			}
			else {
			   f5.despacho.value = "1";
			   cmbpro.disabled = false;
			   cmbesp.disabled = false;
			   //cmbpro.style.display = "none";
			}
			if ($j("#dfTipo"+obj.value).val() == 1) {
				$j("#tipo_despacho").show("slow");
				f5.dfTipSvc.value = "VISIBLE"
			}
			else {
				$j("#tipo_despacho").hide("slow");
				f5.dfTipSvc.value = "HIDDEN"
			}
				
			$j("#dfCostoDsp").val("$ 0");
			if (obj.value > 0 && $j("#esp").val() != "_NONE") $j("form#searchEsp").submit();
			else {
				condiciones.value = "";
				cond_original = condiciones.value;
			}
		}
	}
	
	function llenarCampo(obj) {
		var campo;
		campo=obj.name.substring(0,obj.name.length-2)
		eval("f5."+campo).value = (obj.checked ? 1 : 0);
		//alert("f5."+campo); alert(eval("f5."+campo).value);
	}
	
	function UpdateCantidad(obj,sec) {
	    //alert("UpdateCantidad");
		f3.dfSec.value = sec;
		f3.dfCtd.value = obj.value;
        $j("form#detalle").submit();
	}

	function MarcarEliminado(obj) {
		f2.dfEliminados.value = ResfrescarEliminados(f3);
	}
	
	function Eliminar() {
		if (f2.dfEliminados.value == "" || f2.dfEliminados.value == "_NONE") 
			alert ("Debe seleccionar un producto a eliminar ...");
		else {
			f2.action = "updcotizaciones.php?accion=deldetalle";
			f2.submit();
		}
	}
	
	function NuevaSuc(numdoc) {
		popwindow("cotizador/registrar_suc.php?clt="+numdoc+"&xis=1&acc=newsuc&ret=2",600);
	}
	
	function maximaLongitud(texto,maxlong)
	{
		var tecla, int_value, out_value;

		if (texto.value.length > maxlong)
		{
			/*con estas 3 sentencias se consigue que el texto se reduzca
			al tama�o maximo permitido, sustituyendo lo que se haya
			introducido, por los primeros caracteres hasta dicho limite*/
			in_value = texto.value;
			out_value = in_value.substring(0,maxlong);
			texto.value = out_value;
			alert("La longitud maxima es de " + maxlong + " caracteres");
			return false;
		}
		return true;
	}
	
	function llenarDatosPersona(obj,caso) {
		if (caso == 1)
			f5.dfPesoPer.value = obj.value;
		else if (caso == 2)
			f5.dfEstaturaPer.value = obj.value;
		else if (caso == 3)
			if (obj.checked)
			   f5.dfFlgTer.value = 1;
			else
			   f5.dfFlgTer.value = 0;
	}
	
</script>
</head>

<body>
<div id="body">
	<div id="header"></div>
    <div class="menu" id="menu-noselect">
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
        <a id="catalogo" href="catalogo.php">catalogo</a>
        <a id="contacto" href="contacto.htm">contacto</a>
  
  	</div>
	<?php 
		if ($Cod_Per == 0) { 
	?>
    <ul id="usuario_registro">
        <form ID="F1" AUTOCOMPLETE="off" method="POST" name="F1">
    	<li class="back-verde registro"><a href="registrarse.php">REGISTRARSE</a></li>
        <li class="olvido"><a>OLVID&Oacute; SU CLAVE?</a></li>
        <li class="back-verde"><a href="javascript:ValidarLogin()">ENTRAR</a></li>
        <li class="back-verde inputp"><input type="password" name="dfclave"/></li>
        <li class="back-verde">CONTRASE&Ntilde;A</li>
        <li class="back-verde inputp"><input type="" name="dfrut"></li>
        <li class="back-verde">RUT</li>
		</form>
    </ul>
	<?php }
		  else {
	?>
    <ul id="usuario_registro">
		<?php 	echo display_login($Cod_Per, $Cod_Clt, $db); ?>
    </ul>
	
	<?php 
		}
	?>
    <div id="work">
    	<div id="wrap-back-cotizacion">
    	<div id="back-cotizacion">
			<TABLE WIDTH="100%" BORDER="0" CELLSPACING="0" CELLPADDING="1" ALIGN="center" style="padding-left:50px; padding-right:10px;">
			<tr><td>
				<TABLE WIDTH="100%" class="productos-cotiza" ALIGN="center" >
				<form id="detalle" name="detalle" method="post" >
				<TR>
					<TD align="middle">Productos Cotizados</TD>
					<TD align="middle">Marca</TD>
					<TD align="middle">Talla</TD>
					<TD colspan="2" align="middle">Patr&oacute;n</TD>
					<TD align="middle">Cantidad</TD>
					<TD align="middle">&nbsp;</TD>
				</TR>
				<?php
					$j = 0;
					$iTotPrd = 0;
					$peso = 0.1;
					$result = mssql_query("vm_s_cotweb $cod_cot", $db);
					while ($row = mssql_fetch_array($result)) {
						echo "<TR>\n";
						if ($j == 0) {
							$clase1 = "";
							$clase2 = "";
						}
						else {
							$clase1 = "";
							$clase2 = "";
						}
						echo "   <TD class=\"".$clase1."\" style=\"TEXT-ALIGN: left \">".$row['cod_sty']."-".str_replace("#","'",$row['Nom_Dsg'])."</TD>\n";
						echo "   <TD class=\"".$clase2."\" style=\"TEXT-ALIGN: center\">".$row['cod_mca']."</TD>\n";
						echo "   <TD class=\"".$clase2."\" style=\"TEXT-ALIGN: center\">".$row['val_sze']."</TD>\n";
						echo "   <TD class=\"".$clase2."\" style=\"TEXT-ALIGN: left; BORDER-RIGHT: none\">\n";
						if ($row['cod_pat'] != "_ALL")
							echo "<img src=\"".printimg_addr("img_pattern",$row["cod_pat"])."\" height=\"25px\" width=\"25px\">\n";
						else
							echo "&nbsp;";
						echo "   </TD>\n";
						echo "   <TD class=\"".$clase2."\" style=\"TEXT-ALIGN: center\">".(($row['cod_pat'] != "_ALL") ? $row['key_pat'] : "_ALL")."</TD>\n";
						echo "   <TD class=\"".$clase2."\" style=\"TEXT-ALIGN: center\">";
						echo "   <INPUT name=\"dfCtd".$row["cod_sec"]."\" size=\"3\" maxLength=\"3\" class=\"textfield_m\" onKeyPress=\"SoloNumeros(this)\" onchange=\"UpdateCantidad(this,".$row["cod_sec"].")\" value=\"".$row['cot_ctd']."\"></TD>\n";
						echo "   <TD class=\"".$clase2."\" style=\"TEXT-ALIGN: center\">";
						echo "   <INPUT type=\"checkbox\" class=\"dato\" style=\"height: 14px\" name=\"seleccionadof[]\" value=".$row["cod_sec"]." onclick=\"MarcarEliminado(this)\"></TD>\n";
						echo "</TR>\n";
						$j = 1 - $j;
						$peso+=($row["cot_ctd"]*$row["Peso_Uni"]);
						$iTotPrd++;
					}
					mssql_free_result($result);
					if ($iTotPrd == 0) {
					?>
						<TR>
							<TD colspan="7" style="PADDING-TOP: 10px;" class="label_left_right">
								NO TIENE PRODUCTOS AGREGADOS AL CARRO DE COTIZACIONES
							</TD>
						</TR>
					<?php
					}
				?>
				<TR>
					<TD colspan="7" style="PADDING-TOP: 10px; TEXT-ALIGN: right" class="label_top">
						<INPUT type="hidden" name="dfSec" id="dfSec" value="_NONE">
						<INPUT type="hidden" name="dfCtd" id="dfCtd" value="_NONE">
						<INPUT type="hidden" name="dfPeso" id="dfPeso" value="<?php echo $peso; ?>">
					</TD>
				</TR>
				</form>
				<TR>
					<form id="F2" name="F2" method="post" >
					<TD colspan="7" class="normal" style="TEXT-ALIGN: right">
						<!--A href="javascript:AgregarColores(<?php //echo $p_grpprd; ?>, '<?php //echo $p_title; ?>')">&lt;Agregar mas Colores o Tallas&gt;</a>&nbsp;&nbsp;-->
						<input type="button" class="btn2" value="Agregar nuevos Productos" onclick="Agregar();" />
						<input type="button" class="btn2" value="Eliminar Seleccionados" onclick="Eliminar()" /><!--
						<A href="javascript:Agregar()">&lt;Agregar nuevos Productos&gt;</a>&nbsp;&nbsp;
						<A href="javascript:Eliminar(<?php echo $p_grpprd; ?>, '<?php echo $p_title; ?>')">&lt;Eliminar Seleccionados&gt;</a>-->
						<INPUT type="hidden" name="dfEliminados" id="dfEliminados" value="_NONE">
					</TD>
					</form>
				</TR>
				</TABLE>
			</td></tr>
			<tr><td class="subtitulo-cotiza">Servicios (Pr&oacute;ximamente)</td></tr>
			<tr><td>
				<div id="tipo_bordado">
				<TABLE WIDTH="90%" BORDER="0" CELLSPACING="0" CELLPADDING="1" ALIGN="center">
				<tr>
					<td width="18px"><input type="checkbox" class="dato" style="height: 14px" name="bordado1In" id="bordado1In" onclick="llenarCampo(this)"></td><td class="label2">Servicio Bordado 1 linea</td>
					<td width="18px"><input type="checkbox" class="dato" style="height: 14px" name="bordado2In" id="bordado2In" onclick="llenarCampo(this)"></td><td class="label2">Servicio Bordado 2 lineas</td>
				</tr>
				<tr>
					<td width="18px" colspan="2" class="titulo-bordado">Kerrie Harvison, M.S.</td>
					<td width="18px" colspan="2" class="titulo-bordado">Kimberly A. Bibb, M.D.<BR>UAB Family Medicine</td>
				</tr>
				</TABLE>
				</div>
			</td></tr>
			<tr><td class="subtitulo-cotiza">Informaci&oacute;n Personal</td></tr>
			<tr><td>
				<table border="0" summary="" width="90%" ALIGN="center" cellpadding="0">
					<tr>
						<td colspan="2" valign="top" width="25%">
							<table border="0" width="100%">
								<tr>
									<td>Peso (Kg):</td>
									<td>
										<select id="pesoPer" name="pesoPer" class="dato" onChange="llenarDatosPersona(this,1)">
										<option value="0">_NONE</option>
										<?php 
											for ($pesoPer = 40; $pesoPer <= 120; $pesoPer++) {
										?>
										<option value="<?php echo $pesoPer ?>"><?php echo $pesoPer ?></option>
										<?php
											}
										?>
										</select>
									</td>
								</tr>
								<tr>
									<td>Estatura (cm):</td>
									<td>
										<select name="estatura" class="dato" onChange="llenarDatosPersona(this,2)">
										<option value="0">_NONE</option>
										<?php 
											for ($estatura = 140; $estatura <= 200; $estatura++) {
										?>
										<option value="<?php echo $estatura ?>"><?php echo $estatura ?></option>
										<?php
											}
										?>
										</select>
									</td>
								</tr>
							</table>
						</td>
						<td valign="top" style="padding-left: 50px; padding-right: 50px">
							<table border="0" summary="" align="left">
								<tr>
									<td width="36" valign="top"><img src="images/info.png" border="0" width="16" height="16" alt=""></td>
									<td valign="top">La informaci&oacute;n relacionada con Peso y Estatura permitir&aacute; que nuestro equipo de venta pueda verificar si la talla seleccionada es la m&aacute;s adecuada para usted. En caso de adquirir productos para terceros seleccione la opci&oacute;n "Producto para Terceros".</td>
								</tr>
							</table>
						</td>
					</tr>
					<tr>
                                            <td colspan="3">
                                                <table>
                                                    <tr>
						<td width="23"><input type="checkbox", class="dato" style="height: 14px" name="FlgTerIn" id="FlgTerIn" onclick="llenarDatosPersona(this,3)"></td>
						<td colspan="2">Productos para Terceros y/o No deseo informar peso ni estatura</td>
                                                    </tr>
                                                </table>
                                            </td>
					</tr>
				</table>
			</td></tr>
			<tr><td class="subtitulo-cotiza">Direcci&oacute;n de Facturaci&oacute;n</td></tr>
			<tr><td style="padding-left: 40px">
				<TABLE WIDTH="95%" BORDER="0" CELLSPACING="0" CELLPADDING="1" ALIGN="left">
			<?php
				$j = 1;
				$iTotPrd1 = 1;
				$result = mssql_query("vm_suc_s $Cod_Clt", $db);
				while ($row = mssql_fetch_array($result)) {
			?>
					<TR>
					   <TD style="TEXT-ALIGN: center" width="3%">
					   <INPUT id="rbSucFct" name="rbSucFct" type="radio" style="border:none" value="<?php echo $row['Cod_Suc'] ?>"  onclick="llenarSuc(this,'dfSucFct')" /></TD>
                                            <TD style="TEXT-ALIGN: left; PADDING-LEFT:5px"><?php echo utf8_encode($row['Dir_Suc']); ?> (<?php echo utf8_encode($row['Nom_Cdd']); ?>)</TD>
					</TR>
			<?php
					$j = 1 - $j;
					$iTotPrd1++;
				}
				mssql_free_result($result);
			?>
					<TR>
						<TD colspan="2" style="PADDING-TOP: 10px; PADDING-BOTTOM: 3px; TEXT-ALIGN: left" class="label_top"><input type="button" class="btn2" value="Agregar Direcci&oacute;n" onclick="NuevaSuc('<?php echo $Num_Doc; ?>');" /></TD>
					</TR>
				</TABLE>
			</td></tr>
			<tr><td class="subtitulo-cotiza">Despacho</td></tr>
			<tr><td>
				<TABLE WIDTH="90%" BORDER="0" CELLSPACING="0" CELLPADDING="1" ALIGN="center">
				<tr>
					<td colspan="3" width="100%" style="TEXT-ALIGN: left" valign="top">
					<TABLE WIDTH="100%" BORDER="0" CELLSPACING="0" CELLPADDING="1" ALIGN="left">
					<TR>
						<TD colspan="2" align="left" style="font-weight:bold; font-size:12px;">Direcci&oacute;n</TD>
					</TR>
					<TR>
						<TD width="10px" style="TEXT-ALIGN:center">
							<INPUT id="rbTipoSuc" name="rbTipoSuc" type="radio" style="border:none" value="0" onclick="llenarSuc(this,'dfSuc')" checked>
							<INPUT type="hidden" id="dfTipo0" name="" value="<?php echo $row['Tip_Cmn']; ?>">
						</TD>
						<TD style="TEXT-ALIGN:left; PADDING-LEFT:5px">Pick Up Tienda (Av. Vitacura 5900 Local 5)</TD>
					</TR>
					<?php
						$j = 1;
						$iTotPrd1 = 1;
						$result = mssql_query("vm_suc_s $Cod_Clt", $db);
						while ($row = mssql_fetch_array($result)) {
					?>
							<TR>
							   <TD style="TEXT-ALIGN: center">
									<INPUT id="rbTipoSuc" name="rbTipoSuc" type="radio" style="border:none" value="<?php echo $row['Cod_Suc']; ?>" onclick="llenarSuc(this,'dfSuc')"></TD>
									<INPUT type="hidden" id="dfTipo<?php echo $row['Cod_Suc']; ?>" name="dfTipo<?php echo $row['Cod_Suc']; ?>" value="<?php echo $row['Tip_Cmn']; ?>">
							   <TD style="TEXT-ALIGN: left; PADDING-LEFT:5px"><?php echo utf8_encode($row['Dir_Suc']); ?> (<?php echo utf8_encode($row['Nom_Cdd']); ?>)</TD>
							</TR>
					<?php
							$j = 1 - $j;
							$iTotPrd1++;
						}
						mssql_free_result($result);
					?>
					<TR>
						<TD colspan="2" style="PADDING-TOP: 10px; PADDING-BOTTOM: 10px; TEXT-ALIGN: left" class="label_top"><input type="button" class="btn2" value="Agregar Sucursal" onclick="NuevaSuc('<?php echo $Num_Doc; ?>');" /></TD>
					</TR>
					</TABLE>
					</td>
				</tr>
				</TABLE>
			</td></tr>
			<tr><td>
				<div id="inf_despacho">
				<TABLE WIDTH="90%" BORDER="0" CELLSPACING="0" CELLPADDING="1" ALIGN="center">
				<tr>
					<td class="label22" width="40%" style="TEXT-ALIGN: left">Carrier</td>
					<td class="label22" width="40%" style="TEXT-ALIGN: left">Servicio</td>
					<td class="label22" width="20%" style="TEXT-ALIGN: left">Costo(IVA Inc)</td>
				</tr>
				<tr>
					<form id="searchPro" name="searchPro">
					<TD align="left">
					<select id="pro" name="pro" class="textfieldv2" onChange="filterPro(this)">
						<option selected value="_NONE">Seleccione un Carrier</option>
						<?php //Seleccionar los Carrier
						$sp = mssql_query("vm_CrrCmb",$db);
						while($row = mssql_fetch_array($sp))
						{
							?>
							<option value="<?php echo $row['Cod_Crr'] ?>"><?php echo $row['Des_Crr'] ?></option>
							<?php
						}
						?>
					</select>
					</TD>
					</form>
					<form id="searchEsp" name="searchEsp">
					<TD>
					<select id="esp" name="esp" class="textfieldv2" onChange="filterSvc(this)">
						<option selected value="_NONE">Seleccione un Servicio</option>
						<?php //Seleccionar los Servicios
						$sp = mssql_query("vm_esppro_s 0",$db);
						while($row = mssql_fetch_array($sp))
						{
                                                ?>
                                                    <option value="<?php echo $row['Cod_Esp'] ?>"><?php echo $row['Nom_Esp'] ?></option>
                                                <?php
						}
						?>
					</select>
					</TD>
					</form>
					<td>
						<INPUT name="dfCostoDsp" id="dfCostoDsp" size="8" maxLength="8" class="textfield_m" value="$ 0" ReadOnly />
						<span id="labelPeso" class="dato"></span>
					</td>
				</tr>
				<tr><td colspan="3" style="padding-top: 5px">
				<div id="tipo_despacho">
				<table WIDTH="100%" BORDER="0" CELLSPACING="0" CELLPADDING="1" ALIGN="center">
					<TR>
						<TD width="10px" style="TEXT-ALIGN:center" valign="top">
							<img src="images/warning.png">
						</TD>
						<TD style="TEXT-ALIGN:left; PADDING-LEFT:5px; PADDING-BOTTOM: 5px" colspan="2" valign="top">
						Nuestros registros indican que en el pasado han ocurrido problemas con los despachos a domicilio realizados a la comuna seleccionada. 
						Recomendamos que el despacho sea realizado a la Oficina del Carrier m&aacute;s cercana a su domicilio en donde ser&aacute; recibido y 
						almacenado para su retiro. En caso de concretar una compra, personal de Vestmed se comunicar&aacute; con usted 
						para entregarle mayor informaci&oacute;n.							
						</TD>
					</TR>
					<TR>
						<TD width="10px" style="TEXT-ALIGN:center">
							<INPUT id="rbTipoDsp" name="rbTipoDsp" type="radio" style="border:none" value="0" onclick="refreshCondiciones(this)" />
						</TD>
						<TD style="TEXT-ALIGN:left; PADDING-LEFT:5px" colspan="2">Despacho a Domicilio</TD>
					</TR>
					<TR>
						<TD width="10px" style="TEXT-ALIGN:center">
							<INPUT id="rbTipoDsp" name="rbTipoDsp" type="radio" style="border:none" value="1" onclick="refreshCondiciones(this)" />
						</TD>
						<TD style="TEXT-ALIGN:left; PADDING-LEFT:5px" colspan="2">Despacho a Sucursal del Carrier (Recomendable para localidades distantes/Rurales)</TD>
					</TR>
				</table>
				</div>
				</td></tr>
				<tr>
					<td colspan="3" style="TEXT-ALIGN: left; PADDING-TOP: 10px">
					   <span class="titulo_Condiciones">Condiciones del Servicio:</span><BR>
					   <!--span name="texto_condicion" id="texto_condicion">Overnight: Entrega d&iacute;a Habil Siguiente antes de las 13:00 hrs</span-->
					   <textarea class="textfieldv2" style="overflow: auto" name="texto_condicion" id="texto_condicion" cols="140" rows="4" ReadOnly></textarea>
					</td> 
				</tr>
				</TABLE>				
				</div>
			</td></tr>
			<tr><td class="subtitulo-cotiza">Informaci&oacute;n Adicional</td></tr>
			<tr><td>
				<form id="searchPrc" name="searchPrc">
				<TABLE WIDTH="90%" BORDER="0" CELLSPACING="0" CELLPADDING="1" ALIGN="center">
				<tr>
					<td width="50%" style="TEXT-ALIGN: left;" class="label22">N&uacute;mero de Personas:&nbsp;
						<select name="personas" class="textfieldv2" onChange="llenarPer(this)">
						<option <?php echo (("1"==$p_nper)?'selected':'')?> value="1">Personal</option>
						<option value="2">2 - 15</option>
						<option value="3">15 - 30</option>
						<option value="4">&gt; 30</option>
						</select>
					</td>
					<td width="50%" style="TEXT-ALIGN: right;" class="label22">Precios:&nbsp;
						<INPUT name="precios" id="precios" size="20" class="textfieldv2" style="TEXT-TRANSFORM: uppercase" 
							   ReadOnly value="<?php echo ("2"==$p_nper ? "Mayorista" : "Minorista"); ?>">
					</td>
				</tr>
				<tr>
					<td colspan="2">
						<TABLE WIDTH="100%" BORDER="0" CELLSPACING="0" CELLPADDING="1" ALIGN="center">
						<TD valign="left" style="TEXT-ALIGN: left; PADDING-TOP: 15px" colspan="2">
						<img src="images/catalogo/comentarios.png" />
						   <TEXTAREA class="textfieldv2" rows="5" cols="100" name="fObservaciones" onChange="llenarObs(this)" onKeyUp="return maximaLongitud(this,1000)"><?php echo $observaciones; ?></TEXTAREA>
						</TD>
						</TABLE>
					</td>
				</tr>
				</TABLE>
				</form>
			</td></tr>
			
			<tr><td style="PADDING-TOP: 15px">
				<form ID="F5" method="post" name="F5" AUTOCOMPLETE="on" ACTION="enviarcot.php" onsubmit="return checkDataSucFct(this)">
				<TABLE WIDTH="90%" BORDER="0" CELLSPACING="0" CELLPADDING="1" ALIGN="center">
				<tr>
					<td class="label22" width="80%" style="TEXT-ALIGN: left;">Luego de ser procesada recibir&aacute; su cotizaci&oacute;n
					v&iacute;a e-mail. Tiempo normal de proceso es de un d&iacute;a h&aacute;bil.</td>
					<td style="TEXT-ALIGN: right;">
					<?php if ($iTotPrd > 0) { ?>
					<INPUT class="btn" type="submit" value="Enviar" name="enviar" id="enviar">
					<?php } else { ?>
					&nbsp;
					<?php } ?>
					</td>
				</tr>
				</TABLE>
					<INPUT type="hidden" id="dfCodClt" name="dfCodClt" value="<?php echo $Cod_Clt; ?>">
					<INPUT type="hidden" name="bordado1" value="_NONE">
					<INPUT type="hidden" name="bordado2" value="_NONE">
					<INPUT type="hidden" name="despacho" value="_NONE">
					<INPUT type="hidden" name="dfCrr" value="_NONE">
					<INPUT type="hidden" name="dfCrrSvc" value="_NONE">
					<INPUT type="hidden" id="dfSuc" name="dfSuc" value="0">
					<INPUT type="hidden" name="dfPersonas" value="1">
					<INPUT type="hidden" name="dfPrecios" value="1">
					<INPUT type="hidden" id="dfComentario" name="dfComentario" value="_NONE">
					<INPUT type="hidden" name="dfDirSuc" value="_NONE">
					<input type="hidden" name="dfVal_Pso" value="<?php echo $peso; ?>">
					<INPUT type="hidden" id="dfSucFct" name="dfSucFct" value="0">
					<INPUT type="hidden" id="dfTipSvc" name="dfTipSvc" value="HIDDEN">
					<INPUT type="hidden" id="dfValTipSvc" name="dfValTipSvc" value="_NONE">
					<INPUT type="hidden" id="dfPesoPer" name="dfPesoPer" value="0">
					<INPUT type="hidden" id="dfEstaturaPer" name="dfEstaturaPer" value="0">
					<INPUT type="hidden" id="dfFlgTer" name="dfFlgTer" value="0">
					<INPUT type="hidden" id="dfCostoDspPrd" name="dfCostoDspPrd" value="0">
				</form>
			</td></tr>
			</table>
        </div>
        </div>
  </div>
    <div id="footer"></div>
</div>
<script language="javascript">
	var f1;	
	var f2;
	var f3;
	var f5;
	var f6;
	var cond_original;
	
	f1 = document.F1;	
	f2 = document.F2;	
	f3 = document.detalle;
	f5 = document.F5;
	f6 = document.searchPrc;

    bordado1 = document.getElementById("bordado1In");
    bordado2 = document.getElementById("bordado2In");
    bordado1.disabled = true;
	bordado2.disabled = true;
	
    cmbpro = document.getElementById("pro");
    cmbesp = document.getElementById("esp");
    cmbpro.disabled = true;
	cmbesp.disabled = true;
	
	condiciones = document.getElementById("texto_condicion");
	
	$j("#inf_despacho").hide();
	$j("#tipo_despacho").hide();
	$j("#tipo_bordado").hide();
</script>
</body>
</html>
