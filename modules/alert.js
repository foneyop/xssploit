debug("alert start");
xssm = xssopt("message", "the message to send to the browser", "The Matrix Has You.");
alert(decodeURIComponent(xssm));
debug("sent message: ["+xssm+"]");
// send the debug message back without delay
hb();
