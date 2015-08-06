<?php

	include_once("lib.php");
	getConn();		
	
	$stype = $_GET["tid"];
	//echo $stype;
	$query = "select * from objets_fic where Objet='$stype'";
	$data = $conn->select($query, 'OBJECT');
	if (sizeof($data) == 1)
	{
		echo $data->TipText;
	}
	
?>

