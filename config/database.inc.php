<?php
// Parsing settings.ini file

/**
 * Creating the connection to DB
 */

// parses the settings file
$settings = parse_ini_file('settings.ini', true);
// starts the connection to the database
$dbh = new PDO(
  sprintf(
    "%s:host=%s;dbname=%s",
    $settings['driver'],
    $settings['host'],
    $settings['dbname']
  ),
  $settings['user'],
  $settings['password']
);
