<?php
ini_set("mssql.textsize",  "2147483647");
ini_set("mssql.textlimit", "2147483647");

include("connect.php");

//Funcion para evitar sql injection:
function ok($sql_query)
{
    //return str_replace("'", "\'", $sql_query);
    //return mysql_real_escape_string($sql_query);
    return str_replace("'", "''", $sql_query);
}

//Funcion para mostrar imagenes almacenadas en la base de datos y mostrarlas resizeadas
function printimg_resize($name, $filter, $height=0, $width=0, $title='')
{
    if($title=='')$title=$name;
    $height_string="";
    $width_string="";
    $finalsize=2000;
    if($height != 0)
        {
        $finalsize=min($height,$finalsize);
        $height_string = ' height="'.$height.'"px ';
        }
    if($width != 0)
        {
        $finalsize=min($width,$finalsize);
        $width_string = ' width="'.$width.'"px ';
        }
    if($finalsize==0)$finalsize=60;
    $resize_string=' onload="thumb_resize(\''.$name.'\','.$finalsize.')" ';

    $resize_string="";
    //$height_string = "";
    //$width_string="";
    return '<img id="'.$name.'" alt="'.$title.'" border="0" src="imagedisplay.php?name='.$name.'&filter='.$filter.'"'.$height_string.$width_string.$resize_string. '/>';
}

//Funcion para mostrar imagenes almacenadas en la base de datos e imprimir la ruta
function printimg($name, $filter, $title)
{
    if($title=='')$title=$name;
    return '<img alt="'.$title.'" src="imagedisplay.php?name='.$name.'&filter='.$filter.'/>';
}

//Funcion para imprimir la ruta de la imagen
function printimg_addr($name, $filter)
{
    return '../imagedisplay.php?name='.$name.'&filter='.$filter;
}
function mostrarSidebar()
{
    return jsShowMenu();
}

function funcionesJS()
{
    ?>
     <!-- FreeStyle Menu v1.0RC by Angus Turnbull http://www.twinhelix.com -->
     <script type="text/javascript" src="fsmenu.js"></script>
     <link rel="stylesheet" type="text/css" id="listmenu-o"
      href="listmenu_o.css" title="Vertical 'Office'" />
     <link rel="stylesheet" type="text/css" id="fsmenu-fallback"
      href="listmenu_fallback.css" />

      <script type="text/javascript">
       function thumb_resize(which, max) {
        var elem = document.getElementById(which);

        if (elem == undefined || elem == null) return false;
        var orig_width = elem.width;
        var orig_height = elem.height;

        if (max == undefined) max = 150;
        if (elem.width > elem.height) {
        if (elem.width > max) { elem.width = max; elem.height = orig_height*(max/orig_width);}
        } else {
        if (elem.height > max) { elem.height = max; elem.width = orig_width*(max/orig_height);};
        }
        }
    </script>

    <?
}

function jsShowMenu()
{
    $ret = "";
    $ret.= jsGenerateMenu();
    $ret.= '<script type="text/javascript">';
    $ret.= "var listMenu = new FSMenu('listMenu', true, 'display', 'block', 'none');";
    $ret.= 'listMenu.hideDelay = 300;
    listMenu.animations[listMenu.animations.length] = FSMenu.animFade;
    listMenu.animations[listMenu.animations.length] = FSMenu.animSwipeDown;
    var arrow = null;';
    $ret.= "if (document.createElement && document.documentElement)
    {
     arrow = document.createElement('span');
     arrow.appendChild(document.createTextNode('>'));
     //arrow = document.createElement('img');
     //arrow.src = 'arrow.gif';
     //arrow.style.borderWidth = '0';
     arrow.className = 'subind';
    }";
    $ret.= "addEvent(window, 'load', new Function('" . 'listMenu.activateMenu("listMenuRoot",' . "arrow)'))";
    $ret.= "</script>";
    return $ret;
}

