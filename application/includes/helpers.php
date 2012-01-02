<?php
/**
 * Support-Ticketing Website helpers
 *
 * @package		Support-Ticketing
 * @author		Nicholas Valbusa - info@squallstar.it - @squallstar
 * @copyright	Copyright (c) 2011-2012, Squallstar
 * @license		GNU/GPL (General Public License)
 * @link		http://squallstar.it
 *
 */
 
Class Helpers {
	
	public $months = array(
		'', 'gennaio', 'febbraio', 'marzo', 'aprile', 'maggio', 'giugno', 'luglio',
		    'agosto', 'settembre', 'ottobre', 'novembre', 'dicembre'
	);
	

	public function toItalianDateTime($datetime) {
		list($date, $time) = explode(' ', $datetime);
		$date = array_reverse(explode('-', $date));
		$date[1] = $this->months[(int)$date[1]];
		return implode(' ', $date).' alle ore '.substr($time,0,5);
	}
}