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
	<script src="messages/<?= $lang; ?>/tooltips.js" type="text/javascript"></script>
	<link rel="stylesheet" type="text/css" href="default.css">
</head>
<body>
<div>
<form name="frmgeneral" method="post" action="selectquest.php">
<div id="pageleft">
<?php
	// session_start();
	getConn();
	getParameters();

/*
?>
	<!-- <script type="text/javascript" src="deux.php?variable=1234"></script> -->
<?php
*/

	if ( isset($_POST["txtstore"]) )
	{
		$_SESSION["txtstore"] = $_POST["txtstore"];
		$_SESSION["txtquest"] = $_POST["txtquest"];
		$_SESSION["txtappstr"] = $_POST["txtappstr"];
	}


	$store = $_SESSION["txtstore"];
	$questname = $_SESSION["txtquest"];
	$appstr = $_SESSION["txtappstr"];


	if ( isset($_POST["tabnext"]) )
	{
		// echo "exist";
		$Tab = $_POST["tabnext"];
		$Tab_Next = explode("|",$Tab);
	}
	else
	{
		// echo "absent";
		$Tab_Next = [];
	}


	if ( ( isset( $_POST["txtcarname"] ) ) AND ( $_POST["txtcarname"] <> "cancel" ) )
	{
		// echo "isset_car ";
		$charname = $_POST["txtcarname"];
	}

	elseif ( $_POST["txtcarname"] <> "cancel" )
	{
		// echo "not_isset_car";
		$charname = "";
	}


	elseif ( isset($_SESSION["last_txtstore"]) )
	{
		$charname = "";
		$A = array();
		$A = $_SESSION["last_txtstore"];
		$store = array_pop($A);
		if (strlen($store) == 1)
		for ($i=0; $i<$_GLOBALS["no_state"]; $i++)
		$store .= "0";
		$_SESSION["last_txtstore"] = $A;
	}

	else
	{

	}


	if ( ( $charname <> "" ) AND ( $_POST["txtcarname"] <> "cancel" ) )
	{
		$A = array();
		if ( isset($_SESSION["last_txtstore"]) )
		{
			$A = $_SESSION["last_txtstore"];
		}

		$size = sizeof($A);
		$A[$size] = $store;
		if (strlen($store) == 1)
		for ($i=0; $i<$_GLOBALS["no_state"]; $i++)
			$store .= "0";
		$_SESSION["last_txtstore"] = $A;
		// print_r($A);
	}


	if (strlen($store) == 1)
		for ($i=0; $i<$_GLOBALS["no_state"]; $i++)
			$store .= "0";
	
	$usrstore = array();
	for ($i=0; $i<$_GLOBALS["no_state"]; $i++)
		$usrstore[$i] .= substr($store, $i, 1);

	// The following code segment returns an array with caracter name as index key and number as value
	$query = "select ID_CARAC,NUM from caracteres";
	$data = $conn->select($query, 'OBJECT');
	//	echo "size: " . sizeof($data);
		
	$charvalues = array();	
	$char_val = array();	
	for ($i = 0; $i < sizeof($data); $i++)
	{
		$carac =  $data[$i]->ID_CARAC;	
		$char_val[$carac] = $data[$i]->NUM;
	}	
	

	// echo $appstr." / ";

	if ( $charname <> "" )
	{
		$appstr = desloop_2($char_val[$charname],$appstr,$usrstore,$char_val);
	}


	for ( $i = 0; $i < count($Tab_Next); $i++ )
	{
		$N = $Tab_Next[$i];
		// echo " / ".$N;
		$appstr[$N] = 1;
	}

	
	if ( $charname <> "" )
	{

		$query1 = "select Index_Car,Nb_Car from objets_fic where Objet = '$questname'";
		$questdata = $conn->select($query1, 'OBJECT');
		
		$userval = $char_val[$charname];
		if (sizeof($questdata) > 0)
		{
			$no_of_car = $questdata->Nb_Car; 
			$start = $questdata->Index_Car;
		}
		
		if ( ( $usrstore[$userval] == "1" ) /* AND ( $charname <> "" ) */ )
			$presentflag = 1;
			

		$dependancy = 0 ;
		$depval = -1;
		for ($i = $start; $i < $start + $no_of_car; $i++)
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
	}



		// Make the array to string
	$store = "";
	$Nb_Carac = 0;
	for ($i=0; $i<$_GLOBALS["no_state"]; $i++)
	{
		// echo $usrstore[$i];
		$store .= $usrstore[$i];
		if ( $usrstore[$i] == '1' )
		{
			$Nb_Carac++;
		}
	}

	$T = Sol_exist( $store );
	$Sol = $T[1];


	$agent = $_SERVER['HTTP_USER_AGENT'];
	$str = "";
	for ($i = 0; $i < $_GLOBALS["no_robot"]; $i++)
	{
		//echo "<br>----------For Robot $i ---------------<br/>";
		$flag = false;
		$query = "SELECT * FROM `hierarchie` WHERE Robot_Num = $i ORDER BY `hierarchie`.`Index` ASC";
		//echo $query;
		$data = $conn->select($query, 'OBJECT');
		
	    $no_records = sizeof($data);
		$j = 0;
		$flag = 0;
		while ($j < $no_records && $flag == 0 && $no_records > 1)
		{
			$k = 0;
			// echo $data[$j]->Nom."</br>";
			while ($k < $_GLOBALS["no_constraints"] && $flag == 0)
			{
				$fldname = "C_" . $k;
				//echo $fldname;
				// echo $data[$j]->$fldname;
				$dbvalue = $data[$j]->$fldname;
				$cval = $char_val[$dbvalue];
				//echo " => $dbvalue -- $usrstore[$cval]";
				
				if ($dbvalue == "Fin")
				{	
					$robot_num = $data[$j]->Robot_Num;
					if (strlen($data[$j]->Robot_Num) == 1)
						$robot_num = "0" . $data[$j]->Robot_Num;
						
					$path =  "robot" . "/" . $robot_num . "/" . $data[$j]->Nom;
					// echo $path."</br>";
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

		$_SESSION["txtstore"] = $store;
		// echo "store = ".$store;
		if ( $Nb_Carac > 0 )
			$str .= "|" . "images/Back_Green.svg";
		else
			$str .= "|" . "images/Back_Grey.svg";
		// $Bool = true;
		$questname = "redraw.php?str=" . trim($str);
		// $questname_2 = "redraw.php?str=" . trim("images/Back.svg");
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
			<!-- <div id="header"><h1>WIKWIO</h1></div> -->
			<img class="img" src="images/header.jpg" alt="Wikwio" HEIGHT='38%' WIDTH='100%'  />
			<div id="navbuttons">
				<?php include_once('navbutton.php'); ?>
			</div>	
			<?php
				echo "<input type=\"hidden\" name=\"txtstore\" value=\"$store\" >\n";
				echo "<input type=\"hidden\" name=\"txtcharname\" >\n";
				echo "<input type=\"hidden\" name=\"txtappstr\" value=\"$appstr\">\n";
				echo "<input type=\"hidden\" name=\"txtcarname\" >\n";
				calculateper();
				echo "<p class='result' style='margin: 1.8vw 0vw 2.8vw 0.5vw'>" . $_GLOBALS['topcount'] . " ".$menu_text['species']." ".$menu_text['at']." " . $_GLOBALS['pertop'] . " %</p>";
				ob_end_flush();
			?>	
			
		</div>
		</form>
</div>
</body>
</html>

