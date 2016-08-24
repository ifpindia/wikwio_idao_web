<?php
	session_start();
	ob_start();
	header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Date in the past	
	include_once("lib.php");	
	$lang = getLanguage($_GET['lang']);
	include_once('messages_'.$lang.'.php');
	$Last = array();
	$Last[0]="0";
	$_SESSION["last_txtstore"] = $Last;
	//print_r($menu_text);
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"  "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<?php
	$ini = parse_ini_file("define.ini.php", TRUE);
	$title = $ini['website']['site_titre'];
	$resolution = $ini['website']['site_resolution'];
	echo "<title>".$title."</title>";

?> 
	<!-- 
	<svg height="60" width="200">
		<text x="0" y="15" fill="red" transform="translate(220.000000,200.000000)">I love SVG</text>
	</svg> -->
	<script src="lib.js" type="text/javascript"></script>
	<script src="messages/<?= $lang; ?>/tooltips.js" type="text/javascript"></script>
	<link rel="stylesheet" type="text/css" href="default.css">
</head>

<body>
<?php
	if (isset($_REQUEST))
	{
		$cname =  $_REQUEST['txtcharname'];
		//print_r ($_REQUEST);
	}
	else
		//echo "Not available";
?>

<div>
	<form name="frmgeneral" action="selectquest.php" method="post" >
		<div id="pageleft">									
				<?php
					$agent = $_SERVER['HTTP_USER_AGENT'];
					if (eregi("MSIE", $agent)) 
					{
						$str = '<embed id="svgquest" name="svgquest" type="image/svg+xml" src="drawdefaultsvg.php" width="100%" height="100%"/>';	
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
						echo('<object id="svgquest" name="svgquest" type="image/svg+xml" data="drawdefaultsvg.php" width="100%" height="100%"><param name="src" value="drawdefaultsvg.php"></object>');
					} 
				?>
		</div>

		
		<div id="pageright">
			<img class="img" src="images/header.jpg" alt="Wikwio" HEIGHT='38%' WIDTH='100%'  />
		
			<!--This is a comment. Comments are not displayed in the browser-->

			<!-- <div id="header"><h1>WIKWIO</h1></div> -->
			<div id="navbuttons">
				<?php include_once('navbutton.php'); ?> 
			</div>	
			<br>
				<?php include_once('per_show.php'); ?>
			<div class="lang_wrap">
					<p class="lang_wrap_txt" > <a href="index.php?lang=en" >English</a> | <a href="index.php?lang=fr" >French</a> </p>
			</div>
		</div>
		<input type="hidden" name="txtstore" id="txtstore" value="0">
		<input type="hidden" name="txtappstr" value="<?php echo $appstr; ?>">
		<input type="hidden" name="txtcharname" id="txtcharname">
	</form>	
</div>
</body>
</html>

