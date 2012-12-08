#!/usr/bin/env php
<?php

require_once __DIR__ . '/../bootstrap.php';

use GetOptionKit\GetOptionKit;

$getopt = new GetOptionKit();

$spec = $getopt->add( 'h|help', 'Show help screen' );
//$getopt->add( 'v|verbose', 'Turn on verbose mode' );
//$getopt->add( 'c|config?', "Location of config file to use" );
$getopt->add( 'from?', 'Sender' );
$getopt->add( 's|subject:', 'Subject' );
$getopt->add( 'm|message?', 'Body' );
$getopt->add( 'to:', 'Recipient' );

$options = $getopt->parse( $argv );

if ( $options->has( 'help' ) )
{ 
  writeln( "Usage: ses-mail [-s subject] to-addr");
  $getopt->specs->printOptions();
  writeln();
  exit(0);
}

$arguments = $options->getArguments();
array_shift($arguments); //We don't need the first element at the moment (path to the executable)

$args = $options->toArray();

if ( count( $arguments ) == 1 && $options->has('to') )
{
  echo "ERROR: Syntax error. Please specify a recipient using either an argument or the --to option. Not both.\n";
  exit (1);
}

if ( count( $arguments ) > 1 )
{
  echo "ERROR: Too many arguments.\n";
  exit (1);
}
else if ( count( $arguments == 1 ) )
{
  $args['to'] = $arguments[0];
}

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
    writeln('Sent!');

    exit(0);
} else {
    writeln('Failed!');

    // Extracting the error messages from CFResponse, which is just a SimpleXML wrapper
    // This means that it's possible this barebones error extraction code could cause additional errors - if you encounter one,
    // please file an issue with an output example!
    $error = end($mail->responses)->body;
    $error_code = $error->Error->Code;
    $error_msg = substr($error->Error->Message, 0, strpos($error->Error->Message, "\n"));

    writeln('Error Code: ' . $error_code);
    writeln('Error Message: ' . $error_msg);

    if ( $error_code == 'SignatureDoesNotMatch' )
    {
      writeln();
      writeln("\t".'This error can mean a variety of things:');
      writeln();
      writeln("\t\t".'1. Your access key or secret key may be incorrect');
      writeln("\t\t".'2. Your system clock may be too far out of phase with atomic time (this can cause a signature verification issue)');
      writeln("\t\t".'3. The IAM credentials may not have sufficient privileges to send mail via SES');
      writeln();
    }

    exit(1);
}
