/**
 * Support-Ticketing Application.js
 *
 * @package		Support-Ticketing
 * @author		Nicholas Valbusa - info@squallstar.it - @squallstar
 * @copyright	Copyright (c) 2011-2012, Squallstar
 * @license		GNU/GPL (General Public License)
 * @link		http://squallstar.it
 *
 */

function sendMail(id, type, data) {
	$.post(root+'send-mail', {id:id, type:type, data:data});
}

function sendAsyncMail() {
	$.get(root+'send-mail');
}

function switchAdvancedOptions() {
	$('#advanced_options').slideToggle(300);
}