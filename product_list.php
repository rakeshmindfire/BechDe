<?php
// Include the constant files
require_once 'libraries/db.php';
require_once 'libraries/session.php';

$session = new Session;

// If session not set redirect to index.php
if ( ! $session->check_session()) {
    header('Location:index.php');
}
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
    <body >

        <!-- Include the navigation bar -->
<?php require_once 'templates/seller_navigation.php'; ?>
        <div class="confirmation margin-top120" id="confirm_message">
<?php
if (isset($_GET['success'])) {
    switch ($_GET['success']) {
        case '1':
            echo "Product registered successfully!";
            break;

        case '2':
            echo "Product updated successfully!";
            break;
        
        default:
            error_log_file('Wrong URL');
            break;
    }
}
?>
        </div>
        <div id="loader_div"><img src="img/ajax-loader.gif" id="loader_image"></div>
        <div class="container table-responsive" >
            <div class="col-sm-8" ><h2 id='my_products'>My Products</h2></div>
            <div class="col-sm-4" id='search_category'>Category
                <select id='search'>
                </select>
                <button class='btn btn-default btn-sm' id="search_button">Search</button>
            </div>
            
                <table class="table table-bordered table-condensed" id='products_table' >
                    <thead>
                        <tr>
                            <th>Category</th>
                            <th>Image</th>
                            <th>Name</th>
                            <th>Amount</th>
                            <th>Description</th>
                            <th>Uploaded on</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        
                    </tbody>
                </table>
            <div id='no_data'>
            <h2 class="hide">No Products Found!! </h2><br><h4>To add product <a href="product_register.php"> click now</a></h4>
            </div>
            
            <!--Modal for delete confirmation-->
            <div id="myModalDelete" class="modal fade" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                  <div class="modal-header" >
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Delete Confirmation</h4>
                  </div>
                  <div class="modal-body">
                    <p>Are you sure you want to delete this product ?</p>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal" id="confirm_delete">Confirm</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                  </div>
                </div>

              </div>
           </div>
            
            <!--Modal for Image zoom-->
            <div id="myModalImage" class="modal fade" role="dialog">
                <div class="modal-dialog">
                    <div class="modal-body" >
                        <img class="img-thumbnail" class="show-zoomed" id="zoomed_image">
                        <div class="clearfix">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php require_once 'templates/footer.php'; ?>
    </body>
</html>

