<?php
	ob_start();
	include_once("lib.php");	
	$lang = getLanguage($_GET['lang']);
	include_once('messages_'.$lang.'.php');
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
	<link rel="stylesheet" type="text/css" href="default.css">
</head>
<body>
<div>
<form name="frmgeneral" method="post" action="selectquest.php">
<div id="pageleft">
<?php
	getConn();
	getParameters();
	
	$store = $_POST["txtstore"];
	$questname = $_POST["txtquest"];
	$charname = $_POST["txtcarname"];
	$appstr = $_POST["txtappstr"];
	
	if (strlen($store) == 1)
		for ($i=0; $i<$_GLOBALS["no_state"]; $i++)
			$store .= "0";
	
	$usrstore = array();
	for ($i=0; $i<$_GLOBALS["no_state"]; $i++)
		$usrstore[$i] .= substr($store, $i, 1);

	// The following code segment returns an array with caracter name as index key and number as value
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
	
	/*echo "Constraints: " . $GLOBALS["no_constraints"] ;
	echo "Robots: ". $GLOBALS["no_robot"];
	echo "States: ". $GLOBALS["no_state"];		
	*/
	
		// Mark the number of characters in the selected quest as "0" to avoid the previous selected character 
	$query1 = "select * from objets_fic where Objet = '$questname'";
	$questdata = $conn->select($query1, 'OBJECT');
	
	$userval = $char_val[$charname];
	if (sizeof($questdata) > 0)
	{
		$no_of_car = $questdata->Nb_Car; 
		$start = $questdata->Index_Car;
	}
	
	if ($usrstore[$userval] == "1")
		$presentflag = 1;
		
		
		// find whether descendance (dependancy of layers) has to be eliminated or not 
	$dependancy = 0 ;
	$depval = -1;
	for ($i = $start; $i < $start + $no_of_car - 1; $i++)
	{
		$dependancy += $usrstore[$i] ;
		if ($usrstore[$i] == "1")
			$depval = $i;
	}

	if ($dependancy <> 0) // userselection already there in the same quest
	{
		desloop($depval);
	}
		
	// If the character is already selected, toggle it.
	if ($presentflag) 
		$usrstore[$userval] = "0";	// deselect the character
	else
	{
		$incrval = 0;
		while ($incrval < $no_of_car)
		{
			$usrstore[$start] = "0";
			$start++;
			$incrval++;
		}
		// Mark the user selected character as "1"			
		$usrstore[$userval] = "1";	
	}
	

		// Make the array to string
	$store = "";
	for ($i=0; $i<$_GLOBALS["no_state"]; $i++)
		$store .= $usrstore[$i];
	
	
	//print_r ($char_val);
	//echo $_GLOBALS["no_robot"];
	$agent = $_SERVER['HTTP_USER_AGENT'];
	$str = "";
	for ($i = 0; $i < $_GLOBALS["no_robot"]; $i++)
	{
		//echo "<br>----------For Robot $i ---------------<br/>";
		$flag = false;
		$query = "select * from hierarchie where Robot_Num=$i";
		//echo $query;
		$data = $conn->select($query, 'OBJECT');
		
	    $no_records = sizeof($data);
		$j = 0;
		$flag = 0;
		while ($j < $no_records && $flag == 0 && $no_records > 1)
		{
			$k = 0;
			while ($k < $_GLOBALS["no_constraints"] && $flag == 0)
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
					//echo $path."</br>";
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
		$questname = "redraw.php?str=" . trim($str);
		if (eregi("MSIE", $agent)) 
		{
			$str = '<div><embed  id="svgquest" name="svgquest"  type="image/svg+xml" src="' . $questname . '" width="100%" height="100%"/></div>';	
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
			echo('<object id="svgquest" name="svgquest"  type="image/svg+xml" data="' . $questname . '" width="100%" height="100%"><param name="src" value="' . $questname. '"></object>');
		} 

?>
</div>
		<div id="pageright">
			<div id="header"><h1>WIKWIO</h1></div>
			<div id="navbuttons">
				<?php include_once('navbutton.php'); ?>
			</div>	
			<?php
				echo "<input type=\"hidden\" name=\"txtstore\" value=\"$store\" >\n";
				echo "<input type=\"hidden\" name=\"txtcharname\" >\n";
				echo "<input type=\"hidden\" name=\"txtappstr\" value=\"$appstr\">\n";
				calculateper();
				echo "<p class='result'>" . $_GLOBALS['topcount'] . " ".$menu_text['species']." ".$menu_text['at']." " . $_GLOBALS['pertop'] . "%</p>";
				ob_end_flush();
			?>	
			
		</div>
		</form>
</div>
</body>
</html>

