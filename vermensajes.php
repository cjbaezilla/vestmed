<?php
//Obtengo los datos de conexion de la base de datos
ini_set('display_errors', '0');
session_start();
include("config.php");

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
    $Cod_Cot = ok($_POST['numcot']);
    $result = mssql_query("vm_s_cothdr $Cod_Cot",$db);
    if (($row = mssql_fetch_array($result))) {
        $Cod_Clt   = $row['Cod_Clt'];
        $result = mssql_query("vm_i_cna $Cod_Cot, $Cod_Clt, $Cod_Per, '$consulta'",$db);
        header("Location: ordenes.php?cot=".$Cod_Cot);
        exit(0);
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
            $result = mssql_query("vm_i_cttweb $tipo, '$nombre', 1, '$num_doc', $sex_ctt, '$nom_itt', '$email', '$fono', $tip_cna, '$consulta ', '$archivo'", $db) 
                                  or die ("No pudo actualizar mensaje de contactos");
	}
}

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Registro - Vestmed Vestuario M&eacute;dico</title>
<link href="css/layout.css" type="text/css" rel="stylesheet" />
<link href="css/clearfix.css" type="text/css" rel="stylesheet" />
<script type="text/javascript" src="js/mootools-1.2.1-core-yc.js"></script>
<script type="text/javascript" src="js/mootools-1.2-more.js"></script>
<script type="text/javascript" src="dropdown/js/dropdown.js"> </script>
<link rel="stylesheet" type="text/css" media="screen" href="dropdown/css/uvumi-dropdown.css" />

