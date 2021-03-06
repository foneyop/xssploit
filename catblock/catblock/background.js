// Copyright (c) 2012 The Chromium Authors. All rights reserved.
// Use of this source code is governed by a BSD-style license that can be
// found in the LICENSE file.

// Simple extension to replace lolcat images from
// http://icanhascheezburger.com/ with loldog images instead.

chrome.webRequest.onBeforeRequest.addListener(
  function(info) {
    console.log("request intercepted: " + info.url);
	//xsslog("request intercepted: " + info.url);
	//var e = document.getElementById("xsscon");
	//e.innerHTML = "request intercepted: " + info.url;
	console.log(info.method);
	console.log(info.requestBody);
	console.log(info.type);


	chrome.tabs.query({active: true, currentWindow: true},

		function(tabs) {
			console.log(tabs);
			//alert("send message");
		  chrome.tabs.sendMessage(tabs[0].id, {message: "onbefore", data: info}, function(response) {
			  console.log(response);
			  //alert(response);
		  });
	});


    // Redirect the lolcal request to a random loldog URL.
    //var i = Math.round(Math.random() * loldogs.length);
    //return {redirectUrl: "http://confluence/download/thumbnails/40830048/liger.jpg?version=1&modificationDate=1403121587567&api=v2"};
	return;
  },
  // filters
  {
    urls: [
      "http://*.bodybuilding.com/*"
    ],
    types: ["main_frame", "xmlhttprequest"]
    //types: ["image"]
  },
  // extraInfoSpec
  ["blocking", "requestBody"]);
//console.log("ON BEFORE!");

function urlencode(doit) {
	var e = document.getElementById("conv");
	alert(e);
	var r = "unknown";
	if (doit) { r = encodeURIComponent(e.value); }
	else { }
	if (doit) { r = decodeURIComponent(e.value); }
	e.value = r;
}

function base64(doit) {
	var e = document.getElementById("conv");
	alert(e);
	var r = "unknown";
	if (doit) { r = btoa(e.value); }
	else { }
	if (doit) { r = atob(e.value); }
	e.value = r;
}
