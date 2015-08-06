<?php
	header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Date in the past
		include_once("lib.php");
		$lang = getLanguage($_GET['lang']);
		include_once('messages_'.$lang.'.php');
		getConn();
		
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"  "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<?php
	
	$ini = parse_ini_file("define.ini.php", TRUE);
	$title = $ini['website']['site_titre'];
	echo "<title>".$title."</title>";
	
?> 	
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1"/>
	<link rel="stylesheet" type="text/css" href="default.css">
	<link rel="stylesheet" href="css/main.css">
	<link rel="stylesheet" href="css/button.css">
<style type="text/css">
	
    
	input {
        border:solid 1px #ccc;
        border-radius: 5px;
        /*padding:7px 14px;*/
        padding:7px;
        width: 98% ;
        margin-bottom:10px
    }
input:focus {
    outline:none;
    border-color:#aaa;
}


.foo {
    float: left;
    width: 40px;
    height: 35px;
    /*margin: 5px;*/
    border-width: 1px;
    border-style: outset;
    border-color: rgba("0,0,0,.2");
}

.foo > a {
    
    display:block;
    text-decoration:none;
    margin-top: 5px;
    
}

.back-top {
    position: fixed;
    bottom: 30px;
    right: 70px;
}

.back-top a {
    width: 108px;
    display: block;
    text-align: center;
    font: 11px/100% Arial, Helvetica, sans-serif;
    text-transform: uppercase;
    text-decoration: none;
    color: #bbb;
    
    /* transition */
    -webkit-transition: 1s;
    -moz-transition: 1s;
    transition: 1s;
}
.back-top a:hover {
    color: #000;
}

/* arrow icon (span tag) */
.back-top span {
    width: 51px;
    height: 51px;
    display: block;
    margin-bottom: 7px;
    background: #E57818 url(img/scroll.png) no-repeat center center;
    
    /* rounded corners */
    -webkit-border-radius: 15px;
    -moz-border-radius: 15px;
    border-radius: 15px;
    
    /* transition */
    -webkit-transition: 1s;
    -moz-transition: 1s;
    transition: 1s;
}
.back-top a:hover span {
    background-color: #777;
}
		#clang {
			display: none;
		}
	</style>
</head>
<body>

	<form name="frmgeneral" method="post" action="selectquest.php">
		<div id="pageleft">
			<?php include_once('specieslistpage.php'); ?>
		</div>	
			
		
		<div id="pageright">
			<div id="header"><h1>WIKWIO</h1></div>
			<div id="navbuttons">
				<?php include_once('navbutton.php'); ?>
			</div>	
			<br>
			<?php				
				echo "<input type=\"hidden\" name=\"txtstore\" value=\"$store\" >";
				echo "<input type=\"hidden\" name=\"txtcharname\" >";
				calculateper();
				echo "<p class='result'>" . $_GLOBALS['topcount'] . " ".$menu_text['species']." ".$menu_text['at']." " . $_GLOBALS['pertop'] . "%</p>";
			?>
			
			<div class="lang_wrap">
					<a href="<?= $getUrl; ?>?lang=en" >English</a> | <a href="<?= $getUrl; ?>?lang=fr" >French</a>
			</div>
			
			
		</div>
	</form>
	
<script type="text/javascript" src="js/jquery-1.11.2.min.js"></script>
<script src="lib.js" type="text/javascript"></script>
<script type="text/javascript" src="js/list.min.js"></script>    
<script type="text/javascript" src="js/main.js"></script>

<script type="text/javascript">
        //alert($(".listofspec ul li").size());
$(document).ready(function(){		
        var optionscont = {
            page: $(".reversecontra ul li").size(),
            valueNames: [ 'anchor','' ]
        };
    
    var userListcont = new List('reversecontra', optionscont);
    
    scrollinTop();
    function scrollinTop(){
        $(".back-top").hide();
        
        // fade in .back-top
        $(function () {
          $(window).scroll(function () {
                           
                           if ($(this).scrollTop() > 100) {
                           
                           $('.back-top').fadeIn();
                           } else {
                           $('.back-top').fadeOut();
                           }
                           });
          
          // scroll body to 0px on click
          $('.back-top a').click(function () {
                                 $('body,html').animate({
                                                        scrollTop: 0
                                                        }, 800);
                                 return false;
                                 });
          });
    }
	
	
	
	
       var optionsvar1 = {
            page: $(".commonnames ul li").size(),
            valueNames: [ 'anchorname','' ]
        };
    
    var userListvar1 = new List('commonnames', optionsvar1);
    
    function scrolling(nnn){
        var pos = $("#"+nnn).position();
        $('html, body').animate( { scrollTop: pos.top }, 'slow' );
    }
	
	
	
	 //alert($(".listofspec ul li").size());
        var optionscont = {
            page: $(".reversecontra ul li").size(),
            valueNames: [ 'anchor','' ]
        };
    
    var userListcont = new List('reversecontra', optionscont);
    
    scrollinTop();
    function scrollinTop(){
        $(".back-top").hide();
        
        // fade in .back-top
        $(function () {
          $(window).scroll(function () {
                           
                           if ($(this).scrollTop() > 100) {
                           
                           $('.back-top').fadeIn();
                           } else {
                           $('.back-top').fadeOut();
                           }
                           });
          
          // scroll body to 0px on click
          $('.back-top a').click(function () {
                                 $('body,html').animate({
                                                        scrollTop: 0
                                                        }, 800);
                                 return false;
                                 });
          });
    }
	
	
	 //alert($(".listofspec ul li").size());
        var options = {
            page: $(".commonspecies ul li").size(),
            valueNames: [ 'anchor','' ]
        };
    
    var userList = new List('commonspecies', options);
	
	//alert($(".listofspec ul li").size());
        var optionsvar = {
            page: $(".commonfamilies ul li").size(),
            valueNames: [ 'anchornar','' ]
        };
    
    var userListvar = new List('commonfamilies', optionsvar);
	
});	
    </script>

	
</body>
</html>
