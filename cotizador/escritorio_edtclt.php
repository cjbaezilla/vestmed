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
    if ($row["Id_Mod"] == 1 && $row["ID_Opc"] == 3 && $row['CodUsr'] == $UsrId) {
        $OkOpc = true;
        break;
    }
mssql_free_result($sp);

if (!$OkOpc) {
    header ("Location:../index.php");
    exit(0);
}

$RutClt = ok($_GET['clt']);
$Cod_Suc = 0;
$CodSuc = isset($_GET['suc']) ? ok($_GET['suc']) : 0;
if (!strrpos($RutClt,"-")) $RutClt = substr($RutClt, 0, -1)."-".substr($RutClt, -1);
$doc_id = 1;
//$query = "vm_s_per_tipdoc ".$doc_id.", '".$RutClt."'";
$result = mssql_query ("vm_s_per_tipdoc ".$doc_id.", '".$RutClt."'", $db)	or die ("No se pudo leer datos del Cliente");
if (($row = mssql_fetch_array($result))) {
	$cod_tipper = $row['Cod_TipPer'];
	$Cod_Per    = $row['Cod_Per'];
	if ($cod_tipper == 2) { // Persona Juridica
            $NombreClt    = trim(utf8_encode($row["RznSoc_Per"]));
            $NombreFanClt = trim(utf8_encode($row["NomFan_Per"]));
	}
	else { // Persona Natural
            $AppPat  = trim(utf8_encode($row["Pat_Per"]));
            $AppMat  = trim(utf8_encode($row["Mat_Per"]));
            $NomPer  = trim(utf8_encode($row["Nom_Per"]));
            $Cod_Pro = $row['Cod_Pro'];
            $Cod_Esp = $row['Cod_Esp'];
            $CodSex	 = $row['Sex'];
	}
	$web     = $row["www_Per"];
	$Cod_Clt = ($row["Cod_Clt"] == null ? 0 : $row["Cod_Clt"]);
	mssql_free_result($result); 
	
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
<script language="JavaScript" src="../Include/ValidarDataInput.js"></script>
<script language="JavaScript" src="../Include/SoloNumeros.js"></script>
<script language="JavaScript" src="../Include/validarRut.js"></script>
<LINK href="../Include/estilos.css" type="text/css" rel="stylesheet" />
<script type="text/javascript" src="../js/jquery-1.3.2.min.js"></script>
<script type="text/javascript" src="menu.js"></script>
<script type="text/javascript">
	new UvumiDropdown('dropdown-scliente');
	
	var $j = jQuery.noConflict();
    $j(document).ready
	(
            //$j(":input:first").focus();

            function()
            {
            $j("form#searchPro").submit(function(){
                            $j.post("../ajax-search-esp.php",{
                                    search_type: "esp",
                                    param_filter: $j("#pro").val()
                            }, function(xml) {
                                    listLinEsp(xml);
                            });return false;
                });
            //NO SUBMIT TODAVIA $j("form#patternlist-form").submit();
            }
		
	);
    //TODO: No se esta realizando el post, probablemente debido a que falta el evento submit.

    function listLinEsp(xml)
    {
        options="<select id=\"esp\" name=\"esp\" class=\"textfieldv2\" onChange=\"llenarEsp(this)\">\n";
        options+="<option selected value=\"_NONE\">Seleccione una Especialidad</option>\n";
        $j("filter",xml).each(
			function(id) {
	            filter=$j("filter",xml).get(id);
	            options+= "<option value=\""+$j("code",filter).text()+"\">"+$j("value",filter).text()+"</option>\n";
	        }
		);
        options+="</select>";
        $j("#esp").replaceWith(options);
    }
	
    function llenarEsp(obj)
    {
		f2.codesp.value = obj.value;
    }
	
	function blurPN(obj) {
		f2.nom_per.value = obj.value;
	}

	function blurPJ(obj) {
		f2.nom_clt.value = obj.value;
	}

    function filterPro(obj)
    {
		f2.codpro.value = obj.value;
        $j("form#searchPro").submit();
    }

	function MarcarTodos(form,nombrecheckbox) {
	   for (i=0; i<form.elements.length; i++) {
		if (form.elements[i].name == nombrecheckbox)
			form.elements[i].checked = true;
	   }	
	}

	function DesMarcarTodos(form,nombrecheckbox) {
	   for (i=0; i<form.elements.length; i++) {
		if (form.elements[i].name == nombrecheckbox)
			form.elements[i].checked = false;
	   }	
	}
	
	function CheckEliminarSuc(form) {
		var i;
		for (i=0; i<form.elements.length; i++) {
		  if (form.elements[i].name == "seleccionadoSuc[]")
			 if (form.elements[i].checked) {
				if (confirm("Confirma eliminacion de las Sucursales seleccionadas ?")) return true;
				return false;
			}
		}
		alert("Favor seleccione las sucursales que desea eliminar ...")
		return false;
	}
	
	function CheckEliminarCtt(form) {
		var i;
		for (i=0; i<form.elements.length; i++) {
		  if (form.elements[i].name == "seleccionadoCtt[]")
			 if (form.elements[i].checked) {
				if (confirm("Confirma eliminacion de los Contactos seleccionados ?")) return true;
				return false;
			}
		}
		alert("Favor seleccione los contactos que desea eliminar ...")
		return false;
	}
	
	function LimpiarPwdCtt() {
		var i;
		for (i=0; i<f3.elements.length; i++) {
		  if (f3.elements[i].name == "seleccionadoCtt[]")
			 if (f3.elements[i].checked) {
				if (confirm("Confirma reinicio de la clave de los Contactos seleccionados ?")) {
					f3.action = "sendpwdctt.php?clt=<?php echo $Cod_Clt; ?>&suc=<?php echo $Cod_Suc ?>&ret=1";
					f3.submit();
					return true;
				}
				return false;
			}
		}
		alert("Favor seleccione los contactos que desea reiniciar la clave ...")
		return false;
	}
	
	function popwindow(ventana,altura){
	   window.open(ventana,"Anexos","toolbar=0,location=0,scrollbars=yes,resizable=1,left=60,top=40,width=930,height="+altura);
	}
	
	function BuscarCliente(contexto) {
		//popwindow("busqueda.php?contexto="+contexto,600)
		f1.action = "escritorio_bus.php?opc=clt";
		f1.submit();
   	}
		
	function NuevoCliente() {
		popwindow("registrarse.php",600)
	}
	
	function llenarCampo(obj) {
		var campo;
		
		campo=obj.name.substring(0,obj.name.length-2);
		eval("f2."+campo).value = obj.value;
	}
	
	function NuevaSuc(numdoc) {
		popwindow("registrar_suc.php?clt="+numdoc+"&xis=1&acc=newsuc&ret=1",600)
	}

	function EditarSuc(numdoc,codsuc) {
		popwindow("registrar_suc.php?clt="+numdoc+"&xis=1&suc="+codsuc+"&ret=1",600)
	}
	
	function NuevoCtt(numdoc,codsuc) {
		popwindow("registrar_ctt.php?clt="+numdoc+"&xis=1&suc="+codsuc+"&acc=newctt&ret=1",600)
	}
	
	function EditarCtt(numdoc,codsuc,codper) {
		popwindow("registrar_ctt.php?clt="+numdoc+"&xis=1&suc="+codsuc+"&ctt="+codper+"&ret=1",600)
	}
	
	function Editar() {
		f2.action = "escritorio_edtclt.php?clt=<?php echo $RutClt; ?>";
		f2.submit();	
	}
	
	function Historico() {
		f2.action = "escritorio_histo.php?clt=<?php echo $RutClt; ?>";
		f2.submit();	
	}
	
	function mnuCliente() {
		f1.action = "escritorio_cot.php?opc=clt";
		f1.submit();
	}
	
	function mnuVestmed() {
		f1.action = "escritorio_cot.php?opc=";
		f1.submit();
	}
</script>
</head>

<body>
<div id="body">
	<div id="header"></div>
    <ul id="usuario_registro">
		<?php 	echo display_usr($UsrId, $Perfil, $db); ?>
    </ul>
    <div id="work">
<?php formar_topbox ("100%%","center"); ?>
<P align="left"><strong>Escritorio</strong></P>
<P align="center">
<table WIDTH="100%" BORDER="0" CELLSPACING="0" CELLPADDING="1" ALIGN="center">
<tr>
<td width="170px" align="left" valign="top">
	<?php echo display_mnuizq($UsrId, $db); ?>
</td>
<td width="1%">&nbsp;</td>
<td  valign="top" class="label_left_right_top_bottom" STYLE="PADDING-LEFT:20px; PADDING-RIGHT:20px; TEXT-ALIGN:left">
	<table WIDTH="100%" BORDER="0" CELLSPACING="0" CELLPADDING="1" ALIGN="center">
	<tr><td>
		<table WIDTH="100%" BORDER="0" CELLSPACING="0" CELLPADDING="1" ALIGN="right">
		<tr>
			<td STYLE="TEXT-ALIGN: center">
				<fieldset class="label_left_right_top_bottom">
				<legend>Datos Cliente</legend>
					<table WIDTH="95%" BORDER="0" CELLSPACING="5" CELLPADDING="1" ALIGN="center">
					<tr>
						<td width="180" class="dato">RUT</td>
						<td colspan="2" align="left">
						<input name="dfRutClt" id="dfRutClt" size="15" maxLength="30" class="textfieldv2" style="TEXT-TRANSFORM: uppercase" value="<?php echo formatearRut($RutClt); ?>" readOnly />
						</td></tr>
					</tr>
					<?php if ($cod_tipper == 1) { ?>
					<tr>
						<td class="dato">Apellido Paterno</td>
						<td colspan="2" align="left">
						<input name="dfAppPatIn" id="dfAppPat" size="80" maxLength="80" class="textfieldv2" onchange="llenarCampo(this)" value="<?php echo $AppPat; ?>" style="TEXT-TRANSFORM: uppercase" />
						</td>
					</tr>
					<tr>
						<td class="dato">Apellido Materno</td>
						<td colspan="2" align="left">
						<input name="dfAppMatIn" id="dfAppMat" size="80" maxLength="80" class="textfieldv2" onchange="llenarCampo(this)" value="<?php echo $AppMat; ?>" style="TEXT-TRANSFORM: uppercase" />
						</td>
					</tr>
					<tr>
						<td class="dato">Nombre</td>
						<td colspan="2" align="left">
						<input name="dfNomPerIn" id="dfNomPer" size="80" maxLength="80" class="textfieldv2" onchange="llenarCampo(this)" value="<?php echo $NomPer; ?>" style="TEXT-TRANSFORM: uppercase" />
						</td>
					</tr>
					<tr>
						<td class="dato">Profesi&oacute;n y/o Especialidad</td>
						<form id="searchPro" name="searchPro">
						<td align="left" width="30%">
						<select id="pro" name="pro" class="textfieldv2" onChange="filterPro(this)">
							<option selected value="_NONE">Seleccione una Profesi&oacute;n</option>
							<?php //Seleccionar Profesion
							$sp = mssql_query("vm_pro_s ".($Cod_Pro > 0 ? "" : $Cod_Pro), $db);
							while($row = mssql_fetch_array($sp))
							{
								$flagselect = "";
								if ($Cod_Pro == $row['Cod_Pro']) $flagselect = " selected";
							?>
								<option value="<?php echo $row['Cod_Pro'] ?>"<?php echo $flagselect; ?>><?php echo $row['Nom_Pro'] ?></option>
							<?php
							}
							?>
						</select>
						</td>
						</form>
						<form id="searchEsp" name="searchEsp">
						<td align="left">
						<select id="esp" name="esp" class="textfieldv2" onChange="llenarEsp(this)">
							<option selected value="_NONE">Seleccione una Especialidad</option>
							<?php //Seleccionar las ciudades
							$sp = mssql_query("vm_esppro_s $Cod_Pro",$db);
							while($row = mssql_fetch_array($sp))
							{
								$flagselect = "";
								if ($Cod_Esp == $row['Cod_Esp']) $flagselect = " selected";
								?>
								<option value="<?php echo $row['Cod_Esp'] ?>"<?php echo $flagselect; ?>><?php echo $row['Nom_Esp'] ?></option>
								<?php
							}
							?>
						</select>
						</td>
						</form>
					</tr>
					<tr>
						<td class="dato">Sexo</td>
						<td colspan="2" align="left">
						<input id="rbSexoIn" name="rbSexoIn" type="radio" onclick="llenarCampo(this)" value="2" <?php if ($CodSex == 2) echo "checked"; ?>>&nbsp;<strong>Hombre</strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						<input id="rbSexoIn" name="rbSexoIn" type="radio" onclick="llenarCampo(this)" value="1" <?php if ($CodSex == 1) echo "checked"; ?>>&nbsp;<strong>Mujer</strong>
						</td>
					</tr>
						<?php } else { ?>
					<tr>
						<td class="dato">Raz&oacute;n Social</td>
						<td colspan="2" align="left">
						<input name="dfNombreIn" id="dfNombreIn" size="80" maxLength="80" class="textfieldv2" onchange="llenarCampo(this)" value="<?php echo $NombreClt; ?>" style="TEXT-TRANSFORM: uppercase" />
						</td>
					</tr>
					<tr>
						<td class="dato">Nombre de Fantas&iacute;a</td>
						<td colspan="2" align="left">
						<input name="dfNombreFantasiaIn" id="dfNombreFantasiaIn" size="80" maxLength="80" class="textfieldv2" onchange="llenarCampo(this)" value="<?php echo $NombreFanClt; ?>" style="TEXT-TRANSFORM: uppercase" />
						</td>
					</tr>
					<tr>
						<td class="dato">Web</td>
						<td colspan="2" align="left">
						<input name="dfWebIn" id="dfWebIn" size="80" maxLength="80" class="textfieldv2" onchange="llenarCampo(this)" value="<?php echo $web; ?>" />
						</td>
					</tr>
						<?php } ?>
					<form ID="F2" method="POST" name="F2" ACTION="upd_fichausr.php?ret=1&suc=<?php echo $CodSuc; ?>" onsubmit="return CheckBusqueda(this)">
					<tr>
						<td class="dato" colspan="2">&nbsp;</td>
						<td align="right">
							<input type="hidden" name="dfRutClt" value="<?php echo $RutClt; ?>" />
							
							<input type="hidden" name="dfAppPat" value="<?php echo $AppPat; ?>" />
							<input type="hidden" name="dfAppMat" value="<?php echo $AppMat; ?>" />
							<input type="hidden" name="dfNomPer" value="<?php echo $NomPer; ?>" />
							<input type="hidden" name="codpro" value="<?php echo $Cod_Pro; ?>" />
							<input type="hidden" name="codesp" value="<?php echo $Cod_Esp; ?>" />
							<input type="hidden" name="rbSexo" value="<?php echo $CodSex; ?>" />
							
							<input type="hidden" name="dfNombre" value="<?php echo $NombreClt; ?>" />
							<input type="hidden" name="dfNombreFantasia" value="<?php echo $NombreFanClt; ?>" />
							<input type="hidden" name="dfWeb" value="<?php echo $web; ?>" />
							
							<input type="button" name="btnHistorico"  value="Historico" class="btn" onclick="javascript:Historico()" />
							<input type="submit" name="btnGuardar"  value="Guardar" class="btn" />
						</td>
					</tr>
					</table>
					</form>
				</fieldset>
			</td>
		</tr>
		<tr>
			<td STYLE="TEXT-ALIGN: center">
				<fieldset class="label_left_right_top_bottom">
				<legend>Sucursales</legend>
					<form ID="F4" AUTOCOMPLETE="off" method="POST" name="F4" ACTION="del_sucursales.php?clt=<?php echo $Cod_Clt; ?>&ret=1" onsubmit="return CheckEliminarSuc(this)">
					<table WIDTH="95%" BORDER="0" CELLSPACING="1" CELLPADDING="1" ALIGN="center">
					<tr>
						<td class="titulo_tabla" width="1%" align="middle">&nbsp;</td>
						<td class="titulo_tabla" width="19%" align="middle">Sucursal</td>
						<td class="titulo_tabla" width="20%" align="middle">Direcci&oacute;n</td>
						<td class="titulo_tabla" width="20%" align="middle">Comuna</td>
						<td class="titulo_tabla" width="20%" align="middle">Ciudad</td>
						<td class="titulo_tabla" width="10%" align="middle">Telefono</td>
						<td class="titulo_tabla" width="10%" align="middle">&nbsp;</td>
					</tr>
	<?php 
			$iTotPrd = 0;
			$result = mssql_query ("vm_suc_s $Cod_Clt", $db)	or die ("No se pudo leer datos de las Sucursales ($Cod_Clt");
			while (($row = mssql_fetch_array($result))) 
			{
				$iTotPrd++;
				//$page = "registrar_suc.php?clt=".$RutClt."&xis=1&suc=".$row['Cod_Suc'];
				$page = "'".$RutClt."',".$row['Cod_Suc'];
	?>
			<tr>
			<td align="left"><input type="checkbox" class="dato" style="height: 14px" name="seleccionadoSuc[]" value="<?php echo $row["Cod_Suc"]; ?>" /></td>
			<td align="left"><a href="javascript:EditarSuc(<?php echo $page; ?>)"><?php echo utf8_encode($row['Nom_Suc']) ?></a></td>
                        <td align="left"><?php echo utf8_encode($row['Dir_Suc']) ?></td>
			<td align="left"><?php echo utf8_encode($row['Nom_Cmn']) ?></td>
			<td align="left"><?php echo utf8_encode($row['Nom_Cdd']) ?></td>
			<td align="left"><?php echo utf8_encode($row['Fon_Suc']) ?></td>
			<td align="left"><a href="escritorio_edtclt.php?clt=<?php echo $RutClt; ?>&xis=1&suc=<?php echo $row['Cod_Suc']; ?>">Contactos</a></td>		
			</tr>
	<?php 
				if ($iTotPrd == 1) {
					$Nom_Suc = $row['Nom_Suc'];
					$Cod_Suc = $row['Cod_Suc'];
				}
				if ($CodSuc == $row['Cod_Suc']) {
					$Nom_Suc = $row['Nom_Suc'];
					$Cod_Suc = $row['Cod_Suc'];
				}
			}
	?>
			<tr><td colspan="7" align="right">
				<?php if ($Cod_Clt > 0) { ?>
				<input type="submit" name="EliminarSuc" value="Eliminar" class="btn" />
				<input type="button" name="AgregarSuc" value="Agregar" class="btn" onclick="NuevaSuc('<?php echo $RutClt; ?>')" />
				<?php } else echo "&nbsp;"; ?>
				</td>
			</tr>
					</table>
					</form>
				</fieldset>
			</td>
		</tr>
		<tr>
			<td STYLE="TEXT-ALIGN: center">
				<fieldset class="label_left_right_top_bottom">
				<legend id="">Contactos para la Sucursal <?php echo utf8_encode($Nom_Suc); ?></legend>
					<form ID="F3" method="POST" name="F3" ACTION="del_contactos.php?clt=<?php echo $Cod_Clt; ?>&suc=<?php echo $Cod_Suc ?>&ret=1" onsubmit="return CheckEliminarCtt(this)" />
					<table WIDTH="95%" BORDER="0" CELLSPACING="1" CELLPADDING="1" ALIGN="center">
					<tr>
						<td class="titulo_tabla" width="1%" align="middle">&nbsp;</td>
						<td class="titulo_tabla" width="29%" align="middle">Nombre</td>
						<td class="titulo_tabla" width="15%" align="middle">Cargo</td>
						<td class="titulo_tabla" width="10%" align="middle">Profesi&oacute;n</td>
						<td class="titulo_tabla" width="10%" align="middle">Fono</td>
						<td class="titulo_tabla" width="10%" align="middle">Celular</td>
						<td class="titulo_tabla" width="25%" align="middle">e-mail</td>
					</tr>
	<?php 
			$iTotPrd = 0;
			$result = mssql_query ("vm_ctt_s $Cod_Clt, $Cod_Suc", $db)	or die ("No se pudo leer datos del Contacto");
			while (($row = mssql_fetch_array($result))) 
			{
				$iTotPrd++;
				//$page = "registrar_ctt.php?clt=".$RutClt."&xis=1&suc=".$Cod_Suc."&ctt=".$row['Num_Doc'];
				$page = "'".$RutClt."',".$Cod_Suc.",'".$row['Num_Doc']."'";
	?>
			<tr>
			<td align="left"><input type="checkbox" class="dato" style="height: 14px" name="seleccionadoCtt[]" value="<?php echo $row["Cod_Per"]; ?>" onclick="MarcarEliminado(this)"></td>
			<td align="left"><a href="javascript:EditarCtt(<?php echo $page ?>)"><?php echo utf8_encode(trim($row['Pat_Per']).", ".trim($row['Nom_Per'])); ?></a></td>
			<td align="left"><?php echo $row['Cgo_Ctt'] ?></td>
			<td align="left"><?php echo $row['Des_TipCtt'] ?></td>
			<td align="left"><?php echo $row['Fon_Ctt'] ?></td>
			<td align="left"><?php echo $row['Cel_Ctt'] ?></td>
			<td align="left"><?php echo $row['Mail_Ctt'] ?></td>		
			</tr>
	<?php 
			}
	?>
			<tr><td colspan="7" align="right">
				<?php if ($Cod_Clt > 0) { ?>
				<input type="button" name="ResetearPwd" value="Reset Clave" class="btn" onclick="LimpiarPwdCtt()" />&nbsp;
				<input type="submit" name="EliminarCtt" value="Eliminar" class="btn" />&nbsp;
				<input type="button" name="AgregarCtt" value="Agregar" class="btn" onclick="NuevoCtt('<?php echo $RutClt; ?>',<?php echo $Cod_Suc; ?>)" />
				<?php } else echo "&nbsp;"; ?>
				</td>
			</tr>

					</table>
					</form>
				</fieldset>
			</td>
		</tr>
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
	var f3;
	var f4;
	f1 = document.F1;	
	f2 = document.F2;	
	f3 = document.F3;
	f4 = document.F4;
</script>


</body>
</html>
