<?php
$cuerpo_mail = "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">\n";
$cuerpo_mail = "<html xmlns=\"http://www.w3.org/1999/xhtml\">\n";
$cuerpo_mail = "<head>\n";
$cuerpo_mail = "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" />\n";
$cuerpo_mail = "<title>Comprobante del Proceso de Cotización</title>\n";
$cuerpo_mail = "</head>\n";
$cuerpo_mail = "<body>\n";
$cuerpo_mail = "<center>\n";
$cuerpo_mail = "<!Header>\n";
$cuerpo_mail = "<table width=\"559\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n";
$cuerpo_mail = "  <tr bgcolor=\"#1ac8c9\">\n";
$cuerpo_mail = "	 <td colspan=\"6\">&nbsp;</td>\n";
$cuerpo_mail = "  </tr>\n";
$cuerpo_mail = "  <td colspan=\"6\" align=\"right\" width=\"1\"><font face=\"Arial, Helvetica, sans-serif\" size=\"1\" color=\"#629394\"><font face=\"Arial, Helvetica, sans-serif\">Mi Cuenta | Ayuda</font></td>\n";
$cuerpo_mail = "  <tr rowspan=\"3\" background=\"logo.jpg\" height=\"70\">\n";
$cuerpo_mail = "	<td colspan=\"6\">&nbsp;</td>\n";
$cuerpo_mail = "  </tr>\n";
$cuerpo_mail = "  <td  height=20\" bgcolor=\"#F8F8F8\" colspan=\"6\" align=\"center\"><font face=\"Arial, Helvetica, sans-serif\" color=\"#000030\" size=\"2\">&nbsp;&nbsp;SOLICITUD DE COMPRA PARA COTIZACION # $Cod_Cot</font></td>\n";
$cuerpo_mail = "  <tr>\n";
$cuerpo_mail = "	<td colspan=\"6\" height=12></td>\n";
$cuerpo_mail = "  </tr>\n";
$cuerpo_mail = "<tr>\n";
$cuerpo_mail = "	<td colspan=\"6\" align=\"left\" width=\"1\"><font face=\"Arial, Helvetica, sans-serif\" color=\"#000030\" size=\"1\"><STRONG><STRONG></td>\n";
$cuerpo_mail = "  </tr>\n";
$cuerpo_mail = "  <tr>\n";
$cuerpo_mail = "	<td colspan=\"6\" align=\"left\" width=\"1\"><font face=\"Arial, Helvetica, sans-serif\" color=\"#000030\" size=\"2\"><strong> El cliente ha presionado el boton comprar de la pagina web. Por favor comunicarse inmediatamente.</strong></td>\n";
$cuerpo_mail = "  </tr>\n";
$cuerpo_mail = "  <tr>\n";
$cuerpo_mail = "	<td colspan=\"6\" align=\"left\" width=\"1\"><font face=\"Arial, Helvetica, sans-serif\" color=\"#000030\" size=\"2\">&nbsp;</td>\n";
$cuerpo_mail = "  </tr>\n";
$cuerpo_mail = "  <tr>\n";
$cuerpo_mail = "	<td colspan=\"6\" align=\"left\" width=\"1\"><font face=\"Arial, Helvetica, sans-serif\" color=\"#000030\" size=\"2\">Gracias !!!</td>\n";
$cuerpo_mail = "  </tr>\n";
$cuerpo_mail = "  <tr>\n";
$cuerpo_mail = "	<td colspan=\"6\"height=10></td>\n";
$cuerpo_mail = "  </tr>\n";
$cuerpo_mail = "  <tr>\n";
$cuerpo_mail = "	<td colspan=\"6\" align=\"left\" width=\"1\"><font face=\"Arial, Helvetica, sans-serif\" color=\"#629394\" size=\"1\">Resumen</td>\n";
$cuerpo_mail = "  </tr>\n";
$cuerpo_mail = "  <!Antecedentes del Cliente>\n";
$cuerpo_mail = "  <tr bgcolor=\"#83d7d6\" height=\"15\">\n";
$cuerpo_mail = "	<td colspan=\"6\"><font face=\"Arial, Helvetica, sans-serif\" color=\"#000000\" size=\"1\">&nbsp;&nbsp;Información del Cliente</font></td>\n";
$cuerpo_mail = "  </tr>\n";
$cuerpo_mail = "  <tr>\n";
$cuerpo_mail = "  	<td colspan=\"1\"><font face=\"Arial, Helvetica, sans-serif\" size=\"2\">&nbsp;Nombre:</font></td>\n";
$cuerpo_mail = "    	<td colspan=\"5\"><font face=\"Arial, Helvetica, sans-serif\" size=\"2\" color=\"#404040\">$nombre</font></td>\n";
$cuerpo_mail = "  </tr>\n";
$cuerpo_mail = "  <tr>\n";
$cuerpo_mail = "    	<td colspan=\"1\"><font face=\"Arial, Helvetica, sans-serif\" size=\"2\">&nbsp;Telefono:</font></td>\n";
$cuerpo_mail = "    	<td colspan=\"5\"><font face=\"Arial, Helvetica, sans-serif\" size=\"2\" color=\"#404040\">$Fon_Ctt</font></td>\n";
$cuerpo_mail = "  </tr>\n";
$cuerpo_mail = "  <tr>\n";
$cuerpo_mail = "    	<td colspan=\"1\"><font face=\"Arial, Helvetica, sans-serif\" size=\"2\">&nbsp;E-mail:</font></td>\n";
$cuerpo_mail = "    	<td colspan=\"5\"><font face=\"Arial, Helvetica, sans-serif\" size=\"2\" color=\"#404040\">$Mail_Ctt</font></td>\n";
$cuerpo_mail = "  </tr>\n";
$cuerpo_mail = "  <tr>\n";
$cuerpo_mail = "   	<td colspan=\"6\"height=10></td>\n";
$cuerpo_mail = "  </tr>\n";
$cuerpo_mail = "  <tr bgcolor=\"#83d7d6\" height=\"15\">\n";
$cuerpo_mail = "    	<td colspan=\"6\"><font face=\"Arial, Helvetica, sans-serif\" color=\"#000000\" size=\"1\">&nbsp;&nbsp;Detalle de Productos</font></td>\n";
$cuerpo_mail = "  </tr>\n";
$cuerpo_mail = "</table>\n";



