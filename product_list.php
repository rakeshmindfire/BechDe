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
        <title>QuickSeller:Products List</title>  
<?php
require_once 'templates/header.php';
?>
    </head>
    <body >

        <!-- Include the navigation bar -->
<?php require_once 'templates/show_nav.php'; ?>
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
            <div class="col-sm-4" ><h2 id='my_products'>My Products</h2></div>
            <div class="col-md-offset-1 col-sm-4" id='search_category'>Category
                <select id='search'>
                </select>
                <button class='btn btn-default btn-sm' id="search_button">Search</button>
            </div>
            <div class="col-md-offset-1 col-sm-2">
                <ul class="nav nav-tabs" id="status_tab">
                  <li class="active"><a href="#" data-value="1">Active</a></li>
                  <li><a href="#" data-value="0">Inactive</a></li>
                  <li><a href="#" data-value="3">Sold</a></li>
                </ul>
            </div>
            <div class="clearfix"></div>
                <table class="table table-bordered table-condensed" id='products_table' >
                    <thead>
                        <tr>
                            <th>Category</th>
                            <th>Image</th>
                            <th>Name</th>
                            <th>
                                <a class="glyphicon glyphicon-chevron-up" id="sorting-arrow-up"></a>&nbsp;Amount&nbsp;
                                <a class="glyphicon glyphicon-chevron-down" id="sorting-arrow-down"></a>
                            </th>
                            <th>Description</th>
                            <?php 
                            echo isset($_SESSION['role']) && $_SESSION['role']==='1' ? '<th>Seller</th>':'';
                            ?>
                            <th>Uploaded on</th>                            
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody></tbody>                    
                </table>
            <div id="pagination_div">
                <ul class="pagination" id="product_pagination">                    
                </ul>
            </div>
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
        <script type="text/javascript">
            var page_size = <?php echo PER_PAGE_RECORD; ?>;
            var user_role = <?php echo $_SESSION['role']; ?>
        </script>
        <?php require_once 'templates/footer.php'; ?>
    </body>
</html>

