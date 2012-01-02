<?php
/**
 * Support-Ticketing Async-Mailer
 *
 * @package		Support-Ticketing
 * @author		Nicholas Valbusa - info@squallstar.it - @squallstar
 * @copyright	Copyright (c) 2011-2012, Squallstar
 * @license		GNU/GPL (General Public License)
 * @link		http://squallstar.it
 *
 */
 
require_once('../includes/appdelegate.php');
$delegate = new AppDelegate(true);

if (isset($_SESSION['async_mailer'][0])) {
	$id = $_SESSION['async_mailer'][0]['ticket'];
	$type = $_SESSION['async_mailer'][0]['type'];
	$info = json_decode($_SESSION['async_mailer'][0]['info'], true);
	unset($_SESSION['async_mailer'][0]);
	echo $delegate->sendMail($id, $type, $info);
}

exit;