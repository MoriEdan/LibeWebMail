<?php


require_once '' . $library_path . "Nesote/NesoteResultSet.class.php";


class nesotedalcontroller {


	public $db_driver;
	public $sqlstring;
	public $action;
	public $tablename;
	public $tablealias;
	public $fieldnames;
	public $wherestring;
	public $limitstring;
	public $orderby;
	public $groupby;
	public $havingstring;
	public $joinvar;
	public $noescape;
	public $rightjoinvar;
	public $leftjoinvar;
	public $deletetables;
	public $setstring;
	public $fieldvalues;
	public $resultparams = [];
	public $results = [];
	public $resultindex;

	public function __construct($db_driver = "", $db_server = "", $db_username = "", $db_password = "", $db_name = "", public $db_tableprefix = "") {

		if ($db_driver == "" || $db_server == "" || $db_username == "" || $db_password == "" || $db_name == "")
		{
			require __DIR__ . "/script.inc.php";
			require $config_path . "database.default.config.php";
		}
		$this->db_driver = $db_driver;
		if ($this->db_driver == "mysql")
		{
			if (!(mysql_connect($db_server, $db_username, $db_password)))
			{
				die("Could not connect: " . mysql_error());
			}
			mysql_select_db($db_name);
			mysql_query("set names utf8 collate utf8_unicode_ci");
		}
		$this->valueReset();
	}

	public function dbsafevariable($var) {

		$str = !(get_magic_quotes_gpc()) && $this->noescape = 0 !== 0 ? mysql_real_escape_string($var) : $var;
		$escape = 0;
		if (substr_count((string) $str, "\\'") !== substr_count((string) $str, "'"))
		{
			$escape = 1;
		}
		$lastchar = substr((string) $str, -1);
		$lastsecondchar = substr((string) $str, -2, 1);
		if ($lastchar === "\\" && $lastsecondchar !== "\\")
		{
			$escape = 1;
		}
		if ($escape == 1)
		{
			return "'" . mysql_real_escape_string($str) . "'";
		}
		return "'" . $var . "'";
	}

	public function dbreadsafe($var) {

		if (get_magic_quotes_gpc())
		{
			if (get_magic_quotes_runtime())
			{
				return $var;
			}
			return mysql_real_escape_string($var);
		}
		if (get_magic_quotes_runtime())
		{
			return stripslashes((string) $var);
		}
		return $var;
	}

	public function valuereset() {

		$this->sqlstring = "";
		$this->action = "";
		$this->tablename = "";
		$this->tablealias = "";
		$this->fieldnames = "";
		$this->wherestring = "";
		$this->limitstring = "";
		$this->orderby = "";
		$this->groupby = "";
		$this->havingstring = "";
		$this->joinvar = "";
		$this->noescape = 0;
		$this->rightjoinvar = "";
		$this->leftjoinvar = "";
		$this->deletetables = "";
		$this->setstring = "";
		$this->fieldvalues = "";
		$this->resultindex = "";
	}

	public function select($tables) {

		$this->valueReset();
		$this->action = "select";
		if (is_array($tables))
		{
			$arraykeys = array_keys($tables);
			$i = 0;
			while (count($arraykeys) > $i)
			{
				$this->tablename = $this->tablename . " " . $this->db_tableprefix . $tables[$arraykeys[$i]] . " " . $arraykeys[$i] . ", ";
				$i++;
			}
			$this->tablename = substr((string) $this->tablename, 0, -2) . " ";
			return;
		}
		$this->tablename = " " . $this->db_tableprefix . $tables . " ";
	}

	public function fields($fields) {

		if (is_array($fields))
		{
			$fcount = count($fields);
			$i = 0;
			while ($fcount > $i)
			{
				if ($this->db_driver == "mysql")
				{
					$this->fieldnames = $this->fieldnames . " " . $fields[$i] . ", ";
				}
				$i++;
			}
			$this->fieldnames = substr((string) $this->fieldnames, 0, -2) . " ";
			return;
		}
		$this->fieldnames = " " . $fields . " ";
	}

