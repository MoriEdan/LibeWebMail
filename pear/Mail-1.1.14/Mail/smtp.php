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
// | Authors: Chuck Hagenbuch <chuck@horde.org>                           |
// |          Jon Parise <jon@php.net>                                    |
// +----------------------------------------------------------------------+

/** Error: Failed to create a Net_SMTP object */
define('PEAR_MAIL_SMTP_ERROR_CREATE', 10000);

/** Error: Failed to connect to SMTP server */
define('PEAR_MAIL_SMTP_ERROR_CONNECT', 10001);

/** Error: SMTP authentication failure */
define('PEAR_MAIL_SMTP_ERROR_AUTH', 10002);

/** Error: No From: address has been provided */
define('PEAR_MAIL_SMTP_ERROR_FROM', 10003);

/** Error: Failed to set sender */
define('PEAR_MAIL_SMTP_ERROR_SENDER', 10004);

/** Error: Failed to add recipient */
define('PEAR_MAIL_SMTP_ERROR_RECIPIENT', 10005);

/** Error: Failed to send data */
define('PEAR_MAIL_SMTP_ERROR_DATA', 10006);

/**
 * SMTP implementation of the PEAR Mail interface. Requires the Net_SMTP class.
 * @access public
 * @package Mail
 * @version $Revision: 1.28 $
 */
class Mail_smtp extends Mail {

    /**
     * SMTP connection object.
     *
     * @var object
     * @access private
     */
    public $_smtp;

    /**
     * The SMTP host to connect to.
     * @var string
     */
    public $host = 'localhost';

    /**
     * The port the SMTP server is on.
     * @var integer
     */
    public $port = 25;

    /**
     * Should SMTP authentication be used?
     *
     * This value may be set to true, false or the name of a specific
     * authentication method.
     *
     * If the value is set to true, the Net_SMTP package will attempt to use
     * the best authentication method advertised by the remote SMTP server.
     *
     * @var mixed
     */
    public $auth = false;

    /**
     * The username to use if the SMTP server requires authentication.
     * @var string
     */
    public $username = '';

    /**
     * The password to use if the SMTP server requires authentication.
     * @var string
     */
    public $password = '';

    /**
     * Hostname or domain that will be sent to the remote SMTP server in the
     * HELO / EHLO message.
     *
     * @var string
     */
    public $localhost = 'localhost';

    /**
     * SMTP connection timeout value.  NULL indicates no timeout.
     *
     * @var integer
     */
    public $timeout;

    /**
     * Whether to use VERP or not. If not a boolean, the string value
     * will be used as the VERP separators.
     *
     * @var mixed boolean or string
     */
    public $verp = false;

    /**
     * Turn on Net_SMTP debugging?
     *
     * @var boolean $debug
     */
    public $debug = false;

    /**
     * Indicates whether or not the SMTP connection should persist over
     * multiple calls to the send() method.
     *
     * @var boolean
     */
    public $persist = false;

    /**
     * Constructor.
     *
     * Instantiates a new Mail_smtp:: object based on the parameters
     * passed in. It looks for the following parameters:
     *     host        The server to connect to. Defaults to localhost.
     *     port        The port to connect to. Defaults to 25.
     *     auth        SMTP authentication.  Defaults to none.
     *     username    The username to use for SMTP auth. No default.
     *     password    The password to use for SMTP auth. No default.
     *     localhost   The local hostname / domain. Defaults to localhost.
     *     timeout     The SMTP connection timeout. Defaults to none.
     *     verp        Whether to use VERP or not. Defaults to false.
     *     debug       Activate SMTP debug mode? Defaults to false.
     *     persist     Should the SMTP connection persist?
     *
     * If a parameter is present in the $params array, it replaces the
     * default.
     *
     * @param array Hash containing any parameters different from the
     *              defaults.
     * @access public
     */
    function __construct($params)
    {
        if (isset($params['host'])) {
            $this->host = $params['host'];
        }
        if (isset($params['port'])) {
            $this->port = $params['port'];
        }
        if (isset($params['auth'])) {
            $this->auth = $params['auth'];
        }
        if (isset($params['username'])) {
            $this->username = $params['username'];
        }
        if (isset($params['password'])) {
            $this->password = $params['password'];
        }
        if (isset($params['localhost'])) {
            $this->localhost = $params['localhost'];
        }
        if (isset($params['timeout'])) {
            $this->timeout = $params['timeout'];
        }
        if (isset($params['verp'])) {
            $this->verp = $params['verp'];
        }
        if (isset($params['debug'])) {
            $this->debug = (boolean)$params['debug'];
        }
        if (isset($params['persist'])) {
            $this->persist = (boolean)$params['persist'];
        }

        register_shutdown_function([&$this, '_Mail_smtp']);
    }

    /**
     * Destructor implementation to ensure that we disconnect from any
     * potentially-alive persistent SMTP connections.
     */
    function _Mail_smtp()
    {
        $this->disconnect();
    }

