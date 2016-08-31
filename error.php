<?php
require_once 'libraries/session.php';
$session = new Session;
?>
<h1>Some error occurred</h1><br>
<a href="<?php echo $session->validate_session() ? 'home.php': 'index.php' ?>"> GO HOME </a>