	public function where($condition, $values = "", $noescape = 0) {

		if ($condition == "")
		{
			$ini_error_status = ini_get("error_reporting");
			if ($ini_error_status != 0)
			{
				echo "<br><strong>Error: </strong>Invalid condition for <strong>'where'</strong> function.";
			}
			die(0);
		}
		$this->noescape = $noescape;
		$this->wherestring = " WHERE ";
		if (is_array($values))
		{
			$arraycount = count($values);
			if ($arraycount !== substr_count((string) $condition, "?"))
			{
				$ini_error_status = ini_get("error_reporting");
				if ($ini_error_status != 0)
				{
					echo "<br><strong>Error: </strong>Argument mismatch for <strong>'where'</strong> function.";
				}
				die(0);
			}
			$temp = $condition;
			$condition = "";
			$i = 0;
			while ($arraycount > $i)
			{
				$condition .= substr((string) $temp, 0, strpos((string) $temp, "?"));
				$condition .= $this->dbSafeVariable($values[$i]);
				$temp = substr((string) $temp, strpos((string) $temp, "?") + 1);
				$i += 1;
			}
			$condition .= $temp;
		}
		 else 
		{
			$condition = str_replace("?", $this->dbSafeVariable($values), $condition);
		}
		$this->wherestring .= $condition;
	}

	public function having($condition, $values = "", $noescape = 0) {

		$this->noescape = $noescape;
		$this->havingstring = " HAVING ";
		if (is_array($values))
		{
			$arraycount = count($values);
			if ($arraycount !== substr_count((string) $condition, "?"))
			{
				$ini_error_status = ini_get("error_reporting");
				if ($ini_error_status != 0)
				{
					echo "<br><strong>Error: </strong>Argument mismatch for <strong>'having'</strong> function.";
				}
				die(0);
			}
			$temp = $condition;
			$condition = "";
			$i = 0;
			while ($arraycount > $i)
			{
				$condition .= substr((string) $temp, 0, strpos((string) $temp, "?"));
				$condition .= $this->dbSafeVariable($values[$i]);
				$temp = substr((string) $temp, strpos((string) $temp, "?") + 1);
				$i += 1;
			}
			$condition .= $temp;
		}
		 else 
		{
			$condition = str_replace("?", $this->dbSafeVariable($values[$i]), $condition);
		}
		$this->havingstring .= $condition;
	}

	public function limit($start, $end) {

		if ($this->db_driver == "mysql")
		{
			$this->limitstring = ' LIMIT ' . $start . "," . $end . " ";
		}
	}

	public function fetchrow($arg) {

		if (strtolower($arg::class) !== "nesoteresultset")
		{
			$ini_error_status = ini_get("error_reporting");
			if ($ini_error_status != 0)
			{
				echo "<br><strong>Error: </strong>Invalid argument for  <strong>'fetchRow'</strong> function.";
			}
			die(0);
		}
		return $arg->fetchRow();
	}

	public function order($fields) {

		if ($this->db_driver == "mysql")
		{
			$this->orderby = " ORDER BY ";
			if (is_array($fields))
			{
				$arraycount = count($fields);
				$i = 0;
				while ($arraycount > $i)
				{
					$this->orderby .= $fields[$i] . ", ";
					$i += 1;
				}
				$this->orderby = substr($this->orderby, 0, -2) . " ";
				return;
			}
			$this->orderby .= $fields;
		}
	}

	public function join($tables, $condition) {

		$joinvar = " JOIN ";
		if (is_array($tables))
		{
			$arraykeys = array_keys($tables);
			$i = 0;
			while (count($arraykeys) > $i)
			{
				$joinvar = $joinvar . " " . $this->db_tableprefix . $tables[$arraykeys[$i]] . " " . $arraykeys[$i] . ", ";
				$i++;
			}
			$joinvar = substr($joinvar, 0, -2) . " ";
		}
		 else 
		{
			$joinvar = $joinvar . " " . $this->db_tableprefix . $tables . " ";
		}
		if ($condition != "")
		{
			$joinvar .= " ON " . $condition;
		}
		$this->joinvar = $joinvar;
	}

