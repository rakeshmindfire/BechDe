<?php
// Include the constant files
require_once 'libraries/db.php';
require_once 'libraries/session.php';

$session = new Session;

// If session not set redirect to index.php
if ( ! $session->is_user_authorized()) {
     error_log_file('Unauthorized access.');
}

//// Check if $_GET['product'] is set
//if ( ! isset($_GET['product'])) {
//    error_log_file('No product specified.');
//}
//
//// Check product is active and available
//$db = new dbOperation();
//$db->select('products_list', ['*'], ['id' => $_GET['product'], 'is_avail' => 1]);
//
//if ($db->num_rows_result === 0 ) {
//    error_log_file('Product not for sale.');
//}


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
        <div id="billing_info" class="col-md-offset-1 col-sm-2 pull-right">
                <h4>BILLING INFO </h4>
                <div id="amount_to_pay">
                Amount to pay : <span id="bill"></span><br>
                </div>
                <button id="confirm_purchase_button" type="button" class="btn-sm btn-success">Confirm purchase</button>
                
            </div>
        </div>
        </div>
        
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
        <div id="confirm_purchase_modal" class="modal fade" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                  <div class="modal-header" >
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Purchase Confirmation</h4>
                  </div>
                  <div class="modal-body">
                    <p>Are you sure you want to buy this product ?</p>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal" id="confirm_purchase">Confirm</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                  </div>
                </div>
              </div>
           </div>
        
        <script type="text/javascript">
            var no_image = "<?php echo NOIMAGE; ?>";
        </script>

        <?php require_once 'templates/footer.php'; ?>
    </body>
</html>
