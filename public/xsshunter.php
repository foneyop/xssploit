<?php 
// TODO: get debug working
// TODO: finish xmlhttprequest hijack

require '../config.php';
$log = Logger::getLogger("app");
mt_srand();

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
		usleep(1);
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
			$rows = $db->select("get_param", "SELECT * FROM param", array("url_id" => $id, "name" => $key));
			if (!isset($rows[0])) {
				$guid = create_guid();
				$param_id = $db->insert("add_param", "param", array("url_id" => $id, "name" => $key, "guid" => $guid));
				$result[$part] = array("id" => $param_id, "url_id" => $id, "name" => $key, "guid" => $guid, "last_test" => null);
			}
			else {
				$result[$part] = $rows[0];
			}
		}

		serve_response($result);
	}

	if ($_GET["A"] == "test_param") {
		$db->update("update test", "param", array("last_test" => "!current_timestamp"), array("id" => $_GET['id']));
	}

	if ($_GET["A"] == "found_param") {
		
	}

	if ($_GET["A"] == "ignore") {
		$params = parse_url($_GET["url"]);
		$rows = $db->select("get_url", "SELECT * FROM url", array("domain" => $params['host'], "protocol" => $params['scheme'], "url" => $params['path']));
		if (isset($rows[0]['id'])) {
			$res = $db->update("ignore_url", "url", array("ignore" => 1), array("id" => $rows[0]['id']));
			if ($res) {
				serve_response(array("result" => "success.  url will be ignored forever."));
			}
		}
		serve_response(array("result" => "fail.  unable to find url, or update failed."));
	}


	

} catch(Exception $e) {
	echo "<pre>";
	debug_print_backtrace();
	die ($e);
}

?>
