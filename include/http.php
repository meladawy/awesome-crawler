<?php

/**
 * @file
 */

/**
 * Redirect user to a specific url.
 */
function redirect($url, $permanent = FALSE) {
  if ($permanent) {
    header('HTTP/1.1 301 Moved Permanently');
  }
  header('Location: ' . $url);
  exit();
}

/**
 * Get the current requested path (/contact, /about..etc)
 *
 * @return string path
 */
function request_path() {
  $request_uri = explode('/', trim($_SERVER['REQUEST_URI'], '/'));
  $script_name = explode('/', trim($_SERVER['SCRIPT_NAME'], '/'));
  $parts = array_diff_assoc($request_uri, $script_name);
  if (empty($parts)) {
    return '/';
  }
  $path = implode('/', $parts);
  if (($position = strpos($path, '?')) !== FALSE) {
    $path = substr($path, 0, $position);
  }
  return $path;
}