function jsGenerateMenu()
{
    //Obtengo la naturaleza:
    global $db;
    //$sql_1 = mssql_init("vm_strcat_catasc", $db);
    //$sp_1 = mssql_execute($sql_1);
    $sp_1 = mssql_query("vm_strcat_catasc", $db);
    $sbar='<ul class="menulist" id="listMenuRoot">';
    while($row1 = @mssql_fetch_array($sp_1))
    {
        $nat_id = trim($row1["cat_id"]);
        $nat_desc = ucwords(trim($row1["category"]));
        $sbar.="<li>".'<a href="shop_display_products.php?'."nat=$nat_id".'">'.$nat_desc."</a>\n";
        $sbar.="<ul>"."\n";
        //Obtengp el sexo
        //$sql_2 = mssql_init("vm_strcat_sex", $db);
        //$sp_2 = mssql_execute($sql_2);
        $sp_2 = mssql_query("vm_strcat_sex '".$nat_id."'", $db);
        while($row2 = @mssql_fetch_array($sp_2))
        {
            $sex_id = trim($row2["cod"]);
            $sex_desc = ucwords(trim($row2["sexo"]));
            $sbar.="<li>".'<a href="shop_display_products.php?'."nat=$nat_id&filter=$sex_id".'">'.$sex_desc."</a>\n";
            $sbar.="<ul>"."\n";
            //Obtengo el patron
            //$sql_3 = mssql_init("vm_strcat_pat", $db);
            //$sp_3 = mssql_execute($sql_3);
            $sp_3 = mssql_query("vm_strcat_pat '".$nat_id."','".$sex_id."'", $db);
            while($row3 = @mssql_fetch_array($sp_3))
            {
                $grppat_id = trim($row3["pattern"]);
                $grppat_desc = ucwords(trim($row3["pattern"]));
                $sbar.='<li>'."\n".'<a href="shop_display_products.php?'."nat=$nat_id&filter=$sex_id&pattern=$grppat_id".'">'.$grppat_desc."</a>\n";
                $sbar.="</li>"."\n";
            }
            $sbar.="</ul>\n</li>"."\n";
        }
        $sbar.="</ul>\n</li>"."\n";
    }
    $sbar.='</ul>'."\n";
    return $sbar;
}

// MLB agregado para el manejo de uusarios
function BusCol($ColCript, $sVal) {
   for ($i = 1; $i <= 10; $i++)
     if ($ColCript[$i] == $sVal)
        return $i; 
}

