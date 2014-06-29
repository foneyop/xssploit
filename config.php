<?php
// set headers to never cache calls to API
header('expires: sat, 26 jul 1997 05:00:00 gmt' );
header('last-modified: ' . gmdate( 'd, d m y h:i:s' ) . ' gmt' );
header('cache-control: no-store, no-cache, must-revalidate' );
header('cache-control: post-check=0, pre-check=0', false );
header('pragma: no-cache' );
header('Content-type: text/javascript');

// the simple logger
require 'Logger.php';
// db abstraction for MySQL
require 'db.php';

// Path to the MySQL database.   Update this for your environment
// Be sure to create the MySQL database by calling from the xssploit root dir:
//   mysql -h localhost -u root -p < xssploit.sql
//
define("DB_HOST", "localhost");
// You really should not use the root user.  Update this with a user that has 
// write access to xssploit DB
define("DB_USER", "root");
define("DB_PASS", "Jasmine");
define("DB_NAME", "xssploit");

// set to Logger::ERROR if running with more than a few browsers
$GLOBALS['LOGLEVEL'] = Logger::TRACE;
// see Logger.php for LOGFORMAT issues
$GLOBALS['LOGFORMAT'] = '[%w]: [%I:%r:%n:%p]: %m';
// path to the log file (something web write-able)
$GLOBALS['LOGFILE'] = '/tmp/xssploit.log';

// if true, we will compile the JavaScript we send to the browsers into
// a compressed and obfuscated version.
define('OBFUSCATE_JS', true);

// a web writable location for "caching" the obfuscated JavaScript
define('OBFUSCATE_DIR', '/tmp/xssploit');

// this setting controls if we should create a new host entry (db row)
// for each new guid session we see. Usually you will not want to create
// new hosts for each session if the IP,Browser,OS match an existing host,
// unless your targets are behind NAT of some kind.
//
// NOTE: currently we override the cookies on each new session, this keeps
// the session data fresh.  If you need to store session data, set this
// to false. Or build a session table (one to many relationship).
//
// default: true
define('REUSE_HOST', true);

// REQUIRED: set a unique password.   There is no default password, this
// creates a unique password on each request, effectively locking admin
// console. 
//define('PASSWORD', "NO DEFAULT".preg_replace("/[\+\/\=]/", "", base64_encode(openssl_random_pseudo_bytes(12))));
// EG: define('PASSWORD', 'adminpassword');
// browse to http://localhost/panel.html then type: unlock adminpassword
define('PASSWORD', "Pine Dale");

// The password for the xsshunter Chrome Extension to save data to the MySQL DB
// (centeralized DB is usefull if you have muitple testers)
define('XSSPASSWORD', "xssqahunter");

// define the idle polling interval at 40 seconds
define('IDLE_POLL', 2000);

// define the active polling interval at 2 seconds
// hosts are active when they are targeted
define('ACTIVE_POLL', 2000);

// The absulute path to the module directory (must contain trailing /)
define('MODULE_DIR', __DIR__ .'/modules/');

// define a local cache.  This is espically important if you are controling
// lots of browsers since it will significantly reduce DB conections and
// dramatically increase the number of browsers you can control.
define('CACHE', 'APC');
