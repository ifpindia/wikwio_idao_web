<?php
	$str = $_REQUEST["str"];
	include_once("lib.php");
	header("Content-type:image/svg+xml");
	writesvgheader();
	$filename = explode("|", $str);		
	for ($i = 0; $i < count($filename); $i++)	
		print file_get_contents($filename[$i]);
	writesvgfooter();
?>	