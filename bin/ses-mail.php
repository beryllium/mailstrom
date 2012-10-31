#!/usr/bin/env php
<?php

require_once __DIR__ . '/../bootstrap.php';

$arguments = new \cli\Arguments(array(
    'flags' => array(
        'verbose' => array(
            'description' => 'Turn on verbose mode',
            'aliases' => array('v'),
        ),
    ),
    'options' => array(
        'config' => array(
            'description' => 'Location of config file to use',
        ),
        'to' => array(
            'description' => 'Email Recipient',
            'aliases' => array('recipient'),
        ),
        'from' => array(
            'description' => 'Email Sender',
            'aliases' => array('sender'),
        ),
        'subject' => array(
            'description' => 'Email Subject',
            'aliases' => array('s'),
        ),
        'message' => array(
            'description' => 'Optional string to send as message body (or use STDIN)',
            'aliases' => array('m'),
        ),
    ),
));

$arguments->parse();
$args = $arguments->getArguments();

// Override global settings with CLI options (if any)
$settings = array_merge($settings, $args);

// If message is not set on CLI, read it in from STDIN
if (empty($settings['message'])) {
    $input = null;
    $settings['message'] = fgets(STDIN);

    while (($input = fgets(STDIN)) !== false) {
        $settings['message'] .= $input;
    }
}

$ses = new \AmazonSES(array(
    'key' => $settings['access_key'],
    'secret' => $settings['secret_key'],
));

$mail = new Beryllium\Mailstrom\SesMail($settings, $ses);

$result = $mail->send();

if ($result) {
    \cli\Streams::line('Sent!');

    exit(0);
} else {
    \cli\Streams::line('Failed!');

    // Extracting the error messages from CFResponse, which is just a SimpleXML wrapper
    // This means that it's possible this barebones error extraction code could cause additional errors - if you encounter one,
    // please file an issue with an output example!
    $error = end($mail->responses)->body;
    $error_code = $error->Error->Code;
    $error_msg = substr($error->Error->Message, 0, strpos($error->Error->Message, "\n"));

    \cli\Streams::line('Error Code: ' . $error_code);
    \cli\Streams::line('Error Message: ' . $error_msg);

    // If you're not familiar with exit codes, you should look up boolean logic in CLI shells ... pretty useful. :)
    exit(1);
}
