<?php

class paging {


	public function page($total, $perpagesize, $styleclass = "", $prefix = 0, $prefixformat = 0, $goto = 0, $name = "", $pagenumber = "", $url = "", $postarray = []) {

		require __DIR__ . "/script.inc.php";
		require $config_path . "system.config.php";
		$out = "";
		$hiddenvar = "";
		foreach ($postarray as $key => $value)
		{
			if (!($key != "pagenumber" && $key != "txtpage"))
			{
				continue;
			}
			$hiddenvar .= "<input name=\"" . $key . "\" type=\"hidden\" id=\"" . $key . "\" value=\"" . $value . "\">";
		}
		if ($total > $perpagesize)
		{
			if ($url == "")
			{
				$var = $_SERVER["SCRIPT_NAME"];
				$var = substr((string) $var, 0, strrpos((string) $var, "/"));
				$url = "http://" . $_SERVER["HTTP_HOST"] . "" . $var . "/";
				if (!$mod_rewrite)
				{
					$url .= "index.php?page=";
				}
				$page = $_GET["page"];
				$url .= $page;
			}
			if ($pagenumber == "") {
                $pagenumber = isset($_POST["pagenumber"]) && trim((string) $_POST["pagenumber"]) !== "" ? $_POST["pagenumber"] : 1;
            }
			$lastpage = ceil($total / $perpagesize);
			if ($styleclass != "")
			{
				$styleclass = "class=\"" . $styleclass . "\"";
			}
			$out = '' . "<script language=\"javascript\" type=\"text/javascript\">\r
			function nesotePaging" . $name . "(pageno)\r
			{\r
				document.nesotepage" . $name . ".pagenumber.value=pageno;\r
			//alert();\r
			pagingform=document.getElementById('nesotepage" . $name . "');\r
				pagingform.submit();\r
			}\r
			function goToPage" . $name . "(lastpage)\r
			{\r
				pno=document.nesotepage" . $name . ".txtpage.value;\r
				if(!pno.match(/^\\d+\$/))\r
				{\r
					alert('Not valid !');\r
					document.nesotepage" . $name . ".txtpage.value=\"\";\r
					return ;\r
				}\r
				if(pno>lastpage)\r
				{\r
					alert('Last page is '+lastpage+' !');\r
					document.nesotepage" . $name . ".txtpage.value=\"\";\r
					return ;\r
				}\r
				if(pno<1)\r
				{\r
					alert('Not valid !');\r
					document.nesotepage" . $name . ".txtpage.value=\"\";\r
					return ;\r
				}\r
				nesotePaging" . $name . "(pno);\r
			}\r
			</script>\r
			\r
			<form name=\"nesotepage" . $name . "\" id=\"nesotepage" . $name . "\" action=\"" . $url . "\" method=\"post\" onsubmit=\"return false;\">\r
			" . $hiddenvar . ('' . "\r
			\r
			<input type=\"hidden\" value=\"\" name=\"pagenumber\" id=\"pagenumber\">\r
			<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" " . $styleclass . ">\r
			<tr >\r
			");
			if ($prefix == 1)
			{
				if ($prefixformat == 0)
				{
					$start = $pagenumber - 1 * $perpagesize + 1;
					$end = $total;
					if ($total > $pagenumber * $perpagesize)
					{
						$end = $pagenumber * $perpagesize;
					}
					$out .= '<td  style="padding:1px;align=center;" >' . $start . " - " . $end . " of " . $total . " </td>";
				}
				 else 
				{
					$out .= '<td  style="padding:1px;align=center;" >Page ' . $pagenumber . " of " . $lastpage . " </td>";
				}
			}
			if ($pagenumber > 1)
			{
				$out .= '' . "<td align=\"center\" style=\"width:30px;align=center;padding:1px\">\r
				<a href=\"javascript:nesotePaging" . $name . "(" . $pagenumber - 1 . ")\"  >" . "&lt;&lt;" . "</a>\r
				</td>";
			}
			$count = 0;
			$i = $pagenumber - 4;
			while ($i <= $lastpage && $count < 8)
			{
				if ($pagenumber == $i) {
                    $out .= "<td align=\"center\" style=\"width:30px\" >" . $i . "</td >";
                } elseif ($i > 0) {
                    $out .= '<td align="center" style="width:30px" ><a href="javascript:nesotePaging' . $name . "(" . $i . ")\"   >" . $i . "</a></td>";
                    $count += 1;
                }
				$i++;
			}
			if ($lastpage > $pagenumber)
			{
				$out .= '<td align="center" style="width:30px;"><a href="javascript:nesotePaging' . $name . "(" . $pagenumber + 1 . ")\" >" . "&gt;&gt;" . "</a></td> ";
			}
			if ($goto == 1)
			{
				$out .= '' . "<td align=\"right\" style=\"padding:1px;\">\r
				<input type=\"hidden\" value=\"" . $lastpage . "\" name=\"lastpage\" id=\"lastpage\">\r
				<input type=\"text\" id=\"txtpage\" size=\"1\" style=\"height:20px;\"  name=\"txtpage\"> </td>\r
				<td align=\"left\" style=\"padding:1px;\">\r
				<a href=\"javascript:goToPage" . $name . "(" . $lastpage . ")\" >Go&nbsp;to&nbsp;page</a></td>";
			}
			$out .= "</tr>\r
			</table>\r
			</form>\r
			";
		}
		return $out;
	}

