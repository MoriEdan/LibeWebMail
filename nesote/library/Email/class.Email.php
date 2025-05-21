<?php

define("EmailNewLine", "\r\n");
define("EmailXMailer", "PHP-EMAIL,v2.0 (wmfwlr AT cogeco DOT ca)");
define("EmailDefaultCharset", "iso-8859-1");
define("EmailIsDebugging", false);


class email {


	public $To;
	public $Cc;
	public $Bcc;
	public $From;
	public $Subject;
	public $Versions = [];
	public $Attachments = [];
	public $Headers;
	public $RootContainer;

	public function __construct($to = null, $from = null, $subject = null, $headers = null, $mailserver_type = 0) {

		$this->To = strval($to);
		$this->From = strval($from);
		$this->Subject = strval($subject);
		$this->Headers = strval($headers);
		$this->RootContainer = new ContainerMimeBlock("multipart/mixed");
		if ($mailserver_type == 1)
		{
			define("EmailNewLine", "\n");
		}
	}

	public function settextcontent($content) {

		return $this->SetContent($content, "text/plain", "8bit");
	}

	public function sethtmlcontent($content) {

		return $this->SetContent($content, "text/html", "8bit");
	}

	public function setcontent($content, $mimeType = "text/plain", $encoding = "8bit") {

		$contentBlock = new LiteralMimeBlock($mimeType, $content, $encoding);
		return $this->AddContentBlock($contentBlock);
	}

	public function setfilecontent($pathToFile, $mimeType = null) {

		$fileVersion = new AttachmentMimeBlock($mimeType, $pathToFile);
		$fileVersion->IsAttachment = false;
		if (!($fileVersion->IsValid()))
		{
			return false;
		}
		$this->Versions[] = $fileVersion;
		return true;
	}

	public function addcontentblock($mimeBlock = null) {

		if (!$mimeBlock || !($mimeBlock->IsValid()))
		{
			return false;
		}
		$this->Versions[] = $mimeBlock;
		return true;
	}

	public function attach($pathToFile, $mimeType = null) {

		$attachment = new AttachmentMimeBlock($mimeType, $pathToFile);
		if (!($attachment->IsValid()))
		{
			return false;
		}
		$this->Attachments[] = $attachment;
		return true;
	}

	public function iscomplete() {

		return strlen(trim((string) $this->To)) > 0 && strlen(trim((string) $this->From)) > 0;
	}

	public function clear() {

		$this->RootContainer->Clear();
		$this->Versions = [];
		$this->Attachments = [];
	}

	public function send() {

		if (!($this->IsComplete()))
		{
			return false;
		}
		$headers = "Date: " . date("r", time()) . EmailNewLine . "From: " . strval($this->From) . EmailNewLine;
		if (strlen(trim(strval($this->Cc))) > 0)
		{
			$headers .= "CC: " . strval($this->Cc) . EmailNewLine;
		}
		if (strlen(trim(strval($this->Bcc))) > 0)
		{
			$headers .= "BCC: " . strval($this->Bcc) . EmailNewLine;
		}
		if ($this->Headers != null && strlen(trim((string) $this->Headers)) > 0)
		{
			$headers .= $this->Headers . EmailNewLine;
		}
		$hasMultipleVersions = count($this->Versions) > 1;
		$hasOneVersion = count($this->Versions) == 1;
		$hasAttachments = count($this->Attachments) > 0;
		if ($hasMultipleVersions && $hasAttachments) {
            $this->RootContainer->ContentType = "multipart/mixed";
            $contentContainer = new ContainerMimeBlock("multipart/alternative");
            foreach ($this->Versions as $mimeVersion)
			{
				$contentContainer->Add($mimeVersion);
			}
            $this->RootContainer->Add($contentContainer);
            foreach ($this->Attachments as $mimeFile)
			{
				$this->RootContainer->Add($mimeFile);
			}
        } elseif ($hasMultipleVersions) {
            $this->RootContainer->ContentType = "multipart/alternative";
            foreach ($this->Versions as $mimeVersion)
				{
					$this->RootContainer->Add($mimeVersion);
				}
        } elseif ($hasAttachments) {
            $this->RootContainer->ContentType = "multipart/mixed";
            if ($hasOneVersion)
					{
						$this->RootContainer->Add($this->Versions[0]);
					}
            foreach ($this->Attachments as $mimeFile)
					{
						$this->RootContainer->Add($mimeFile);
					}
        } elseif ($hasOneVersion) {
            $this->RootContainer->ContentType = "multipart/mixed";
            $this->RootContainer->Add($this->Versions[0]);
        }
		$headers .= "X-Mailer: " . EmailXMailer . EmailNewLine . "MIME-Version: 1.0" . EmailNewLine . "Content-Type: " . $this->RootContainer->ContentType . "; " . "boundary=\"" . $this->RootContainer->Boundary . "\"" . EmailNewLine . EmailNewLine;
		$thebody = $this->RootContainer->GetEncodedData();
		$this->RootContainer->Clear();
		if (EmailIsDebugging)
		{
			print "<b>&lt;email&gt;</b><br>";
			print str_replace(EmailNewLine, "&lt;newline&gt;<br>", $headers);
			print str_replace(EmailNewLine, "&lt;newline&gt;<br>", $thebody);
			print "<br><b>&lt;/email&gt;</b><br>";
		}
		if (str_contains((string) $this->From, "<"))
		{
			preg_match("/.* <(.*)>/iU", (string) $this->From, $m);
			$env_sender = trim($m[1]);
		}
		 else 
		{
			$env_sender = $this->From;
		}
		if (!(mail((string) $this->To, (string) $this->Subject, (string) $thebody, $headers, '-f' . $env_sender)))
		{
			return mail((string) $this->To, (string) $this->Subject, (string) $thebody, $headers);
		}
		return true;
	}

