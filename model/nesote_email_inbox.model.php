<?php 
class nesote_email_inbox extends NesoteModel
{
public $id;
public $from_list;
public $to_list;
public $cc;
public $bcc;
public $subject;
public $body;
public $time;
public $status;
public $readflag;
public $starflag;
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

function getFrom_list()
	{
		if (get_magic_quotes_gpc()) {
            return stripslashes((string) $this->from_list);
        } else {
            return $this->from_list;
        }
	}

function getTo_list()
	{
		if (get_magic_quotes_gpc()) {
            return stripslashes((string) $this->to_list);
        } else {
            return $this->to_list;
        }
	}

function getCc()
	{
		if (get_magic_quotes_gpc()) {
            return stripslashes((string) $this->cc);
        } else {
            return $this->cc;
        }
	}

function getBcc()
	{
		if (get_magic_quotes_gpc()) {
            return stripslashes((string) $this->bcc);
        } else {
            return $this->bcc;
        }
	}

function getSubject()
	{
		if (get_magic_quotes_gpc()) {
            return stripslashes((string) $this->subject);
        } else {
            return $this->subject;
        }
	}

function getBody()
	{
		if (get_magic_quotes_gpc()) {
            return stripslashes((string) $this->body);
        } else {
            return $this->body;
        }
	}

function getTime()
	{
		if (get_magic_quotes_gpc()) {
            return stripslashes((string) $this->time);
        } else {
            return $this->time;
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

function getReadflag()
	{
		if (get_magic_quotes_gpc()) {
            return stripslashes((string) $this->readflag);
        } else {
            return $this->readflag;
        }
	}

function getStarflag()
	{
		if (get_magic_quotes_gpc()) {
            return stripslashes((string) $this->starflag);
        } else {
            return $this->starflag;
        }
	}

function setId($id)
	{
	 $this->id=$id;                   
	}

function setFrom_list($from_list)
	{
	 $this->from_list=$from_list;                   
	}

function setTo_list($to_list)
	{
	 $this->to_list=$to_list;                   
	}

function setCc($cc)
	{
	 $this->cc=$cc;                   
	}

function setBcc($bcc)
	{
	 $this->bcc=$bcc;                   
	}

function setSubject($subject)
	{
	 $this->subject=$subject;                   
	}

function setBody($body)
	{
	 $this->body=$body;                   
	}

function setTime($time)
	{
	 $this->time=$time;                   
	}

function setStatus($status)
	{
	 $this->status=$status;                   
	}

function setReadflag($readflag)
	{
	 $this->readflag=$readflag;                   
	}

function setStarflag($starflag)
	{
	 $this->starflag=$starflag;                   
	}

};
?>