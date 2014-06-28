debug("append crash");
var imagediv = xssce("div");
imagediv.id = "xssimg";
imagediv.innerHTML = "THE DIV!";
//xsssa(imagediv, "id");
document.body.appendChild(imagediv);

console.log("IFRAMED DOC:");
console.log(window.document);

//ci("http://media1.giphy.com/media/13AN8X7jBIm15m/200.gif", "position:absolute;top:0;left:0;z-index:1677270", "hookbody");

var ifr = $('<iframe>').css({
		 position: 'fixed',
		 width:463,	
		height:200,
		 top: 0,
		 right: 0,
		 border: 0,
		 background: '#fff',
		 overflow: "hidden",
		"z-index":50
	 }).attr('src', "http://media1.giphy.com/media/13AN8X7jBIm15m/200.gif").appendTo("body");
//"http://infosec3/hook.html").appendTo('body');


debug("appended");
