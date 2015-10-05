<?php
/*Code Created By A.Makki
Last Update : 1/12/2010 , 11:45Am 

Database Class.*/

class Database{
	
	//Define Varibles
	var $server   = ""; //database server
	var $user     = ""; //database login name
	var $pass     = ""; //database login password
	var $database = ""; //database name
	var $pre      = ""; //table prefix
	#######################
	//internal info
	var $record = array();
	
	var $error = "";
	var $errno = 0;
	
	//table name affected by SQL query
	var $field_table= "";
	
	//number of rows affected by SQL query
	var $affected_rows = 0;
	
	var $link_id = 0;
	var $query_id = 0;
	
	
	#-#############################################
	# desc: constructor
	function Database($server, $user, $pass, $database, $pre='')
	{
		$this->server=$server;
		$this->user=$user;
		$this->pass=$pass;
		$this->database=$database;
		$this->pre=$pre;
	}#-#constructor()
	
	
	#-#############################################
	# desc: connect and select database using vars above
	function connect()
	{
		$this->link_id=@mysql_connect($this->server,$this->user,"E_1008_b");
		mysql_query(" SET NAME 'utf8' ", $this->link_id);
	
		if (!$this->link_id) {//open failed
			$this->oops("Could not connect to server: <b>$this->server</b>.");
			}
	
		if(!@mysql_select_db($this->database, $this->link_id)) {//no database
			$this->oops("Could not open database: <b>$this->database</b>.");
			}
	
		// unset the data so it can't be dumped
		$this->server='';
		$this->user='';
		$this->pass='';
		$this->database='';
	}#-#connect()
	
	#-#############################################
	# desc: close the connection
	function close()
	{
		if(!mysql_close()){
			$this->oops("Connection close failed.");
		}
	}#-#close()
	
	function querySelect($qry)
	{
			$i=0;
			$data=array();
			$qry_result=mysql_query($qry) or die("QUERY ERROR1 => ".mysql_error());
			while ($row=mysql_fetch_assoc($qry_result)) 
			{
				$data[$i] = $row;
				$i++;
			}
			 
		  return $data;
	}
	
	function querySelectSingle($qry)
	{
		 $qry_result=mysql_query($qry) or die("$qry<br>QUERY ERROR7 => ".mysql_error());
		 $row=mysql_fetch_assoc($qry_result);
		 return $row;
	}
		
	function numRows($nr)
	{
		$qry_result1=mysql_query($nr) or die("QUERY ERROR2 => ".mysql_error());
		$num_row=mysql_num_rows($qry_result1);
		return $num_row;
	 
	}
	


	function queryExecute($query)
	{ 

		 $result_update=mysql_query($query) ;
		 if  (!$result_update) echo "$query<br>QUERY ERROR6 => ".mysql_error();
			  return mysql_affected_rows();
	}
		
	//---------------Merging Another Class-------------//
	function escape($string) 
	{
		if(get_magic_quotes_gpc()) $string = stripslashes($string);
		return mysql_real_escape_string($string);
	}#-#escape()
	
	function update($table, $data, $where='1')
	{
		$q="UPDATE `".$this->pre.$table."` SET ";
	
		foreach($data as $key=>$val) {
			if(strtolower($val)=='null') $q.= "`$key` = NULL, ";
			elseif(strtolower($val)=='now()') $q.= "`$key` = NOW(), ";
			else $q.= "`$key`='".$this->escape($val)."', ";
		}
	
		$q = rtrim($q, ', ') . ' WHERE '.$where.';';
	
		return $this->query($q);
	}#-#update()
	
	function insert($table, $data) 
	{
		$q="INSERT INTO `".$this->pre.$table."` ";
		$v=''; $n='';
	
		foreach($data as $key=>$val) {
			$n.="`$key`, ";
			if(strtolower($val)=='null') $v.="NULL, ";
			elseif(strtolower($val)=='now()') $v.="NOW(), ";
			else $v.= "'".$this->escape($val)."', ";
		}
	
		$q .= "(". rtrim($n, ', ') .") VALUES (". rtrim($v, ', ') .");";
	
		if($this->query($q)){
			$this->free_result();
			return mysql_insert_id();
		}
		else return false;

	}#-#insert()
	
	function query($sql) 
	{
		// do query
		$this->query_id = @mysql_query($sql, $this->link_id);
	
		if (!$this->query_id) {
			$this->oops("<b>MySQL Query fail:</b> $sql");
		}
		
		$this->affected_rows = @mysql_affected_rows();
	
		return $this->query_id;
	}#-#query()
	
	function free_result($query_id=-1) 
	{
		if ($query_id!=-1)
		{
			$this->query_id=$query_id;
		}
		
	}#-#free_result()
	
	function oops($msg='')
	{
		if($this->link_id>0)
		{
			$this->error=mysql_error($this->link_id);
			$this->errno=mysql_errno($this->link_id);
		}
	
		$this->error=mysql_error();
		$this->errno=mysql_errno();
		?>
			<table align="center" border="1" cellspacing="0" style="background:white;color:black;width:80%;">
			<tr><th colspan=2>Database Error</th></tr>
			<tr><td align="right" valign="top">Message:</td><td><?php echo $msg; ?></tr></td>
			<?php if(strlen($this->error)>0) echo '<tr><td align="right" valign="top" nowrap>MySQL Error:</td><td>'.$this->error.'</tr></td>'; ?>
			<tr><td align="right">Date:</td><td><?php echo date("l, F j, Y \a\\t g:i:s A"); ?></tr></td>
			<tr><td align="right">Script:</td><td><a href="<?php echo @$_SERVER['REQUEST_URI']; ?>"><?php echo @$_SERVER['REQUEST_URI']; ?></a></tr></td>
			<?php if(strlen(@$_SERVER['HTTP_REFERER'])>0) echo '<tr><td align="right">Referer:</td><td><a href="'.@$_SERVER['HTTP_REFERER'].'">'.@$_SERVER['HTTP_REFERER'].'</a></tr></td>'; ?>
			</table><div style="clear:both"></div>
		<?php
	}#-#oops()
			
}
//----------------------------------------------------------------
?>
