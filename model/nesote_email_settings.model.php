<?php 
class nesote_email_settings extends NesoteModel
{
public $id;
public $name;
public $value;
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

function getValue()
	{
		if (get_magic_quotes_gpc()) {
            return stripslashes((string) $this->value);
        } else {
            return $this->value;
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

function setValue($value)
	{
	 $this->value=$value;                   
	}

};
?>