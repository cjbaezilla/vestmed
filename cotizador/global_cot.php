<?php

ini_set("mssql.textsize",  "2147483647");
ini_set("mssql.textlimit", "2147483647");

include("../connect.php");

//Funcion para evitar sql injection:
function ok($sql_query)
{
    return str_replace("'", "''", $sql_query);
}

function display_usr($UsrId, $Perfil, $opcion, $link) {
	$nombre = $UsrId;

	$linea ="<form ID=\"F1\" AUTOCOMPLETE=\"off\" method=\"POST\" name=\"F1\">\n";
        switch ($opcion) {
            case "MiVestmed":
            case "Mensajes":
		$linea.="<li class=\"back-verde\"><a href=\"javascript:CerrarLogin()\">Salir</a></li>\n";
		$linea.="<li class=\"olvido\">".$nombre."</li>\n";
		$linea.="<li class=\"back-verde\">Usuario:</li>\n";
                break;

            case "Cotizaciones":
		$linea.="<li class=\"back-verde\"><a href=\"javascript:CerrarLogin()\">Salir</a></li>\n";
		$linea.="<li class=\"back-verde\"><a href=\"historicorep.php\">Hist&oacute;rico</a></li>\n";
		$linea.="<li class=\"back-verde\"><a href=\"seguimiento.php\">Seguimiento</a></li>\n";
		$linea.="<li class=\"back-verde\"><a href=\"nueva_cot.php\">Nueva</a></li>\n";
		$linea.="<li class=\"back-verde\"><a href=\"escritorio_cot.php\">Escritorio</a></li>\n";
		$linea.="<li class=\"olvido\">".$nombre."</li>\n";
		$linea.="<li class=\"back-verde\">Usuario:</li>\n";
                break;

            case "Ventas":
		$linea.="<li class=\"back-verde\"><a href=\"javascript:CerrarLogin()\">Salir</a></li>\n";
		$linea.="<li class=\"back-verde\"><a href=\"historicoventas.php\">Autorizadas</a></li>\n";
		$linea.="<li class=\"back-verde\"><a href=\"ventas.php\">Recibidas</a></li>\n";
		$linea.="<li class=\"olvido\">".$nombre."</li>\n";
		$linea.="<li class=\"back-verde\">Usuario:</li>\n";
                break;

            case "Clientes":
		$linea.="<li class=\"back-verde\"><a href=\"javascript:CerrarLogin()\">Salir</a></li>\n";
		$linea.="<li class=\"back-verde\"><a href=\"javascript:Editar()\">Ficha</a></li>\n";
		$linea.="<li class=\"back-verde\"><a href=\"javascript:BuscarCliente('mnu')\">Buscar</a></li>\n";
		$linea.="<li class=\"olvido\">".$nombre."</li>\n";
		$linea.="<li class=\"back-verde\">Usuario:</li>\n";
                break;

            default:
		$linea.="<li class=\"olvido\">".$nombre."</li>\n";
		$linea.="<li class=\"back-verde\">Usuario:</li>\n";
                break;
        }
        /*
	if (!isset($_SESSION['opcion']) || $_SESSION['opcion'] == "") {
		$linea.="<li class=\"back-verde\"><a href=\"javascript:CerrarLogin()\">Salir</a></li>\n";
		$linea.="<li class=\"back-verde\"><a href=\"historicorep.php\">Hist&oacute;rico</a></li>\n";
		$linea.="<li class=\"back-verde\"><a href=\"seguimiento.php\">Seguimiento</a></li>\n";
		$linea.="<li class=\"back-verde\"><a href=\"nueva_cot.php\">Nueva</a></li>\n";
		$linea.="<li class=\"back-verde\"><a href=\"escritorio_cot.php\">Escritorio</a></li>\n";
		$linea.="<li class=\"olvido\">".$nombre."</li>\n";
		$linea.="<li class=\"back-verde\">Usuario:</li>\n";
	} else if ($_SESSION['opcion'] == "clt") {
		$linea.="<li class=\"back-verde\"><a href=\"javascript:CerrarLogin()\">Salir</a></li>\n";
		//$linea.="<li class=\"back-verde\"><a href=\"javascript:Historico()\">Hist&oacute;rico</a></li>\n";
		$linea.="<li class=\"back-verde\"><a href=\"javascript:Editar()\">Ficha</a></li>\n";
		$linea.="<li class=\"back-verde\"><a href=\"javascript:BuscarCliente('mnu')\">Buscar</a></li>\n";
		//$linea.="<li class=\"back-verde\"><a href=\"javascript:NuevoCliente('mnu')\">Nuevo</a></li>\n";
		$linea.="<li class=\"olvido\">".$nombre."</li>\n";
		$linea.="<li class=\"back-verde\">Usuario:</li>\n";
	}
        */
	$linea.="</form>\n";
	
	return $linea;
}

function display_mnu($UsrId, $Cod_TipPer, $Cod_Cot, $link) {
	$linea ="<form ID=\"F1\" AUTOCOMPLETE=\"off\" method=\"POST\" name=\"F1\">\n";
	$linea.="<li class=\"back-verde\"><a href=\"javascript:window.close()\">Salir</a></li>\n";
	$linea.="<li class=\"back-verde\"><a href=\"javascript:window.print()\">Imprimir</a></li>\n";
	//if ($UsrId != "cotizador") {
	//   $page = ($Cod_TipPer == 1 ? "comprar.php?cot=$Cod_Cot" : "javascript:DisplayAviso()");
	//   $linea.="<li class=\"back-verde\"><a href=\"$page\">Comprar</a></li>\n";
	//}
    $linea.="<li class=\"back-verde\"><a href=\"consultas.php?cot=$Cod_Cot\">Consultas</a></li>\n";
    $linea.="<li class=\"back-verde\"><a href=\"preview.php?cot=$Cod_Cot\">Cotizaci&oacute;n</a></li>\n";
	$linea.="</form>\n";
	
	return $linea;
}

function display_mnu_odc($UsrId, $Cod_TipPer, $Cod_Cot, $link) {
	$linea ="<form ID=\"F1\" AUTOCOMPLETE=\"off\" method=\"POST\" name=\"F1\">\n";
	$linea.="<li class=\"back-verde\"><a href=\"javascript:window.close()\">Salir</a></li>\n";
	$linea.="<li class=\"back-verde\"><a href=\"javascript:window.print()\">Imprimir</a></li>\n";
	//if ($UsrId != "cotizador") {
	//   $page = ($Cod_TipPer == 1 ? "comprar.php?cot=$Cod_Cot" : "javascript:DisplayAviso()");
	//   $linea.="<li class=\"back-verde\"><a href=\"$page\">Comprar</a></li>\n";
	//}
    $linea.="<li class=\"back-verde\"><a href=\"printOdc.php?cot=$Cod_Cot\">O.Compra</a></li>\n";
	$linea.="</form>\n";
	
	return $linea;
}

function display_mnuizq2() {
	echo "<TABLE WIDTH=\"100%%\" BORDER=\"0\" CELLSPACING=\"0\" CELLPADDING=\"1\" ALIGN=\"left\">\n";
	echo "<TR><TD class=\"label_left_right_top\" STYLE=\"PADDING-LEFT:20px; PADDING-TOP:10px; TEXT-ALIGN: left\"><li><a href=\"mivestmed.php\">Mi Vestmed</li></TD></TR>\n";
	echo "<TR><TD class=\"label_left_right\" STYLE=\"PADDING-LEFT:20px; PADDING-TOP:10px; TEXT-ALIGN: left\"><li><a href=\"javascript:mnuVestmed()\">Cotizaciones</a></li></TD></TR>\n";
	echo "<TR><TD class=\"label_left_right\" STYLE=\"PADDING-LEFT:20px; PADDING-TOP:10px; TEXT-ALIGN: left\"><li><a href=\"javascript:BuscarCliente('')\">Clientes</a></li></TD></TR>\n";
	echo "<TR><TD class=\"label_left_right\" STYLE=\"PADDING-LEFT:20px; PADDING-TOP:10px; TEXT-ALIGN: left\"><li><a href=\"ventas.php\">Ventas</a></li></TD></TR>\n";
	echo "<TR><TD class=\"label_left_right\" STYLE=\"PADDING-LEFT:20px; PADDING-TOP:10px; TEXT-ALIGN: left\"><li><a href=\"pdespacho.php\">Valor Despacho</a></li></TD></TR>\n";
	echo "<TR><TD class=\"label_left_right\" STYLE=\"PADDING-LEFT:20px; PADDING-TOP:10px; TEXT-ALIGN: left\"><li>Reposici&oacute;n</li></TD></TR>\n";
	echo "<TR><TD class=\"label_left_right\" STYLE=\"PADDING-LEFT:20px; PADDING-TOP:10px; TEXT-ALIGN: left\"><li>Compras</li></TD></TR>\n";
	echo "<TR><TD class=\"label_left_right\" STYLE=\"PADDING-LEFT:20px; PADDING-TOP:10px; TEXT-ALIGN: left\"><li>Despacho</li></TD></TR>\n";
	echo "<TR><TD class=\"label_left_right\" STYLE=\"PADDING-LEFT:20px; PADDING-TOP:10px; TEXT-ALIGN: left\"><li><a href=\"javascript:mnuMensajes()\">Mensajes</a></li></TD></TR>\n";
	echo "<TR><TD class=\"label_left_right_bottom\" STYLE=\"PADDING-LEFT:20px; PADDING-TOP:10px; PADDING-BOTTOM:10px; TEXT-ALIGN: left\"><li>Bordados</li></TD></TR>\n";
	echo "</TABLE>\n";
}

