<?php
    ini_set('display_errors', '0');
    session_start();
    include("global_cot.php");

    $pagina="http://server2000/vestmed";
    $cod_per = 50001;
    $cod_cot = 840;
    $nombre = "Mario Alberto Labrin Barrientos";

    include("avisonewmensaje.php");

    echo $cuerpo_mail;
?>
