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
	<script src="messages/<?= $lang; ?>/tooltips.js" type="text/javascript"></script>
	<link rel="stylesheet" type="text/css" href="default.css">
</head>
<body>
<div>
<form name="frmquest" method="post" action="redrawdefault.php">
<div id="pageleft">

<?php
	// session_start();
	getConn();
	getParameters();

	if ( isset($_POST["txtstore"]) )
	{
		$_SESSION["txtstore"] = $_POST["txtstore"];
		$_SESSION["txtappstr"] = $_POST["txtappstr"];
	}

	$store = $_SESSION["txtstore"];
	$appstr = $_SESSION["txtappstr"];
	
	if (strlen($store) == 1)
		for ($i=0; $i<$_GLOBALS["no_state"]; $i++)
			$store .= "0";
	

	for ($i = 0; $i < strlen($appstr); $i++)
		$appellable[$i] = substr($appstr, $i, 1);


	$showflag = 0;
	calculateper();
	if ($_GLOBALS['topcount'] == 1)
	{
		echo "<p align='center' class='warning'>".$menu_text['OneSpicieLeft']."</p>";
		$showflag = 1;		
	}	
	

		// convert the user selections from string to an array
	$usrstore = array();
	$Questions = array();
	
	for ($i=0; $i<$_GLOBALS["no_state"]; $i++)
		$usrstore[$i] = substr($store, $i, 1);



	$sql = "select Index_Car,Nb_Car,Objet from objets_fic order by Index_Car";
	$data = $conn->select($sql,'OBJECT');
	$eps = 0;
	for ($i=0; $i<count($data); $i++)
	{
		$Index_car = $data[$i]->Index_Car;
		$Nb_car = $data[$i]->Nb_Car;

		if ( zeros( $usrstore, $Index_car, $Nb_car ) AND ( $appellable[$i] == 1 ) )	
		{
			// echo "Yes";
			$Questions[$i-$eps] = $data[$i]->Objet;
		}
		else
		{
			$eps++;
		}
	}

	if (sizeof($Questions) == 0)
	{
		echo "<p align='center' class='warning'>".$menu_text['NoQuestLeft']."</p>";
	}

	// print_r($usrstore);
	$str = Create_str( $usrstore, 0 )[0];

	$sql = "SELECT Code FROM `flore` WHERE $str";	
	$Result = $conn->select($sql,'OBJECT');	


	$numero = Best_Question($Questions,$str,count($Result));
	

	$sql = "select Objet,Popup,Desc_Num from objets_fic order by Index_Car";
	$data = $conn->select($sql,'OBJECT');

	for ( $i = 0 ; $i < count($data) ; $i++ )
	{
		if ( $data[$i]->Objet == $Questions[$numero] )
			$num = $i;
	}

	// $dno = $data[$num]->Desc_Num;
	$questname = "quest/" . $data[$num]->Popup;	
	$qname = $num;		// $qname = $data->Desc_Num;
	$cname = $data[$num]->Objet;


	if ($_GLOBALS['topcount'] == 1)
		?>

		<script> 
		var Quest = '<?php echo $cname; ?>';
		</script>

		<?php
		$Msg_Quest = '<script> document.write(tooltips[Quest]);  </script>';
		?>
		<div id="Msg_Quest" style=" font-size: 1.3vw; text-align: center; font-weight: bold; width: 32%; padding-top: 0.8vw; padding-bottom: 0.8vw; position: relative; top: 0.8vw; left: 32%; border: 0.25vw solid #E97900; border-radius: 2vw; " >
		<?php echo $Msg_Quest; ?>
		</div>

		<?php


	if ($showflag == 0)
	{
		$agent = $_SERVER['HTTP_USER_AGENT'];
		if (eregi("MSIE", $agent)) 
		{
			$str = '<embed id="svgquest" name="svgquest" type="image/svg+xml" src="' . $questname . '" width="84%" height="100%" style="margin-left: 4.4vw; margin-top:1vw" />';	
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
			echo('<object id="svgquest" name="svgquest" type="image/svg+xml" data="' . $questname . '" width="84%" height="100%" style="margin-left: 4.4vw; margin-top:1vw" ><param name="src" value="' . $questname. '"></object>');
		} 
	}	
		echo "<input type=\"hidden\" name=\"txtstore\" value=\"$store\">\n";
		echo "<input type=\"hidden\" name=\"txtquest\" value=\"$cname\">\n"; 
		echo "<input type=\"hidden\" name=\"txtcharname\" id=\"txtcharname\">\n";
		echo "<input type=\"hidden\" name=\"txtcarname\" >\n";
		echo "<input type=\"hidden\" name=\"txtcurquest\" value=\"$qname\">\n";
		echo "<input type=\"hidden\" name=\"txtappstr\" value=\"$appstr\">\n";
	
?>
	</div>
		<div id="pageright">
			<img class="img" src="images/header.jpg" alt="Wikwio" HEIGHT='38%' WIDTH='100%'  />
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
					
					include_once('navbutton.php');
					?>
					</div>

					<br>

					<?php
					echo "<p class='result'>" . $_GLOBALS['topcount'] . " ".$menu_text['specie']." ".$menu_text['at']." " . $_GLOBALS['pertop'] . " %</p>";
					?>	
					
					<div class="lang_wrap">
					<p class="lang_wrap_txt" > <a href="index.php?lang=en" >English</a> | <a href="index.php?lang=fr" >French</a> </p>
					</div>
	
					<?php

				}			
			?>	
			</div>
		<?php	
		if ($showflag == 0)	
		{
			calculateper();
			echo "<p class='result' style='margin: 1.8vw 0vw 2.8vw 0.5vw'>" . $_GLOBALS['topcount'] . " ".$menu_text['species']." ".$menu_text['at']." " . $_GLOBALS['pertop'] . " %</p>";	
		}
		?>	
		</div>
	</form>
</div>
</body>
</html>

