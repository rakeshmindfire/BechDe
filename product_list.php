<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once 'config/constants.php';

// Connecting to DB
$conn = mysqli_connect(SERVERNAME, USERNAME, PASSWORD, DBNAME);

// Handled case if connection failed
if ( ! $conn) {
    die("Connection failed: " . mysqli_connect_error());
}

if (isset($_GET['delete_id'])) {
    $sql_get_image = "SELECT `image` FROM `products_list`"
            . " WHERE `products_list`.`id` ='" . $_GET['delete_id'] . "'";
    $image_to_delete = mysqli_query($conn, $sql_get_image);
    
    if ( ! empty(mysqli_num_rows($image_to_delete))) {
        $image_to_delete = mysqli_fetch_assoc($image_to_delete);
        unlink(PRODUCT_PIC . $image_to_delete['image']);
    }
    $sql_delete = "DELETE FROM `products_list` WHERE `products_list`.`id` ='" . $_GET['delete_id'] . "'";
    mysqli_query($conn, $sql_delete);
}

$sql_get_products = "SELECT pl.id,pc.name as category_name,pl.image,pl.name as product_name,pl.amount,"
        . "pl.description,pl.created_date"
        . " FROM products_list pl JOIN products_category pc ON pl.category=pc.id "
        . "ORDER BY pl.created_date DESC";

$products = mysqli_query($conn, $sql_get_products);
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
<?php require_once 'templates/navigation.php'; ?>

        <div class="confirmation margin-top120">
<?php
if (isset($_GET['success'])) {
    switch ($_GET['success']) {
        case 1:
            echo "Product registered successfully!";
            break;

        case 2:
            echo "Product updated successfully!";
            break;

        case 3:
            echo "Product deleted successfully!";
            break;
    }
}
?>
        </div>
        <div class="container table-responsive" >
            <?php if (mysqli_num_rows($products) > 0) { ?>
                <h2>Your Products</h2>
                <table class="table table-bordered table-condensed" >
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
                        <?php while ($row = mysqli_fetch_assoc($products)) { ?>                
                            <tr> 
                                <td><?php echo$row['category_name']; ?></td>
                                <td>
                                    <img src="<?php
                    echo (( ! is_null($row['image']) && file_exists(PRODUCT_PIC . $row['image'])) ? PRODUCT_PIC . $row['image'] : NOIMAGE);
                    ?>" data-toggle="modal" data-target="#myModal<?php echo $row['id'] ?>">
                                    <div id="myModal<?php echo $row['id'] ?>" class="modal fade" role="dialog">
                                        <div class="modal-dialog">

                                            <!-- Modal content-->

                                            <div class="modal-body" >
                                                <img src="<?php
                                 echo (( ! is_null($row['image']) && file_exists(PRODUCT_PIC . $row['image'])) ? PRODUCT_PIC . $row['image'] : NOIMAGE);
                                 ?>" 
                                                     class="img-thumbnail" class="show-zoomed">
                                                <div class="clearfix">
                                                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td><?php echo $row['product_name']; ?></td>
                                <td><?php echo $row['amount']; ?></td>
                                <td><?php echo $row['description']; ?> </td>
                                <td><?php echo $row['created_date']; ?></td>
                                <td><a href="product_register.php?update_id=<?php echo $row['id']; ?>"
                                       class="glyphicon glyphicon-pencil color-edit">&nbsp;
                                        <a href="product_list.php?delete_id=<?php echo $row['id']; ?>&success=3"
                                           class=" glyphicon glyphicon-remove color-remove"></a>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            <?php } else { ?>
                <h2>No Products Found!! </h2><br><h4>Please add product <a href="product_register.php"> click now</a></h4>
            <?php } ?>
        </div>
        <?php require_once 'templates/footer.php'; ?>
    </body>
</html>

