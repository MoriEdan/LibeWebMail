<?php

class nesoteresultset {


	public $errorstring = "";
	public $sqlstring = "";
	public $totalresults = "";
	public $results = [];
	public $resultindex = "";

	public function setnumresults($arg) {

		$this->totalresults = $arg;
	}

	public function seterror($arg) {

		$this->errorstring = $arg;
	}

	public function setsqlstring($arg) {

		$this->sqlstring = $arg;
	}

	public function setresult($arg, $cnt) {

		$i = 0;
		while ($cnt > $i)
		{
			$this->results[$i] = $arg[$i];
			$i++;
		}
	}

	public function geterror() {

		return "SQL String: " . $this->sqlstring . "<br>" . $this->errorstring;
	}

	public function getresult() {

		return $this->results;
	}

	public function getnumresults() {

		return $this->totalresults;
	}

	public function getsqlstring() {

		return $this->sqlstring;
	}

	public function fetchrow() {

		if (count($this->results) == 0)
		{
			return false;
		}
		if ($this->resultindex == "")
		{
			$this->resultindex = 0;
		}
		if (count($this->results) > $this->resultindex)
		{
			$value = $this->results[$this->resultindex];
			$this->resultindex += 1;
			return $value;
		}
		return false;
	}

};


?>
