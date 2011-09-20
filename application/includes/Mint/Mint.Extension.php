<?php
/**
 * Mint Framework Extensions
 *
 * @package		Mint
 * @author		Nicholas Valbusa - info@squallstar.it - @squallstar
 * @copyright	Copyright (c) 2011, Squallstar
 * @license		GNU/GPL (General Public License)
 * @link		http://squallstar.it
 *
 */

Class Extension {
  public $path;
  
  public function __construct($path) {
    $this->path = $path;
  }
}