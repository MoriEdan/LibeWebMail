<?php 
class nesote_email_months_messages extends NesoteModel
{
public $id;
public $month_id;
public $lang_id;
public $message;
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

function getMonth_id()
	{
		if (get_magic_quotes_gpc()) {
            return stripslashes((string) $this->month_id);
        } else {
            return $this->month_id;
        }
	}

function getLang_id()
	{
		if (get_magic_quotes_gpc()) {
            return stripslashes((string) $this->lang_id);
        } else {
            return $this->lang_id;
        }
	}

function getMessage()
	{
		if (get_magic_quotes_gpc()) {
            return stripslashes((string) $this->message);
        } else {
            return $this->message;
        }
	}

function setId($id)
	{
	 $this->id=$id;                   
	}

function setMonth_id($month_id)
	{
	 $this->month_id=$month_id;                   
	}

function setLang_id($lang_id)
	{
	 $this->lang_id=$lang_id;                   
	}

function setMessage($message)
	{
	 $this->message=$message;                   
	}

};
?>