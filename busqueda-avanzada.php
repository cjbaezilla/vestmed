<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Busqueda Avanzada - Vestmed Vestuario Médico</title>
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
<script type="text/javascript" src="js/jquery-1.3.2.min.js"></script>
<script type="text/javascript">
    var $j = jQuery.noConflict();
    $j(document).ready(function(){
        //Filtrar lineas por marca
        $j("form#searchMca").submit(function(){
            $j.post("ajax-search.php",{
                search_type: "mca",
                param_filter: $j("#mca").val()
            }, function(xml) {
                listLinMca(xml);
            });
            return false;
        });
        //Filtrar los patrones por marca y grupo de patron
        $j("form#searchGrpPat").submit(function(){
            $j.post("ajax-search.php",{
                search_type: "grppat",
                param_filter: $j("#mca").val(),
                param_grppat: $j("#grppat").val()
            }, function(xml) {
                listGrpPat(xml);
            });
            return false;
        });
    });

    function filterMca()
    {
        $j("form#searchMca").submit();
    }

    function filterGrpPat()
    {
        $j("form#searchGrpPat").submit();
    }

    function listLinMca(xml)
    {
        options="<select id=\"linmca\" name=\"linmca\" >\n";
        options+="<option selected value=\"_ALL\">Todas las lineas</option>\n";
        $j("filter",xml).each(function(id) {
            filter=$j("filter",xml).get(id);
            options+= "<option value=\""+$j("code",filter).text()+"\">"+$j("value",filter).text()+"</option>\n";

        });
        options+="</select>";
        $j("#linmca").replaceWith(options);
    }

    function listGrpPat(xml)
    {
        options="<select id=\"pat\" name=\"pat\" >\n";
        options+="<option selected value=\"_ALL\">Todos los colores</option>\n";
        $j("filter",xml).each(function(id) {
            filter=$j("filter",xml).get(id);
            options+= "<option value=\""+$j("code",filter).text()+"\">"+$j("value",filter).text()+"</option>\n";

        });
        options+="</select>";
        $j("#pat").replaceWith(options);
    }

    function setSearchValues()
    {
        $j("#p_mca").val(    (   $j("#mca").val()     !='_ALL')?$j("#mca").val()   : '');
        $j("#p_linmca").val( (   $j("#linmca").val()  !='_ALL')?$j("#linmca").val(): '');
        $j("#p_nat").val(    (   $j("#nat").val()     !='_ALL')?$j("#nat").val()   : '');
        $j("#p_grppat").val( (   $j("#grppat").val()  !='_ALL')?$j("#grppat").val(): '');
        $j("#p_sex").val(    (   $j("#sex").val()     !='_ALL')?$j("#sex").val()   : '');
        $j("#p_mat").val(    (   $j("#mat").val()     !='_ALL')?$j("#mat").val()   : '');
        $j("#p_keywords").val($j("#keywords").val());
        $j("form#fullSearch").submit();
    }

    function setColSearchValues()
    {
        $j("#p_mca_col").val(    (   $j("#col_mca").val()     !='_ALL')?$j("#col_mca").val()   : '');
        $j("#p_grppat_col").val( (   $j("#col_grppat").val()  !='_ALL')?$j("#col_grppat").val(): '');
        $j("#p_mat_col").val(    (   $j("#col_mat").val()     !='_ALL')?$j("#col_mat").val()   : '');
        return true;
    }

</script>
</head>

<?php
//Obtengo los datos de conexion de la base de datos
ini_set('display_errors', '0');
session_start();
include("config.php");
?>

<body>
<div id="body">
	<div id="header"></div>
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
        <div id="catalogo-selected" class="selected"><a id="catalogo2" href="catalogo.php">catalogo</a></div>
        <a id="contacto" href="contacto.htm">contacto</a>
   
  	</div>
    <div id="work">
    	
    	<div id="back-busqueda"><a style="right:20px;" class="volver-catalogo" href="catalogo.php">Volver</a>
        	<div id="text-busqueda">Para buscar un producto usted debe seleccionar la palabra/s claves sobre el vestuario que requiere.<br/> 
