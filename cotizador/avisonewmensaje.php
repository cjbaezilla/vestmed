<?php

$casomail = isset($casomail) ? $casomail : 0;
if ($casomail == 0) {
    $folio = $Cod_Cot;
    //$query = "vm_s_cothdr $Cod_Cot";
    $result = mssql_query("vm_s_cothdr $Cod_Cot", $db);
    if (($row = mssql_fetch_array($result))) {
        $Num_Cot = $row['Num_Cot'];
        $nombre = $row['Nom_Per']." ".$row['Pat_Per']." ".$row['Mat_Per'];
        $cod_per = $row['Cod_Per'];
    }

    $QtySinLec = 0;
    //$query = "vm_msjsinlec $cod_per, $Cod_Cot";
    $result = mssql_query("vm_msjsinlec $cod_per, $Cod_Cot", $db);
    if (($row = mssql_fetch_array($result))) $QtySinLec = $row['QtySinLec'];
}
else {
    $folio = $folctt;
    $result = mssql_query("vm_cttweb_s $folctt", $db);
    if ((($row = mssql_fetch_array($result)))) {
        $cod_per = $row['Cod_Per'];
        $nombre = $row['nom_ctt'];
        $Num_Cot = $folctt;
        $Tip_Cna = $row['tip_cna'];
    }

    $QtySinLec = 0;
    //$query = "vm_msjsinlec $cod_per, $folctt, $Tip_Cna";
    $result = mssql_query("vm_msjsinlec $cod_per, $folctt, $Tip_Cna", $db);
    if (($row = mssql_fetch_array($result))) $QtySinLec = $row['QtySinLec'];
}

//$query = "vm_per_s $cod_per";
$result = mssql_query("vm_per_s $cod_per", $db);
if (($row = mssql_fetch_array($result))) {
    $NumDoc = $row["Num_Doc"];
    $doc_id = 1;
    //$query = "vm_s_usrweb $doc_id, '$NumDoc'";
    //echo $query."<BR>";
    $result = mssql_query ("vm_s_usrweb $doc_id, '$NumDoc'", $db) or die ("No se pudo leer datos del usuario");
    if (($row = mssql_fetch_array($result))) {
        $claveenc = $row["Pwd_Web"];
        //echo $claveenc."<BR";
        $clave = desencriptar($claveenc);
        //echo $clave."<BR";
    }
}

$parametros = encriptar($NumDoc.";".$clave.";".$folio.";mensajes".($casomail == 0 ? "cot" : "ctt"),50);
$pagina = $home."/validalogin.php?loginmail=".$parametros;

$parametros = encriptar($NumDoc.";".$clave.";".$folio.";micuenta",50);
$pagina2 = $home."/validalogin.php?loginmail=".$parametros;

