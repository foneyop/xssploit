//
//chrome.webNavigation.onBeforeNavigate.addListener(navigate_event);
console.log(chrome);
//'console.log(chrome.experimental.webNavigation);
chrome.experimental.webNavigation.onBeforeNavigate.addListener(function(details) {
	alert("NAVIGATE!");
	console.log(details);
	alert("LAST NAV");

});
console.log("IN CONTENT SCRIPT!");
alert("content script");

