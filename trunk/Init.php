<?php
/*
    Copyright 2005 The Board of Regents of the University of Wisconsin System,
    Eric Larson, Nathan Vack

    This file is part of the Library Statistics Database (Libstats).
    Libstats is free software; you may redistribute it and/or modify
    it under the terms of version 2 of the GNU General Public License 
    as published by the Free Software Foundation.

    Libstats is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.


    You should have received a copy of the GNU General Public License
    along with Libstats; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

error_reporting(E_ALL);

/* Database connection info; put your info here. */
$dbHost = 'localhost';
$dbUser = 'libstats';
$dbPass = 'libstats';
$dbName = 'libstats_development';

/* This is autogenerated. Change from mysql if you're feeling adventurous */
define('DSN', "mysql://$dbUser:$dbPass@$dbHost/$dbName");

/* Will affect page titles and the like */
define('SITE_NAME','Library Stats');

/* Set this if your PHP server is really running on a port other than :80; usually not the case */
define('STRIP_PORT', false);

/* These values show up on the error page; change to your own info */
define('DEV_NAME', 'Your Admin');
define('DEV_3SPN', 'he');
define('DEV_3OPN', 'him');
define('DEV_PPN', 'his');
define('DEV_EMAIL', 'admin@example.com');

/* Debugging / upgrading things. */
// Causes a 'Maintanence' page to be shown
define('SITE_MAINTANENCE', false);
// This IP address will really be able to log in
define('DEBUG_IP', "");
// Set this to true if you want to skip authentication and make everyone an
// admin. Useful for things like the W3C Validators
define('AUTO_ADMIN_LOGON', false);

// Disable magic quotes with prejudice
if (get_magic_quotes_gpc()) {
    $_POST = array_map('stripslashes_deep', $_POST);
    $_GET = array_map('stripslashes_deep', $_GET);
    $_COOKIE = array_map('stripslashes_deep', $_COOKIE);
	$_REQUEST = array_map('stripslashes_deep', $_REQUEST);
}

// Fix order of variables in $_REQUEST; cookie variables should have less
// importance.
$_REQUEST = array_merge($_COOKIE, $_GET, $_POST);

function stripslashes_deep($value) {
    $value = is_array($value) ?
        array_map('stripslashes_deep', $value) :
        stripslashes($value);
    return $value;
}


session_start();
if (!isset($_SESSION['loggedIn'])) { $_SESSION['loggedIn'] = false; }

function registerAll($pattern) {
     foreach (glob($pattern) as $file) {
         require_once($file);
     }
}

if (STRIP_PORT) {
  // Pull the port number off $_SERVER['HTTP_HOST']
  $portPos = strpos($_SERVER['HTTP_HOST'], ":");
  if ($portPos) {
      $_SERVER['HTTP_HOST'] = substr($_SERVER['HTTP_HOST'], 0, $portPos);
  }  
}

require_once 'DB.php';
require_once 'Utils.php';

//loop through subclasses--tight code! San Dimas High-School Football rules!
registerAll('actions/*Action.php');
registerAll('reports/*Report.php');
registerAll('finders/*Finder.php');

// Let's try and share the database connection throughout the application;
// reopening it all the time is a HUGE PAIN.
$db = DB::connect(DSN);
if (DB::isError($db)) {
    // This will be a fatal error, head to a bad page
    $act = new PageErrorAction();
    $rInfo = $act->perform();
    include $rInfo['renderer'];

    die;

}

$db->setFetchMode(DB_FETCHMODE_ASSOC);
$_REQUEST['db'] = $db;

?>