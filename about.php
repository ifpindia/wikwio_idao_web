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

<p><b>WIKWIO IDAO</b> is a software to aid the identification and knowledge base of the main weeds of cash crops and food crops in the Western Indian Ocean countries. This application has been edited by CIRAD, IFP, FOFIFA, MCIA/MSIRI and CNDRS with the financial support of the European Programme "ACP Science and Technology II» 2012. The authors wish to thank all the national partners of the Wikwio project and members of the participatory portal <h2><i><a href="#" onClick="window.open('http://portal.wikwio.org/', '_system');"> portal.wikwio.org</a> </i></h2> </p>
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
			<div id="header"><h1>WIKWIO</h1></div>
			<div id="navbuttons">
				<?php include_once('navbutton.php');
				echo "<input type=\"hidden\" name=\"txtcharname\" >";	
				 ?>
				
			</div>	
			<br>
			<div class="lang_wrap">
					<a href="<?= $getUrl; ?>?lang=en" >English</a> | <a href="<?= $getUrl; ?>?lang=fr" >French</a>
			</div>
			
			
		</div>
</div>
</form>
</body>
</html>