	public function rightjoin($tables, $condition) {

		$rightjoinvar = " RIGHT JOIN ";
		if (is_array($tables))
		{
			$arraykeys = array_keys($tables);
			$i = 0;
			while (count($arraykeys) > $i)
			{
				$rightjoinvar = $rightjoinvar . " " . $this->db_tableprefix . $tables[$arraykeys[$i]] . " " . $arraykeys[$i] . ", ";
				$i++;
			}
			$rightjoinvar = substr($rightjoinvar, 0, -2) . " ";
		}
		 else 
		{
			$rightjoinvar = $rightjoinvar . " " . $this->db_tableprefix . $tables . " ";
		}
		if ($condition != "")
		{
			$rightjoinvar .= " ON " . $condition;
		}
		$this->rightjoinvar = $rightjoinvar;
	}

	public function leftjoin($tables, $condition) {

		$leftjoinvar = " LEFT JOIN ";
		if (is_array($tables))
		{
			$arraykeys = array_keys($tables);
			$i = 0;
			while (count($arraykeys) > $i)
			{
				$leftjoinvar = $leftjoinvar . " " . $this->db_tableprefix . $tables[$arraykeys[$i]] . " " . $arraykeys[$i] . ", ";
				$i++;
			}
			$leftjoinvar = substr($leftjoinvar, 0, -2) . " ";
		}
		 else 
		{
			$leftjoinvar = $leftjoinvar . " " . $this->db_tableprefix . $tables . " ";
		}
		if ($condition != "")
		{
			$leftjoinvar .= " ON " . $condition;
		}
		$this->leftjoinvar = $leftjoinvar;
	}

	public function group($fieldname) {

		if ($this->db_driver == "mysql")
		{
			$this->groupby = ' GROUP BY ' . $fieldname . " ";
		}
	}

	public function setsql($sql) {

		$this->sqlstring = $sql;
		$this->action = "unknown";
	}

	public function setselectsql($sql) {

		$this->setSQL($sql);
	}

	public function query() {

		if ($this->action == "select" && $this->db_driver == "mysql")
		{
			$this->sqlstring = " SELECT " . $this->fieldnames . " FROM " . $this->tablename . $this->joinvar . $this->leftjoinvar . $this->rightjoinvar . $this->wherestring . $this->groupby . $this->havingstring . $this->orderby . $this->limitstring . ";";
		}
		if ($this->action == "delete" && $this->db_driver == "mysql")
		{
			$this->sqlstring = " DELETE " . $this->deletetables . " FROM " . $this->tablename . $this->joinvar . $this->leftjoinvar . $this->rightjoinvar . $this->wherestring . ";";
		}
		if ($this->action == "update" && $this->db_driver == "mysql")
		{
			$this->sqlstring = " UPDATE " . $this->tablename . $this->joinvar . $this->leftjoinvar . $this->rightjoinvar . $this->setstring . $this->wherestring . ";";
		}
		if ($this->action == "insert" && $this->db_driver == "mysql")
		{
			$this->sqlstring = " INSERT INTO " . $this->tablename . " ( " . $this->fieldnames . " ) " . "VALUES " . $this->fieldvalues . ";";
		}
		$rsset = new NesoteResultSet();
		if ($this->db_driver == "mysql")
		{
			$result = mysql_query($this->sqlstring);
			$this->resultparams[0] = $this->sqlstring;
			$this->resultparams[1] = mysql_error();
			if (strlen((string) $this->resultparams[1]) > 1)
			{
				$ini_error_status = ini_get("error_reporting");
				if ($ini_error_status != 0)
				{
					echo $this->getQuery() . "<br><strong>MySQL Error: " . $this->resultparams[1] . "</strong>";
				}
			}
			$sqltype = "";
			if ($this->action == "unknown")
			{
				$unknown_sql = trim((string) $this->sqlstring);
				$sqltype = strtolower(substr($unknown_sql, 0, 6));
			}
			if ($this->action == "select" || $sqltype === "select")
			{
				$this->resultparams[2] = mysql_num_rows($result);
				$this->resultindex = 0;
				$i = 0;
				while ($row = mysql_fetch_row($result))
				{
					$this->results[$i++] = $row;
				}
				$rsset->setResult($this->results, $this->resultparams[2]);
			}
			 else 
			{
				$this->resultparams[2] = mysql_affected_rows();
			}
			$rsset->setNumResults($this->resultparams[2]);
			$rsset->setError($this->resultparams[1]);
			$rsset->setSQLString($this->sqlstring);
		}
		return $rsset;
	}

