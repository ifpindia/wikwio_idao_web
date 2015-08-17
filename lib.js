var svgns = "http://www.w3.org/2000/svg";
var svgdoc;

function changelevel()
{
	document.forms[0].action = "results.php";
	document.forms[0].submit();
}

function showhelp()
{
	document.forms[0].action = "help.php";
	document.forms[0].submit();
}

function showabout()
{
	document.forms[0].action = "about.php";	
	document.forms[0].submit();
}

function shownext()
{
	document.forms[0].action = "searchnext.php";
	document.forms[0].submit();
}

function showcancel()
{
	document.forms[0].action="redrawdefault.php";
	document.forms[0].submit();
}

function showspecies()
{
	document.forms[0].action = "specieslist.php";
	document.forms[0].submit();
}

function showsearch()
{	
	document.forms[0].action="search.php";
	document.forms[0].submit();	
}

function showquest(charname)
{
	document.forms[0].action = "selectquest.php";
	document.forms[0].txtcharname.value = charname;
	document.forms[0].submit();
}


function showresults()
{
	document.forms[0].action = "results.php";
	document.forms[0].submit();
}

function replacechar(cname)
{
	document.forms[0].txtcarname.value = cname;
	document.forms[0].submit();
}


function showerrors(spcode)
{
	//alert (spcode);
	document.forms[0].txtspcode.value = spcode;
	document.forms[0].action = "contra.php";
	document.forms[0].submit();
}



 function markselected(cid) {	
 try
  {
	if (returnie())
		svgdoc = document.svgquest.getSVGDocument();
	else
		svgdoc = document.svgquest.contentDocument;		

	var bbox = svgdoc.getElementById(cid).getBBox();	
	
	var node = svgdoc.getElementById("imgtick");
	var nwidth = bbox.width;
	var newx = bbox.x+nwidth/2;
	var newy = bbox.y+2 +20;

	node.setAttribute("x", newx);
	node.setAttribute("y", newy);
 }
catch(err)
  {
	  txt="There was an error on this page -- " + cid + ".\n\n";
	  txt+="Error description: " + err.message + "\n\n";
	  txt+="Error description: " + err.description + "\n\n";
	  txt+="Click OK to continue.\n\n";
	  alert(txt);
  } 
 }
 
var http = createRequestObject();

function showtooltip(cid, tiptext)
{
	if (returnie())
		svgdoc = document.svgquest.getSVGDocument();
	else
		svgdoc = document.svgquest.contentDocument;	
		
	var mid = svgdoc.getElementById(cid);
	
	if (!(mid.hasAttribute("fill-opacity")))
		mid.setAttribute("style", "fill-opacity:0.85");
	
	var bbox = svgdoc.getElementById(cid).getBBox();	
	
	var rectbox = svgdoc.getElementById("recttooltip");
	var node = svgdoc.getElementById("textboxid");
	var nwidth = bbox.width;
	var newx = bbox.x+nwidth/2-23;
	var newy = bbox.y+2 +20;

	var child = node.firstChild;
	while (child != null)
	{
		if (child.nodeName == "tspan" && child.hasChildNodes()) 
				child.firstChild.nodeValue = " ";
		
		child = child.nextSibling;
	}

	rectbox.setAttribute("x", newx);
	rectbox.setAttribute("y", newy);	
	rectbox.setAttribute("style", "fill:#FAFE90;stroke:#474746;stroke-width:18;fill-opacity:0.85");
	node.setAttribute("x", newx + 100);
	node.setAttribute("y", newy + 400);
	node.setAttribute("style", "font:380px verdana,Trebuchet MS, sans-serif;pointer-events:none;fill:#black");

	/*response = tiptext;*/
	response = tooltips[cid];
	console.log("cid ="+cid+" tooltips ="+response+" tiptext ="+tiptext);
	response = response.replace("&gt;", ">");
	response = response.replace("&lt;", "<");
	response = response.replace("&amp;", "&");
	response = response.replace("&#232;", "è");
	response = response.replace("&#233;", "é");
	newtip = response;		   
//	new_width = response.length * 1.5 + response.length * 0.5 ;		   
	new_width = response.length * 2 ;		   
	calwidth = new_width + newx;
	node.firstChild.nodeValue = " ";
	flag = 1;
	
    if (calwidth > 23000)
    {	
		flag = 0;
		var mysplit = response.split(" ");
		newtip = "";
		remtip = ""
		addbr = mysplit.length/2;
		addbr = Math.floor(addbr);
		for(i = 0; i < mysplit.length; i++)
		{
			if (i <= addbr)
				newtip = newtip + mysplit[i] + " ";					
			else
				remtip = remtip + mysplit[i] + " ";
		}
		newstr = remtip.replace(/^s+|s+$/g,"");
		if (newstr.length > 0 )
		{
			// wrap the tooltip in two lines, if the tooltip is too big
			fspan = svgdoc.createElementNS(svgns, "tspan");
			var textnode = svgdoc.createTextNode(newtip);				
			fspan.appendChild(textnode);
			fspan.setAttribute("x", newx +2);
			fspan.setAttribute("y", newy+5);				
			node.appendChild(fspan);
			
			
			fspan = svgdoc.createElementNS(svgns, "tspan");
			var textnode = svgdoc.createTextNode(remtip);
			fspan.appendChild(textnode);
			fspan.setAttribute("x", newx+2);
			fspan.setAttribute("y", newy + 10);
			node.appendChild(fspan);
			
			rectbox.setAttribute("width", newtip.length * 1.5 + newtip.length * 0.35);
			rectbox.onclick = function() { parent.replacechar(cid)}
			node.onclick = function() { parent.replacechar(cid)}
			rectbox.setAttribute("height", 14);
		}					
		else
		   flag = 1
      }
      if (flag == 1) 
      {					
		rectbox.setAttribute("height", 6*100);	
		rectbox.setAttribute("width",new_width*100 + 300);
		node.firstChild.nodeValue = response;
		rectbox.onclick = function() { parent.replacechar(cid)}
		node.onclick = function() { parent.replacechar(cid)}
       }
}


