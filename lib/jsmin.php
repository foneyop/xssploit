<?php

// serve a javascript file, doing minification as needed
function serve_javascript($file, $extraJs = "") {
	$parts = explode("/", $file);
	$fileName = end($parts);
	$obfile = OBFUSCATE_DIR . $fileName;

	if (OBFUSCATE_JS) {
		// stat both to find out the last modification time
		$st1 = stat($obfile);
		$st2 = stat($file);

		// if obfuscated file does not exist, or modification time is < orig
		//if (!file_exists($obfile) || $st1['mtime'] < $st2['mtime']) {
		if (true) {
			$script = file_get_contents($file);
			$p2 = jsmin($script . $extraJs);	
			file_put_contents($obfile, $p2);
		}
	}
	else {
		$script = file_get_contents($file);
		file_put_contents($obfile, $script);
	}

	// include the obfuscated file (this should usually be cached)
	include $obfile;
	echo "if(xss&&xss.cls){xss.cls();}";
}

// do simple JavaScript minification.   Might not work on all JS. 
function jsmin($string) {
	// only min if the JavaScript has a minification call
	if (!stristr($string, "xssmin"))
		return $string;

	// for replacing variable names (skip s)
	$alphas = array_merge(range("a", "r"), range("t","z"), range("A", "Z"));
	// remove the minification call
	$string = preg_replace("/xssmin\s*\(\)\s*\;/", "", $string);

	// remove 1 line comments
	$s1 = preg_replace("![^:\"']{1,1}//.*?\n!m", "\n", $string);
	$s1 = preg_replace("!^//.*!m", "\n", $s1);

	// remove new lines
	$s2 = preg_replace("/\n/m", " ", $s1);
	// remove multi line comments
	$s3 = preg_replace("!/\*.*?\*\/!m", "", $s2);
	// replace multiple white space with single whitespace
	$s4 = preg_replace("/\s+/m", " ", $s3);

	// smash things that don't need whitespace
	$s5 = preg_replace("/\s*([\,\=\;\{\(\)\}])\s*/", "$1", $s4);
	$s5 = preg_replace("/}([;\n]*)/m", "}$1\n", $s5);
	// find all declared variables and functions
	preg_match_all("/var\s+(\w+)/", $s5, $matches);
	preg_match_all("/function\s+(\w+)\(/", $s5, $matches2);
	// get a distinct list of declared variables / functions
	// TODO: look for string "new $vars" in script so we won't replace strings that are also object names
	//       then we won't have to re-replace them
	$vars = array_unique(array_merge($matches[1], $matches2[1]));

	// reverse sort by length (replace longest first so we don't replace vars that have a short var name in them)
	usort($vars, "len_sort");


	// 2 lists of variables to replace
	$vars1 = array();
	$repl1 = array();
	$vars2 = array();
	$repl2 = array();
	$i = 0;

	
	// create the list of replacement regexes
	foreach ($vars as $var) {
		//echo "$var \n";
		if (!stristr($var,"script") && !stristr($var, "xss") && strlen($var) > 1) {
			$vars2[] = "/([^a-zA-Z0-9])new\s+xs{$alphas[$i]}\s*/";
			$repl2[] = "$1new {$var}[^a-zA-Z0-9]";

			$vars1[] = "/([^a-zA-Z0-9\"']){$var}/";
			$repl1[] = "$1xs{$alphas[$i]}";
		}
		$i++;
	}

	// replace all declared varialbes with short names
	$s6 = preg_replace($vars1, $repl1, $s5);
	// if we have a variable name that is also a class, then replace it back...
	$s7 = preg_replace($vars2, $repl2, $s6);
	
	// replace options and remove the description and defaults
	$s8 = preg_replace("/xss.opt\(\"([^\"]+)\".*?\)/", "xss.opt(\"$1\")", $s7);
	
	//var uname = xss.opt("user_field", "the name attribute of the username field", "username");
	return $s8;
}

function len_sort($a, $b) {
	return strlen($b)-strlen($a);
}


//echo serve_javascript("/code/xssploit/modules/local_ip.js");


?>
