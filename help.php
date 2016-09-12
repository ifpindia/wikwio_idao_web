<?php
	header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Date in the past	
	include_once("lib.php");	
	$lang = getLanguage($_GET['lang']);
	include_once('messages_'.$lang.'.php');
	session_start();
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"  "http://www.w3.org/TR/html4/loose.dtd">

<html>
<head>
<?php
	$ini = parse_ini_file("define.ini.php", TRUE);
	$title = $ini['website']['site_titre'];
	echo "<title>".$title."</title>";


	if ( isset($_POST["txtstore"]) )
	{
		// echo "isset";
		$_SESSION["txtstore"] = $_POST["txtstore"];
		$_SESSION["txtappstr"] = $_POST["txtappstr"];
	}


	$store = $_SESSION["txtstore"];
	$appstr = $_SESSION["txtappstr"];
	// echo "store_3 = ".$store;


?> 	<script src="lib.js" type="text/javascript"></script>
	<link rel="stylesheet" type="text/css" href="default.css">
		<style type="text/css">
		p, li {
			font-family: verdana, arial;
			font-size: 12px;
			line-height: 18px;
			margin-left: 25px;
			text-align: justify;
		}
		
		#pageleft img {
			text-align:center;
			margin-left: auto;
			margin-right: auto;
		}
	</style>
</head>

<body>


<form name="frmgeneral" method="post" action="selectquest.php">
<div id="pageleft">
<?php if($lang == 'en'){
	include_once('help_en.php');
}
if($lang == 'fr'){
	include_once('help_fr.php');
}?>  

</div>

<div id="pageright">
	<!-- <div id="header"><h1>WIKWIO</h1></div> -->
	<img class="img" src="images/header.jpg" alt="Wikwio" HEIGHT='38%' WIDTH='100%'  />
			<div id="navbuttons">
				<?php include_once('navbutton.php');
				echo "<input type=\"hidden\" name=\"txtcharname\" >";	
				echo "<input type=\"hidden\" name=\"txtstore\" value=\"$store\" >\n";
				echo "<input type=\"hidden\" name=\"txtappstr\" value=\"$appstr\">\n";
				 ?>
				
			</div>	
			<br>
			<div class="lang_wrap">
					<p class="lang_wrap_txt" style="margin-left: -2.7vw; margin-top: 2vw; text-align: center;" > <a href="<?= $getUrl; ?>?lang=en" >English</a> | <a href="<?= $getUrl; ?>?lang=fr" >French</a> </p>
			</div>	
			
			
		</div>
</div>
</form>
</body>
</html>