function display_mnuizq($UsrId, $link) {
    echo "<TABLE WIDTH=\"100%%\" BORDER=\"0\" CELLSPACING=\"0\" CELLPADDING=\"1\" ALIGN=\"left\">\n";
    echo "<TR><TD class=\"label_left_right_top\" STYLE=\"PADDING-LEFT:20px; TEXT-ALIGN: left; HEIGHT: 3px\">&nbsp;</TD></TR>\n";
    $sp = mssql_query("vm_seg_usr_opcmodweb '$UsrId'", $link);
    while (($row = mssql_fetch_array($sp))) 
        if ($row["Id_Mod"] == 1 && strtoupper($row['CodUsr']) == strtoupper($UsrId)) {
           echo "<TR><TD class=\"label_left_right\" STYLE=\"PADDING-LEFT:20px; PADDING-TOP:10px; TEXT-ALIGN: left\"><li>";
           if ($row['PagOpc'] != null)
               echo "<a href=\"".$row['PagOpc']."\">";
           echo utf8_encode($row["NomOpc"]);
           if ($row['PagOpc'] != null)
               echo "</a>";
           echo "</li></TD></TR>\n";
        }
    mssql_free_result($sp);    
    echo "<TR><TD class=\"label_left_right_bottom\" STYLE=\"PADDING-LEFT:20px; PADDING-TOP:10px; PADDING-BOTTOM:10px; TEXT-ALIGN: left\"></TD></TR>\n";
    echo "</TABLE>\n";
}


function formatearRut($rut){
	$aRut   = split("-", $rut);
	return formatearMillones($aRut[0])."-".$aRut[1];
}

function formatearMillones($nNmb){
	$sRes = "";
	for ($j, $i = strlen($nNmb) - 1, $j = 0; $i >= 0; $i--, $j++)
		$sRes = substr($nNmb,$i,1).(($j > 0) && ($j % 3 == 0)? ".": "").$sRes;
	return $sRes;
}

function agregarLevelSession($link,$namelink,$level) {
	$buffer = "";
	$buffersession = split(";", $_SESSION['buffer']);
	for ($i=0; $i<minimo($level,count($buffersession)); $i++) $buffer.=$buffersession[$i].";";
	$buffer.=$link.",".$namelink;
	$_SESSION['buffer'] = $buffer;
}

function eliminarLastLevelSession() {
	$buffer = "";
	$buffersession = split(";", $_SESSION['buffer']);
	for ($i=0; $i<(count($buffersession)-1); $i++) $buffer.=$buffersession[$i].";";
	$_SESSION['buffer'] = substr($buffer,0,strlen($buffer)-1);
}

function DisplayLevelSession($class) {
	$session = split(";", $_SESSION['buffer']);
	$i = 1;
	foreach ($session as $key => $value) {
		$link = split(",", $value);
		if ($i++ < count($session))
			echo "<a href=\"".$link[0]."\">".$link[1]."</a> / ";	
		else
			echo $link[1];
	}
}

function formar_topbox ($ancho,$alineacion) {
  $aWH = split("-", $ancho);
  
  if (!isset($aWH[1]))
     echo "<table cellpadding=0 cellspacing=0 border=0 width=\"".$aWH[0]."\" align=".$alineacion.">\n";
  else
     echo "<table cellpadding=0 cellspacing=0 border=0 width=\"".$aWH[0]."\" height=\"".$aWH[1]."\" align=".$alineacion.">\n";
  echo "<tr>\n";
  echo "<td valign=top><img src=\"../images/box/1.gif\"></td>\n";
  echo "<td valign=top bgcolor=\"#f2f2f2\" height=1>";
  echo "<img src=\"../images/box/blank.gif\" width=\"1\" height=\"1\"></td>\n";
  echo "<td valign=top><img src=\"../images/box/3.gif\"></td>\n";
  echo "</tr><tr>\n";
  echo "<td valign=top style=\"background-image: url(../images/box/5.gif); background-repeat: repeat-y\">\n";
  echo "<img src=\"../images/box/blank.gif\" width=\"1\" height=\"1\"></td>\n";
  echo "<td valign=top bgcolor=\"#f2f2f2\" width=\"100%\">\n";
}

function formar_bottombox () {
  echo "</td>\n";
  echo "<td valign=top style=\"background-image: url(../images/box/7.gif); background-repeat: repeat-y\">\n";
  echo "<img src=\"../images/box/blank.gif\" width=\"1\" height=\"1\">\n";
  echo "</td></tr><tr>\n";
  echo "<td valign=top height=1><img src=\"../images/box/2.gif\"></td>\n";
  echo "<td valign=top height=1 style=\"background-image: url(../images/box/6.gif); background-repeat: repeat-x\">\n";
  echo "<img src=\"../images/box/blank.gif\" width=\"1\" height=\"1\">\n";
  echo "</td>\n";
  echo "<td valign=top height=1><img src=\"../images/box/4.gif\"></td>\n";
  echo "</tr>\n";
  echo "</table>\n";
}

function printimg_addr($name, $filter)
{
    return '../imagedisplay.php?name='.$name.'&filter='.$filter;
}

function BusCol($ColCript, $sVal) {
   for ($i = 1; $i <= 10; $i++)
     if ($ColCript[$i] == $sVal)
        return $i; 
}

function BusMatrix ($Matrix, $sVal, &$fila, &$columna) {
   for ($i = 1; $i <= 8; $i++)
      for ($j = 1; $j <= 10; $j++)
 	    if ($Matrix[$i][$j] == $sVal) {
           $fila = $i;
           $columna = $j;
	       return 0;
	    }
   $fila = 0;
   $columna = 0;
}

function BusRow($RowCript, $sVal) {
   for ($i = 1; $i <= 8; $i++)
     if ($RowCript[$i] == $sVal)
        return $i; 
}

function desencriptar ($sPassWd) {
   $ColCript = "01XS9RnLWBkR";
   $RowCript = "0PZ8y3JXMa";

   $Matrix[0] = split ("/", "0/0/0/0/0/0/0/0/0/0/0");
   $Matrix[1] = split ("/", "0/A/B/a/b/K/k/P/p/Y/y");
   $Matrix[2] = split ("/", "0/C/F/c/f/L/Q/l/q/Z/z");
   $Matrix[3] = split ("/", "0/D/d/G/g/M/m/R/r/�/�");
   $Matrix[4] = split ("/", "0/E/e/H/h/O/o/S/s/1/4");
   $Matrix[5] = split ("/", "0/I/i/J/j/N/n/T/t/2/5");
   $Matrix[6] = split ("/", "0/U/u/V/v/W/w/X/x/3/6");
   $Matrix[7] = split ("/", "0/7/8/9/0/-/_/./ /*/+");
   $Matrix[8] = split ("/", "0/$/=/(/)/!/&/#/;/?/�");

   $sClave = "";
   $nAux = (strlen($sPassWd) - 3) / 2;

   for ($nIndex = 1; $nIndex <= $nAux; $nIndex++) 
      if ($sPassWd[2*$nIndex] == "a")
         $sClave = substr($sPassWd, 0, 2*$nIndex - 1);

   if ($sClave == "" && strlen($sPassWd) != 0) 
      return "�ERROR!";

   $nCorrimiento = BusCol($ColCript, $sClave[0]);
   $sAux = "";
   for ($nIndex = 1; $nIndex <= $nAux; $nIndex++) {
   	  if (2*$nIndex < strlen($sClave)) {
     	  //echo $nIndex.")".$sClave[2*$nIndex - 1]."-".$sClave[2*$nIndex]."<BR>";
        $columna = BusCol($ColCript, $sClave[2*$nIndex - 1]);
        $fila = BusRow($RowCript, $sClave[2*$nIndex]);
        //echo $nIndex.")columna=".$columna.",fila=".$fila."<BR>";
      
        $columna = $columna - 1 + 10 - $nCorrimiento;
        if ($columna == 0) $columna = 10;
        if ($columna > 10) $columna = $columna - 10;

        //echo $nIndex.")matrix(".$fila.",".$columna.")=".$Matrix[$fila][$columna]."<BR>";

        $sAux1 = sprintf("%s%s", $sAux, $Matrix[$fila][$columna]);
        $sAux = $sAux1;
      }
   }
   
   return $sAux;
}

function encriptar ($sValor, $nMaxLen) {

   $ColCript = "01XS9RnLWBkR";
   $RowCript = "0PZ8y3JXMa";

   $Matrix[0] = split ("/", "0/0/0/0/0/0/0/0/0/0/0");
   $Matrix[1] = split ("/", "0/A/B/a/b/K/k/P/p/Y/y");
   $Matrix[2] = split ("/", "0/C/F/c/f/L/Q/l/q/Z/z");
   $Matrix[3] = split ("/", "0/D/d/G/g/M/m/R/r/�/�");
   $Matrix[4] = split ("/", "0/E/e/H/h/O/o/S/s/1/4");
   $Matrix[5] = split ("/", "0/I/i/J/j/N/n/T/t/2/5");
   $Matrix[6] = split ("/", "0/U/u/V/v/W/w/X/x/3/6");
   $Matrix[7] = split ("/", "0/7/8/9/0/-/_/./ /*/+");
   $Matrix[8] = split ("/", "0/$/=/(/)/!/&/#/;/?/�");

   $fila = 0;
   $columna = 0;

   $nLargo = strlen($sValor);
   $nLen = rand(1,10);

   $sAux = "";
   $sAux = $ColCript[$nLen];

   $nIndex = 1;
   
   for ($nIndex = 1; $nIndex <= $nMaxLen; $nIndex++) {
       if ($nIndex <= $nLargo) 
          BusMatrix ($Matrix, $sValor[$nIndex-1], $fila, $columna);

       if ($nIndex == ($nLargo+1)) {
          $columna = $nLen;
          $fila = 9;
       }

       if ($nIndex > $nLargo + 1) {
          $columna = rand (1,10);
          $fila = rand(1,8);

          while (($columna == 9 && $fila == 8) || ($columna == 10 && $fila == 8) || $columna == 0 || $fila == 0) 
          {
             $columna = rand(1, 10);
             $fila = rand(1,8); 
          }
       }

       if ($fila == 0 || $columna == 0) {
         return "?";
       }

       $columna = (($columna + $nLen) % 10) + 1;
       $sAux1 = sprintf ("%s%s%s", $sAux, $ColCript[$columna], $RowCript[$fila]);

       $sAux = $sAux1;
       
   }

   return $sAux;
}