    /**
     * Implements Mail::send() function using SMTP.
     *
     * @param mixed $recipients Either a comma-seperated list of recipients
     *              (RFC822 compliant), or an array of recipients,
     *              each RFC822 valid. This may contain recipients not
     *              specified in the headers, for Bcc:, resending
     *              messages, etc.
     *
     * @param array $headers The array of headers to send with the mail, in an
     *              associative array, where the array key is the
     *              header name (e.g., 'Subject'), and the array value
     *              is the header value (e.g., 'test'). The header
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
        include_once __DIR__ . '/Net/SMTP.php';
        include_once __DIR__ . '/PEAR.php';
        $pear = new PEAR();

        /* If we don't already have an SMTP object, create one. */
        if (is_object($this->_smtp) === false) {
            $this->_smtp = new Net_SMTP($this->host, $this->port,
                                         $this->localhost);

            /* If we still don't have an SMTP object at this point, fail. */
            if (is_object($this->_smtp) === false) {
                return $pear->raiseError('Failed to create a Net_SMTP object',
                                        PEAR_MAIL_SMTP_ERROR_CREATE);
            }

            /* Configure the SMTP connection. */
            if ($this->debug) {
                $this->_smtp->setDebug(true);
            }

            /* Attempt to connect to the configured SMTP server. */
            if ($pear->isError($res = $this->_smtp->connect($this->timeout))) {
                $error = $this->_error('Failed to connect to ' .
                                       $this->host . ':' . $this->port,
                                       $res);
                return $pear->raiseError($error, PEAR_MAIL_SMTP_ERROR_CONNECT);
            }

            /* Attempt to authenticate if authentication has been enabled. */
            if ($this->auth) {
                $method = is_string($this->auth) ? $this->auth : '';

                if ($pear->isError($res = $this->_smtp->auth($this->username,
                                                            $this->password,
                                                            $method))) {
                    $error = $this->_error("$method authentication failure",
                                           $res);
                    $this->_smtp->rset();
                    return $pear->raiseError($error, PEAR_MAIL_SMTP_ERROR_AUTH);
                }
            }
        }

        $this->_sanitizeHeaders($headers);
        $headerElements = $this->prepareHeaders($headers);
        if ($pear->isError($headerElements)) {
            $this->_smtp->rset();
            return $headerElements;
        }
        [$from, $textHeaders] = $headerElements;

        /* Since few MTAs are going to allow this header to be forged
         * unless it's in the MAIL FROM: exchange, we'll use
         * Return-Path instead of From: if it's set. */
        if (!empty($headers['Return-Path'])) {
            $from = $headers['Return-Path'];
        }

        if (!isset($from)) {
            $this->_smtp->rset();
            return $pear->raiseError('No From: address has been provided',
                                    PEAR_MAIL_SMTP_ERROR_FROM);
        }

        $args['verp'] = $this->verp;
        if ($pear->isError($res = $this->_smtp->mailFrom($from, $args))) {
            $error = $this->_error("Failed to set sender: $from", $res);
            $this->_smtp->rset();
            return $pear->raiseError($error, PEAR_MAIL_SMTP_ERROR_SENDER);
        }

        $recipients = $this->parseRecipients($recipients);
        if ($pear->isError($recipients)) {
            $this->_smtp->rset();
            return $recipients;
        }

        foreach ($recipients as $recipient) {
            if ($pear->isError($res = $this->_smtp->rcptTo($recipient))) {
                $error = $this->_error("Failed to add recipient: $recipient",
                                       $res);
                $this->_smtp->rset();
                return $pear->raiseError($error, PEAR_MAIL_SMTP_ERROR_RECIPIENT);
            }
        }

        /* Send the message's headers and the body as SMTP data. */
        if ($pear->isError($res = $this->_smtp->data($textHeaders . "\r\n\r\n" . $body))) {
            $error = $this->_error('Failed to send data', $res);
            $this->_smtp->rset();
            return $pear->raiseError($error, PEAR_MAIL_SMTP_ERROR_DATA);
        }

        /* If persistent connections are disabled, destroy our SMTP object. */
        if ($this->persist === false) {
            $this->disconnect();
        }

        return true;
    }

    /**
     * Disconnect and destroy the current SMTP connection.
     *
     * @return boolean True if the SMTP connection no longer exists.
     *
     * @since  1.1.9
     * @access public
     */
    function disconnect()
    {
        /* If we have an SMTP object, disconnect and destroy it. */
        if (is_object($this->_smtp) && $this->_smtp->disconnect()) {
            $this->_smtp = null;
        }

        /* We are disconnected if we no longer have an SMTP object. */
        return ($this->_smtp === null);
    }

    /**
     * Build a standardized string describing the current SMTP error.
     *
     * @param string $text  Custom string describing the error context.
     * @param object $error Reference to the current PEAR_Error object.
     *
     * @return string       A string describing the current SMTP error.
     *
     * @since  1.1.7
     * @access private
     */
    function _error($text, &$error)
    {
        /* Split the SMTP response into a code and a response string. */
        [$code, $response] = $this->_smtp->getResponse();

        /* Build our standardized error string. */
        $msg = $text;
        $msg .= ' [SMTP: ' . $error->getMessage();

        return $msg . " (code: $code, response: $response)]";
    }

}
