<?php
/**
 * The Mail_Mime class is used to create MIME E-mail messages
 *
 * The Mail_Mime class provides an OO interface to create MIME
 * enabled email messages. This way you can create emails that
 * contain plain-text bodies, HTML bodies, attachments, inline
 * images and specific headers.
 *
 * Compatible with PHP versions 4 and 5
 *
 * LICENSE: This LICENSE is in the BSD license style.
 * Copyright (c) 2002-2003, Richard Heyes <richard@phpguru.org>
 * Copyright (c) 2003-2006, PEAR <pear-group@php.net>
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or
 * without modification, are permitted provided that the following
 * conditions are met:
 *
 * - Redistributions of source code must retain the above copyright
 *   notice, this list of conditions and the following disclaimer.
 * - Redistributions in binary form must reproduce the above copyright
 *   notice, this list of conditions and the following disclaimer in the
 *   documentation and/or other materials provided with the distribution.
 * - Neither the name of the authors, nor the names of its contributors 
 *   may be used to endorse or promote products derived from this 
 *   software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
 * ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE
 * LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
 * CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF
 * THE POSSIBILITY OF SUCH DAMAGE.
 *
 * @category  Mail
 * @package   Mail_Mime
 * @author    Richard Heyes  <richard@phpguru.org>
 * @author    Tomas V.V. Cox <cox@idecnet.com>
 * @author    Cipriano Groenendal <cipri@php.net>
 * @author    Sean Coates <sean@php.net>
 * @author    Aleksander Machniak <alec@php.net>
 * @copyright 2003-2006 PEAR <pear-group@php.net>
 * @license   http://www.opensource.org/licenses/bsd-license.php BSD License
 * @version   CVS: $Id: mime.php 297878 2010-04-12 10:57:34Z alec $
 * @link      http://pear.php.net/package/Mail_mime
 *
 *            This class is based on HTML Mime Mail class from
 *            Richard Heyes <richard@phpguru.org> which was based also
 *            in the mime_mail.class by Tobias Ratschiller <tobias@dnet.it>
 *            and Sascha Schumann <sascha@schumann.cx>
 */


/**
 * require PEAR
 *
 * This package depends on PEAR to raise errors.
 */
require_once __DIR__ . '/PEAR.php';

/**
 * require Mail_mimePart
 *
 * Mail_mimePart contains the code required to
 * create all the different parts a mail can
 * consist of.
 */
require_once __DIR__ . '/Mail/mimePart.php';


/**
 * The Mail_Mime class provides an OO interface to create MIME
 * enabled email messages. This way you can create emails that
 * contain plain-text bodies, HTML bodies, attachments, inline
 * images and specific headers.
 *
 * @category  Mail
 * @package   Mail_Mime
 * @author    Richard Heyes  <richard@phpguru.org>
 * @author    Tomas V.V. Cox <cox@idecnet.com>
 * @author    Cipriano Groenendal <cipri@php.net>
 * @author    Sean Coates <sean@php.net>
 * @copyright 2003-2006 PEAR <pear-group@php.net>
 * @license   http://www.opensource.org/licenses/bsd-license.php BSD License
 * @version   Release: @package_version@
 * @link      http://pear.php.net/package/Mail_mime
 */
class Mail_mime
{
    /**
     * Contains the plain text part of the email
     *
     * @var string
     * @access private
     */
    public $_txtbody;

    /**
     * Contains the html part of the email
     *
     * @var string
     * @access private
     */
    public $_htmlbody;

    /**
     * list of the attached images
     *
     * @var array
     * @access private
     */
    public $_html_images = [];

    /**
     * list of the attachements
     *
     * @var array
     * @access private
     */
    public $_parts = [];

    /**
     * Headers for the mail
     *
     * @var array
     * @access private
     */
    public $_headers = [];

    /**
     * Build parameters
     *
     * @var array
     * @access private
     */
    public $_build_params = [
        // What encoding to use for the headers
        // Options: quoted-printable or base64
        'head_encoding' => 'quoted-printable',
        // What encoding to use for plain text
        // Options: 7bit, 8bit, base64, or quoted-printable
        'text_encoding' => 'quoted-printable',
        // What encoding to use for html
        // Options: 7bit, 8bit, base64, or quoted-printable
        'html_encoding' => 'quoted-printable',
        // The character set to use for html
        'html_charset'  => 'ISO-8859-1',
        // The character set to use for text
        'text_charset'  => 'ISO-8859-1',
        // The character set to use for headers
        'head_charset'  => 'ISO-8859-1',
        // End-of-line sequence
        'eol'           => "\r\n",
        // Delay attachment files IO until building the message
        'delay_file_io' => false
    ];

    /**
     * Constructor function
     *
     * @param mixed $params Build parameters that change the way the email
     *                      is built. Should be an associative array.
     *                      See $_build_params.
     *
     * @return void
     * @access public
     */
    function __construct($params = [])
    {
        // Backward-compatible EOL setting
        if (is_string($params)) {
            $this->_build_params['eol'] = $params;
        } elseif (defined('MAIL_MIME_CRLF') && !isset($params['eol'])) {
            $this->_build_params['eol'] = MAIL_MIME_CRLF;
        }

        // Update build parameters
        if (!empty($params) && is_array($params)) {
            foreach ($params as $key => $value) {
                $this->_build_params[$key] = $value;
            }
        }
    }

