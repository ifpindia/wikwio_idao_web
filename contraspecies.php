<?php
	ob_start();
	include_once("lib.php");
	$lang = getLanguage($_GET['lang']);
	include_once('messages_'.$lang.'.php');	
	getConn();
	//$spcode = $_POST["txtspcode"];
	//$store = $_POST["txtstore"];
	
	// The following code segment returns an array with caracter name as index key and number as value
	$code = $_POST['code'];
	$query = "select * from flore where Code='$code'";
	$data = $conn->select($query, 'OBJECT');

	$test = 0;
	$exstore = "";
	foreach($data as $key => $value){
		$test++;
		if($test >=6){
			$exstore.=$value;
		}
		//echo "Key: $key; Value: $value<br />\n";
	}	
	
	getParameters();
	
		// convert the user selection from string to array
	$usrstore = array();	
	for ($i=0; $i<$_GLOBALS["no_state"]; $i++)
		$usrstore[$i] .= substr($exstore, $i, 1);
	
	
	$query = "select * from caracteres";
	$data = $conn->select($query, 'OBJECT');
	//	echo "size: " . sizeof($data);
		
	$charvalues = array();	
	$char_val = array();	
	for ($i = 0; $i < sizeof($data); $i++)
	{
		$carac =  $data[$i]->ID_CARAC;	
		$char_val[$carac] = $data[$i]->NUM;
	}
	
		
	$str = "";
	for ($i = 0; $i < $_GLOBALS["no_robot"]; $i++)
	{
		//echo "<br>----------For Robot $i ---------------<br/>";
		$flag = false;
		$query = "select * from hierarchie where Robot_Num=$i";
		$data = $conn->select($query, 'OBJECT');
		
		$no_records = sizeof($data);
		$j = 0;
		$flag = 0;
		while ($j < $no_records && $flag == 0)
		{
			$k = 0;
			while ($k < $_GLOBALS["no_constraints"] && $flag == 0 && $no_records > 1)
			{
				$fldname = "C_" . $k;
				//echo $fldname;
				//echo $data[$j]->$fldname;
				$dbvalue = $data[$j]->$fldname;
				$cval = $char_val[$dbvalue];
				//echo " => $dbvalue -- $usrstore[$cval]";
				
				if ($dbvalue == "Fin")
				{	
					$robot_num = $data[$j]->Robot_Num;
					if (strlen($data[$j]->Robot_Num) == 1)
						$robot_num = "0" . $data[$j]->Robot_Num;
						
					$path =  "robot" . "/" . $robot_num . "/" . $data[$j]->Nom;
					if (strlen($str) > 0 )
						$str .= "|" . $path;
					else 
						$str .= $path;
					$flag = 1;
				}
				
				if ($usrstore[$cval] == "0")
					break;			
			
				$k++;
			}
			$j++;
		}
	}	

	
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
   "http://www.w3.org/TR/html4/loose.dtd">

<html>
<head>
<?php
	$ini = parse_ini_file("define.ini.php", TRUE);
	$title = $ini['website']['site_titre'];
	echo "<title>".$title."</title>";
?> 	<script src="lib.js" type="text/javascript"></script>
	<script src="messages/<?= $lang; ?>/tooltips.js" type="text/javascript"></script>
	<link rel="stylesheet" type="text/css" href="default.css">
</head>
<body>
	<div>
		<form name="frmgeneral" method="post" action="selectquest.php">
			<div id="pageleft">
			<?php
				//display the svg
				$questname = "redraw.php?str=" . trim($str);
				if (eregi("MSIE", $agent)) 
				{
					$str = '<div><embed type="image/svg+xml" src="' . $questname . '" width="100%" height="100%"/></div>';	
					echo "<script type=\"text/javascript\" src=\"writethis.js\"></script>\n";
					$jval .= "<script type=\"text/javascript\">\n";
					$jval .= " var strtowrite= '$str';\n";
					$jval .= "	writethis(strtowrite);\n";
					$jval .= "</script>\n";
					$jval .= "<noscript>Javascript not enabled</noscript>\n";
					echo $jval;
				}
				else
				{
					echo('<div><object type="image/svg+xml" data="' . $questname . '" width="100%" height="100%"><param name="src" value="' . $questname. '"></object></div>');
				} 
	
				// Buttons are habit, phyllotaxy, and pneumatophores
				// check these quest are set 
				
	
			?>
			</div>
			
			<div id="pageright">
				<!-- <div id="header"><h1>WIKWIO</h1></div> -->
				<img class="img" src="images/header.jpg" alt="Wikwio" HEIGHT='38%' WIDTH='100%'  />
				<div id="navbuttons">
					<?php include_once('navbutton.php'); ?>
				</div>	
				<?php
					echo "<input type=\"hidden\" name=\"txtstore\" value=\"$store\" >";
					echo "<input type=\"hidden\" name=\"txtcharname\" >";
					calculateper();
					echo "<p class='result'>" . $_GLOBALS['topcount'] . " ".$menu_text['species']." ".$menu_text['at']." " . $_GLOBALS['pertop'] . "%</p>";
				?>			
			</div>
		</form>
	</div>
</body>
</html>
