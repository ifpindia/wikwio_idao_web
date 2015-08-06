<?php 
// //////////////////////////////////////////////////
// PHP mysql_object class
// Mysql interface
// create : July 26 05
// last_modify : July 26 05
// Copyright (C) 2005 IFP -  Simon GEORGET
// //////////////////////////////////////////////////

error_reporting(E_ERROR);
class mysql {
    /* PROPERTIES */
    var $debug = true;
    var $con_link;
    var $error;
    var $errno;
    var $logpath="logs/mysql.log";


    /**
    * mysql::mysql()
    * Constructor
    * 
    * @access public 
    * @param string $user : user
    * @param string $pass : password
    * @param string $server : database server
    * @param string $db : database name
    */
    function mysql($user, $pass, $server, $db)
    {
	$this->con_link = @mysql_connect($server, $user, $pass);

	if (!$this->con_link) {
		$this->_setError();
	} else {
		$this->selectDB($db);
	}
    } 

    /**
    * mysql::selectDB()	
    * Select or Change Database connectivity
    * 
    * @access public 
    * @param $db string 
    * @return boolean 
    */
    function selectDB($db)
    {
	    $db_set = @mysql_select_db($db);
	    if(!$db_set) {
		    $this->_setError();
		    return false;
	    } else {
		    return true;
	    }
    }

    /**
    * mysql::setError()
    * Error Management
    * 
    * @access private 
    * @param string $msg_err : user message error (facultative)
    */
    function _setError($msg_err=-1)
    {
	    if ($this->con_link) {
		    $this->error = mysql_error($this->con_link);
		    $this->errno = mysql_errno($this->con_link);
		    @error_log($this->error.'::'.$this->errno, 3, $this->logpath);
	    }
	    if ($this->debug == true) echo $this->error.'::'.$this->errno;
    }

    /**
    * mysql::getLastID()
    * get last mysql ID
    *
    * @return string
    */
        function getLastID()
        {
                if ($this->con_link) {
                        return mysql_insert_id($this->con_link);
                } else {
                        return false;
                }
        }


    /**
    * mysql::insert()
    * database insert
    * 
    * @access public 
    * @param string $query : SQL query
    * @return integer
    */
    function insert ($query)
    {
	    if (!($this->execute($query))) {
		    return false;
	    } else {
		    return $this->getLastID();
	    }
    }





    /**
    * mysql::affectedRows()
    * Get Affected Rows number
    * 
    * @access public
    * @return integer
    */
        function affectedRows()
        {
                if ($this->con_link) {
                        return mysql_affected_rows($this->con_link);
                } else {
                        return false;
                }
        }

    /**
    * mysql::execute()
    * execute querys
    * 
    * @access public
    * @param string $query : SQL query
    * @return boolean
    */
    function execute($query)
    {
		mysql_set_charset("iso-8859-1");
	    $query=str_replace('{db_prefix}', $GLOBALS['ini']['database']['db_prefix'], $query);
	    //echo $query.'<br /><br />';
	    $res=mysql_query($query, $this->con_link) or die(mysql_error());
	    if(!$res) {
		    $this->_setError();
		    return false;
	    } else {
		    return $res;
	    }
    } 

    /**
    * mysql::select()
    * Querys - SELECT
    * 
    * @access public 
    * @param string $query : SQL query
    * @param string $type : DATA FORMAT OBJECT (default), ARRAY_NUM OR ASSOC, KEY-VALUE 
    * @return array : $result
    */

    function select($query, $type = 'OBJECT')
    {
	$i = 0;
	$res = $this->execute($query);
	
	//echo $this->affectedRows();
	
        if (!($res) || $this->affectedRows()==0) {
            return false;
        }
	
        if ($type == 'OBJECT') {
		if($this->affectedRows()!=1) 
		{
		    while ($data = @mysql_fetch_object($res)) 
		    {
			$rows[$i] = $data;
			$i++;
		    }
		}
		else 
		{
			$rows = @mysql_fetch_object($res);
			
		}
        }
        if ($type == 'ARRAY_NUM') {
		if($this->affectedRows()!=1) 
		{
			while ($data = @mysql_fetch_array($res, MYSQL_NUM)) 
			{
				$rows[$i] = $data;
				$i++;
			}
		}
		else 
		{
			$rows = @mysql_fetch_array($res, MYSQL_NUM);
		}
	}
        if ($type == 'ASSOC') {
		if($this->affectedRows()!=1) 
		{
			while ($data = mysql_fetch_assoc($res)) 
			{
				$rows[$i] = $data;
				$i++;
			}
		}
		else 
		{
			$rows = @mysql_fetch_assoc($res);					
		}
        }
        if ($type == 'KEY-VALUE') 
	{
            while ($data = @mysql_fetch_array($res, MYSQL_NUM)) 
	    {
		    $rows[$data[0]] = $data[1];
            }
        }
        @mysql_free_result($res);
	
	//echo $rows;
        return $rows;
    }
    
    /**
    * mysql::close()	
    * Close DB connection
    * 
    * @access public
    * @return boolean 
    */
    function close()
    {
	if ($this->con_id) {
		mysql_close($this->con_id);
		return true;
	} else {
		return false;
	}

    } 
} 

?>