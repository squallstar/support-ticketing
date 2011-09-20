<?php
/**
 * Mint Framework RSS
 *
 * @package		Mint
 * @author		Nicholas Valbusa - info@squallstar.it - @squallstar
 * @copyright	Copyright (c) 2011, Squallstar
 * @license		GNU/GPL (General Public License)
 * @link		http://squallstar.it
 *
 */

class Rss { 
              
        private $feed;
              
        public function __construct($title,$link='',$description='') {
        	$this->feed.='<?xml version="1.0" encoding="UTF-8" ?>
        <rss version="2.0">
        	<channel>
        		<title>'.$title.'</title>
        		<link>'.$link.'</link>
        		<description>'.$description.'</description>';
        }      
        
        public function add($params) {
        	$this->feed.='
        		<item>
        			<pubDate>'.$params['date'].'</pubDate>
        			<title>'.$params['title'].'</title>
        			<description>'.$params['description'].'</description>
        			<link>'.$params['link'].'</link>
        			<author>'.$params['author'].'</author>
        		</item>';
        }
        
        public function render() {
        	
        	$this->feed.= '
        		</channel>
        	</rss>';
        	
        	header("Content-Type: application/xml; charset=UTF-8");
        	echo $this->feed;        
        }
        	               
	           
}