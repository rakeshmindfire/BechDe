<!-- Navigation -->
<nav id="main-nav" class="navbar navbar-default navbar-custom navbar-fixed-top">
    <div class="container">
        <!-- Brand and toggle get grouped for better mobile display -->
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                <span class="sr-only">Toggle navigation</span> Menu
            </button>
            <a class="navbar-brand" href="home.php">QuickSeller</a>
        </div>

        <!-- Collect the nav links, forms, and other content for toggling -->
        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
            <ul class="nav navbar-nav navbar-right">
                <li class="hidden">
                    <a href="home.php"></a>
                </li>
                <li>
                    <a class="" href="product_list.php">My Product List</a>
                </li>
                <li>
                    <a class="" href="my_profile.php">My Profile</a>
                </li>
                <li>
                    <a class="" href="product_register.php">SELL NOW!</a>
                </li>
                <li>
                    <a class="" href="logout.php">Log out</a>
                </li>
            </ul>
        </div>
        <!-- /.navbar-collapse -->
    </div>
    <!-- /.container-fluid -->

    <div class="container nav-name"><?php echo 'Welcome, '.$_SESSION['name'].' ( ' 
            .$_SESSION['role_name'] . ' )';?></div>
</nav>


