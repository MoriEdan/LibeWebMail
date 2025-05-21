<?php


define("HAR_AUTO_NAME", 1);


class imageresizer {


	public $imgFile = "";
	public $imgWidth = 0;
	public $imgHeight = 0;
	public $imgType = "";
	public $imgAttr = "";
	public $type;
	public $_img;
	public $_error = "";

	public function __construct($imgFile = "") {

		if (!(function_exists("imagecreate")))
		{
			$this->_error = "Error: GD Library is not available.";
			return;
		}
		$this->type = [1 => "GIF", 2 => "JPG", 3 => "PNG", 4 => "SWF", 5 => "PSD", 6 => "BMP", 7 => "TIFF", 8 => "TIFF", 9 => "JPC", 10 => "JP2", 11 => "JPX", 12 => "JB2", 13 => "SWC", 14 => "IFF", 15 => "WBMP", 16 => "XBM"];
		if (!(empty($imgFile)))
		{
			$this->setImage($imgFile);
		}
	}

	public function error() {

		return $this->_error;
	}

	public function setimage($imgFile) {

		$this->imgFile = $imgFile;
		return $this->_createImage();
	}

	public function close() {

		return imagedestroy($this->_img);
	}

	public function resize_limitwh($imgwidth, $imgheight, $newfile = NULL) {

		[$width, $height, $type, $attr] = getimagesize($this->imgFile);
		if ($width > $imgwidth)
		{
			$image_per = floor($imgwidth * 100 / $width);
		}
		if (floor($height * $image_per / 100) > $imgheight)
		{
			$image_per = floor($imgheight * 100 / $height);
		}
		$this->resize_percentage($image_per, $newfile);
	}

	public function resize_percentage($percent = 100, $newfile = NULL) {

		$newWidth = $this->imgWidth * $percent / 100;
		$newHeight = $this->imgHeight * $percent / 100;
		return $this->resize($newWidth, $newHeight, $newfile);
	}

	public function resize_xypercentage($xpercent = 100, $ypercent = 100, $newfile = NULL) {

		$newWidth = $this->imgWidth * $xpercent / 100;
		$newHeight = $this->imgHeight * $ypercent / 100;
		return $this->resize($newWidth, $newHeight, $newfile);
	}

	public function resize($width, $height, $newfile = NULL) {

		if (empty($this->imgFile))
		{
			$this->_error = "File name is not initialised.";
			return false;
		}
		if ($this->imgWidth <= 0 || $this->imgHeight <= 0)
		{
			$this->_error = "Could not resize given image";
			return false;
		}
		if ($width <= 0)
		{
			$width = $this->imgWidth;
		}
		if ($height <= 0)
		{
			$height = $this->imgHeight;
		}
		return $this->_resize($width, $height, $newfile);
	}

	public function _getimageinfo() {

		[$this->imgWidth, $this->imgHeight, $type, $this->imgAttr] = getimagesize($this->imgFile);
		$this->imgType = $this->type[$type];
	}

	public function _createimage() {

		$this->_getImageInfo();
		if ($this->imgType == "GIF") {
            $this->_img = imagecreatefromgif($this->imgFile);
        } elseif ($this->imgType == "JPG") {
            $this->_img = imagecreatefromjpeg($this->imgFile);
        } elseif ($this->imgType == "PNG") {
            $this->_img = imagecreatefrompng($this->imgFile);
        }
		if (!($this->_img) || !(is_resource($this->_img)))
		{
			$this->_error = "Error loading " . $this->imgFile;
			return false;
		}
		return true;
	}

	public function _resize($width, $height, $newfile = NULL) {

		if (!(function_exists("imagecreate")))
		{
			$this->_error = "Error: GD Library is not available.";
			return false;
		}
		$newimg = imagecreatetruecolor($width, $height);
		imagecopyresampled($newimg, $this->_img, 0, 0, 0, 0, $width, $height, $this->imgWidth, $this->imgHeight);
		if ($newfile === HAR_AUTO_NAME) {
            if (preg_match('/\..*+$/', basename((string) $this->imgFile), $matches))
			{
				$newfile = substr_replace($this->imgFile, "_har", -strlen($matches[0]), 0);
			}
        } elseif (!(empty($newfile))) {
            if (!preg_match('/\..*+$/', basename((string) $newfile)) && preg_match('/\..*+$/', basename((string) $this->imgFile), $matches))
				{
					$newfile .= $matches[0];
				}
        }
		if ($this->imgType == "GIF") {
            if (!(empty($newfile)))
			{
				imagegif($newimg, $newfile);
			}
			 else 
			{
				header("Content-type: image/gif");
				imagegif($newimg);
			}
        } elseif ($this->imgType == "JPG") {
            if (!(empty($newfile)))
				{
					imagejpeg($newimg, $newfile);
				}
				 else 
				{
					header("Content-type: image/jpeg");
					imagejpeg($newimg);
				}
        } elseif ($this->imgType == "PNG") {
            if (!(empty($newfile)))
					{
						imagepng($newimg, $newfile);
					}
					 else 
					{
						header("Content-type: image/png");
						imagepng($newimg);
					}
        }
		imagedestroy($newimg);
		return null;
	}

};


?>
