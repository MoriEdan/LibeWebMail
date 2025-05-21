<?php 
class nesote_email_reservedemail extends NesoteModel
{
public $id;
public $name;
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

function getName()
	{
		if (get_magic_quotes_gpc()) {
            return stripslashes((string) $this->name);
        } else {
            return $this->name;
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

function setName($name)
	{
	 $this->name=$name;                   
	}

function setStatus($status)
	{
	 $this->status=$status;                   
	}

};
?>