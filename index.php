<!DOCTYPE html>
<html lang="en">

    <head>
        <title>QuickSeller : Home</title>
        <?php
        require_once 'templates/header.php';
        ?>
    </head>

    <body >
        <!-- Include the navigation bar -->
        <?php require_once 'templates/navigation.php'; ?>

        <!-- Header -->
        <header>
            <div class="container">
                <div class="intro-text">
                    <div class="intro-lead-in">Welcome To QuickSeller</div>
                    <div class="intro-heading">1,2,3 and its sold</div>
                    <a href="#" class=" btn btn-xl">Log in</a>
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