	public function getquery() {

		if ($this->action == "select" && $this->db_driver == "mysql")
		{
			$this->sqlstring = " SELECT " . $this->fieldnames . " FROM " . $this->tablename . $this->joinvar . $this->leftjoinvar . $this->rightjoinvar . $this->wherestring . $this->groupby . $this->havingstring . $this->orderby . $this->limitstring . ";";
		}
		if ($this->action == "delete" && $this->db_driver == "mysql")
		{
			$this->sqlstring = " DELETE " . $this->deletetables . " FROM " . $this->tablename . $this->joinvar . $this->leftjoinvar . $this->rightjoinvar . $this->wherestring . ";";
		}
		if ($this->action == "update" && $this->db_driver == "mysql")
		{
			$this->sqlstring = " UPDATE " . $this->tablename . $this->joinvar . $this->leftjoinvar . $this->rightjoinvar . $this->setstring . $this->wherestring . ";";
		}
		if ($this->action == "insert" && $this->db_driver == "mysql")
		{
			$this->sqlstring = " INSERT INTO " . $this->tablename . " ( " . $this->fieldnames . " ) " . "VALUES " . $this->fieldvalues . ";";
		}
		return $this->sqlstring;
	}

	public function lastinsert() {

		if ($this->db_driver == "mysql")
		{
			$lastid = mysql_query("SELECT LAST_INSERT_ID() ");
			$row = mysql_fetch_row($lastid);
			return $row[0];
		}
		return null;
	}

	public function sql($arg) {

		if (strtolower($arg::class) !== "nesoteresultset")
		{
			$ini_error_status = ini_get("error_reporting");
			if ($ini_error_status != 0)
			{
				echo "<br><strong>Error: </strong>Invalid argument for  <strong>'sql'</strong> function.";
			}
			die(0);
		}
		return $arg->getSQLString();
	}

	public function error($arg) {

		if (strtolower($arg::class) !== "nesoteresultset")
		{
			$ini_error_status = ini_get("error_reporting");
			if ($ini_error_status != 0)
			{
				echo "<br><strong>Error: </strong>Invalid argument for  <strong>'error'</strong> function.";
			}
			die(0);
		}
		return $arg->getError();
	}

	public function numrows($arg) {

		if (strtolower($arg::class) !== "nesoteresultset")
		{
			$ini_error_status = ini_get("error_reporting");
			if ($ini_error_status != 0)
			{
				echo "<br><strong>Error: </strong>Invalid argument for  <strong>'numRows'</strong> function.";
			}
			die(0);
		}
		return $arg->getNumResults();
	}

	public function result($arg) {

		if (strtolower($arg::class) !== "nesoteresultset")
		{
			$ini_error_status = ini_get("error_reporting");
			if ($ini_error_status != 0)
			{
				echo "<br><strong>Error: </strong>Invalid argument for  <strong>'result'</strong> function.";
			}
			die(0);
		}
		return $arg->getResult();
	}

	public function delete($tables, $selecttables = "") {

		$this->valueReset();
		$this->action = "delete";
		if ($selecttables != "")
		{
			if (is_array($selecttables))
			{
				$i = 0;
				while (count($selecttables) > $i)
				{
					$this->tablename = $this->tablename . " " . $this->db_tableprefix . $selecttables[$i] . ", ";
					$i++;
				}
				$this->tablename = substr((string) $this->tablename, 0, -2) . " ";
			}
			 else 
			{
				$this->tablename = " " . $this->db_tableprefix . $tables . " ";
			}
			if (is_array($tables))
			{
				$i = 0;
				while (count($tables) > $i)
				{
					$this->deletetables = $this->deletetables . " " . $this->db_tableprefix . $tables[$i] . ", ";
					$i++;
				}
				$this->deletetables = substr((string) $this->deletetables, 0, -2) . " ";
				return;
			}
			$this->deletetables = " " . $this->db_tableprefix . $tables . " ";
			return;
		}
		if (is_array($tables))
		{
			$i = 0;
			while (count($tables) > $i)
			{
				$this->tablename = $this->tablename . " " . $this->db_tableprefix . $tables[$i] . ", ";
				$i++;
			}
			$this->tablename = substr((string) $this->tablename, 0, -2) . " ";
			$this->deletetables = $this->tablename;
			return;
		}
		$this->tablename = " " . $this->db_tableprefix . $tables . " ";
	}