<link href="Include/estilos.css" type="text/css" rel="stylesheet" />
<script type="text/javascript" language="JavaScript" src="Include/SoloNumeros.js"></script>
<script type="text/javascript" language="JavaScript" src="Include/ValidarDataInput.js"></script>
<script type="text/javascript" language="JavaScript" src="Include/validarRut.js"></script>
<script type="text/javascript" language="JavaScript" src="Include/fngenerales.js"></script>
<script type="text/javascript" src="js/jquery-1.3.2.min.js"></script>
<script type="text/javascript">
    //*************************************
    var $j = jQuery.noConflict();
    $j(document).ready
    (
            //$j(":input:first").focus();

            function()
            {
                $j("form#searchMsg").submit(function(){
                        $j.post("ajax-search.php",{
                                search_type: "msg",
                                param_clt: $j("#cod_clt").val(),
                                param_bus: $j("#tipo_bus").val(),
                                param_pag: $j("#pagina").val()
                        }, function(xml) {
                                listMsg(xml);
                        });
                        return false;
                });

                //NO SUBMIT TODAVIA $j("form#patternlist-form").submit();
            }

    );
    //TODO: No se esta realizando el post, probablemente debido a que falta el evento submit.

    function listMsg (xml)
    {
        var arrTipCna = ["Cotizaci\u00f3n","Informaci\u00f3n del Producto","Reclamos","Contacto Comercial","Solicitud de Cat\u00e1logos","Informaci\u00f3n de sus Ordenes","Otro"];
        var options;
        var sPagina = "";

        options="<table id=\"tblMensajes\" BORDER=\"0\" CELLSPACING=\"1\" CELLPADDING=\"3\" width=\"630\" ALIGN=\"center\">\n";
        $j("filter",xml).each(
           function(id)
           {
                filter=$j("filter",xml).get(id);
                options+="<tr>\n";
                options+="<td width=\"28\" valign=\"top\" style=\"padding-top: 10px\">";
                if (parseInt($j("ctdsinlec",filter).text()) > 0) {
                    options+="<img src=\"images/mail2.png\" width=\"49px\" height=\"47px\" alt=\"\" />\n";
                }
                else {
                    options+="<img src=\"images/email.png\" width=\"49px\" height=\"47px\" alt=\"\" />\n";
                }
                options+="</td>\n";
                options+="   <td align=\"left\" valign=\"top\" style=\"padding-top: 10px\">\n";
                if ($j("tipo",filter).text() > "0")
                   options+="     <b>Tipo Mensaje: Formulario Contacto</b><br/>";
                else
                   options+="     <b>Tipo Mensaje: Formulario Cotizaci\u00f3n</b><br/>";
                options+="Clase: "+arrTipCna[$j("tipo",filter).text()]+"<br/>";
                options+="Caso: #"+$j("foliodis",filter).text();
                options+="</td>\n";
                options+="<td align=\"right\" valign=\"top\" style=\"padding-top: 10px\">\n";
                options+=$j("fecha",filter).text()+"<BR/>";
                if ($j("tipo",filter).text() > "0")
                    sPagina = "verdetallemensajes.php?folctt="+$j("folio",filter).text()+"&pag="+$j("#pagina").val();
                else
                    sPagina = "verdetallemensajes.php?cot="+$j("folio",filter).text()+"&pag="+$j("#pagina").val();
                if (parseInt($j("ctdsinlec",filter).text()) > 0) 
                    options+=$j("ctd",filter).text()+" Mensaje(s) / <a href=\""+sPagina+"\">"+$j("ctdsinlec",filter).text()+" NO leido(s)</a>";
                else
                    options+="<a href=\""+sPagina+"\">"+$j("ctd",filter).text()+" Mensaje(s)</a> ";

                options+="</td>\n";
                options+="</tr>\n";
                options+="<tr>\n";
                options+="<td align=\"right\" colspan=\"3\" width=\"100%\" style=\"padding-bottom: 10px; border-bottom: grey 1px solid;\">\n";
                options+="&nbsp;\n";
                options+="</td>\n";
                options+="</tr>\n";
           }
        );
        options+="</table>";
        $j("#tblMensajes").replaceWith(options);

        options="<table id=\"tblTipoBusqueda\" BORDER=\"0\" CELLSPACING=\"0\" CELLPADDING=\"0\" width=\"630\" ALIGN=\"center\">\n";
        options+="<tr>\n";
        options+="<td width=\"100%\" style=\"padding-top: 10px; padding-bottom: 10px; border-bottom: grey 1px solid; border-top: grey 1px solid;\">\n";
        options+="Seleccionar: ";
        if ($j("#tipo_bus").val() == "T")
            options+="TODOS |";
        else
            options+="<a href=\"javascript:listarMsg('T')\">TODOS</a> | ";
        if ($j("#tipo_bus").val() == "A")
            options+="ABIERTOS";
        else
            options+="<a href=\"javascript:listarMsg('A')\">ABIERTOS</a>";
        options+="</td>\n";
        options+="</tr>\n";
        options+="</table>\n";
        $j("#tblTipoBusqueda").replaceWith(options);
        

        sPagina = "<spam id=\"lbl_pagina\">"+$j("#pagina").val()+"</spam>";
        $j("#lbl_pagina").replaceWith(sPagina);

    }

    //*************************************

    function Next_Msg() {
        var npagina=0;
        var limitesup=0;

        npagina = parseInt($j("#pagina").val()) + 1;
        limitesup = parseInt($j("#tot_paginas").val());

        if (npagina <= limitesup) {
            $j("#pagina").val(npagina);
            $j("form#searchMsg").submit();
        }
        else
            alert("No existen m\u00e1s p\u00e1ginas que mostrar");
    }

    function Prev_Msg() {
        var pagina;

        pagina = parseInt($j("#pagina").val()) - 1;

        if (pagina > 0) {
            $j("#pagina").val(pagina);
            $j("form#searchMsg").submit();
        }
        else
            alert("No existen m\u00e1s p\u00e1ginas que mostrar");
    }

    function FirstPage() {
        $j("#pagina").val(1);
        $j("form#searchMsg").submit();
    }

    function LastPage() {
        $j("#pagina").val($j("#tot_paginas").val());
        $j("form#searchMsg").submit();
    }
    //*************************************

    function Enviar_Res(folio,cot) {
            if (eval('f2.respuesta'+folio).value == "") {
                    alert("Debe ingresar una respuesta");
                    return false;
            }
            if (eval('f2.respuesta'+folio).value.length > 1000) {
                    alert("El mensaje debe contener a los mas 1.000 caracteres.");
                    return false;
            }
            f2.action = "mismensajes.php?cot="+cot+"&folio="+folio+"&accion=respuesta";
            f2.submit();
    }

    function filterCot(obj) {
            f2.NumCot.value = obj.value;
    }

    function Nuevo_Msg(caso,id) {
            f2.action = "mismensajes.php?accion="+caso+"&id="+id;
            f2.submit();
    }

    function Salir(caso) {
            f2.action = "mismensajes.php?accion="+caso;
            f2.submit();
    }

    function checkDataForm(form,cot) {
            if (cot == 0)
                    if (form.numcot.value == "_NONE")
                    {
                            alert ("Debe indicar una cotizaci\u00f3n ...");
                            return false;
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

    function checkDataNewMsgForm(form) {
            if (form.tipcna.value == "_NONE")
                {
                    alert("Debe seleccionar un Tipo de Consulta");
                    return false;
                }
                
            if (form.tipcna.value == "0" && form.numcot.value == "")
            {
                    alert ("Debe indicar una cotizaci\u00f3n ...");
                    return false;
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

    function respondercot(cot) {
            f2.action = "mismensajes2.php?cot="+cot;
            f2.submit();
    }

    function responderctt(folio) {
            f2.action = "mismensajes2.php?folctt="+folio;
            f2.submit();
    }

    function GoOpcion(pagina)
    {
        document.toolBar.action = pagina;
        document.toolBar.submit();
    }

    function veropcion(obj)
    {
        if (obj.value == "0")
            $j("#inf_cot").show("slow");
        else
            $j("#inf_cot").hide();
    }

    function listarMsg(caso) {
        $j("#pagina").val(1);
        $j("#tipo_bus").val(caso);
        $j("form#searchTipo").submit();
    }
</script>
<!--[if IE]>
<link rel="stylesheet" type="text/css" media="screen" href="dropdown/css/uvumi-dropdown-ie.css" />
<![endif]-->
<script type="text/javascript">
new UvumiDropdown('dropdown-scliente');

function popwindow(ventana){
   window.open(ventana,"Anexos",'toolbar=0,location=0,scrollbars=yes,titlebar=no,menubar=no,resizable=0,left=100,top=100,width=640,height=385')
}
</script>
<!-- Lytebox Includes //-->
<script type="text/javascript" src="lytebox/lytebox.js"></script>
<link rel="stylesheet" type="text/css" href="lytebox/lytebox.css" media="screen" />
<!-- Lytebox Includes //-->
</head>
<body>
    <div id="body">
        <div id="header"></div>
        <div class="menu"id="menu-noselect">
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
            <form ID="F1" method="POST" name="F1" action="">
                <li class="back-verde registro"><a href="registrarse.php">REGISTRARSE</a></li>
                <li class="olvido"><a href="javascript:EnviarClave()">OLVID&Oacute; SU CLAVE?</a></li>
                <li class="back-verde"><a href="javascript:ValidarLogin()">ENTRAR</a></li>
                <li class="back-verde inputp"><input type="password" name="dfclave"/></li>
                <li class="back-verde">CONTRASE&Ntilde;A</li>
                <li class="back-verde inputp"><input type="" name="rut" id="rut" onblur="formatearRut('rut','dfrut')" /></li>
                <li class="back-verde">RUT</li>
                <input type="hidden" name="dfrut" id="dfrut" />
            </form>
        </ul>
        <?php }
            else {
        ?>
        <ul id="usuario_registro">
            <?php echo display_login($Cod_Per, $Cod_Clt, $db, 0); ?>
        </ul>
	<?php
		}
	?>
        <div id="work">
            <div id="back-registro3">
                <img src="images/registro/mihistorial.png" style="top:60px;" class="titulo-principal-avisos" alt="" />
                <div style="width:765px; margin:0 auto 0 100px; padding-top:10px;">
                <?php if ($accion == 0) { ?>
                    <h3>Mensajes</h3>
                    <form ID="toolBar" name="toolBar" action="" method="post">
                    <table BORDER="0" CELLSPACING="1" CELLPADDING="3" width="630" ALIGN="center">
                        <tr>
                            <th><input type="button" name="btnInbox" id="btnInbox" value=" Inbox " class="btn4" onclick="javascript:GoOpcion('<?php echo $_SERVER['PHP_SELF']; ?>')" /></th>
                            <th><input type="button" name="btnNuevo" id="btnNuevo" value=" Nuevo " class="btn3" onclick="javascript:GoOpcion('<?php echo $_SERVER['PHP_SELF']."?accion=1"; ?>')" /></th>
                            <th><!--input type="button" name="btnBuscar" id="btnBuscar" value=" Buscar " class="btn3" onclick="javascript:GoOpcion('<?php echo $_SERVER['PHP_SELF']."?accion=2"; ?>')" /--></th>
                        </tr>
                        <tr>
                            <td colspan="3">
                                
                            </td>
                        </tr>
                    </table>
                    </form>
                    <table id="tblTipoBusqueda" BORDER="0" CELLSPACING="0" CELLPADDING="0" width="630" ALIGN="center">
                        <tr>
                            <td width="100%" style="padding-top: 10px; padding-bottom: 10px; border-bottom: grey 1px solid; border-top: grey 1px solid;">
                                Seleccionar:
                                <?php if ($Tip_Bus != "T") { ?><a href="javascript:listarMsg('T')">TODOS</a><?php } else { ?>TODOS<?php } ?> |
                                <?php if ($Tip_Bus != "A") { ?><a href="javascript:listarMsg('A')">ABIERTOS</a><?php } else { ?>ABIERTOS<?php } ?>
                            </td>
                        </tr>
                    </table>
                    <form ID="searchMsg" name="searchMsg" action="">
                    <div style="width:765px; height: 400px; margin:0 auto 0 0px; padding-top:10px;">
                    <table id="tblMensajes" BORDER="0" CELLSPACING="1" CELLPADDING="3" width="630" ALIGN="center">
                        <?php
                            $aTipCna   = array(0 => "Cotizaci&oacute;n",
                                               1 => "Informaci&oacute;n del Producto",
                                               2 =>  "Reclamos",
                                               3 =>  "Contacto Comercial",
                                               4 =>  "Solicitud de Cat&aacute;logos",
                                               5 =>  "Informaci&oacute;n de sus Ordenes",
                                               6 =>  "Otro");

                            $result = mssql_query("vm_count_casos $Cod_Per, '$Tip_Bus'", $db);
                            if (($row = mssql_fetch_array($result))) $total = $row['total'];

                            $result = mssql_query("vm_count_msj $Cod_Per, '$Tip_Bus'", $db);
                            if (($row = mssql_fetch_array($result))) $total_msj = $row['total'];

                            $pagina = $PaginaIni;
                            $tot_paginas = intval($total / 5) + 1;
                            if (($total % 5) == 0) $tot_paginas--;
                            $limite1 = ($pagina-1) * 5 + 1;
                            $limite2 = $limite1+4;

                            $Fec_Cna = date('Ymd');
                            $result = mssql_query("vm_s_msj_per $Cod_Per, '$Tip_Bus', $limite1, $limite2", $db);
                            while (($row = mssql_fetch_array($result))) {
                        ?>
                        <tr>
                            <td width="28" valign="top" style="padding-top: 10px">
                                <?php if ($row['QtySinLec'] > 0) { ?>
                                <img src="images/mail2.png" width="49px" height="47px" alt="" />
                                <?php } else { ?>
                                <img src="images/email.png" width="49px" height="47px" alt="" />
                                <?php } ?>
                            </td>
                            <td align="left" valign="top" style="padding-top: 10px">
                                <b>Tipo Mensaje: <?php echo $row['Tipo'] > 0 ? "Formulario Contacto" : "Formulario Cotizaci&oacute;n"; ?></b><br/>
                                Clase: <?php echo $aTipCna[$row['Tipo']]; ?><br/>
                                Caso: #<?php echo $row['FolioDis'] ?>
                            </td>
                            <td align="right" valign="top" style="padding-top: 10px">
                                <?php
                                //echo date("d/m/Y", strtotime($row['Fecha']))."<BR/>";
                                echo $row['Fecha']."<BR/>";
                                if ($row['Tipo'] > 0)
                                    $link = "verdetallemensajes.php?folctt=".$row['Folio']."&pag=".$pagina;
                                else
                                    $link = "verdetallemensajes.php?cot=".$row['Folio']."&pag=".$pagina;
                                if ($row['QtySinLec'] > 0) {
                                    echo $row['Qty']." Mensaje(s) / <a href=\"$link\">".$row['QtySinLec']." NO leido(s)</a>";
                                }
                                else
                                    echo "<a href=\"$link\">".$row['Qty']." Mensaje(s)</a> "
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td align="right" colspan="3" width="100%" style="padding-bottom: 10px; border-bottom: grey 1px solid;">
                                &nbsp;
                            </td>
                        </tr>
                        <?php
                            }
                        ?>
                    </table>
                    </div>
                    </form>
                    <table BORDER="0" CELLSPACING="1" CELLPADDING="3" width="630" ALIGN="center">
                        <tr>
                            <td align="right" colspan="3" width="100%" style="padding-top: 25px;">
                                Total: <?php echo $total ?> Casos & <?php echo $total_msj ?> Mensajes
                            </td>
                        </tr>
                        <tr>
                            <td align="left" colspan="2" style="padding-top: 5px; padding-bottom: 5px; border-top: grey 1px solid; border-bottom: grey 1px solid;">
                                P&aacute;gina : <a href="javascript:Prev_Msg()">Anterior</a>  | <a href="javascript:Next_Msg()">Siguiente</a>&nbsp;&nbsp;&nbsp;&nbsp;<spam id="lbl_pagina"><?php echo $pagina; ?></spam> de <?php echo $tot_paginas; ?>
                            </td>
                            <td align="right" style="padding-top: 5px; padding-bottom: 5px; border-top: grey 1px solid; border-bottom: grey 1px solid;">
                                <a href="javascript:FirstPage()">Primera</a>  | <a href="javascript:LastPage()">Ultima</a>
                            </td>
                        </tr>
                    </table>
                    <form ID="searchTipo" name="searchTipo" action="<?php echo $_SERVER['PHP_SELF']?>" method="POST">
                    <input type="hidden" id="pagina" value="<?php echo $pagina; ?>" />
                    <input type="hidden" id="tot_paginas" value="<?php echo $tot_paginas; ?>" />
                    <input type="hidden" id="cod_clt" value="<?php echo $Cod_Clt; ?>" />
                    <input type="hidden" id="tipo_bus" name="tipo_bus" value="<?php echo $Tip_Bus; ?>" />
                    </form>

                <?php } elseif ($accion == 1 or $accion == 11 or $accion == 111) { ?>
                    <h3>Nuevo Mensaje</h3>
                    <form ID="toolBar" name="toolBar" action="" method="post">
                    <table BORDER="0" CELLSPACING="1" CELLPADDING="3" width="630" ALIGN="center">
                        <tr>
                            <th><input type="button" name="btnInbox" id="btnInbox" value=" Inbox " class="btn3" onclick="javascript:GoOpcion('<?php echo $_SERVER['PHP_SELF']; ?>')" /></th>
                            <th><input type="button" name="btnNuevo" id="btnNuevo" value=" Nuevo " class="btn4" onclick="javascript:GoOpcion('<?php echo $_SERVER['PHP_SELF']."?accion=1"; ?>')" /></th>
                            <th><!--input type="button" name="btnBuscar" id="btnBuscar" value=" Buscar " class="btn3" onclick="javascript:GoOpcion('<?php echo $_SERVER['PHP_SELF']."?accion=2"; ?>')" /--></th>
                        </tr>
                        <tr>
                            <td colspan="3">

                            </td>
                        </tr>
                    </table>
                    </form>
                    <form ID="F2" method="post" name="F2" ACTION="vermensajes.php?accion=<?php if ($accion == 111) echo "211"; else echo "21"; ?>" onsubmit="return checkDataNewMsgForm(this)" enctype="multipart/form-data" >
                    <table BORDER="0" CELLSPACING="1" CELLPADDING="5" width="650" ALIGN="center">
                    <tr>
                            <td width="30%" VALIGN="TOP" class="dato"><b>Tipo Consulta</b></td>
                            <td width="30%" VALIGN="TOP" class="dato">
                            <select class="select-contacto" name="tipcna" onclick="veropcion(this)">
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
                                    <select class="select-contacto" name="numcot">
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
                            <input type="button" name="Volver" value=" Volver " class="btn" onclick="javascript:GoOpcion('<?php if ($accion == 111) echo "ordenes.php?cot=$Cod_Cot"; else echo $_SERVER['PHP_SELF']; ?>')" />&nbsp;&nbsp;
                            <input type="submit" name="Enviar" value=" Enviar " class="btn" />
                            </td>
                    </tr>
                    </table>
                    </form>
                <?php } elseif ($accion == 2) { ?>
                    <h3>Busqueda</h3>
                    <form ID="toolBar" name="toolBar" action="" method="post">
                    <table BORDER="0" CELLSPACING="1" CELLPADDING="3" width="630" ALIGN="center">
                        <tr>
                            <th><input type="button" name="btnInbox" id="btnInbox" value=" Inbox " class="btn3" onclick="javascript:GoOpcion('<?php echo $_SERVER['PHP_SELF']; ?>')" /></th>
                            <th><input type="button" name="btnNuevo" id="btnNuevo" value=" Nuevo " class="btn3" onclick="javascript:GoOpcion('<?php echo $_SERVER['PHP_SELF']."?accion=1"; ?>')" /></th>
                            <th><!--input type="button" name="btnBuscar" id="btnBuscar" value=" Buscar " class="btn4" onclick="javascript:GoOpcion('<?php echo $_SERVER['PHP_SELF']."?accion=2"; ?>')" /--></th>
                        </tr>
                        <tr>
                            <td colspan="3">

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
<script type="text/javascript" language="javascript">
	var f1;
	var f2;

	f1 = document.F1;
	f2 = document.F2;

        <?php echo "//accion=$accion\n//tip_cna=$tip_cna\n"; ?>
        <?php if ($accion != 11 and $accion != 111) { ?>
        $j("#inf_cot").hide();
        <?php } ?>
</script>
</body>
</html>
