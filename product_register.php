<?php 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Include the constant file
require_once 'dbconstants.php';
require_once 'image_check.php';
require_once 'img_directories.php';

//print_r($_FILES['product_pic']);
$msg = '';

// Connecting to DB
$conn = mysqli_connect(SERVERNAME, USERNAME, PASSWORD, DBNAME);

// Handled case if connection failed
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$sql_getcategory="SELECT `id`, `name` FROM `products_category`";
$categories = mysqli_query($conn, $sql_getcategory);

if (!empty($_POST)) {
    
    $pic_name='product_pic';
    $uploadOk= image_check($pic_name);
        if($uploadOk){

        $sql = "INSERT INTO `products_list` ( `category`,`user_id`, `name`, `amount`, "
                . "`description`, `created_date`) VALUES "
                . "( '"
                . $_POST['category']
                . "','33','"
                . $_POST['product_name']
                . "', '"
                . $_POST['product_price']
                . "', '"
                . $_POST['description']
                . "', NOW())";


        if (! mysqli_query($conn, $sql)) {
               $msg= "New record in PRODUCT_LIST FAILURE";
               echo 'err1-->'. mysqli_error($conn);
               header('Location: error.php');
              // exit;
        }
        
        $product_id = mysqli_insert_id($conn);
     
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

        
    header('Location: product_list.php?success=1');
    }
}

 mysqli_close($conn);

?>

<!DOCTYPE html>
<html lang="en">

    <!--head-->
    <head>
        <title>QuickSeller:Add your Product</title>  
        <?php
           require_once 'templates/header.php';     
        ?>
    </head>


<body id="sign_up">

    <!-- Include the navigation bar -->
    <?php require_once 'templates/navigation.php'; ?>

    <section>
        <h3><?php echo $msg; ?></h3>
    <div class="container">
      <h3>Add your product ...</h3>
      <form class="form-horizontal" role="form" method="post" enctype="multipart/form-data" action="product_register.php">
              
        <div class="form-group">
          <label class="control-label col-sm-2" for="category">Category:</label>           
         
          <div class="col-sm-4">
            <select class="form-control " id="category" name="category" >  
                <option value="" >Select Category</option>
                    <?php
                        while($row = mysqli_fetch_assoc($categories)) {                    
                            echo '<option value="'.$row['id'].'" ';
                            echo  (isset($_POST["category"]) && $_POST["category"] == $row['id'])
                                ?'selected ':'';
                            echo    '>'.$row['name'].'</option>';
                        }         
                    ?>
            </select>
          </div>
        </div>
          
        <div class="form-group">
            <label class="control-label col-sm-2" for="product_name">Product Name:</label>
            <div class="col-sm-2">
                <input type="text" class="form-control" id="product_name" placeholder="Samsung 2360"
                   name="product_name" value="<?php echo (isset($_POST["product_name"])) ? 
                   $_POST["product_name"]:''; ?>">
          </div>
        </div>
          
        <div class="form-group">
            <label class="control-label col-sm-2" for="product_price">Price (INR):</label>
            <div class="col-sm-3">
                <input type="number" min='0' class="form-control" id="product_price" 
                    placeholder="12324" name="product_price" value="
                    <?php echo (isset($_POST["product_price"])) ? $_POST["product_price"] : ''; ?>">
            </div>
        </div>
        
        <div class="form-group">
          <label class="control-label col-sm-2" >Photo:</label>
          <div class="col-sm-10">
              <input type="file" name="product_pic" id="product_pic" />
          </div>
        </div>
        
        <div class="form-group">
            <label class="control-label col-sm-2" for="description">Description:</label>
            <div class="col-sm-5">
                <textarea class="form-control" rows="5" id="description" 
                        placeholder="Describe the product..." name="description" >
                            <?php  echo isset($_POST["description"]) ? $_POST["description"]:''; ?>
                </textarea>
            </div>
        </div> 
                    
        <div class="form-group">
          <div class="col-sm-offset-2 col-sm-1">
              <button type="submit" class="btn btn-default btn-lg btn-success">Add</button>
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
