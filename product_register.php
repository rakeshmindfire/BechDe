<?php 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Include the constant file
require_once 'helper/validation.php';
require_once 'config/constants.php';

$msg = '';
$is_update = FALSE;

// Connecting to DB
$conn = mysqli_connect(SERVERNAME, USERNAME, PASSWORD, DBNAME);

// Handled case if connection failed
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

if(isset($_GET['update_id']) ) {
    $is_update = TRUE;
    $sql_getvaluestoupdate="SELECT `category`,`name`,`amount`,`description` FROM `products_list` "
        . "WHERE `products_list`.`id` ='". $_GET['update_id']."'";
    $row_toupdate=mysqli_fetch_assoc(mysqli_query($conn, $sql_getvaluestoupdate));
    print_r ($row_toupdate);
} 

$sql_getcategory="SELECT `id`, `name` FROM `products_category`";
$categories = mysqli_query($conn, $sql_getcategory);

if (!empty($_POST)) {
    
    $pic_name='product_pic';
    
    //trim all whitespaces from string values
    foreach ($_POST as $key => $postvalues) {
        if (is_string($postvalues)) {
            $_POST[$key] = trim ($postvalues);
        }
    }
    $error = validate_data($_POST); 
    $error[$pic_name]= $is_update ? '': ($_FILES[$pic_name]['size']==0 ? 'Picture required'
            : image_check($pic_name));
    $fields_validated=TRUE;
    print_r($error);
    foreach ($error as $error_keys => $error_messages) {
   
        if( ! empty($error_messages)) {
            $fields_validated = FALSE;
            break;
        }
    }
    
    if (empty($error[$pic_name]) && $fields_validated){

        if(!$is_update) {
            $sql = "INSERT INTO `products_list` ( `category`,`user_id`, `name`, `amount`, "
                    . "`description`, `created_date`) VALUES "
                    . "( '"
                    . $_POST['category']
                    . "','1','"
                    . $_POST['product_name']
                    . "', '"
                    . $_POST['product_price']
                    . "', '"
                    . $_POST['description']
                    . "', NOW())";

        } else {
            $sql="UPDATE `products_list` SET `category` = '"
                . $_POST['category']
                . "', `name` = '"
                . $_POST['product_name']
                . "', `amount` = '"
                 . $_POST['product_price']
                . "', `description` = '"
                . $_POST['description']
                . "' WHERE `products_list`.`id` = "
                . $_GET['update_id'];
        }
        if ( ! mysqli_query($conn, $sql)) {
               $msg= "New record in PRODUCT_LIST FAILURE";
               echo 'err1-->'. mysqli_error($conn);
               header('Location: error.php');
              // exit;
        }

        $product_id = $is_update ? $_GET['update_id'] : mysqli_insert_id($conn);
        if($is_update && $_FILES[$pic_name]['size']!=0) {
           $sql_getimage="SELECT `image` from `products_list` WHERE `id`=".$_GET['update_id'];
           $imgtoupdate=mysqli_fetch_assoc(mysqli_query($conn, $sql_getimage));
           if(!is_null($imgtoupdate['image']) && file_exists(PRODUCT_PIC.$imgtoupdate['image'])) {
               unlink(PRODUCT_PIC.$imgtoupdate['image']);
           }
        }

        if(!empty($_FILES[$pic_name]))
        {

            $extension=(pathinfo( basename($_FILES[$pic_name]["name"]))['extension']);
            $file_name = PRODUCT_PIC . $product_id .'_'. time() . '.' . $extension;
            if (move_uploaded_file($_FILES[$pic_name]["tmp_name"], $file_name)) {

                $sql_putimage="UPDATE `products_list` SET `image`='"
                        . basename($file_name)
                        ."' WHERE `id`='".$product_id."'";

                if (! mysqli_query($conn, $sql_putimage)) {
                    $msg= "New record in PRODUCTS_LIST (image) FAILURE";
                    echo 'err1-->'. mysqli_error($conn);
                    header('Location: error.php');
                }
            }
        }
        $message= $is_update ?  2: 1;
        header("Location: product_list.php?success=$message");
    
    }
}

 mysqli_close($conn);

?>

