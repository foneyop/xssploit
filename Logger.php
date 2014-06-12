<?php
/**
 * This needs to be included in your sysconf.php
 * PHP errors will be redirected to this logger as well.
 * Logger supports web output (currently incorporated into BodyWrap.xhtml),
 * file output (set $GLOBALS['LOGFILE'] to writable file)
 * syslog output (set $GLOBALS['LOGHOST'] to syslog server)
 * <code>
 * $log = Logger::getLogger('logName');
 * $log->logDebug('This is a debug message');
 * </code>
 *
 * @category PHP
 * @package utilities
 * @see set_error_handler
 * @copyright Copyright, 2008 Bodybuilding.com
 * @author Cory
 * @version 1.2
 */

// this is our timezone
date_default_timezone_set('America/Boise');

/**
 * logger, similar to Log4J.  referenced in sysconf.php, so use it everywhere.
 * <code>
 * $log = Logger::getLogger('logName');
 * $log->logDebug('This is a debug message');
 * </code>
 * @copyright Copyright, 2008 Bodybuilding.com
 * @author Cory
 * @version 1.2
 */
class Logger
{
	// the log handle, and name
	protected $_logName;
	// min error level to save
	protected $_logLevel;
	// the format strings forthe log levels
	protected $_formatStr;
	// bool, true saves logs only at script exit
	protected $_volatile;

	// the syslog server to log to
	protected static $_logHost;
	// log socket to send syslog data to
	// log file to log to
	protected static $_logFile;

	// all loggers
	protected static $_loggers = array();
	// all log messages for this logger
	protected static $_entries = array();
	// logger creation time
	protected static $_creationTime;

	// log levels
	const DISABLE = -1;
	const TRACE = 0;
	const DEBUG = 1;
	const INFO = 2;
	const WARN = 3;
	const ERROR = 4;
	const FATAL = 5;
	const NONE = 98;
	const VOLATILE = 99;

	// syslog facilitys
	const SYSLOG_USER = 1;
	const SYSLOG_AUTH = 10;
	const SYSLOG_LOCAL0 = 16;
	const SYSLOG_LOCAL1 = 17;
	const SYSLOG_LOCAL2 = 18;
	const SYSLOG_LOCAL3 = 19;
	const SYSLOG_LOCAL4 = 20;
	const SYSLOG_LOCAL5 = 21;
	const SYSLOG_LOCAL6 = 22;
	const SYSLOG_LOCAL7 = 23;

	// syslog levels
	const SYSLOG_EMERG = 0;
	const SYSLOG_ALRET = 1;
	const SYSLOG_CRITICAL = 2;
	const SYSLOG_ERROR = 3;
	const SYSLOG_WARN = 4;
	const SYSLOG_NOTICE = 5;
	const SYSLOG_INFO = 6;
	const SYSLOG_DEBUG = 7;

	// the syslog level and facility
	protected $_facility = Logger::SYSLOG_LOCAL0;
	protected $_severity = Logger::SYSLOG_NOTICE;

