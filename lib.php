<?php
session_start();
//global $no_constraints;

include_once('class.mysql.php');

function writesvgheader()
{
	print "<?xml version=\"1.0\" encoding=\"UTF-8\"?>";
	print "<!DOCTYPE svg PUBLIC \"-//W3C//DTD SVG 1.0//EN\" \"http://www.w3.org/TR/2001/REC-SVG-20010904/DTD/svg10.dtd\">";
	print "\n<svg xmlns=\"http://www.w3.org/2000/svg\" xml:space=\"preserve\"  shape-rendering=\"geometricPrecision\" text-rendering=\"geometricPrecision\" image-rendering=\"optimizeQuality\" fill-rule=\"evenodd\" clip-rule=\"evenodd\" ";	
	print " viewBox=\"0 0 26000 18000\" ";
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
	
	$query = "select Etat from parametres where Type_Param = 'Nb_Contraintes'" ;
	$data = $conn->select($query, 'OBJECT');
	$_GLOBALS["no_constraints"] = $data->Etat;
	
	$query = "select Etat from parametres where Type_Param = 'Nb_Robot'" ;
	$data = $conn->select($query, 'OBJECT');
	$_GLOBALS["no_robot"] = $data->Etat;
	
	$query = "select Etat from parametres where Type_Param = 'Nb_Etats_Desc'" ;
	$data = $conn->select($query, 'OBJECT');
	$_GLOBALS["no_state"] = $data->Etat;
	
	$query = "select count(Code) as recordcount from flore";
	$data = $conn->select($query, 'OBJECT');
	$_GLOBALS["no_species"] = $data->recordcount;	
}

function calculateper()
{
	global $store, $_GLOBALS, $conn;		
		
	$userselcount = 0;	// Find number of components selected by the user
	$usrstore = array(); // user selected components
	$perarray = array(); // percentage for each species

	$query = "select Code from flore order by Espece";
	$data = $conn->select($query, 'OBJECT');
	
		// Initialize the percentage array to 0
	for ($i = 0; $i < sizeof($data); $i++)
		$perarray[$data[$i]->Code] = 0;
	
		// Get the caracters		
	$query = "select ID_CARAC from caracteres";
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
			$query = "select Code from flore where $key=1";
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
	
	$sql = "select ID_CARAC from caracteres where NUM=" . $carval;
	$cardata = $conn->select($sql, 'OBJECT');
	if ((sizeof($cardata)) == 1)
		$charname = $cardata->ID_CARAC;
	
	$query = "select * from descendance_Quest";
	$data = $conn->select($query, 'OBJECT');
	
	for ($j = 0; $j < sizeof($data); $j++)
	{
		if ($data[$j]->Carac == $charname)
		{
			$objet = $data[$j]->Quest;
			$query1 = "select Index_Car,Nb_Car from objets_fic where objet ='$objet' ";
			//echo $query;
			$objdata = $conn->select($query1, 'OBJECT');
			
			for ($i = $objdata->Index_Car; $i <= $objdata->Index_Car + $objdata->Nb_Car - 1; $i++)
			{
				$usrstore[$i] = 0;
				desloop($i);
			}			
		}		
	}		
}


function desloop_2($carval,$appstr,$usrstore,$char_val)
{
	global $conn, $usrstore;

	$appellable = [];
	for ($a = 0; $a < strlen($appstr); $a++)
		$appellable[$a] = substr($appstr, $a, 1);
	
	$sql = "select ID_CARAC from caracteres where NUM=" . $carval;
	$cardata = $conn->select($sql, 'OBJECT');
	if ((sizeof($cardata)) == 1)
		$charname = $cardata->ID_CARAC;
	
	$query = "select * from descendance_Quest";
	$data = $conn->select($query, 'OBJECT');

	$sql = "select Objet from objets_fic ORDER BY Index_Car";
	$cardata = $conn->select($sql, 'OBJECT');

	if ( $usrstore[$carval] == 0 )
	{
		for ($j = 0; $j < sizeof($data); $j++)
		{
			$Car = $data[$j]->Carac ;
			if ( $Car == $charname )
			{
				// echo " Extension ";
				$Objet = $data[$j]->Quest;
				for ($k = 0; $k < sizeof($cardata); $k++)
				{
					if ( $cardata[$k]->Objet == $Objet )
					{
						$Q = $cardata[$k]->Objet;
						// echo " / Confirmé, question : $Q / ";
						$T = $appellable[$k];
						// echo " Valeure actuelle = $T / ";
						$appellable[$k] = 1;
						break;
					}		
				}
			}	
		}
	}
	else if ( $usrstore[$carval] == "1" )
	{
		for ($j = 0; $j < sizeof($data); $j++)
		{
			$Car = $data[$j]->Carac ;
			if ( $Car == $charname )
			{
				// echo " Diminution ";
				$Objet = $data[$j]->Quest;
				for ($k = 0; $k < sizeof($cardata); $k++)
				{
					if ( $cardata[$k]->Objet == $Objet )
					{
						$Q = $cardata[$k]->Objet;
						// echo " / Confirmé, question : $Q / ";
						$T = $appellable[$k];
						// echo " Valeure actuelle = $T / ";
						$appellable[$k] = 0;
						break;
					}		
				}
			}	
		}
	}
	else
	{

	}

	$appstr_bis = "";
	for ($i = 0; $i < sizeof($appellable); $i++)	
	{
		$appstr_bis .= $appellable[$i];
	}

	return $appstr_bis;
}



