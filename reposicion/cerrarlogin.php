<?php 
	session_start();
	
	$pageretorno = "../index2.htm";
	if (isset($_SESSION['UsrId'])) {
		unset($_SESSION['UsrId']);
		//$pageretorno = "index2.htm";
	}
	if (isset($_SESSION['CodPer'])) unset($_SESSION['CodPer']);
	if (isset($_SESSION['CodCot'])) unset($_SESSION['CodCot']);

	header("Location: ".$pageretorno);
?>
