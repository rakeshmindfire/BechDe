<?php 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

?>
<!DOCTYPE html>
<html lang="en">
    <!--head-->
    <head>
        <title>QuickSeller:Products List</title>  
        <?php
           require_once 'templates/header.php';     
        ?>
    </head>


<body id="sign_up">

    <!-- Include the navigation bar -->
    <?php require_once 'templates/navigation.php'; ?>

    <section>
        <?php if(isset($_GET['success']) && '1' === $_GET['success']) {?>
            <div class='alert-success'> Product registration Successful ! </div>
        <?php } ?>
    </section>
    
</body>
</html>