	/**
	 * a private constructor forces you to use the singleton static methods
	 * @param string $name the name of the logger, this is the logging facility
	 *   that is used in the log format string
	 * @param boolean $volatile if true, log messages will be written to disk or
	 *   syslog on object destruction, this happens AFTER the page is sent to
	 *   the browser.
	 */
	private function __construct($name = false, $volatile = false)
	{
        $name = strtolower($name);

		// allow global reset of volative logging
		if (isset($GLOBALS['LOGNONVOLATILE']))
			$volatile = Logger::VOLATILE;

		// the logger instance name and handle
		$this->_logName = $name;
		// the log level for this logger
		$this->_logLevel = Logger::FATAL;
		// multiple log formats for different log levels
		$this->_formatStr = array ();
		// a volitle logger only saves messages on script end
		$this->_volatile = $volatile;

		// override the defaults if sysconf or appconf has overriden our level and format
		if (isset($GLOBALS['LOGFORMAT']))
			$format = $GLOBALS['LOGFORMAT'];
		else if (isset($GLOBALS['LOGFORMAT-' . $name]))
			$format = $GLOBALS['LOGFORMAT-' . $name];
		else
			$format = '%v[%w]: [%I:%r:%n:%p]: %m';

		// setup the logformats for each level
		$this->_formatStr[Logger::TRACE] = $format;
		$this->_formatStr[Logger::DEBUG] = $format;
		$this->_formatStr[Logger::INFO] = $format;
		$this->_formatStr[Logger::WARN] = $format;
		$this->_formatStr[Logger::ERROR] = $format;
		$this->_formatStr[Logger::FATAL] = $format;
		if (isset($GLOBALS['LOGFORMAT:TRACE']))
			$this->_formatStr[Logger::TRACE] = $GLOBALS['LOGFORMAT:TRACE'];
		if (isset($GLOBALS['LOGFORMAT:DEBUG']))
			$this->_formatStr[Logger::DEBUG] = $GLOBALS['LOGFORMAT:DEBUG'];
		if (isset($GLOBALS['LOGFORMAT:INFO']))
			$this->_formatStr[Logger::INFO] = $GLOBALS['LOGFORMAT:INFO'];
		if (isset($GLOBALS['LOGFORMAT:WARN']))
			$this->_formatStr[Logger::WARN] = $GLOBALS['LOGFORMAT:WARN'];
		if (isset($GLOBALS['LOGFORMAT:ERROR']))
			$this->_formatStr[Logger::ERROR] = $GLOBALS['LOGFORMAT:ERROR'];
		if (isset($GLOBALS['LOGFORMAT:FATAL']))
			$this->_formatStr[Logger::FATAL] = $GLOBALS['LOGFORMAT:FATAL'];

		// set the loglevel to the sys/app conf
        if (isset($GLOBALS['LOGLEVEL-' . $name]))
			$this->_logLevel = $GLOBALS['LOGLEVEL-' . $name];
		else if (isset($GLOBALS['LOGLEVEL']))
			$this->_logLevel = $GLOBALS['LOGLEVEL'];

		// volatile loggers don't write the log data until after the page is displayed
		// becuase of this we only connect to persistant storage on construction
		// if we arn't volatile
		if ($this->_volatile != Logger::VOLATILE)
			$this->connectStorage();
	}

	/**
	 * writes volitle logger's log data to disk, after script execution
	 * or on logger destruction (same thing)
	 * @since 1.2
	 **/
	public function __destruct()
	{

		// save the log to persitant storage
		if ($this->_volatile == Logger::VOLATILE)
		{
			$this->connectStorage();
			// loop over each
			do
			{
				// get the next message
				$message = array_shift(Logger::$_entries);
				if ($message == null)
					continue;

				// write to disk
				if (Logger::$_logFile)
					fwrite(Logger::$_logFile, $message[2] . "\n");
				
			}
			while ($message != null);
		}

        Logger::$_logFile = false;
	}

	/**
	 * connect to persistant storage for saving log messages.
	 * connects to the logfile, and the remote syslog udp server IF they are
	 * configured.
	 * MUST call this method before writing to disk as $_logFile or sending
	 * @since 1.2
	 */
	private function connectStorage()
	{
		// open the log file
		if (!isset (Logger::$_logFile) && isset($GLOBALS['LOGFILE']))
			Logger::$_logFile = fopen($GLOBALS['LOGFILE'], 'a+');

	}

	/**
	 * static singleton constructor.  Always create a rootLogger if one does not exist yet
	 * @param string $name the logger name (similar to log facility, will be added to each log message
	 * @param boolean $volatile true if this is a volaitle logger
	 *  (write's log as script end, not when messages arrive)
	 * @return Logger the new logger, ready for logging.
	 */
	public static function getLogger($name = false, $volatile = true)
	{
		// return an already constructed logger of this name (singleton-ish)
		if (isset(Logger::$_loggers[$name]))
			return Logger::$_loggers[$name];

		return Logger::$_loggers[$name] = new Logger($name, $volatile);
	}

