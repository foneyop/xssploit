<?php
function jsmin($string) {
	$alphas = array_merge(range("a","z"), range("A", "Z"));
	$s1 = preg_replace("/\/\/.*?\n/", "\n", $string);
	$s2 = preg_replace("/\/\*.*?\*\//", "", $s1);
	$s3 = preg_replace("/\n/", " ", $s2);
	$s4 = preg_replace("/\s+/", " ", $s3);
	$s5 = preg_replace("/\s*([\,\=\;\{\(\)\}])\s*/", "$1", $s4);
	preg_match_all("/var\s+(\w+)/", $s5, $matches);
	$vars = array_unique($matches[1]);
	$repl = array();
	$vars2 = array();
	$repl2 = array();
	for ($i=0,$m=count($vars);$i<$m;$i++) {

		$vars2[] = "/\s+new\s+{$alphas[$i]}\s*/";
		$repl2[] = " new {$vars[$i]} ";

		$vars[$i] = "/([^a-zA-Z0-9]){$vars[$i]}/";
		$repl[] = "$1{$alphas[$i]}";

	}
	$s6 = preg_replace($vars, $repl, $s5);
	$s7 = preg_replace($vars2, $repl2, $s6);
	
	//print_r($vars2);
	//print_r($repl2);

	return $s7;
}

//$f = file_get_contents("/code/xssploit/modules/local_ip.js");
//echo jsmin($f);

?>
