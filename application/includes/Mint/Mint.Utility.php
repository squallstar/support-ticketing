<?php
/**
 * Mint Framework Utility class
 *
 * @package		Mint
 * @author		Nicholas Valbusa - info@squallstar.it - @squallstar
 * @copyright	Copyright (c) 2011, Squallstar
 * @license		GNU/GPL (General Public License)
 * @link		http://squallstar.it
 *
 */

abstract class Utility {

	static public function debug($x) {
		echo '<pre>';
		print_r($x);
		echo '</pre>';
	}

	/**
	 * Trim a string to the desiderd value only if exceed it in length. It adds also 3 points (not counted on the length)
	 * @param string $string
	 * @param integer @maxlength
	 * @return string
	 */
	static public function trimString($string,$maxlength) {
		if (strlen($string)>$maxlength) return substr($string,0,$maxlength).'...';
		else return $string;
	}
	
	/**
	 * Splits a text file into an array using the given delimiter. It converts also the line breaks into html break lines.
	 * @param string $path
	 * @param string $delimiter
	 * @return array
	 */
	static public function fileToArray($path,$delimiter) {
		$var=fopen($path,"r");
		$content=fread($var,filesize($path));
		fclose($var);
		$tmp=explode($delimiter,$content);
		foreach ($tmp as &$v) { $v=nl2br($v);if(substr($v,0,6)=='<br />')$v=substr($v,6);}
		return $tmp;
	}

	/**
	 * Converts a string in the datetime format to the italian format, returning it into an associative arrays with the keys 'date' and 'time'
	 * @param string $datetime
	 * @return array 
	 */
	static public function italianDateTime($datetime) {
		$temp=explode(' ',$datetime);
		$tmp['time']=$temp[1];
		$tmp['date']=implode('/',array_reverse(explode('-',$temp[0])));
		return $tmp;
	}
	
	/**
	 * Converts a number into an integer, rounding it to excess and using the given decimal positions number.
	 * @param  number $value
	 * @param  integer $dp
	 * @return integer
	 */
	static public function roundUp ($value, $dp){
		$offset = pow (10, -($dp + 1)) * 5;
		return round ($value + $offset, $dp);
	}
}