    /**
     * Set build parameter value
     *
     * @param string $name  Parameter name
     * @param string $value Parameter value
     *
     * @return void
     * @access public
     * @since 1.6.0
     */
    function setParam($name, $value)
    {
        $this->_build_params[$name] = $value;
    }

    /**
     * Get build parameter value
     *
     * @param string $name Parameter name
     *
     * @return mixed Parameter value
     * @access public
     * @since 1.6.0
     */
    function getParam($name)
    {
        return $this->_build_params[$name] ?? null;
    }

    /**
     * Accessor function to set the body text. Body text is used if
     * it's not an html mail being sent or else is used to fill the
     * text/plain part that emails clients who don't support
     * html should show.
     *
     * @param string $data   Either a string or
     *                       the file name with the contents
     * @param bool   $isfile If true the first param should be treated
     *                       as a file name, else as a string (default)
     * @param bool   $append If true the text or file is appended to
     *                       the existing body, else the old body is
     *                       overwritten
     *
     * @return mixed         True on success or PEAR_Error object
     * @access public
     */
    function setTXTBody($data, $isfile = false, $append = false)
    {
        if (!$isfile) {
            if (!$append) {
                $this->_txtbody = $data;
            } else {
                $this->_txtbody .= $data;
            }
        } else {
            $cont = $this->_file2str($data);
            if ((new PEAR())->isError($cont)) {
                return $cont;
            }
            if (!$append) {
                $this->_txtbody = $cont;
            } else {
                $this->_txtbody .= $cont;
            }
        }
        return true;
    }

    /**
     * Get message text body
     *
     * @return string Text body
     * @access public
     * @since 1.6.0
     */
    function getTXTBody()
    {
        return $this->_txtbody;
    }

    /**
     * Adds a html part to the mail.
     *
     * @param string $data   Either a string or the file name with the
     *                       contents
     * @param bool   $isfile A flag that determines whether $data is a
     *                       filename, or a string(false, default)
     *
     * @return bool          True on success
     * @access public
     */
    function setHTMLBody($data, $isfile = false)
    {
        if (!$isfile) {
            $this->_htmlbody = $data;
        } else {
            $cont = $this->_file2str($data);
            if ((new PEAR())->isError($cont)) {
                return $cont;
            }
            $this->_htmlbody = $cont;
        }

        return 1;
    }

    /**
     * Get message HTML body
     *
     * @return string HTML body
     * @access public
     * @since 1.6.0
     */
    function getHTMLBody()
    {
        return $this->_htmlbody;
    }

    /**
     * Adds an image to the list of embedded images.
     *
     * @param string $file       The image file name OR image data itself
     * @param string $c_type     The content type
     * @param string $name       The filename of the image.
     *                           Only used if $file is the image data.
     * @param bool   $isfile     Whether $file is a filename or not.
     *                           Defaults to true
     * @param string $content_id Desired Content-ID of MIME part
     *                           Defaults to generated unique ID
     *
     * @return bool          True on success
     * @access public
     */
    function addHTMLImage($file,
        $c_type='application/octet-stream',
        $name = '',
        $isfile = true,
        $content_id = null
    ) {
        $bodyfile = null;

        if ($isfile) {
            // Don't load file into memory
            if ($this->_build_params['delay_file_io']) {
                $filedata = null;
                $bodyfile = $file;
            } elseif ((new PEAR())->isError($filedata = $this->_file2str($file))) {
                return $filedata;
            }
            $filename = ($name ?: $file);
        } else {
            $filedata = $file;
            $filename = $name;
        }

        if (!$content_id) {
            $content_id = md5(uniqid(time()));
        }

        $this->_html_images[] = [
            'body'      => $filedata,
            'body_file' => $bodyfile,
            'name'      => $filename,
            'c_type'    => $c_type,
            'cid'       => $content_id
        ];

        return true;
    }

