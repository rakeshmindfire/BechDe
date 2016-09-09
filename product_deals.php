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
            <a type="button" class="btn btn-lg pull-right btn-primary" id="checkout_button" href="purchase.php">CHECKOUT</a>
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
        <!--Modal for Image zoom-->
        <div id="my_modal_image" class="modal fade" role="dialog">
            <div class="modal-dialog">
                <div class="modal-body" >
                    <img class="img-thumbnail" class="show-zoomed" id="zoomed_image">
                    <div class="clearfix">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
        <!--Modal for Seller Info-->
        <div id="seller_info_modal" class="modal fade" role="dialog">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                <div class="modal-header">
                    
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Seller Info </h4>
                </div>
                <div class="modal-body" >
                    <div class="row">
                        <img id="seller_image" class="col-xs-2 img-rounded">
                        <div class="col-md-9 text-justify"> 
                            <div >
                                <b >Name : </b><span id="seller_name"></span>
                            </div>
                            <div >
                                <b >Sex : </b><span id="seller_sex"></span>
                            </div>
                            <div >
                                <b >DOB : </b><span id="seller_dob"></span>
                            </div>
                            <div >
                                <b >Mobile : </b><span id="seller_mob"></span>
                            </div>
                            <div >
                                <b>About : </b><span id="seller_bio"></span>
                            </div>
                            <div >
                                <b>Preferred communication : </b><span id="seller_prefcomm"></span>
                            </div>
                            <div >
                                <b>Office Address : </b><span id="seller_addr"></span>
                            </div>  
                            <div >
                                <b>Email : </b><span id="seller_email"></span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
            </div>
        </div>
        
        <script type="text/javascript">
            var page_size = <?php echo PER_PAGE_RECORD; ?>;
            var user_role = <?php echo $_SESSION['role']; ?>;
            var no_image = "<?php echo NOIMAGE; ?>";
               
        </script>
        <?php require_once 'templates/footer.php'; ?>
    </body>
</html>

