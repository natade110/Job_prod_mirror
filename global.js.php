<script type='text/javascript'>
function fireMyPopup(popup_name, the_width, the_height) {
	<!-- Due to different browser naming of certain key global variables, we need to do three different tests to determine their values -->

	// Determine how much the visitor had scrolled
	var scrolledX, scrolledY;
	if( self.pageYOffset ) {
	  scrolledX = self.pageXOffset;
	  scrolledY = self.pageYOffset;
	} else if( document.documentElement && document.documentElement.scrollTop ) {
	  scrolledX = document.documentElement.scrollLeft;
	  scrolledY = document.documentElement.scrollTop;
	} else if( document.body ) {
	  scrolledX = document.body.scrollLeft;
	  scrolledY = document.body.scrollTop;
	}
	// Determine the coordinates of the center of browser's window
	var centerX, centerY;
	if( self.innerHeight ) {
	  centerX = self.innerWidth;
	  centerY = self.innerHeight;
	} else if( document.documentElement && document.documentElement.clientHeight ) {
	  centerX = document.documentElement.clientWidth;
	  centerY = document.documentElement.clientHeight;
	} else if( document.body ) {
	  centerX = document.body.clientWidth;
	  centerY = document.body.clientHeight;
	}

	var leftOffset = scrolledX + (centerX - the_width) / 2;
	var topOffset = scrolledY + (centerY - the_height) / 2;
	
	document.getElementById(popup_name).style.top = topOffset + "px";
	document.getElementById(popup_name).style.left = leftOffset + "px";
	document.getElementById(popup_name).style.display = "block";
	
	//set opacity 
	setOpacity(popup_name,0);
	//fade in pop up
	fadeInMyPopup(popup_name);
}

function fadeInMyPopup(for_what) {
	//alert(for_what);
	for( var i = 0 ; i <= 100 ; i++ ){		
		setTimeout( 'setOpacity("'+for_what+'",' + (i / 10) + ')' , 4 * i );
	}
}

function fireInThenFadeOut(popup_name, the_width, the_height) {
	
	fireMyPopup(popup_name,the_width,the_height);
	setTimeout('fadeOutMyPopup("'+popup_name+'")', 5000 );//fadeout in 5 sec
	
}

function fadeOutMyPopup(for_what) {
	for( var i = 0 ; i <= 100 ; i++ ) {
		setTimeout( 'setOpacity("'+for_what+'",' + (10 - i / 10) + ')' , 4 * i );
	}
	setTimeout('closeMyPopup("'+for_what+'")', 400 );
}
function fadeOutThenOpen(fade_what,open_what,new_popup_width, new_popup_height) {
	for( var i = 0 ; i <= 100 ; i++ ) {
		setTimeout( 'setOpacity("'+fade_what+'",' + (10 - i / 10) + ')' , 4 * i );
	}
	setTimeout('closeMyPopup("'+fade_what+'")', 400 );
	document.getElementById(open_what).style.display = "block";
	//alert(new_popup_width);
	//alert(new_popup_height);
	fireMyPopup(open_what, new_popup_width, new_popup_height);
}

function closeMyPopup(for_what) {
	//close msg popup then show mail_sent
	//
	
	document.getElementById(for_what).style.display = "none";
	//document.getElementById("mymail_sent").style.display = "block";
}

function setOpacity( for_what,value ) {
	document.getElementById(for_what).style.opacity = value / 10;
	document.getElementById(for_what).style.filter = 'alpha(opacity=' + value * 10 + ')';
}

function doHoverOrHide(is_hover, for_what){
	if(is_hover == 1){
		//show swap image
		document.getElementById(for_what+"icon").style.display = "none";
		document.getElementById(for_what+"roll").style.display = "";
	}else{
		document.getElementById(for_what+"icon").style.display = "";
		document.getElementById(for_what+"roll").style.display = "none";
	}
}

function doAddHi(to_who) {
	
	makePOSTRequest("http://www.thailovelines.com/ajax_tll/doAddHi.php","userid="+to_who);
	
}

function makePOSTRequest(url, parameters) {
  http_request = false;
  if (window.XMLHttpRequest) { // Mozilla, Safari,...
	 http_request = new XMLHttpRequest();
	 if (http_request.overrideMimeType) {
		// set type accordingly to anticipated content type
		//http_request.overrideMimeType('text/xml');
		http_request.overrideMimeType('text/html');
	 }
  } else if (window.ActiveXObject) { // IE
	 try {
		http_request = new ActiveXObject("Msxml2.XMLHTTP");
	 } catch (e) {
		try {
		   http_request = new ActiveXObject("Microsoft.XMLHTTP");
		} catch (e) {}
	 }
  }
  if (!http_request) {
	 alert('Cannot create XMLHTTP instance');
	 return false;
  }
  
  http_request.onreadystatechange = alertContents;
  http_request.open('POST', url, true);
  http_request.setRequestHeader("Content-type", "application/x-www-form-urlencoded;");
  http_request.setRequestHeader("Content-length", parameters.length);
  http_request.setRequestHeader("Connection", "close");

  http_request.send(parameters);
  
 
}

