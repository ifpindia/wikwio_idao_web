<?php
	getConn();			
	getParameters();		
	apparrayinit();
	echo "<p class='result'>" . $_GLOBALS["no_species"] .  " ".$menu_text['species']." ".$menu_text['at']." 0 %</p>";
	ob_end_flush();
?>


