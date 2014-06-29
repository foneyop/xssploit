<?php

require 'Logger.php';
require 'db.php';

define("DB_HOST", "localhost");
define("DB_USER", "root");
define("DB_PASS", "Jasmine");
define("DB_NAME", "xssploit");

// set to Logger::ERROR if running with more than a few browsers
$GLOBALS['LOGLEVEL'] = Logger::TRACE;
// see Logger.php for LOGFORMAT issues
$GLOBALS['LOGFORMAT'] = '[%w]: [%I:%r:%n:%p]: %m';
// path to the log file (something web write-able)
$GLOBALS['LOGFILE'] = '/tmp/xssploit.log';

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
// EG: define('PASSWORD', 'my secret passphrase');
//define('PASSWORD', "NO DEFAULT".preg_replace("/[\+\/\=]/", "", base64_encode(openssl_random_pseudo_bytes(12))));
define('PASSWORD', "Pine Dale");

define('XSSPASSWORD', "xssqahunter");

// define the idle polling interval at 40 seconds
define('IDLE_POLL', 29000);

// define the active polling interval at 2 seconds
// hosts are activve when they are targeted
define('ACTIVE_POLL', 2000);

// THe path to the module directory
define('MODULE_DIR', __FILE__ .'/modules/');
