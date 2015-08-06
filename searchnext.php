<?php
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
<form name="frmquest" method="post" action="redrawdefault.php">
<div id="pageleft">

<?php

	getConn();
	getParameters();
	
	$store = $_POST["txtstore"];
	$ddno = $_POST["txtcurquest"];
	$appstr = $_POST["txtappstr"];
	
	
	// get the appellable string and convert it to array
	for ($i = 0; $i < strlen($appstr); $i++)
		$appellable[$i] = substr($appstr, $i, 1);	
	
	// do manipulation with appellable array
	$appellable[$ddno] = 0;
	
	//again change the appellable array to string
	$appstrlen = strlen($appstr);
	$appstr = "";
	for ($i = 0; $i < $appstrlen; $i++)
		$appstr .= $appellable[$i];
	
	if (strlen($store) == 1)
		for ($i=0; $i<$_GLOBALS["no_state"]; $i++)
			$store .= "0";	

	$showflag = 0;
	calculateper();
	if ($_GLOBALS['topcount'] == 1)
	{
		echo "<p align='center' class='warning'>Il n'y a plus qu'une espèce!</p>";
		$showflag = 1;		
	}	
	
	$s = array();
	$matrice = array();
	$etat = array();
	
		// convert the user selections from string to an array
	$usrstore = array();
	
	for ($i=0; $i<$_GLOBALS["no_state"]; $i++)
		$usrstore[$i] = substr($store, $i, 1);
	
	$sql = "select * from objets_fic order by Objet";
	$data = $conn->select($sql,'OBJECT');
	$obj_reccount = sizeof($data);

	// Initialize the matrice array
	$sql = "select * from flore";
	$data = $conn->select($sql,'OBJECT');
	
	for ($i = 0; $i < sizeof($data); $i++)
	{
		$charsql = "select * from caracteres";
		$chardata = $conn->select($charsql,'OBJECT');
		for ($j = 0; $j < sizeof($chardata); $j++)
		{
			$char = $chardata[$j]->ID_CARAC;
			$matrice[$i][$j] = 0;
			$matrice[$i][$j] = $data[$i]->$char;
			//echo $matrice[$i][$j] . "&nbsp;&nbsp;&nbsp;&nbsp;";
		}		
	}
	
	$nombre = sizeof($data);
	$max = 1;
	$compteur = 0;
	$sql = "select * from objets_fic order by Objet";
	$data = $conn->select($sql,'OBJECT');
	for ($ij =0; $ij < $obj_reccount; $ij++)
	{
		$dno = $data[$ij]->Desc_Num;
		/*echo "<br>Index Value: " . $ij;
		echo "<br>Desc No: " . $dno . " - ". $data[$ij]->Objet;
		echo "<br>Appellable value " . $appellable[$dno];  */
		
		if ($appellable[$data[$ij]->Desc_Num] == 1)
		{	
			$provenance  = -1;
			$index_car= $data[$ij]->Index_Car;
			$nb_car = $data[$ij]->Nb_Car;
			for ($i = $index_car; $i <= $index_car + $nb_car -1 ; $i++)
				if ($usrstore[$i] <> "0")
					$provenance = $i - $data[$ij]->Index_Car;
					
			//echo "<Br>Provenance: " . $provenance;			
		}
		else
		{
			$provenance = 1;	
			$s[$compteur] = 0;
		}
		//echo "<br>" . $ij. " - ". $data[$ij]->Objet . "  " . $provenance   ;
		
		/*echo "<br>Index_car : " . $index_car;
		echo "<br>"; */
		
		//echo $nombre; */
		if ($provenance == -1)
		{
			//echo "No. of cars: " . $nb_car;
			$statval = 0;

			for ($j = 0; $j <=  $nb_car - 1; $j++)
				$etats[$j] = 0;
					 
			for ($i = 0; $i <= $nombre - 1 ; $i++)
			{
				//echo "<br>";					 
				for ($j = 0; $j <=  $nb_car - 1; $j++)
				{					
					$etats[$j] = $etats[$j]  + $matrice[$i][$j + $index_car];
					//echo  $matrice[$i][$j + $index_car] . " ";
					//$etats[$j] = $c;
				}
				
			}
			//echo "<br>State val: " . $c;
			//for ($j = 0; $j <=  $nb_car - 1; $j++)
				//echo $etats[$j]. "&nbsp;&nbsp;&nbsp;&nbsp;"; 
			//echo "<br>S value calculation: "; 
			$s[$compteur] = 0;
			for ($i = 0; $i <= $nb_car - 1 ; $i++)
			{
				if ($etats[$i] <> "0")
				{
					$val = $etats[$i] / $nombre * log($nombre / $etats[$i]) / log($nb_car);
					$s[$compteur] = $s[$compteur] - $etats[$i] / $nombre * log($nombre / $etats[$i]) / log($nb_car);
					//echo "<br>s - $compteur val : " . $s[$compteur] . " val :" . $val;
				}	
			}		
			$s[$compteur] = abs($s[$compteur]);
 		}
		else
			$s[$compteur] = 0;
		$compteur = $compteur + 1;
	}	

	// sort the array and get the maximum	
	$max = 0;
	for ($i = 0; $i <= $compteur - 1; $i++)
	{
		if ($max < $s[$i])
		{
			$max = $s[$i];		   
			$numero = $i;
		}
	}
	//echo "Numero: $numero";
	//for ($i = 0; $i <= $compteur - 1; $i++)
	//	echo  "<br>" . $s[$i];

	if ($max <> "0")
	{		
		$sql = "select * from objets_fic order by Objet";
		$data = $conn->select($sql,'OBJECT');
		$dno = $data[$numero]->Desc_Num;
		$questname = "quest/" . $data[$numero]->Popup;	
		$qname = $data[$numero]->Desc_Num;
		$cname = $data[$numero]->Objet;
	}
	else
	{
		
	}
	
	if ($showflag == 0)
	{
		$agent = $_SERVER['HTTP_USER_AGENT'];
		//echo "<form name=\"frmquest\" method=\"post\" action=\"redrawdefault.php\">";
		if (eregi("MSIE", $agent)) 
		{
			$str = '<embed id="svgquest" name="svgquest" type="image/svg+xml" src="' . $questname . '" width="100%" height="100%"/>';	
			echo "<script type=\"text/javascript\" src=\"writethis.js\"></script>\n";
			$jval .= "<script type=\"text/javascript\">\n";
			$jval .= " var strtowrite= '$str';\n";
			$jval .= " writethis(strtowrite);\n";
			$jval .= "</script>\n";
			$jval .= "<noscript>Javascript not enabled</noscript>\n";
			echo $jval;
		}
		else
		{
			echo('<object id="svgquest" name="svgquest" type="image/svg+xml" data="' . $questname . '" width="100%" height="100%"><param name="src" value="' . $questname. '"></object>');
		} 
	}	
		//echo $store;
		echo "<input type=\"hidden\" name=\"txtstore\" value=\"$store\">\n";
		echo "<input type=\"hidden\" name=\"txtquest\" value=\"$cname\">\n"; 
		echo "<input type=\"hidden\" name=\"txtcharname\" id=\"txtcharname\">\n";
		echo "<input type=\"hidden\" name=\"txtcarname\" >\n";
		echo "<input type=\"hidden\" name=\"txtcurquest\" value=\"$qname\">\n";
		echo "<input type=\"hidden\" name=\"txtappstr\" value=\"$appstr\">\n";
	
	/*for ($i = 0; $i <= $compteur - 1; $i++)
	{
		echo "<br>$s[$i]";
	} */
