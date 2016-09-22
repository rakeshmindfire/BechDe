<?php
// Include the constant files
require_once 'libraries/db.php';
require_once 'libraries/session.php';

$session = new Session;

// If session not set redirect to index.php
if ( ! $session->is_user_authorized(FALSE)) {
    error_log_file('Unauthorized access. Session not set');
}

?>

<!DOCTYPE html>
<html lang="en">
    <!--head-->
    <head>
        <title>QuickSeller: Permissions</title>  
<?php
require_once 'templates/header.php';
?>
    </head>
    <body >

        <!-- Include the navigation bar -->
<?php require_once 'templates/show_nav.php'; ?>
        <div class='confirmation margin-top120'></div>
        
        <section>
            <div class="container">
                <h3>Permissions</h3>            
            <ul class="nav nav-tabs" id="role_tab"></ul>

            <div class="form-group" id="permissions_div"></div>
                    
                    <div class="form-group">
                        <div class=" col-sm-9"><p class="bg-success hide" id="saved">Permissions saved</p></div>
                        <div class=" col-sm-1">
                            <button class="btn btn-default btn-lg btn-success" id="submit_permissions">Submit</button>
                        </div>
                     
                        <div class="col-sm-offset-1 col-sm-1">
                            <button class="btn btn-default btn-lg btn-danger" id="reset_permissions">Reset</button>
                        </div>
                    </div>                
            </div>
        </section>
        <?php require_once 'templates/footer.php'; ?>
    </body>
</html>