function enviar_mail ($destinatario,$asunto,$cuerpo,$formato) {
	require_once('../PHP-Mail/class.phpgmailer.php');
	$mail = new PHPGMailer();
	$mail->Username = 'ventas@vestmed.cl';
	$mail->Password = 'beztmedre';
	$mail->From = 'ventas@vestmed.cl';
	$mail->FromName = 'Vestmed';
	$mail->Subject = $asunto;
	$mail->AddAddress($destinatario);
	$mail->Body = $cuerpo;

	if ($formato == "HTML") $mail->IsHTML(true);
	
	$mail->Send();
}


function cuerpo_envioODC ($id_pais,$folio,$home,$pathadjuntos,$adj_ctt,$arc_adj,$link) {
  $aPaises   = array(0 => "Demo", 56 => "Chile", 51 => "Peru", 54 => "Argentina");
  $aPersonas = array(1 => "Personal", 2 => "2 - 15", 3 => "15 - 30", 4 => "> 30");
  $aPrecios  = array(1 => "Minorista", 2 => "Mayorista");
  
  $dato3 = "BORDER-RIGHT: goldenrod 1px solid; FONT-SIZE: 10px; COLOR: black; FONT-FAMILY: Verdana, Arial;";
  $dato33 = "BORDER-RIGHT: goldenrod 1px solid; FONT-SIZE: 10px; COLOR: black; FONT-FAMILY: Verdana, Arial; BACKGROUND-COLOR: peachpuff";

  $label3 = "FONT-SIZE: 10px; BORDER-LEFT: goldenrod 1px solid; COLOR: black; FONT-FAMILY: Verdana, Arial;";
  $label33 = "FONT-SIZE: 10px; BORDER-LEFT: goldenrod 1px solid; COLOR: black; FONT-FAMILY: Verdana, Arial; BACKGROUND-COLOR: peachpuff;";
  $label333 = "BORDER-RIGHT: goldenrod 1px solid; FONT-SIZE: 10px; BORDER-LEFT: goldenrod 1px solid; COLOR: black; FONT-FAMILY: Verdana, Arial; BACKGROUND-COLOR: peachpuff;";
  $label4 = "FONT-SIZE: 10px; BORDER-LEFT: goldenrod 1px solid; COLOR: black; BORDER-BOTTOM: goldenrod 1px solid; FONT-FAMILY: Verdana, Arial; TEXT-ALIGN: right";
  $dato4 = "BORDER-RIGHT: goldenrod 1px solid; FONT-SIZE: 10px; COLOR: black; BORDER-BOTTOM: goldenrod 1px solid; FONT-FAMILY: Verdana, Arial";

  $label_top = "BORDER-TOP: goldenrod 1px solid; FONT-SIZE: 10px; COLOR: black; FONT-FAMILY: Verdana, Arial; TEXT-ALIGN: center;";
  $label_left_right = "BORDER-RIGHT: goldenrod 1px solid; FONT-SIZE: 10px; BORDER-LEFT: goldenrod 1px solid; COLOR: black; FONT-FAMILY: Verdana, Arial;";
  
  $titulo_tabla = "BORDER-RIGHT: goldenrod 1px solid; BORDER-TOP: goldenrod 1px solid; FONT-WEIGHT: bold; FONT-SIZE: 11px; PADDING-BOTTOM: 3px; BORDER-LEFT: goldenrod 1px solid; COLOR: white; PADDING-TOP: 3px;BORDER-BOTTOM: goldenrod 1px solid; FONT-FAMILY: Arial, Verdana; BACKGROUND-COLOR: #993300;";    
  $subtitulo_tabla_left = "BORDER-RIGHT: goldenrod 1px solid; BORDER-TOP: goldenrod 1px solid; FONT-WEIGHT: bold; FONT-SIZE: 11px; PADDING-BOTTOM: 3px; BORDER-LEFT: goldenrod 1px solid; COLOR: black; PADDING-TOP: 3px; BORDER-BOTTOM: goldenrod 1px solid; FONT-FAMILY: Arial, Verdana; BACKGROUND-COLOR: #ffcc66; TEXT-ALIGN: center";
  $subtitulo_tabla = "BORDER-RIGHT: goldenrod 1px solid; BORDER-TOP: goldenrod 1px solid; FONT-WEIGHT: bold; FONT-SIZE: 11px; PADDING-BOTTOM: 3px; COLOR: black; PADDING-TOP: 3px; BORDER-BOTTOM: goldenrod 1px solid; FONT-FAMILY: Arial, Verdana; BACKGROUND-COLOR: #ffcc66; TEXT-ALIGN: center";
  
  $H1 = "FONT-SIZE: 1em; COLOR: #3366cc; FONT-FAMILY: tahoma";

  $result1 = mssql_query("vm_s_cothdr $folio",$link);
  if ($row = mssql_fetch_array($result1)) {
      $Num_Cot = $row['Num_Cot'];
      $Cod_Nvt = $row['Cod_Odc'];
	  $Cod_Clt = $row['Cod_Clt'];
	  $Cod_Per = $row['Cod_Per'];
	  $Num_TrnBco = $row['Num_TrnBco'];
	  $Tip_DocSII = $row['Tip_DocSII'];
	  $Cod_PerFct = $row['Cod_PerFct'];
	  
	  $rubro = "Orden de Compra Cotizaci&oacute;n ".$Num_Cot."<BR>Nota de Venta Nro ".$Cod_Nvt;
	  mssql_free_result($result1);

	  $result1 = mssql_query("vm_codusr_s $Cod_Clt, $Cod_Per", $link);
	  if ($row = mssql_fetch_array($result1)) $Cod_Usr = $row['Cod_Usr'];
	  
	  $result1 = mssql_query("vm_s_cotsvcweb $folio", $link);
	  if ($row = mssql_fetch_array($result1)) {
		$srv1linea = $row["is_svc1"];
		$srv2linea = $row["is_svc2"];
		$despacho  = $row["is_dsp"];
		if ($despacho == 1) {
			$cod_crr = $row["Cod_Crr"];
			$des_crr = $row["Des_Crr"];
			$cod_crr = $row["Cod_SvcCrr"];
			$des_svc = $row["Des_SvcCrr"];
			$cod_cdd = $row["Cod_Cdd"];
			$nom_cdd = $row["Nom_Cdd"];
			$nom_cmn = $row["Nom_Cmn"];
			$cod_suc = $row["Cod_Suc"];
			$nom_suc = $row["Nom_Suc"];
			$dir_suc = $row["Dir_Suc"];
		}
		$num_per =$row["Num_Per"];
		$cod_pre = $row["Cod_Pre"];
		$observacion = $row["obs_cot"];
		if ($observacion == " ") $observacion = "&nbsp;";
	  }
	  mssql_free_result($result1); 

	  $result1 = mssql_query("vm_s_cotusrweb  $folio", $link);
	  if ($row = mssql_fetch_array($result1)) {
		 $usuario_cot   = $row["Pat_Per"]." ".$row["Mat_Per"]." ".$row["Nom_Per"];
		 $nombre_usrcot = str_replace("\&#39;", "'", $usuario_cot);
		 $comprador	    = $nombre_usrcot;
		 $email_usrcot  = $row["Mail_Ctt"];
		 $fono_usrcot   = $row["Fon_Ctt"];
		 $mismo         = true;
		 if ($row["Cod_Clt"] != $row["Cod_CltPer"]) {
			$CodClt = $row["Cod_Clt"];
			mssql_free_result($result1); 
			$result1 = mssql_query("vm_cli_s $CodClt", $link);
			$mismo   = false;
			if ($row = mssql_fetch_array($result1))
				$comprador     = str_replace("\&#39;", "'", $row["RznSoc_Per"]);
		 }
	  }
	  mssql_free_result($result1); 
	  
	  $cuerpo = formar_topmail ($id_pais, "ORDEN DE COMPRA");
	  $cuerpo.="<td width=\"85%\">\n";

	  $cuerpo.="<P align=center>\n";
	  $cuerpo.="<H1 style=\"TEXT-ALIGN: center; ".$H1."\">\n";
	  $cuerpo.=$rubro;
	  $cuerpo.="</H1></P>\n";
	  
	  $cuerpo.="<P align=center>\n";
	  $cuerpo.="<TABLE WIDTH=500 BORDER=0 CELLSPACING=0 CELLPADDING=1>\n";
	  $cuerpo.="<TR>\n";
	  $cuerpo.="<TD colspan=2 style=\"WIDTH: 500px; ".$titulo_tabla."\" align=middle>\n";
	  $cuerpo.="Antecedentes del Comprador\n";
	  $cuerpo.="</TD>\n";
	  $cuerpo.="</TR>\n";
	  if (!$mismo) {
		  $cuerpo.="<TR>\n";
		  $cuerpo.="<TD style=\"WIDTH: 220px; TEXT-ALIGN: right;".$label3."\">\n";
		  $cuerpo.="<STRONG>Raz&oacute;n Social :</STRONG>&nbsp;</TD>\n";
		  $cuerpo.="<TD style=\"WIDTH: 280px; TEXT-ALIGN: left; PADDING-LEFT: 5px;".$dato3."\" valign=center>\n";
		  $cuerpo.=$comprador."</TD>\n";
		  $cuerpo.="</TR>\n";
	  }
	  $cuerpo.="<TR>\n";
	  $cuerpo.="<TD style=\"WIDTH: 200px; TEXT-ALIGN: right;".$label33."\">\n";
	  $cuerpo.="<STRONG>Nombre Usuario :</STRONG>&nbsp;</TD>\n";
	  $cuerpo.="<TD style=\"WIDTH: 500px; TEXT-ALIGN: left; PADDING-LEFT: 5px;".$dato33."\" valign=center>\n";
	  $cuerpo.=$nombre_usrcot."</TD>\n";
	  $cuerpo.="</TR>\n";
	  $cuerpo.="<TR>\n";
	  $cuerpo.="<TD style=\"WIDTH: 200px; TEXT-ALIGN: right;".$label3."\">\n";
	  $cuerpo.="<STRONG>Tel&eacute;fono Usuario :</STRONG>&nbsp;</TD>\n";
	  $cuerpo.="<TD style=\"WIDTH: 500px; TEXT-ALIGN: left; PADDING-LEFT: 5px;".$dato3."\" valign=center>\n";
	  $cuerpo.=$fono_usrcot."</TD>\n";
	  $cuerpo.="</TR>\n";
	  $cuerpo.="<TR>\n";
	  $cuerpo.="<TD style=\"WIDTH: 200px; TEXT-ALIGN: right;".$label33."\">\n";
	  $cuerpo.="<STRONG>e-mail Usuario :</STRONG>&nbsp;</TD>\n";
	  $cuerpo.="<TD style=\"WIDTH: 500px; TEXT-ALIGN: left; PADDING-LEFT: 5px;".$dato33."\" valign=center>\n";
	  $cuerpo.="<A style=\"COLOR: #3366cc; TEXT-DECORATION: underline\" HREF=mailto:".$email_usrcot.">".$email_usrcot."</A></TD>\n";
	  $cuerpo.="</TR>\n";
	  $cuerpo.="<TR><TD style=\"".$label_top."\" colspan=2>&nbsp;</TD></TR>\n";
	  $cuerpo.="</TABLE>\n";

	  $cuerpo.="<TABLE WIDTH=500 BORDER=0 CELLSPACING=0 CELLPADDING=1>\n";
	  $cuerpo.="<TR>\n";
	  $cuerpo.="<TD colspan=\"6\" style=\"WIDTH: 500px; ".$titulo_tabla."\" align=\"middle\">\n";
	  $cuerpo.="Detalle de Productos";
		
	  $cuerpo.="</TD>\n";
	  $cuerpo.="</TR>\n";
	  $cuerpo.="<TR>\n";
	  $cuerpo.="<TD style=\"WIDTH: 30px; ".$subtitulo_tabla_left."\"  align=middle>Producto</TD>\n";
	  $cuerpo.="<TD style=\"WIDTH: 80px; ".$subtitulo_tabla."\" align=middle>Marca</TD>\n";
	  $cuerpo.="<TD style=\"WIDTH: 80px; ".$subtitulo_tabla."\" align=middle>Linea</TD>\n";
	  $cuerpo.="<TD style=\"WIDTH: 80px; ".$subtitulo_tabla."\" align=middle>Talla</TD>\n";
	  $cuerpo.="<TD style=\"WIDTH: 80px; ".$subtitulo_tabla."\" align=middle>Patr&oacute;n</TD>\n";
	  $cuerpo.="<TD style=\"WIDTH: 80px; ".$subtitulo_tabla."\" align=middle>Cantidad</TD>\n";
	  $cuerpo.="</TR>\n";

	  $j = 0;
	  $i = 1;
	  $result1 = mssql_query("vm_detprd_nvt_s $Cod_Nvt, $Cod_Clt, $Cod_Usr", $link);
	  while ($row = mssql_fetch_array($result1)) {
		 $cuerpo.="    <TR>\n";
		 if ($j == 0) {
			  $clase1 = $label_left_right;
			  $clase2 = $dato3;
		 }
		 else {
			  $clase1 = $label333;
			  $clase2 = $dato33;
		 }
		 $cuerpo.="   <TD style=\"TEXT-ALIGN: center; ".$clase1."\">".$row["Cod_Sty"]."</TD>\n";
		 $cuerpo.="   <TD style=\"TEXT-ALIGN: center; ".$clase2."\">".$row["Cod_Mca"]."</TD>\n";
		 $cuerpo.="   <TD style=\"TEXT-ALIGN: center; ".$clase2."\">".$row["Des_LinMca"]."</TD>\n";
		 $cuerpo.="   <TD style=\"TEXT-ALIGN: center; ".$clase2."\">".$row["Val_Sze"]."</TD>\n";
		 $cuerpo.="   <TD style=\"TEXT-ALIGN: center; ".$clase2."\">".$row['Key_Pat']."</TD>\n";
		 $cuerpo.="   <TD style=\"TEXT-ALIGN: center; ".$clase2."\">".$row["Cant_AccDetPrd_Nvt"]."</TD>\n";
		 $j = 1 - $j; 
		 $cuerpo.="    </TR>\n";
	  } 
	  mssql_free_result($result1); 
		
	  $cuerpo.="<TR><TD colspan=\"6\" style=\"".$label_top."\">&nbsp;</td></tr>\n";
	  $cuerpo.="</TABLE>\n";

	  $cuerpo.="<TABLE WIDTH=500 BORDER=0 CELLSPACING=0 CELLPADDING=1>\n";
	  $cuerpo.="<TR>\n";
	  $cuerpo.="<TD colspan=2 style=\"WIDTH: 500px; ".$titulo_tabla."\" align=middle>\n";
	  $cuerpo.="Condiciones de la Compra\n";
	  $cuerpo.="</TD>\n";
	  $cuerpo.="</TR>\n";
	  $cuerpo.="<TR>\n";
	  $cuerpo.="<TD style=\"WIDTH: 220px; TEXT-ALIGN: right; ".$label3."\" valign=top>\n";
	  $cuerpo.="<STRONG>Servicio Bordado 1 linea</STRONG> :&nbsp;</TD>\n";
	  $cuerpo.="<TD style=\"WIDTH: 280px; TEXT-ALIGN: left; PADDING-LEFT: 5px; ".$dato3."\" valign=center>\n";
	  $cuerpo.=($srv1linea == 1 ? "SI" : "NO")."</TD>\n";
	  $cuerpo.="</TR>\n";
	  $cuerpo.="<TR>\n";
	  $cuerpo.="<TD style=\"TEXT-ALIGN: right; ".$label33."\"><STRONG>Servicio Bordado 2 lineas</STRONG> :&nbsp;</TD>\n";
	  $cuerpo.="<TD style=\"TEXT-ALIGN: left; PADDING-LEFT: 5px; ".$dato33."\" valign=center>\n";
	  $cuerpo.=($srv2linea == 1 ? "SI" : "NO")."</TD>\n";
	  $cuerpo.="</TR>\n";
	  $cuerpo.="<TR>\n";
	  $cuerpo.="<TD style=\"TEXT-ALIGN: right; ".$label3."\">\n";
	  $cuerpo.="<STRONG>Despacho</STRONG> :&nbsp;</TD>\n";
	  $cuerpo.="<TD style=\"TEXT-ALIGN: left; PADDING-LEFT: 5px; ".$dato3."\" valign=center>\n";
	  $cuerpo.=($despacho == 1 ? "SI" : "NO");
	  $cuerpo.="</TD>\n";
	  $cuerpo.="</TR>\n";

	  $labelDisplay = $label33;
	  $datoDisplay = $dato33;
	  if ($despacho == 1) {  
		  $cuerpo.="<TR>\n";
		  $cuerpo.="<TD style=\"TEXT-ALIGN: right; ".$label33."\"><STRONG>Carrier</STRONG> :&nbsp;</TD>\n";
		  $cuerpo.="<TD style=\"TEXT-ALIGN: left; PADDING-LEFT: 5px; ".$dato33."\" valign=center>\n";
		  $cuerpo.=(($des_crr != "") ? $des_crr : "&nbsp;");
		  $cuerpo.="</TD>\n";
		  $cuerpo.="</TR>\n";

		  $cuerpo.="<TR>\n";
		  $cuerpo.="<TD style=\"TEXT-ALIGN: right; ".$label3."\"><STRONG>Servicio</STRONG> :&nbsp;</TD>\n";
		  $cuerpo.="<TD style=\"TEXT-ALIGN: left; PADDING-LEFT: 5px; ".$dato3."\" valign=center>\n";
		  $cuerpo.=(($des_svc != "") ? $des_svc : "&nbsp;");
		  $cuerpo.="</TD>\n";
		  $cuerpo.="</TR>\n";

		  $cuerpo.="<TR>\n";
		  $cuerpo.="<TD style=\"TEXT-ALIGN: right; ".$label33."\"><STRONG>Comuna</STRONG> :&nbsp;</TD>\n";
		  $cuerpo.="<TD style=\"TEXT-ALIGN: left; PADDING-LEFT: 5px; ".$dato33."\" valign=center>\n";
		  $cuerpo.=(($nom_cdd != "") ? $nom_cmn : "&nbsp;");
		  $cuerpo.="</TD>\n";
		  $cuerpo.="</TR>\n";
		  
		  $cuerpo.="<TR>\n";
		  $cuerpo.="<TD style=\"TEXT-ALIGN: right; ".$label3."\"><STRONG>Ciudad</STRONG> :&nbsp;</TD>\n";
		  $cuerpo.="<TD style=\"TEXT-ALIGN: left; PADDING-LEFT: 5px; ".$dato3."\" valign=center>\n";
		  $cuerpo.=(($nom_suc != "") ? $nom_cdd : "&nbsp;");
		  $cuerpo.="</TD>\n";
		  $cuerpo.="</TR>\n";
		  
		  $cuerpo.="<TR>\n";
		  $cuerpo.="<TD style=\"TEXT-ALIGN: right; ".$label33."\"><STRONG>Direcci&oacute;n</STRONG> :&nbsp;</TD>\n";
		  $cuerpo.="<TD style=\"TEXT-ALIGN: left; PADDING-LEFT: 5px; ".$dato33."\" valign=center>\n";
		  $cuerpo.=(($nom_cdd != "") ? $dir_suc : "&nbsp;");
		  $cuerpo.="</TD>\n";
		  $cuerpo.="</TR>\n";

		 $labelDisplay = $label3;
		 $datoDisplay = $dato3;
	  }
	  
	  $cuerpo.="<TR>\n";
	  $cuerpo.="<TD style=\"TEXT-ALIGN: right; ".$labelDisplay."\">\n";
	  $cuerpo.="<STRONG>Numero de Personas</STRONG> :&nbsp;</TD>\n";
	  $cuerpo.="<TD style=\"TEXT-ALIGN: left; PADDING-LEFT: 5px; ".$datoDisplay."\" valign=center>\n";
	  $cuerpo.=$aPersonas[$num_per];
	  $cuerpo.="</TD>\n";
	  $cuerpo.="</TR>\n";
	  
	  $labelDisplay = ($labelDisplay == $label3 ? $label33 : $label3);
	  $datoDisplay = ($datoDisplay == $dato3 ? $dato33 : $dato3);
	  $cuerpo.="<TR>\n";
	  $cuerpo.="<TD style=\"TEXT-ALIGN: right; ".$labelDisplay."\">\n";
	  $cuerpo.="<STRONG>Precios</STRONG> :&nbsp;</TD>\n";
	  $cuerpo.="<TD style=\"TEXT-ALIGN: left; PADDING-LEFT: 5px; ".$datoDisplay."\" valign=center>\n";
	  $cuerpo.=$aPrecios[$cod_pre];
	  $cuerpo.="</TD>\n";
	  $cuerpo.="</TR>\n";
	  
	  $labelDisplay = ($labelDisplay == $label3 ? $label33 : $label3);
	  $datoDisplay = ($datoDisplay == $dato3 ? $dato33 : $dato3);
	  $cuerpo.="<TR>\n";
	  $cuerpo.="<TD style=\"TEXT-ALIGN: right; ".$labelDisplay."\" valign=top><STRONG>Observaciones Generales</STRONG> :&nbsp;</TD>\n";
	  $cuerpo.="<TD valign=center style=\"TEXT-ALIGN: left; PADDING-LEFT: 5px; ".$datoDisplay."\">\n";
	  $cuerpo.=str_replace("\&#39;", "'", $observacion);
	  $cuerpo.="</TD>\n";
	  $cuerpo.="</TR>\n";
	  
	  $labelDisplay = ($labelDisplay == $label3 ? $label33 : $label3);
	  $datoDisplay = ($datoDisplay == $dato3 ? $dato33 : $dato3);
	  $cuerpo.="<TR>\n";
	  $cuerpo.="<TD style=\"WIDTH: 200px; TEXT-ALIGN: right;".$labelDisplay."\">\n";
	  $cuerpo.="<STRONG>Comprobante del Pago :</STRONG>&nbsp;</TD>\n";
	  $cuerpo.="<TD style=\"WIDTH: 500px; TEXT-ALIGN: left; PADDING-LEFT: 5px;".$datoDisplay."\" valign=center>\n";
	  if (trim($adj_ctt) != "")
	      $cuerpo.="<A style=\"COLOR: #3366cc; TEXT-DECORATION: underline\" HREF=\"".$home."/".$pathadjuntos.$arc_adj."\">".$adj_ctt."</A></TD>\n";
	  else
	      $cuerpo.="NO ADJUNTADO</TD>\n";
	  $cuerpo.="</TR>\n";

	  $labelDisplay = ($labelDisplay == $label3 ? $label33 : $label3);
	  $datoDisplay = ($datoDisplay == $dato3 ? $dato33 : $dato3);
	  $cuerpo.="<TR>\n";
	  $cuerpo.="<TD style=\"TEXT-ALIGN: right; ".$labelDisplay."\" valign=top><STRONG>N&uacute;mero Transacci&oacute;n</STRONG> :&nbsp;</TD>\n";
	  $cuerpo.="<TD valign=center style=\"TEXT-ALIGN: left; PADDING-LEFT: 5px; ".$datoDisplay."\">\n";
	  $cuerpo.=$Num_TrnBco;
	  $cuerpo.="</TD>\n";
	  $cuerpo.="</TR>\n";

	  $labelDisplay = ($labelDisplay == $label3 ? $label33 : $label3);
	  $datoDisplay = ($datoDisplay == $dato3 ? $dato33 : $dato3);
	  $cuerpo.="<TR>\n";
	  $cuerpo.="<TD style=\"TEXT-ALIGN: right; ".$labelDisplay."\" valign=top><STRONG>Tipo de Documento</STRONG> :&nbsp;</TD>\n";
	  $cuerpo.="<TD valign=center style=\"TEXT-ALIGN: left; PADDING-LEFT: 5px; ".$datoDisplay."\">\n";
	  $cuerpo.=($Tip_DocSII == 1 ? "Boleta" : "Factura");
	  $cuerpo.="</TD>\n";
	  $cuerpo.="</TR>\n";
	  
	  if ($Tip_DocSII == 2) {
			$result1 = mssql_query("vm_s_rutfct  $Cod_Clt, $Cod_PerFct", $link);
			if ($row = mssql_fetch_array($result1)) {
				$labelDisplay = ($labelDisplay == $label3 ? $label33 : $label3);
				$datoDisplay = ($datoDisplay == $dato3 ? $dato33 : $dato3);
				$cuerpo.="<TR>\n";
				$cuerpo.="<TD style=\"TEXT-ALIGN: right; ".$labelDisplay."\" valign=top><STRONG>RUT de Facturaci&oacute;n</STRONG> :&nbsp;</TD>\n";
				$cuerpo.="<TD valign=center style=\"TEXT-ALIGN: left; PADDING-LEFT: 5px; ".$datoDisplay."\">\n";
				$cuerpo.=formatearRut($row['Num_Doc']);
				$cuerpo.="</TD>\n";
				$cuerpo.="</TR>\n";
	  
				$labelDisplay = ($labelDisplay == $label3 ? $label33 : $label3);
				$datoDisplay = ($datoDisplay == $dato3 ? $dato33 : $dato3);
				$cuerpo.="<TR>\n";
				$cuerpo.="<TD style=\"TEXT-ALIGN: right; ".$labelDisplay."\" valign=top><STRONG>Nombre de Facturaci&oacute;n</STRONG> :&nbsp;</TD>\n";
				$cuerpo.="<TD valign=center style=\"TEXT-ALIGN: left; PADDING-LEFT: 5px; ".$datoDisplay."\">\n";
				$cuerpo.=$row['Nom_Clt'];
				$cuerpo.="</TD>\n";
				$cuerpo.="</TR>\n";
				
				$labelDisplay = ($labelDisplay == $label3 ? $label33 : $label3);
				$datoDisplay = ($datoDisplay == $dato3 ? $dato33 : $dato3);
				$cuerpo.="<TR>\n";
				$cuerpo.="<TD style=\"TEXT-ALIGN: right; ".$labelDisplay."\" valign=top><STRONG>Direcci&oacute;n de Facturaci&oacute;n</STRONG> :&nbsp;</TD>\n";
				$cuerpo.="<TD valign=center style=\"TEXT-ALIGN: left; PADDING-LEFT: 5px; ".$datoDisplay."\">\n";
				$cuerpo.=$row['Dir_Fct'];
				$cuerpo.="</TD>\n";
				$cuerpo.="</TR>\n";
				
				$labelDisplay = ($labelDisplay == $label3 ? $label33 : $label3);
				$datoDisplay = ($datoDisplay == $dato3 ? $dato33 : $dato3);
				$cuerpo.="<TR>\n";
				$cuerpo.="<TD style=\"TEXT-ALIGN: right; ".$labelDisplay."\" valign=top><STRONG>Comuna de Facturaci&oacute;n</STRONG> :&nbsp;</TD>\n";
				$cuerpo.="<TD valign=center style=\"TEXT-ALIGN: left; PADDING-LEFT: 5px; ".$datoDisplay."\">\n";
				$cuerpo.=$row['Nom_Cmn'];
				$cuerpo.="</TD>\n";
				$cuerpo.="</TR>\n";
				
				$labelDisplay = ($labelDisplay == $label3 ? $label33 : $label3);
				$datoDisplay = ($datoDisplay == $dato3 ? $dato33 : $dato3);
				$cuerpo.="<TR>\n";
				$cuerpo.="<TD style=\"TEXT-ALIGN: right; ".$labelDisplay."\" valign=top><STRONG>Ciudad de Facturaci&oacute;n</STRONG> :&nbsp;</TD>\n";
				$cuerpo.="<TD valign=center style=\"TEXT-ALIGN: left; PADDING-LEFT: 5px; ".$datoDisplay."\">\n";
				$cuerpo.=$row['Nom_Cdd'];
				$cuerpo.="</TD>\n";
				$cuerpo.="</TR>\n";
				
				$labelDisplay = ($labelDisplay == $label3 ? $label33 : $label3);
				$datoDisplay = ($datoDisplay == $dato3 ? $dato33 : $dato3);
				$cuerpo.="<TR>\n";
				$cuerpo.="<TD style=\"TEXT-ALIGN: right; ".$labelDisplay."\" valign=top><STRONG>Fono de Facturaci&oacute;n</STRONG> :&nbsp;</TD>\n";
				$cuerpo.="<TD valign=center style=\"TEXT-ALIGN: left; PADDING-LEFT: 5px; ".$datoDisplay."\">\n";
				$cuerpo.=$row['Fon_Fct'];
				$cuerpo.="</TD>\n";
				$cuerpo.="</TR>\n";
				
				$labelDisplay = ($labelDisplay == $label3 ? $label33 : $label3);
				$datoDisplay = ($datoDisplay == $dato3 ? $dato33 : $dato3);
				$cuerpo.="<TR>\n";
				$cuerpo.="<TD style=\"TEXT-ALIGN: right; ".$labelDisplay."\" valign=top><STRONG>Fax de Facturaci&oacute;n</STRONG> :&nbsp;</TD>\n";
				$cuerpo.="<TD valign=center style=\"TEXT-ALIGN: left; PADDING-LEFT: 5px; ".$datoDisplay."\">\n";
				$cuerpo.=$row['Fax_Fct'];
				$cuerpo.="</TD>\n";
				$cuerpo.="</TR>\n";
			}
	  }
	  
	  $cuerpo.="<TR><TD colspan=\"2\" style=\"".$label_top."\">&nbsp;</td></tr>\n";
	  $cuerpo.="</TABLE></P>\n";

	  $cuerpo.="</td>\n";
	  $cuerpo.=formar_bottommail();
  }

  return $cuerpo;  
}

