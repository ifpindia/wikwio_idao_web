<?php
	//print_r($_GET);
	
	//htmlentities("text", ENT_QUOTES, "ISO-8859-1");
	header('Content-Type:text/plain;charset=ISO-8859-1');
	include_once("lib.php");
	getConn();	
	// show all community languages and the common names
	
	$stype = $_GET["searchtype"];
		
	if ($stype == "2")
	{
		$str = "";
		// show all the families
		//$str .= "<ul id=\"tree\">";
		$str .= "<li><strong>Families</strong>";
		$query = "select distinct(Famille) from flore order by Famille";
		$data = $conn->select($query, 'OBJECT');					
		$str .= "<ul>";
		for ($i = 0; $i < sizeof($data); $i++)
		{
			$str .= "<li>" . ucfirst(strtolower($data[$i]->Famille))  ;
			$sql = "select * from flore where Famille = '" . $data[$i]->Famille . "'";
			$familledata = $conn->select($sql, 'OBJECT');
			$str .= "<ul>";
			if (sizeof($familledata) > 1)
				for ($j= 0; $j < sizeof($familledata); $j++)
				{
					$key =  $familledata[$j]->Code;
                                        $url =  $familledata[$j]->portal_url;			
					$str .= "<li>&nbsp;&nbsp;<a href=\"$url\" target='_blank'>". $familledata[$j]->Espece  ."</a></li>";
				}
			else
			{
				$key =  $familledata->Code;
                                $url =  $familledata->portal_url;
				$str .= "<li>&nbsp;&nbsp;<a href=\"$url\" target='_blank'>". $familledata->Espece  ."</a></li>";						  }
			$str .= "</ul>";
			$str .= "</li>";
			
		}
		$str .= "</ul></li>";
		echo $str;
	}
	
	if ($stype == "3")
	{
		$str = "";
		// Show all the species
		//$str .= "<ul id=\"tree\">";
		$str .= "<li><strong>Espèces</strong>";
		$query = "select * from flore";
		$data = $conn->select($query, 'OBJECT');
		$str .= "<ul>";
		for ($i = 0; $i < sizeof($data); $i++)
		{
			$key =  $data[$i]->Code;
			$url = $data[$i]->portal_url;
			$str .= "<li>&nbsp;&nbsp;<a href=\"$url\" target='_blank'>" .  $data[$i]->Espece  . "</a></li>";
		}
		$str .= "</ul></li>";		
		echo $str;
	}
	
	if ($stype != "2" and $stype != "3")
	{
		$str = "";
		$langauageArray = array(1 => 'français');
		$langFull = $langauageArray[$stype];
		// echo $stype;
		$str .= "<li><strong>Common names in $langFull</strong>";
		$str .= "<ul>";
		
		
		$tablename = "communs_" .$langFull; //Edited by Sathish on 09-05-2014
							
		$sql = "select distinct(Nom) from $tablename order by Nom";						
		$langdata = $conn->select($sql, 'OBJECT');						
			for ($j = 0; $j < sizeof($langdata); $j++)
			{
				$langcname = $langdata[$j]->Nom;
				$str .=  "\n		<li>" .  $langcname;
				$str .=  "\n		<ul>";							
				$commonsql = "select a.Code,a.portal_url, a.Espece  from flore a, $tablename b where ";
				$commonsql .= 'a.Code = b.Code and b.Nom="' . mb_strtolower($langcname , 'ISO-8859-1'). '"';
				$commondata = $conn->select($commonsql, 'OBJECT');
				if (sizeof($commondata) > 1)
				{
					for ($k =0; $k < sizeof($commondata); $k++)
					{
						$key =  $commondata[$k]->Code;
						$url = $commondata[$k]->portal_url;
						$str .=  "\n		<li><a href=\"$url\" target='_blank'>". $commondata[$k]->Espece  ."</a></li>";
					}
				}
				else
				{
						$key =  $commondata->Code;
						$url = $commondata->portal_url;
						$str .=  "\n		<li><a href=\"$url\" target='_blank'>". $commondata->Espece  ."</a></li>";
				}
				$str .=  "\n</ul>"; // ul for species 
				$str .= "</li>";
			}
		$str .=  "\n</ul></li>"; //  ul for common names
		echo $str;
	}
	
?>