function showrobotip(cid, tiptext)
{
	if (returnie())
		svgdoc = document.svgquest.getSVGDocument();
	else
		svgdoc = document.svgquest.contentDocument;	
		
	var bbox = svgdoc.getElementById(cid).getBBox();	
	
	var mid = svgdoc.getElementById(cid);
	if (!(mid.hasAttribute("fill-opacity")))
		mid.setAttribute("style", "fill-opacity:0.85");
	
	var rectbox = svgdoc.getElementById("recttooltip");
	var node = svgdoc.getElementById("textboxid");
	var nwidth = bbox.width;
	var newx = bbox.x + nwidth / 2 - 2500;
	if (newx < 0)
		newx = newx + 2000;
	var newy = bbox.y + 2; 	
	
	//response = tiptext;
	console.log(tooltips[cid] +" tiptext = "+tiptext+" cid="+cid);
	response = tooltips[cid];

	new_width = response.length * 1.5 + response.length * 0.4 + 6;			
	rectbox.setAttribute("width",new_width*100);
	
	calwidth = new_width + newx;
	if (calwidth > 240)
		newx = newx - 28;
		
	rectbox.setAttribute("x", newx);
	rectbox.setAttribute("y", newy);	
	rectbox.setAttribute("style", "fill:#FAFE90;stroke:#474746;stroke-width:18;fill-opacity:0.85");
	node.setAttribute("x", newx + 100);
	node.setAttribute("y", newy + 400);
	node.setAttribute("style", "font:380px verdana,Trebuchet MS, sans-serif;pointer-events:none;fill:#black");
	node.firstChild.nodeValue = response;
	rectbox.onclick = function() { parent.showquest(cid)}
	node.onclick = function() { parent.showquest(cid)}
}


function hidetooltip(cid)
{	
	if (returnie())
		svgdoc = document.svgquest.getSVGDocument();
	else
		svgdoc = document.svgquest.contentDocument;
	
	var mid = svgdoc.getElementById(cid);
	if (isNaN(parseInt(mid.getAttribute("fill-opacity"))))
	{
		//alert ("I am here")
		mid.setAttribute("style", "fill-opacity:1");
	}
	
	var rectbox = svgdoc.getElementById("recttooltip");
	var node = svgdoc.getElementById("textboxid");	
	rectbox.setAttribute("x", "-10000");
	rectbox.setAttribute("y", "-10000");
	node.setAttribute("x", "-10000");
	node.setAttribute("y", "-10000");
	//node.firstChild.nodeValue = " ";

	var child = node.firstChild;
	while (child != null)
	{
		//see if child is a tspan and has child nodes
		if (child.nodeName == "tspan" && child.hasChildNodes()) 
				child.firstChild.nodeValue = " ";
		
		child = child.nextSibling;
	}
}


function popupBlank(url) { 
	var proprietes;
	
	if (pop) {
	pop.window.close();
	}
	
	var top= 50;
	var left=50;
	
	width_ = screen.width - 200;
	height_ = screen.height - 300;
	proprietes = "toolbar=yes, location=yes, directories=yes, scrollbars=yes, resizable=yes, status=yes, menubar=yes, width="+width_+", height="+height_+",  left="+left+",top="+top+"";
	var pop=window.open(url,"1",proprietes);
	pop.focus();	
}

function returnie()
{
	if (navigator.userAgent.toLowerCase().indexOf('msie') != -1)
		return true;
	else
		return false;
}

function createRequestObject() {
    var ro;
    var browser = navigator.appName;

    if(browser == "Microsoft Internet Explorer"){
        ro = new ActiveXObject("Microsoft.XMLHTTP");
    }else{
        ro = new XMLHttpRequest();
    }
    return ro;
}

function pop(stype,that){
	$('.button').removeClass('active_btn').addClass('green');
	$(that).removeClass('green').addClass('active_btn');
	$(".newspecieslistwrapper").hide();
	 if(stype==1){
	 	
	 	$("#commonnames").show();
	 	$(".scrollnames").hide();
	 }
	 else if(stype==2){
	 
	 	$("#commonfamilies").show();
	 }
	 else if(stype==3){
	 
	 	$("#commonspecies").show();
	 	$(".scrollspecies").hide();
	 }else {
	 	$("#reversecontra").show();
	 	$(".scrollcontra").hide();
	 }

}


function speciesPopup(link){
	//alert(link);
	window.open(link, '_blank');
	
}

function check(code){
	document.forms[0].action = "contraspecies.php";	
	document.forms[0].code.value = code;
	document.forms[0].submit();
}