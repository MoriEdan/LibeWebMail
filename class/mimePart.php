<?php
/**
 * The Mail_mimePart class is used to create MIME E-mail messages
 *
 * This class enables you to manipulate and build a mime email
 * from the ground up. The Mail_Mime class is a userfriendly api
 * to this class for people who aren't interested in the internals
 * of mime mail.
 * This class however allows full control over the email.
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
 * @author    Cipriano Groenendal <cipri@php.net>
 * @author    Sean Coates <sean@php.net>
 * @author    Aleksander Machniak <alec@php.net>
 * @copyright 2003-2006 PEAR <pear-group@php.net>
 * @license   http://www.opensource.org/licenses/bsd-license.php BSD License
 * @version   CVS: $Id: mimePart.php 296266 2010-03-16 10:21:59Z alec $
 * @link      http://pear.php.net/package/Mail_mime
 */


/**
 * The Mail_mimePart class is used to create MIME E-mail messages
 *
 * This class enables you to manipulate and build a mime email
 * from the ground up. The Mail_Mime class is a userfriendly api
 * to this class for people who aren't interested in the internals
 * of mime mail.
 * This class however allows full control over the email.
 *
 * @category  Mail
 * @package   Mail_Mime
 * @author    Richard Heyes  <richard@phpguru.org>
 * @author    Cipriano Groenendal <cipri@php.net>
 * @author    Sean Coates <sean@php.net>
 * @author    Aleksander Machniak <alec@php.net>
 * @copyright 2003-2006 PEAR <pear-group@php.net>
 * @license   http://www.opensource.org/licenses/bsd-license.php BSD License
 * @version   Release: @package_version@
 * @link      http://pear.php.net/package/Mail_mime
 */
class Mail_mimePart
{
    /**
    * The encoding type of this part
    *
    * @var string
    * @access private
    */
    public $_encoding;

    /**
    * An array of subparts
    *
    * @var array
    * @access private
    */
    public $_subparts;

    /**
    * The output of this part after being built
    *
    * @var string
    * @access private
    */
    public $_encoded;

    /**
    * Headers for this part
    *
    * @var array
    * @access private
    */
    public $_headers;

    /**
    * The location of file with body of this part (not encoded)
    *
    * @var string
    * @access private
    */
    public $_body_file;

    /**
    * The end-of-line sequence
    *
    * @var string
    * @access private
    */
    public $_eol = "\r\n";

