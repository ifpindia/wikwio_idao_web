<?php
ob_start();
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
?> 	
	<script src="lib.js" type="text/javascript"></script>
	<link rel="stylesheet" type="text/css" href="default.css">		
	<!--<script type="text/javascript" src="lib/webtoolkit.scrollabletable.js"></script>	-->	
	<link rel="stylesheet" href="lib/style.css" type="text/css" media="print, projection, screen">
	
	<script type="text/javascript" src="lib/jquery-latest.js"></script>
	<script type="text/javascript" src="lib/jquery.tablesorter.js"></script>
	<script type="text/javascript" src="lib/jquery.tablesorter.pager.js"></script>
	<script type="text/javascript" src="lib/chili-1.8b.js"></script>
	<script type="text/javascript" src="lib/docs.js"></script>
	<script type="text/javascript">
	$(function() {
		$("table")
			.tablesorter({widthFixed: true	})
	});
	</script>

	<style type="text/css">
		table a {
			color: #6262DE;
		}
	</style>
</head>
<body>
<div>
	<script type="text/javascript">
		//alert (document.body.clientHeight);
	</script>
	<form name="frmgeneral" method="post" action="">
		<div style="float:left; width:70%">		
		<h4><?= $menu_value[6]; ?></h4><br>	
		<?php
				include_once("lib.php");
				getConn();			
				getParameters();	

				$txtappstr = $_POST['txtappstr'];	

				if ( isset($_POST["txtstore"]) )
				{
					$_SESSION["txtstore"] = $_POST["txtstore"];
					$_SESSION["txtappstr"] = $_POST["txtappstr"];
				}

				$store = $_SESSION["txtstore"];
				$appstr = $_SESSION["txtappstr"];


				if (isset($_POST["cbopercent"]))
					$usrpercent = $_POST["cbopercent"];
				else
					$usrpercent = 0;
				
				// Find whether we want to show errors button or not
				
				$errorflag = 0;
				for ($i=0; $i<$_GLOBALS["no_state"]; $i++)
					$errorflag = $errorflag + intval(substr($store, $i, 1));
					
				if ($errorflag <> "0")	
				{
					echo "<div style=\"text-align:right; font-family:verdana;font-size:12px\">".$menu_text['listdes'];
					echo "<select name=\"cbopercent\" onchange=\"changelevel()\">";
					echo "<option>95</option>";
					echo "<option>90</option>";
					echo "<option>80</option>";
					echo "<option>70</option>";
					echo "<option>60</option>";
					echo "<option>50</option>";
					echo "<option>40</option>";
					echo "<option>30</option>";
					echo "<option>20</option>";
					echo "<option>10</option>";
					echo "<option>0</option>";
					echo "</select> %</div>";
					echo "<br>";
				}
				
				$outputarr = calculateper();	
				
				$totspecies = count($outputarr);
				
				$max = 100;
				while ($max >= 0)
				{
					foreach ($outputarr as $key=>$val)
					{
						if ($val == $max)
							$perarray[$key] = $max;
					}
					$max--;
				}
				
				
				echo "<div align='center'>";
				echo "<div style='float: right; margin-right: 10vw; height: 600px; width: 750px; overflow-y: scroll;'>";
				echo "<table class='tablesorter'  id=\"resultstable\" cellpadding=\"3\">";				
				echo "<thead><tr class='rowtop'>";
				echo "<th style=\"width:25%;\">".$menu_text['species']."</th>";
				echo "<th style=\"width:15%;\">".$menu_text['resultpercentage']."</th>";
				
				if ($errorflag <> "0")						
					echo "<th style=\"width:15%;\">".$menu_text['resulterr']."</th>";
				echo "</tr></thead><tbody>";
				// $targeturl = "http://www.afroweeds.org/list_plante.php?code_plante=";
				$targeturl = "http://www.wikwio.org/species/";
				$trow = 0 ;
				
				foreach ($perarray as $key=>$val)
				{
					if ($val < $usrpercent)
						continue;
					$query = "select Espece,portal_url from flore where code = '$key'";
					$data = $conn->select($query, 'OBJECT');
					if ($trow % 2 == 0)
						echo "<tr class='rowone'>";
					else
						echo "<tr class='rowtwo'>";
						
					if (sizeof($data) == 1)
					{
                        $url_d = $data->portal_url;
						// $url = $targeturl . $key;
						$url = $targeturl . substr($key, 0,1) . "/" . $key ."/";
						$url = $url . $key . "_fr.html";
						$url = strtolower($url);

						$url = ($data->portal_url) ? $data->portal_url : $url ;
						echo "<td style=\"padding-left: 10px; text-align:left\">";
						echo "<a href='$url' onclick='popupBlank(this.href); return false;'>$data->Espece</a>";
						echo "</td><td style=\"text-align:center\">";
						echo $val . "%";
						echo "</td>";
						
						//echo $val. "---" . $errorflag;
						
						// don't show error button when there is no user selection or for species 100%
						if ($errorflag <> "0" )
						{
							echo "<td style=\"text-align:center\">";
							if ($val <> "100")
								echo "<a href='#' onclick=\"showerrors('" .$key . "');\">".$menu_text['resultview']."</a>";
							else
								echo "-";
							echo "</td>";
						}
						echo "</tr>";
					}
					$trow++;				
				}
				echo "</tbody></table></div></div>";
		?>
		</div>

		<div id="pageright">
			<!-- <div id="header"><h1>WIKWIO</h1></div> -->
			<img class="img" src="images/header.jpg" alt="Wikwio" HEIGHT='38%' WIDTH='100%'  />
			<div id="navbuttons">
				<?php include_once('navbutton.php'); ?>
			</div>	
			<br>
			<?php	
				getConn();			
				getParameters();		
				apparrayinit();
				calculateper();
				echo "<p class='result'>" . $_GLOBALS['topcount'] .  " ".$menu_text['species']." ".$menu_text['at']." ".$_GLOBALS['pertop']."%</p>";
				ob_end_flush();
			?>
			<br>
			<div align="center"><a href="javascript:void(0);" onClick="showcancel()" class="button center green" style="width: 4vw;"><?= $menu_text['back'];?></a></div>
		</div>
		
		<input type="hidden" name="txtspcode" id="txtspcode">
		<input type="hidden" name="txtcharname" id="txtcharname">
		<input type="hidden" name="txtstore" id="txtstore" value="<?php echo $store; ?>">
		<input type="hidden" name="txtappstr" id="txtappstr" value="<?php echo $txtappstr; ?>">
	</form>	
</div>

<!--<script type="text/javascript">
	var t = new ScrollableTable(document.getElementById('resultstable'), 450, 570);
</script>-->

</body>
</html>
