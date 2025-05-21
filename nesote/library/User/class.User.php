<?php

class user {


	public $username_cookie = "io_username";
	public $password_cookie = "io_password";
	public $db;

	public function __construct(public $user_table, public $uid_field = "uid", public $username_field = "username", public $password_field = "password", public $email_field = "email", public $status_field = "status", public $active_status = 1) {

		$this->db = new NesoteDALController();
	}

	public function cookieuser($username, $password) {

		if ($username == "" || $password == "")
		{
			return FALSE;
		}
		$password = md5((string) $password);
		$condition = $this->username_field . "=? AND " . $this->password_field . "=? AND " . $this->status_field . "=?";
		$usercount = $this->db->total($this->user_table, $condition, [$username, $password, $this->active_status]);
		if (1 == $usercount)
		{
			setcookie($this->username_cookie, (string) $username, ['expires' => 0, 'path' => $this->getCurrentDirPath()]);
			setcookie($this->password_cookie, $password, ['expires' => 0, 'path' => $this->getCurrentDirPath()]);
			return TRUE;
		}
		return FALSE;
	}

	public function getcurrentdirpath() {

		return "/";
	}

	public function userexists($username, $status = 100) {

		$query = $this->username_field . "=?";
		$arr = [];
		if ($status != 100)
		{
			$query = $query . " AND " . $this->status_field . "=?";
			$arr = [$username, $status];
		}
		 else 
		{
			$arr = [$username];
		}
        return $this->db->total($this->user_table, $query, $arr) > 0;
	}

	public function validateuser() {

		if (!(isset($_COOKIE[$this->username_cookie])) || !(isset($_COOKIE[$this->password_cookie])))
		{
			return FALSE;
		}
		$username = $_COOKIE[$this->username_cookie];
		$password = $_COOKIE[$this->password_cookie];
		$usercount = $this->db->total($this->user_table, $this->username_field . "=? AND " . $this->password_field . "=? AND " . $this->status_field . "=?", [$username, $password, $this->active_status]);
        return 1 == $usercount;
	}

	public function getusername($id = "") {

		if ($id == "")
		{
			return $_COOKIE[$this->username_cookie];
		}
		$this->db->select($this->user_table);
		$this->db->fields($this->username_field);
		$this->db->where($this->uid_field . "=?", $id);
		$res = $this->db->query();
		$row = $res->fetchRow();
		return $row[0];
	}

	public function getemailid($username = "") {

		if ($username == "")
		{
			$username = $_COOKIE[$this->username_cookie];
		}
		$this->db->select($this->user_table);
		$this->db->fields($this->email_field);
		$this->db->where($this->username_field . "=?", $username);
		$res = $this->db->query();
		if ($res->getNumResults() == 1)
		{
			$row = $res->fetchRow();
			return $row[0];
		}
		return FALSE;
	}

	public function getuserid($username = "") {

		if ($username == "")
		{
			$username = $_COOKIE[$this->username_cookie];
		}
		$this->db->select($this->user_table);
		$this->db->fields($this->uid_field);
		$this->db->where($this->username_field . "=?", $username);
		$res = $this->db->query();
		$row = $this->db->fetchRow($res);
		return $row[0];
	}

	public function changepassword($oldpass, $newpass) {

		if (md5((string) $oldpass) == $_COOKIE[$this->password_cookie])
		{
			$this->db->update($this->user_table);
			$this->db->set($this->password_field . "=?", md5((string) $newpass));
			$this->db->where($this->username_field . "=?", $this->getUsername());
			$this->db->query();
			setcookie($this->password_cookie, md5((string) $newpass), ['expires' => 0, 'path' => $this->getCurrentDirPath()]);
			return TRUE;
		}
		return FALSE;
	}

	public function logout() {

		setcookie($this->username_cookie, "", ['expires' => 0, 'path' => $this->getCurrentDirPath()]);
		setcookie($this->password_cookie, "", ['expires' => 0, 'path' => $this->getCurrentDirPath()]);
	}

	public function sendpassword($username, $from, $subject) {

		$emailstring = "EOD\r
		\r
		Hello,\r
		Your login information is given below.\r
		Username : {USERNAME}\r
		New Password : {PASSWORD}\r
		\r
		Please login and change the the temporary password.\r
		\r
		Thanks.\r
		\r
		EOD";
		$this->db->select($this->user_table);
		$this->db->fields('' . $this->username_field . ", " . $this->email_field . ", " . $this->password_field);
		$this->db->where($this->username_field . "=?", $username);
		$res = $this->db->query();
		if ($res->getNumResults() == 1)
		{
			$row = $res->fetchRow();
			$username = $row[0];
			$email = $row[1];
			$oldpass = $row[2];
			$newpass = substr((string) $oldpass, 0, 7);
			$this->db->update($this->user_table);
			$this->db->set($this->password_field . "=?", md5($newpass));
			$this->db->where($this->username_field . "=?", $username);
			$this->db->query();
			$emailstring = str_replace("{USERNAME}", $username, $emailstring);
			$emailstring = str_replace("{PASSWORD}", $newpass, $emailstring);
            return mail('' . $email, '' . $subject, $emailstring, 'From: ' . $from . "\r" . "
" . ('Reply-To: ' . $from . "\r" . "
") . "X-Mailer: PHP/" . phpversion());
		}
		return FALSE;
	}

};


?>