    /**
     * Constructor.
     *
     * Sets up the object.
     *
     * @param string $_body The body of the mime part if any.
     * @param array  $params An associative array of optional parameters:
     *     content_type      - The content type for this part eg multipart/mixed
     *     encoding          - The encoding to use, 7bit, 8bit,
     *                         base64, or quoted-printable
     *     cid               - Content ID to apply
     *     disposition       - Content disposition, inline or attachment
     *     dfilename         - Filename parameter for content disposition
     *     description       - Content description
     *     charset           - Character set to use
     *     name_encoding     - Encoding for attachment name (Content-Type)
     *                         By default filenames are encoded using RFC2231
     *                         Here you can set RFC2047 encoding (quoted-printable
     *                         or base64) instead
     *     filename_encoding - Encoding for attachment filename (Content-Disposition)
     *                         See 'name_encoding'
     *     eol               - End of line sequence. Default: "\r\n"
     *     body_file         - Location of file with part's body (instead of $body)
     *
     * @access public
     */
    function __construct(/**
     * The body of this part (not encoded)
     *
     * @access private
     */
    public $_body = '', $params = [])
    {
        if (!empty($params['eol'])) {
            $this->_eol = $params['eol'];
        } elseif (defined('MAIL_MIMEPART_CRLF')) {
            // backward-copat.
            $this->_eol = MAIL_MIMEPART_CRLF;
        }

        $c_type = [];
        $c_disp = [];
        foreach ($params as $key => $value) {
            switch ($key) {
            case 'content_type':
                $c_type['type'] = $value;
                break;

            case 'encoding':
                $this->_encoding = $value;
                $headers['Content-Transfer-Encoding'] = $value;
                break;

            case 'cid':
                $headers['Content-ID'] = '<' . $value . '>';
                break;

            case 'disposition':
                $c_disp['disp'] = $value;
                break;

            case 'dfilename':
                $c_disp['filename'] = $value;
                $c_type['name'] = $value;
                break;

            case 'description':
                $headers['Content-Description'] = $value;
                break;

            case 'charset':
                $c_type['charset'] = $value;
                $c_disp['charset'] = $value;
                break;

            case 'language':
                $c_type['language'] = $value;
                $c_disp['language'] = $value;
                break;

            case 'location':
                $headers['Content-Location'] = $value;
                break;

            case 'body_file':
                $this->_body_file = $value;
                break;
            }
        }

        // Content-Type
        if (isset($c_type['type'])) {
            $headers['Content-Type'] = $c_type['type'];
            if (isset($c_type['name'])) {
                $headers['Content-Type'] .= ';' . $this->_eol;
                $headers['Content-Type'] .= $this->_buildHeaderParam(
                    'name', $c_type['name'], 
                    $c_type['charset'] ?? 'US-ASCII', 
                    $c_type['language'] ?? null,
                    $params['name_encoding'] ?? null
                );
            }
            if (isset($c_type['charset'])) {
                $headers['Content-Type']
                    .= ';' . $this->_eol . " charset={$c_type['charset']}";
            }
        }

        // Content-Disposition
        if (isset($c_disp['disp'])) {
            $headers['Content-Disposition'] = $c_disp['disp'];
            if (isset($c_disp['filename'])) {
                $headers['Content-Disposition'] .= ';' . $this->_eol;
                $headers['Content-Disposition'] .= $this->_buildHeaderParam(
                    'filename', $c_disp['filename'], 
                    $c_disp['charset'] ?? 'US-ASCII', 
                    $c_disp['language'] ?? null,
                    $params['filename_encoding'] ?? null
                );
            }
        }

        if (!empty($headers['Content-Description'])) {
            $headers['Content-Description'] = $this->encodeHeader(
                'Content-Description', $headers['Content-Description'],
                $c_type['charset'] ?? 'US-ASCII',
                $params['name_encoding'] ?? 'quoted-printable',
                $this->_eol
            );
        }

        // Default content-type
        if (!isset($headers['Content-Type'])) {
            $headers['Content-Type'] = 'text/plain';
        }

        // Default encoding
        if ($this->_encoding === null) {
            $this->_encoding = '7bit';
        }

        // Assign stuff to member variables
        $this->_encoded  = [];
        $this->_headers  = $headers;
    }

    /**
     * Encodes and returns the email. Also stores
     * it in the encoded member variable
     *
     * @param string $boundary Pre-defined boundary string
     *
     * @return An associative array containing two elements,
     *         body and headers. The headers element is itself
     *         an indexed array. On error returns PEAR error object.
     * @access public
     */
    function encode($boundary=null)
    {
        $encoded =& $this->_encoded;

        if (count($this->_subparts)) {
            $boundary = $boundary ?: '=_' . md5(random_int(0, mt_getrandmax()) . microtime());
            $eol = $this->_eol;
            $this->_headers['Content-Type'] .= ";$eol boundary=\"$boundary\"";
            $encoded['body'] = '';
            $counter = count($this->_subparts);
            for ($i = 0; $i < $counter; $i++) {
                $encoded['body'] .= '--' . $boundary . $eol;
                $tmp = $this->_subparts[$i]->encode();
                if ((new PEAR())->isError($tmp)) {
                    return $tmp;
                }
                foreach ($tmp['headers'] as $key => $value) {
                    $encoded['body'] .= $key . ': ' . $value . $eol;
                }
                $encoded['body'] .= $eol . $tmp['body'] . $eol;
            }
            $encoded['body'] .= '--' . $boundary . '--' . $eol;
        } elseif ($this->_body) {
            $encoded['body'] = $this->_getEncodedData($this->_body, $this->_encoding);
        } elseif ($this->_body_file) {
            // Temporarily reset magic_quotes_runtime for file reads and writes
            if ($magic_quote_setting = get_magic_quotes_runtime()) {
                @ini_set('magic_quotes_runtime', 0);
            }
            $body = $this->_getEncodedDataFromFile($this->_body_file, $this->_encoding);
            if ($magic_quote_setting) {
                @ini_set('magic_quotes_runtime', $magic_quote_setting);
            }
            if ((new PEAR())->isError($body)) {
                return $body;
            }
            $encoded['body'] = $body;
        } else {
            $encoded['body'] = '';
        }

        // Add headers to $encoded
        $encoded['headers'] =& $this->_headers;

        return $encoded;
    }

