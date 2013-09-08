#!/usr/bin/env php
<?php

require_once __DIR__ . '/../bootstrap.php';

use GetOptionKit\GetOptionKit;
use Aws\Ses\SesClient;

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
else if ( count( $arguments ) == 1 )
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

$ses = SesClient::factory(array(
    'key' => $settings['access_key'],
    'secret' => $settings['secret_key'],
    'region' => 'us-east-1',
));

$mail = new Beryllium\Mailstrom\SesMail($settings, $ses);

$result = $mail->send();

if (true === $result['status']) {
    writeln('Sent!');

    exit(0);
} else {
    writeln('Failed!');

    $error_code = $result['exception']->getCode();
    writeln('Error Code: ' . $error_code );
    writeln('Error Message: ' . $result['exception']->getMessage() );

    switch ( $error_code )
    {
    case 0:
    default:
      writeln();
      writeln("\t".'This error can mean a variety of things:');
      writeln();
      writeln("\t\t".'1. Your access key or secret key may be incorrect');
      writeln("\t\t".'2. The IAM credentials may not have sufficient privileges to send mail via SES');
      writeln("\t\t".'3. Your system clock may be too far out of phase with atomic time (this can cause a signature verification issue)');
      writeln();
      break;
    }

    exit(1);
}
