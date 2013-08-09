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
$pagina = 1;
if (isset($_SESSION['CodPer'])) $Cod_Per = intval($_SESSION['CodPer']);
if (isset($_SESSION['CodClt'])) $Cod_Clt = intval($_SESSION['CodClt']);
if (isset($_POST['pagina'])) $pagina = intval($_POST['pagina']);
if ($Cod_Per > 0) {
	$result = mssql_query ("vm_per_s ".$Cod_Per, $db)
							or die ("No se pudo leer datos del usuario");
	if (($row = mssql_fetch_array($result))) {
		$tip_doc = $row["Cod_TipDoc"];
		$num_doc = $row["Num_Doc"];
	}
	mssql_free_result($result); 
}
$total = 0;
$result = mssql_query("vm_s_count_tracking $Cod_Clt", $db);
if (($row = mssql_fetch_array($result))) $total = $row['tot_odc'];

$sizepage = 18;
$desde = ($pagina - 1) * $sizepage + 1;
$hasta = $desde + $sizepage - 1;
$tot_paginas = intval($total / $sizepage);
if (($total % $sizepage) > 0) $tot_paginas++;
?>

<!DOCTYPE html PUBLIC "-//W3C//Dtd XHTML 1.0 transitional//EN" "http://www.w3.org/tr/xhtml1/Dtd/xhtml1-transitional.dtd">
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
<script type="text/javascript" src="Include/SoloNumeros.js"></script>
<script type="text/javascript" src="Include/ValidarDataInput.js"></script>
<script type="text/javascript" src="Include/validarRut.js"></script>
<script type="text/javascript" src="Include/fngenerales.js"></script>
<script type="text/javascript" src="js/jquery-1.3.2.min.js"></script>
<script type="text/javascript">
    var $j = jQuery.noConflict();
    $j(document).ready
	(
		
            function()
            {
                $j("form#searchOdc").submit(function(){
                        $j.post("ajax-search.php",{
                                search_type: "odc",
                                param_clt: $j("#cod_clt").val(),
                                param_sze: $j("#size_page").val(),
                                param_pag: $j("#pagina").val()
                        }, function(xml) {
                                listODC(xml);
                        });
                        return false;
                });
	        //NO SUBMIT TODAVIA $j("form#patternlist-form").submit();
	    }		
	);
    //TODO: No se esta realizando el post, probablemente debido a que falta el evento submit.

    function listODC (xml)
    {
        var images = "";
        var estado = "";
        var options;
        var mensaje = "";

        options="<table id=\"tblOrdenes\" WIDTH=\"95%\" BORDER=\"0\" align=\"center\" CELLSPACING=\"0\" CELLPADDING=\"1\" style=\"margin-top:20px;\">\n";

 	options+="<tr>\n";
	options+="<td class=\"titulo_tabla\" width=\"10%\" align=\"center\">Cotizaci&oacute;n</td>\n";
	options+="<td class=\"titulo_tabla\" width=\"10%\" style=\"TEXT-ALIGN:center\">NV</td>\n";
	options+="<td class=\"titulo_tabla\" width=\"15%\" style=\"TEXT-ALIGN:center\">F.Envio</td>\n";
	options+="<td class=\"titulo_tabla\" colspan=\"2\" width=\"20%\" style=\"TEXT-ALIGN:center\">Estado</td>\n";
	options+="<td class=\"titulo_tabla\" colspan=\"2\" width=\"25%\" style=\"TEXT-ALIGN:center\">Mensajes</td>\n";
	options+="<td class=\"titulo_tabla\" colspan=\"2\" width=\"20%\" style=\"TEXT-ALIGN:center\">Opciones</td>\n";
	options+="</tr>\n";

        $j("filter",xml).each(
           function(id)
           {
                filter=$j("filter",xml).get(id);

         	options+="<tr>\n";
                options+="<td style=\"TEXT-ALIGN:center\">"+$j("numcot",filter).text()+"</td>\n";
                options+="<td style=\"TEXT-ALIGN:center\">"+$j("codnvt",filter).text()+"</td>\n";
                options+="<td style=\"TEXT-ALIGN:center\">"+$j("fecnvt",filter).text()+"</td>\n";

                if ($j("arcadj",filter).text() == " " && $j("numtrnbco",filter).text() == " ") {
                    images = "001_30.gif";
                    estado = "Enviada sin Pago";
                }
                else {
                    images = "001_06.gif";
                    estado = "Enviada con Pago";
                }
                options+="<td style=\"TEXT-ALIGN:left\"><img src=\"images/"+images+"\" alt=\"\" /></td>\n";
                options+="<td style=\"TEXT-ALIGN:left\">"+estado+"</td>\n";

                options+="<td style=\"TEXT-ALIGN:right\" width=\"10%\"><img src=\"images/mail.png\" alt=\"\" /></td>\n";

                Tot_Cna = parseInt($j("totcna",filter).text());
                Tot_New = parseInt($j("totsinres",filter).text());
                if (Tot_Cna == 0) mensaje = "Ninguno";
                else {
                        mensaje = Tot_Cna.toString();
                        if (Tot_New > 0) mensaje += ", " + Tot_New.toString() + " No le\u00eddo(s)";
                }

                options+="<td style=\"TEXT-ALIGN:left\" width=\"15%\">"+mensaje+"</td>\n";
                options+="<td style=\"TEXT-ALIGN:right\"><img src=\"images/001_38.gif\" alt=\"\" ></td>\n";

                options+="<td style=\"TEXT-ALIGN:left\"><a href=\"ordenes.php?cot="+$j("codcot",filter).text()+"\">Ver</a></td>\n";
        	options+="</tr>\n";

           }
        );
        options+="</table>";
        $j("#tblOrdenes").replaceWith(options);

        sPagina = "<spam id=\"lbl_pagina\">"+$j("#pagina").val()+"</spam>";
        $j("#lbl_pagina").replaceWith(sPagina);

    }

    //*************************************

    function Next_Page() {
        var npagina=0;
        var limitesup=0;

        npagina = parseInt($j("#pagina").val()) + 1;
        limitesup = parseInt($j("#tot_paginas").val());

        if (npagina <= limitesup) {
            $j("#pagina").val(npagina);
            $j("form#searchOdc").submit();
        }
        else
            alert("No existen m\u00e1s p\u00e1ginas que mostrar");
    }

    function Prev_Page() {
        var pagina;

        pagina = parseInt($j("#pagina").val()) - 1;

        if (pagina > 0) {
            $j("#pagina").val(pagina);
            $j("form#searchOdc").submit();
        }
        else
            alert("No existen m\u00e1s p\u00e1ginas que mostrar");
    }

    function FirstPage() {
        $j("#pagina").val(1);
        $j("form#searchOdc").submit();
    }

    function LastPage() {
        $j("#pagina").val($j("#tot_paginas").val());
        $j("form#searchOdc").submit();
    }
    //*************************************
    
