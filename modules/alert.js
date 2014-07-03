xssmin();
xss.dbg("alert start");
function xssalert() {
	var msg = xss.opt("message", "the message to send to the browser", "The Matrix Has You.");
	alert(decodeURIComponent(msg));
	xss.dbg("sent message: ["+msg+"]");
	// send the debug message back without delay
	xss.hb();
}
xssalert();
