// iterate over the DOM and remove any XSSploit scripts
try {
	var scripts = document.getElementsByTagName("script");
	for (var i=0; i<scripts.length; i++) {
		if (scripts[i].src.match(/https?:\/\/[^\/]+\/h\.php/)) {
			scripts[i].parentNode.removeChild(scripts[i]);
		}
	}
} catch (e) { }
// unhook the heart beat
if (xss.interval) { window.clearInterval(xss.interval); }
// remove the content we know about
xss.cls();
// remove xss name space variables
xss={};delete(xss);
