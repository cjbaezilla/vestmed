<?
//Obtengo los datos de conexion de la base de datos
ini_set('display_errors', '0');
session_start();
include("../config.php");

$Cod_Per = 0;
$Cod_Clt = 0;
$RutClt = "";
$xis = 0;
$EsCliente = false;
$bExisteUsr = false;
if (isset($_SESSION['CodPer'])) $Cod_Per = intval($_SESSION['CodPer']);
if (isset($_SESSION['CodClt'])) $Cod_Clt = intval($_SESSION['CodClt']);
$accion = (isset($_GET['accion'])) ? intval(ok($_GET['accion'])) : 0;
$idmsg = (isset($_GET['id'])) ? intval(ok($_GET['id'])) : 0;

$Cod_Cot = (isset($_GET['cot']) ? intval(ok($_GET['cot'])) : 0);
$Tip_Bus = (isset($_POST['tipo_bus']) ? ok($_POST['tipo_bus']) : 'T');
$PaginaIni = (isset($_POST['pagina']) ? ok($_POST['pagina']) : 1);

if ($Cod_Per > 0) {
	$result = mssql_query ("vm_per_s ".$Cod_Per, $db)
							or die ("No se pudo leer datos del usuario (".$Cod_Per.")");
	if (($row = mssql_fetch_array($result))) {
		$tipo = $row["Cod_TipPer"];
		$sex_ctt = $row["Sex"];
		$tip_doc = $row["Cod_TipDoc"];
		$num_doc = $row["Num_Doc"];
		$nombre  = $row["Pat_Per"]." ".$row["Mat_Per"]." ".$row["Nom_Per"];
		$nom_itt = ""; $email = ""; $fono = "";

		mssql_free_result($result);

		$result = mssql_query ("vm_usrweb_ctt_s ".$Cod_Per, $db)
								or die ("No se pudo leer datos del usuario contacto (".$Cod_Per.")");

		while ($row = mssql_fetch_array($result)) {
			if ($row['Nom_Suc'] != 'MIGRACION') {
				$email = $row['Mail_Ctt'];
				$fono = $row['Fon_Ctt'];
				break;
			}
		}

	}
	mssql_free_result($result);

	$result = mssql_query ("vm_cna_sin_res_ctt ".$Cod_Clt, $db)
							or die ("No se pudo leer datos del cliente");
	if (($row = mssql_fetch_array($result))) $tot_cnactt = $row["tot_cna"];
	mssql_free_result($result);

	$result = mssql_query ("vm_cna_sin_res ".$Cod_Clt, $db)
							or die ("No se pudo leer datos del cliente");
	if (($row = mssql_fetch_array($result))) $tot_cna = $row["tot_cna"];
	mssql_free_result($result);
}

if ($accion == 211) {
    $consulta = str_replace("\'", "''", utf8_decode($_POST['consulta']));
	//$Cod_Cot = ok($_POST['numcot']);
    $result = mssql_query("vm_s_cothdr $Cod_Cot",$db);
    if (($row = mssql_fetch_array($result))) {
        $Cod_Clt   = $row['Cod_Clt'];
        $result = mssql_query("vm_i_cna $Cod_Cot, $Cod_Clt, $Cod_Per, '$consulta'",$db);
        //header("Location: ../ordenes.php?cot=".$Cod_Cot);
        //exit(0);
	//	echo "Mensaje enviado, puede cerrar la ventana.";
    }

}