function apparrayinit()
{
	global $conn, $appellable, $appstr;
		
	$GLOBALS['appellable'] = array();
	
	$sql = "select Objet,Joker from objets_fic order by Index_Car";
	$data = $conn->select($sql,'OBJECT');
	
	$appstr = "";
	
	for ($i = 0; $i < sizeof($data); $i++)
	{
		//$desc_num = $data[$i]->Desc_Num;
		$appellable[$i] = $data[$i]->Joker;			
	}

	// print_r($appellable);
	
	for ($i = 0; $i < sizeof($appellable); $i++)
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




function zeros( $usrstore, $Index_car, $Nb_car )
{
	for ( $i = $Index_car; $i < $Index_car + $Nb_car; $i++ )
		if ( $usrstore[$i] == 1 )
		{
			return false;
		}
	return true;
}


function Ones($Tab)
{
for ( $i = 0; $i < count($Tab); $i++ )
	{
		if ( $Tab[$i] <> 1 )
		{
			return false;
		}
	}
return true;
}


function Entropy($A,$Size)
{
$S = 0;
$C = count($A);
for ( $i = 0; $i < $C ; $i++ )	
	{
	$r = ( $A[$i] / $Size );
	$S = $S - ( $r * log( $r ) ) ; // log(2)
	}
$S = $S / log( $C );
return $S;		
}	


function Nombre_rep_Dyna($obj,$str)		// Calcul des effectifs restants après les différentes réponses possibles de l'utilisateur, Pour UNE question.
{
	global $conn;			// Variable nécessaire à la connection.

	$sql = "SELECT Nb_Car,Index_Car FROM `objets_fic` WHERE Objet='$obj' ";
	$objet_fic = $conn->select($sql,'OBJECT');
	$Nb_Car = $objet_fic->Nb_Car;					// On établit les différents caractères qui interviennent pour répondre à la question.
	$Index_Car = $objet_fic->Index_Car;				// Plage des caractère : NUM :  Index_Car  -->   Index_Car + Nb_Car.
	$sql = "SELECT ID_CARAC FROM `caracteres` ORDER BY `NUM` ASC";	
	$caracteres = $conn->select($sql,'OBJECT');
	$A = array();
	$str_bis = $str." AND ( ";
	for ($i = $Index_Car; $i < $Index_Car + $Nb_Car ; $i++)		// Pour chaque caractère, quel sera l'effectif restant s'il est sélectionné.
		{
		$ID_carac = $caracteres[$i]->ID_CARAC;
		$sql = "SELECT Code FROM `flore` WHERE $ID_carac=1 AND $str ";	
		$flore = $conn->select($sql,'OBJECT');
		$str_bis = $str_bis."$ID_carac=1 OR ";
		$A[$i-$Index_Car] = count($flore);
		}
	$str_bis = $str_bis." 0 ) ";
	$sql = "SELECT Code FROM `flore` WHERE $str_bis ";	
	$flore_bis = $conn->select($sql,'OBJECT');
	$Union = count($flore_bis);
	return [$A,$Union];
}



function Best_Question($Quest,$str,$Size)
{
	global $conn;

	$E_Max = 0;
	$i_Max = 0;
	// print_r($Quest);
	foreach ($Quest as $i => $Val)
		{
		$T = Nombre_rep_Dyna($Val,$str);
		// echo " Tab $Val = ";
		// print_r($T[0]);

		$A = $T[0];
		// print_r($A);
		$Union = $T[1]; 
		$E = Entropy($A,$Size) * ( $Union / $Size ) ;	// $E = Entropy($A,$Size) * ( $Union / $Size ) ; // $E = Entropy($A,$Size);

		if ( ( $Union == $Size ) AND Ones($A) )
		{
			$E = $E + 5;
		}


		// echo " Entropy $Val = ".$E."  ";


		// echo $Val." ".$E ." : ";
		// echo $Val." : ".$E."   ";
		// print_r($A);
		// echo " Union = ".$Union."\n";

		// echo $Val." $E - ";
	
		/*
		if ( $Val == "Ligule" )
		{
			$E = $E/1.5;
		}
		*/
		
		// echo "$E  ";

		if ( $E > $E_Max )
			{
			$E_Max = $E;
			$i_Max = $i;
			}
		}
	/*
	$Rep = $Quest[$i_Max];
	unset($Quest[$i_Max]);
	// echo "\n";
	*/
	return /*[$Quest,*/$i_Max/*]*/;			
}


/*
function Create_str( $usrstore )
{
	global $conn;
	$charsql = "select ID_CARAC from caracteres order by NUM";
	$chardata = $conn->select($charsql,'OBJECT');
	$str = " 1 ";
	for ( $i = 0; $i < sizeof($usrstore); $i++ )
	{
		if ( $usrstore[$i] == 1 )
		{
			$bis = $chardata[$i]->ID_CARAC;
			$str = $str." AND $bis = 1";
		}
	}
	return $str;
}
*/

function Sol_exist( $usrstore )
{
	global $conn;

	$charsql = "select ID_CARAC from caracteres order by NUM";
	$chardata = $conn->select($charsql,'OBJECT');

	$str = " 1 ";

	for ( $i = 0; $i < count($chardata); $i++ )
	{
		if ( $usrstore[$i] == 1 )
		{
			$Carac = $chardata[$i]->ID_CARAC;
			$str .= " AND $Carac = 1";
		}
	}

	$charsql = "SELECT count(*) as Total from flore WHERE $str";
	$chardata = $conn->select($charsql,'OBJECT');

	// echo $chardata->Total;

	if ( $chardata->Total >= 1 )
	{
		return [$str,true];
	}
	else
	{
		return [$str,false];
	}
}


function Create_str( $usrstore, $Val )
{
	global $conn;

	if ( $Val >= 5 ) 
	{ 
		// echo " Surcharge ! ";
		return [" 1 ",true];
	}

	$C_Max = -1;
	$charsql = "select ID_CARAC from caracteres order by NUM";
	$chardata = $conn->select($charsql,'OBJECT');
	$str_Max = " 1 ";

	$T = Sol_exist($usrstore);
	if ( $T[1] )
	{
		$str_rep = $T[0];
		return[$str_rep,false];
	}

	for ( $i = 0; $i < sizeof($usrstore); $i++ )
	{
		if ( $usrstore[$i] == 1 )
		{
			$str = " 1 ";
			for ( $j = 0; $j < sizeof($usrstore); $j++ )
			{
				if ( ( $i <> $j ) AND ( $usrstore[$j] == 1 ) )
				{
					$bis = $chardata[$j]->ID_CARAC;
					$str = $str." AND $bis = 1";
				}
			}
			$sql = "SELECT count(*) AS Total FROM flore WHERE $str";	
			$Result = $conn->select($sql,'OBJECT');	

			// $C = count($Result);
			// echo " $str : count(str) =".sizeof($Result);
			// echo " Ici -> ".$Result->Total;

			if ( $Result->Total > $C_Max )
			{
				$str_Max = $str;
				$C_Max = $Result->Total;
			}
		}

	}

	// echo " str_Max = $str_Max \n";

	if ( $C_Max < 1 )
	{
		$Val++;
		// echo " PASSAGE A LA RÉCURSION ";
		$C_Max = 0;
		for ( $j = 0; $j < sizeof($usrstore); $j++ )
		{
			if ( $usrstore[$j] == 1 )
			{
				$usrstore_bis = array();
				$usrstore_bis = $usrstore;
				$usrstore_bis[$j] = 0;
				$T = Create_str( $usrstore_bis, $Val );
				$str = $T[0];
				$bool = $T[1];

				if ( $bool ) { return [" 1 ",true]; }

				$sql = "SELECT count(Code) AS Total FROM flore WHERE $str";	
				$Result = $conn->select($sql,'OBJECT');	

				// echo " Ici -> ".$Result->Total;
				//$C = count($Result->Code);
				//echo " count(str) = $C \n";

				if ( $Result->Total > $C_Max )
				{
					$str_Max = $str;
					$C_Max = $Result->Total;
				}
			}
		}
	}

	// echo " str_Max = $str_Max \n";
	return [$str_Max,false];
}


?>