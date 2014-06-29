debug("alert start");
xssm = xssopt("message");
alert(decodeURIComponent(xssm));
debug("sent message: ["+xssm+"]");
// send the debug message back without delay
hb();