    /**
     * Encodes and saves the email into file. File must exist.
     * Data will be appended to the file.
     *
     * @param string  $filename  Output file location
     * @param string  $boundary  Pre-defined boundary string
     * @param boolean $skip_head True if you don't want to save headers
     *
     * @return array An associative array containing message headers
     *               or PEAR error object
     * @access public
     * @since 1.6.0
     */
    function encodeToFile($filename, $boundary=null, $skip_head=false)
    {
        if (file_exists($filename) && !is_writable($filename)) {
            return (new PEAR())->raiseError('File is not writeable: ' . $filename);
        }

        if (!($fh = fopen($filename, 'ab'))) {
            return (new PEAR())->raiseError('Unable to open file: ' . $filename);
        }

        // Temporarily reset magic_quotes_runtime for file reads and writes
        if ($magic_quote_setting = get_magic_quotes_runtime()) {
            @ini_set('magic_quotes_runtime', 0);
        }

        $res = $this->_encodePartToFile($fh, $boundary, $skip_head);

        fclose($fh);

        if ($magic_quote_setting) {
            @ini_set('magic_quotes_runtime', $magic_quote_setting);
        }

        return (new PEAR())->isError($res) ? $res : $this->_headers;
    }

    /**
     * Encodes given email part into file
     *
     * @param string  $fh        Output file handle
     * @param string  $boundary  Pre-defined boundary string
     * @param boolean $skip_head True if you don't want to save headers
     *
     * @return array True on sucess or PEAR error object
     * @access private
     */
    function _encodePartToFile($fh, $boundary=null, $skip_head=false)
    {
        $eol = $this->_eol;

        if (count($this->_subparts)) {
            $boundary = $boundary ?: '=_' . md5(random_int(0, mt_getrandmax()) . microtime());
            $this->_headers['Content-Type'] .= ";$eol boundary=\"$boundary\"";
        }

        if (!$skip_head) {
            foreach ($this->_headers as $key => $value) {
                fwrite($fh, $key . ': ' . $value . $eol);
            }
            $f_eol = $eol;
        } else {
            $f_eol = '';
        }

        if (count($this->_subparts)) {
            $counter = count($this->_subparts);
            for ($i = 0; $i < $counter; $i++) {
                fwrite($fh, $f_eol . '--' . $boundary . $eol);
                $res = $this->_subparts[$i]->_encodePartToFile($fh);
                if ((new PEAR())->isError($res)) {
                    return $res;
                }
                $f_eol = $eol;
            }
            fwrite($fh, $eol . '--' . $boundary . '--' . $eol);
        } elseif ($this->_body) {
            fwrite($fh, $f_eol . $this->_getEncodedData($this->_body, $this->_encoding));
        } elseif ($this->_body_file) {
            fwrite($fh, $f_eol);
            $res = $this->_getEncodedDataFromFile(
                $this->_body_file, $this->_encoding, $fh
            );
            if ((new PEAR())->isError($res)) {
                return $res;
            }
        }

        return true;
    }

    /**
     * Adds a subpart to current mime part and returns
     * a reference to it
     *
     * @param string $body   The body of the subpart, if any.
     * @param array  $params The parameters for the subpart, same
     *                       as the $params argument for constructor.
     *
     * @return Mail_mimePart A reference to the part you just added. It is
     *                       crucial if using multipart/* in your subparts that
     *                       you use =& in your script when calling this function,
     *                       otherwise you will not be able to add further subparts.
     * @access public
     */
    function &addSubpart($body, $params)
    {
        $this->_subparts[] = new Mail_mimePart($body, $params);
        return $this->_subparts[count($this->_subparts) - 1];
    }