function cuerpo_envioSinODC ($id_pais, $folio, $fono, $email, $link) {
  $aPaises   = array(0 => "Demo", 56 => "Chile", 51 => "Peru", 54 => "Argentina");
  $aPersonas = array(1 => "Personal", 2 => "2 - 15", 3 => "15 - 30", 4 => "> 30");
  $aPrecios  = array(1 => "Minorista", 2 => "Mayorista");
  
  $result1 = mssql_query("vm_s_cothdr $folio",$link);
  if ($row = mssql_fetch_array($result1)) {
    $Num_Cot  = $row['Num_Cot'];
	$Cod_Clt  = $row['Cod_Clt'];
	$Cod_Per  = $row['Cod_Per'];
	$Fon_Ctt  = $row['Fon_Ctt'];
	$Mail_Ctt = $row['Mail_Ctt'];
	  
	$rubro = "Orden de Compra Cotizaci&oacute;n ".$Num_Cot;
	mssql_free_result($result1);

	$result1 = mssql_query("vm_codusr_s $Cod_Clt, $Cod_Per", $link);
	if ($row = mssql_fetch_array($result1)) $Cod_Usr = $row['Cod_Usr'];
	  
	$result1 = mssql_query("vm_s_cotsvcweb $folio", $link);
	if ($row = mssql_fetch_array($result1)) {
		$srv1linea = $row["is_svc1"];
		$srv2linea = $row["is_svc2"];
		$servicio = ($srv1linea == 1 or $srv2linea == 1) ? "SI" : "NO";
		$despacho  = $row["is_dsp"];
		if ($despacho == 1) {
			$cod_crr = $row["Cod_Crr"];
			$des_crr = $row["Des_Crr"];
			$cod_crr = $row["Cod_SvcCrr"];
			$des_svc = $row["Des_SvcCrr"];
			$cod_cdd = $row["Cod_Cdd"];
			$nom_cdd = $row["Nom_Cdd"];
			$nom_cmn = $row["Nom_Cmn"];
			$cod_suc = $row["Cod_Suc"];
			$nom_suc = $row["Nom_Suc"];
			$dir_suc = $row["Dir_Suc"];
		}
		$num_per =$row["Num_Per"];
		$cod_pre = $row["Cod_Pre"];
		$observacion = ($row["obs_cot"] == "_NONE" ? "&nbsp;" : utf8_encode($row["obs_cot"]));
		if ($observacion == " ") $observacion = "&nbsp;";
	}
	mssql_free_result($result1); 

	$result1 = mssql_query("vm_s_cotusrweb $folio", $link);
	if ($row = mssql_fetch_array($result1)) {
		$usuario_cot   = $row["Pat_Per"]." ".$row["Mat_Per"]." ".$row["Nom_Per"];
		$nombre_usrcot = str_replace("\&#39;", "'", $usuario_cot);
		$comprador	    = $nombre_usrcot;
		$email_usrcot  = $row["Mail_Ctt"];
		$fono_usrcot   = $row["Fon_Ctt"];
		$mismo         = true;
		if ($row["Cod_Clt"] != $row["Cod_CltPer"]) {
			$CodClt = $row["Cod_Clt"];
			mssql_free_result($result1); 
			$result1 = mssql_query("vm_cli_s $CodClt", $link);
			$mismo   = false;
			if ($row = mssql_fetch_array($result1))
				$comprador     = str_replace("\&#39;", "'", $row["RznSoc_Per"]);
		}
	}
    mssql_free_result($result1); 
	  
	/* Consultas realizadas por el Usuario a Vestmed */
	$tot_cnaclt = 0;
	$tot_sinresemp = 0;
	$result = mssql_query("vm_totcna_totres $folio, $Cod_Per");
	if ($row = mssql_fetch_array($result)) {
		$tot_cnaclt    = $row["tot_cna"];
		$tot_sinresemp = $row["tot_sinres"];
	}
	
	/* Consultas realizadas por Vestmed al Usuario */
	$tot_cnaemp = 0;
	$tot_sinresclt = 0;
	$result = mssql_query("vm_totcna_totres $folio, 0");
	if ($row = mssql_fetch_array($result)) {
		$tot_cnaemp = $row["tot_cna"];
		$tot_sinresclt = $row["tot_sinres"];
	}
	$tot_cna = $tot_cnaclt + $tot_cnaemp;
	$tot_new = $tot_sinresemp + $tot_sinresclt;
	  
	$cuerpo = "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">\n";
	$cuerpo .= "<html xmlns=\"http://www.w3.org/1999/xhtml\">\n";
	$cuerpo .= "<head>\n";
	$cuerpo .= "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" />\n";
	$cuerpo .= "<title>Comprobante del Proceso de Cotizaci�n</title>\n";
	$cuerpo .= "</head>\n";
	$cuerpo .= "<body>\n";
	$cuerpo .= "<center>\n";
	$cuerpo .= "<!Header>\n";
	$cuerpo .= "<table width=\"559\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n";
	$cuerpo .= "  <tr bgcolor=\"#1ac8c9\">\n";
	$cuerpo .= "	 <td colspan=\"6\">&nbsp;</td>\n";
	$cuerpo .= "  </tr>\n";
	$cuerpo .= "  <td colspan=\"6\" align=\"right\" width=\"1\"><font face=\"Arial, Helvetica, sans-serif\" size=\"1\" color=\"#629394\"><font face=\"Arial, Helvetica, sans-serif\">Mi Cuenta | Ayuda</font></td>\n";
	$cuerpo .= "  <tr rowspan=\"3\" background=\"logo.jpg\" height=\"70\">\n";
	$cuerpo .= "	<td colspan=\"6\">&nbsp;</td>\n";
	$cuerpo .= "  </tr>\n";
	$cuerpo .= "  <td  height=20\" bgcolor=\"#F8F8F8\" colspan=\"6\" align=\"center\"><font face=\"Arial, Helvetica, sans-serif\" color=\"#000030\" size=\"2\">&nbsp;&nbsp;SOLICITUD DE COMPRA PARA COTIZACION # $Num_Cot</font></td>\n";
	$cuerpo .= "  <tr>\n";
	$cuerpo .= "	<td colspan=\"6\" height=12></td>\n";
	$cuerpo .= "  </tr>\n";
	$cuerpo .= "<tr>\n";
	$cuerpo .= "	<td colspan=\"6\" align=\"left\" width=\"1\"><font face=\"Arial, Helvetica, sans-serif\" color=\"#000030\" size=\"1\"><STRONG><STRONG></td>\n";
	$cuerpo .= "  </tr>\n";
	$cuerpo .= "  <tr>\n";
	$cuerpo .= "	<td colspan=\"6\" align=\"left\" width=\"1\"><font face=\"Arial, Helvetica, sans-serif\" color=\"#000030\" size=\"2\"><strong> El cliente ha presionado el boton comprar de la pagina web. Por favor comunicarse inmediatamente.</strong></td>\n";
	$cuerpo .= "  </tr>\n";
	$cuerpo .= "  <tr>\n";
	$cuerpo .= "	<td colspan=\"6\" align=\"left\" width=\"1\"><font face=\"Arial, Helvetica, sans-serif\" color=\"#000030\" size=\"2\">&nbsp;</td>\n";
	$cuerpo .= "  </tr>\n";
	$cuerpo .= "  <tr>\n";
	$cuerpo .= "	<td colspan=\"6\" align=\"left\" width=\"1\"><font face=\"Arial, Helvetica, sans-serif\" color=\"#000030\" size=\"2\">Gracias !!!</td>\n";
	$cuerpo .= "  </tr>\n";
	$cuerpo .= "  <tr>\n";
	$cuerpo .= "	<td colspan=\"6\"height=10></td>\n";
	$cuerpo .= "  </tr>\n";
	$cuerpo .= "  <tr>\n";
	$cuerpo .= "	<td colspan=\"6\" align=\"left\" width=\"1\"><font face=\"Arial, Helvetica, sans-serif\" color=\"#629394\" size=\"1\">Resumen</td>\n";
	$cuerpo .= "  </tr>\n";
	$cuerpo .= "  <!Antecedentes del Cliente>\n";
	$cuerpo .= "  <tr bgcolor=\"#83d7d6\" height=\"15\">\n";
	$cuerpo .= "	<td colspan=\"6\"><font face=\"Arial, Helvetica, sans-serif\" color=\"#000000\" size=\"1\">&nbsp;&nbsp;Informaci�n del Cliente</font></td>\n";
	$cuerpo .= "  </tr>\n";
	$cuerpo .= "  <tr>\n";
	$cuerpo .= "  	<td colspan=\"1\"><font face=\"Arial, Helvetica, sans-serif\" size=\"2\">&nbsp;Nombre:</font></td>\n";
	$cuerpo .= "    	<td colspan=\"5\"><font face=\"Arial, Helvetica, sans-serif\" size=\"2\" color=\"#404040\">$usuario_cot</font></td>\n";
	$cuerpo .= "  </tr>\n";
	$cuerpo .= "  <tr>\n";
	$cuerpo .= "    	<td colspan=\"1\"><font face=\"Arial, Helvetica, sans-serif\" size=\"2\">&nbsp;Telefono:</font></td>\n";
	$cuerpo .= "    	<td colspan=\"5\"><font face=\"Arial, Helvetica, sans-serif\" size=\"2\" color=\"#404040\">$fono_usrcot</font></td>\n";
	$cuerpo .= "  </tr>\n";
	$cuerpo .= "  <tr>\n";
	$cuerpo .= "    	<td colspan=\"1\"><font face=\"Arial, Helvetica, sans-serif\" size=\"2\">&nbsp;E-mail:</font></td>\n";
	$cuerpo .= "    	<td colspan=\"5\"><font face=\"Arial, Helvetica, sans-serif\" size=\"2\" color=\"#404040\">$email_usrcot</font></td>\n";
	$cuerpo .= "  </tr>\n";
	$cuerpo .= "  <tr>\n";
	$cuerpo .= "   	<td colspan=\"6\"height=10></td>\n";
	$cuerpo .= "  </tr>\n";
	$cuerpo .= "  <tr bgcolor=\"#83d7d6\" height=\"15\">\n";
	$cuerpo .= "    	<td colspan=\"6\"><font face=\"Arial, Helvetica, sans-serif\" color=\"#000000\" size=\"1\">&nbsp;&nbsp;Detalle de Productos</font></td>\n";
	$cuerpo .= "  </tr>\n";
	$cuerpo .= "</table>\n";

	$cuerpo .= "<!Tabla Productos Solicitados>\n";

	$cuerpo .= "<table width=\"559\" border=\"0\" bordercolor=\"#3e3e3e\" bordercolorlight=0 bordercolordark=0 cellspacing=\"0\"cellpadding=\"0\">\n";
	$cuerpo .= "  <tr align=\"center\" bgcolor=\"#629394\" height=\"15\">\n";
	$cuerpo .= "    	<td WIDTH=100><font face=\"Arial, Helvetica, sans-serif\" size=\"1\" color=\"#000000\">Marca</font></td>\n";
	$cuerpo .= "    	<td WIDTH=100><font face=\"Arial, Helvetica, sans-serif\" size=\"1\" color=\"#000000\">Linea</font></td>\n";
	$cuerpo .= "    	<td WIDTH=119><font face=\"Arial, Helvetica, sans-serif\" size=\"1\" color=\"#000000\">Codigo</font></td>\n";
	$cuerpo .= "    	<td WIDTH=70><font face=\"Arial, Helvetica, sans-serif\" size=\"1\" color=\"#000000\">Color</font></td>\n";
	$cuerpo .= "    	<td WIDTH=70><font face=\"Arial, Helvetica, sans-serif\" size=\"1\" color=\"#000000\">Talla</font></td>\n";
	$cuerpo .= "    	<td WIDTH=70><font face=\"Arial, Helvetica, sans-serif\" size=\"1\" color=\"#000000\">Cant.</font></td>\n";
	$cuerpo .= "  </tr>\n";
 
	  $j = 0;
	  $i = 1;
	  $result1 = mssql_query("vm_s_cotdet $folio", $link);
	  while ($row = mssql_fetch_array($result1)) {
		 $cuerpo.="   <TR align=\"center\">\n";
    	 $cuerpo.="   <td><font face=\"Arial, Helvetica, sans-serif\" size=\"2\" color=\"#3e3e3e\">".$row['Cod_Mca']."</font></td>\n";
    	 $cuerpo.="   <td><font face=\"Arial, Helvetica, sans-serif\" size=\"2\" color=\"#3e3e3e\">".$row['Des_LinMca']."</font></td>\n";
    	 $cuerpo.="   <td><font face=\"Arial, Helvetica, sans-serif\" size=\"2\" color=\"#3e3e3e\">".$row['Cod_Sty']."</font></td>\n";
    	 $cuerpo.="   <td><font face=\"Arial, Helvetica, sans-serif\" size=\"2\" color=\"#3e3e3e\">".$row['Key_Pat']."</font></td>\n";
    	 $cuerpo.="   <td><font face=\"Arial, Helvetica, sans-serif\" size=\"2\" color=\"#3e3e3e\">".$row['Val_Sze']."</font></td>\n";
    	 $cuerpo.="   <td><font face=\"Arial, Helvetica, sans-serif\" size=\"2\" color=\"#3e3e3e\">".$row['Cot_Ctd']."</font></td>\n";
		 $cuerpo.="   </TR>\n";
	  } 
	  mssql_free_result($result1); 
		
	  $cuerpo.="</TABLE>\n";


	  $cuerpo.="<!Tabla Condiciones de Compra>\n";
	  $cuerpo.="<table width=\"559\" border=\"0\" bordercolor=\"#3e3e3e\" bordercolorlight=0 bordercolordark=0 cellspacing=\"0\"cellpadding=\"0\">\n";
	  $cuerpo.="  <td colspan=\"6\"height=12></td>\n";
	  $cuerpo.="  <tr bgcolor=\"#83d7d6\" height=\"15\">\n";
	  $cuerpo.="    	<td colspan=\"6\"><font face=\"Arial, Helvetica, sans-serif\" color=\"#000000\" size=\"1\">&nbsp;&nbsp;Condiciones</font></td>\n";
	  $cuerpo.="  </tr>\n";
	  $cuerpo.="  <tr>\n";
	  $cuerpo.="    	<td colspan=\"1\" WIDTH=80><font face=\"Arial, Helvetica, sans-serif\" size=\"2\">&nbsp;Bordado:</font></td>\n";
	  $cuerpo.="    	<td colspan=\"5\"><font face=\"Arial, Helvetica, sans-serif\" size=\"2\" color=\"#404040\">$servicio</font></td>\n";
	  $cuerpo.="  </tr>\n";
	  $cuerpo.="  <tr>\n";
	  $cuerpo.="    	<td colspan=\"1\"><font face=\"Arial, Helvetica, sans-serif\" size=\"2\">&nbsp;Despacho:</font></td>\n";
	  $cuerpo.="    	<td colspan=\"5\"><font face=\"Arial, Helvetica, sans-serif\" size=\"2\" color=\"#404040\">".($despacho == 1 ? "SI" : "NO")."</font></td>\n";
	  $cuerpo.="  </tr>\n";
	  
	  if ($despacho == 1) {  
		  $cuerpo.="  <tr>\n";
		  $cuerpo.="    	<td colspan=\"1\"><font face=\"Arial, Helvetica, sans-serif\" size=\"2\">&nbsp;Carrier:</font></td>\n";
		  $cuerpo.="    	<td colspan=\"1\"WIDTH=250><font face=\"Arial, Helvetica, sans-serif\" size=\"2\" color=\"#404040\">".$des_crr."</font></td>\n";
		  $cuerpo.="    	<td colspan=\"2\"><font face=\"Arial, Helvetica, sans-serif\" size=\"2\"></font></td>\n";
		  $cuerpo.="        <td colspan=\"1\"WIDTH=60><font face=\"Arial, Helvetica, sans-serif\" size=\"2\">Servicio:</font></td>\n";
		  $cuerpo.="    	<td colspan=\"1\"><font face=\"Arial, Helvetica, sans-serif\" size=\"2\" color=\"#404040\">&nbsp;".$des_svc."</font></td>\n";
		  $cuerpo.="  </tr>\n";
		  $cuerpo.="  <tr>\n";
		  $cuerpo.="     	<td colspan=\"1\"><font face=\"Arial, Helvetica, sans-serif\" size=\"2\">&nbsp;Direcci&oacute;n:</font></td>\n";
		  $cuerpo.="    	<td colspan=\"5\"><font face=\"Arial, Helvetica, sans-serif\" size=\"2\" color=\"#404040\">".$dir_suc."</font></td>\n";
		  $cuerpo.="  </tr>\n";
		  $cuerpo.="  <tr>\n";
		  $cuerpo.="    	<td colspan=\"1\"><font face=\"Arial, Helvetica, sans-serif\" size=\"2\">&nbsp;Ciudad :</font></td>\n";
		  $cuerpo.="    	<td colspan=\"1\"><font face=\"Arial, Helvetica, sans-serif\" size=\"2\" color=\"#404040\">".$nom_cdd."</font></td>\n";
		  $cuerpo.="    	<td colspan=\"2\" ><font face=\"Arial, Helvetica, sans-serif\" size=\"2\"></font></td>\n";
		  $cuerpo.="    	<td colspan=\"1\" ><font face=\"Arial, Helvetica, sans-serif\" size=\"2\">Comuna:</font></td>\n";
		  $cuerpo.="    	<td colspan=\"1\"><font face=\"Arial, Helvetica, sans-serif\" size=\"2\" color=\"#404040\">&nbsp;".$nom_cmn."</font></td>\n";
		  $cuerpo.="  </tr>\n";
	  }
	  
	  $cuerpo.="  <tr>\n";
	  $cuerpo.="    	<td colspan=\"1\"><font face=\"Arial, Helvetica, sans-serif\" size=\"2\">&nbsp;# Personas:</font></td>\n";
	  $cuerpo.="    	<td colspan=\"1\"><font face=\"Arial, Helvetica, sans-serif\" size=\"2\" color=\"#404040\">".$aPersonas[$num_per]."</font></td>\n";
	  $cuerpo.="    	<td colspan=\"2\" ><font face=\"Arial, Helvetica, sans-serif\" size=\"2\"></font></td>\n";
	  $cuerpo.="    	<td colspan=\"1\" ><font face=\"Arial, Helvetica, sans-serif\" size=\"2\">Precios:</font></td>\n";
	  $cuerpo.="    	<td colspan=\"1\"><font face=\"Arial, Helvetica, sans-serif\" size=\"2\" color=\"#404040\">&nbsp;".$aPrecios[$cod_pre]."</font></td>\n";
	  $cuerpo.="  </tr>\n";
	  $cuerpo.="  <tr>\n";
	  $cuerpo.="    	<td colspan=\"6\"height=12></td>\n";
	  $cuerpo.="  </tr>\n";
	  $cuerpo.="  <tr bgcolor=\"#83d7d6\" height=\"15\">\n";
	  $cuerpo.="    	<td colspan=\"6\"><font face=\"Arial, Helvetica, sans-serif\" color=\"#000000\" size=\"1\">&nbsp;&nbsp;Observaciones Generales</font></td>\n";
	  $cuerpo.="  </tr>\n";
	  $cuerpo.="  <tr>\n";
	  $cuerpo.="    	<td colspan=\"6\"><font face=\"Arial, Helvetica, sans-serif\" size=\"2\" color=\"#404040\">&nbsp;<strong>Historial de Mensajes: (".$tot_cna.")</strong><br> &nbsp; Mensajes Nuevos: (".$tot_new.") <br><br> &nbsp;<strong>Informaci�n enviada en la solicitud de contacto: </strong> <br> &nbsp; Telefono: $Fon_Ctt<br>&nbsp; Email: $Mail_Ctt<br> &nbsp; Mensaje: ".$observacion." <br></font></td>\n";
	  $cuerpo.="  </tr>\n";
	  $cuerpo.="  <tr>\n";
	  $cuerpo.="  	<td colspan=\"6\"></td>\n";
	  $cuerpo.="  </tr>\n";
	  $cuerpo.="  <tr height=\"35\">\n";
	  $cuerpo.="  	<td colspan=\"6\"></td>\n";
	  $cuerpo.="  </tr>\n";
	  $cuerpo.="  <td colspan=\"6\" align=\"right\" width=\"1\"><font face=\"Arial, Helvetica, sans-serif\" size=\"1\" color=\"#629394\"><font face=\"Arial, Helvetica, sans-serif\">Mapa | Telefono | Ayuda</font></td>  \n";
	  $cuerpo.="  <tr bgcolor=\"#31bdbb\" height=\"25\" align=\"right\">\n";
	  $cuerpo.="    	<td colspan=\"6\"><font face=\"Arial, Helvetica, sans-serif\" size=\"1\" color=\"#FFFFFF\">VESTMED � Ltda. 2010 Todos los derechos reservados&nbsp;&nbsp;</font></td>\n";
	  $cuerpo.="  </tr>\n";
	  $cuerpo.="  <tr>\n";
	  $cuerpo.="  	<td colspan=\"6\" height=\"10\"></td>\n";
	  $cuerpo.="  </tr>\n";
	  $cuerpo.="  <tr>\n";
	  $cuerpo.="    	<td colspan=\"6\"><font face=\"Arial, Helvetica, sans-serif\" size=\"1\" color=\"#C8C8C8\" >Nota: Los acentos han sido omitidos intencionalmente </font></td>\n";
	  $cuerpo.="  </tr>\n";
	  $cuerpo.="</table>\n";
	  $cuerpo.="</center>\n";
	  $cuerpo.="</body>\n";
	  $cuerpo.="</html>\n";
  }

  return $cuerpo;  
}