	public function seopage($total, $perpagesize, $pagenumber, $url, $styleclass = "", $prefix = 0, $prefixformat = 0, $goto = 0, $name = "") {

		require __DIR__ . "/script.inc.php";
		require $config_path . "system.config.php";
		$out = "";
		if ($total > $perpagesize)
		{
			$lastpage = ceil($total / $perpagesize);
			if ($styleclass != "")
			{
				$styleclass = "class=\"" . $styleclass . "\"";
			}
			$out = '' . "<script language=\"javascript\" type=\"text/javascript\">\r
\r
			function goToPage" . $name . "(lastpage)\r
			{\r
				pno=document.getElementById('txtpage" . $name . ('' . "').value;\r
				if(!pno.match(/^\\d+\$/))\r
				{\r
					alert('Not valid !');\r
					document.getElementById('txtpage") . $name . "').value=\"\";\r
					return ;\r
				}\r
				if(pno>lastpage)\r
				{\r
					alert('Last page is '+lastpage+' !');\r
					document.getElementById('txtpage" . $name . "').value=\"\";\r
					return ;\r
				}\r
				if(pno<1)\r
				{\r
					alert('Not valid !');\r
					document.getElementById('txtpage" . $name . ('' . "').value=\"\";\r
					return ;\r
				}\r
				window.location.href=\"" . $url . "/\"+pno+\"\";\r
				\r
			}\r
			</script>\r
			\r
			<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" " . $styleclass . ">\r
			<tr >\r
			");
			if ($prefix == 1)
			{
				if ($prefixformat == 0)
				{
					$start = $pagenumber - 1 * $perpagesize + 1;
					$end = $total;
					if ($total > $pagenumber * $perpagesize)
					{
						$end = $pagenumber * $perpagesize;
					}
					$out .= '<td  style="padding:1px;align=center;" >' . $start . " - " . $end . " of " . $total . " </td>";
				}
				 else 
				{
					$out .= '<td  style="padding:1px;align=center;" >Page ' . $pagenumber . " of " . $lastpage . " </td>";
				}
			}
			if ($pagenumber > 1)
			{
				$out .= '' . "<td align=\"center\" style=\"width:30px;align=center;padding:1px\">\r
				<a href=\"" . $url . "/" . $pagenumber - 1 . "\"  >" . "&lt;&lt;" . "</a>\r
				</td>";
			}
			$count = 0;
			$i = $pagenumber - 4;
			while ($i <= $lastpage && $count < 8)
			{
				if ($pagenumber == $i) {
                    $out .= "<td align=\"center\" style=\"width:30px\" >" . $i . "</td >";
                } elseif ($i > 0) {
                    $out .= '<td align="center" style="width:30px" ><a href="' . $url . "/" . $i . "\"   >" . $i . "</a></td>";
                    $count += 1;
                }
				$i++;
			}
			if ($lastpage > $pagenumber)
			{
				$out .= '<td align="center" style="width:30px;"><a href="' . $url . "/" . $pagenumber + 1 . "\" >" . "&gt;&gt;" . "</a></td> ";
			}
			if ($goto == 1)
			{
				$out .= '' . "<td align=\"right\" style=\"padding:1px;\">\r
				<input type=\"text\" id=\"txtpage" . $name . "\" size=\"1\" style=\"height:20px;\"  name=\"txtpage\"> </td>\r
				<td align=\"left\" style=\"padding:1px;\">\r
				<a href=\"javascript:goToPage" . $name . "(" . $lastpage . ")\" >Go&nbsp;to&nbsp;page</a></td>";
			}
			$out .= "</tr>\r
			</table>\r
			";
		}
		return $out;
	}

};


?>
