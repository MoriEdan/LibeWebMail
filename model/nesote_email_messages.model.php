<?php 
class nesote_email_messages extends NesoteModel
{
public $id;
public $msg_id;
public $lang_id;
public $wordscript;
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

function getMsg_id()
	{
		if (get_magic_quotes_gpc()) {
            return stripslashes((string) $this->msg_id);
        } else {
            return $this->msg_id;
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

function getWordscript()
	{
		if (get_magic_quotes_gpc()) {
            return stripslashes((string) $this->wordscript);
        } else {
            return $this->wordscript;
        }
	}

function setId($id)
	{
	 $this->id=$id;                   
	}

function setMsg_id($msg_id)
	{
	 $this->msg_id=$msg_id;                   
	}

function setLang_id($lang_id)
	{
	 $this->lang_id=$lang_id;                   
	}

function setWordscript($wordscript)
	{
	 $this->wordscript=$wordscript;                   
	}

};
?>