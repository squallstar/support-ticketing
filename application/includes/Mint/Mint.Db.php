<?php
/**
 * Mint Framework Database Class
 *
 * @package	Mint
 * @author	Nicholas Valbusa - info@squallstar.it - @squallstar
 * @copyright	Copyright (c) 2011-2012, Squallstar
 * @license	GNU/GPL (General Public License)
 * @link	http://squallstar.it
 *
 */

class Db { 
              
        private $db; 
        private $alternate_credentials;
        private $connected; 
        private $last_query; 
        private $script_name; 
        private $timer_start; 
        private $resource;
              
        public function __construct($credentials=NULL) {
        	if (isset($credentials)) $this->alternate_credentials=$credentials;
        }      
        
        public function __destruct() {
        	$this->disconnect();
        }
        
        /**
         * Open a link to the database (auto launched on first query)
         */        
        private function connect() { 
                if (!$this->db) { 
                		if (!is_array($this->alternate_credentials)) {
                			$this->db=@mysql_connect(DATABASE_HOST, DATABASE_USER, DATABASE_PASS) or die('Manutenzione in corso');
                			mysql_select_db(DATABASE_NAME,$this->db);
                		}else{
                			$this->db=mysql_connect($this->alternate_credentials['host'], $this->alternate_credentials['user'], $this->alternate_credentials['pass']);
                			mysql_select_db($this->alternate_credentials['dbname'],$this->db);
                		}
                        if (QUERY_LOGGING) $this->writeLog('----------');
                }       
        } 
        
        /**
         * Close the link to the database
         * @return integer
         */
        public function disconnect() { 
                return @mysql_close($this->db); 
        } 
        
        /**
         * Write a string into the log file (private function)
         * @param string $sql
         * @param (optional) string $time
         * @return void
        */        
        private function writeLog($sql,$time='') { 
                $this->last_query=$sql;
                file_put_contents(LOG_DIRECTORY.'/'.basename($_SERVER["SCRIPT_NAME"]).'.log', date('Y-m-d H:i:s').' '.SCRIPT_NAME.' >> '.str_replace('
                	',' ',$sql).$time.'
'.(mysql_error($this->db)?(' >> '.mysql_error($this->db)):''), FILE_APPEND); 
               
        }
        
        /**
         * Destroy the log file
         * @return void
         */        
        public function clearLog() {
        	@unlink(QUERY_LOG_FILE);
        } 
        
        /**
         * Change the active database
         * @param string $dbname
         * @return integer (1=ok)
         */
        public function changeDatabase($dbname) { 
        		if (!$this->db) $this->connect();
                define('DATABASE_NAME', $dbname); 
                return mysql_select_db($dbname,$this->db); 
        } 
        
        /**
         * Executes a query on the database
         * @param string $sql
         * @return resource
         */        
        public function query($sql) { 
                if (!$this->db) $this->connect();
                if (QUERY_LOGGING) $this->timer(); 
                $this->resource=mysql_query($sql,$this->db); 
                if (QUERY_LOGGING) $this->writeLog($sql,$this->timer(true)); 
                if (mysql_error($this->db)) {
                	echo 'MySql Error: <strong>'.mysql_error().'</strong>. Query: <strong>'.$sql.'</strong>';
                	return false;
                }else return true;           
	    } 
        
        /**
         * When query logging is active, returns the last executed query
         * @return string
         */
        public function lastQuery() { 
                return $this->last_query; 
        } 
        
        /**
         * returns the number of rows
     	 * @return integer
     	 */        
        public function numRows() { 
                return mysql_num_rows($this->resource); 
        } 
        
        /**
         * Makes an INSERT query to the database, given an associate array of data
         * @param string $table
         * @param array $data (field => value)
         * @return integer
         */
        public function insert($table, $data) { 
        		if (!$this->db) $this->connect();
                foreach ($data as $n => $v) { 
                        $fields.=', '.$n; 
                        $values.=', \''.mysql_real_escape_string($v,$this->db).'\''; 
                } 
                return $this->query("INSERT INTO ".$table." (".substr($fields,2).") VALUES (".substr($values,2).");"); 
        } 
        
        /**
         * Get insert id from the last executed query
         * @return integer
         */        
        public function insertId() { 
                return mysql_insert_id($this->db); 
        } 
        
        /**
         * Makes an UPDATE query to the database
         * @param  string $table
         * @param  array $data (field => value)
         * @param  string $where
         * @return integer
         */
        public function update($table, $data, $where) { 
        		if (!$this->db) $this->connect();
                foreach ($data as $n => $v) { 
                        $str.=", ".$n."='".mysql_real_escape_string($v,$this->db)."'"; 
                } 
                return $this->query("UPDATE ".$table." SET ".substr($str,2)." ".$where); 
        } 
        
        /**
         * Saves a record
         * @param string $table
         * @param array $data (field => value)
         * @param string $primarykey
         */
         
        public function save($table, $data, $primary_key) {
        	if ($primary_key!=null) {
        		if ($data[$primary_key]) {
        			//Update
        			$val = $data[$primary_key];
        			unset($data[$primary_key]);
        			return $this->update($table, $data, "WHERE ".$primary_key." = ".$val." LIMIT 1;");
        		}else{
        			return $this->insert($table, $data);
        		}
        	}else{
        		if (QUERY_LOGGING) $this->writeLog("PRIMARY KEY NOT GIVEN DURING A SAVE OPERATION");
        	}
        }
        
        /**
         * Makes a DELETE query to the database
         * @param  string $table
         * @param  string $where
         * @return integer
         */
        public function delete($table, $where) { 
        		if (!$this->db) $this->connect();
                return $this->query("DELETE FROM $table $where"); 
        } 
                
        /**
         * Fetch a MySQL resource to an array of associative arrays
         * @param  resource $result
         * @return array
         */
        public function row() { 
               if ($row=mysql_fetch_assoc($this->resource)) return $row;
               else return false;
                   
        } 
        
        
        /**
         * A private function to monitor the time of each query
         */
        
        private function timer($action=false) { 
                if (!$action) { 
                        $load_time = explode(' ',microtime()); 
                        $this->timer_start = $load_time[1] + $load_time[0]; 
                }else{ 
                        $load_time = explode(' ',microtime()); 
                        $page_end = $load_time[1] + $load_time[0]; 
                        return ' ('.(number_format($page_end - $this->timer_start, 4, '.', '')*100).'s)'; 
                } 
        } 
        
        /**
	     * Get the columns (fields) of a table, in a human readable format
	     * @param  string $table
	     * @return string
	     */        
	    public function getFields($table) {
	    	$this->query('SHOW COLUMNS FROM '.$table.';');
	    	while ($row=$this->row()) {
	    		$tmp.=$row['Field'].' {'.$row['Type'].'} '.($row['Key']?'{Key: '.$row['Key'].'}':'').'<br />';
	    	}
	    	return '<strong>TABLE '.$table.'</strong><br />'.$tmp;
	    }   
	    
	    public function dump($output) {
	    	$dump = "mysqldump -h ".DATABASE_HOST." -u ".DATABASE_USER." -p ".DATABASE_PASS." ".DATABASE_NAME." > ".$output;
	    	system($dump);
	    	//echo $dump;
	    }
	               
	           
}