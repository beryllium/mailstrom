<?php

if (php_sapi_name() != 'cli') {
    die('Must run from command line');
}

error_reporting(E_ALL | E_STRICT);
ini_set('display_errors', 1);
ini_set('log_errors', 0);
ini_set('html_errors', 0);

define('MAILSTROM_GLOBAL_INI', '/etc/mailstrom.ini');
define('MAILSTROM_LOCAL_INI', $_SERVER['HOME'] . '/.mailstrom.ini');

$settings = array('type'=>'ses');

require_once __DIR__ . '/vendor/autoload.php';

// Begin building the configuration & argument array
// 1. Include global settings
if (file_exists(MAILSTROM_GLOBAL_INI)) {
    $global_ini = parse_ini_file(MAILSTROM_GLOBAL_INI);
    if (is_array($global_ini)) {
        $settings = array_merge($settings, $global_ini);
    }
}

// 2. Override with local settings (if any)
if (file_exists(MAILSTROM_LOCAL_INI)) {
    $local_ini = parse_ini_file(MAILSTROM_LOCAL_INI);
    if (is_array($local_ini)) {
        $settings = array_merge($settings, $local_ini);
    }
}

function writeln( $msg = '' )
{
  echo $msg . "\n";
}