function BusRow($RowCript, $sVal) {
   for ($i = 1; $i <= 8; $i++)
     if ($RowCript[$i] == $sVal)
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

function Maximo($a, $b) {
 return $a > $b ? $a : $b;
}

function Minimo($a, $b) {
 return $a < $b ? $a : $b;
}

function right($value, $count){
    $value = substr($value, (strlen($value) - $count), strlen($value));
    return $value;
}

function left($string, $count){
    return substr($string, 0, $count);
}

function fechafmt ($fecha) {
	return right($fecha, 2)."/".substr($fecha, 4, 2)."/".left($fecha,4);
}

function encriptar ($sValor, $nMaxLen) {

   $ColCript = "01XS9RnLWBkR";
   $RowCript = "0PZ8y3JXMa";

   $Matrix[0] = split ("/", "0/0/0/0/0/0/0/0/0/0/0");
   $Matrix[1] = split ("/", "0/A/B/a/b/K/k/P/p/Y/y");
   $Matrix[2] = split ("/", "0/C/F/c/f/L/Q/l/q/Z/z");
   $Matrix[3] = split ("/", "0/D/d/G/g/M/m/R/r/Ñ/ñ");
   $Matrix[4] = split ("/", "0/E/e/H/h/O/o/S/s/1/4");
   $Matrix[5] = split ("/", "0/I/i/J/j/N/n/T/t/2/5");
   $Matrix[6] = split ("/", "0/U/u/V/v/W/w/X/x/3/6");
   $Matrix[7] = split ("/", "0/7/8/9/0/-/_/./ /*/+");
   $Matrix[8] = split ("/", "0/$/=/(/)/!/&/#/;/?/¿");

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

function desencriptar ($sPassWd) {
   $ColCript = "01XS9RnLWBkR";
   $RowCript = "0PZ8y3JXMa";

   $Matrix[0] = split ("/", "0/0/0/0/0/0/0/0/0/0/0");
   $Matrix[1] = split ("/", "0/A/B/a/b/K/k/P/p/Y/y");
   $Matrix[2] = split ("/", "0/C/F/c/f/L/Q/l/q/Z/z");
   $Matrix[3] = split ("/", "0/D/d/G/g/M/m/R/r/Ñ/ñ");
   $Matrix[4] = split ("/", "0/E/e/H/h/O/o/S/s/1/4");
   $Matrix[5] = split ("/", "0/I/i/J/j/N/n/T/t/2/5");
   $Matrix[6] = split ("/", "0/U/u/V/v/W/w/X/x/3/6");
   $Matrix[7] = split ("/", "0/7/8/9/0/-/_/./ /*/+");
   $Matrix[8] = split ("/", "0/$/=/(/)/!/&/#/;/?/¿");

   $sClave = "";
   $nAux = (strlen($sPassWd) - 3) / 2;

   for ($nIndex = 1; $nIndex <= $nAux; $nIndex++) 
      if ($sPassWd[2*$nIndex] == "a")
         $sClave = substr($sPassWd, 0, 2*$nIndex - 1);

   if ($sClave == "" && strlen($sPassWd) != 0) 
      return "¡ERROR!";

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

function enviar_mail2 ($destinatario,$asunto,$cuerpo,$formato) {

   if ($formato == "HTML") {	
      //para el envío en formato HTML 
      $headers = "MIME-Version: 1.0\r\n"; 
      $headers .= "Content-type: text/html; charset=iso-8859-1\r\n"; 

      //dirección del remitente 
      $headers .= "From: www.vestmed.cl <informaciones@vestmed.cl>\r\n"; 

      //dirección de respuesta, si queremos que sea distinta que la del remitente 
      //$headers .= "Reply-To: informaciones@coticemos.com\r\n"; 

     //ruta del mensaje desde origen a destino 
     //$headers .= "Return-path: informaciones@coticemos.com\r\n"; 

     //direcciones que recibián copia 
     //$headers .= "Cc: microsign@microsystem.cl\r\n"; 

     //direcciones que recibirán copia oculta 
     //$headers .= "Bcc: mlabrinb@vtr.net\r\n"; 

     return mail($destinatario,$asunto,$cuerpo,$headers) ;
   }
   else
     return mail($destinatario,$asunto,$cuerpo);
}

function enviar_mail ($destinatario,$asunto,$cuerpo,$formato) {
	require_once('PHP-Mail/class.phpgmailer.php');
	$mail = new PHPGMailer();
	$mail->Username = 'mario.labrin@microsign.cl';
	$mail->Password = 'micro2009';
	$mail->From = 'mario.labrin@microsign.cl';
	$mail->FromName = 'Pagina Web';
	$mail->Subject = $asunto;
	$mail->AddAddress($destinatario);
	$mail->Body = $cuerpo;

	if ($formato == "HTML") $mail->IsHTML(true);
	
	$mail->Send();
}

function cuerpo_cotizacion ($id_pais,$folio,$link) {
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

  $result1 = mssql_query("vm_s_cotsvcweb $folio", $link);
  if ($row = mssql_fetch_array($result1)) {
	$srv1linea = $row["is_svc1"];
	$srv2linea = $row["is_svc2"];
	$despacho  = $row["is_dsp"];
	$num_cot   = $row['Num_Cot'];
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
    $rubro = "Cotización Nro ".$num_cot;
    mssql_free_result($result1); 
  }
  else return "";

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
  
  $cuerpo = formar_topmail ($id_pais, "COTIZACION");
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
  $result1 = mssql_query("vm_s_cotweb $folio", $link);
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
     $cuerpo.="   <TD style=\"TEXT-ALIGN: center; ".$clase1."\">".$row["cod_sty"]."</TD>\n";
     $cuerpo.="   <TD style=\"TEXT-ALIGN: center; ".$clase2."\">".$row["cod_mca"]."</TD>\n";
     $cuerpo.="   <TD style=\"TEXT-ALIGN: center; ".$clase2."\">".$row["des_linmca"]."</TD>\n";
     $cuerpo.="   <TD style=\"TEXT-ALIGN: center; ".$clase2."\">".$row["val_sze"]."</TD>\n";
     $cuerpo.="   <TD style=\"TEXT-ALIGN: center; ".$clase2."\">".(($row['cod_pat'] != "_ALL") ? $row['key_pat'] : "_ALL")."</TD>\n";
     $cuerpo.="   <TD style=\"TEXT-ALIGN: center; ".$clase2."\">".$row["cot_ctd"]."</TD>\n";
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
  
  $cuerpo.="<TR><TD colspan=\"2\" style=\"".$label_top."\">&nbsp;</td></tr>\n";
  $cuerpo.="</TABLE></P>\n";

  $cuerpo.="</td>\n";
  $cuerpo.=formar_bottommail();


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

function cuerpo_contactoweb ($home, $pathadjuntos, $id_pais,$folio,$link) {
  $aPaises   = array(0 => "Demo", 56 => "Chile", 51 => "Peru", 54 => "Argentina");
  $aTipCna   = array(1 => "Informaci&oacute;n del Producto", 2 =>  "");
  
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

  $rubro = "Contacto Nro ".$folio;
  
  $result1 = mssql_query("vm_cttweb_s $folio", $link);
  
  if ($row = mssql_fetch_array($result1)) {
	 $tip_ctt = $row['tip_ctt'];	
	 $nom_ctt = $row['nom_ctt']; 
	 $cod_tipdoc = $row['cod_tipdoc']; 
	 $rut_ctt = $row['rut_ctt'];
	 $nom_itt = $row['nom_itt']; 
	 $email = $row['email']; 
	 $fono  = $row['fon_ctt']; 
	 $tip_cna = $row['tip_cna']; 
	 $obs_ctt = $row['obs_ctt']; 
	 $adj_ctt = $row['adj_ctt'];
  }
  mssql_free_result($result1); 

  $cuerpo = formar_topmail ($id_pais, "CONTACTO");
  $cuerpo.="<td width=\"85%\">\n";

  $cuerpo.="<P align=center>\n";
  $cuerpo.="<H1 style=\"TEXT-ALIGN: center; ".$H1."\">\n";
  $cuerpo.=$rubro;
  $cuerpo.="</H1></P>\n";
  
  $cuerpo.="<P align=center>\n";
  $cuerpo.="<TABLE WIDTH=500 BORDER=0 CELLSPACING=0 CELLPADDING=1>\n";
  $cuerpo.="<TR>\n";
  $cuerpo.="<TD colspan=2 style=\"WIDTH: 500px; ".$titulo_tabla."\" align=middle>\n";
  $cuerpo.="Antecedentes del Contacto\n";
  $cuerpo.="</TD>\n";
  $cuerpo.="</TR>\n";

  $cuerpo.="<TR>\n";
  $cuerpo.="<TD style=\"WIDTH: 220px; TEXT-ALIGN: right;".$label3."\">\n";
  $cuerpo.="<STRONG>Tipo de Cliente :</STRONG>&nbsp;</TD>\n";
  $cuerpo.="<TD style=\"WIDTH: 280px; TEXT-ALIGN: left; PADDING-LEFT: 5px;".$dato3."\" valign=center>\n";
  $cuerpo.=($tip_ctt == 1 ? "Persona" : "Institucional")."</TD>\n";
  $cuerpo.="</TR>\n";

  $cuerpo.="<TR>\n";
  $cuerpo.="<TD style=\"WIDTH: 200px; TEXT-ALIGN: right;".$label33."\">\n";
  $cuerpo.="<STRONG>Rut Personal :</STRONG>&nbsp;</TD>\n";
  $cuerpo.="<TD style=\"WIDTH: 500px; TEXT-ALIGN: left; PADDING-LEFT: 5px;".$dato33."\" valign=center>\n";
  $cuerpo.=$rut_ctt."</TD>\n";
  $cuerpo.="</TR>\n";

  $cuerpo.="<TR>\n";
  $cuerpo.="<TD style=\"WIDTH: 200px; TEXT-ALIGN: right;".$label3."\">\n";
  $cuerpo.="<STRONG>Nombre Completo :</STRONG>&nbsp;</TD>\n";
  $cuerpo.="<TD style=\"WIDTH: 500px; TEXT-ALIGN: left; PADDING-LEFT: 5px;".$dato3."\" valign=center>\n";
  $cuerpo.=$nom_ctt."</TD>\n";
  $cuerpo.="</TR>\n";

  $cuerpo.="<TR>\n";
  $cuerpo.="<TR>\n";
  $cuerpo.="<TD style=\"WIDTH: 200px; TEXT-ALIGN: right;".$label33."\">\n";
  $cuerpo.="<STRONG>Institucion :</STRONG>&nbsp;</TD>\n";
  $cuerpo.="<TD style=\"WIDTH: 500px; TEXT-ALIGN: left; PADDING-LEFT: 5px;".$dato33."\" valign=center>\n";
  $cuerpo.=($tip_ctt == 1 ? "&nbsp;" : $nom_itt)."</TD>\n";
  $cuerpo.="</TR>\n";

  $cuerpo.="<TR>\n";
  $cuerpo.="<TD style=\"WIDTH: 200px; TEXT-ALIGN: right;".$label3."\">\n";
  $cuerpo.="<STRONG>e-mail :</STRONG>&nbsp;</TD>\n";
  $cuerpo.="<TD style=\"WIDTH: 500px; TEXT-ALIGN: left; PADDING-LEFT: 5px;".$dato3."\" valign=center>\n";
  $cuerpo.="<A style=\"COLOR: #3366cc; TEXT-DECORATION: underline\" HREF=mailto:".$email.">".$email."</A></TD>\n";
  $cuerpo.="</TR>\n";

  $cuerpo.="<TR>\n";
  $cuerpo.="<TR>\n";
  $cuerpo.="<TD style=\"WIDTH: 200px; TEXT-ALIGN: right;".$label33."\">\n";
  $cuerpo.="<STRONG>Fono :</STRONG>&nbsp;</TD>\n";
  $cuerpo.="<TD style=\"WIDTH: 500px; TEXT-ALIGN: left; PADDING-LEFT: 5px;".$dato33."\" valign=center>\n";
  $cuerpo.=$fono."</TD>\n";
  $cuerpo.="</TR>\n";
  
  $cuerpo.="<TR>\n";
  $cuerpo.="<TD style=\"WIDTH: 200px; TEXT-ALIGN: right;".$label3."\">\n";
  $cuerpo.="<STRONG>Tipo de Consulta :</STRONG>&nbsp;</TD>\n";
  $cuerpo.="<TD style=\"WIDTH: 500px; TEXT-ALIGN: left; PADDING-LEFT: 5px;".$dato3."\" valign=center>\n";
  $cuerpo.=$aTipCna[$tip_cna]."</TD>\n";
  $cuerpo.="</TR>\n";

  $cuerpo.="<TR>\n";
  $cuerpo.="<TD style=\"WIDTH: 200px; TEXT-ALIGN: right;".$label33."\">\n";
  $cuerpo.="<STRONG>Comentario :</STRONG>&nbsp;</TD>\n";
  $cuerpo.="<TD style=\"WIDTH: 500px; TEXT-ALIGN: left; PADDING-LEFT: 5px;".$dato33."\" valign=center>\n";
  $cuerpo.=str_replace("&#39;", "'", $obs_ctt)."</TD>\n";
  $cuerpo.="</TR>\n";

  if (trim($adj_ctt) != "") {
	  $cuerpo.="<TR>\n";
	  $cuerpo.="<TD style=\"WIDTH: 200px; TEXT-ALIGN: right;".$label3."\">\n";
	  $cuerpo.="<STRONG>Archivo Adjunto :</STRONG>&nbsp;</TD>\n";
	  $cuerpo.="<TD style=\"WIDTH: 500px; TEXT-ALIGN: left; PADDING-LEFT: 5px;".$dato3."\" valign=center>\n";
      $cuerpo.="<A style=\"COLOR: #3366cc; TEXT-DECORATION: underline\" HREF=\"".$home."/".$pathadjuntos.$adj_ctt."\">".$adj_ctt."</A></TD>\n";
	  $cuerpo.="</TR>\n";
  }

  $cuerpo.="<TR><TD style=\"".$label_top."\" colspan=2>&nbsp;</TD></TR>\n";
  $cuerpo.="</TABLE>\n";

  $cuerpo.="</td>\n";
  $cuerpo.=formar_bottommail();

  return $cuerpo;  
}

function display_login($Cod_Per, $Cod_Clt, $link, $Cod_Cot) {
	$cantidad = 0;
	
	$result = mssql_query ("vm_per_s ".$Cod_Per, $link) or die ("No se pudo leer datos del usuario");
	if ($row = mssql_fetch_array($result)) {
		$nombre	= $row["Pat_Per"]." ".$row["Mat_Per"].", ".$row["Nom_Per"];
		$cod_clt_usr = $row["Cod_Clt"];
	}
	mssql_free_result($result); 
	
	if ($Cod_Cot > 0) {
		$result = mssql_query ("vm_count_cotweb $Cod_Cot", $link) or die ("No se pudo leer datos de la cotizacion");
		if ($row = mssql_fetch_array($result)) $cantidad = $row['cantidad'];
		mssql_free_result($result); 
	}
	
	$linea ="<form ID=\"F1\" AUTOCOMPLETE=\"off\" method=\"POST\" name=\"F1\">\n";
    $linea.="<li class=\"back-verde\"><a href=\"javascript:CerrarLogin()\">Salir</a></li>\n";
    $linea.="<li class=\"back-verde\"><a href=\"#\">Devoluciones</a></li>\n";
    $linea.="<li class=\"back-verde\"><a href=\"mihistorial.php\">Historial</a></li>\n";
    $linea.="<li class=\"back-verde\"><a href=\"tracking.php\">Ordenes</a></li>\n";
    $linea.="<li class=\"back-verde\"><a href=\"miscotizaciones.php\">Cotizaciones</a></li>\n";
    $linea.="<li class=\"back-verde\"><a href=\"#\">Mensajes</a></li>\n";
    $linea.="<li class=\"back-verde\"><a href=\"micuenta.php\">Mi Cuenta</a></li>\n";
    $linea.="<li class=\"olvido\">".$nombre."</li>\n";
    $linea.="<li class=\"back-verde\">Usuario:</li>\n";
	if ($cantidad > 0) {
        $pagina = "<a rev=\"width: 750px; height: 300px; border: 0 none; scrolling: auto;\" title=\"Lista de Productos a Cotizar\" rel=\"lyteframe[cotizaciones]\" href=\"cotizaciones.php\">Shopping Bag ($cantidad)</a>";
        $linea.="<li style=\"padding-right: 20px\"><table><tr><td><img src=\"images/Shoppingbag3.png\"></td><td>".$pagina."</td></tr></table></li>\n";
	}
	//$linea.="<li class=\"back-verde\"><a href=\"#\"><img src=\"images/Shoppingbag3.png\"></a></li>\n";
	if ($cod_clt_usr != $Cod_Clt) {
		$result = mssql_query ("vm_cli_s ".$Cod_Clt, $link) or die ("No se pudo leer datos del cliente");
		if ($row = mssql_fetch_array($result)) 
			$nombre_clt	= $row["RznSoc_Per"];
		mssql_free_result($result); 
		$linea.="<li class=\"olvido\">".$nombre_clt."</a></li>\n";
		$linea.="<li class=\"back-verde\">Cliente:</li>\n";
	}
	$linea.="</form>\n";
	
	return $linea;
}

function display_mnuizq($perfil) {
	echo "<TABLE WIDTH=\"100%%\" BORDER=\"0\" CELLSPACING=\"0\" CELLPADDING=\"1\" ALIGN=\"left\">\n";
	echo "<TR><TD class=\"label_left_right_top\" STYLE=\"PADDING-LEFT:20px; PADDING-TOP:10px; TEXT-ALIGN: left\"><li>Mi Vestmed</li></TD></TR>\n";
	switch ($perfil) {
	case 1:
		echo "<TR><TD class=\"label_left_right\" STYLE=\"PADDING-LEFT:20px; PADDING-TOP:10px; TEXT-ALIGN: left\"><li>Ventas</li></TD></TR>\n";
		break;
		
	case 2:
		echo "<TR><TD class=\"label_left_right\" STYLE=\"PADDING-LEFT:20px; PADDING-TOP:10px; TEXT-ALIGN: left\"><li>Reposici&oacute;n</li></TD></TR>\n";
		break;

	case 3:
		echo "<TR><TD class=\"label_left_right\" STYLE=\"PADDING-LEFT:20px; PADDING-TOP:10px; TEXT-ALIGN: left\"><li>Compras</li></TD></TR>\n";
		break;
		
	case 4:
		echo "<TR><TD class=\"label_left_right\" STYLE=\"PADDING-LEFT:20px; PADDING-TOP:10px; TEXT-ALIGN: left\"><li><a href=\"javascript:mnuVestmed()\">Cotizaciones</a></li></TD></TR>\n";
		echo "<TR><TD class=\"label_left_right\" STYLE=\"PADDING-LEFT:20px; PADDING-TOP:10px; TEXT-ALIGN: left\"><li><a href=\"javascript:BuscarCliente('')\">Clientes</a></li></TD></TR>\n";
		break;
		
	} 
	echo "<TR><TD class=\"label_left_right\" STYLE=\"PADDING-LEFT:20px; PADDING-TOP:10px; TEXT-ALIGN: left\"><li>Despacho</li></TD></TR>\n";
	echo "<TR><TD class=\"label_left_right_bottom\" STYLE=\"PADDING-LEFT:20px; PADDING-TOP:10px; PADDING-BOTTOM:10px; TEXT-ALIGN: left\"><li>Bordados</li></TD></TR>\n";
	echo "</TABLE>\n";
}

function display_usr($UsrId, $Perfil, $link) {
	$nombre = $UsrId;
	//$result = mssql_query ("vm_per_s ".$UsrId, $link) or die ("No se pudo leer datos del usuario");
	//if ($row = mssql_fetch_array($result)) {
	//	$nombre	= $row["Pat_Per"]." ".$row["Mat_Per"].", ".$row["Nom_Per"];
	//	$cod_clt_usr = $row["Cod_Clt"];
	//}
	//mssql_free_result($result); 
        
	$linea ="<form ID=\"F1\" AUTOCOMPLETE=\"off\" method=\"POST\" name=\"F1\">\n";
    $linea.="<li class=\"back-verde\"><a href=\"javascript:CerrarLogin()\">Salir</a></li>\n";
	switch ($Perfil) {
	case 1:
		$linea.="<li class=\"back-verde\"><a href=\"historicorep.php\">Hist&oacute;rico</a></li>\n";
		$linea.="<li class=\"back-verde\"><a href=\"oreponer.php?est=all\">Ordenes de Reposiciones</a></li>\n";
		$linea.="<li class=\"back-verde\"><a href=\"oreponer.php?est=1\">Reposiciones Pendientes</a></li>\n";
		$linea.="<li class=\"back-verde\"><a href=\"mdiario.php\">Ventas a Reponer</a></li>\n";
		break;
		
	case 2:
		$linea.="<li class=\"back-verde\"><a href=\"historicorep.php\">Hist&oacute;rico</a></li>\n";
		$linea.="<li class=\"back-verde\"><a href=\"odrdiario.php\">Solicitudes de Compra</a></li>\n";
		$linea.="<li class=\"back-verde\"><a href=\"oreponer.php?est=3\">Reposiciones Realizadas</a></li>\n";
		$linea.="<li class=\"back-verde\"><a href=\"oreponer.php?est=2\">Reposiciones Vigentes</a></li>\n";
		break;
		
	case 3:
		$linea.="<li class=\"back-verde\"><a href=\"historicorep.php\">Hist&oacute;rico</a></li>\n";
		$linea.="<li class=\"back-verde\"><a href=\"ocompra.php\">Ordenes de Compra</a></li>\n";
		$linea.="<li class=\"back-verde\"><a href=\"odrdiario.php\">Solicitudes de Compra</a></li>\n";
		break;
		
	case 4:
		$linea.="<li class=\"back-verde\"><a href=\"historicorep.php\">Hist&oacute;rico</a></li>\n";
		$linea.="<li class=\"back-verde\"><a href=\"ocompra.php\">Ordenes de Compra</a></li>\n";
		$linea.="<li class=\"back-verde\"><a href=\"odrdiario.php\">Solicitudes de Compra</a></li>\n";
		break;
	}
    $linea.="<li class=\"olvido\">".$nombre."</li>\n";
    $linea.="<li class=\"back-verde\">Usuario:</li>\n";
	$linea.="</form>\n";
	
	return $linea;
}

function solicitar_login() {
	
        $linea = "<form ID=\"F1\" AUTOCOMPLETE=\"off\" method=\"POST\" name=\"F1\">\n";
		$linea.= "<li class=\"back-verde\" style=\"float:left;\"><div id=\"alerta_error\" style=\"visibility: hidden;\"></div></li>\n";
		$linea.= "<input type=\"hidden\" name=\"dfrut\" id=\"dfrut\" />\n";
		$linea.= "<li class=\"back-verde\" style=\"float:left;\">RUT</li>\n";
		$linea.= "<li class=\"back-verde inputp\" style=\"float:left;\"><input type=\"\" name=\"rut\" id=\"rut\" onblur=\"rutBlur('rut','dfrut')\" onKeyPress=\"javascript:return soloRUT(event)\" tabIndex=\"1\"></li>\n";
		$linea.= "<li class=\"back-verde\" style=\"float:left;\">CONTRASE&Ntilde;A</li>\n";
		$linea.= "<li class=\"back-verde inputp\" style=\"float:left;\"><input type=\"password\" id=\"dfclave\" name=\"dfclave\" tabIndex=\"2\" readOnly /></li>\n";
		$linea.= "<li class=\"back-verde\" style=\"float:left;\"><a href=\"javascript:ValidarLogin()\">ENTRAR</a></li>\n";
    	$linea.= "<li class=\"back-verde registro\"><a href=\"registrarse.php\">REGISTRARSE</a></li>\n";
        $linea.= "<li class=\"olvido\" ><a href=\"javascript:EnviarClave()\">OLVID&Oacute; SU CLAVE?</a></li>\n";
		$linea.= "</form>\n";
		return $linea;
}

function formatearRut($rut){
	$aRut   = split("-", $rut);
	return formatearMillones($aRut[0])."-".$aRut[1];
}

function formatearMillones($nNmb){
	$sRes = "";
	for ($j=0, $i = strlen($nNmb) - 1, $j = 0; $i >= 0; $i--, $j++)
		$sRes = substr($nNmb,$i,1).(($j > 0) && ($j % 3 == 0)? ".": "").$sRes;
	return $sRes;
}

function agregarLevelSession($link,$namelink,$level) {
	$buffer = "";
	$buffersession = split(";", $_SESSION['buffer']);
	for ($i=0; $i<min($level,count($buffersession)); $i++) $buffer.=$buffersession[$i].";";
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

function get_size_image($name, $filter, $db, &$width, &$height)
{
	//$pathadjuntos = "C:\\Windows\\Temp\\";
	$pathadjuntos = "./adjuntos/";
	$width = 0;
	$height = 0;
	$file = $pathadjuntos."paso".rand().".jpg";
	$result = mssql_query("vm_strimg_get '".$name."','".$filter."'", $db);
	if (@mssql_result($result, 0, 0)!=null) {
		$current = @mssql_result($result, 0, 0);
		file_put_contents($file, $current);
		list($width, $height, $type, $attr) = getimagesize($file);
		unlink($file);
	}
}



// --- ooooo ----

?>
