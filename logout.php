<?php
// Include the constant files
require_once 'libraries/session.php';
$session = new Session;

$session->sign_out();
?>

<!-- Clear cookies-->
<script src="libraries/cookie_clear.js"></script>
<script>
    delete_cookies();
    window.location='index.php';
</script>