Puede hacer búsquedas globales por tipo de prenda, ejemplo: Pantalones, Delantales, etc.</div>
		  <ul id="filtros">
                <form id="searchKeywords">
                    <li>
                        <span>PALABRAS CLAVES</span>
                        <input id="keywords" name="keywords" style="width:324px;" type="text" />
                    </li>
                </form>
                <form id="searchMca">
                    <li>
                        <span>MARCA</span>
                        <select id="mca" name="mca" onChange="filterMca()">
                            <option selected value="_ALL">Todas las marcas</option>
                            <?php //Seleccionar las marcas
                            $sp = mssql_query("vm_mca_cmb 0",$db);
                            while($row = mssql_fetch_array($sp))
                            {
                                ?>
                                <option value="<?php echo $row['Cod_Mca'] ?>"><?php echo $row['Cod_Mca'] ?></option>
                                <?php
                            }
                            ?>
                        </select>
                    </li>
                </form>
                <form id="searchLinMca">
                    <li>
                        <span>LINEA</span>
                        <select id="linmca" name="linmca" >
                            <option selected value="_ALL">Todas las lineas</option>
                            <?php //Seleccionar las lineas
                            $sp = mssql_query("vm_linmca_s '', '', 0",$db);
                            while($row = mssql_fetch_array($sp))
                            {
                                ?>
                                <option value="<?php echo $row[1] ?>"><?php echo $row[2] ?></option>
                                <?php
                            }
                            ?>
                        </select>
                    </li>
                </form>
                <form id="searchNat">
                    <li>
                        <span>VESTUARIO</span>
                        <select id="nat" name="nat" >
                            <option selected value="_ALL">Todos los tipos de vestuario</option>
                            <?php //Seleccionar las naturalezas
                            $sp = mssql_query("vm_nat_cmb_full",$db);
                            while($row = mssql_fetch_array($sp))
                            {
                                ?>
                                <option value="<?php echo $row[1] ?>"><?php echo $row[0] ?></option>
                                <?php
                            }
                            ?>
                        </select>
                    </li>
                </form>
                <form id="searchSex">
                    <li>
                        <span>SEXO</span>
                        <select id="sex" name="sex" >
                            <option selected value="_ALL">Todos los sexos</option>
                            <?php //Seleccionar los sexos
                            $sp = mssql_query("vm_sex_cmb_full",$db);
                            while($row = mssql_fetch_array($sp))
                            {
                                ?>
                                <option value="<?php echo $row[1] ?>"><?php echo $row[0] ?></option>
                                <?php
                            }
                            ?>
                        </select>
                    </li>
                </form>
                <form id="searchMat">
                    <li>
                        <span>MATERIAL</span>
                        <select id="mat" name="mat" >
                            <option selected value="_ALL">Todos los materiales</option>
                            <?php //Seleccionar los materiales
                            $sp = mssql_query("vm_mat_cmb",$db);
                            while($row = mssql_fetch_array($sp))
                            {
                                ?>
                                <option value="<?php echo $row[1] ?>"><?php echo $row[0] ?></option>
                                <?php
                            }
                            ?>
                        </select>
                    </li>
                </form>
                <form id="fullSearch" action="catalogo.php?advsearch=1" method="post">
                    <li>
                    <span>BUSCAR POR ESTILO</span>
                    <select id="sty" name="sty" >
                    <option selected value="_ALL">Todos los estilos</option>
                  
                     </select>
                    <input type="hidden" id="p_mca" name="p_mca" />
                    <input type="hidden" id="p_linmca" name="p_linmca" />
                    <input type="hidden" id="p_nat" name="p_nat" />
                    <input type="hidden" id="p_grppat" name="p_grppat" />
                    <input type="hidden" id="p_sex" name="p_sex" />
                    <input type="hidden" id="p_mat" name="p_mat" />
                    <input type="hidden" id="p_keywords" name="p_keywords" />
                   
                    </li>
                    
                </form>
                <li>
                	 <input  id="btn_buscar_estilo" onclick="setSearchValues()" type="button"  value="" />
                </li>
                <form id="colorLabel">
                    <li><b>B&Uacute;SQUEDA POR COLOR:</b>
                   <!-- <select id="color" name="color" >
                            <option selected value="_ALL">Todas las colores</option>
                            
                        </select>--></li>
                </form>
                <form id="searchColMca">
                    <li>
                        <span>MARCA</span>
                        <select id="col_mca" name="col_mca" >
                            <option selected value="_ALL">Todas las marcas</option>
                            <?php //Seleccionar las marcas
                            $sp = mssql_query("vm_mca_cmb 0",$db);
                            while($row = mssql_fetch_array($sp))
                            {
                                ?>
                                <option value="<?php echo $row['Cod_Mca'] ?>"><?php echo $row['Cod_Mca'] ?></option>
                                <?php
                            }
                            ?>
                        </select>
                    </li>
                </form>
                <form id="searchColGrpPat">
                    <li>
                        <span>GRUPO DE COLORES</span>
                        <select id="col_grppat" name="col_grppat">
                            <option selected value="_ALL">Todos los grupos de colores</option>
                            <?php //Seleccionar los grupos de patrones
                            $sp = mssql_query("vm_grppat_cmb_full",$db);
                            while($row = mssql_fetch_array($sp))
                            {
                                ?>
                                <option value="<?php echo $row[1] ?>"><?php echo $row[0] ?></option>
                                <?php
                            }
                            ?>
                        </select>
                    </li>
                </form>
                <form id="searchColMat">
                    <li>
                        <span>MATERIAL</span>
                        <select id="col_mat" name="col_mat" >
                            <option selected value="_ALL">Todos los materiales</option>
                            <?php //Seleccionar los materiales
                            $sp = mssql_query("vm_mat_cmb",$db);
                            while($row = mssql_fetch_array($sp))
                            {
                                ?>
                                <option value="<?php echo $row[1] ?>"><?php echo $row[0] ?></option>
                                <?php
                            }
                            ?>
                        </select>
                    </li>
                </form>
                 <li>
                	 <form id="colSearch" action="catalogo.php?colsearch=1" method="post" onsubmit="setColSearchValues()">
                <input type="hidden" id="p_mca_col" name="p_mca" />
                <input type="hidden" id="p_grppat_col" name="p_grppat" />
                <input type="hidden" id="p_mat_col" name="p_mat" />
                <input onclick="$j('#colSearch').submit()" type="button" id="btn_buscar_color" value="" />
                </form>
                </li>
            </ul>

            
        </div>
    </div>
    <div id="footer"></div>
</div>
</body>
</html>
