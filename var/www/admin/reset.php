<?php

ob_start();

@unlink('/tmp/' . $_SERVER['REMOTE_ADDR'] . '.txt');

echo json_encode(array('result' => 'ok'));
