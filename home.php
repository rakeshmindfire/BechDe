<?php
// Include the constant files
require_once 'libraries/db.php';
require_once 'libraries/session.php';
require_once 'libraries/twitter.php';

$session = new Session;

// If session not set redirect to index.php
if ( ! $session->is_user_authorized()) {
     error_log_file('Unauthorized access.');
}

$db = new dbOperation();
$db->select('role',['name'],['id' => $_SESSION['role']]);
$role_name = $db->fetch();
$_SESSION['role_name'] = $role_name['name'];
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
                    <div class="intro-heading"><?php echo $_SESSION['name'];?></div>
                </div>
            </div>
        </header>
        <section id="tweet_section" class="margin-top120">
            <div class="row">
                <div class="col-lg-12 text-center">
                    <h2 class="section-heading">MY RECENT TWEETS</h2>
                </div>
            </div>
            <div class="container well" id = "recent_tweet_space">       <?php 
                $tweets = json_decode(get_tweets());
                
                foreach ($tweets as $t) {
                ?> 
                <div class = "recent-tweets">
                    <span class="tweet-text"><?php echo $t->text; ?></span><br>
                    <span class="tweet-icon glyphicon glyphicon-time"></span><span class="tweet-date"><?php echo str_replace(' ', '-', substr($t->created_at, 0, 10)); ?></span>
                    <span class="tweet-icon glyphicon glyphicon-retweet"></span><span class="tweet-retweet"><?php echo $t->retweet_count; ?></span>
                    <span class="tweet-icon glyphicon glyphicon-heart"></span><span class="tweet-fav"><?php echo $t->favorite_count; ?></span>
                </div>
                    <hr>
                
          <?php }?>
            </div>
        </section>
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
        <script type="text/javascript">
           document.cookie = 'username = <?php echo $_SESSION['email']; ?>';
        </script>
        <?php require_once 'templates/footer.php'; ?>
    </body>
</html>
