<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once 'config/constants.php';
require_once 'libraries/db.php';


$db = new dbOperation;

if (isset($_GET['delete_id'])) {
  
    $db->select('products_list', ['image'],['id'=>$_GET['delete_id']]);
    $image_to_delete = $db->fetch();         
    
    if( ! is_null($image_to_delete['image']) && file_exists(PRODUCT_PIC.$image_to_delete['image'])) {
        unlink(PRODUCT_PIC.$image_to_delete['image']);
    }
    $db->delete('products_list', ['id'=>$_GET['delete_id']]);
}
$db->select('products_list pl JOIN products_category pc ON pl.category=pc.id' ,
    ['pl.id', 'pc.name as category_name', 'pl.image', 'pl.name as product_name', 'pl.amount', 'pl.description', 'pl.created_date'],
    NULL, ['pl.created_date', 'DESC'])
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
            <?php if ($db->num_rows_result > 0) { ?>
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
                        <?php while ($row = $db->fetch()) { ?>                
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

