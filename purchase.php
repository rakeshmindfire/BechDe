<?php
// Include the constant files
require_once 'libraries/db.php';
require_once 'libraries/session.php';

$session = new Session;

// If session not set redirect to index.php
if ( ! $session->is_user_authorized(TRUE, 'deals', 'view')) {
     error_log_file('Unauthorized access.');
}
?>
<!DOCTYPE html>
<html lang="en">

    <head>
        <title>QuickSeller : Purchase</title>
        <?php
        require_once 'templates/header.php';
        ?>
    </head>

    <body >
        <!-- Include the navigation bar -->
        <?php require_once 'templates/show_nav.php'; ?>
        <div class="confirmation margin-top120" id="confirm_message"></div>
        <div class="container">
        <div class="container" id="items_to_be_purchased">
        <div id="billing_info" class="container col-md-offset-1 col-sm-2 pull-right">
            <div id="billing_bill">
            <h4>BILLING INFO </h4>
            <div id="amount_to_pay">
            Amount to pay : <span id="bill"></span><br>
            </div>
            <button id="confirm_purchase_button" type="button" class="btn-sm btn-success">Confirm purchase</button> 
            </div>
            <div id="twitter_check">
                <img src="img/twitter.png" id="twitter_img">Tweet about this<input type="checkbox" checked="true" id="post_tweet">
            </div>
        </div>
        </div>
        </div>
        <!--Modal for Seller Info-->
        <?php require_once 'templates/seller_info_modal.php';?>
        
        <div id="confirm_purchase_modal" class="modal fade" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                  <div class="modal-header" >
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Purchase Confirmation</h4>
                  </div>
                  <div class="modal-body">
                    <p>Are you sure you want to purchase ?</p>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal" id="confirm_purchase">Confirm</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                  </div>
                </div>
              </div>
           </div>
        
        <div id="processing" class="hide">
            <img src="img/ajax-loader.gif">
        </div>
       
        <script type="text/javascript">
            var no_image = "<?php echo NOIMAGE; ?>";
        </script>

        <?php require_once 'templates/footer.php'; ?>
    </body>
</html>
