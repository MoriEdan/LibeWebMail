<?php



class nesoteform {


	public $form_validation = "";
	public $func_set = "";
	public $func_isPositive = 0;
	public $func_isEmail = 0;
	public $func_isNotNull = 0;
	public $func_isSame = 0;
	public $func_isNotShort = 0;
	public $func_isNotOver = 0;
	public $func_isOverMin = 0;

	public function __construct(public $formname, public $formaction, public $formtype = "POST")
    {
    }

	public function ispositive($fieldname, $error_message = "Invalid Number") {

		if ($this->func_isPositive == 0)
		{
			$this->func_set .= "\r
					function isPositive(strString)\r
				   //  check for valid numeric strings	\r
				   {\r
				   var strValidChars = \"0123456789.\";\r
				   var strChar;\r
				   var retResult = true;\r
				\r
				   if (strString.length == 0) return false;\r
				\r
				   //  test strString consists of valid characters listed above\r
				   for (i = 0; i < strString.length && retResult == true; i++)\r
					  {\r
					  strChar = strString.charAt(i);\r
					  if (strValidChars.indexOf(strChar) == -1)\r
						 {\r
						 retResult = false;\r
						 }\r
					  }\r
				   return retResult;\r
				   }\r
				";
			$this->func_isPositive = 1;
		}
		$this->form_validation .= '' . "\r
					if(isPositive(document." . $this->formname . "." . $fieldname . ".value)==false) {	\r
					alert(\"" . $error_message . "\");\r
					document." . $this->formname . "." . $fieldname . ".focus();\r
					return false;\r
					}\r
					";
	}

	public function isemail($fieldname, $error_message = "Invalid Email Address") {

		if ($this->func_isEmail == 0)
		{
			$this->func_set .= "\r
						function isEmail(emailString)\r
					   //  check for valid Email Addresses	\r
					   {\r
					   \r
					   	 if (emailString.length == 0) return true;\r
					   \r
						 var filter=/^([\\w-]+(?:\\.[\\w-]+)*)@((?:[\\w-]+\\.)*\\w[\\w-]{0,66})\\.([a-z]{2,6}(?:\\.[a-z]{2})?)\$/i\r
						 \r
							if (filter.test(emailString))\r
								return true;\r
							else\r
								return false;					\r
						\r
						}\r
					";
			$this->func_isEmail = 1;
		}
		$this->form_validation .= '' . "\r
						if(isEmail(document." . $this->formname . "." . $fieldname . ".value)==false) {	\r
						alert(\"" . $error_message . "\");\r
						document." . $this->formname . "." . $fieldname . ".focus();\r
						return false;\r
						}\r
						";
	}

	public function isnotnull($fieldname, $error_message = "Value cannot be NULL") {

		if ($this->func_isNotNull == 0)
		{
			$this->func_set .= "\r
					 \r
					function trim(stringValue){return stringValue.replace(/(^\\s*|\\s*\$)/, \"\");}\r
					\r
					function isNotNull(strString)\r
				   //  checks wheteher value of field not null 	\r
				   {\r
				\r
				   \r
					if (trim(strString).length == 0) return false;\r
				   \r
					\r
					\r
					}\r
				";
			$this->func_isNotNull = 1;
		}
		$this->form_validation .= '' . "\r
					if(isNotNull(document." . $this->formname . "." . $fieldname . ".value)==false) {	\r
					alert(\"" . $error_message . "\");\r
					document." . $this->formname . "." . $fieldname . ".focus();\r
					return false;\r
					}\r
					";
	}

	public function issame($fieldname1, $fieldname2, $error_message = "Values are not same") {

		if ($this->func_isSame == 0)
		{
			$this->func_set .= "\r
					 \r
					function isSame(strString1, strString2)\r
					//checks wheteher both field values are same\r
					{   \r
						if (strString1 != strString2 ) return false;\r
					}\r
				";
			$this->func_isSame = 1;
		}
		$this->form_validation .= '' . "\r
					if(isSame(document." . $this->formname . "." . $fieldname1 . ".value,document." . $this->formname . "." . $fieldname2 . ".value)==false) {	\r
					alert(\"" . $error_message . "\");\r
					document." . $this->formname . "." . $fieldname1 . ".focus();\r
					return false;\r
					}\r
					";
	}

	public function isnotshort($fieldname, $fieldlength, $error_message = "Length of the text entered is small") {

		if ($this->func_isNotShort == 0)
		{
			$this->func_set .= "\r
				 \r
				function isNotShort(strString, strLength)\r
				//checks wheteher value has minimum length\r
				{   \r
					if (strString.length < strLength ) return false;\r
				}\r
			";
			$this->func_isNotShort = 1;
		}
		$this->form_validation .= '' . "\r
				if(isNotShort(document." . $this->formname . "." . $fieldname . ".value, " . $fieldlength . ")==false) {	\r
				alert(\"" . $error_message . "\");\r
				document." . $this->formname . "." . $fieldname . ".focus();\r
				return false;\r
				}\r
				";
	}

	public function isovermin($fieldname, $fieldminvalue, $error_message = "Value entered is less than minimum value") {

		if ($this->func_isOverMin == 0)
		{
			$this->func_set .= "\r
					 \r
					function isOverMin(strString, minvalue)\r
					//checks wheteher value has minimum length\r
					{   \r
						if (strString < minvalue ) return false;\r
					}\r
				";
			$this->func_isOverMin = 1;
		}
		$this->form_validation .= '' . "\r
					if(isOverMin(document." . $this->formname . "." . $fieldname . ".value, " . $fieldminvalue . ")==false) {	\r
					alert(\"" . $error_message . "\");\r
					document." . $this->formname . "." . $fieldname . ".focus();\r
					return false;\r
					}\r
					";
	}

	public function formstart() {

		$jscript = "";
		$enc = "";
		if (strtolower((string) $this->formtype) !== "get")
		{
			$enc = "enctype=\"multipart/form-data\"";
		}
		$returnstring = "<form name=\"" . $this->formname . "\" id=\"" . $this->formname . "\" " . $enc . " method=\"" . $this->formtype . "\"  action=\"" . $this->formaction . "\"";
		if ($this->form_validation != "")
		{
			$jscript = "<script type=\"text/javascript\">" . $this->func_set . "function verifyForm_" . $this->formname . "(){" . $this->form_validation . "}</script>";
			$returnstring = $returnstring . " onSubmit=\"return verifyForm_" . $this->formname . "()\"";
		}
		return $jscript . $returnstring . ">";
	}

	public function addtextbox($name, $display_value = "", $size = 25, $max = "255") {

		return "<input name=\"" . $name . "\" type=\"text\" id=\"" . $name . "\" size=\"" . $size . "\" value=\"" . $display_value . "\" maxlength=\"" . $max . "\">";
	}

	public function addtextarea($name, $display_value = "", $size = 25, $lines = 5, $max = "2500") {

		return "<textarea name=\"" . $name . "\"  id=\"" . $name . "\" cols=\"" . $size . "\" rows=\"" . $lines . "\" maxlength=\"" . $max . "\">" . $display_value . "</textarea>";
	}

	public function addhiddenfield($name, $value) {

		return "<input name=\"" . $name . "\" type=\"hidden\" id=\"" . $name . "\" value=\"" . $value . "\">";
	}

	public function addpassword($name, $size = 25) {

		return "<input name=\"" . $name . "\" type=\"password\" id=\"" . $name . "\" size=\"" . $size . "\" value=\"\">";
	}

	public function addsubmit($display_value, $name = "submit") {

		return "<input type=\"submit\" name=\"" . $name . "\" value=\"" . $display_value . "\">";
	}

	public function formclose() {

		return "</form>";
	}

};


?>
