<?
/**
 * FlycKER's MySQLi Class
 * 
 * Help in common tasks - like accessing mysql and executing queries.
 * {@package cFlycKER}
 * {@author Tiago Fischer <tiagofischer@gmail.com>}
 * {@since 2009-06-02}
 */
class fMysqli
{
	// Connection handle
	public $conn;
	// Access information
	private $host;
	private $user;
	private $pass;
	private $db;
	private $port;
	// Query Result
	private $qResult;
	// Memcache handle
	private $memcache;
	// Debug
	private $debug;
	private $debugTxt;
	private $logErrorFile;
	private $dieOnError = true; // set if the script will die on sql errors or not
	
	// Constants
	private $multiInsert = 50;
	
	/**
	* fMysqli Contructor
	* 
	* @param string 	$host
	* @param string 	$user
	* @param string 	$pass
	* @param string 	$db
	* @param int 		$port
	* @param bol		$debug
	* @param memcache 	&$memcache
	* @param string		$logErrorFile
	* @return fMysqli
	*/
	public function __construct($host, $user = null, $pass = null, $db = 'mysql', $port = 3306, $debug = false, &$memcache = null, $logErrorFile = false) 
	{
		if (is_object($host)) {
			// Use an exists connection
			$this->conn = $host;
			// In this case, the next parameter is an unique DB string (we use this for cache tables stats)
			// $this->db = $user;
		} else {
			// Create a new connection
			$this->host = $host;
			$this->user = $user;
			$this->pass = $pass;
			$this->db 	= $db;
			$this->port = $port;
			$this->memcache = &$memcache;
			$this->debug = $debug;
			$this->debugTxt = '';
			$this->logErrorFile = $logErrorFile;
			// Connecting
			$this->_connect();
		}
    }
    
    /**
    * Change the database
    * 
    * @param 	string 	$db
    * @param 	bol 	$retry
    * @return 	bol
    */
    public function selectDb($db, $retry = true)
	{
		if ($this->debug) { $this->debugTxt .= "[fMysqli debug] selectDb({$db}, {$retry}) <br />\n"; }
		$this->db = $db;
		if (!$this->conn->select_db($db))
		{
			if ($retry)
			{
				// try to reconnect before die
				$this->_connect();
				if (!$this->conn->select_db($db))
				{
					return false;
				}
			}
			else
			{
				return false;
			}
		}
		return true;
	}
		
	/**
	* General purpose SQLs queries
	* 
	* @param string $sql
	* @return qResult		mysqli result handle
	*/
	public function qSql($sql)
	{
		if ($this->debug) { $this->debugTxt .= "[fMysqli debug] qSql({$sql}) <br />\n"; }
		$this->_sqlQuery($sql);
		return $this->qResult;
	}
	
	/**
	* Use this function to execute SELECTs queries
	* 
	* @param string $sql			SELECT query
	* #param string	$cacheType		apc/memcache
	* @param int 	$cacheTtl		Time in seconds to keep this query on cache, 0 to not cache, -1 to refresh the cache
	* @return rArray array('numRows' => ..., 'timestamp' => ..., 'data' => ...)
	*/
	public function qSelect($sql, $cacheTtl = 0, $cacheType = 'apc')
	{
		if ($this->debug) { $this->debugTxt .= "[fMysqli debug] qSelect({$sql}, {$cacheTtl}) <br />\n"; }
		// It's cached?
		if ($cacheTtl > 0)
		{
			$sqlUniqueId = md5($this->db . $sql);
			$resultArray = ($cacheType == 'memcache') ? $this->memcache->get($sqlUniqueId) : apc_fetch($sqlUniqueId);
			if ($resultArray) { return $resultArray; }
		}
		// No, it's not. Let's work...
		$this->_sqlQuery($sql);
		$resultArray = array(
			'numRows' => $this->qResult->num_rows,
			'timestamp' => time(),
			'data' => array()
		);
		while ($row = $this->qResult->fetch_assoc()) 
		{
        	$resultArray['data'][] = $row;
    	}
    	$this->qResult->close();
    	// Put to cache?
    	if ($cacheTtl)
    	{    		
    		if ($cacheType == 'memcache')
    		{
				$this->memcache->add($sqlUniqueId, $resultArray, true, $cacheTtl);
    		}
    		else
    		{
    			if ($cacheTtl == -1) 
    			{ 
    				$sqlUniqueId = md5($this->db . $sql);
    				apc_store($sqlUniqueId, $resultArray, $cacheTtl);
    			}
    			else
    			{
					apc_add($sqlUniqueId, $resultArray, $cacheTtl);
				}
    		}
		}
    	return $resultArray;
	}
	
