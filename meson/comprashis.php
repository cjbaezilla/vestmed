<?php
ini_set('display_errors', '0');
session_start();
include("../config.php");

if (!isset($_SESSION['usuario'])) header("Location: index.php");

$p_grpprd = ok($_GET['producto']);  

$select_prod = mssql_query("vm_strinv_prodinfo '".$p_grpprd."'", $db);
$totalrows = mssql_num_rows($select_prod);

if($totalrows==0)
      echo "El producto seleccionado ya no est&aacute; en nuestra base de datos (".$p_grpprd.")";
else
{
    //Datos del producto
    $row = mssql_fetch_array($select_prod);
    $grpprd_title = str_replace("#", "'",$row["title"]);
    $dsg_style = $row["style"];                 $cod_grppat = $row["cod_grppat"];
    $dsg_image = $row["image"];                 $dsg_marca = $row["marca"];
    $dsg_iddsg = $row["id_dsg"];                $grpprd_descripcion = str_replace("#", "'",utf8_encode($row["grp_desc"]));
    $dsg_name = str_replace("#", "'",utf8_encode($row["nom_dsg"]));
    $p_coddsg = ok($dsg_iddsg);
    //Datos de la marca
    $select_marca = mssql_query("vm_strmrc_nom '".$p_coddsg."'",$db);
    $rowx = mssql_fetch_array($select_marca);
    $mca_nombre = $rowx["mca_nombre"];           $mca_pais = $rowx["mca_pais"];
    $mca_ciudad = $rowx["mca_ciudad"];           $mca_direccion = $rowx["mca_direccion"];
    $mca_zip = $rowx["mca_zip"];                 $mca_fono = $rowx["mca_fono"];
    $mca_fax = $rowx["mca_fax"];                 $mca_web = $rowx["mca_web"];
    $mca_shipping = $rowx["mca_shipping"];       $mca_descripcion = utf8_encode($rowx["mca_descripcion"]);
    $linmca_codigo = $rowx["linmca_codigo"];     $linmca_descripcion = utf8_encode($rowx["linmca_descripcion"]);
    $mat_descripcion = utf8_encode($rowx["mat_descripcion"]); 
    $hoy = date('Ymd');
    mssql_free_result($row);    
}


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <link href="../css/layout.css" type="text/css" rel="stylesheet" />
        <link href="../Include/estilos.css" type="text/css" rel="stylesheet" />
        <title>Histórico Compras</title>
        <script language="JavaScript" src="../Include/ValidarDataInput.js" type="text/javascript" ></script>
        <script language="JavaScript" src="../Include/SoloNumeros.js" type="text/javascript" ></script>
        <script language="JavaScript" src="../Include/validarRut.js" type="text/javascript" ></script>
    </head>
    <body>

               	<div id="titulo-catalogo"></div>
            	<div id="wrap-imagen-producto">
                    <div id="imagen-producto">
                        <?php
                        //Obtengo la imagen de la DB
                        //var_dump( getimagesize(printimg_addr("img1_grupo", $p_grpprd)));
                        $tamaño = get_size_image("img1_grupo", $p_grpprd, $db, $ancho, $alto);
                        if($ancho > $alto || $ancho == $alto){
                            $tamano = "width=\"270\"";
                        } else if($alto > $ancho) $tamano = "height=\"270\"";
                            echo '<img src="../'.printimg_addr("img1_grupo", $p_grpprd).'" '. $tamano .'/>';
                        ?>
                    </div>
                </div>
                <div id="wrap-detalle-producto">
                    <div class="titulo-producto"><?php echo $grpprd_title; ?></div>
                    <div class="subtitulo-producto">Style <?php echo $dsg_style . ' ' . $dsg_name ?></div>
                    <p class="descripcion-producto">
                        <?php echo $grpprd_descripcion ?>
                    </p>
                    <p class="descripcion-producto">
                        Marca: <?php echo $mca_nombre ?>
                        <br />
                        L&iacute;nea: <?php echo $linmca_descripcion ?>
                    </p>
                </div>
        
    </body>
</html>