    /**
     * Returns encoded data based upon encoding passed to it
     *
     * @param string $data     The data to encode.
     * @param string $encoding The encoding type to use, 7bit, base64,
     *                         or quoted-printable.
     *
     * @return string
     * @access private
     */
    function _getEncodedData($data, $encoding)
    {
        return match ($encoding) {
            'quoted-printable' => $this->_quotedPrintableEncode($data),
            'base64' => rtrim(chunk_split(base64_encode($data), 76, $this->_eol)),
            default => $data,
        };
    }

    /**
     * Returns encoded data based upon encoding passed to it
     *
     * @param string   $filename Data file location
     * @param string   $encoding The encoding type to use, 7bit, base64,
     *                           or quoted-printable.
     * @param resource $fh       Output file handle. If set, data will be
     *                           stored into it instead of returning it
     *
     * @return string Encoded data or PEAR error object
     * @access private
     */
    function _getEncodedDataFromFile($filename, $encoding, $fh=null)
    {
        if (!is_readable($filename)) {
            return (new PEAR())->raiseError('Unable to read file: ' . $filename);
        }

        if (!($fd = fopen($filename, 'rb'))) {
            return (new PEAR())->raiseError('Could not open file: ' . $filename);
        }

        $data = '';

        switch ($encoding) {
        case 'quoted-printable':
            while (!feof($fd)) {
                $buffer = $this->_quotedPrintableEncode(fgets($fd));
                if ($fh) {
                    fwrite($fh, $buffer);
                } else {
                    $data .= $buffer;
                }
            }
            break;

        case 'base64':
            while (!feof($fd)) {
                // Should read in a multiple of 57 bytes so that
                // the output is 76 bytes per line. Don't use big chunks
                // because base64 encoding is memory expensive
                $buffer = fread($fd, 57 * 9198); // ca. 0.5 MB
                $buffer = base64_encode($buffer);
                $buffer = chunk_split($buffer, 76, $this->_eol);
                if (feof($fd)) {
                    $buffer = rtrim($buffer);
                }

                if ($fh) {
                    fwrite($fh, $buffer);
                } else {
                    $data .= $buffer;
                }
            }
            break;

        case '8bit':
        case '7bit':
        default:
            while (!feof($fd)) {
                $buffer = fread($fd, 1048576); // 1 MB
                if ($fh) {
                    fwrite($fh, $buffer);
                } else {
                    $data .= $buffer;
                }
            }
        }

        fclose($fd);

        if (!$fh) {
            return $data;
        }
        return null;
    }

    /**
     * Encodes data to quoted-printable standard.
     *
     * @param string $input    The data to encode
     * @param int    $line_max Optional max line length. Should
     *                         not be more than 76 chars
     *
     * @return string Encoded data
     *
     * @access private
     */
    function _quotedPrintableEncode($input , $line_max = 76)
    {
        $eol = $this->_eol;
        /*
        // imap_8bit() is extremely fast, but doesn't handle properly some characters
        if (function_exists('imap_8bit') && $line_max == 76) {
            $input = preg_replace('/\r?\n/', "\r\n", $input);
            $input = imap_8bit($input);
            if ($eol != "\r\n") {
                $input = str_replace("\r\n", $eol, $input);
            }
            return $input;
        }
        */
        $lines  = preg_split("/\r?\n/", $input);
        $escape = '=';
        $output = '';

        foreach ($lines as $idx => $line) {
            $newline = '';
            $i = 0;
            while (isset($line[$i])) {
                $char = $line[$i];
                $dec  = ord($char);
                $i++;

                if (($dec == 32) && (!isset($line[$i]))) {
                    // convert space at eol only
                    $char = '=20';
                } elseif ($dec == 9 && isset($line[$i])) {
                    ; // Do nothing if a TAB is not on eol
                } elseif (($dec == 61) || ($dec < 32) || ($dec > 126)) {
                    $char = $escape . sprintf('%02X', $dec);
                } elseif (($dec == 46) && (($newline === '')
                    || ((strlen($newline) + strlen("=2E")) >= $line_max))
                ) {
                    // Bug #9722: convert full-stop at bol,
                    // some Windows servers need this, won't break anything (cipri)
                    // Bug #11731: full-stop at bol also needs to be encoded
                    // if this line would push us over the line_max limit.
                    $char = '=2E';
                }

                // Note, when changing this line, also change the ($dec == 46)
                // check line, as it mimics this line due to Bug #11731
                // EOL is not counted
                if ((strlen($newline) + strlen($char)) >= $line_max) {
                    // soft line break; " =\r\n" is okay
                    $output  .= $newline . $escape . $eol;
                    $newline  = '';
                }
                $newline .= $char;
            }
            // end of for
            $output .= $newline . $eol;
            unset($lines[$idx]);
        }
        // Don't want last crlf
        $output = substr($output, 0, -1 * strlen($eol));
        return $output;
    }