    /**
     * Adds a file to the list of attachments.
     *
     * @param string $file        The file name of the file to attach
     *                            OR the file contents itself
     * @param string $c_type      The content type
     * @param string $name        The filename of the attachment
     *                            Only use if $file is the contents
     * @param bool   $isfile      Whether $file is a filename or not
     *                            Defaults to true
     * @param string $encoding    The type of encoding to use.
     *                            Defaults to base64.
     *                            Possible values: 7bit, 8bit, base64, 
     *                            or quoted-printable.
     * @param string $disposition The content-disposition of this file
     *                            Defaults to attachment.
     *                            Possible values: attachment, inline.
     * @param string $charset     The character set used in the filename
     *                            of this attachment.
     * @param string $language    The language of the attachment
     * @param string $location    The RFC 2557.4 location of the attachment
     * @param string $n_encoding  Encoding for attachment name (Content-Type)
     *                            By default filenames are encoded using RFC2231 method
     *                            Here you can set RFC2047 encoding (quoted-printable
     *                            or base64) instead
     * @param string $f_encoding  Encoding for attachment filename (Content-Disposition)
     *                            See $n_encoding description
     * @param string $description Content-Description header
     *
     * @return mixed              True on success or PEAR_Error object
     * @access public
     */
    function addAttachment($file,
        $c_type      = 'application/octet-stream',
        $name        = '',
        $isfile      = true,
        $encoding    = 'base64',
        $disposition = 'attachment',
        $charset     = '',
        $language    = '',
        $location    = '',
        $n_encoding  = null,
        $f_encoding  = null,
        $description = ''
    ) {

        $bodyfile = null;

        if ($isfile) {
            // Don't load file into memory
            if ($this->_build_params['delay_file_io']) {
                $filedata = null;
                $bodyfile = $file;
            } elseif ((new PEAR())->isError($filedata = $this->_file2str($file))) {
                return $filedata;
            }
            // Force the name the user supplied, otherwise use $file
            $filename = ($name ?: $file);
        } else {
            $filedata = $file;
            $filename = $name;
        }

        if ((string) $filename === '') {
            $msg = "The supplied filename for the attachment can't be empty";
            return (new PEAR())->raiseError($msg);
        }
        $filename = $this->_basename($filename);

        $this->_parts[] = [
            'body'        => $filedata,
            'body_file'   => $bodyfile,
            'name'        => $filename,
            'c_type'      => $c_type,
            'encoding'    => $encoding,
            'charset'     => $charset,
            'language'    => $language,
            'location'    => $location,
            'disposition' => $disposition,
            'description' => $description,
            'name_encoding'     => $n_encoding,
            'filename_encoding' => $f_encoding
        ];

        return true;
    }

    /**
     * Get the contents of the given file name as string
     *
     * @param string $file_name Path of file to process
     *
     * @return string           Contents of $file_name
     * @access private
     */
    function &_file2str($file_name)
    {
        // Check state of file and raise an error properly
        if (!file_exists($file_name)) {
            $err = (new PEAR())->raiseError('File not found: ' . $file_name);
            return $err;
        }
        if (!is_file($file_name)) {
            $err = (new PEAR())->raiseError('Not a regular file: ' . $file_name);
            return $err;
        }
        if (!is_readable($file_name)) {
            $err = (new PEAR())->raiseError('File is not readable: ' . $file_name);
            return $err;
        }

        // Temporarily reset magic_quotes_runtime and read file contents
        if ($magic_quote_setting = get_magic_quotes_runtime()) {
            @ini_set('magic_quotes_runtime', 0);
        }
        $cont = file_get_contents($file_name);
        if ($magic_quote_setting) {
            @ini_set('magic_quotes_runtime', $magic_quote_setting);
        }

        return $cont;
    }

    /**
     * Adds a text subpart to the mimePart object and
     * returns it during the build process.
     *
     * @param mixed  &$obj The object to add the part to, or
     *                     null if a new object is to be created.
     * @param string $text The text to add.
     *
     * @return object      The text mimePart object
     * @access private
     */
    function &_addTextPart(&$obj, $text)
    {
        $params['content_type'] = 'text/plain';
        $params['encoding']     = $this->_build_params['text_encoding'];
        $params['charset']      = $this->_build_params['text_charset'];
        $params['eol']          = $this->_build_params['eol'];

        if (is_object($obj)) {
            $ret = $obj->addSubpart($text, $params);
            return $ret;
        } else {
            $ret = new Mail_mimePart($text, $params);
            return $ret;
        }
    }

    /**
     * Adds a html subpart to the mimePart object and
     * returns it during the build process.
     *
     * @param mixed &$obj The object to add the part to, or
     *                    null if a new object is to be created.
     *
     * @return object     The html mimePart object
     * @access private
     */
    function &_addHtmlPart(&$obj)
    {
        $params['content_type'] = 'text/html';
        $params['encoding']     = $this->_build_params['html_encoding'];
        $params['charset']      = $this->_build_params['html_charset'];
        $params['eol']          = $this->_build_params['eol'];

        if (is_object($obj)) {
            $ret = $obj->addSubpart($this->_htmlbody, $params);
            return $ret;
        } else {
            $ret = new Mail_mimePart($this->_htmlbody, $params);
            return $ret;
        }
    }

    /**
     * Creates a new mimePart object, using multipart/mixed as
     * the initial content-type and returns it during the
     * build process.
     *
     * @return object The multipart/mixed mimePart object
     * @access private
     */
    function &_addMixedPart()
    {
        $params                 = [];
        $params['content_type'] = 'multipart/mixed';
        $params['eol']          = $this->_build_params['eol'];

        // Create empty multipart/mixed Mail_mimePart object to return
        $ret = new Mail_mimePart('', $params);
        return $ret;
    }

    /**
     * Adds a multipart/alternative part to a mimePart
     * object (or creates one), and returns it during
     * the build process.
     *
     * @param mixed &$obj The object to add the part to, or
     *                    null if a new object is to be created.
     *
     * @return object     The multipart/mixed mimePart object
     * @access private
     */
    function &_addAlternativePart(&$obj)
    {
        $params['content_type'] = 'multipart/alternative';
        $params['eol']          = $this->_build_params['eol'];

        if (is_object($obj)) {
            return $obj->addSubpart('', $params);
        } else {
            $ret = new Mail_mimePart('', $params);
            return $ret;
        }
    }

