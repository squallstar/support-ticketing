<?php
/**
 * Mint Framework Loader
 *
 * @package		Mint
 * @author		Nicholas Valbusa - info@squallstar.it - @squallstar
 * @copyright	Copyright (c) 2011, Squallstar
 * @license		GNU/GPL (General Public License)
 * @link		http://squallstar.it
 *
 */

/* SETTINGS */
define('QUERY_LOGGING', false);
define('EMAIL_LOGGING', false);
define('LOG_DIRECTORY', '/support/log/');
define('APPPATH', 'http://support.mycompany.com/');
define('APPMAIL', 'support@mycompany.it');

/* configs */
define('DATABASE_HOST', 'localhost');
define('DATABASE_USER', 'username');
define('DATABASE_PASS', 'password');
define('DATABASE_NAME', 'dbname');

/* END OF SETTINGS */

define('TICKETING_VERSION', '1.1');

session_start();

define('SCRIPT_NAME', basename($_SERVER['SCRIPT_NAME']));

function __autoload($classname) {
    require_once "Mint.$classname.php";
}
