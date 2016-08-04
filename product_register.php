<?php 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'helper/states.php';
require_once 'dbconstants.php';
$msg = '';

if (!empty($_POST)) {
    print_r($_POST);
    $conn = mysqli_connect(SERVERNAME, USERNAME, PASSWORD, DBNAME);

    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }
$_POST['pref_comm'] = isset($_POST['pref_comm']) ? $_POST['pref_comm'] : [];
    $sql = "INSERT INTO `users` ("
            . " `first_name`, `middle_name`, `last_name`, `gender`, "
            . "`dob`, `type`, `bio`, `preferred_comm`, `image`, `mobile`, "
            . "`created_date`) VALUES ('"
            . $_POST['firstname'] . "','"
            . $_POST['middlename'] . "','"
            . $_POST['lastname'] . "','"
            . $_POST['gender'] . "','"
            . $_POST['dob'] . "','"
            . $_POST['user_type'] . "','"
            . $_POST['comment'] . "','"
            . implode(',', $_POST['pref_comm']) . "','','"
            . $_POST['contact_num'] . "',"
            . " NOW())";

    if (! mysqli_query($conn, $sql)) {
           $msg= "New record in USERS FAILURE";
           echo 'err1-->'. mysqli_error($conn);
           header('Location: error.php');
          // exit;
    }
    $user_id = mysqli_insert_id($conn);

    $sql_login = "INSERT INTO `login` "
            . "("
            . "`email`, `password`, `user_id`, `created_date`) VALUES "
            . "('"
            . $_POST['email']
            . "',"
            . " '"
            . $_POST['password']
            . "',"
            . " '"
            . $user_id  
            . "',"
            . " NOW())";

            
    if (! mysqli_query($conn, $sql_login)) {
            $msg= "New record in LOGIN FAILURE";
            echo 'err2-->'. mysqli_error($conn);
            //exit;
        }
     
    $sql_addr = "INSERT INTO `user_address` ("
            . "`user_id`, `type`, `street`, `city`, `state`, `zip`,"
            . " `created_date`) VALUES"
            . " ('"
            . $user_id
            . "', '"
            . "1"
            . "', '"
            . $_POST['res_addrstreet']
            . "', '"
            . $_POST['res_addrcity']
            . "', '"
            . $_POST['res_addrstate']
            . "', '"
            . $_POST['res_addrzip']
            . "', NOW())";
    if(!empty($_POST['ofc_addrstreet']) || !empty($_POST['ofc_addrcity']) || !empty($_POST['ofc_addrstate'])){
            $sql_addr.= ","
            . " ('"
            . $user_id
            . "', '"
            . "2"
            . "', '"
            . $_POST['ofc_addrstreet']
            . "', '"
            . $_POST['ofc_addrcity']
            . "', '"
            . $_POST['ofc_addrstate']
            . "', '"
            . $_POST['ofc_addrzip']
            . "', NOW())";
        }
        
    if (! mysqli_query($conn, $sql_addr)) {
        $msg = "New record in USER_ADDR FAILURE";
        echo 'err-->'. mysqli_error($conn);
        
    //    echo 'shhjshhs';
    }
            
    mysqli_close($conn);
}

?>
      

<!DOCTYPE html>
<html lang="en">

    <!--head-->
    <head>
        <title>QuickSeller:Product Registration</title>  
        <?php
           require_once 'templates/header.php';     
        ?>
    </head>


<body id="sign_up">

    <!-- Include the navigation bar -->
    <?php require_once 'templates/navigation.php'; ?>

    <section id="signupform">
        <h3><?php echo $msg; ?></h3>
    <div class="container">
      <h3>Please fill in to register your product ...</h3>
      <form class="form-horizontal" role="form" method="post" action="products_register.php">
              
        <div class="form-group">
          <label class="control-label col-sm-2" for="category">Category:</label>           
         
          <div class="col-sm-4">
            <select class="form-control " id="category" name="category" >  
                <option value="" >Select Category</option>
                    <?php
                        foreach($states as $state_id => $state_name)
                        {
                            echo '<option value="'.$state_id.'" ';
                            echo  (isset($_POST["ofc_addrstate"]) && $_POST["ofc_addrstate"] == $state_id) ?'selected ':'';
                            echo    '>'.$state_name.'</option>';
                        }         
                    ?>
            </select>
          </div>
        </div>
          
          
        <div class="form-group">
          <label class="control-label col-sm-2" for="product_name">Product Name:</label>
          <div class="col-sm-2">
             <input type="text" class="form-control" id="product_name" placeholder="Samsung 2360"
                   name="product_name" value="<?php echo (isset($_POST["product_name"])) ? $_POST["product_name"]:''; ?>">
          </div>
        </div>
          
        <div class="form-group">
          <label class="control-label col-sm-2" for="product_price">Price (INR):</label>
          <div class="col-sm-3">
              <input type="number" class="form-control" id="product_price" placeholder="12324"
                   name="product_price" value="<?php echo (isset($_POST["product_price"])) ? $_POST["product_price"]:''; ?>">
          </div>
        </div>
        
    

        <div class="form-group">
            <label class="control-label col-sm-2" for="description">Description:</label>
            <div class="col-sm-5">
                <textarea class="form-control" rows="5" id="description" placeholder="Describe the product..."
                          name="description" ><?php  echo isset($_POST["description"]) ? $_POST["description"]:''; ?></textarea>
            </div>
        </div> 
          
 
          
        <div class="form-group">
          <div class="col-sm-offset-2 col-sm-1">
            <button type="submit" class="btn btn-default btn-lg btn-success">Submit</button>
          </div>
          <div class="col-sm-offset-1 col-sm-1">
              <button type="reset" class="btn btn-default btn-lg btn-danger" onclick="sign_up.php">Reset</button>
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