$cuerpo_mail  = "<html xmlns=\"http://www.w3.org/1999/xhtml\">\n";
$cuerpo_mail .= "<head>\n";
$cuerpo_mail .= "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" />\n";
$cuerpo_mail .= "<title>Cotización Publicada</title>\n";
$cuerpo_mail .= "<SCRIPT LANGUAGE=\"JavaScript\">\n";
$cuerpo_mail .= "function ShowButton(objName, ImageName) {\n";
$cuerpo_mail .= "	objName.src=ImageName\n";
$cuerpo_mail .= "}\n";
$cuerpo_mail .= "function PreloadImages() {\n";
$cuerpo_mail .= "  if(document.images)\n";
$cuerpo_mail .= "    { if (!document.tmpImages)\n";
$cuerpo_mail .= "         document.tmpImages=new Array();\n";
$cuerpo_mail .= "      with(document) {\n";
$cuerpo_mail .= "       var\n";
$cuerpo_mail .= "          i,j=tmpImages.length,\n";
$cuerpo_mail .= "          a=PreloadImages.arguments;\n";
$cuerpo_mail .= "\n";
$cuerpo_mail .= "       for(i=0; i<a.length; i++)\n";
$cuerpo_mail .= "          if (a[i].indexOf(\"#\")!=0) {\n";
$cuerpo_mail .= "             tmpImages[j]=new Image;\n";
$cuerpo_mail .= "             tmpImages[j++].src=a[i];\n";
$cuerpo_mail .= "          }\n";
$cuerpo_mail .= "      }\n";
$cuerpo_mail .= "    }\n";
$cuerpo_mail .= "}\n";
$cuerpo_mail .= "</SCRIPT>\n";
$cuerpo_mail .= "</head>\n";
$cuerpo_mail .= "<body>\n";
$cuerpo_mail .= "<center>\n";
//$cuerpo_mail .= "<form ID=\"F2\" AUTOCOMPLETE=\"off\" method=\"POST\" name=\"F2\" ACTION=\"$pagina\"  />";
$cuerpo_mail .= "<table width=\"559\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n";
$cuerpo_mail .= "  <tr bgcolor=\"#1ac8c9\">\n";
$cuerpo_mail .= "	<td>&nbsp;</td>\n";
$cuerpo_mail .= "	<td>&nbsp;</td>\n";
$cuerpo_mail .= "	<td>&nbsp;</td>\n";
$cuerpo_mail .= "	<td>&nbsp;</td>\n";
$cuerpo_mail .= "    	<td>&nbsp;</td>\n";
$cuerpo_mail .= "    	<td>&nbsp;</td>\n";
$cuerpo_mail .= "  </tr>\n";
$cuerpo_mail .= "  <td colspan=\"6\" align=\"right\" width=\"1\"><font face=\"Arial, Helvetica, sans-serif\" size=\"1\" color=\"#629394\"><font face=\"Arial, Helvetica, sans-serif\"><a href=\"".$pagina2."\">Mi Cuenta</a> | <a href=\"http://www.vestmed.cl/faq.htm\">Ayuda</a></font></td>\n";
$cuerpo_mail .= "  <tr rowspan=\"3\" background=\"logo.jpg\" height=\"70\">\n";
$cuerpo_mail .= "	<td colspan=\"6\">&nbsp;</td>\n";
$cuerpo_mail .= "  </tr>\n";
$cuerpo_mail .= "  <td  height=20\" bgcolor=\"#F8F8F8\" colspan=\"6\" align=\"center\"><font face=\"Arial, Helvetica, sans-serif\" color=\"#000030\" size=\"2\">AVISO DE NUEVO MENSAJE - ".date("d/m/Y")." ".date("h:i:s")."</font></td>\n";
$cuerpo_mail .= "  <tr>\n";
$cuerpo_mail .= "	<td colspan=\"6\" height=20></td>\n";
$cuerpo_mail .= "  </tr>\n";
$cuerpo_mail .= "  <tr>\n";
$cuerpo_mail .= "	<td colspan=\"6\" align=\"left\" width=\"1\"><font face=\"Arial, Helvetica, sans-serif\" color=\"#000030\" size=\"2\"><STRONG>Estimado(a) $nombre,<STRONG></td>\n";
$cuerpo_mail .= "  </tr>\n";
$cuerpo_mail .= "  <tr>\n";
$cuerpo_mail .= "	<td colspan=\"6\" align=\"left\" width=\"1\"><font face=\"Arial, Helvetica, sans-serif\" color=\"#000030\" size=\"2\">Tienes un nuevo mensaje sin leer asociado al caso ".$Num_Cot.". Podras acceder a el presionando el siguiente boton:";
$cuerpo_mail .= " </tr>\n";
$cuerpo_mail .= "  <tr>\n";
$cuerpo_mail .= "	<td colspan=\"6\" height=12></td>\n";
$cuerpo_mail .= "  </tr>\n";
$cuerpo_mail .= "  <tr>\n";
$cuerpo_mail .= "	<td colspan=\"6\" height=25></td>\n";
$cuerpo_mail .= "  </tr>\n";
$cuerpo_mail .= "  <td colspan=\"6\" align=\"center\">\n";
$cuerpo_mail .= "  <a href=\"$pagina\"\n";
$cuerpo_mail .= "     onMouseOver=\"ShowButton(document.images['Image_boton_cotizar'],'$home/images/Btn_vermensajes_0.jpg')\"\n";
$cuerpo_mail .= "     onMouseOut=\"ShowButton(document.images['Image_boton_cotizar'],'$home/images/Btn_vermensajes_0.jpg')\"\n";
$cuerpo_mail .= "     onMouseDown=\"ShowButton(document.images['Image_boton_cotizar'],'$home/images/Btn_vermensajes_2.jpg')\"\n";
$cuerpo_mail .= "     onMouseUp=\"ShowButton(document.images['Image_boton_cotizar'],'$home/images/Btn_vermensajes_0.jpg')\">\n";
$cuerpo_mail .= "     <img src=\"$home/images/Btn_vermensajes_0.jpg\" border=\"0\" name=\"Image_boton_cotizar\"></a>\n";
$cuerpo_mail .= "  </td> \n";
$cuerpo_mail .= "  <tr>\n";
$cuerpo_mail .= "	<td colspan=\"6\" height=35></td>\n";
$cuerpo_mail .= "  </tr>\n";
$cuerpo_mail .= "  <tr>\n";
$cuerpo_mail .= "  	<td colspan=\"6\" align=\"left\" width=\"1\"><font face=\"Arial, Helvetica, sans-serif\" color=\"#000030\" size=\"2\"> En este sitio encontraras todo el historial de mensajes asociados a este caso y podras enviar nuevas consultas en caso que lo requieras. <br><br> \n";
$cuerpo_mail .= "  </tr>\n";
$cuerpo_mail .= "  <tr>\n";
$cuerpo_mail .= "  	<td colspan=\"6\" align=\"left\" width=\"1\"><font face=\"Arial, Helvetica, sans-serif\" color=\"#000030\" size=\"2\"> Obs: Altenativamente puedes acceder a tus cotizaciones a traves del menu \"<b>Mensajes</b>\" de tu cuenta de usuario al acceder con tu RUT y contrase&ntilde;a.  <br><br> \n";
$cuerpo_mail .= "  </tr>\n";
$cuerpo_mail .= "  <tr>\n";
$cuerpo_mail .= "	<td colspan=\"6\"height=20></td>\n";
$cuerpo_mail .= "  </tr>\n";
$cuerpo_mail .= "  <tr bgcolor=\"#83d7d6\" height=\"15\">\n";
$cuerpo_mail .= "    	<td colspan=\"6\"><font face=\"Arial, Helvetica, sans-serif\" color=\"#000000\" size=\"1\">&nbsp;&nbsp;Observaciones</font></td>\n";
$cuerpo_mail .= "  </tr>  \n";
$cuerpo_mail .= "  <tr>\n";
$cuerpo_mail .= "    	<td colspan=\"6\"><font face=\"Arial, Helvetica, sans-serif\" size=\"1\" color=\"#404040\">Tiene (".$QtySinLec.") mensaje(s) sin leer asociado a este caso.</font></td>\n";
$cuerpo_mail .= "  </tr>\n";
$cuerpo_mail .= "  <tr>\n";
$cuerpo_mail .= "  	<td colspan=\"6\"></td>\n";
$cuerpo_mail .= "  </tr>\n";
$cuerpo_mail .= "  <tr height=\"35\">\n";
$cuerpo_mail .= "  	<td colspan=\"6\"></td>\n";
$cuerpo_mail .= "  </tr>\n";
$cuerpo_mail .= "  <td colspan=\"6\" align=\"right\" width=\"1\"><font face=\"Arial, Helvetica, sans-serif\" size=\"1\" color=\"#629394\"><font face=\"Arial, Helvetica, sans-serif\"><a href=\"http://www.vestmed.cl/mapa.php\">Mapa</a> | Telefono: 242 10 42 - 241 98 39  | <a href=\"http://www.vestmed.cl/faq.htm\">Ayuda</a></font></td>  \n";
$cuerpo_mail .= "  <tr bgcolor=\"#31bdbb\" height=\"25\" align=\"right\">\n";
$cuerpo_mail .= "    	<td colspan=\"6\"><font face=\"Arial, Helvetica, sans-serif\" size=\"1\" color=\"#FFFFFF\">VESTMED &copy; Ltda. 2010 Todos los derechos reservados&nbsp;&nbsp;</font></td>\n";
$cuerpo_mail .= "  </tr>\n";
$cuerpo_mail .= "  <tr>\n";
$cuerpo_mail .= "  	<td colspan=\"6\" height=\"10\"></td>\n";
$cuerpo_mail .= "  </tr>\n";
$cuerpo_mail .= "  <tr>\n";
$cuerpo_mail .= "    	<td colspan=\"6\"><font face=\"Arial, Helvetica, sans-serif\" size=\"1\" color=\"#C8C8C8\" >Nota: Los acentos han sido omitidos intencionalmente </font></td>\n";
$cuerpo_mail .= "  </tr>\n";
$cuerpo_mail .= "</table>\n";
//$cuerpo_mail .= "</form>\n";
$cuerpo_mail .= "</center>\n";
$cuerpo_mail .= "</body>\n";
$cuerpo_mail .= "</html>\n";
?>
