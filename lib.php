<?php
session_start();
//global $no_constraints;

include_once('class.mysql.php');

function writesvgheader()
{
	print "<?xml version=\"1.0\" encoding=\"UTF-8\"?>";
	print "<!DOCTYPE svg PUBLIC \"-//W3C//DTD SVG 1.0//EN\" \"http://www.w3.org/TR/2001/REC-SVG-20010904/DTD/svg10.dtd\">";
	print "\n<svg xmlns=\"http://www.w3.org/2000/svg\" xml:space=\"preserve\"  shape-rendering=\"geometricPrecision\" text-rendering=\"geometricPrecision\" image-rendering=\"optimizeQuality\" fill-rule=\"evenodd\" clip-rule=\"evenodd\" ";	
	print " viewBox=\"0 0 24000 18000\" ";
	print " xmlns:xlink=\"http://www.w3.org/1999/xlink\">\n";
	print "<a>";
}

function addzero($str)
{
	if (strlen($str) == 1)
		return "0" . $str;
	else
		return $str;
}

function writesvgfooter()
{
	print "\n<rect id=\"recttooltip\" x=\"-10000\" y=\"-10000\" width=\"5500\" height=\"600\" fill-opacity=\"0.8\" pointer-events=\"none\"/>";
	print "\n<text id=\"textboxid\" x=\"-10000\" y=\"-10000\" pointer-events=\"none\"> </text>";                 
	print "\n</a>";
	print "\n</svg>";
}
	
function writesvgbody($fname)
{
	$agent = $_SERVER['HTTP_USER_AGENT'];
	
	if (eregi("MSIE", $agent))
		echo('<embed type="image/svg+xml" src="' . $fname . '" width="100%" height="100%"/>');
	else
		echo('<object type="image/svg+xml" data="' . $fname . '" width="100%" height="100%"><param name="src" value="' . $fname. '"/></object>');
}

function getConn()
{
	global $conn;
	$ini = parse_ini_file("define.ini.php", TRUE);
	$conn = new mysql($ini['database']['db_user'], $ini['database']['db_pass'], $ini['database']['db_host'], $ini['database']['db_name']);
}

function getParameters()
{
	global $conn, $_GLOBALS;
	
	$query = "select * from parametres where Type_Param = 'Nb_Contraintes'" ;
	$data = $conn->select($query, 'OBJECT');
	$_GLOBALS["no_constraints"] = $data->Etat;
	
	$query = "select * from parametres where Type_Param = 'Nb_Robot'" ;
	$data = $conn->select($query, 'OBJECT');
	$_GLOBALS["no_robot"] = $data->Etat;
	
	$query = "select * from parametres where Type_Param = 'Nb_Etats_Desc'" ;
	$data = $conn->select($query, 'OBJECT');
	$_GLOBALS["no_state"] = $data->Etat;
	
	$query = "select count(*) as recordcount from flore";
	$data = $conn->select($query, 'OBJECT');
	$_GLOBALS["no_species"] = $data->recordcount;	
}

