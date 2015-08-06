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
	<script type="text/javascript" src="lib/webtoolkit.scrollabletable.js"></script>		
	<link rel="stylesheet" href="lib/style.css" type="text/css" media="print, projection, screen">
	
	<script type="text/javascript" src="lib/jquery-latest.js"></script>
	<script type="text/javascript" src="lib/jquery.tablesorter.js"></script>
	<script type="text/javascript" src="lib/jquery.tablesorter.pager.js"></script>
	<script type="text/javascript" src="lib/chili-1.8b.js"></script>
	<script type="text/javascript" src="lib/docs.js"></script>
	<script type="text/javascript">
	$(function() {
		$("table")
			.tablesorter({widthFixed: false})
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
		<div style="float:left; width:80%">		
		<h4>Results</h4><br>	
		<?php
				include_once("lib.php");
				getConn();			
				getParameters();		
				$store = $_POST["txtstore"];
				$appstr = $_POST["txtappstr"];
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
					echo "<div style=\"text-align:right; font-family:verdana;font-size:12px\">Liste des espèces ";
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
				echo "<table class='tablesorter' style=\"width:50%;\" id=\"resultstable\" cellpadding=\"3\">";				
				echo "<thead><tr class='rowtop'>";
				echo "<th style=\"width:25%;\">Nom d'espèce</th>";
				echo "<th style=\"width:15%;\">Pourcentage</th>";
				
				if ($errorflag <> "0")						
					echo "<th style=\"width:15%;\">Erreurs</th>";
				echo "</tr></thead><tbody>";
				// $targeturl = "http://www.afroweeds.org/list_plante.php?code_plante=";
				$targeturl = "http://www.wikwio.org/species/";
				$trow = 0 ;
				
				foreach ($perarray as $key=>$val)
				{
					if ($val < $usrpercent)
						continue;
					$query = "select * from flore where code = '$key'";
					$data = $conn->select($query, 'OBJECT');
					if ($trow % 2 == 0)
						echo "<tr class='rowone'>";
					else
						echo "<tr class='rowtwo'>";
						
					if (sizeof($data) == 1)
					{
						// $url = $targeturl . $key;
						$url = $targeturl . substr($key, 0,1) . "/" . $key ."/";
						$url = $url . $key . "_fr.html";
						$url = strtolower($url);
						
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
								echo "<a href='#' onclick=\"showerrors('" .$key . "');\">View</a>";
							else
								echo "-";
							echo "</td>";
						}
						echo "</tr>";
					}
					$trow++;				
				}
				echo "</tbody></table></div>";
		?>
		</div>

		<div id="pageright">
			<div id="header"><h1>WIKWIO</h1></div>
			<div id="navbuttons">
				<a href="index.php"><img src="images/new_ident_btn.jpg" alt=""></a><br>
				<?php					
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
				?>
				
				<a href="#" onclick="showsearch('<?php echo $store; ?>')"><img src="images/search.jpg" alt=""></a><br>
				<a href="#" onclick="showspecies('<?php echo $store; ?>')"><img src="images/species_list_btn.jpg" alt=""></a><br>
				<a href="#" onclick="showresults('<?php echo $store; ?>')"><img src="images/results_btn.jpg" alt=""></a><br>
				<a href="#" onclick="showabout('<?php echo $store; ?>')"><img src="images/about_btn.jpg" alt=""></a><br>
				<a href="#" onclick="showhelp('<?php echo $store; ?>')"><img src="images/help_btn.jpg" alt=""></a><br>
			</div>	
			<br><br>
			<div align="center"><a href="javascript:history.go(-1);"><img src="images/back_btn.png" border="0" alt=""></a></div>
			<?php
				calculateper();
				echo "<p class='result'>" . $_GLOBALS['topcount'] . " espèces à " . $_GLOBALS['pertop'] . "%</p>";
			?>
		</div>
		<input type="hidden" name="txtspcode" id="txtspcode">
		<input type="hidden" name="txtcharname" id="txtcharname">
		<input type="hidden" name="txtstore" id="txtstore" value="<?php echo $store; ?>">
	</form>	
</div>

<script type="text/javascript">
	var t = new ScrollableTable(document.getElementById('resultstable'), 450, 570);
</script>

</body>
</html>