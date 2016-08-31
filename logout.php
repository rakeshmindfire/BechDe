<?php
// Include the constant files
require_once 'libraries/session.php';
$session = new Session;

$session->sign_out();
header('Location: index.php');

