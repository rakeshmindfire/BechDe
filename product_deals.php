<?php
// Include the constant files
require_once 'libraries/db.php';
require_once 'libraries/session.php';

$session = new Session;

// If session not set redirect to index.php
if ( ! $session->is_user_authorized(TRUE, 'deals', 'view')) {
    error_log_file('Unauthorized access. Session not set');
}

?>
<!DOCTYPE html>
<html lang="en">
    <!--head-->
    <head>
        <title>QuickSeller:Product Deals</title>  
<?php
require_once 'templates/header.php';
?>
    </head>
    <body >

        <!-- Include the navigation bar -->
<?php require_once 'templates/show_nav.php'; ?>
    <div class="confirmation margin-top120" id="confirm_message">
        </div>
        <div class="container">
            <h3 class="col-md-10">Deals of the day</h3>
            <a type="button" class="btn btn-lg pull-right btn-primary"
               id="checkout_button" href="purchase.php"><span class="glyphicon glyphicon-shopping-cart"></span><span id="items_in_cart"></span></a>
            <table id="deals" class="cell-border">
                <thead>
                 <tr>
                    <th>Name</th>
                    <th>Category</th>
                    <th>Image</th>
                    <th>Amount</th>
                    <th>Description</th>
                    <th>Uploaded on</th>
                     <th>Actions</th>                
                 </tr>
                </thead>
            </table>
        </div>
        <!--Modal for Image Zoom-->
        <?php require_once 'templates/image_modal.php';?> 
        <!--Modal for Seller Info-->
        <?php require_once 'templates/seller_info_modal.php';?>
        
        <script type="text/javascript">
            var page_size = <?php echo PER_PAGE_RECORD; ?>;
            var user_role = <?php echo $_SESSION['role']; ?>;
            var no_image = "<?php echo NOIMAGE; ?>";
               
        </script>
        <?php require_once 'templates/footer.php'; ?>
    </body>
</html>