    /**
     * Encodes the paramater of a header.
     *
     * @param string $name      The name of the header-parameter
     * @param string $value     The value of the paramter
     * @param string $charset   The characterset of $value
     * @param string $language  The language used in $value
     * @param string $encoding  Parameter encoding. If not set, parameter value
     *                          is encoded according to RFC2231
     * @param int    $maxLength The maximum length of a line. Defauls to 75
     *
     * @return string
     *
     * @access private
     */
    function _buildHeaderParam($name, $value, $charset=null, $language=null,
        $encoding=null, $maxLength=75
    ) {
        // RFC 2045:
        // value needs encoding if contains non-ASCII chars or is longer than 78 chars
        if (!preg_match('#[^\x20-\x7E]#', $value)) {
            $token_regexp = '#([^\x21,\x23-\x27,\x2A,\x2B,\x2D'
                . ',\x2E,\x30-\x39,\x41-\x5A,\x5E-\x7E])#';
            if (!preg_match($token_regexp, $value)) {
                // token
                if (strlen($name) + strlen($value) + 3 <= $maxLength) {
                    return " {$name}={$value}";
                }
            } else {
                // quoted-string
                $quoted = addcslashes($value, '\\"');
                if (strlen($name) + strlen($quoted) + 5 <= $maxLength) {
                    return " {$name}=\"{$quoted}\"";
                }
            }
        }

        // RFC2047: use quoted-printable/base64 encoding
        if ($encoding == 'quoted-printable' || $encoding == 'base64') {
            return $this->_buildRFC2047Param($name, $value, $charset, $encoding);
        }

        // RFC2231:
        $encValue = preg_replace_callback(
            '/([^\x21,\x23,\x24,\x26,\x2B,\x2D,\x2E,\x30-\x39,\x41-\x5A,\x5E-\x7E])/',
            $this->_encodeReplaceCallback(...), $value
        );
        $value = "$charset'$language'$encValue";

        $header = " {$name}*={$value}";
        if (strlen($header) <= $maxLength) {
            return $header;
        }

        $preLength = strlen(" {$name}*0*=");
        $maxLength = max(16, $maxLength - $preLength - 3);
        $maxLengthReg = "|(.{0,$maxLength}[^\%][^\%])|";

        $headers = [];
        $headCount = 0;
        while ($value) {
            $matches = [];
            $found = preg_match($maxLengthReg, $value, $matches);
            if ($found) {
                $headers[] = " {$name}*{$headCount}*={$matches[0]}";
                $value = substr($value, strlen($matches[0]));
            } else {
                $headers[] = " {$name}*{$headCount}*={$value}";
                $value = '';
            }
            $headCount++;
        }
        return implode(';' . $this->_eol, $headers);
    }