<!Tabla Productos Solicitados>

<table width=\"559\" border=\"0\" bordercolor=\"#3e3e3e\" bordercolorlight=0 bordercolordark=0 cellspacing=\"0\"cellpadding=\"0\">
  <tr align=\"center\" bgcolor=\"#629394\" height=\"15\">
    	<td WIDTH=100><font face=\"Arial, Helvetica, sans-serif\" size=\"1\" color=\"#000000\">Marca</font></td>
    	<td WIDTH=100><font face=\"Arial, Helvetica, sans-serif\" size=\"1\" color=\"#000000\">Linea</font></td>
    	<td WIDTH=119><font face=\"Arial, Helvetica, sans-serif\" size=\"1\" color=\"#000000\">Codigo</font></td>
    	<td WIDTH=70><font face=\"Arial, Helvetica, sans-serif\" size=\"1\" color=\"#000000\">Color</font></td>
    	<td WIDTH=70><font face=\"Arial, Helvetica, sans-serif\" size=\"1\" color=\"#000000\">Talla</font></td>
    	<td WIDTH=70><font face=\"Arial, Helvetica, sans-serif\" size=\"1\" color=\"#000000\">Cant.</font></td>
  </tr>
 
  <tr  align=\"center\">
    	<td><font face=\"Arial, Helvetica, sans-serif\" size=\"2\" color=\"#3e3e3e\">Cherokee</font></td>
    	<td><font face=\"Arial, Helvetica, sans-serif\" size=\"2\" color=\"#3e3e3e\">Tooniforms</font></td>
    	<td><font face=\"Arial, Helvetica, sans-serif\" size=\"2\" color=\"#3e3e3e\">6700V</font></td>
    	<td><font face=\"Arial, Helvetica, sans-serif\" size=\"2\" color=\"#3e3e3e\">BKCPE</font></td>
    	<td><font face=\"Arial, Helvetica, sans-serif\" size=\"2\" color=\"#3e3e3e\">XSM</font></td>
    	<td><font face=\"Arial, Helvetica, sans-serif\" size=\"2\" color=\"#3e3e3e\">1</font></td>
  </tr>
  
  <tr bgcolor=\"#b9e8e8\" align=\"center\">
    	<td><font face=\"Arial, Helvetica, sans-serif\" size=\"2\" color=\"#3e3e3e\">Cherokee</font></td>
    	<td><font face=\"Arial, Helvetica, sans-serif\" size=\"2\" color=\"#3e3e3e\">Tooniforms</font></td>
    	<td><font face=\"Arial, Helvetica, sans-serif\" size=\"2\" color=\"#3e3e3e\">6700V</font></td>
    	<td><font face=\"Arial, Helvetica, sans-serif\" size=\"2\" color=\"#3e3e3e\">BKCPE</font></td>
    	<td><font face=\"Arial, Helvetica, sans-serif\" size=\"2\" color=\"#3e3e3e\">XSM</font></td>
    	<td><font face=\"Arial, Helvetica, sans-serif\" size=\"2\" color=\"#3e3e3e\">1</font></td>
  </tr>
  
  <tr  align=\"center\">
    	<td><font face=\"Arial, Helvetica, sans-serif\" size=\"2\" color=\"#3e3e3e\">Cherokee</font></td>
    	<td><font face=\"Arial, Helvetica, sans-serif\" size=\"2\" color=\"#3e3e3e\">Tooniforms</font></td>
    	<td><font face=\"Arial, Helvetica, sans-serif\" size=\"2\" color=\"#3e3e3e\">6700V</font></td>
    	<td><font face=\"Arial, Helvetica, sans-serif\" size=\"2\" color=\"#3e3e3e\">BKCPE</font></td>
    	<td><font face=\"Arial, Helvetica, sans-serif\" size=\"2\" color=\"#3e3e3e\">XSM</font></td>
    	<td><font face=\"Arial, Helvetica, sans-serif\" size=\"2\" color=\"#3e3e3e\">1</font></td>
  </tr>
  
  <tr bgcolor=\"#b9e8e8\" align=\"center\">
    	<td><font face=\"Arial, Helvetica, sans-serif\" size=\"2\" color=\"#3e3e3e\">Cherokee</font></td>
    	<td><font face=\"Arial, Helvetica, sans-serif\" size=\"2\" color=\"#3e3e3e\">Tooniforms</font></td>
    	<td><font face=\"Arial, Helvetica, sans-serif\" size=\"2\" color=\"#3e3e3e\">6700V</font></td>
    	<td><font face=\"Arial, Helvetica, sans-serif\" size=\"2\" color=\"#3e3e3e\">BKCPE</font></td>
    	<td><font face=\"Arial, Helvetica, sans-serif\" size=\"2\" color=\"#3e3e3e\">XSM</font></td>
    	<td><font face=\"Arial, Helvetica, sans-serif\" size=\"2\" color=\"#3e3e3e\">1</font></td>
  </tr>
  
  <tr align=\"center\">
    	<td><font face=\"Arial, Helvetica, sans-serif\" size=\"2\" color=\"#3e3e3e\">Cherokee</font></td>
    	<td><font face=\"Arial, Helvetica, sans-serif\" size=\"2\" color=\"#3e3e3e\">Tooniforms</font></td>
    	<td><font face=\"Arial, Helvetica, sans-serif\" size=\"2\" color=\"#3e3e3e\">6700V</font></td>
    	<td><font face=\"Arial, Helvetica, sans-serif\" size=\"2\" color=\"#3e3e3e\">BKCPE</font></td>
    	<td><font face=\"Arial, Helvetica, sans-serif\" size=\"2\" color=\"#3e3e3e\">XSM</font></td>
    	<td><font face=\"Arial, Helvetica, sans-serif\" size=\"2\" color=\"#3e3e3e\">1</font></td>
  </tr>
  
  <tr bgcolor=\"#b9e8e8\" align=\"center\">
    	<td><font face=\"Arial, Helvetica, sans-serif\" size=\"2\" color=\"#3e3e3e\">Cherokee</font></td>
    	<td><font face=\"Arial, Helvetica, sans-serif\" size=\"2\" color=\"#3e3e3e\">Tooniforms</font></td>
    	<td><font face=\"Arial, Helvetica, sans-serif\" size=\"2\" color=\"#3e3e3e\">6700V</font></td>
    	<td><font face=\"Arial, Helvetica, sans-serif\" size=\"2\" color=\"#3e3e3e\">BKCPE</font></td>
    	<td><font face=\"Arial, Helvetica, sans-serif\" size=\"2\" color=\"#3e3e3e\">XSM</font></td>
    	<td><font face=\"Arial, Helvetica, sans-serif\" size=\"2\" color=\"#3e3e3e\">1</font></td>
  </tr>
  
  <tr  align=\"center\">
    	<td><font face=\"Arial, Helvetica, sans-serif\" size=\"2\" color=\"#3e3e3e\">Cherokee</font></td>
    	<td><font face=\"Arial, Helvetica, sans-serif\" size=\"2\" color=\"#3e3e3e\">Tooniforms</font></td>
    	<td><font face=\"Arial, Helvetica, sans-serif\" size=\"2\" color=\"#3e3e3e\">6700V</font></td>
    	<td><font face=\"Arial, Helvetica, sans-serif\" size=\"2\" color=\"#3e3e3e\">BKCPE</font></td>
    	<td><font face=\"Arial, Helvetica, sans-serif\" size=\"2\" color=\"#3e3e3e\">XSM</font></td>
    	<td><font face=\"Arial, Helvetica, sans-serif\" size=\"2\" color=\"#3e3e3e\">1</font></td>
  </tr>
  
  <tr bgcolor=\"#b9e8e8\" align=\"center\">
    	<td><font face=\"Arial, Helvetica, sans-serif\" size=\"2\" color=\"#3e3e3e\">Cherokee</font></td>
    	<td><font face=\"Arial, Helvetica, sans-serif\" size=\"2\" color=\"#3e3e3e\">Tooniforms</font></td>
    	<td><font face=\"Arial, Helvetica, sans-serif\" size=\"2\" color=\"#3e3e3e\">6700V</font></td>
    	<td><font face=\"Arial, Helvetica, sans-serif\" size=\"2\" color=\"#3e3e3e\">BKCPE</font></td>
    	<td><font face=\"Arial, Helvetica, sans-serif\" size=\"2\" color=\"#3e3e3e\">XSM</font></td>
    	<td><font face=\"Arial, Helvetica, sans-serif\" size=\"2\" color=\"#3e3e3e\">1</font></td>
  </tr>
  
  <tr  align=\"center\">
    	<td><font face=\"Arial, Helvetica, sans-serif\" size=\"2\" color=\"#3e3e3e\">Cherokee</font></td>
    	<td><font face=\"Arial, Helvetica, sans-serif\" size=\"2\" color=\"#3e3e3e\">Tooniforms</font></td>
    	<td><font face=\"Arial, Helvetica, sans-serif\" size=\"2\" color=\"#3e3e3e\">6700V</font></td>
    	<td><font face=\"Arial, Helvetica, sans-serif\" size=\"2\" color=\"#3e3e3e\">BKCPE</font></td>
    	<td><font face=\"Arial, Helvetica, sans-serif\" size=\"2\" color=\"#3e3e3e\">XSM</font></td>
    	<td><font face=\"Arial, Helvetica, sans-serif\" size=\"2\" color=\"#3e3e3e\">1</font></td>
 </tr>

