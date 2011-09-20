<?php
/**
 * Mint Framework Form
 *
 * @package		Mint
 * @author		Nicholas Valbusa - info@squallstar.it - @squallstar
 * @copyright	Copyright (c) 2011, Squallstar
 * @license		GNU/GPL (General Public License)
 * @link		http://squallstar.it
 *
 */
 
class Form {

	private $errors = array();
	private $errorClass;
	
	/**
	 * Optional parameter: the string to get with "hasErrorClass" function.
	 * @param string $errorclass
	 */	
	public function __construct($errorclass='error') {
		$this->errorClass=$errorclass;
	}
	
	/**
	 * Returns the associative array with the errors
	 * @return associative array
	 */	
	public function getErrors() {
		if (count($this->errors)) return $this->errors;
		else return false;
	}
	
	/**
	 * Returns "true" if the parameter is contained in error list
	 * If any parameter is passed, check if any error is set in the array (global check)
	 * @param string $field
	 * @return boolean
	 */	
	public function hasError($field=false) {
		if ($field)	return $this->errors[$field];
		else {
			if (count($this->errors)) return true;
			else return false;
		}
	}
	
	/**
	 * Returns the string named "errorclass" if the parameter is contained in error list
	 * @param string $field
	 * @return boolean
	 */	
	public function hasErrorClass($field) {
		if ($this->errors[$field]) return $this->errorClass;
	}
	
	/**
	 * Check if a POST var is null and adds it to the error array
	 * @param string $postvar
	 * @return boolean
	 */
	public function check($postvar) {
		if (!$_POST[$postvar]) {
			$this->errors[$postvar]=true;
			return true;
		}else return false;
	}
	
	/**
	 * Check if a POST var is a corrected email address
	 * @param string $postvar 
	 * @return boolean
	 */
	public function checkEmail($postvar) {
		if (!preg_match('/^[a-z0-9_][a-z0-9_\\.\\-]*@[a-z0-9_\\.\\-]+\\.[a-z]{2,4}$/i', $_POST[$postvar])) {
			$this->errors[$postvar]=true;
			return true;
		}
	}
	
	/**
	 * Check if a POST var is a name
	 * @param string $postvar 
	 * @return boolean
	 */
	public function checkName($postvar) {
		$tmp = ($postvar && preg_match('/^[a-z][a-zàèìòùéçñ\`\'\.\s]*$/i', $postvar));
		if (!$tmp) {
			$this->errors[$postvar]=true;
			return true;
		}
	}
	
	/**
	 * Check if a POST var is a phone number
	 * @param string $postvar 
	 * @return boolean
	 */
	public function checkPhone($postvar) {
		$tmp = ($postvar && preg_match('/^\d{7,15}$/', $postvar));
		if (!$tmp) {
			$this->errors[$postvar]=true;
			return true;
		}
	}
	
	/**
	 * Check if a POST var is an italian CAP code (5 digits)
	 * @param string $postvar 
	 * @return boolean
	 */
	public function checkCap($postvar) {
		$tmp = ($postvar && preg_match('/^\\d\\d\\d\\d\\d$/', $postvar));
		if (!$tmp) {
			$this->errors[$postvar]=true;
			return true;
		}
	}
	
	/**
	 * Check if a POST var is an italian CAP code (5 digits)
	 * @param string $postvar 
	 * @param associative array $array
	 * @return boolean
	 */
	public function checkInArray($postvar,$array) {
		$tmp = in_array($postvar, array_keys($array));
		if (!$tmp) {
			$this->errors[$postvar]=true;
			return true;
		}
	}	
}