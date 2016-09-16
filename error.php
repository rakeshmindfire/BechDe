<?php
require_once 'libraries/session.php';
$session = new Session;
?>
<!DOCTYPE html>
<html lang="en">

    <head>
        <title>QuickSeller : ERROR</title>
        <?php
        require_once 'templates/header.php';
        ?>
    </head>
    <body class="container jumbotron">
        <h1>Some Internal Error occurred</h1><br>
        <a href="<?php echo $session->validate_session() ? 'home.php': 'index.php' ?>" id="danger"> GO HOME </a>
    </body>
</html>