<?php
	header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Date in the past
	include_once("lib.php");	
	$lang = getLanguage($_GET['lang']);
	include_once('messages_'.$lang.'.php');
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
	</style>
</head>

<body>

<form name="frmgeneral" method="post" action="selectquest.php">
<div id="pageleft">
<?php if($lang == 'en'){?>          
<div class="lang_en">

<p><b>WIKWIO IDAO</b> is a software to aid the identification and knowledge base of the main weeds of cash crops and food crops in the Western Indian Ocean countries.<br> This application has been edited by CIRAD, IFP, FOFIFA, MCIA/MSIRI and CNDRS with the financial support of the European Programme "ACP Science and Technology II» 2012.<br> The authors wish to thank all the national partners of the Wikwio project and members of the participatory portal </p>
<h2 style="margin-left: 27vw; margin-top: 2vw;" > &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; <i><a href="#" onClick="window.open('http://portal.wikwio.org/', '_system');"> portal.wikwio.org</a> </i></h2>
<br>
          <p>Copyright Cirad-Ifp-FOFIFA-MCIA/MSIRI-CNDRS 2014 </p>
</div>
<?php } ?>
<?php if($lang == 'fr'){?> 
<div class="lang_fr">

	<p><b>WIKWIO IDAO</b> est un logiciel d’aide à l’identification et une base de connaissance des principales adventices des cultures alimentaires et des cultures de rente des pays de l’ouest de l’Océan Indien. Ce produit a été édité par le Cirad, l’IFP, le FOFIFA, le MCIA/MSIRI et le CNDRS avec le soutien financier du programme européen « ACP Science and Technology II» 2012. Les auteurs tiennent à associer et à remercier tous les partenaires nationaux du projet Wikwio et membres de la plateforme collaborative <h2><i><a href="#" onClick="window.open('http://portal.wikwio.org/', '_system');"> portal.wikwio.org</a> </i></h2></p>
	<br>
      <p>Copyright Cirad-Ifp-FOFIFA-MCIA/MSIRI-CNDRS 2014 </p>

            
</div>
<?php } ?>
</div>
<div id="pageright">
			<img class="img" src="images/header.jpg" alt="Wikwio" HEIGHT='38%' WIDTH='100%'  />
			<!-- <div id="header"><h1>WIKWIO</h1></div> -->
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
			</div>						<!--     T    R    B    L   -->   
			
			
		</div>
</div>
</form>
</body>
</html>
