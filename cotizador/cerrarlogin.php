<?php 
	session_start();
	
	$pageretorno = "../index2.htm";
	if (isset($_SESSION['CodPer'])) unset($_SESSION['CodPer']);
	if (isset($_SESSION['CodCot'])) unset($_SESSION['CodCot']);
	if (isset($_SESSION['UsrIntra'])) unset($_SESSION['UsrIntra']);

	header("Location: ".$pageretorno);
?>