</table>



<!Tabla Condiciones de Compra>

<table width=\"559\" border=\"0\" bordercolor=\"#3e3e3e\" bordercolorlight=0 bordercolordark=0 cellspacing=\"0\"cellpadding=\"0\">

  <td colspan=\"6\"height=12></td>

  <tr bgcolor=\"#83d7d6\" height=\"15\">
    	<td colspan=\"6\"><font face=\"Arial, Helvetica, sans-serif\" color=\"#000000\" size=\"1\">&nbsp;&nbsp;Condiciones</font></td>
  </tr>
   
  <tr>
    	<td colspan=\"1\" WIDTH=80><font face=\"Arial, Helvetica, sans-serif\" size=\"2\">&nbsp;Bordado:</font></td>
    	<td colspan=\"5\"><font face=\"Arial, Helvetica, sans-serif\" size=\"2\" color=\"#404040\">NO</font></td>
  </tr>
  
  <tr>
    	<td colspan=\"1\"><font face=\"Arial, Helvetica, sans-serif\" size=\"2\">&nbsp;Despacho:</font></td>
    	<td colspan=\"5\"><font face=\"Arial, Helvetica, sans-serif\" size=\"2\" color=\"#404040\">S&Iacute;</font></td>
  </tr>
  
  <tr>
    	<td colspan=\"1\"><font face=\"Arial, Helvetica, sans-serif\" size=\"2\">&nbsp;Carrier:</font></td>
    	<td colspan=\"1\"WIDTH=250><font face=\"Arial, Helvetica, sans-serif\" size=\"2\" color=\"#404040\">Chilexpress</font></td>
    	<td colspan=\"2\"><font face=\"Arial, Helvetica, sans-serif\" size=\"2\"></font></td> 
        <td colspan=\"1\"WIDTH=60><font face=\"Arial, Helvetica, sans-serif\" size=\"2\">Servicio:</font></td>
    	<td colspan=\"1\"><font face=\"Arial, Helvetica, sans-serif\" size=\"2\" color=\"#404040\">&nbsp;Overnight</font></td>
  </tr>
  
  <tr>
     	<td colspan=\"1\"><font face=\"Arial, Helvetica, sans-serif\" size=\"2\">&nbsp;Direcci&oacute;n:</font></td>
    	<td colspan=\"5\"><font face=\"Arial, Helvetica, sans-serif\" size=\"2\" color=\"#404040\">Camino de las Bordalesas 7961</font></td>
  </tr>
  
  <tr>
    	<td colspan=\"1\"><font face=\"Arial, Helvetica, sans-serif\" size=\"2\">&nbsp;Ciudad :</font></td>
    	<td colspan=\"1\"><font face=\"Arial, Helvetica, sans-serif\" size=\"2\" color=\"#404040\">Santiago</font></td>   
    	<td colspan=\"2\" ><font face=\"Arial, Helvetica, sans-serif\" size=\"2\"></font></td> 
    	<td colspan=\"1\" ><font face=\"Arial, Helvetica, sans-serif\" size=\"2\">Comuna:</font></td>
    	<td colspan=\"1\"><font face=\"Arial, Helvetica, sans-serif\" size=\"2\" color=\"#404040\">&nbsp;Vitacura</font></td>
  </tr>
 
  <tr >
    	<td colspan=\"1\"><font face=\"Arial, Helvetica, sans-serif\" size=\"2\">&nbsp;# Personas:</font></td>
    	<td colspan=\"1\"><font face=\"Arial, Helvetica, sans-serif\" size=\"2\" color=\"#404040\">10-20</font></td>   
    	<td colspan=\"2\" ><font face=\"Arial, Helvetica, sans-serif\" size=\"2\"></font></td> 
    	<td colspan=\"1\" ><font face=\"Arial, Helvetica, sans-serif\" size=\"2\">Precios:</font></td>
    	<td colspan=\"1\"><font face=\"Arial, Helvetica, sans-serif\" size=\"2\" color=\"#404040\">&nbsp;Mayoristas</font></td>
  </tr>
  
  <tr>
    	<td colspan=\"6\"height=12></td>
  </tr>

  <tr bgcolor=\"#83d7d6\" height=\"15\">
    	<td colspan=\"6\"><font face=\"Arial, Helvetica, sans-serif\" color=\"#000000\" size=\"1\">&nbsp;&nbsp;Observaciones Generales</font></td>
  </tr>  

  <tr>
    	<td colspan=\"6\"><font face=\"Arial, Helvetica, sans-serif\" size=\"2\" color=\"#404040\">&nbsp;<strong>Historial de Mensajes: (10)</strong><br> &nbsp; Mensajes Nuevos: (5) <br><br> &nbsp;<strong>Información enviada en la solicitud de contacto: </strong> <br> &nbsp; Telefono: 23232323<br>&nbsp; Email: mdsds@ddsd.cl<br> &nbsp; Mensaje: aksdkdsjdask <br></font></td>
  </tr>

  <tr>
  	<td colspan=\"6\"></td>
  </tr>

  <tr height=\"35\">
  	<td colspan=\"6\"></td>
  </tr>

  <td colspan=\"6\" align=\"right\" width=\"1\"><font face=\"Arial, Helvetica, sans-serif\" size=\"1\" color=\"#629394\"><font face=\"Arial, Helvetica, sans-serif\">Mapa | Telefono | Ayuda</font></td>  
  
  <tr bgcolor=\"#31bdbb\" height=\"25\" align=\"right\">
    	<td colspan=\"6\"><font face=\"Arial, Helvetica, sans-serif\" size=\"1\" color=\"#FFFFFF\">VESTMED © Ltda. 2010 Todos los derechos reservados&nbsp;&nbsp;</font></td>
  </tr>
 
  <tr>
  	<td colspan=\"6\" height=\"10\"></td>
  </tr>

  <tr>
    	<td colspan=\"6\"><font face=\"Arial, Helvetica, sans-serif\" size=\"1\" color=\"#C8C8C8\" >Nota: Los acentos han sido omitidos intencionalmente </font></td>
  </tr>

</table>


</center>
</body>
</html>
?>
