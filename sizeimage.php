<?php
include("config.php");

$p_grpprd = 327;

$tamaño = get_size_image("img1_grupo", $p_grpprd, $db, $ancho, $alto);

echo "<img src=\"imagedisplay.php?name=img1_grupo&filter=$p_grpprd\">\n";

echo "<BR>Ancho=".$ancho;
echo "<BR>Alto=".$alto;

?>
