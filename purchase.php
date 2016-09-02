<?php
// Include the constant files
require_once 'libraries/db.php';
require_once 'libraries/session.php';

$session = new Session;

// If session not set redirect to index.php
if ( ! $session->is_user_authorized()) {
     error_log_file('Unauthorized access.');
}


//
//check product is active and available
//

?>
<!DOCTYPE html>
<html lang="en">

    <head>
        <title>QuickSeller : Purchase</title>
        <?php
        require_once 'templates/header.php';
        ?>
    </head>

    <body >
        <!-- Include the navigation bar -->
        <?php require_once 'templates/show_nav.php'; ?>
        <div class="confirmation margin-top120" id="confirm_message"></div>
        <div class="container">
            <div class>
        </div>

        <?php require_once 'templates/footer.php'; ?>
    </body>
</html>