if ($accion == 21) {
        $consulta = str_replace("\'", "''", utf8_decode($_POST['consulta']));
        $tip_cna = ok($_POST['tipcna']);
        if ($tip_cna == 0) {
            $numcot = ok($_POST['numcot']);
            $Cod_Cot = ok($_POST['numcot']);
            $result = mssql_query("vm_s_cothdr $Cod_Cot",$db);
            if (($row = mssql_fetch_array($result))) {
                $Cod_CltOrg   = $row['Cod_Clt'];
                if ($Cod_Clt != $Cod_CltOrg) $accion = 11;
                else {
                    $consulta = str_replace("\'", "''", $consulta);
                    $result = mssql_query("vm_i_cna $Cod_Cot, $Cod_Clt, $Cod_Per, '$consulta'",$db);
                    $accion = 0;
                }
            }
            else {
                $accion = 11;
            }
        }
	else {
            $archivo = "";
            $accion = 0;
            /*
            $archivo = $_FILES['documento']['name'];
            if ($archivo != "") {
                    $fileupload = $pathadjuntos.$archivo;
                    if (!move_uploaded_file($_FILES['documento']['tmp_name'], $fileupload)){
                       echo $_FILES['documento']['tmp_name']."<br>".$fileupload."<br>";
                       echo "Ocurri&oacute; alg&uacute;n error al subir el fichero. No pudo guardarse.";
                       exit(0);
                    }
            }
            */
            $consulta = str_replace("\'", "''", $consulta);
            //$query = "vm_i_cttweb $tipo, '$nombre', 1, '$num_doc', $sex_ctt, '$nom_itt', '$email', '$fono', $tip_cna, '$consulta ', '$archivo'";
            $result = mssql_query("vm_i_cttweb $tipo, '$nombre', 1, '$num_doc', $sex_ctt, '$nom_itt', '$email', '$fono', $tip_cna, '$consulta ', '$archivo'", $db) or die ("No pudo actualizar mensaje de contactos");
	}
}

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="../css/layout.css" type="text/css" rel="stylesheet" />
<link href="../css/clearfix.css" type="text/css" rel="stylesheet" />
<script type="text/javascript" src="../js/mootools-1.2.1-core-yc.js"></script>
<script type="text/javascript" src="../js/mootools-1.2-more.js"></script>
<script type="text/javascript" src="../dropdown/js/dropdown.js"> </script>
<link rel="stylesheet" type="text/css" media="screen" href="../dropdown/css/uvumi-dropdown.css" />

<link href="../Include/estilos.css" type="text/css" rel="stylesheet" />
<script type="text/javascript" language="JavaScript" src="../Include/SoloNumeros.js"></script>
<script type="text/javascript" language="JavaScript" src="../Include/ValidarDataInput.js"></script>
<script type="text/javascript" language="JavaScript" src="../Include/validarRut.js"></script>
<script type="text/javascript" language="JavaScript" src="../Include/fngenerales.js"></script>
<script type="text/javascript" src="../js/jquery-1.3.2.min.js"></script>
<script type="text/javascript">
    //*************************************
    var $j = jQuery.noConflict();

    //*************************************

    //*************************************



    function checkDataNewMsgForm(form, cot) {
            if (form.tipcna.value == "_NONE")
                {
                    alert("Debe seleccionar un Tipo de Consulta");
                    return false;
                }
            if (cot == 0){
	            if (form.tipcna.value == "0" && form.numcot.value == "") 
	            {
                    alert ("Debe indicar una cotizaci\u00f3n ...");
                    return false;
	            }
				
			}

            if (form.consulta.value == "") {
                    alert("Debe ingresar una consulta ...");
                    return false;
            }

            if (form.consulta.value.length > 1000)
            {
                    alert("El mensaje debe contener a los mas 1.000 caracteres.");
                    return false;
            }
            return true;
    }

    function veropcion(obj)
    {
        if (obj.value == "0")
            $j("#inf_cot").show("slow");
        else
            $j("#inf_cot").hide();
    }

    function ActualizaPadre()
    {
        parent.opener.actualizar_qtymsg();
        window.close();
    }
	
</script>
<!--[if IE]>
<link rel="stylesheet" type="text/css" media="screen" href="../dropdown/css/uvumi-dropdown-ie.css" />
<![endif]-->
<script type="text/javascript">
new UvumiDropdown('dropdown-scliente');