    /**
     * Adds a multipart/related part to a mimePart
     * object (or creates one), and returns it during
     * the build process.
     *
     * @param mixed &$obj The object to add the part to, or
     *                    null if a new object is to be created
     *
     * @return object     The multipart/mixed mimePart object
     * @access private
     */
    function &_addRelatedPart(&$obj)
    {
        $params['content_type'] = 'multipart/related';
        $params['eol']          = $this->_build_params['eol'];

        if (is_object($obj)) {
            return $obj->addSubpart('', $params);
        } else {
            $ret = new Mail_mimePart('', $params);
            return $ret;
        }
    }

    /**
     * Adds an html image subpart to a mimePart object
     * and returns it during the build process.
     *
     * @param object &$obj  The mimePart to add the image to
     * @param array  $value The image information
     *
     * @return object       The image mimePart object
     * @access private
     */
    function &_addHtmlImagePart(&$obj, $value)
    {
        $params['content_type'] = $value['c_type'];
        $params['encoding']     = 'base64';
        $params['disposition']  = 'inline';
        $params['dfilename']    = $value['name'];
        $params['cid']          = $value['cid'];
        $params['body_file']    = $value['body_file'];
        $params['eol']          = $this->_build_params['eol'];

        if (!empty($value['name_encoding'])) {
            $params['name_encoding'] = $value['name_encoding'];
        }
        if (!empty($value['filename_encoding'])) {
            $params['filename_encoding'] = $value['filename_encoding'];
        }

        $ret = $obj->addSubpart($value['body'], $params);
        return $ret;
    }

    /**
     * Adds an attachment subpart to a mimePart object
     * and returns it during the build process.
     *
     * @param object &$obj  The mimePart to add the image to
     * @param array  $value The attachment information
     *
     * @return object       The image mimePart object
     * @access private
     */
    function &_addAttachmentPart(&$obj, $value)
    {
        $params['eol']          = $this->_build_params['eol'];
        $params['dfilename']    = $value['name'];
        $params['encoding']     = $value['encoding'];
        $params['content_type'] = $value['c_type'];
        $params['body_file']    = $value['body_file'];
        $params['disposition']  = $value['disposition'] ?? 'attachment';
        if ($value['charset']) {
            $params['charset'] = $value['charset'];
        }
        if ($value['language']) {
            $params['language'] = $value['language'];
        }
        if ($value['location']) {
            $params['location'] = $value['location'];
        }
        if (!empty($value['name_encoding'])) {
            $params['name_encoding'] = $value['name_encoding'];
        }
        if (!empty($value['filename_encoding'])) {
            $params['filename_encoding'] = $value['filename_encoding'];
        }
        if (!empty($value['description'])) {
            $params['description'] = $value['description'];
        }

        $ret = $obj->addSubpart($value['body'], $params);
        return $ret;
    }

    /**
     * Returns the complete e-mail, ready to send using an alternative
     * mail delivery method. Note that only the mailpart that is made
     * with Mail_Mime is created. This means that,
     * YOU WILL HAVE NO TO: HEADERS UNLESS YOU SET IT YOURSELF 
     * using the $headers parameter!
     * 
     * @param string $separation The separation between these two parts.
     * @param array  $params     The Build parameters passed to the
     *                           &get() function. See &get for more info.
     * @param array  $headers    The extra headers that should be passed
     *                           to the &headers() function.
     *                           See that function for more info.
     * @param bool   $overwrite  Overwrite the existing headers with new.
     *
     * @return mixed The complete e-mail or PEAR error object
     * @access public
     */
    function getMessage($separation = null, $params = null, $headers = null,
        $overwrite = false
    ) {
        if ($separation === null) {
            $separation = $this->_build_params['eol'];
        }

        $body = $this->get($params);

        if ((new PEAR())->isError($body)) {
            return $body;
        }

        $head = $this->txtHeaders($headers, $overwrite);
        return $head . $separation . $body;
    }

    /**
     * Returns the complete e-mail body, ready to send using an alternative
     * mail delivery method.
     * 
     * @param array $params The Build parameters passed to the
     *                      &get() function. See &get for more info.
     *
     * @return mixed The e-mail body or PEAR error object
     * @access public
     * @since 1.6.0
     */
    function getMessageBody($params = null)
    {
        return $this->get($params, null, true);
    }

    /**
     * Writes (appends) the complete e-mail into file.
     * 
     * @param string $filename  Output file location
     * @param array  $params    The Build parameters passed to the
     *                          &get() function. See &get for more info.
     * @param array  $headers   The extra headers that should be passed
     *                          to the &headers() function.
     *                          See that function for more info.
     * @param bool   $overwrite Overwrite the existing headers with new.
     *
     * @return mixed True or PEAR error object
     * @access public
     * @since 1.6.0
     */
    function saveMessage($filename, $params = null, $headers = null, $overwrite = false)
    {
        // Check state of file and raise an error properly
        if (file_exists($filename) && !is_writable($filename)) {
            return (new PEAR())->raiseError('File is not writable: ' . $filename);
        }

        // Temporarily reset magic_quotes_runtime and read file contents
        if ($magic_quote_setting = get_magic_quotes_runtime()) {
            @ini_set('magic_quotes_runtime', 0);
        }

        if (!($fh = fopen($filename, 'ab'))) {
            return (new PEAR())->raiseError('Unable to open file: ' . $filename);
        }

        // Write message headers into file (skipping Content-* headers)
        $head = $this->txtHeaders($headers, $overwrite, true);
        if (fwrite($fh, $head) === false) {
            return (new PEAR())->raiseError('Error writing to file: ' . $filename);
        }

        fclose($fh);

        if ($magic_quote_setting) {
            @ini_set('magic_quotes_runtime', $magic_quote_setting);
        }

        // Write the rest of the message into file
        $res = $this->get($params, $filename);

        return $res ?: true;
    }

