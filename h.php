<?php 
require 'Logger.php';
$GLOBALS['LOGLEVEL'] = Logger::TRACE;
$GLOBALS['LOGFORMAT'] = '[%w]: [%I:%r:%n:%p]: %m';
$GLOBALS['LOGFILE'] = '/tmp/xssploit.log';
$log = Logger::getLogger("app");

$db = DB::getConnection("localhost", "root", "pinkfloyd", "xssploit");
$host = $db->select("");


?>
