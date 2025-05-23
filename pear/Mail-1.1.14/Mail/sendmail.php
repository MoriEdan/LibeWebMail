<?php
//
// +----------------------------------------------------------------------+
// | PHP Version 4                                                        |
// +----------------------------------------------------------------------+
// | Copyright (c) 1997-2003 The PHP Group                                |
// +----------------------------------------------------------------------+
// | This source file is subject to version 2.02 of the PHP license,      |
// | that is bundled with this package in the file LICENSE, and is        |
// | available at through the world-wide-web at                           |
// | http://www.php.net/license/2_02.txt.                                 |
// | If you did not receive a copy of the PHP license and are unable to   |
// | obtain it through the world-wide-web, please send a note to          |
// | license@php.net so we can mail you a copy immediately.               |
// +----------------------------------------------------------------------+
// | Author: Chuck Hagenbuch <chuck@horde.org>                            |
// +----------------------------------------------------------------------+

/**
 * Sendmail implementation of the PEAR Mail:: interface.
 * @access public
 * @package Mail
 * @version $Revision: 1.17 $
 */
class Mail_sendmail extends Mail {

	/**
     * The location of the sendmail or sendmail wrapper binary on the
     * filesystem.
     * @var string
     */
    public $sendmail_path = '/usr/sbin/sendmail';

	/**
     * Any extra command-line parameters to pass to the sendmail or
     * sendmail wrapper binary.
     * @var string
     */
    public $sendmail_args = '-i';

	/**
     * Constructor.
     *
     * Instantiates a new Mail_sendmail:: object based on the parameters
     * passed in. It looks for the following parameters:
     *     sendmail_path    The location of the sendmail binary on the
     *                      filesystem. Defaults to '/usr/sbin/sendmail'.
     *
     *     sendmail_args    Any extra parameters to pass to the sendmail
     *                      or sendmail wrapper binary.
     *
     * If a parameter is present in the $params array, it replaces the
     * default.
     *
     * @param array $params Hash containing any parameters different from the
     *              defaults.
     * @access public
     */
    function __construct($params)
    {
        if (isset($params['sendmail_path'])) {
            $this->sendmail_path = $params['sendmail_path'];
        }
        if (isset($params['sendmail_args'])) {
            $this->sendmail_args = $params['sendmail_args'];
        }

        /*
         * Because we need to pass message headers to the sendmail program on
         * the commandline, we can't guarantee the use of the standard "\r\n"
         * separator.  Instead, we use the system's native line separator.
         */
        if (defined('PHP_EOL')) {
            $this->sep = PHP_EOL;
        } else {
            $this->sep = (str_contains(PHP_OS, 'WIN')) ? "\r\n" : "\n";
        }
    }

	/**
     * Implements Mail::send() function using the sendmail
     * command-line binary.
     *
     * @param mixed $recipients Either a comma-seperated list of recipients
     *              (RFC822 compliant), or an array of recipients,
     *              each RFC822 valid. This may contain recipients not
     *              specified in the headers, for Bcc:, resending
     *              messages, etc.
     *
     * @param array $headers The array of headers to send with the mail, in an
     *              associative array, where the array key is the
     *              header name (ie, 'Subject'), and the array value
     *              is the header value (ie, 'test'). The header
     *              produced from those values would be 'Subject:
     *              test'.
     *
     * @param string $body The full text of the message body, including any
     *               Mime parts, etc.
     *
     * @return mixed Returns true on success, or a PEAR_Error
     *               containing a descriptive error message on
     *               failure.
     * @access public
     */
    #[\Override]
    function send($recipients, $headers, $body)
    {
        $recipients = $this->parseRecipients($recipients);
        if ((new PEAR())->isError($recipients)) {
            return $recipients;
        }
        $recipients = escapeShellCmd(implode(' ', $recipients));

        $this->_sanitizeHeaders($headers);
        $headerElements = $this->prepareHeaders($headers);
        if ((new PEAR())->isError($headerElements)) {
            return $headerElements;
        }
        [$from, $text_headers] = $headerElements;

        if (!isset($from)) {
            return (new PEAR())->raiseError('No from address given.');
        } elseif (str_contains($from, ' ') ||
                  str_contains($from, ';') ||
                  str_contains($from, '&') ||
                  str_contains($from, '`')) {
            return (new PEAR())->raiseError('From address specified with dangerous characters.');
        }

        $from = escapeShellCmd($from);
        $mail = @popen($this->sendmail_path . (empty($this->sendmail_args) ? '' : ' ' . $this->sendmail_args) . " -f$from -- $recipients", 'w');
        if (!$mail) {
            return (new PEAR())->raiseError('Failed to open sendmail [' . $this->sendmail_path . '] for execution.');
        }

        // Write the headers following by two newlines: one to end the headers
        // section and a second to separate the headers block from the body.
        fwrite($mail, $text_headers . $this->sep . $this->sep);

        fwrite($mail, $body);
        $result = pclose($mail);
        if (version_compare(phpversion(), '4.2.3') == -1) {
            // With older php versions, we need to shift the pclose
            // result to get the exit code.
            $result = $result >> 8 & 0xFF;
        }

        if ($result != 0) {
            return (new PEAR())->raiseError('sendmail returned error code ' . $result, $result);
        }

        return true;
    }

}