	/**
	 * get an HTML formatted version of the log.  Gives you a div#logger.
	 * styles in bodyspace/styles.css can pretty print the HTML.
	 * @return string hidden HTML display of the log
	 * @since 1.2
	 */
	public static function getWebLog()
	{
                // These static variables look stupid, but they're to fix a weird bug. Keep 'em.
                static $infoLevel = Logger::INFO;  
                static $traceLevel = Logger::TRACE;
                static $errorLevel = Logger::ERROR;
                static $warnLevel = Logger::WARN;
                static $debugLevel = Logger::DEBUG;
                static $fatalLevel = Logger::FATAL;

		global $diagnostics;
		$factory = BBUserFactory::getInstance();
		$user = $factory->getCurrentUser();
		if ($user->getPointsTotal() != 999999)
			return;

		$output = '<div id="logger">';

		foreach (Logger::$_entries as $entry)
		{
			$entry[2] = str_replace("\n", '<br/>', $entry[2]);
			if ($entry[1] === $errorLevel || $entry[1] === $fatalLevel)
				$output .= "<br><span class='red'>$entry[2]</span>\n";
			else if ($entry[1] == $warnLevel)
				$output .= "<br><span class='yellow'>$entry[2]</span>\n";
			else if ($entry[1] == $infoLevel)
				$output .= "<br><span class='green'>$entry[2]</span>\n";
			else if ($entry[1] == $debugLevel)
				$output .= "<br><span class='debug'>$entry[2]</span>\n";
			else if ($entry[1] == $traceLevel)
				$output .= "<br><span class='light'>$entry[2]</span>\n";
			else
				$output .= "<br>$entry[2]\n";
		}
		if ($diagnostics)
			foreach ($diagnostics as $key => $value)
				$output .= "<br><span style='color: blue'>$key in: $value secs</span>\n";
		$output .= '</div>';
		return $output;
	}

	/**
	 * set the minimum log level for the logger to actually log.
	 * available log levels are Logger::FATAL, ERROR, WARN, INFO, DEBUG, TRACE
	 * $log->_setLevel(Logger::$ERROR);
	 * $log->_logWarn('this will not log');
	 * $log->_logError('this will log');
	 * @param integer $level one of the const logging levels
	 * @see Logger logger const settings
	 */
	public function setLevel($level = false)
	{
		$this->_logLevel = $level;
	}

	/**
	 * set the logging format.  The default is: {%d [%n:%p] %m}
	 * unless the $GLOBAL variable LOGFORMAT is set, then that will be used instead
	 * setting this method will override the LOGFORMAT, but this can be overriden
	 * with the FORCELOGFORMAT $GLOBAL.
	 *
	 *
	 * Substitute symbol
	 * %d{dd/MM/yy HH:MM:ss } Date
	 * %t{HH:MM:ss } Time
	 * %w the logger instance id (sesison tracking)
	 * %r Milliseconds since logger was created
	 * %n logger name
	 * %p Level
	 * %m user-defined message
	 *
	 * %S Server Name
	 * %s PHP script name
	 * %I IP address of browser
	 * %H the request url
	 * %U HTTP user agent
	 * %Ccookie_name COOKIE content
	 * %u mybb2 cookie user slug
	 * %i mybb2 cookie user id
	 *
	 * %f File name
	 * %F complete File path
	 * %L Line number
	 * %M Method name
	 * %A Method arguments
	 * %B a formatted backtrace
	 * Caution: %f, %F, %L, %M, %A are slow formats
	 * @param string $format the format stringto set forthe logger
	 * @param integer $level the logging level to set the format for
	 * @return nothing
	 */
	public function setFormat($format = '%d [%n:%p] %m', $level = false)
	{
		if ($level == false)
		{
			$this->_formatStr[Logger::TRACE] = $format;
			$this->_formatStr[Logger::DEBUG] = $format;
			$this->_formatStr[Logger::INFO] = $format;
			$this->_formatStr[Logger::WARN] = $format;
			$this->_formatStr[Logger::ERROR] = $format;
			$this->_formatStr[Logger::FATAL] = $format;
		}
		else
			$this->_formatStr[$level] = $format;
	}

