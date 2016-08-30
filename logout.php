<?php
// Include the constant files
require_once 'libraries/session.php';
$session = new Session;

// If session not set redirect to index.php
if ( ! $session->check_session()) {
 error_log_file('Unauthorized access. Session not set');
}

$session->sign_out();
header('Location: index.php');