?>
	</div>
		<div id="pageright">
			<div id="header"><h1>WIKWIO</h1></div>
			<div id="navbuttons">
			<?php
				if ($showflag == 0)
				{ ?>
					<a href="#" onClick="shownext()"><?= $menu_text['nextbut'];?></a><br>
					<a href="#" onClick="showcancel()"><?= $menu_text['cancel'];?></a><br>
			<?php
				}
				else
				{
					echo "<a href=\"index.php\"><img src=\"images/new_ident_btn.jpg\"  alt=\"\"></a><br>";
					$query = "select * from but_deter";
					$data = $conn->select($query, 'OBJECT');
					
					if ($data <> "")
					{
						for ($i = 0; $i < sizeof($data); $i++)
						{
							$imgname = $data[$i]->File;
							$key = $data[$i]->Key;
							echo "\n<a href=\"#\" onclick=\"showquest('" . $key . "')\">";
							echo "<img src=\"images/$imgname\" alt=''>";
							echo "</a><br>";
						}
					}
					
					echo "<a href=\"#\" onclick=\"showsearch()\"><img src=\"images/search.jpg\" alt=\"\"></a><br>";
					echo "<a href=\"#\" onclick=\"showspecies()\"><img src=\"images/species_list_btn.jpg\" alt=\"\"></a><br>";
					echo "<a href=\"#\" onclick=\"showresults()\"><img src=\"images/results_btn.jpg\" alt=\"\"></a><br>";
					echo "<a href=\"#\" onclick=\"showabout()\"><img src=\"images/about_btn.jpg\" alt=\"\"></a><br>";
					echo "<a href=\"#\" onclick=\"showhelp()\"><img src=\"images/help_btn.jpg\" alt=\"\"></a><br>";
				}			
			?>	
			</div>	
			<?php
				//calculateper();
				echo "<p class='result'>" . $_GLOBALS['topcount'] . " espèces à " . $_GLOBALS['pertop'] . "%</p>";
			?>			
		</div>
	</form>
</div>
</body>
</html>