//just do this so after request is done it calls to "alertContents2" instead
function makePOSTRequest2(url, parameters) {
  http_request = false;
  if (window.XMLHttpRequest) { // Mozilla, Safari,...
	 http_request = new XMLHttpRequest();
	 if (http_request.overrideMimeType) {
		// set type accordingly to anticipated content type
		//http_request.overrideMimeType('text/xml');
		http_request.overrideMimeType('text/html');
	 }
  } else if (window.ActiveXObject) { // IE
	 try {
		http_request = new ActiveXObject("Msxml2.XMLHTTP");
	 } catch (e) {
		try {
		   http_request = new ActiveXObject("Microsoft.XMLHTTP");
		} catch (e) {}
	 }
  }
  if (!http_request) {
	 alert('Cannot create XMLHTTP instance');
	 return false;
  }
  
  http_request.onreadystatechange = alertContents2;
  http_request.open('POST', url, true);
  http_request.setRequestHeader("Content-type", "application/x-www-form-urlencoded;");
  http_request.setRequestHeader("Content-length", parameters.length);
  http_request.setRequestHeader("Connection", "close");

  http_request.send(parameters);
  
 
}


function doAddFavorite(to_who) {

	makePOSTRequest("http://www.thailovelines.com/ajax_tll/doAddFav.php","userid="+to_who);
	
}

function doSendFlirt(param){
	//alert(param);
	makePOSTRequest('http://www.thailovelines.com/ajax_tll/doSendFlirt.php', param);
}

function doSendMessage(param){
	document.getElementById('message_button').disabled = true;
	getstr = getFormObject(document.getElementById('myform'));
	//alert(getstr);
	makePOSTRequest('http://www.thailovelines.com/ajax_tll/doSendMessage.php', getstr);
}

function doSendTip(param){
	fadeOutMyPopup("tip_popup");
	getstr = getFormObject(document.getElementById('tipform'));
	//alert(getstr);
	makePOSTRequest('http://www.thailovelines.com/ajax_tll/doSendTip.php', getstr);
}



function getFormObject(obj) {
	//var poststr = "txt_subject=" + encodeURI( document.getElementById("txt_subject").value ) +
	//			"&txt_message=" + encodeURI( document.getElementById("txt_message").value );
	//makePOSTRequest('post.php', poststr);
	//var getstr = "?";
	var getstr = "";
	//alert(obj.elements.length);
	for (i=0; i<obj.getElementsByTagName("input").length; i++) {
		//alert(obj.getElementsByTagName("input")[i].type);
		if (obj.getElementsByTagName("input")[i].type == "text") {
			getstr += obj.getElementsByTagName("input")[i].name + "=" + 
			obj.getElementsByTagName("input")[i].value + "&";
		}
		if (obj.getElementsByTagName("input")[i].type == "hidden") {
			getstr += obj.getElementsByTagName("input")[i].name + "=" + 
			obj.getElementsByTagName("input")[i].value + "&";
		}
		if (obj.getElementsByTagName("input")[i].type == "checkbox") {
			if (obj.getElementsByTagName("input")[i].checked) {
				getstr += obj.getElementsByTagName("input")[i].name + "=" + 
				obj.getElementsByTagName("input")[i].value + "&";
			} else {
				getstr += obj.getElementsByTagName("input")[i].name + "=&";
			}
		}
		if (obj.getElementsByTagName("input")[i].type == "radio") {
			if (obj.getElementsByTagName("input")[i].checked) {
				getstr += obj.getElementsByTagName("input")[i].name + "=" + 
				obj.getElementsByTagName("input")[i].value + "&";
			}
		}  
		
	}

	//alert(obj.getElementsByTagName("textarea").length);
	for (i=0; i<obj.getElementsByTagName("textarea").length; i++) {
		getstr += obj.getElementsByTagName("textarea")[i].name + "=" + 
			obj.getElementsByTagName("textarea")[i].value + "&";
	}
	
	for (i=0; i<obj.getElementsByTagName("select").length; i++) {
		var sel = obj.getElementsByTagName("select")[i];
		getstr += sel.name + "=" + sel.options[sel.selectedIndex].value + "&";
	}
		
	return getstr;
}


function doDeleteFile(the_id, the_parent_table){
												
	$choice = confirm('แน่ใจหรือว่าคุณต้องการลบไฟล์นี้ ไฟล์ที่ถูกลบจะไม่สามารถเรียกกลับคืนมาได้'); 
	
	if($choice){
		//alert(the_id);
		var param = 'yoes=san&id='+the_id;
		param = ((the_parent_table != "undefined") && (the_parent_table != ""))? (param + '&parenttable='+the_parent_table) : param;
		document.getElementById("file_"+the_id).style.display = 'none';
		document.getElementById("loading_"+the_id).style.display = '';
		makePOSTRequest('./ajax_delete_file.php', param);
	}else{
		return false;
	}
	
}


//person code thingie
function autotab(original,destination){
	if (original.getAttribute&&original.value.length==original.getAttribute("maxlength")){
		destination.focus();	
		destination.select();
					
	}
}

function deleteBefore(e, orignal, destination)
{
													
	if(window.event) // IE
	  {
		 // alert(e.keyCode);
		  
		  if(e.keyCode == 8 && orignal.value.length == 0){									
			destination.value = "";
			destination.focus();	
			destination.select();
		  }
		  
	  }
	else if(e.which) // Netscape/Firefox/Opera
	  {
		//alert(e.which);
		if(e.which == 8 && orignal.value.length == 0){
			//alert("do ff delete");
			destination.value = "";
			destination.focus();	
			destination.select();
		 }
	  }


}
</script>