	public function update($tables) {

		$this->valueReset();
		$this->action = "update";
		if (is_array($tables))
		{
			$arraykeys = array_keys($tables);
			$i = 0;
			while (count($arraykeys) > $i)
			{
				$this->tablename = $this->tablename . " " . $this->db_tableprefix . $tables[$arraykeys[$i]] . " " . $arraykeys[$i] . ", ";
				$i++;
			}
			$this->tablename = substr((string) $this->tablename, 0, -2) . " ";
			return;
		}
		$this->tablename = " " . $this->db_tableprefix . $tables . " ";
	}

	public function set($condition, $values = "", $noescape = 0) {

		$this->noescape = $noescape;
		$this->setstring = " SET ";
		if (is_array($values))
		{
			$arraycount = count($values);
			if ($arraycount !== substr_count((string) $condition, "?"))
			{
				$ini_error_status = ini_get("error_reporting");
				if ($ini_error_status != 0)
				{
					echo "<br><strong>Error: </strong>Argument mismatch for <strong>'set'</strong> function.";
				}
				die(0);
			}
			$temp = $condition;
			$condition = "";
			$i = 0;
			while ($arraycount > $i)
			{
				$condition .= substr((string) $temp, 0, strpos((string) $temp, "?"));
				$condition .= $this->dbSafeVariable($values[$i]);
				$temp = substr((string) $temp, strpos((string) $temp, "?") + 1);
				$i += 1;
			}
			$condition .= $temp;
		}
		 else 
		{
			$condition = str_replace("?", $this->dbSafeVariable($values), $condition);
		}
		$this->setstring .= $condition;
	}

	public function insert($tablename = "") {

		$this->valueReset();
		$this->action = "insert";
		if ($tablename != "")
		{
			$this->tablename = " " . $this->db_tableprefix . $tablename . " ";
			return;
		}
		$ini_error_status = ini_get("error_reporting");
		if ($ini_error_status != 0)
		{
			echo "<br><strong>Error: </strong>Table name not specified for the <strong>'insert'</strong> function.";
		}
		die(0);
	}

	public function values($values, $noescape = 0) {

		$this->noescape = $noescape;
		if (is_array($values))
		{
			$fcount = count($values);
			$this->fieldvalues = " ( ";
			$i = 0;
			while ($fcount > $i)
			{
				if ($this->db_driver == "mysql")
				{
					$temp = $values[$i];
					if (is_array($temp))
					{
						$cnt = count($temp);
						if ($i > 0)
						{
							$this->fieldvalues .= " ( ";
						}
						$j = 0;
						while ($cnt > $j)
						{
							$this->fieldvalues = $this->fieldvalues . " " . $this->dbSafeVariable($temp[$j]) . ", ";
							$j++;
						}
						if ($i < $fcount - 1)
						{
							$this->fieldvalues = substr($this->fieldvalues, 0, -2) . " ";
							$this->fieldvalues .= " ), ";
						}
					}
					 else 
					{
						$this->fieldvalues = $this->fieldvalues . " " . $this->dbSafeVariable($temp) . ", ";
					}
				}
				$i++;
			}
			$this->fieldvalues = substr($this->fieldvalues, 0, -2) . " ";
			$this->fieldvalues .= " ) ";
			return;
		}
		$ini_error_status = ini_get("error_reporting");
		if ($ini_error_status != 0)
		{
			echo "<br><strong>Error: 'values'</strong> function accepts only an array as its argument.";
		}
		die(0);
	}

