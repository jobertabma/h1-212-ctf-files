<?php

ob_start();

if(!isset($_COOKIE['admin'])) {
  setcookie('admin', 'no');
}

if($_COOKIE['admin'] !== 'yes') {
  die();
}

if($_SERVER['REQUEST_METHOD'] !== 'POST') {
  header('HTTP/1.1 405 Method Not Allowed');
  die();
}

if($_SERVER['CONTENT_TYPE'] != 'application/json') {
  header('HTTP/1.1 406 Not Acceptable');
  die();
}

$input = file_get_contents('php://input');

$data = @json_decode($input, true);

if(!is_array($data)) {
  header('HTTP/1.1 418 I\'m a teapot');
  header('Content-Type: application/json');
  die(json_encode(array('error' => array('body' => 'unable to decode'))));
}

if(!isset($data['domain'])) {
  header('HTTP/1.1 418 I\'m a teapot');
  header('Content-Type: application/json');
  die(json_encode(array('error' => array('domain' => 'required'))));
}

if(!is_string($data['domain'])) {
  header('HTTP/1.1 418 I\'m a teapot');
  header('Content-Type: application/json');
  die(json_encode(array('error' => array('domain' => 'incorrect type, string expected'))));
}

if(substr($data['domain'], -4) !== '.com') {
  header('HTTP/1.1 418 I\'m a teapot');
  header('Content-Type: application/json');
  die(json_encode(array('error' => array('domain' => 'incorrect value, .com domain expected'))));
}

$domain_parts = explode('.', $data['domain']);

if(count($domain_parts) < 3) {
  header('HTTP/1.1 418 I\'m a teapot');
  header('Content-Type: application/json');
  die(json_encode(array('error' => array('domain' => 'incorrect value, .com domain expected'))));
}

if(strpos($domain_parts[0], '212') === false) {
  header('HTTP/1.1 418 I\'m a teapot');
  header('Content-Type: application/json');
  die(json_encode(array('error' => array('domain' => 'incorrect value, sub domain should contain 212'))));
}

if(strpos($data['domain'], '?') !== false) {
  header('HTTP/1.1 418 I\'m a teapot');
  header('Content-Type: application/json');
  die(json_encode(array('error' => array('domain' => 'domain cannot contain ?'))));
}

if(strpos($data['domain'], '&') !== false) {
  header('HTTP/1.1 418 I\'m a teapot');
  header('Content-Type: application/json');
  die(json_encode(array('error' => array('domain' => 'domain cannot contain &'))));
}

if(strpos($data['domain'], '\\') !== false) {
  header('HTTP/1.1 418 I\'m a teapot');
  header('Content-Type: application/json');
  die(json_encode(array('error' => array('domain' => 'domain cannot contain \\'))));
}

if(strpos($data['domain'], '%') !== false) {
  header('HTTP/1.1 418 I\'m a teapot');
  header('Content-Type: application/json');
  die(json_encode(array('error' => array('domain' => 'domain cannot contain %'))));
}

if(strpos($data['domain'], '#') !== false) {
  header('HTTP/1.1 418 I\'m a teapot');
  header('Content-Type: application/json');
  die(json_encode(array('error' => array('domain' => 'domain cannot contain #'))));
}

$file_name = '/tmp/' . $_SERVER['REMOTE_ADDR'] . '.txt';

if(file_exists($file_name)) {
  $contents = file_get_contents($file_name);
} else {
  $contents = '';
}

$new_contents = $contents . $data['domain'] . "\n";
$line_count = explode("\n", $new_contents);

file_put_contents($file_name, $new_contents);

echo json_encode(array('next' => '/read.php?id=' . (count($line_count) - 2)));