<!DOCTYPE html>
<html lang="en">

    <!--head-->
    <head>
        <title>QuickSeller:<?php echo $is_update? 'Update ': 'Add '; ?> your Product</title>  
        <?php
           require_once 'templates/header.php';     
        ?>
    </head>


    <body >

        <!-- Include the navigation bar -->
        <?php require_once 'templates/navigation.php'; ?>

        <section>
            <h3><?php echo $msg; ?></h3>
        <div class="container">
          <h3><?php echo $is_update? 'update ': 'add '; ?>  your product ...</h3>
          <form class="form-horizontal" role="form" method="post" enctype="multipart/form-data" 
                action="product_register.php<?php echo $is_update ? '?update_id='.$_GET['update_id']:''; ?>">

            <div class="form-group">
              <label class="control-label col-sm-2" for="category">Category  <span class="color_remove">*</span></label>           

              <div class="col-sm-4">
                <select class="form-control " id="category" name="category" >  
                    <option value="" >Select Category</option>
                        <?php
                            while($row = mysqli_fetch_assoc($categories)) {                    
                                echo '<option value="'.$row['id'].'" ';
                                echo  ($is_update && $row['id'] == $row_toupdate['category'])
                                || (isset($_POST["category"]) && $_POST["category"] == $row['id'])
                                    ?'selected ':'';
                                echo    '>'.$row['name'].'</option>';
                            }         
                        ?>
                </select>
              </div>
              <div class="col-sm-4 error_msg"><?php echo isset($error['category']) ? $error['category'] : '';?> </div> 
            </div>

            <div class="form-group">
                <label class="control-label col-sm-2" for="product_name">Product Name <span class="color_remove">*</span></label>
                <div class="col-sm-2">
                    <input type="text" class="form-control" id="product_name" placeholder="Samsung 2360"
                       name="product_name" value="<?php echo  $is_update ? $row_toupdate['name']:
                           ((isset($_POST["product_name"])) ? $_POST["product_name"]:''); ?>">
                </div>
                <div class="col-sm-4 error_msg"><?php echo isset($error['product_name']) ? $error['product_name'] : '';?> </div> 
            </div>

            <div class="form-group">
                <label class="control-label col-sm-2" for="product_price">Price (INR) <span class="color_remove">*</span></label>
                <div class="col-sm-3">
                    <input type="number" step="0.01" min='0' class="form-control" id="product_price" 
                        placeholder="12324" name="product_price" value=
                        "<?php echo $is_update ? $row_toupdate['amount']: 
                            ((isset($_POST["product_price"])) ? $_POST["product_price"] : ''); ?>">
                </div>
                <div class="col-sm-4 error_msg"><?php echo isset($error['product_price']) ? $error['product_price'] : '';?> </div>
            </div>

            <div class="form-group">
                <label class="control-label col-sm-2" >Product Image
                    <?php 
                    echo $is_update?'':'<span class="color_remove">*</span>';
                    ?></label>
                 <div class="col-sm-3">
                    <input type="file" name="product_pic" id="product_pic" />
                </div>
                <div class="col-sm-4 error_msg"><?php echo isset($error['product_pic']) ? $error['product_pic'] : '';?> </div>
            </div>

            <div class="form-group">
                <label class="control-label col-sm-2" for="description">Description:</label>
                <div class="col-sm-5">
                    <textarea class="form-control" rows="5" id="description" 
                            placeholder="Describe the product..." name="description" ><?php
                            if($is_update) {
                                echo $row_toupdate['description'];
                            } else {
                                  echo isset($_POST["description"]) ? $_POST["description"]:'';
                            }?></textarea>
                </div>
            </div> 

            <div class="form-group">
              <div class="col-sm-offset-2 col-sm-1">
                  <button type="submit" class="btn btn-default btn-lg btn-success">
                      <?php echo $is_update?'Update': 'Add'; ?></button>
              </div>
              <div class="col-sm-offset-1 col-sm-1">
                  <button type="reset" class="btn btn-default btn-lg btn-danger">Clear</button>
              </div>
            </div>
          </form>
        </div>
        </section>   

        <!-- jQuery -->
        <script src="vendor/jquery/jquery.min.js"></script>

        <!-- Bootstrap Core JavaScript -->
        <script src="vendor/bootstrap/js/bootstrap.min.js"></script>
    </body>

</html>
