<?php 
class nesote_email_admin extends NesoteModel
{
public $id;
public $username;
public $password;
public $lastlogin;
public $status;
function __construct()
{
$this->primaryKey(["id"]);
$this->defaultValues([]);

}
function getId()
	{
		if (get_magic_quotes_gpc()) {
            return stripslashes((string) $this->id);
        } else {
            return $this->id;
        }
	}

function getUsername()
	{
		if (get_magic_quotes_gpc()) {
            return stripslashes((string) $this->username);
        } else {
            return $this->username;
        }
	}

function getPassword()
	{
		if (get_magic_quotes_gpc()) {
            return stripslashes((string) $this->password);
        } else {
            return $this->password;
        }
	}

function getLastlogin()
	{
		if (get_magic_quotes_gpc()) {
            return stripslashes((string) $this->lastlogin);
        } else {
            return $this->lastlogin;
        }
	}

function getStatus()
	{
		if (get_magic_quotes_gpc()) {
            return stripslashes((string) $this->status);
        } else {
            return $this->status;
        }
	}

function setId($id)
	{
	 $this->id=$id;                   
	}

function setUsername($username)
	{
	 $this->username=$username;                   
	}

function setPassword($password)
	{
	 $this->password=$password;                   
	}

function setLastlogin($lastlogin)
	{
	 $this->lastlogin=$lastlogin;                   
	}

function setStatus($status)
	{
	 $this->status=$status;                   
	}

};
?>