	/**
	* Use this function to execute DELETEs queries
	* 
	* <code>
	array(
		'table' => 'table_name',		// (required)
		'where' => 'id = x'				// (optional)
	);
	* </code>
	* @param array $array
	* @return rArray array('affectedRows' => ...)
	*/
	public function qDelete($array)
	{
		if ($this->debug) { $this->debugTxt .= "[fMysqli debug] qDelete() <br />\n" . var_export($array, true) . " <br />\n"; }
		$where = (isset($array['where'])) ? " WHERE {$array['where']}" : '';
		$this->_sqlQuery("DELETE FROM `{$array['table']}`{$where};");
		return array('affectedRows' => $this->conn->affected_rows);
	}
	
	/**
	* Use this function to execute UPDATEs queries
	* 
	* <code>
	array(
		'table' => 'table_name',								// (required)
		'escape' => true,										// (optional)
		'fields' => 'field_name1,field_name2,field_name3',		// (required)
		'where' => 'id = x',									// (optional)
		'data' => array (										// (required)
			value1,
			'value2',
			value3
		)
	);
	* </code>
	* @param array $array
	* @return rArray array('affectedRows' => ...)
	*/
	public function qUpdate($array)
	{
		if ($this->debug) { $this->debugTxt .= "[fMysqli debug] qUpdate() <br />\n" . var_export($array, true) . " <br />\n"; }
		// Fields/FieldQuotes
		$tableFields 		= explode(',', $array['fields']);
		$tableFieldsQuotes 	= $this->_getFields($array['table']);
		$tmpSql = '';
		foreach ($array['data'] as $key => $value)
		{
			$tableFields[$key] = trim($tableFields[$key]);
			$tmpSql .= ($key) ? ',' : '';
			if ($tableFieldsQuotes[$tableFields[$key]] && (!isset($array['escape']) || $array['escape'])) { $value = $this->conn->real_escape_string($value); }
			$tmpSql .= ($tableFieldsQuotes[$tableFields[$key]] && $value != 'NULL') ? "{$tableFields[$key]} = '{$value}'" : "{$tableFields[$key]} = {$value}";			
		}
		$where = (isset($array['where'])) ? " WHERE {$array['where']}" : '';
		if ($tmpSql) { $this->_sqlQuery("UPDATE `{$array['table']}` SET {$tmpSql}{$where};"); }
		return array('affectedRows' => $this->conn->affected_rows);
	}
	
/**
* Use this function to execute INSERTs queries
* 
* <code>
array(
	'table' => 'table_name',									// (required)
	'escape' => true,											// (optional)
	'fields' => 'field_name1,field_name2,field_name3',			// (required)
	'data' => array(											// (required)
		array(
			value1,
			'value2',
			value3,
	 		array('value4', 'P')							// (optional) - 'P' means to use the PASSWORD() mysql function.
	 		array('value5', 'U')							// (optional) - 'U' means to use 'ON DUPLICATE KEY UPDATE', this field will be updated in a duplicate entry case. (cannot be used on multi-inserts)
 		)
	)
);
* </code>
* @param array $array
* @return rArray array('affectedRows' => ..., 'insertId' => ...);
*/
	public function qInsert($array)
	{
		if ($this->debug) { $this->debugTxt .= "[fMysqli debug] qInsert() <br />\n" . var_export($array, true) . " <br />\n"; }
		// Fields/FieldQuotes
		$tableFields 		= explode(',', $array['fields']);
		$tableFieldsQuotes 	= $this->_getFields($array['table']);
		// Tmp variables
		$tmp = array(
			'insertTimes' => 0,
			'insertSql' => '',
			'insertAffectedRow' => 0
		);
		$tmpOnDuplicate = array(
			'insertTimes' => 0,
			'insertSql' => '',
			'insertAffectedRow' => 0
		);
		// Thru one or more insert itens (building multi-inserts)
		foreach ($array['data'] as $entryKey => $entryArray)
		{
			$tmp['insertSql'] .= ($tmp['insertTimes']) ? ',(' : '(';
			// Thru the fields
			foreach ($tableFields as $fieldKey => $fieldName)
			{
				$fieldName = trim($fieldName);
				$field = $entryArray[$fieldKey];
				$tmp['insertSql'] .= ($fieldKey) ? ',' : '';
				$tmpContinue = true;
				if (is_array($field))
				{
					switch ($field[1])
					{
						case 'P':
							$tmp['insertSql'] .= (!isset($array['escape']) || $array['escape']) ? 'PASSWORD(\'' . $this->conn->real_escape_string($field[0]) . '\')' : 'PASSWORD(\'' . $field[0] . '\')';
							$tmpContinue = false;
						break;						
						case 'U':
							$tmpOnDuplicate['insertSql'] .= ($tmpOnDuplicate['insertTimes']) ? ',' : '';
							$tmpOnDuplicate['insertSql'] .= $fieldName . ' = ';
							if ($tableFieldsQuotes[$fieldName] && $field[0] != 'NULL')
								$tmpOnDuplicate['insertSql'] .= (!isset($array['escape']) || $array['escape']) ? '\'' . $this->conn->real_escape_string($field[0]) . '\'' : '\'' . $field[0] . '\'';
							else
								$tmpOnDuplicate['insertSql'] .= $field[0];
							$tmpOnDuplicate['insertTimes']++;
							$field = $field[0]; // tmpContinue keeps true
						break;
					}
				}
				if ($tmpContinue)
				{
					if (isset($tableFieldsQuotes[$fieldName]) && $tableFieldsQuotes[$fieldName] && $field != 'NULL')
						$tmp['insertSql'] .= (!isset($array['escape']) || $array['escape']) ? '\'' . $this->conn->real_escape_string($field) . '\'' : '\'' . $field . '\'';
					else
						$tmp['insertSql'] .= $field;
				}
			}
			$tmp['insertSql'] .= ')';
			$tmp['insertTimes']++;
			// If reach the limit ($this->multiInsert) - execute and reset
			if ($tmp['insertTimes'] == $this->multiInsert)
			{
				// Build the insert query
				$this->_sqlQuery("INSERT INTO `{$array['table']}` ({$array['fields']}) VALUES {$tmp['insertSql']};");
				$tmp['insertTimes'] 		= 0;
				$tmp['insertSql'] 			= '';
				$tmp['insertAffectedRow'] 	+= $this->conn->affected_rows;
			}
		}
		if ($tmp['insertSql']) 
		{
			// Build the insert query
			$query = "INSERT INTO `{$array['table']}` ({$array['fields']}) VALUES {$tmp['insertSql']}";
			// Add ON DUPLICATE if needed
			$query .= ($tmpOnDuplicate['insertTimes']) ? ' ON DUPLICATE KEY UPDATE ' . $tmpOnDuplicate['insertSql'] : '';
			$query .= ';';
			// Execute
			$this->_sqlQuery($query);
			$tmp['insertAffectedRow'] += $this->conn->affected_rows;
		}
		return array('affectedRows' => $tmp['insertAffectedRow'], 'insertId' => $this->conn->insert_id);
	}
	
