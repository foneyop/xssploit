// Copyright (c) 2012 The Chromium Authors. All rights reserved.
// Use of this source code is governed by a BSD-style license that can be
// found in the LICENSE file.


function xsslog(text) {
	console.log(text);
}

chrome.runtime.onMessage.addListener(
	function(request, sender, sendResponse) {
		//sender.tab ?  "from a content script:" + sender.tab.url : "from the extension");
		//console.log(sender.tab ?  "from a content script:" + sender.tab.url : "from the extension");
		console.log("from extension: ");	
		console.log(request);
		sendResponse({farewell: "goodbye"});

		if (!sender.tab && request.message == "onbefore") {
			alter_request(request);
		}
	}
);

// display a panel to edit the request parameters
function alter_request(request) {

}

console.log("CONTENT SCRIPT loaded!");
