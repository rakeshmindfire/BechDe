<?php
// Include the constant files
require_once 'libraries/session.php';
$session = new Session;

// If session not set redirect to index.php
if ( ! $session->check_session()) {
    header('Location:index.php');
}

$session->sign_out();
header('Location: index.php');