	/**
	* Clear debug log
	*/
	public function resetDebug()
	{
		$this->debugTxt = '';
	}
	
	/**
	* Get debug log
	*/
	public function getDebug()
	{
		return $this->debugTxt;
	}
	
	/**
	* Set this variable
	*/
	public function setDieOnError($dieOnError) {
		$this->dieOnError = $dieOnError;		
	}
	
	/**
	* Close MySQL connect
	*/
	public function close()
	{
		$this->conn->close();
	}
	
	/**
	* Connect to MySQLi
	*/
	private function _connect()
	{
		if ($this->debug) { $this->debugTxt .= "[fMysqli debug] _connect() [{$this->host}, {$this->user}, ******, {$this->db}, {$this->port}] <br />\n"; }
		//$this->conn = new mysqli('p:'.$this->host, $this->user, $this->pass, $this->db, $this->port);
		$this->conn = new mysqli($this->host, $this->user, $this->pass, $this->db, $this->port);
		if ($this->conn->connect_errno) { $this->_sqlError(); }
		$this->_sqlQuery("SET NAMES 'utf8';", false);
	}
		
	
	/**
	* Execute a mysql query
	* 
	* @param string $sql
	* @param bol 	$retry
	* @return bol
	*/
	private function _sqlQuery($sql, $retry = true)
	{
		if ($this->debug) { $this->debugTxt .= "[fMysqli debug] _sqlQuery({$sql}, {$retry}) <br />\n"; }
		//echo $sql."\n";
		if (($this->qResult = $this->conn->query($sql)) === false)
		{
			if ($retry)
			{
				// try to reconnect
				$this->_connect();
				if (($this->qResult = $this->conn->query($sql)) === false)
				{
					$this->_sqlError($sql);
				}
			}
			else
			{
				$this->_sqlError($sql);
			}
		}
		return true;
	}
    
