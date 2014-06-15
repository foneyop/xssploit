<? # Send an alert dialog to the targeted browsers. ?>
var xssm = "<?=$message?>";
debug("alert start");
alert(decodeURIComponent(xssm));
debug("sent message: ["+xssm+"]");
// send the debug message back without delay
hb();