    /**
     * Writes (appends) the complete e-mail body into file.
     * 
     * @param string $filename Output file location
     * @param array  $params   The Build parameters passed to the
     *                         &get() function. See &get for more info.
     *
     * @return mixed True or PEAR error object
     * @access public
     * @since 1.6.0
     */
    function saveMessageBody($filename, $params = null)
    {
        // Check state of file and raise an error properly
        if (file_exists($filename) && !is_writable($filename)) {
            return (new PEAR())->raiseError('File is not writable: ' . $filename);
        }

        // Temporarily reset magic_quotes_runtime and read file contents
        if ($magic_quote_setting = get_magic_quotes_runtime()) {
            @ini_set('magic_quotes_runtime', 0);
        }

        if (!($fh = fopen($filename, 'ab'))) {
            return (new PEAR())->raiseError('Unable to open file: ' . $filename);
        }

        // Write the rest of the message into file
        $res = $this->get($params, $filename, true);

        return $res ?: true;
    }

    /**
     * Builds the multipart message from the list ($this->_parts) and
     * returns the mime content.
     *
     * @param array    $params    Build parameters that change the way the email
     *                            is built. Should be associative. See $_build_params.
     * @param resource $filename  Output file where to save the message instead of
     *                            returning it
     * @param boolean  $skip_head True if you want to return/save only the message
     *                            without headers
     *
     * @return mixed The MIME message content string, null or PEAR error object
     * @access public
     */
    function &get($params = null, $filename = null, $skip_head = false)
    {
        if (isset($params)) {
            foreach ($params as $key => $value) {
                $this->_build_params[$key] = $value;
            }
        }

        if (isset($this->_headers['From'])) {
            // Bug #11381: Illegal characters in domain ID
            $domainID = preg_match("|(@[0-9a-zA-Z\-\.]+)|", $this->_headers['From'], $matches) ? $matches[1] : "@localhost";
            foreach ($this->_html_images as $i => $img) {
                $this->_html_images[$i]['cid'] .= $domainID;
            }
        }

        if (count($this->_html_images) && $this->_htmlbody !== null) {
            foreach ($this->_html_images as $key => $value) {
                $regex   = [];
                $regex[] = '#(\s)((?i)src|background|href(?-i))\s*=\s*(["\']?)' .
                            preg_quote((string) $value['name'], '#') . '\3#';
                $regex[] = '#(?i)url(?-i)\(\s*(["\']?)' .
                            preg_quote((string) $value['name'], '#') . '\1\s*\)#';

                $rep   = [];
                $rep[] = '\1\2=\3cid:' . $value['cid'] .'\3';
                $rep[] = 'url(\1cid:' . $value['cid'] . '\1)';

                $this->_htmlbody = preg_replace($regex, $rep, (string) $this->_htmlbody);
                $this->_html_images[$key]['name']
                    = $this->_basename($this->_html_images[$key]['name']);
            }
        }

        $this->_checkParams();

        $null        = null;
        $attachments = (bool) count($this->_parts);
        $html_images = (bool) count($this->_html_images);
        $html        = (bool) strlen($this->_htmlbody);
        $text        = !$html && strlen($this->_txtbody);

        switch (true) {
        case $text && !$attachments:
            $message =& $this->_addTextPart($null, $this->_txtbody);
            break;

        case !$text && !$html && $attachments:
            $message =& $this->_addMixedPart();
            $counter = count($this->_parts);
            for ($i = 0; $i < $counter; $i++) {
                $this->_addAttachmentPart($message, $this->_parts[$i]);
            }
            break;

        case $text && $attachments:
            $message =& $this->_addMixedPart();
            $this->_addTextPart($message, $this->_txtbody);
            $counter = count($this->_parts);
            for ($i = 0; $i < $counter; $i++) {
                $this->_addAttachmentPart($message, $this->_parts[$i]);
            }
            break;

        case $html && !$attachments && !$html_images:
            if ($this->_txtbody !== null) {
                $message =& $this->_addAlternativePart($null);
                $this->_addTextPart($message, $this->_txtbody);
                $this->_addHtmlPart($message);
            } else {
                $message =& $this->_addHtmlPart($null);
            }
            break;

        case $html && !$attachments && $html_images:
            // * Content-Type: multipart/alternative;
            //    * text
            //    * Content-Type: multipart/related;
            //       * html
            //       * image...
            if ($this->_txtbody !== null) {
                $message =& $this->_addAlternativePart($null);
                $this->_addTextPart($message, $this->_txtbody);

                $ht =& $this->_addRelatedPart($message);
                $this->_addHtmlPart($ht);
                $counter = count($this->_html_images);
                for ($i = 0; $i < $counter; $i++) {
                    $this->_addHtmlImagePart($ht, $this->_html_images[$i]);
                }
            } else {
                // * Content-Type: multipart/related;
                //    * html
                //    * image...
                $message =& $this->_addRelatedPart($null);
                $this->_addHtmlPart($message);
                $counter = count($this->_html_images);
                for ($i = 0; $i < $counter; $i++) {
                    $this->_addHtmlImagePart($message, $this->_html_images[$i]);
                }
            }
            /*
            // #13444, #9725: the code below was a non-RFC compliant hack
            // * Content-Type: multipart/related;
            //    * Content-Type: multipart/alternative;
            //        * text
            //        * html
            //    * image...
            $message =& $this->_addRelatedPart($null);
            if (isset($this->_txtbody)) {
                $alt =& $this->_addAlternativePart($message);
                $this->_addTextPart($alt, $this->_txtbody);
                $this->_addHtmlPart($alt);
            } else {
                $this->_addHtmlPart($message);
            }
            for ($i = 0; $i < count($this->_html_images); $i++) {
                $this->_addHtmlImagePart($message, $this->_html_images[$i]);
            }
            */
            break;

        case $html && $attachments && !$html_images:
            $message =& $this->_addMixedPart();
            if ($this->_txtbody !== null) {
                $alt =& $this->_addAlternativePart($message);
                $this->_addTextPart($alt, $this->_txtbody);
                $this->_addHtmlPart($alt);
            } else {
                $this->_addHtmlPart($message);
            }
            $counter = count($this->_parts);
            for ($i = 0; $i < $counter; $i++) {
                $this->_addAttachmentPart($message, $this->_parts[$i]);
            }
            break;

        case $html && $attachments && $html_images:
            $message =& $this->_addMixedPart();
            if ($this->_txtbody !== null) {
                $alt =& $this->_addAlternativePart($message);
                $this->_addTextPart($alt, $this->_txtbody);
                $rel =& $this->_addRelatedPart($alt);
            } else {
                $rel =& $this->_addRelatedPart($message);
            }
            $this->_addHtmlPart($rel);
            $counter = count($this->_html_images);
            for ($i = 0; $i < $counter; $i++) {
                $this->_addHtmlImagePart($rel, $this->_html_images[$i]);
            }
            $counter = count($this->_parts);
            for ($i = 0; $i < $counter; $i++) {
                $this->_addAttachmentPart($message, $this->_parts[$i]);
            }
            break;

        }

        if (!isset($message)) {
            $ret = null;
            return $ret;
        }
        
        // Use saved boundary
        $boundary = empty($this->_build_params['boundary']) ? null : $this->_build_params['boundary'];

        // Write output to file
        if ($filename) {
            // Append mimePart message headers and body into file
            $headers = $message->encodeToFile($filename, $boundary, $skip_head);
            if ((new PEAR())->isError($headers)) {
                return $headers;
            }
            $this->_headers = array_merge($this->_headers, $headers);
            $ret = null;
            return $ret;
        } else {
            $output = $message->encode($boundary, $skip_head);
            if ((new PEAR())->isError($output)) {
                return $output;
            }
            $this->_headers = array_merge($this->_headers, $output['headers']);
            $body = $output['body'];
            return $body;
        }
    }

