
debug("start audio hack");
var xssurl = "http://"+sploit+"/hkhack.mp3";
var audio = new Audio(xssurl);
audio.play();
debug("audio played");