    /**
    * Show/log the error and die without mercy
    * 
    * @param string $sql
    * @return never
    */
    private function _sqlError($sql = '')
	{
		if (!$this->conn || $this->conn->connect_errno)
		{
			$error = 'Connect Error (' . $this->conn->connect_errno . ') - ' . $this->conn->connect_error . "\n";
			if ($this->logErrorFile) { $h = fopen($this->logErrorFile, 'a'); fwrite($h, '[' . date("d/m/Y H:i:s") . '] ' . $error); fclose($h); }
			if ($this->debug) { $this->debugTxt .= $error; }
			if ($this->dieOnError) { die($error); }
		}
		
		$error = 'SQL Error (' . $this->conn->errno . ') - ' . $this->conn->error . "\n\t" . $sql . "\n";
		if ($this->logErrorFile) { $h = fopen($this->logErrorFile, 'a'); fwrite($h, '[' . date("d/m/Y H:i:s") . '] ' . $error); fclose($h); }
		if ($this->debug) { $this->debugTxt .= $error; }
		if ($this->dieOnError) { die($error); }
	}
	
	/**
	* This return a array with all fields of the table.
	* Every field have flag indicate if it's needed use quotes in DML operations.
	* 
	* @param string $table
	* @return array
	*/
	private function _getFields($table)
	{
		if ($this->debug) { $this->debugTxt .= "[fMysqli debug] _getFields({$table}) <br />\n"; }
		$tableUniqueId = 'fields-' . $this->db . '-' . $table;
		// It's cached?
		$resultArray = apc_fetch($tableUniqueId);
		if ($resultArray) { return $resultArray; }
		// No, it's not. Let's work...
		$this->_sqlQuery("SHOW FULL FIELDS FROM `{$table}`;");
		$resultArray = array();
		while ($row = $this->qResult->fetch_assoc()) 
		{
			// Determines if the field needs string quotes
        	$resultArray[$row['Field']] = (preg_match('/(int\(|^bool|double|float|^set)/', $row['Type'])) ? false : true; // todo: upgrade the regx to all mysql types
    	}
    	// Cache it
    	apc_add($tableUniqueId, $resultArray, 86400);
    	return $resultArray;
	}
}
?>