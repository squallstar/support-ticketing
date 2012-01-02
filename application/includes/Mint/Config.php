<?php
/**
 * Application config file
 *
 * @package		Support-ticketing + Mint Framework
 * @author		Nicholas Valbusa - info@squallstar.it - @squallstar
 * @copyright	Copyright (c) 2011-2012, Squallstar
 * @license		GNU/GPL (General Public License)
 * @link		http://squallstar.it
 *
 */

/*
|--------------------------------------------------------------------------
| Application Path
|--------------------------------------------------------------------------
*/
define('APPPATH', 'http://support.mycompany.com/');

/*
|--------------------------------------------------------------------------
| Email "From" address
|--------------------------------------------------------------------------
*/
define('APPMAIL', 'support@mycompany.it');

/*
|--------------------------------------------------------------------------
| Enable/disable query logging
|--------------------------------------------------------------------------
*/
define('QUERY_LOGGING', false);

/*
|--------------------------------------------------------------------------
| Enable/Disable email logging
|--------------------------------------------------------------------------
*/
define('EMAIL_LOGGING', false);

/*
|--------------------------------------------------------------------------
| Log directory (absolute path)
|--------------------------------------------------------------------------
*/
define('LOG_DIRECTORY', '/support/log/');

/*
|--------------------------------------------------------------------------
| Database settings
|--------------------------------------------------------------------------
|
| Note that only mysql driver is currently supported!
|
*/
define('DATABASE_HOST', 'localhost');
define('DATABASE_USER', 'username');
define('DATABASE_PASS', 'password');
define('DATABASE_NAME', 'dbname');

/*
|--------------------------------------------------------------------------
|-----------------       END OF APPLICATION SETTINGS      -----------------
|--------------------------------------------------------------------------
*/


session_start();
define('TICKETING_VERSION', '1.2');
define('SCRIPT_NAME', basename($_SERVER['SCRIPT_NAME']));
function __autoload($classname) {
    require_once "Mint.$classname.php";
}