function formar_topmail ($id_pais,$titulo) {
  $aPaises   = array(0 => "Demo", 56 => "Chile", 51 => "Peru", 54 => "Argentina");
  $titulo_mail = "FONT-SIZE: 20px; COLOR: black; FONT-FAMILY: Verdana, Arial, Helvetica";
  
  $cuerpo = "";
  $cuerpo.="<?xml version=\"1.0\"?>\n";
  $cuerpo.="<html xmlns:fo=\"http://www.w3.org/1999/XSL/Format\" xmlns=\"http://www.w3.org/1999/xhtml\">\n";
  $cuerpo.="<head>\n";
  $cuerpo.="<title>Aviso Cotizacion</title>\n";
  //$cuerpo.="<LINK href=\"http://www.coticemos.com/".$aPaises[$id_pais]."/Include/estilos.css\" type=text/css rel=stylesheet>\n";
  //$cuerpo.="<LINK href=\"Include/estilos.css\" type=text/css rel=stylesheet>\n";
  $cuerpo.="</HEAD>\n";
  $cuerpo.="<body link=\"#CC0000\" vlink=\"#CC0000\" alink=\"#CC0000\">\n";
  $cuerpo.="<table width=\"532\" border=\"0\" align=\"center\" cellpadding=\"1\" cellspacing=\"1\" bgcolor=\"#E5E5E5\">\n";
  $cuerpo.="<tr><td bgcolor=\"#CCCCCC\">\n";
  $cuerpo.="<table width=\"100%\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\">\n";
  $cuerpo.="<tr>\n";
  //$cuerpo.="<td bgcolor=\"#FFFFFF\">\n";
  //$cuerpo.="<a href=\"http://www.coticemos.com/".$aPaises[$id_pais]."/default.php\" target=\"_blank\">\n";
  //$cuerpo.="<img src=\"http://www.coticemos.com/Images2/logomail_0.gif\" border=\"0\"/>\n";
  //$cuerpo.="<td align=right width=532 height=57 background=http://www.coticemos.com/Images2/tituloMail_0.jpg\n";
  //$cuerpo.="    onClick=\"window.open('http://www.coticemos.com/".$aPaises[$id_pais]."/default.php')\"\n";
  //$cuerpo.="    style=\"cursor:hand; padding-right:20px\"><span class=titulo_mail>".$titulo."<span>\n";

  $cuerpo.="<td align=left width=532 height=57 background=\"http://www.vestmed.cl/images/fondomail_0.jpg\">\n";
  $cuerpo.="<a href=\"http://www.vestmed.cl\" target=\"_blank\">\n";
  $cuerpo.="<IMG src=\"http://www.vestmed.cl/images/header.jpg\" border=0 top=\"100\" width=\"532\"></a>\n";
  //$cuerpo.="<span style=\"".$titulo_mail."\">".$titulo."</span>";
  //$cuerpo.=$titulo;
  $cuerpo.="</TD>\n";
  $cuerpo.="</tr>\n";
  $cuerpo.="<tr>\n";
  $cuerpo.="<td bgcolor=\"#FFFFFF\">\n";
  $cuerpo.="<img src=\"http://www.vestmed.cl/images/topmail_0.jpg\" width=\"532\" height=\"34\" />\n";
  $cuerpo.="</td>\n";
  $cuerpo.="</tr>\n";
  $cuerpo.="<tr><td bgcolor=\"#FFFFFF\">\n";
  $cuerpo.="<table width=\"100%\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" bgcolor=\"#F4F4F4\">\n";
  $cuerpo.="<tr>\n";
  $cuerpo.="<td width=\"7%\">&nbsp;</td>\n";

  return $cuerpo;
}