	/**
	 * return the current logging level
	 * @return integer the logging level as an integer
	 */
	public function getLevel()
	{
		return $this->_logLevel;
	}

	/**
	 * dump the error log to STDOUT for THIS logger.  The root logger
	 * (Logger::getLogger('rootLogger')) will dump all errors
	 */
	public function dumpLog($dumpLevel = false)
	{
		$this->logFatal('depricated method "dumpLog" called');
	}

	/**
	 * return the actual logging entries in the format:
	 * array (facility, level, message);
	 * @return array the log entries. each entry is of the form:
     *  array((string)logName, (int)logLevel, (string)message)
	 */
	public function getEntries()
	{
		return Logger::$_entries;
	}

	/**
	 * add a log line of value TRACE to the log journal
	 * @param string $message the message to log
	 * @return false;
	 */
	public function trace($message)
	{
                // These static variables look stupid, but they're to fix a weird bug. Keep 'em.
                static $infoLevel = Logger::INFO;  
                static $traceLevel = Logger::TRACE;

		// don't log if the log level is too low
                if ($this->_logLevel > $infoLevel)
                        return false;

		// format the mesage
		$message = $this->msgFormat($message, 'trace', $traceLevel);

		// add the log line to our entries
                if(class_exists("Logger")) // This is a terrible check having to be done for BOOMS-1842.
			Logger::$_entries[] = array($this->_logName, (int)$traceLevel, $message);

		$this->dispatch($traceLevel, $message);
		return false;
	}


	/**
	 * add a log line of value DEBUG to the log journal
	 * @param string $message the message to log
	 * @return false;
	 */
	public function debug($message)
	{
		// don't log if the log level is too low
		if ($this->_logLevel > Logger::DEBUG)
			return false;

		// format the mesage
		$message = $this->msgFormat($message, 'debug', Logger::DEBUG);

		// add the log line to our entries
		Logger::$_entries[] = array($this->_logName, LOGGER::DEBUG, $message);
		$this->dispatch(LOGGER::DEBUG, $message);
		return false;
	}

	/**
	 * add a log line of value INFO to the log journal
	 * @param string $message the message to log
	 * @return false;
	 */
	public function info($message)
	{
		// don't log if the log level is too low
		if ($this->_logLevel > Logger::INFO)
			return false;

		// format the mesage
		$message = $this->msgFormat($message, 'info', Logger::INFO);
		// add the log line to our entries

		Logger::$_entries[] = array($this->_logName, LOGGER::INFO, $message);
		$this->dispatch(LOGGER::INFO, $message);
		return false;
	}

	/**
	 * add a log line of value WARN to the log journal
	 * @param string $message the message to log
	 * @return false;
	 */
	public function warn($message)
	{
		// don't log if the log level is too low
		if ($this->_logLevel > Logger::WARN)
			return false;

		// format the mesage
		$message = $this->msgFormat($message, 'warn', Logger::WARN);

		// add the log line to our entries
		Logger::$_entries[] = array($this->_logName, LOGGER::WARN, $message);
		$this->dispatch(LOGGER::WARN, $message);
		return false;
	}

	/**
	 * add a log line of value ERROR to the log journal
	 * @param string $message the message to log
	 * @return false;
	 */
	public function error($message)
	{
		// don't log if the log level is too low
		if ($this->_logLevel > Logger::ERROR)
			return false;

		// format the mesage
		$message = $this->msgFormat($message, 'error', Logger::ERROR);

		// add the log line to our entries
		Logger::$_entries[] = array($this->_logName, LOGGER::ERROR, $message);
		$this->dispatch(LOGGER::ERROR, $message);
		return false;
	}

