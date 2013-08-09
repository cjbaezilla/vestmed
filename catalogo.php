<?php
//Obtengo los datos de conexion de la base de datos
ini_set('display_errors', '0');
session_start();
include("config.php");
if (isset($_SESSION['CodCot'])) $cod_cot = intval($_SESSION['CodCot']);
$Cod_Per = 0;
$Cod_Clt = 0;
if (isset($HTTP_GET_VARS["idu"])) {
	$Cod_Per = intval($HTTP_GET_VARS["idu"]);
    $_SESSION['CodPer'] = $Cod_Per;     
}
else if (isset($_SESSION['CodPer'])) 
	$Cod_Per = intval($_SESSION['CodPer']);
	
if (isset($HTTP_GET_VARS["idc"])) {
	$Cod_Clt = intval($HTTP_GET_VARS["idc"]);
    $_SESSION['CodClt'] = $Cod_Clt;     
}
else if (isset($_SESSION['CodClt'])) 
	$Cod_Clt = intval($_SESSION['CodClt']);
	
function dimensionesPrd () {
	return "width:100px;";
	//return "width:100; height:auto;";
}

function dimensionesCol () {
	return "width:80px; height:80px;";
}

function dimensionesBigCol () {
	return "width:110px; height:110px;";
}

function dimensionesBlankPrd () {
	return "width:99px; height:149px;";
}