    /**
     * Returns an array with the headers needed to prepend to the email
     * (MIME-Version and Content-Type). Format of argument is:
     * $array['header-name'] = 'header-value';
     *
     * @param array $xtra_headers Assoc array with any extra headers (optional)
     *                            (Don't set Content-Type for multipart messages here!)
     * @param bool  $overwrite    Overwrite already existing headers.
     * @param bool  $skip_content Don't return content headers: Content-Type,
     *                            Content-Disposition and Content-Transfer-Encoding
     * 
     * @return array              Assoc array with the mime headers
     * @access public
     */
    function &headers($xtra_headers = null, $overwrite = false, $skip_content = false)
    {
        // Add mime version header
        $headers['MIME-Version'] = '1.0';

        // Content-Type and Content-Transfer-Encoding headers should already
        // be present if get() was called, but we'll re-set them to make sure
        // we got them when called before get() or something in the message
        // has been changed after get() [#14780]
        if (!$skip_content) {
            $headers += $this->_contentHeaders();
        }

        if (!empty($xtra_headers)) {
            $headers = array_merge($headers, $xtra_headers);
        }

        $this->_headers = $overwrite ? array_merge($this->_headers, $headers) : array_merge($headers, $this->_headers);

        $headers = $this->_headers;

        if ($skip_content) {
            unset($headers['Content-Type']);
            unset($headers['Content-Transfer-Encoding']);
            unset($headers['Content-Disposition']);
        } elseif (!empty($this->_build_params['ctype'])) {
            $headers['Content-Type'] = $this->_build_params['ctype'];
        }

        $encodedHeaders = $this->_encodeHeaders($headers);
        return $encodedHeaders;
    }

