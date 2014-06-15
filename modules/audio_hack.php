<?
# Send sound clip from the movie hackers to the targeted browsers.
?> 
debiug("start audio hack");
var xssurl = "http://"+sploit+"/modules/hkhack.mp3";
var audio = new Audio(xssurl);
audio.play();
debiug("audio played");
