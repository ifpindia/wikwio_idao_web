<?php
	ob_start();
	header("Content-type:image/svg+xml");
	include_once('class.mysql.php');
	include_once("lib.php");
	$ini = parse_ini_file("define.ini.php", TRUE);
	$conn = new mysql($ini['database']['db_user'], $ini['database']['db_pass'], $ini['database']['db_host'], $ini['database']['db_name']);	
	//$query = "select * from hierarchie where C_0 = 'Fin' and Robot_Num <= 5";
	$query = "select * from hierarchie where C_0 = 'Fin'";
	$data = $conn->select($query, 'OBJECT');
	writesvgheader();
	for($i=0; $i<sizeof($data); $i++)
	{		
		$path = "robot" . "/"  . addzero($data[$i]->Robot_Num) . "/" . $data[$i]->Nom;
		print $path;
		//print $path;
		print file_get_contents($path);
	}
	writesvgfooter();
	ob_end_flush();
?>