    /**
     * Get the text version of the headers
     * (usefull if you want to use the PHP mail() function)
     *
     * @param array $xtra_headers Assoc array with any extra headers (optional)
     *                            (Don't set Content-Type for multipart messages here!)
     * @param bool  $overwrite    Overwrite the existing headers with new.
     * @param bool  $skip_content Don't return content headers: Content-Type,
     *                            Content-Disposition and Content-Transfer-Encoding
     *
     * @return string             Plain text headers
     * @access public
     */
    function txtHeaders($xtra_headers = null, $overwrite = false, $skip_content = false)
    {
        $headers = $this->headers($xtra_headers, $overwrite, $skip_content);

        // Place Received: headers at the beginning of the message
        // Spam detectors often flag messages with it after the Subject: as spam
        if (isset($headers['Received'])) {
            $received = $headers['Received'];
            unset($headers['Received']);
            $headers = ['Received' => $received] + $headers;
        }

        $ret = '';
        $eol = $this->_build_params['eol'];

        foreach ($headers as $key => $val) {
            if (is_array($val)) {
                foreach ($val as $value) {
                    $ret .= "$key: $value" . $eol;
                }
            } else {
                $ret .= "$key: $val" . $eol;
            }
        }

        return $ret;
    }

    /**
     * Sets message Content-Type header.
     * Use it to build messages with various content-types e.g. miltipart/raport
     * not supported by _contentHeaders() function.
     *
     * @param string $type   Type name
     * @param array  $params Hash array of header parameters
     *
     * @return void
     * @access public
     * @since 1.7.0
     */
    function setContentType($type, $params = [])
    {
        $header = $type;

        $eol = empty($this->_build_params['eol'])
            ? "\r\n" : $this->_build_params['eol'];

        // add parameters
        $token_regexp = '#([^\x21,\x23-\x27,\x2A,\x2B,\x2D'
            . ',\x2E,\x30-\x39,\x41-\x5A,\x5E-\x7E])#';
        if (is_array($params)) {
            foreach ($params as $name => $value) {
                if ($name == 'boundary') {
                    $this->_build_params['boundary'] = $value;
                }
                if (!preg_match($token_regexp, (string) $value)) {
                    $header .= ";$eol $name=$value";
                } else {
                    $value = addcslashes((string) $value, '\\"');
                    $header .= ";$eol $name=\"$value\"";
                }
            }
        }

        // add required boundary parameter if not defined
        if (preg_match('/^multipart\//i', $type)) {
            if (empty($this->_build_params['boundary'])) {
                $this->_build_params['boundary'] = '=_' . md5(random_int(0, mt_getrandmax()) . microtime());              
            }

            $header .= ";$eol boundary=\"".$this->_build_params['boundary']."\"";
        }

        $this->_build_params['ctype'] = $header;
    }

    /**
     * Sets the Subject header
     *
     * @param string $subject String to set the subject to.
     *
     * @return void
     * @access public
     */
    function setSubject($subject)
    {
        $this->_headers['Subject'] = $subject;
    }

    /**
     * Set an email to the From (the sender) header
     *
     * @param string $email The email address to use
     *
     * @return void
     * @access public
     */
    function setFrom($email)
    {
        $this->_headers['From'] = $email;
    }

    /**
     * Add an email to the Cc (carbon copy) header
     * (multiple calls to this method are allowed)
     *
     * @param string $email The email direction to add
     *
     * @return void
     * @access public
     */
    function addCc($email)
    {
        if (isset($this->_headers['Cc'])) {
            $this->_headers['Cc'] .= ", $email";
        } else {
            $this->_headers['Cc'] = $email;
        }
    }

    /**
     * Add an email to the Bcc (blank carbon copy) header
     * (multiple calls to this method are allowed)
     *
     * @param string $email The email direction to add
     *
     * @return void
     * @access public
     */
    function addBcc($email)
    {
        if (isset($this->_headers['Bcc'])) {
            $this->_headers['Bcc'] .= ", $email";
        } else {
            $this->_headers['Bcc'] = $email;
        }
    }

    /**
     * Since the PHP send function requires you to specify
     * recipients (To: header) separately from the other
     * headers, the To: header is not properly encoded.
     * To fix this, you can use this public method to 
     * encode your recipients before sending to the send
     * function
     *
     * @param string $recipients A comma-delimited list of recipients
     *
     * @return string            Encoded data
     * @access public
     */
    function encodeRecipients($recipients)
    {
        $input = ["To" => $recipients];
        $retval = $this->_encodeHeaders($input);
        return $retval["To"] ;
    }

    /**
     * Encodes headers as per RFC2047
     *
     * @param array $input  The header data to encode
     * @param array $params Extra build parameters
     *
     * @return array        Encoded data
     * @access private
     */
    function _encodeHeaders($input, $params = [])
    {
        $build_params = $this->_build_params;
        foreach ($params as $key => $value) {
            $build_params[$key] = $value;
        }

        foreach ($input as $hdr_name => $hdr_value) {
            if (is_array($hdr_value)) {
                foreach ($hdr_value as $idx => $value) {
                    $input[$hdr_name][$idx] = $this->encodeHeader(
                        $hdr_name, $value,
                        $build_params['head_charset'], $build_params['head_encoding']
                    );
                }
            } else {
                $input[$hdr_name] = $this->encodeHeader(
                    $hdr_name, $hdr_value,
                    $build_params['head_charset'], $build_params['head_encoding']
                );
            }
        }

        return $input;
    }

