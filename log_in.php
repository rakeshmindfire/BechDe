<?php 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'dbconstants.php';

?>


<!DOCTYPE html>
<html lang="en">

    <!--head-->
    <head>
        <title>QuickSeller:Log in</title>  
        <?php
           require_once 'templates/header.php';     
        ?>
    </head>


    <body >

    <!-- Include the navigation bar -->
    <?php require_once 'templates/navigation.php'; ?>
        <section>
            <div class="container">
                  <h3>enter log in details</h3>
                  <form class="form-horizontal" role="form" method="post" action="log_in.php">

                    <div class="form-group">
                      <label class="control-label col-sm-2" for="email_id">E-mail id:</label>  

                        <div class="col-sm-2">
                            <input type="text" class="form-control" id="email_id" placeholder="xyz@abc.com"
                               name="email_id">
                        </div>
                    </div>
                     <div class="form-group">
                      <label class="control-label col-sm-2" for="password">Password:</label>  

                        <div class="col-sm-2">
                            <input type="password" class="form-control" id="password" placeholder="*****"
                               name="email_id">
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-offset-2 col-sm-1">
                            <button type="submit" class="btn btn-default btn-lg btn-success">Log in</button>
                        </div>
                        <div class="col-sm-offset-1 col-sm-1">
                            <button type="reset" class="btn btn-default btn-lg btn-danger">Clear</button>
                        </div>
                    </div>
                  </form>
            </div>
        </section>
    
    </body>
</html>