function popwindow(ventana){
   window.open(ventana,"Anexos",'toolbar=0,location=0,scrollbars=yes,titlebar=no,menubar=no,resizable=0,left=100,top=100,width=640,height=385')
}
</script>
<!-- Lytebox Includes //-->
<script type="text/javascript" src="../lytebox/lytebox.js"></script>
<link rel="stylesheet" type="text/css" href="../lytebox/lytebox.css" media="screen" />
<!-- Lytebox Includes //-->
</head>
<body>
    <div id="body">
        <div id="header"></div>
        <div id="work">
            <div id="back-registro3">
                <img src="../images/registro/mihistorial.png" style="top:60px;" class="titulo-principal-avisos" alt="" />
                <div style="width:765px; margin:0 auto 0 100px; padding-top:10px;">
                <?php if ($accion == 1 or $accion == 11 or $accion == 111 or $accion == 211) { ?>
                    <h3>Nuevo Mensaje</h3>
                    <form ID="F2" method="post" name="F2" ACTION="lyt_vermensajes.php?accion=<?php if ($accion == 111) echo "211"; else echo "21"; ?>&cot=<?php echo $Cod_Cot?>" onsubmit="return checkDataNewMsgForm(this,<?php echo $Cod_Cot?>)" enctype="multipart/form-data" >
                    <table BORDER="0" CELLSPACING="1" CELLPADDING="5" width="650" ALIGN="center">
                    <tr>
                            <td width="30%" VALIGN="TOP" class="dato"><b>Tipo Consulta</b></td>
                            <td width="30%" VALIGN="TOP" class="dato">
                            <select class="select-contacto" name="tipcna" id="tipcna" onclick="veropcion(this)">
                            <option selected value="_NONE">Seleccione tipo de Consulta</option>
                            <option value="0"<?php if ($accion == 11 or $accion == 111) echo "selected"; ?>>Asociado a una Cotizaci&oacute;n</option>
                            <option value="1">Informaci&oacute;n del Producto</option>
                            <option value="2">Reclamos</option>
                            <option value="3">Contacto Comercial</option>
                            <option value="4">Solicitud de Catalogos</option>
                            <option value="5">Informaci&oacute;n de sus Ordenes</option>
                            <option value="6">Otro</option>
                            </select>
                            </td>
                            <td width="40%" VALIGN="TOP" class="dato">
                                <div id="inf_cot">
                                    Cotizaci&oacute;n :
                                    <select class="select-contacto" name="numcot" id="numcot">
                                        <option selected value="">Seleccione una Cotizaci&oacute;n</option>
                                        <?php 
                                            $result = mssql_query ("vm_cmb_cot ".$Cod_Per, $db)
                                                                                            or die ("No se pudo leer datos de las cotizaciones (".$Cod_Per.")");
                                            while (($row = mssql_fetch_array($result))) {                                        
                                        ?>
                                        <option value="<?php echo $row['Cod_Cot'] ?>"<?php if (($accion == 11 or $accion == 111) and $Cod_Cot == $row['Cod_Cot']) echo " selected" ?>><?php echo $row['Num_Cot']; ?></option>
                                        <?php
                                            }
                                        ?>
                                    </select>
                                </div>
                            </td>
                    </tr>
                    <tr>
                            <td width="30%" VALIGN="TOP" class="dato"><b>Nuevo Mensaje</b></td>
                            <td width="70%" VALIGN="TOP" class="dato" colspan="2">
                            <textarea class="textfieldv2" rows="5" cols="100" name="consulta"><?php if ($accion == 11) echo $consulta; ?></textarea>
                            </td>
                    </tr>
                    <?php if ($accion == 11) { ?>
                        <tr>
                            <td width="100%" colspan="3" valign="top" class="datorojo" style="padding-top: 10px; padding-bottom: 10px;">Cotizaci&oacute;n ingresada no Existe o no le pertenece. Favor ingresar una cotizaci&oacute;n v&aacute;lida.</td>
                        </tr>
                    <?php } ?>
                    <tr>
                            <td width="100%" VALIGN="TOP" class="dato" colspan="3" style="text-align: right">
                            <input type="button" name="Volver" value=" Volver " class="btn" onclick="javascript:window.close()" />&nbsp;&nbsp;
                            <input type="submit" name="Enviar" value=" Enviar " class="btn" />
                            </td>
                    </tr>
                    </table>
                    </form>
                <?php } ?>
                </div>
            </div>
        </div>
	<div id="footer"></div>
    </div>
<script type="text/javascript">
	var f1;
	var f2;

	f1 = document.F1;
	f2 = document.F2;
        tipcna = document.getElementById("tipcna");
        numcot = document.getElementById("numcot");
        tipcna.disabled = true;
        numcot.disabled = true;

        <?php if ($accion != 11 and $accion != 111) { ?>
        $j("#inf_cot").hide();
        <?php } ?>

	<?php if ($accion == 211){ ?>
	ActualizaPadre();	
	<?php } ?>

</script>
</body>
</html>