    /**
     * Encodes a header as per RFC2047
     *
     * @param string $name     The header name
     * @param string $value    The header data to encode
     * @param string $charset  Character set name
     * @param string $encoding Encoding name (base64 or quoted-printable)
     *
     * @return string          Encoded header data (without a name)
     * @access public
     * @since 1.5.3
     */
    function encodeHeader($name, $value, $charset, $encoding)
    {
        return (new Mail_mimePart())->encodeHeader($name, $value, $charset, $encoding, $this->_build_params['eol']);
    }

    /**
     * Get file's basename (locale independent) 
     *
     * @param string $filename Filename
     *
     * @return string          Basename
     * @access private
     */
    function _basename($filename)
    {
        // basename() is not unicode safe and locale dependent
        if (stristr(PHP_OS, 'win') || stristr(PHP_OS, 'netware')) {
            return preg_replace('/^.*[\\\\\\/]/', '', $filename);
        } else {
            return preg_replace('/^.*[\/]/', '', $filename);
        }
    }

    /**
     * Get Content-Type and Content-Transfer-Encoding headers of the message
     *
     * @return array Headers array
     * @access private
     */
    function _contentHeaders()
    {
        $attachments = (bool) count($this->_parts);
        $html_images = (bool) count($this->_html_images);
        $html        = (bool) strlen($this->_htmlbody);
        $text        = !$html && strlen($this->_txtbody);
        $headers     = [];

        // See get()
        switch (true) {
        case $text && !$attachments:
            $headers['Content-Type'] = 'text/plain';
            break;

        case !$text && !$html && $attachments:
        case $text && $attachments:
        case $html && $attachments && !$html_images:
        case $html && $attachments && $html_images:
            $headers['Content-Type'] = 'multipart/mixed';
            break;

        case $html && !$attachments && !$html_images && $this->_txtbody !== null:
        case $html && !$attachments && $html_images && $this->_txtbody !== null:
            $headers['Content-Type'] = 'multipart/alternative';
            break;

        case $html && !$attachments && !$html_images && $this->_txtbody === null:
            $headers['Content-Type'] = 'text/html';
            break;

        case $html && !$attachments && $html_images && $this->_txtbody === null:
            $headers['Content-Type'] = 'multipart/related';
            break;

        default:
            return $headers;
        }

        $this->_checkParams();

        $eol = empty($this->_build_params['eol'])
            ? "\r\n" : $this->_build_params['eol'];

        if ($headers['Content-Type'] == 'text/plain') {
            // single-part message: add charset and encoding
            $headers['Content-Type']
                .= ";$eol charset=" . $this->_build_params['text_charset'];
            $headers['Content-Transfer-Encoding']
                = $this->_build_params['text_encoding'];
        } elseif ($headers['Content-Type'] == 'text/html') {
            // single-part message: add charset and encoding
            $headers['Content-Type']
                .= ";$eol charset=" . $this->_build_params['html_charset'];
            $headers['Content-Transfer-Encoding']
                = $this->_build_params['html_encoding'];
        } else {
            // multipart message: add charset and boundary
            if (!empty($this->_build_params['boundary'])) {
                $boundary = $this->_build_params['boundary'];
            } elseif (!empty($this->_headers['Content-Type'])
                && preg_match('/boundary="([^"]+)"/', (string) $this->_headers['Content-Type'], $m)) {
                $boundary = $m[1];
            } else {
                $boundary = '=_' . md5(random_int(0, mt_getrandmax()) . microtime());
            }

            $this->_build_params['boundary'] = $boundary;
            $headers['Content-Type'] .= ";$eol boundary=\"$boundary\"";
        }

        return $headers;
    }

    /**
     * Validate and set build parameters
     *
     * @return void
     * @access private
     */
    function _checkParams()
    {
        $encodings = ['7bit', '8bit', 'base64', 'quoted-printable'];

        $this->_build_params['text_encoding']
            = strtolower((string) $this->_build_params['text_encoding']);
        $this->_build_params['html_encoding']
            = strtolower((string) $this->_build_params['html_encoding']);

        if (!in_array($this->_build_params['text_encoding'], $encodings)) {
            $this->_build_params['text_encoding'] = '7bit';
        }
        if (!in_array($this->_build_params['html_encoding'], $encodings)) {
            $this->_build_params['html_encoding'] = '7bit';
        }

        // text body
        if ($this->_build_params['text_encoding'] == '7bit'
            && !preg_match('/ascii/i', (string) $this->_build_params['text_charset'])
            && preg_match('/[^\x00-\x7F]/', $this->_txtbody)
        ) {
            $this->_build_params['text_encoding'] = 'quoted-printable';
        }
        // html body
        if ($this->_build_params['html_encoding'] == '7bit'
            && !preg_match('/ascii/i', (string) $this->_build_params['html_charset'])
            && preg_match('/[^\x00-\x7F]/', $this->_htmlbody)
        ) {
            $this->_build_params['html_encoding'] = 'quoted-printable';
        }
    }

} // End of class