function calculateper()
{
	global $store, $_GLOBALS, $conn;		
		
	$userselcount = 0;	// Find number of components selected by the user
	$usrstore = array(); // user selected components
	$perarray = array(); // percentage for each species

	$query = "select * from flore order by Espece";
	$data = $conn->select($query, 'OBJECT');
	
		// Initialize the percentage array to 0
	for ($i = 0; $i < sizeof($data); $i++)
		$perarray[$data[$i]->Code] = 0;
	
		// Get the caracters		
	$query = "select * from caracteres";
	$data = $conn->select($query, 'OBJECT');
	for ($i = 0; $i < sizeof($data); $i++)
	{
		$carac =  $data[$i]->ID_CARAC;	
		$usrstore[$carac] = substr($store, $i, 1); // convert the user selection string to array
		if (substr($store, $i, 1) == "1")
			$userselcount++;
	}	
	

	foreach ($usrstore as $key=>$val)
	{
		if ($val == "1")
		{
			$query = "select * from flore where $key=1";
			$data = $conn->select($query, 'OBJECT');
			if (sizeof($data) == 1)
				$perarray[$data->Code]++;
			else
				for ($i = 0; $i < sizeof($data) ; $i++)
					$perarray[$data[$i]->Code]++;
		}
	}

	foreach ($perarray as $key=>$val)
	{
		if ($val > 0)
		{
			$per = ($val/$userselcount) * 100;
			$perarray[$key] = round($per);
		}
	}
	
	$outputarr = $perarray;
	
	arsort($perarray); // Sort the percentage array in descending order
		
	// Get the top value
	
	foreach ($perarray as $key=>$val)
	{
		$topval = $val;
		break;
	}	
	
	// Find number of species with top value
	$ntopcount = 0;
	foreach ($perarray as $key=>$val)
	{
		if ($val == $topval)
			$ntopcount++;
	}
	
	$_GLOBALS['topcount'] = $ntopcount;
	$_GLOBALS['pertop']= $topval;
	//echo "<p class='result'>" . $ntopcount . " espèces à " . $topval . "%</p>";
	return $outputarr;
}

function formtargeturl($key)
{
	// $targeturl = "http://www.afroweeds.org/list_plante.php?code_plante=";	
	// $url = $targeturl . $key;
	// return $url;

	$targeturl = "http://www.wikwio.org/species/";	
	$url = $targeturl . substr($key, 0,1) . "/" . $key ."/";
	$url = strtolower($url . $key . "_fr.html");
	return $url;
}

function desloop($carval)
{
	global $conn, $usrstore; 
	
	$sql = "select * from caracteres where NUM=" . $carval;
	$cardata = $conn->select($sql, 'OBJECT');
	if ((sizeof($cardata)) == 1)
		$charname = $cardata->ID_CARAC;
	
	$query = "select * from descendance";
	$data = $conn->select($query, 'OBJECT');
	
	for ($j = 0; $j < sizeof($data); $j++)
	{
		if ($data[$j]->ID_CARAC == $charname)
		{
			$objet = $data[$j]->Objet;
			$query1 = "select * from objets_fic where objet ='$objet' ";
			//echo $query;
			$objdata = $conn->select($query1, 'OBJECT');
			
			for ($i = $objdata->Index_Car; $i <= $objdata->Index_Car + $objdata->Nb_Car - 1; $i++)
			{
				$usrstore[$i]  = 0;
				desloop($i);
			}			
		}		
	}
		
}

function apparrayinit()
{
	global $conn, $appellable, $appstr;
		
	$GLOBALS['appellable'] = array();
	
	$sql = "select * from objets_fic order by Objet";
	$data = $conn->select($sql,'OBJECT');
	
	$appstr = "";
	
	for ($i =0; $i < sizeof($data); $i++)
	{
		$desc_num = $data[$i]->Desc_Num;
		$appellable[$desc_num] = $data[$i]->Joker;				
	}
	
	for ($i =0; $i < sizeof($data); $i++)
		$appstr .= $appellable[$i];	
}


function getLanguage($get_lang){
		if(isset($get_lang)){
			$lang = $get_lang;
		}else{	
			$lang = $_SESSION['lang'];
		}
		$allow_lang = array('en','fr');
		
		if(!isset($lang)){return $allow_lang[1];}
		if(in_array($lang,$allow_lang)){
			$_SESSION['lang'] = $lang;			
			return $lang;
		}else{
			$_SESSION['lang'] = $allow_lang[1];
			return $allow_lang[1];
		}
}
function getUrl(){
		$link =  "//$_SERVER[HTTP_HOST]$_SERVER[PHP_SELF]";
		$escaped_link = htmlspecialchars($link, ENT_QUOTES, 'UTF-8');
	return 	$escaped_link;
}
$getUrl = getUrl();
?>