	/**
	 * add a log line of value FATAL to the log journal
	 * @param string $message the message to log
	 * @return false;
	 */
	public function fatal($message)
	{
		// don't log if the log level is too low
		if ($this->_logLevel > Logger::FATAL)
			return false;

		// format the mesage
		$message = $this->msgFormat($message, 'fatal', Logger::FATAL);

		// add the log line to our entries
		Logger::$_entries[] = array($this->_logName, LOGGER::FATAL, $message);
		$this->dispatch(LOGGER::FATAL, $message);
		return false;
	}


	/**
	 * send a message to disk, and/or the syslog.
	 *
	 * @param integer $level the logging level for the message
	 * @param string $message the messsage to log
	 * @return nothing
	 */
	private function dispatch($level, $message)
	{
		// dont' write out volitile logger data
		if (class_exists("Logger") && $this->_volatile == Logger::VOLATILE)
			return;

        // send log data to the file
        if (class_exists("Logger") && isset(Logger::$_logFile))
        {
            fwrite(Logger::$_logFile, $message . "\n");;
        }

	}

	/**
	 * convert a normal message to a syslog message
	 * @param string $message the message to convert to syslog format
	 * @return string a syslog message with a syslog header for the current
	 *  facility and severity level
	 */
	private function syslogize($message)
    {
        // Facility/severity
        $header = '<' . ($this->_facility * 8 + $this->_severity) . '>' . date('M d G:i:s ');

        // Host
        if (isset($_SERVER['SERVER_NAME'])) {
            $header .= $_SERVER['SERVER_NAME'] . ' ';
        } elseif (function_exists('gethostname')) {
            $header .= gethostname()  . ' ';
        } else {
            $header .= 'unknown ';
        }

        return $header . $message;
    }