	public function isvalid($email) {

		$result = TRUE;
		if (!(preg_match('^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,4})$', (string) $email)))
		{
			$result = FALSE;
		}
		return $result;
	}

};

class mimeblock {


	public $ContentType;
	public $Encoding;

	public function __construct($type, $encMethod = null) {

		$this->ContentType = strval($type);
		$this->Encoding = strval($encMethod);
	}

	public function isvalid() {

		return false;
	}

	public function hasencoding() {

		return strlen(trim((string) $this->Encoding)) > 0;
	}

	public function getencodeddata() {

		return "";
	}

	public function getadditionalcontenttypeheader() {

		return "";
	}

	public function getcustomheaders() {

		return "";
	}

	public function tostring() {

		$text = "Content-Type: " . $this->ContentType . $this->GetAdditionalContentTypeHeader() . EmailNewLine;
		if ($this->HasEncoding())
		{
			$text .= "Content-Transfer-Encoding: " . $this->Encoding . EmailNewLine;
		}
		$text .= $this->GetCustomHeaders();
		$text .= EmailNewLine;
		return $text . $this->GetEncodedData();
	}

};

class literalmimeblock extends mimeblock {


	public $LiteralContent;
	public $Charset = EmailDefaultCharset;

	#[\Override]
    public function isvalid() {

		return strlen((string) $this->LiteralContent) > 0;
	}

	#[\Override]
    public function getencodeddata() {

		return $this->LiteralContent;
	}

	#[\Override]
    public function getadditionalcontenttypeheader() {

		return "; charset=\"" . $this->Charset . "\"";
	}

	public function __construct($type, $content, $encMethod = null) {

		$this->LiteralContent = strval($content);
	}

};

class attachmentmimeblock extends mimeblock {


	public $FilePath;
	public $IsAttachment = true;

	#[\Override]
    public function isvalid() {

		return $this->Exists();
	}

	#[\Override]
    public function getencodeddata() {

		$fileData = "";
		if ($this->Exists())
		{
			$thefile = fopen($this->FilePath, "rb");
			$fileData = fread($thefile, filesize($this->FilePath));
			fclose($thefile);
		}
		$encData = chunk_split(base64_encode($fileData), 76, EmailNewLine);
		return substr($encData, 0, strlen($encData) - strlen(EmailNewLine));
	}

	#[\Override]
    public function getadditionalcontenttypeheader() {

		if ($this->IsAttachment)
		{
			return "; name=\"" . basename((string) $this->FilePath) . "\"";
		}
		return "";
	}

	#[\Override]
    public function getcustomheaders() {

		if ($this->IsAttachment)
		{
			return "Content-Disposition: attachment; filename=\"" . basename((string) $this->FilePath) . "\"" . EmailNewLine;
		}
		return "";
	}

	#[\Override]
    public function tostring() {

		if (strlen(trim((string) $this->ContentType)) == 0)
		{
			$this->ContentType = "application/octet-stream";
		}
		return mimeblock::tostring();
	}

	public function __construct($type, $filePath) {

		$this->FilePath = strval($filePath);
	}

	public function exists() {

		if ($this->FilePath == null || strlen(trim((string) $this->FilePath)) == 0)
		{
			return false;
		}
		return file_exists($this->FilePath);
	}

};

class containermimeblock extends mimeblock {


	public $Boundary;
	public $Blocks = [];

	#[\Override]
    public function isvalid() {

		return $this->Blocks != null && count($this->Blocks) > 0;
	}

	#[\Override]
    public function hasencoding() {

		return false;
	}

	#[\Override]
    public function getencodeddata() {

		$text = "";
		if (!($this->IsValid()))
		{
			return $text;
		}
		foreach ($this->Blocks as $mimeBlock)
		{
			$text .= "--" . $this->Boundary . EmailNewLine . $mimeBlock->ToString() . EmailNewLine . EmailNewLine;
		}
		return $text . ("--" . $this->Boundary . "--");
	}

	#[\Override]
    public function getadditionalcontenttypeheader() {

		return "; boundary=\"" . $this->Boundary . "\"";
	}

	public function __construct()
    {
        $this->Boundary = "--" . md5(uniqid("mime_container"));
    }

	public function clear() {

		$this->Blocks = [];
	}

	public function add($mimeBlock = null) {

		if (!$mimeBlock || !($mimeBlock->IsValid()))
		{
			return false;
		}
		$this->Blocks[] = $mimeBlock;
		return true;
	}

};


?>