    /**
     * Encodes header parameter as per RFC2047 if needed
     *
     * @param string $name      The parameter name
     * @param string $value     The parameter value
     * @param string $charset   The parameter charset
     * @param string $encoding  Encoding type (quoted-printable or base64)
     * @param int    $maxLength Encoded parameter max length. Default: 76
     *
     * @return string Parameter line
     * @access private
     */
    function _buildRFC2047Param($name, $value, $charset,
        $encoding='quoted-printable', $maxLength=76
    ) {
        // WARNING: RFC 2047 says: "An 'encoded-word' MUST NOT be used in
        // parameter of a MIME Content-Type or Content-Disposition field",
        // but... it's supported by many clients/servers
        $quoted = '';

        if ($encoding == 'base64') {
            $value = base64_encode($value);
            $prefix = '=?' . $charset . '?B?';
            $suffix = '?=';

            // 2 x SPACE, 2 x '"', '=', ';'
            $add_len = strlen($prefix . $suffix) + strlen($name) + 6;
            $len = $add_len + strlen($value);

            while ($len > $maxLength) { 
                // We can cut base64-encoded string every 4 characters
                $real_len = floor(($maxLength - $add_len) / 4) * 4;
                $_quote = substr($value, 0, $real_len);
                $value = substr($value, $real_len);

                $quoted .= $prefix . $_quote . $suffix . $this->_eol . ' ';
                $add_len = strlen($prefix . $suffix) + 4; // 2 x SPACE, '"', ';'
                $len = strlen($value) + $add_len;
            }
            $quoted .= $prefix . $value . $suffix;

        } else {
            // quoted-printable
            $value = $this->encodeQP($value);
            $prefix = '=?' . $charset . '?Q?';
            $suffix = '?=';

            // 2 x SPACE, 2 x '"', '=', ';'
            $add_len = strlen($prefix . $suffix) + strlen($name) + 6;
            $len = $add_len + strlen($value);

            while ($len > $maxLength) {
                $length = $maxLength - $add_len;
                // don't break any encoded letters
                if (preg_match("/^(.{0,$length}[^\=][^\=])/", $value, $matches)) {
                    $_quote = $matches[1];
                }

                $quoted .= $prefix . $_quote . $suffix . $this->_eol . ' ';
                $value = substr($value, strlen($_quote));
                $add_len = strlen($prefix . $suffix) + 4; // 2 x SPACE, '"', ';'
                $len = strlen($value) + $add_len;
            }

            $quoted .= $prefix . $value . $suffix;
        }

        return " {$name}=\"{$quoted}\"";
    }

    /**
     * Encodes a header as per RFC2047
     *
     * @param string $name     The header name
     * @param string $value    The header data to encode
     * @param string $charset  Character set name
     * @param string $encoding Encoding name (base64 or quoted-printable)
     * @param string $eol      End-of-line sequence. Default: "\r\n"
     *
     * @return string          Encoded header data (without a name)
     * @access public
     * @since 1.6.1
     */
    function encodeHeader($name, $value, $charset='ISO-8859-1',
        $encoding='quoted-printable', $eol="\r\n"
    ) {
        // Structured headers
        $comma_headers = [
            'from', 'to', 'cc', 'bcc', 'sender', 'reply-to',
            'resent-from', 'resent-to', 'resent-cc', 'resent-bcc',
            'resent-sender', 'resent-reply-to',
            'return-receipt-to', 'disposition-notification-to',
        ];
        $other_headers = [
            'references', 'in-reply-to', 'message-id', 'resent-message-id',
        ];

        $name = strtolower($name);

        if (in_array($name, $comma_headers)) {
            $separator = ',';
        } elseif (in_array($name, $other_headers)) {
            $separator = ' ';
        }

        if (!$charset) {
            $charset = 'ISO-8859-1';
        }

        // Structured header (make sure addr-spec inside is not encoded)
        if ($separator !== '0') {
            $parts = Mail_mimePart::_explodeQuotedString($separator, $value);
            $value = '';
            foreach ($parts as $part) {
                $part = preg_replace('/\r?\n[\s\t]*/', $eol . ' ', (string) $part);
                $part = trim((string) $part);

                if ($part === '' || $part === '0') {
                    continue;
                }
                if ($value !== '' && $value !== '0') {
                    $value .= $separator === ',' ? $separator.' ' : ' ';
                } else {
                    $value = $name . ': ';
                }

                // let's find phrase (name) and/or addr-spec
                if (preg_match('/^<\S+@\S+>$/', $part)) {
                    $value .= $part;
                } elseif (preg_match('/^\S+@\S+$/', $part)) {
                    // address without brackets and without name
                    $value .= $part;
                } elseif (preg_match('/<*\S+@\S+>*$/', $part, $matches)) {
                    // address with name (handle name)
                    $address = $matches[0];
                    $word = str_replace($address, '', $part);
                    $word = trim($word);
                    // check if phrase requires quoting
                    if ($word !== '' && $word !== '0') {
                        // non-ASCII: require encoding
                        if (preg_match('#([\x80-\xFF]){1}#', $word)) {
                            if ($word[0] == '"' && $word[strlen($word)-1] == '"') {
                                // de-quote quoted-string, encoding changes
                                // string to atom
                                $search = ["\\\"", "\\\\"];
                                $replace = ["\"", "\\"];
                                $word = str_replace($search, $replace, $word);
                                $word = substr($word, 1, -1);
                            }
                            // find length of last line
                            $last_len = $pos = strrpos($value, $eol) !== false ? strlen($value) - $pos : strlen($value);
                            $word = Mail_mimePart::encodeHeaderValue(
                                $word, $charset, $encoding, $last_len, $eol
                            );
                        } elseif (($word[0] != '"' || $word[strlen($word)-1] != '"')
                            && preg_match('/[\(\)\<\>\\\.\[\]@,;:"]/', $word)) {
                            // ASCII: quote string if needed
                            $word = '"'.addcslashes($word, '\\"').'"';
                        }
                    }
                    $value .= $word.' '.$address;
                } else {
                    // addr-spec not found, don't encode (?)
                    $value .= $part;
                }

                // RFC2822 recommends 78 characters limit, use 76 from RFC2047
                $value = wordwrap($value, 76, $eol . ' ');
            }
            // remove header name prefix (there could be EOL too)
            $value = preg_replace(
                '/^'.$name.':('.preg_quote($eol, '/').')* /', '', $value
            );
        } elseif (preg_match('#([\x80-\xFF]){1}#', $value)) {
            // Unstructured header
            // non-ASCII: require encoding
            if ($value[0] == '"' && $value[strlen($value)-1] == '"') {
                // de-quote quoted-string, encoding changes
                // string to atom
                $search = ["\\\"", "\\\\"];
                $replace = ["\"", "\\"];
                $value = str_replace($search, $replace, $value);
                $value = substr($value, 1, -1);
            }
            $value = Mail_mimePart::encodeHeaderValue(
                $value, $charset, $encoding, strlen($name) + 2, $eol
            );
        } elseif (strlen($name.': '.$value) > 78) {
            // ASCII: check if header line isn't too long and use folding
            $value = preg_replace('/\r?\n[\s\t]*/', $eol . ' ', $value);
            $tmp = wordwrap($name.': '.$value, 78, $eol . ' ');
            $value = preg_replace('/^'.$name.':\s*/', '', $tmp);
            // hard limit 998 (RFC2822)
            $value = wordwrap((string) $value, 998, $eol . ' ', true);
        }

        return $value;
    }

