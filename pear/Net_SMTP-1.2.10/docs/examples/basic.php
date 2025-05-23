<?php

require __DIR__ . '/Net/SMTP.php';

$host = 'mail.example.com';
$from = 'user@example.com';
$rcpt = ['recipient1@example.com', 'recipient2@example.com'];
$subj = "Subject: Test Message\n";
$body = "Body Line 1\nBody Line 2";

/* Create a new Net_SMTP object. */
if (! ($smtp = new Net_SMTP($host))) {
    die("Unable to instantiate Net_SMTP object\n");
}

/* Connect to the SMTP server. */
if ((new PEAR())->isError($e = $smtp->connect())) {
    die($e->getMessage() . "\n");
}

/* Send the 'MAIL FROM:' SMTP command. */
if ((new PEAR())->isError($smtp->mailFrom($from))) {
    die("Unable to set sender to <$from>\n");
}

/* Address the message to each of the recipients. */
foreach ($rcpt as $to) {
    if ((new PEAR())->isError($res = $smtp->rcptTo($to))) {
        die("Unable to add recipient <$to>: " . $res->getMessage() . "\n");
    }
}

/* Set the body of the message. */
if ((new PEAR())->isError($smtp->data($subj . "\r\n" . $body))) {
    die("Unable to send data\n");
}

/* Disconnect from the SMTP server. */
$smtp->disconnect();