	/**
	 Substitute symbol
	%d{dd/MM/yy HH:MM:ss } Date
	%t{HH:MM:ss } Time
	%w the logger instance id (sesison tracking)
	%r Milliseconds since logger was created
	%n logger name
	%p Level
	%m user-defined message

	%S Server Name
	%s PHP script name
	%I IP address of browser
	%H the request url
	%U HTTP user agent
	%Ccookie_name COOKIE content
	%u mybb2 cookie user slug
	%i mybb2 cookie user id

	%f File name
	%F complete File path
	%L Line number
	%M Method name
	%A Method arguments
	%B a formatted backtrace

	%% individual percentage sign
	Caution: %f, %F, %L, %M, %A slow down program run!
	 */
	private function msgFormat($message, $level, $levelNum = false)
	{
                // These static variables look stupid, but they're to fix a weird bug. Keep 'em.
                static $infoLevel = Logger::INFO;  
                static $traceLevel = Logger::TRACE;
                static $errorLevel = Logger::ERROR;
                static $warnLevel = Logger::WARN;
                static $debugLevel = Logger::DEBUG;

		// get the log format to use, and break it up into % tokens
		if (isset($GLOBALS['FORCELOGFORMAT']))
            $token = strtok($GLOBALS['FORCELOGFORMAT'], '%');
		else if ($levelNum != false)
            $token = strtok($this->_formatStr[$levelNum], '%');
		else
            $token = strtok($this->_formatStr[$traceLevel], '%');

		$line = '';
		$bt = false;
		// loop over all tokens
		while ($token !== false)
		{
			// the first char in the token is the token type (this is right after the %)
			$type = $token[0];
			switch ($type)
			{
				case 'd':
					$line .= date('Y/m/d H:i:s');
                    break;
				case 't':
					$line .= date('H:i:s');
                    break;
				case 'r':
                                        if(class_exists("Logger")) // This is a terrible check having to be done for BOOMS-1842.
						$line .= sprintf('%06f', microtime(true) - self::$_creationTime);//round ((microtime(true) - self::$_creationTime), 6);
                    break;
				case 'w':
					$line .= getmypid();
                    break;
				case 'p':
					$line .= sprintf('%s', $level);
                    break;
				case 'n':
					$line .= sprintf('%.11s', $this->_logName);
                    break;
				case 'm':
					$line .= $message;
                    break;
				case 'S':
					if (isset($_SERVER['SERVER_NAME']))
						$line .= $_SERVER['SERVER_NAME'];
                    break;
				case 's':
					if (isset($_SERVER['SCRIPT_FILENAME']))
						$line .= $_SERVER['SCRIPT_FILENAME'];
                    break;
				case 'U':
					if (isset($_SERVER['HTTP_USER_AGENT']))
						$line .= $_SERVER['HTTP_USER_AGENT'];
                    break;
				case 'I':
					if (isset($_SERVER['REMOTE_ADDR']))
						$line .= $_SERVER['REMOTE_ADDR'];
                    else
                        $line .= 'unknownaddr';
                    break;
				case 'H':
					if (isset($_SERVER['REQUEST_URI']))
						$line .= $_SERVER['REQUEST_URI'];
                    break;
				case 'C':
                    if (isset($_COOKIE[substr($token, 1)]))
                        $line .= $_COOKIE[substr($token, 1)];
                    else
                        $line .= 'na';
                    break;
				case 'u':
                    if (isset($_COOKIE['mybb2']))
                    {
                        $parts = explode(':', $_COOKIE['mybb2']);
                        $line .= base64_decode($parts[4]);
                    }
                    else
                        $line .= 'anonymous';
                    break;
				case 'i':
                    if (isset($_COOKIE['mybb2']))
                    {
                        $parts = explode(':', $_COOKIE['mybb2']);
                        $line .= $parts[0];
                    }
                    else
                        $line .= 'na';
                    break;
				case 'f':
                    if ($bt === false)
                        $bt = $this->getBtCaller();
                    $paths = explode('/', $bt['file']);
                    $line .= array_pop($paths);
                    break;
				case 'F':
					if ($bt === false)
						$bt = $this->getBtCaller();
					$line .= $bt['file'];
                    break;
				case 'L':
				case 'M':
					if ($bt === false)
						$bt = $this->getBtCaller();
					$line .= $bt['line'];
                    break;
				case 'A':
					$bt = debug_backtrace();
					$line .= var_export($bt[2]['args'], true);
                    break;
				case 'B':
					$line .= $this->formatBT();
                    break;
                case 'v':
                    if (isset($_SERVER['HTTP_HOST'])) {
                        $line .= $_SERVER['HTTP_HOST'];
                    } else {
                        $line .= 'unknownhost';
                    } 
                    break;
                default:
                    $line .= $type;
                    break;
			}
			// append anything that is not a symbol as literal text to the string
			if (isset($token[1]))
				$line .= substr($token, 1);

			// get the next token
			$token = strtok('%');
		}

		// return the formatted string
		return $line;
	}

	/**
	 * get the caller information through a backtrace
	 * @return string the backtrace caller that called the log message (2 deep)
	 */
	private static function getBtCaller()
	{
		$bt = debug_backtrace();
		return $bt[2];
	}

	/**
	 * format a backtrace and return the result as a string.  Each function call is
	 * displayed on a newline.
	 * @return string a stack trace, each stack level is separated by a newline.
	 */
	private function formatBT()
	{
		// get the backtrace
		$bt = debug_backtrace();
		$log = '';

		// loop over the back trace elements EXCEPT for the call to formatBT()
		for($i = 2, $m = count($bt); $i < $m; $i++)
		{
			$log .= "\t";
			// the fine, lineno, function
			$line = $bt[$i];

			if(isset($line['file']))
				$log .= $line['file'];

			if(isset($line['line'] ))
				$log .= ' line:[' . $line['line'] . '] ';

			if(isset($line['function'] ))
				$log .= ' func: ' . $line['function'];
			$log .= "\n";
		}

		// return the list of function calls
		return $log;
	}
}
?>
