<?php
require_once 'libraries/db.php';
require_once 'libraries/encrypt_decrypt.php';
require_once 'libraries/session.php';

$session = new Session;

// If session not set 
if ( ! $session->is_user_authorized()) {
    error_log_file('Unauthorized access. Session not set in activation.php', TRUE);
    exit;
}

$fetch_id = simple_decrypt($_GET['id']);

// If fetch id is not an integer then show failure
if ( ! is_int($fetch_id)) {
    error_log_file('Invalid user id');
}

$db = new dbOperation;
$db->insert_or_update(2, 'login', ['is_activated'=> '1'], ['user_id'=>$fetch_id]); 
header('Location: login.php?success=2');
?>