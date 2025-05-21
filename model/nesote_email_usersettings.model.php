<?php 
class nesote_email_usersettings extends NesoteModel
{
public $id;
public $userid;
public $signature;
public $mailperpage;
public $lang_id;
public $theme_id;
public $display;
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

function getUserid()
	{
		if (get_magic_quotes_gpc()) {
            return stripslashes((string) $this->userid);
        } else {
            return $this->userid;
        }
	}

function getSignature()
	{
		if (get_magic_quotes_gpc()) {
            return stripslashes((string) $this->signature);
        } else {
            return $this->signature;
        }
	}

function getMailperpage()
	{
		if (get_magic_quotes_gpc()) {
            return stripslashes((string) $this->mailperpage);
        } else {
            return $this->mailperpage;
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

function getTheme_id()
	{
		if (get_magic_quotes_gpc()) {
            return stripslashes((string) $this->theme_id);
        } else {
            return $this->theme_id;
        }
	}

function getDisplay()
	{
		if (get_magic_quotes_gpc()) {
            return stripslashes((string) $this->display);
        } else {
            return $this->display;
        }
	}

function setId($id)
	{
	 $this->id=$id;                   
	}

function setUserid($userid)
	{
	 $this->userid=$userid;                   
	}

function setSignature($signature)
	{
	 $this->signature=$signature;                   
	}

function setMailperpage($mailperpage)
	{
	 $this->mailperpage=$mailperpage;                   
	}

function setLang_id($lang_id)
	{
	 $this->lang_id=$lang_id;                   
	}

function setTheme_id($theme_id)
	{
	 $this->theme_id=$theme_id;                   
	}

function setDisplay($display)
	{
	 $this->display=$display;                   
	}

};
?>