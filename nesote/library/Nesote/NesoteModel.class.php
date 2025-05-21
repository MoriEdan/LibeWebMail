<?php

class nesotemodel {


	public $tablefields = "";
	public $dalinstance;
	public $primarykeys = [];
	public $primaryvalues = [];
	public $qobj;

	public function __construct(public $modelname) {

		$this->modelname = "";
		$this->dalinstance = new NesoteDALController();
		$this->findFields();
	}

	public function findfields() {

		$methods = get_class_methods($this->modelname);
		$j = 0;
		$i = 0;
		while (count($methods) > $i)
		{
			if (preg_match("/set(.*)/i", $methods[$i]))
			{
				$this->tablefields[$j] = substr(strtolower($methods[$i]), 3);
				$j++;
			}
			$i++;
		}
	}

	public function getfieldvalues() {

		$fieldsvalues = [];
		$i = 0;
		while (count($this->tablefields) > $i)
		{
			$fname = "get" . ucfirst((string) $this->tablefields[$i]);
			$fieldsvalues[$i] = $this->$fname();
			if (get_magic_quotes_gpc())
			{
				$fieldsvalues[$i] = mysql_real_escape_string($fieldsvalues[$i]);
			}
			$i++;
		}
		return $fieldsvalues;
	}

	public function save() {

		$this->dalinstance->insert($this->modelname);
		$this->dalinstance->fields($this->tablefields);
		$this->dalinstance->values($this->getFieldValues());
		$this->qobj = $this->dalinstance->query();
		$id = $this->dalinstance->lastInsert();
		$i = 0;
		while (count($this->primarykeys) > $i)
		{
			if ($this->primaryvalues[$i] == "")
			{
				$fname = "set" . ucfirst((string) $this->tablefields[$i]);
				$this->$fname($id);
			}
			$i += 1;
		}
		$this->loadPrimayValues();
	}

	public function update() {

		$setvar = "";
		$i = 0;
		while (count($this->tablefields) > $i)
		{
			$setvar = $setvar . $this->tablefields[$i] . " = ?, ";
			$i++;
		}
		$setvar = substr($setvar, 0, -2);
		$setconditions = "";
		$i = 0;
		while (count($this->primarykeys) > $i)
		{
			$setconditions = $setconditions . $this->primarykeys[$i] . " = ? AND ";
			$i++;
		}
		$setconditions = substr($setconditions, 0, -4);
		$this->dalinstance->update($this->modelname);
		$this->dalinstance->set($setvar, $this->getFieldValues());
		$this->dalinstance->where($setconditions, $this->primaryKeyValues());
		$this->qobj = $this->dalinstance->query();
		$this->loadPrimayValues();
	}

	public function delete() {

		$setcondition = "";
		$i = 0;
		while (count($this->primarykeys) > $i)
		{
			$setcondition = $setcondition . $this->tablefields[$i] . " = ? AND ";
			$i++;
		}
		$setconditions = substr($setcondition, 0, -4);
		$this->dalinstance->delete($this->modelname);
		$this->dalinstance->where($setconditions, $this->primaryKeyValues());
		$this->qobj = $this->dalinstance->query();
	}

	public function load($values) {

		$row = [];
		if (count($values) !== count($this->primarykeys))
		{
			$ini_error_status = ini_get("error_reporting");
			if ($ini_error_status != 0)
			{
				echo "<br><strong>Error: </strong>Primary key value number mismatch for the model <b>'load'</b> function.";
			}
			die(0);
		}
		if (is_array($values))
		{
			$i = 0;
			while (count($values) > $i)
			{
				$this->primaryvalues[$i] = $values[$i];
				$i += 1;
			}
		}
		$this->primaryvalues[0] = $values;
        $setcondition = "";
        $i = 0;
        while (count($this->primarykeys) > $i)
			{
				$setcondition = $setcondition . $this->tablefields[$i] . " = ? AND ";
				$i++;
			}
        $setconditions = substr($setcondition, 0, -4);
        $this->dalinstance->select($this->modelname);
        $this->dalinstance->fields($this->tablefields);
        $this->dalinstance->where($setconditions, $this->primaryKeyValues());
        $this->qobj = $this->dalinstance->query();
        if (!($row = $this->dalinstance->result($this->qobj)))
			{
				return false;
			}
        $i = 0;
        while (count($this->tablefields) > $i)
			{
				$variablename = "" . $this->tablefields[$i];
				$this->$variablename = $this->dalinstance->dbReadSafe($row[0][$i]);
				$i++;
			}
        return true;
	}

	public function primarykey($keys) {

		if (is_array($keys))
		{
			$this->primarykeys = $keys;
			return;
		}
		$this->primarykeys[0] = $keys;
	}

	public function defaultvalues($defaults) {

		if (is_array($defaults))
		{
			$arraykeys = array_keys($defaults);
			$i = 0;
			while (count($arraykeys) > $i)
			{
				$fname = "set" . ucfirst($arraykeys[$i]);
				$this->$fname($defaults[$arraykeys[$i]]);
				$i++;
			}
		}
		$ini_error_status = ini_get("error_reporting");
		if ($ini_error_status != 0)
		{
			echo "<br><strong>Error: </strong> Invalid argument for the model <b>'defaultValues'</b> function.";
		}
		die(0);
	}

	public function primarykeyvalues() {

		return $this->primaryvalues;
	}

	public function loadprimayvalues() {

		$primaryvalues = [];
		$i = 0;
		while (count($this->primarykeys) > $i)
		{
			$variablename = "" . $this->primarykeys[$i];
			$primaryvalues[$i] = $this->$variablename;
			if ($primaryvalues[$i] == "")
			{
				$ini_error_status = ini_get("error_reporting");
				if ($ini_error_status != 0)
				{
					echo '<br><strong>Error: </strong>Primary key value(s) for the model <b>' . $this->modelname . "</b> is not assigned in controller action.";
				}
				die(0);
			}
			$i++;
		}
		$this->primaryvalues = $primaryvalues;
	}

	public function sql() {

		return $this->dalinstance->sql($this->qobj);
	}

	public function error() {

		return $this->dalinstance->getError($this->qobj);
	}

	public function numrows() {

		return $this->dalinstance->numRows($this->qobj);
	}

	public function resultarray() {

		return $this->dalinstance->resultArray($this->qobj);
	}

};


?>