    /**
     * Explode quoted string
     *
     * @param string $delimiter Delimiter expression string for preg_match()
     * @param string $string    Input string
     *
     * @return array            String tokens array
     * @access private
     */
    function _explodeQuotedString($delimiter, $string)
    {
        $result = [];
        $strlen = strlen($string);

        for ($q=$p=$i=0; $i < $strlen; $i++) {
            if ($string[$i] == "\""
                && (empty($string[$i-1]) || $string[$i-1] != "\\")) {
                $q = !(bool) $q;
            } elseif (!$q && preg_match("/$delimiter/", $string[$i])) {
                $result[] = substr($string, $p, $i - $p);
                $p = $i + 1;
            }
        }

        $result[] = substr($string, $p);
        return $result;
    }

    /**
     * Encodes a header value as per RFC2047
     *
     * @param string $value      The header data to encode
     * @param string $charset    Character set name
     * @param string $encoding   Encoding name (base64 or quoted-printable)
     * @param int    $prefix_len Prefix length. Default: 0
     * @param string $eol        End-of-line sequence. Default: "\r\n"
     *
     * @return string            Encoded header data
     * @access public
     * @since 1.6.1
     */
    function encodeHeaderValue($value, $charset, $encoding, $prefix_len=0, $eol="\r\n")
    {
        if ($encoding == 'base64') {
            // Base64 encode the entire string
            $value = base64_encode($value);

            // Generate the header using the specified params and dynamicly 
            // determine the maximum length of such strings.
            // 75 is the value specified in the RFC.
            $prefix = '=?' . $charset . '?B?';
            $suffix = '?=';
            $maxLength = 75 - strlen($prefix . $suffix) - 2;
            $maxLength1stLine = $maxLength - $prefix_len;

            // We can cut base4 every 4 characters, so the real max
            // we can get must be rounded down.
            $maxLength -= $maxLength % 4;
            $maxLength1stLine -= $maxLength1stLine % 4;

            $cutpoint = $maxLength1stLine;
            $value_out = $value;
            $output = '';
            while ($value_out) {
                // Split translated string at every $maxLength
                $part = substr($value_out, 0, $cutpoint);
                $value_out = substr($value_out, $cutpoint);
                $cutpoint = $maxLength;
                // RFC 2047 specifies that any split header should
                // be seperated by a CRLF SPACE. 
                if ($output !== '' && $output !== '0') {
                    $output .= $eol . ' ';
                }
                $output .= $prefix . $part . $suffix;
            }
            $value = $output;
        } else {
            // quoted-printable encoding has been selected
            $value = Mail_mimePart::encodeQP($value);

            // Generate the header using the specified params and dynamicly 
            // determine the maximum length of such strings.
            // 75 is the value specified in the RFC.
            $prefix = '=?' . $charset . '?Q?';
            $suffix = '?=';
            $maxLength = 75 - strlen($prefix . $suffix) - 3;
            $maxLength1stLine = $maxLength - $prefix_len;
            $maxLength -= 1;

            // This regexp will break QP-encoded text at every $maxLength
            // but will not break any encoded letters.
            $reg1st = "|(.{0,$maxLength1stLine}[^\=][^\=])|";
            $reg2nd = "|(.{0,$maxLength}[^\=][^\=])|";

            $value_out = $value;
            $realMax = $maxLength1stLine + strlen($prefix . $suffix);
            if (strlen($value_out) >= $realMax) {
                // Begin with the regexp for the first line.
                $reg = $reg1st;
                $output = '';
                while ($value_out) {
                    // Split translated string at every $maxLength
                    // But make sure not to break any translated chars.
                    $found = preg_match($reg, $value_out, $matches);

                    // After this first line, we need to use a different
                    // regexp for the first line.
                    $reg = $reg2nd;

                    // Save the found part and encapsulate it in the
                    // prefix & suffix. Then remove the part from the
                    // $value_out variable.
                    if ($found) {
                        $part = $matches[0];
                        $len = strlen($matches[0]);
                        $value_out = substr($value_out, $len);
                    } else {
                        $part = $value_out;
                        $value_out = "";
                    }

                    // RFC 2047 specifies that any split header should 
                    // be seperated by a CRLF SPACE
                    if ($output !== '' && $output !== '0') {
                        $output .= $eol . ' ';
                    }
                    $output .= $prefix . $part . $suffix;
                }
                $value_out = $output;
            } else {
                $value_out = $prefix . $value_out . $suffix;
            }
            $value = $value_out;
        }

        return $value;
    }

