<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title></title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

	<script src="../js/mootools-1.2.1-core-yc.js" type="text/javascript"></script> <!-- MOOTOOLS 1.2 BETA -->
	<script src="../js/mootools-1.2-more.js" type="text/javascript"></script> <!-- MOOTOOLS 1.2 BETA -->

	<script src="../js/imagegallery.js" type="text/javascript"></script> <!--   IMAGE GALLERY   -->

	<link rel='stylesheet' href='../css/imagegallery.css' type='text/css' />

</head>

<?php
//Obtengo los datos de conexion de la base de datos
ini_set('display_errors', '0');
include("../config.php");

//Obtengo informacion relacionada al producto
$p_grpprd = ok($_GET['producto']);
$select_prod = mssql_query("vm_strinv_prodinfo '".$p_grpprd."'", $db);
$totalrows = mssql_num_rows($select_prod);
$row = mssql_fetch_array($select_prod);
$dsg_iddsg = $row["id_dsg"];
?>

<body>
	<div id="img_gallery">
		<div id="fullimg">
			<img src="../images/ajax-loader.gif" alt="loading" class="loading" width="24" height="24" />

		</div>
		<a style="display:none;" href="#" id="moveleft">Left</a>	
		<div id="wrapper">
			<ul id="items">
				<li><a href="<?php echo printimg_addr("img1_grupo", $p_grpprd) ?>" id="first" class="item">
                	<span>Imagen 1</span>
					<img class="thumb" alt="img" height="375" src="<?php echo printimg_addr("img1_grupo", $p_grpprd) ?>"/>
				</a></li>

				<li><a href="<?php echo printimg_addr("sketch_design", $dsg_iddsg) ?>" class="item">
                	<span>Imagen 1</span>
					<img class="thumb" alt="img" height="375" src="<?php echo printimg_addr("sketch_design", $dsg_iddsg) ?>"/>
				</a></li>

				<li><a href="<?php echo printimg_addr("img2_grupo", $p_grpprd) ?>" class="item">
                	<span>Imagen 1</span>
					<img class="thumb" alt="img" height="375" src="<?php echo printimg_addr("img2_grupo", $p_grpprd) ?>"/>
				</a></li>
			
				<li><a href="<?php echo printimg_addr("img3_grupo", $p_grpprd) ?>" class="item">
                	<span>Imagen 1</span>
					<img class="thumb" alt="img" height="375" src="<?php echo printimg_addr("img3_grupo", $p_grpprd) ?>"/>
				</a></li>

				<li><a href="<?php echo printimg_addr("img4_grupo", $p_grpprd) ?>" class="item">
                	<span>Imagen 1</span>
					<img class="thumb" alt="img" height="375" src="<?php echo printimg_addr("img4_grupo", $p_grpprd) ?>"/>
				</a></li>

                <li><a href="<?php echo printimg_addr("img5_grupo", $p_grpprd) ?>" class="item">
                	<span>Imagen 1</span>
					<img class="thumb" alt="img" height="375" src="<?php echo printimg_addr("img5_grupo", $p_grpprd) ?>"/>
				</a></li>
			</ul>
		</div>
		<a style="display:none;" id="moveright" href="#">Right</a>
	</div>
    
</body>
</html>
