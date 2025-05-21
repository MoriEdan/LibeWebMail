<?php 
class nesote_email_languages extends NesoteModel
{
public $lang_code;
public $id;
public $language;
public $language_script;
public $status;
public $char_encoding;
public $lang_alignment;
public $image;
function __construct()
{
$this->primaryKey(["id"]);
$this->defaultValues(["lang_alignment"=>"1"]);

}
function getId()
	{
		if (get_magic_quotes_gpc()) {
            return stripslashes((string) $this->id);
        } else {
            return $this->id;
        }
	}

function getLanguage()
	{
		if (get_magic_quotes_gpc()) {
            return stripslashes((string) $this->language);
        } else {
            return $this->language;
        }
	}

function getLanguage_script()
	{
		if (get_magic_quotes_gpc()) {
            return stripslashes((string) $this->language_script);
        } else {
            return $this->language_script;
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

function getChar_encoding()
	{
		if (get_magic_quotes_gpc()) {
            return stripslashes((string) $this->char_encoding);
        } else {
            return $this->char_encoding;
        }
	}

function getLang_alignment()
	{
		if (get_magic_quotes_gpc()) {
            return stripslashes((string) $this->lang_alignment);
        } else {
            return $this->lang_alignment;
        }
	}

function getImage()
	{
		if (get_magic_quotes_gpc()) {
            return stripslashes((string) $this->image);
        } else {
            return $this->image;
        }
	}
	

function getLang_code()
	{
		if (get_magic_quotes_gpc()) {
            return stripslashes((string) $this->lang_code);
        } else {
            return $this->lang_code;
        }
	}
	
function setId($id)
	{
	 $this->id=$id;                   
	}

function setLanguage($language)
	{
	 $this->language=$language;                   
	}

function setLanguage_script($language_script)
	{
	 $this->language_script=$language_script;                   
	}

function setStatus($status)
	{
	 $this->status=$status;                   
	}

function setChar_encoding($char_encoding)
	{
	 $this->char_encoding=$char_encoding;                   
	}

function setLang_alignment($lang_alignment)
	{
	 $this->lang_alignment=$lang_alignment;                   
	}

function setImage($image)
	{
	 $this->image=$image;                   
	}
function setLang_code($lang_code)
	{
	 $this->lang_code=$lang_code;                   
	}

};
?>