    /**
     * Encodes the given string using quoted-printable
     *
     * @param string $str String to encode
     *
     * @return string     Encoded string
     * @access public
     * @since 1.6.0
     */
    function encodeQP($str)
    {
        // Bug #17226 RFC 2047 restricts some characters
        // if the word is inside a phrase, permit chars are only:
        // ASCII letters, decimal digits, "!", "*", "+", "-", "/", "=", and "_"

        // "=",  "_",  "?" must be encoded
        $regexp = '/([\x22-\x29\x2C\x2E\x3A-\x40\x5B-\x60\x7B-\x7E\x80-\xFF])/';
        $str = preg_replace_callback(
            $regexp, ['Mail_mimePart', '_qpReplaceCallback'], $str
        );

        return str_replace(' ', '_', $str);
    }

    /**
     * Callback function to replace extended characters (\x80-xFF) with their
     * ASCII values (RFC2047: quoted-printable)
     *
     * @param array $matches Preg_replace's matches array
     *
     * @return string        Encoded character string
     * @access private
     */
    function _qpReplaceCallback($matches)
    {
        return sprintf('=%02X', ord($matches[1]));
    }

    /**
     * Callback function to replace extended characters (\x80-xFF) with their
     * ASCII values (RFC2231)
     *
     * @param array $matches Preg_replace's matches array
     *
     * @return string        Encoded character string
     * @access private
     */
    function _encodeReplaceCallback($matches)
    {
        return sprintf('%%%02X', ord($matches[1]));
    }

} // End of class
