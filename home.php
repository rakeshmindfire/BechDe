<?php
// Include the constant files
require_once 'libraries/db.php';
require_once 'libraries/session.php';

$session = new Session;

// If session not set redirect to index.php
if ( ! $session->check_session()) {
     error_log_file('Unauthorized access. Session not set in home page');
}

// Get full name of user from database and add to $_SESSION
$db = new dbOperation;
$db->select('users', ['first_name','middle_name','last_name'], ['id'=>$_SESSION['id']]);
$_SESSION = array_merge($_SESSION, $db->fetch());
?>
<!DOCTYPE html>
<html lang="en">

    <head>
        <title>QuickSeller : User HomePage</title>
        <?php
        require_once 'templates/header.php';
        ?>
    </head>

    <body >
        <!-- Include the navigation bar -->
        <?php require_once 'templates/show_nav.php'; ?>

        <!-- Header -->
        <header>
            <div class="container">
                <div class="intro-text">
                    <div class="intro-lead-in">Welcome To QuickSeller</div>
                    <div class="intro-heading"><?php echo $_SESSION['first_name'].' '.$_SESSION['middle_name'].' '.$_SESSION['last_name'];?></div>
                </div>
            </div>
        </header>

        <section id="services" class="margin-top120">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12 text-center">
                        <h2 class="section-heading">Services</h2>
                    </div>
                </div>
                <div class="row text-center">
                    <div class="col-md-4">
                        <span class="fa-stack fa-4x">
                            <i class="fa fa-circle fa-stack-2x text-primary"></i>
                            <i class="fa fa-shopping-cart fa-stack-1x fa-inverse"></i>
                        </span>
                        <h4 class="service-heading">Service1</h4>
                        <p class="text-muted">Detail of Service</p>
                    </div>
                    <div class="col-md-4">
                        <span class="fa-stack fa-4x">
                            <i class="fa fa-circle fa-stack-2x text-primary"></i>
                            <i class="fa fa-laptop fa-stack-1x fa-inverse"></i>
                        </span>
                        <h4 class="service-heading">Service2</h4>
                        <p class="text-muted">Detail of Service</p>
                    </div>
                    <div class="col-md-4">
                        <span class="fa-stack fa-4x">
                            <i class="fa fa-circle fa-stack-2x text-primary"></i>
                            <i class="fa fa-lock fa-stack-1x fa-inverse"></i>
                        </span>
                        <h4 class="service-heading">Service3</h4>
                        <p class="text-muted">Detail of Service</p>
                    </div>
                </div>
            </div>
        </section>

        <?php require_once 'templates/footer.php'; ?>
    </body>
</html>