	public function selectlastrow($tablename, $primarykey) {

		$this->valueReset();
		$this->select($tablename);
		$this->fields("*");
		$this->order('' . $primarykey . " DESC");
		$this->limit(0, 1);
		$res = $this->query();
		if ($this->numRows($res) == 0)
		{
			return false;
		}
		return $this->fetchRow($res);
	}

	public function selectlastkey($tablename, $primarykey) {

		$this->valueReset();
		$this->select($tablename);
		$this->fields($primarykey);
		$this->order('' . $primarykey . " DESC");
		$this->limit(0, 1);
		$res = $this->query();
		if ($this->numRows($res) == 0)
		{
			return false;
		}
		$row = $this->fetchRow($res);
		return $row[0];
	}

	public function total($tablename, $condition = "", $values = "", $noescape = 0) {

		$this->valueReset();
		$this->noescape = $noescape;
		$this->select($tablename);
		$this->fields("count(*)");
		if ($condition == "")
		{
			$this->where("1", $values, $noescape);
		}
		 else 
		{
			$this->where($condition, $values, $noescape);
		}
		$res = $this->query();
		$row = $this->fetchRow($res);
		return $row[0];
	}

	public function selecttablefields($tablename, $noescape = 0) {

		$this->valueReset();
		$this->noescape = $noescape;
		if ($tablename == "")
		{
			$ini_error_status = ini_get("error_reporting");
			if ($ini_error_status != 0)
			{
				echo "<br><strong>Error: </strong>Table name not specified for <strong>'selectTableFields'</strong> function.";
			}
			die(0);
		}
		$rsset = new NesoteResultSet();
		if ($this->db_driver == "mysql")
		{
			$this->sqlstring = "SHOW COLUMNS FROM ";
			$this->sqlstring .= $tablename;
			$result = mysql_query($this->sqlstring);
			$this->resultparams[0] = $this->sqlstring;
			$this->resultparams[1] = mysql_error();
			if (strlen((string) $this->resultparams[1]) > 1)
			{
				echo $this->sqlstring;
				$ini_error_status = ini_get("error_reporting");
				if ($ini_error_status != 0)
				{
					echo "<br><strong>MySQL Error: " . $this->resultparams[1] . "</strong>";
				}
			}
			$this->resultparams[2] = mysql_num_rows($result);
			$this->resultindex = 0;
			$i = 0;
			while ($row = mysql_fetch_row($result))
			{
				$this->results[$i++] = $row;
			}
			$rsset->setResult($this->results, $this->resultparams[2]);
			$rsset->setNumResults($this->resultparams[2]);
			$rsset->setError($this->resultparams[1]);
			$rsset->setSQLString($this->sqlstring);
		}
		return $rsset;
	}

	public function selecttables($db_name = "", $noescape = 0) {

		$this->valueReset();
		$this->noescape = $noescape;
		if ($db_name == "")
		{
			require __DIR__ . "/script.inc.php";
			require $config_path . "database.default.config.php";
		}
		$rsset = new NesoteResultSet();
		if ($this->db_driver == "mysql")
		{
			$this->sqlstring = "SHOW TABLES FROM ";
			$this->sqlstring .= $db_name;
			$result = mysql_query($this->sqlstring);
			$this->resultparams[0] = $this->sqlstring;
			$this->resultparams[1] = mysql_error();
			if (strlen((string) $this->resultparams[1]) > 1)
			{
				echo $this->sqlstring;
				$ini_error_status = ini_get("error_reporting");
				if ($ini_error_status != 0)
				{
					echo "<br><strong>MySQL Error: </strong>" . $this->resultparams[1];
				}
			}
			$this->resultparams[2] = mysql_num_rows($result);
			$this->resultindex = 0;
			$i = 0;
			while ($row = mysql_fetch_row($result))
			{
				$this->results[$i++] = $row;
			}
			$rsset->setResult($this->results, $this->resultparams[2]);
			$rsset->setNumResults($this->resultparams[2]);
			$rsset->setError($this->resultparams[1]);
			$rsset->setSQLString($this->sqlstring);
		}
		return $rsset;
	}

};


?>
