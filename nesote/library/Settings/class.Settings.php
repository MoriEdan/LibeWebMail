<?php

class settings {

	public $db;
	public $setting = [];

	public function __construct(public $tablename = "nesote_zwp_settings", public $namefield = "name", public $valuefield = "value") {

		$this->db = new NesoteDALController();
		$this->loadValues();
	}

	public function loadvalues() {

		$this->db->select($this->tablename);
		$this->db->fields([$this->namefield, $this->valuefield]);
		$res = $this->db->query();
		while ($row = $res->fetchRow())
		{
			$this->setting[$row[0]] = $row[1];
		}
	}

	public function getvalue($name) {

		return $this->setting[$name];
	}

};


?>