function dimensionesBlankColor () {
	return "width:80px; height:80px;";
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Catalogo - Vestmed Vestuario M&eacute;dico</title>
<link href="css/layout.css" type="text/css" rel="stylesheet" />
<link href="css/headers.css" type="text/css" rel="stylesheet" />
<script type="text/javascript" src="js/mootools-1.2.1-core-yc.js"></script>
<script type="text/javascript" src="js/mootools-1.2-more.js"></script>
<script type="text/javascript" src="dropdown/js/dropdown.js"> </script>
<link rel="stylesheet" type="text/css" media="screen" href="dropdown/css/uvumi-dropdown.css" />
<link href="css/clearfix.css" type="text/css" rel="stylesheet" />
<!--[if IE]>
<link rel="stylesheet" type="text/css" media="screen" href="dropdown/css/uvumi-dropdown-ie.css" />
<![endif]-->
<script language="JavaScript" src="Include/ValidarDataInput.js"></script>
<script language="JavaScript" src="Include/SoloNumeros.js"></script>
<script language="JavaScript" src="Include/validarRut.js"></script>
<!-- Lytebox Includes //-->
<script type="text/javascript" src="lytebox/lytebox.js"></script>
<link rel="stylesheet" type="text/css" href="lytebox/lytebox.css" media="screen" />
<link rel="stylesheet" type="text/css" href="images/catalogo1/catalogo.css" media="screen" />

<!-- Lytebox Includes //-->
<script type="text/javascript">
new UvumiDropdown('dropdown-scliente');

function EnviarClave() {
   $('F1').action = "aviso.php?idmsg=20";
  	$('F1').submit();
}
</script>
<script type="text/javascript" src="js/jquery-1.3.2.min.js"></script>
<script type="text/javascript">
    var $j = jQuery.noConflict();
	
    function enviarPaginacion(pag)
    {
        $j("#p_page").val(pag);
        $j("form#fullSearch").submit();
    }

    function enviarPaginacionColores(patron)
    {
        $j("#p_page").val(1);
        $j("#p_pat").val(patron);
        $j("form#fullSearch").attr("action","catalogo.php?advsearch=1&colorSel="+patron);
        $j("form#fullSearch").submit();
    }

    $j(document).ready(function(){
        //Filtrar grppat por naturaleza
        $j("form#searchGrppat").submit(function(){
            $j.post("ajax-search.php",{
                search_type: "nat",
                param_filter: $j("#post_nat").val()
            }, function(xml) {
                listGrpPat(xml);
            });
            return false;
        });
        //Filtrar sex por grppat y por naturaleza
        $j("form#searchSex").submit(function(){
            $j.post("ajax-search.php",{
                search_type: "sex",
                param_filter: $j("#post_nat").val(),
                param_grppat: $j("#post_grppat").val()
            }, function(xml) {
                listSex(xml);
            });
            return false;
        });
    });

    function listGrpPat(xml)
    {
        options="<select id=\"post_grppat\" name=\"post_grppat\" >\n";
        options+="<option selected value=\"\">Elija Pattern</option>\n";
        $j("filter",xml).each(function(id) {
            filter=$j("filter",xml).get(id);
            options+= "<option value=\""+$j("code",filter).text()+"\">"+$j("value",filter).text()+"</option>\n";
        });
        options+="</select>";
        $j("#post_grppat").replaceWith(options);
    }

    function filterGrpPat()
    {
        $j("form#searchGrppat").submit();
        $j("form#searchSex").submit();
    }

    function listSex(xml)
    {
        options="<select id=\"post_filter\" name=\"post_filter\" >\n";
        options+="<option selected value=\"\">Elija Sexo</option>\n";
        $j("filter",xml).each(function(id) {
            filter=$j("filter",xml).get(id);
            options+= "<option value=\""+$j("code",filter).text()+"\">"+$j("value",filter).text()+"</option>\n";
        });
        options+="</select>";
        $j("#post_filter").replaceWith(options);
    }

    function filterSex()
    {
        $j("form#searchSex").submit();
    }
	
    function ValidaFiltros()
    {
            if ($j("#post_grppat").val() != "" && $j("#post_mca").val() == "") {
                    alert("Es obligatorio seleccionar una Marca si desea filtrar por Pattern");
                    return false;
            }
            return true;
    }
	
    function buscar_style(texto){
            $('post_style').set('value', texto);
            $('form_cata').submit();
    }
	
    function MostrarOfertas(valor) {
        alert('MostrarOfertas');
        var post_oferta = document.getElementById("post_oferta");
        var form_cata = document.getElementById("form_cata");
        post_oferta.value = valor;
        form_cata.submit();
        //$('post_oferta').set('value', valor);
        //$('form_cata').submit();        
    }
    
    window.addEvent('domready', function(){
    $$('.listado-prods .opcion').addEvent('click', function(){
            var campo = this.getProperty('campo');
            var valor = this.getProperty('valor');
            if(campo == "post_grppat"){
                    //searchSex();
            }
            //alert(campo + " | " + valor)
            $(campo).set('value', valor);
            $('form_cata').submit();
    });
    $$('.quitar').addEvent('click', function()
            {
                var campo = this.getProperty('campo');
                $(campo).set('value', "");
                $('form_cata').submit();
            });
    });
    
    
</script>
</head>

<?php
//Obtengo parametros busqueda o browse
//$_SESSION['buffer'] = "";
$colsearch=false;
$p_mca = ""; $p_nat=""; $p_grppat=""; $p_sex=""; $p_style=""; $p_linmca=""; 

$p_nat=ok(($_POST['post_nat']!=null)?$_POST['post_nat']:$_GET['nat']);
$p_grppat=ok(($_POST['post_grppat']!=null)?$_POST['post_grppat']:$_GET['pattern']);
$p_sex=ok(($_POST['post_filter']!=null)?$_POST['post_filter']:$_GET['filter']);
$p_style=ok(($_POST['post_style']!=null)?$_POST['post_style']:$_GET['style']);
$p_linmca=ok(($_POST['post_linmca']!=null)?$_POST['post_linmca']:$_GET['linmca']);
$p_mca=ok(($_POST['post_mca']!=null)?$_POST['post_mca']:$_GET['mca']);
$p_page = (isset($_GET['page'])?$_GET['page']:((isset($_POST['p_page'])?$_POST['p_page']:1)));

$ofertas = (isset($_GET['oferta']) ? intval($_GET['oferta']) : 0);

$advsearch=ok(($_GET['advsearch']!=null)?true:false);
$advsearch=ok(($_GET['colsearch']!=null)?true:false);
$browsecol=ok(($_GET['browsecol']!=null)?true:false);
$header_search=ok(($_GET['id']!=null)?true:false);

$QtyOft = 0;
$query = mssql_query("vm_xisoft");
if (($row = mssql_fetch_array($query))) $QtyOft = intval($row['QtyOft']);

if ($p_mca=="" && $p_nat=="" && $p_grppat=="" && $p_sex=="" && $p_linmca=="" && !$advsearch && !$colsearch) agregarLevelSession("catalogo.php", "Catalogo", 0);
?>

<body>
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
        <div id="catalogo-selected" class="selected">catalogo</div>
        <a id="contacto" href="contacto.htm">contacto</a>
  
  	</div>
	<?php 
		if ($Cod_Per == 0) { 
	?>
    <ul id="usuario_registro">
		<?php echo solicitar_login(); ?>
    </ul>
	<?php }
		  else {
	?>
    <ul id="usuario_registro">
		<?php 	echo display_login($Cod_Per, $Cod_Clt, $db, $cod_cot); ?>
    </ul>
	
	<?php 
		}
	?>
<script language="javascript">
	var f1;	
	f1 = document.F1;	
</script>
	<div id="content-catalogo" class="clearfix" style="width:930px; padding:5px; margin:5px auto; background:#fff;">
		<div id="menu-left" style="width:156px; float:left; border:1px solid #e9e9e9; padding:2px;">
			<form style="display:none;" id="searchGrppat">
                        </form>
        <form id="searchSex" style="display:none;">
        </form>
        <form name="input" action="catalogo.php" method="post" id="form_cata" onsubmit="return ValidaFiltros();" style="margin:0; padding:0;">
            <input type="hidden" name="post_style" id="post_style" />
            <a class="adv-search" href="busqueda-avanzada.php">Advanced Search</a>
            <?php if ($QtyOft > 0) { ?>
            <h2 style="margin:5px 0 5px 0; "><a href="catalogo.php?oferta=1"><img src="images/catalogo1/Ofertas_VM_Arial_2.png" /></a></h2>
            <?php } ?>
            <h2 style="margin:5px 0 5px 0; "><img src="images/catalogo1/productos.gif" /></h2>
            <div>
            <?php
            if($p_nat != "" || $p_grppat != "" || $p_sex != "" || $p_mca !="" || $p_linmca != ""){
            ?>
            <div style="margin:2px 0;"><img src="images/catalogo1/active.gif" /></div>
            <?php
            }
            ?>
            <?php
            //Obtener naturalezas
            $nivel=0;
            $link = "catalogo.php";
            $sep = "?";
            $sp_0 = mssql_query("vm_linmca_s", $db);
            while($row = mssql_fetch_array($sp_0)) {
                if ($row['Cod_LinMca']==$p_linmca) {
                //
            ?>
            <div campo="post_linmca" class="quitar"><?php echo $row['Des_LinMca']; ?></div>
            <?php
                    $link.=($sep."linmca=".$p_linmca);
                    $sep = "&";
                    agregarLevelSession($link, $row['Des_LinMca'], ++$nivel);
                    break;
                }
            }
            ?>
			
            <?php
            //Obtener naturalezas
            $sp_1 = mssql_query("vm_nat_cmb_full", $db);
            while($row = mssql_fetch_array($sp_1)){
            	if($row['code']==$p_nat){
           ?>
                <div campo="post_nat" class="quitar"><?php echo $row['opcion']; ?></div>
           <?php
                    $link.=($sep."nat=".$p_nat);
                    $sep = "&";
                    agregarLevelSession($link, $row['opcion'], ++$nivel);
                    break;
                }
            }
            ?>
            
            <?php
            //Obtener patrones
            $sp_2 = mssql_query("vm_grppat_cmb_full", $db);
            while($row = mssql_fetch_array($sp_2)){
            	if($row['code']==$p_grppat){
                ?>
               <div campo="post_grppat" class="quitar"><?php echo $row['opcion']; ?></div>
                <?php
                    $link.=($sep."pattern=".$p_grppat);
                    $sep = "&";
                    agregarLevelSession($link, $row['opcion'], ++$nivel);
                    break;
                }
            }
            ?>
            
            <?php
            //Obtener sexos
            $sp_3 = mssql_query("vm_sex_cmb_full", $db);
            while($row = mssql_fetch_array($sp_3)){
                if($row['code']==$p_sex){
                ?>
               <div campo="post_filter" class="quitar"><?php echo $row['opcion']; ?></div>
               <?php
                    $link.=($sep."filter=".$p_sex);
                    $sep = "&";
                    agregarLevelSession($link, $row['opcion'], ++$nivel);
                    break;
               }
            }
            ?>
            
            <?php
            //Obtener naturalezas
            $sp_1 = mssql_query("vm_mca_s", $db);
            while($row = mssql_fetch_array($sp_1)){
                if ($row['Cod_Mca']==$p_mca) {
                //
                ?>
                <div campo="post_mca" class="quitar"><?php echo $row['Cod_Mca']; ?></div>
                <?php
                        $link.=($sep."mca=".$p_mca);
                        $sep = "&";
                        agregarLevelSession($link, $row['Cod_Mca'], ++$nivel);
                        break;
                }
            }
            ?>
            
			</div>
			<?php
			if($p_nat != "" || $p_grppat != "" || $p_sex != "" || $p_mca !="" || $p_linmca != ""){
			?>
			<a href="catalogo.php" style="margin-bottom:5px;"><img src="images/catalogo1/reset.gif" /></a>
			<?php
			}
			?>
			
			<h3 class="titulo-prods">CATEGOR&Iacute;A</h3>
			<input type="hidden" name="post_nat" id="post_nat" value="<?php echo $p_nat; ?>" />
			<?php if ($p_nat == "") { ?>
			<ul class="listado-prods">
			<?php
                        //Obtener naturalezas
                        $sp_1 = mssql_query("vm_nat_cmb_full '$p_mca', '$p_linmca', '$p_nat', '$p_sex', '$p_grppat', '$p_style'", $db);
                        while($row = mssql_fetch_array($sp_1)){
                            if($row['code']!=$p_nat And $row['Total'] > 0){
                            ?>
                            <li><a class="opcion" campo="post_nat" valor="<?php echo $row["code"];?>"><?php echo $row['opcion']; ?></a> (<?php echo $row['Total']; ?>)</li>
                            <?php
                            }
                        }
                        ?>
			</ul>
			<?php } ?>
			<h3 class="titulo-prods">COLOR / ESTAMPADO</h3>
			<input type="hidden" name="post_grppat" id="post_grppat" value="<?php echo $p_grppat; ?>" />
			<?php if ($p_grppat == "") { ?>
			<ul class="listado-prods">
			<?php
            //Obtener patrones
            $sp_2 = mssql_query("vm_grppat_cmb_full '$p_mca', '$p_linmca', '$p_nat', '$p_sex', '$p_grppat', '$p_style'", $db);
            while($row = mssql_fetch_array($sp_2)){
            	if($row['code']!=$p_grppat And $row['Total'] > 0){
                ?>
                <li><a class="opcion" campo="post_grppat" valor="<?php echo $row["code"];?>"><?php echo $row['opcion'] ?></a> (<?php echo $row['Total']; ?>)</li>
                <?php
                }
            }
            ?>
			</ul>
			<?php } ?>
			<h3 class="titulo-prods">SEXO</h3>
			<input type="hidden" name="post_filter" id="post_filter" value="<?php echo $p_sex; ?>">
			<?php if ($p_sex == "") { ?>
			<ul class="listado-prods">
			<?php
            //Obtener sexos
            $sp_3 = mssql_query("vm_sex_cmb_full '$p_mca', '$p_linmca', '$p_nat', '$p_sex', '$p_grppat', '$p_style'", $db);
            while($row = mssql_fetch_array($sp_3)){
            	if($row["code"]!=$p_sex And $row['Total'] > 0){
                ?>
               <li><a class="opcion" campo="post_filter" valor="<?php echo $row["code"];?>"><?php echo $row['opcion'] ?></a> (<?php echo $row['Total']; ?>)</li>
                <?php
                }
            }
            ?>
			</ul>
			<?php } ?>
			<h3 class="titulo-prods">MARCA</h3>
			<input type="hidden" name="post_mca" id="post_mca" value="<?php echo $p_mca;?>">
			<?php if ($p_mca == "") { ?>
			<ul class="listado-prods">
			<?php
            //Obtener naturalezas
            $sp_1 = mssql_query("vm_mca_cmb_full '$p_mca', '$p_linmca', '$p_nat', '$p_sex', '$p_grppat', '$p_style'", $db);
            $num_ros = mssql_num_rows($sp_1);
            $tantos = 1;
            while($row = mssql_fetch_array($sp_1)){
                if ($row['Hab_Mca'] == 0) {
                if ($row['Cod_Mca']!=$p_mca And $row['Total'] > 0) {
                $tantos++;
                //$row['Cod_Mca']==$p_mca
                ?>
                <li <?php if($tantos == $num_ros) echo "class=\"last\"";?>><a class="opcion" campo="post_mca" valor="<?php echo $row["Cod_Mca"];?>"><?php echo $row['Cod_Mca']; ?></a> (<?php echo $row['Total']; ?>)</li>
                
                <?php
				}
				}
            }
            ?>
			</ul>
			<?php } ?>
		</form>
		</div>
		
		<div id="el-catalogo" style="float:right; width:760px;">
			<div style="border:1px solid #e9e9e9;margin-bottom: 5px;">
				<div id="top-titulo">
					<div class="barra-sup" style="float:left; width:400px;">
					
					
					<?php
                        //Si no hay productos filtrados, se seleccionan 8 al azar:
                        if ($ofertas == 1)
                            $result = mssql_query("vm_strinv_prodcat_oft",$db);
                        else if(($p_mca=="" && $p_linmca=="" && $p_nat=="" && $p_sex=="" && $p_grppat=="" && $p_style=="")&&(!$advsearch)&&(!$colsearch)) 
                            $result = mssql_query("vm_strinv_prodcat",$db);
                        else
                            if($advsearch){
                                //busqueda avanzada
                                $p_mca = ok($_POST['p_mca']);
                                //Si se esta buscando por advsearch, linmca es POST.
                                //Si se esta buscando por el listado de la derecha, linmca es GET
                                $p_linmca = ok($_POST['p_linmca']);
                                $p_linmca = (isset($_GET['id'])?ok($_GET['id']):$p_linmca);
                                $p_nat = ok($_POST['p_nat']);   
                                $p_grppat = (isset($_POST['p_grppat']))?ok($_POST['p_grppat']):ok($_GET['pattern']);
                                $p_pat = (isset($_POST['p_pat']))?ok($_POST['p_pat']):ok($_GET['colorSel']);   
                                $p_sex = ok($_POST['p_sex']);
                                $p_mat = ok($_POST['p_mat']);   
                                $kw_codsty = ok($_POST['p_keywords']);
                                $kw_nomgrpprd = ok($_POST['p_keywords']);
                                //$query1 = "vm_advsearch '".$p_mca."','".$p_linmca."','".$p_nat."','".$p_grppat."','".$p_pat."','".$p_sex."','".$p_mat."','".$kw_codsty."','".$kw_nomgrpprd."'";
                                $result = mssql_query("vm_advsearch '".$p_mca."','".$p_linmca."','".$p_nat."','".$p_grppat."','".$p_pat."','".$p_sex."','".$p_mat."','".$kw_codsty."','".$kw_nomgrpprd."'",$db);
                                if (!isset($_GET['colorSel']))	{
                                    $link = "catalogo.php?advsearch=1&id=$p_linmca&nat=$p_nat&pattern=$grppat&filter=$p_sex";
                                    $namelink = "Busqueda Avanzada";
                                    agregarLevelSession($link, $namelink, 1);
                                    //echo $namelink;
                                }
                                else {
                                    $p_pat = ok($_GET['colorSel']);
                                    $query = mssql_query("vm_pat_s '".$p_pat."'",$db);
                                    if(($row = mssql_fetch_array($query))) {
                                        $Key_Pat = $row['Key_Pat'];	
                                        $Des_Pat = $row['Des_Pat'];	
                                        $Cod_GrpPat = $row['Cod_GrpPat'];
                                        $link = "catalogo.php?advsearch=1&colorSel=$p_pat&pattern=$p_grppat";
                                        $namelink = $Key_Pat." ".$Des_Pat;
                                        agregarLevelSession($link, $namelink, 2);
                                        //echo $namelink;
                                    }
                                }
							
                            }else if($colsearch and false){
                                $p_mca = (isset($_POST['p_mca']))?ok($_POST['p_mca']):ok($_GET['mca']);   
                                $p_grppat = (isset($_POST['p_grppat']))?ok($_POST['p_grppat']):ok($_GET['pattern']);
                                $p_mat = (isset($_POST['p_mat']))?ok($_POST['p_mat']):ok($_GET['mat']);
                                $namelink = "Color / ";
                                if ($p_mca != "") $namelink.=trim($p_mca)." ";
                                if ($p_grppat != "") {
                                    $query = mssql_query("vm_grppat_s $p_grppat",$db);
                                    if(($row = mssql_fetch_array($query))) $namelink.=trim($row['Des_GrpPat'])." / ";
                                    mssql_free_result($query);
                                }
                                if ($p_mat != "") {
                                    $query = mssql_query("vm_mat_s $p_mat",$db);
                                    if(($row = mssql_fetch_array($query))) $namelink.=trim($row['Des_Mat'])." / ";
                                    mssql_free_result($query);
                                }
                                $link = "catalogo.php?colsearch=1&mca=$p_mca&pattern=$p_grppat&mat=$p_mat";
                                agregarLevelSession($link, $namelink, 1);
                                //echo $namelink;
								
                                $result = mssql_query("vm_colsearch '".$p_mca."','".$p_grppat."','".$p_mat."'",$db);
                            }else if($header_search){
							
                            }else{
                                //busqueda con filtrado
                                $nivel = 1;
                                $namelink = "";
                                if ($p_mca != "") {
                                    $query = mssql_query("vm_mca_s '$p_mca'",$db);
                                    if(($row = mssql_fetch_array($query))) $namelink = trim($row['Cod_Mca']);
                                    //agregarLevelSession($link, $namelink, ++$nivel);
                                    mssql_free_result($query);
                                }
                                if ($p_linmca != "") {
                                    $query = mssql_query("vm_linmca_s '$p_linmca'",$db);
                                    if(($row = mssql_fetch_array($query))) $namelink = trim($row['Des_LinMca']);
                                    //agregarLevelSession($link, $namelink, ++$nivel);
                                    mssql_free_result($query);
                                }
                                if ($p_nat != "") {
                                    $query = mssql_query("vm_nat_s '$p_nat'",$db);
                                    if(($row = mssql_fetch_array($query))) $namelink =trim($row['Des_Nat']);
                                    //agregarLevelSession($link, $namelink, ++$nivel);
                                    mssql_free_result($query);
                                }
                                if ($p_grppat != "") {
                                    $query = mssql_query("vm_grppat_s '$p_grppat'",$db);
                                    if(($row = mssql_fetch_array($query))) $namelink = trim($row['Des_GrpPat']);
                                    //agregarLevelSession($link, $namelink, ++$nivel);
                                    mssql_free_result($query);
                                }
                                if ($p_sex != "") {
                                    $query = mssql_query("vm_sex_s '$p_sex'",$db);
                                    if(($row = mssql_fetch_array($query))) $namelink = trim($row['Des_Sex']);
                                    //agregarLevelSession($link, $namelink, ++$nivel);
                                    mssql_free_result($query);
                                }
                                if ($p_style != "") $namelink.=$p_style;

                                if($p_grppat!="" && $p_nat=="" && $p_sex=="" && false) {
                                        // Cambiamos a busqueda por colores
                                        $link = "catalogo.php?mca=$p_mca&pattern=$p_grppat";
                                        //$query = "vm_colsearch '".$p_mca."','".$p_grppat."','".$p_mat."'";
                                        $result= mssql_query("vm_colsearch '".$p_mca."','".$p_grppat."','".$p_mat."'",$db);
                                        //$colsearch=true;
                                } else {
                                        $link = "catalogo.php?mca=$p_mca&linmca=$p_linmca&nat=$p_nat&pattern=$p_grppat&filter=$p_sex&style=$p_style";
                                        //$query = "vm_strinv_prodcat '$p_mca','$p_linmca','$p_nat','$p_sex','$p_grppat','$p_style'";
                                        //echo $query;
                                        $result = mssql_query("vm_strinv_prodcat '$p_mca','$p_linmca','$p_nat','$p_sex','$p_grppat','$p_style'",$db);
				}
                            }
                            $num = mssql_num_rows($result);
                            DisplayLevelSession("");
						
                            ?>
					</div>
					<div style="float:right; margin-top:7px;width:225px; position:relative;">
                                        <input type="text" class="text-search" id="texto-busqueda" />
                                        <input type="button" class="button-search" onclick="buscar_style($('texto-busqueda').get('value'));">
                                        </div> 
				</div>
				<div id="top-paginacion" style="position:relative;">
				<a style="position:absolute; bottom:1px; right:1px;" href="catalogo/ayudabus.htm" rel="lyteframe[ayuda2]" title="Ayuda" rev="width: 740px; height: 500px; border: 0 none; scrolling: auto;">+ Necesita Ayuda</a>
					 <?php
                        $p_page = ($p_page!=null)?$p_page:1;
                        $page_temp = ($p_page>1)?($p_page-1):1;
                        $pagina_actual = $page_temp;
                        //Permitir paginacion con advanced search
                        if($advsearch)
                        {
                        ?>
                            <form id="fullSearch" action="catalogo.php?advsearch=1<?php echo (isset($_GET['header'])?('&header='.$_GET['header'].'&id='.$p_linmca):'') ?>" method="post">
                                <input type="hidden" id="p_mca" name="p_mca" value="<?php echo $p_mca ?>" />
                                <input type="hidden" id="p_linmca" name="p_linmca" value="<?php echo $p_linmca ?>" />
                                <input type="hidden" id="p_nat" name="p_nat"  value="<?php echo $p_nat ?>" />
                                <input type="hidden" id="p_grppat" name="p_grppat"  value="<?php echo $p_grppat ?>" />
                                <input type="hidden" id="p_pat" name="p_pat"  value="<?php echo $p_pat ?>" />
                                <input type="hidden" id="p_sex" name="p_sex"  value="<?php echo $p_sex ?>" />
                                <input type="hidden" id="p_mat" name="p_mat"  value="<?php echo $p_mat ?>" />
                                <input type="hidden" id="p_keywords" name="p_keywords" value="<?php echo $kw_nomgrpprd ?>" />
                                <input type="hidden" id="p_page" name="p_page" />
                        <?php
                        }else if($colsearch){
                        ?>
                            <form id="fullSearch" action="catalogo.php?colsearch=1" method="post">
                                <input type="hidden" id="p_mca" name="p_mca" value="<?php echo $p_mca ?>" />
                                <input type="hidden" id="p_linmca" name="p_linmca" value="<?php echo $p_linmca ?>" />
                                <input type="hidden" id="p_nat" name="p_nat"  value="<?php echo $p_nat ?>" />
                                <input type="hidden" id="p_grppat" name="p_grppat"  value="<?php echo $p_grppat ?>" />
                                <input type="hidden" id="p_pat" name="p_pat"  value="<?php echo $p_pat ?>" />
                                <input type="hidden" id="p_sex" name="p_sex"  value="<?php echo $p_sex ?>" />
                                <input type="hidden" id="p_mat" name="p_mat"  value="<?php echo $p_mat ?>" />
                                <input type="hidden" id="p_keywords" name="p_keywords" value="<?php echo $kw_nomgrpprd ?>" />
                                <input type="hidden" id="p_page" name="p_page" />
                        <?php
                        }
                        $limite = 25;
                        if($p_page == 0) $limite = $num;
                        $nupaginas = ceil($num/$limite);
                        $numpagina = ($num<$limite)?$num:$limite;
                        if($num > 0){
                        ?>
					<span style="float:left; margin-left:10px;margin-top:9px;">Viendo <?php echo (($page_temp*$num)-$num+1);?> a <?php echo (($page_temp*$num)-$num+$numpagina);?> de <?php echo $num;?> productos</span>
					<span style="float:left; margin-left:50px;">
					
					<span style="float:left;margin-top:9px;">P&aacute;ginas: </span>
					<ul id="pagination">
                        <?php

                        $largotitle = 17;
                        $i_max = ceil($num/$limite);
                        for($i=1;$i<=$i_max;$i++)
                        {
                            $page_temp = $i;
                            ?>
                            <li><a <?php if($i==$p_page && $p_page != 0) echo "class=\"sel\"";?> <?php if($advsearch||$colsearch) echo "onclick=\"enviarPaginacion($page_temp)\"";else echo " href=\"catalogo.php?mca=$p_mca&nat=$p_nat&filter=$p_sex&pattern=$p_grppat&page=$page_temp\""; ?>><?php echo $page_temp ?></a></li>
                            <?php
                            //if($i<$i_max)
                                //echo "&nbsp;&nbsp;";
                        }
                        $page_temp=($p_page<$i_max)?($p_page+1):$i_max;
                   
                        ?>
                    <?php
                    if($pagina_actual < $numpaginas){
                    $pagina_sig = $pagina_actual+1;
                    ?>
                   <li style="width:70px;">
                    <a <?php if($i==$p_page && $p_page != 0) echo "class=\"sel\"";?> <?php if($advsearch||$colsearch) echo "onclick=\"enviarPaginacion($pagina_sig)\"";else echo " href=\"catalogo.php?mca=$p_mca&nat=$p_nat&filter=$p_sex&pattern=$p_grppat&page=$pagina_sig\""; ?>>Siguiente</a></li>
                    <?php
                    }
                    if($num > $limite){
                    ?>
                    
                    <li style="width:70px;"><a <?php if($p_page==0) echo "class=\"sel\"";?> <?php if($advsearch||$colsearch) echo "onclick=\"enviarPaginacion(0)\"";else echo " href=\"catalogo.php?mca=$p_mca&nat=$p_nat&filter=$p_sex&pattern=$p_grppat&page=0\""; ?>>Ver Todos</a></li>
					</span>
					<?php
					}
					if($p_page == 0){
					?>
					<li style="width:70px;">
                    <a <?php if($advsearch||$colsearch) echo "onclick=\"enviarPaginacion(1)\"";else echo " href=\"catalogo.php?mca=$p_mca&nat=$p_nat&filter=$p_sex&pattern=$p_grppat&page=1\""; ?>>Siguiente</a></li>
					<?php
					}
					}
					?>
					</ul>
				</div>
			</div>
			<?php
			if ($advsearch && isset($_GET['colorSel']) && false) {
				echo "<table>";
				echo "<TR style=\"height:117px;\"><TD colspan=\"1\" style=\"text-align:left; PADDING-LEFT: 14px; padding-top:10px;\">\n";
                                echo "<img src=\"".printimg_addr("img_pattern",$p_pat)."\" style=\"".dimensionesBigCol()."\" class=\"cursor image-producto\" /></TD>\n";
				echo "<TD colspan=\"4\" style=\"text-align:left; text-valign:bottom;padding-top:10px;\">\n";
				echo "<p class=\"titulo-producto\" STYLE=\"margin:0;\"	>Productos en ".$Key_Pat." (".$Des_Pat.").</p>";
				echo "<p class=\"descripcion-producto\" style=\"TEXT-ALIGN: left\">El color seleccionado est&aacute; disponible en los siguientes productos. Click sobre la imagen para ver en detalle el producto.</p>\n";
				echo "</TD>\n";
				echo "</TR>\n";
				echo "</table>";
			}
			?>
			<form>
					
			<ul class="productos-res">
				<?php
				if($row = mssql_fetch_array($result)){
                                //Saltarme los elementos de las paginas anteriores
                                if($p_page != null && $p_page<100)
                                    if($p_page == 0) $totlai = 0;
                                    else  $totlai = ($p_page-1)*$limite;
                                for($j=0;$j<$totlai;$j++) $row = mssql_fetch_array($result);



                    for($i=0; $i<$limite; $i++){
                        if($row[0]==null)break;
                        if($colsearch)
                        {
                            $pat_cod = $row['codpat'];
                            $pat_key = $row['keypat'];
                            $pat_des = $row['despat'];
                            $titlelarge=stripslashes($pat_des);
                            $titlelarge2 = split(':',$titlelarge);
                            $title =  $titlelarge2[1];
                            ?>
                            <li>
                            <a onclick="enviarPaginacionColores(<?php echo $pat_cod ?>)">
                            <img src="<?php echo printimg_addr("img_pattern",$pat_cod) ?>" title="<?php echo $titlelarge; ?>" class="cursor image-color" style="<?php echo dimensionesCol(); ?>" />
                            </a>
                            <div class="descripcion-producto"><?php echo $title ?></div>
                            <div class="style-producto">Key #<?php echo $pat_key ?></div>
                            </li>
                            <?php
                        }
                        else
                        {
                            $dsg_marca=$row["dsg_marca"];
                            $grp_id=$row["grpprd_id"];
                            $grp_title=$row["grpprd_title"];
                            $grp_status=$row["grpprd_status"];
                            $dsg_style=$row["style"];
                            $titlelarge=stripslashes($grp_title);
                           /* $title=$titlelarge;
                           
                                if(strlen($titlelarge)>$largotitle){
                                $title=substr($titlelarge,0,$largotitle);
                                $title.="...";
                            }

							else
                            */ 
							$posblanco = strpos($titlelarge, " ");
							$title =  substr(trim($titlelarge), $posblanco+1);
							//$title = str_replace("&#39;", "'", $title);
							$title = str_replace("#", "'", $title);
                            ?>
                            <li>
                            
                            <div class="imagen">
                                <?php if ($row['QtyOft'] > 0) { ?>
                                <div style="position: absolute; background: url(images/catalogo1/PRD_40px.png); width: 42px; height: 42px;"></div>
                                <?php } ?>
                                <img src="<?php echo printimg_addr("img1_grupo",$grp_id) ?>" width="140" title="<?php echo $titlelarge; ?>" class=" image-producto" />
                            </div>

                            
                            <div class="prodb">
                            	<p><?php echo $dsg_style." ".$title ?></p>
                            	<?php
                                if($_GET['colorSel']!=null)
                                {
                                    $p_pat = ok($_GET['colorSel']);
                                    $pat_result = mssql_query("vm_pat_s '".$p_pat."'",$db);
                                    $pat_row = mssql_fetch_array($pat_result);
                                    ?>
                                    <a href="detalle-producto.php?producto=<?php echo $grp_id."&pagina=".$p_page; ?><?php echo "&colorSel=".$_GET['colorSel']."&desc=".$pat_row['Key_Pat']."_BR_".$pat_row['Des_Pat'] ?>"><img src="images/catalogo1/ver.gif" /></a>
                                    <?php
                                }
                                else
                                {
                                    ?>
                                    <a href="detalle-producto.php?producto=<?php echo $grp_id."&pagina=".$p_page; ?>"><img src="images/catalogo1/ver.gif" /></a>
                                    <?php
                                }
                            ?>
                            </div>

                            </li>
                            <?php
                        }
                        $row = mssql_fetch_array($result);
                    }
					?>
					</li>
					<?php

					if($row[0]==null)break;
                    }
            ?>
			</ul>
		</div>
	</div>
	
	
    <div id="footer"></div>
</div>
<script type="text/javascript">
<?php echo "// ".$_SESSION['buffer']; ?>
</script>

</body>
</html>