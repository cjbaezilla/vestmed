<?php
unset($_SESSION['usuario']);

$pagina_retorno = "index.php";

header( 'Location: '.$pagina_retorno );
?>