function formar_bottommail () {

  $cuerpo ="<td width=\"7%\">&nbsp;</td>\n";
  $cuerpo.="</tr>\n";
  $cuerpo.="</table>\n";
  $cuerpo.="</td>\n";
  $cuerpo.="</tr>\n";
  $cuerpo.="<tr>\n";
  $cuerpo.="<td bgcolor=\"#FFFFFF\">\n";
  $cuerpo.="<img src=\"http://www.vestmed.cl/images/bottommail_0.jpg\" width=\"532\" height=\"37\" />\n";
  $cuerpo.="</td>\n";
  $cuerpo.="</tr>\n";
  $cuerpo.="</table>\n";
  $cuerpo.="</td>\n";
  $cuerpo.="</tr>\n";
  $cuerpo.="</table>\n";
  $cuerpo.="<table width=\"532\" border=\"0\" align=\"center\" cellpadding=\"5\" cellspacing=\"0\">\n";
  $cuerpo.="<tr>\n";
  $cuerpo.="<td>\n";
  $cuerpo.="<div align=\"center\">\n";
  $cuerpo.="<img src=\"http://www.vestmed.cl/images/footer.jpg\" width=\"532\"/>\n";
  $cuerpo.="</div>\n";
  $cuerpo.="</td>\n";
  $cuerpo.="</tr>\n";
  $cuerpo.="</table>\n";
  $cuerpo.="</body>\n";
  $cuerpo.="</html>\n";

  return $cuerpo;
}

function display_max ($texto, $largomax)
{
	if (strlen($texto) <= $largomax)
	    return $texto;
	return substr($texto,0,$largomax)."...";
}

// --- ooooo ----

?>
