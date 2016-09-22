<?php
// Include the constant files
require_once 'libraries/db.php';
require_once 'libraries/session.php';

$session = new Session;

// If session not set redirect to index.php
if ( ! $session->is_user_authorized(TRUE, 'products', 'view')) {
    error_log_file('Unauthorized access. Session not set');
}

?>
<!DOCTYPE html>
<html lang="en">
    <!--head-->
    <head>
        <title>QuickSeller: Purchase History</title>  
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
            <h3>My Purchase History</h3>
            <table id="deals" class="cell-border">
                <thead>
                 <tr>
                    <th>Name</th>
                    <th>Category</th>
                    <th>Image</th>
                    <th>Amount</th>
                    <th>Description</th>
                    <th>Purchase Date</th>
                    <th>Seller</th>                
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