</script>
<!--[if IE]>
<link rel="stylesheet" type="text/css" media="screen" href="dropdown/css/uvumi-dropdown-ie.css" />
<![endif]-->
<script type="text/javascript">
new UvumiDropdown('dropdown-scliente');

function popwindow(ventana){
   window.open(ventana,"Anexos",'toolbar=0,location=0,scrollbars=yes,resizable=1,left=60,top=40,width=830,height=600')
}
</script>
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
                            <a href="tracking-ordenes.htm">tracking de &Oacute;rdenes</a>
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
    	<li class="back-verde registro"><a href="registrarse.php">REGIStrARSE</a></li>
        <li class="olvido"><a href="javascript:EnviarClave()">OLVID&Oacute; SU CLAVE?</a></li>
        <li class="back-verde"><a href="javascript:ValidarLogin()">ENtrAR</a></li>
        <li class="back-verde inputp"><input type="password" name="dfclave"/></li>
        <li class="back-verde">CONtrASE&Ntilde;A</li>
        <li class="back-verde inputp"><input type="" name="rut" id="rut" onblur="formatearRut('rut','dfrut')"></li>
        <li class="back-verde">RUT</li>
		<input type="hidden" name="dfrut" id="dfrut" />
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
    <div id="back-registro2">
		<img src="images/registro/mihistorial.png" style="top:60px;" class="titulo-principal-avisos" alt="" />
            	<div style="width:765px; margin:0 auto 0 100px; padding-top:10px;">
                                <div style="width:765px; height: 520px; margin:0 auto 0 0px; padding-top:10px;">
				<table id="tblOrdenes" WIDTH="95%" BORDER="0" align="center" CELLSPACING="0" CELLPADDING="1" style="margin-top:20px;">
				<tr>
					<td class="titulo_tabla" width="10%" align="center">Cotizaci&oacute;n</td>
					<td class="titulo_tabla" width="10%" style="TEXT-ALIGN:center">NV</td>
					<td class="titulo_tabla" width="15%" style="TEXT-ALIGN:center">F.Envio</td>
					<td class="titulo_tabla" colspan="2" width="20%" style="TEXT-ALIGN:center">Estado</td>
					<td class="titulo_tabla" colspan="2" width="25%" style="TEXT-ALIGN:center">Mensajes</td>
					<td class="titulo_tabla" colspan="2" width="20%" style="TEXT-ALIGN:center">Opciones</td>
				</tr>
				<?php
					$result = mssql_query("vm_s_cot_tracking $Cod_Clt, NULL, $desde, $hasta", $db);
					while ($row = mssql_fetch_array($result)) {
						$Cod_Cot = $row['Cod_Cot'];
						$Num_Cot = $row['Num_Cot'];
						$Cod_Nvt = $row['Cod_Odc'];
						$fecha   = $row['Fec_Nvt'];
						$Arc_Adj = $row['Arc_Adj'];
						$Num_trn = $row['Num_trnBco'];
						if (trim($Arc_Adj) == "" and trim($Num_trn) == "") {
							$images = "001_30.gif";
							$estado = "Enviada sin Pago";
						}
						else {
							$images = "001_06.gif";
							$estado = "Enviada con Pago";
						}
						$Tot_Cna = $row['Tot_Cna'];
						$Tot_New = $row['Tot_SinRes'];
						if ($Tot_Cna == 0) $mensaje = "Ninguno";
						else {
							$mensaje = "".$Tot_Cna;
							if ($Tot_New > 0) $mensaje .= ", ".$Tot_New." No le&iacute;do(s)";
						}
				?>
						<tr>
						<td style="TEXT-ALIGN:center"><?php echo $Num_Cot; ?></td>
						<td style="TEXT-ALIGN:center"><?php echo $Cod_Nvt; ?></td>
						<td style="TEXT-ALIGN:center"><?php echo fechafmt($fecha); ?></td>
						<td style="TEXT-ALIGN:left"><img src="images/<?php echo $images; ?>" alt="" /></td>
						<td style="TEXT-ALIGN:left"><?php echo $estado; ?></td>
						<td style="TEXT-ALIGN:right" width="10%"><img src="images/mail.png" alt="" ></td>
						<td style="TEXT-ALIGN:left" width="15%"><?php echo $mensaje; ?></td>
						<td style="TEXT-ALIGN:right"><img src="images/001_38.gif" alt="" ></td>
						<td style="TEXT-ALIGN:left"><a href="ordenes.php?cot=<?php echo $Cod_Cot ?>">Ver</a></td>
						</tr>
				<?php
					}
					mssql_free_result($result);
				?>
				</table>
                                </div>
                                <table BORDER="0" CELLSPACING="1" CELLPADDING="3" width="95%" ALIGN="center">
                                    <tr>
                                        <td align="right" colspan="3" width="100%" style="padding-top: 5px;">
                                            Total: <?php echo $total ?> Ordenes
                                        </td>
                                    </tr>
                                    <tr>
                                        <td align="left" colspan="2" style="padding-top: 5px; padding-bottom: 5px; border-top: grey 1px solid; border-bottom: grey 1px solid;">
                                            P&aacute;gina : <a href="javascript:Prev_Page()">Anterior</a>  | <a href="javascript:Next_Page()">Siguiente</a>&nbsp;&nbsp;&nbsp;&nbsp;<spam id="lbl_pagina"><?php echo $pagina; ?></spam> de <?php echo $tot_paginas; ?>
                                        </td>
                                        <td align="right" style="padding-top: 5px; padding-bottom: 5px; border-top: grey 1px solid; border-bottom: grey 1px solid;">
                                            <a href="javascript:FirstPage()">Primera</a>  | <a href="javascript:LastPage()">Ultima</a>
                                        </td>
                                    </tr>
                                </table>
				<form ID="searchOdc" method="post" name="searchODC" action="">
                                <input type="hidden" id="cod_clt" value="<?php echo $Cod_Clt; ?>" />
                                <input type="hidden" id="pagina" value="<?php echo $pagina; ?>" />
                                <input type="hidden" id="size_page" value="<?php echo $sizepage; ?>" />
                                <input type="hidden" id="tot_paginas" value="<?php echo $tot_paginas; ?>" />
				</form>
			</div>
		</div>
	</div>
	<div id="footer"></div>
</div>
<script type="text/javascript">
	var f1;	
	
	f1 = document.F1;	
	
</script>
</body>
</html>
