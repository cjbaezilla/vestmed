<?php 
	session_start();
	
	$pageretorno = "catalogo.php";
	if (isset($_SESSION['usuario'])) {
		unset($_SESSION['usuario']);
		//$pageretorno = "index2.htm";
	}
	if (isset($_SESSION['CodPer'])) unset($_SESSION['CodPer']);
	if (isset($_SESSION['CodCot'])) unset($_SESSION['CodCot']);

	header("Location: ".$pageretorno);
?>
