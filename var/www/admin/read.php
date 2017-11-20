<?php

ob_start();

require_once('http_fetcher.php');

if(!isset($_COOKIE['admin'])) {
  setcookie('admin', 'no');
}

if($_COOKIE['admin'] !== 'yes') {
  die();
}

if($_SERVER['REQUEST_METHOD'] !== 'GET') {
  header('HTTP/1.1 405 Method Not Allowed');
  die();
}

$row = $_GET['id'];

if(!isset($row) || !is_numeric($row)) {
  header('HTTP/1.1 418 I\'m a teapot');
  header('Content-Type: application/json');
  die(json_encode(array('error' => array('row' => 'incorrect type, number expected'))));
}

$file_name = '/tmp/' . $_SERVER['REMOTE_ADDR'] . '.txt';

if(!file_exists($file_name)) {
  header('HTTP/1.1 404 Not Found');
  header('Content-Type: application/json');
  die(json_encode(array('error' => array('row' => 'incorrect row'))));
}

$contents = @file_get_contents($file_name);

$domains = explode("\n", $contents);

if(!isset($domains[$row])) {
  header('HTTP/1.1 404 Not Found');
  header('Content-Type: application/json');
  die(json_encode(array('error' => array('row' => 'incorrect row'))));
}

$domain = $domains[$row];

$fetcher = new HttpRequest('http://' . $domain);
$data = $fetcher->DownloadToString();

die(json_encode(array('data' => base64_encode($data))));
