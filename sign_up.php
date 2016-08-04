<?php 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'helper/states.php';
require_once 'dbconstants.php';
require_once 'img_directories.php';
$msg = '';


if (!empty($_POST)) {
    
   
    

    if($_FILES['profile_pic']['error']==0){ // $FILES['name']['error'] is 0 if successfully uploaded
    
    $target_file = PROFILE_PIC .$_POST['username']. basename($_FILES["profile_pic"]["name"]);
    $uploadOk = 1;
    $imageFileType = pathinfo( basename($_FILES["profile_pic"]["name"]));
    print_r ($_FILES['profile_pic']);
    print_r ($imageFileType);
        //Check if image file is a actual image or fake image     
           $check = getimagesize($_FILES["profile_pic"]["tmp_name"]);
           print_r ($check);
           if($check !== false) {
               echo "File is an image - " . $check["mime"] . ".";
               $uploadOk = 1;
           } else {
               echo "File is not an image.";
               $uploadOk = 0;
           }
        
       // Check if file already exists
       if (file_exists($target_file)) {
           echo "Sorry, file already exists.";
           $uploadOk = 0;
       }
       // Check file size
       if ($_FILES["profile_pic"]["size"] > 500000) {
           echo "Sorry, your file is too large.";
           $uploadOk = 0;
       }
       // Allow certain file formats
       if(!in_array($imageFileType['extension'], array('jpeg','jpg','png','gif'))) {
           echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
           $uploadOk = 0;
       }
       // Check if $uploadOk is set to 0 by an error
       if ($uploadOk == 0) {
           echo "Sorry, your file was not uploaded.";
       // if everything is ok, try to upload file
       } else {
           if (move_uploaded_file($_FILES["profile_pic"]["tmp_name"], $target_file)) {
               echo "The file ". basename( $_FILES["profile_pic"]["name"]). " has been uploaded.";
           } else {
               echo "Sorry, there was an error uploading your file.";
           }
       }
    }

    
    $conn = mysqli_connect(SERVERNAME, USERNAME, PASSWORD, DBNAME);

    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }
$_POST['pref_comm'] = isset($_POST['pref_comm']) ? $_POST['pref_comm'] : [];
    $sql = "INSERT INTO `users` ("
            . " `first_name`, `middle_name`, `last_name`, `gender`, "
            . "`dob`, `type`, `bio`, `preferred_comm`, `mobile`,`image`, "
            . "`created_date`) VALUES ('"
            . $_POST['firstname'] . "','"
            . $_POST['middlename'] . "','"
            . $_POST['lastname'] . "','"
            . $_POST['gender'] . "','"
            . $_POST['dob'] . "','"
            . $_POST['user_type'] . "','"
            . $_POST['comment'] . "','"
            . implode(',', $_POST['pref_comm']) . "','"
            . $_POST['contact_num'] . "','"
            . $_POST['username']
            . "',"
            . " NOW())";
echo $sql;
    if (! mysqli_query($conn, $sql)) {
           $msg= "New record in USERS FAILURE";
           echo 'err1-->'. mysqli_error($conn);
          // header('Location: error.php');
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
            
    //mysqli_close($conn);
}

?>
      

<!DOCTYPE html>
<html lang="en">

    <!--head-->
    <head>
           <title>QuickSeller : Sign Up</title>
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
      <h3>Please fill in to sign up ...</h3>
      <form class="form-horizontal" role="form" method="post" enctype="multipart/form-data" action="sign_up.php">
        <div class="form-group">
          <label class="control-label col-sm-2" for="username">Username:</label>
          <div class="col-sm-2">
             <input type="text" class="form-control" id="username" placeholder="bob234"
                   name="username" value="<?php echo (isset($_POST["username"])) ? $_POST["username"]:''; ?>">
          </div>
        </div>
          
        <div class="form-group">
          <label class="control-label col-sm-2" for="firstname">First name:</label>
          <div class="col-sm-3">
            <input type="text" class="form-control" id="firstname" placeholder="Bob"
                   name="firstname" value="<?php echo (isset($_POST["firstname"])) ? $_POST["firstname"]:''; ?>">
          </div>
        </div>
        
         <div class="form-group">
          <label class="control-label col-sm-2" for="middlename">Middle name:</label>
          <div class="col-sm-3">
            <input type="text" class="form-control" id="middlename" placeholder="James"
                    name="middlename" value="<?php echo isset($_POST["middlename"]) ? $_POST["middlename"]:''; ?>">
          </div>
        </div>
          
        <div class="form-group">
          <label class="control-label col-sm-2" for="lastname">Last name:</label>
          <div class="col-sm-3">
            <input type="text" class="form-control" id="lastname" placeholder="Martin"
                    name="lastname" value="<?php echo isset($_POST["lastname"]) ? $_POST["lastname"]:''; ?>">
          </div>
        </div>      
           
        <div class="form-group">
          <label class="control-label col-sm-2" for="email">Email:</label>
          <div class="col-sm-3">
            <input type="email" class="form-control" id="email" placeholder="bobjmartin@example.com"
                    name="email" value="<?php echo isset($_POST["email"]) ? $_POST["email"]:''; ?>">
          </div>
        </div>
          
        <div class="form-group">
          <label class="control-label col-sm-2" for="pwd">Password:</label>
          <div class="col-sm-3">
            <input type="password" class="form-control" id="pwd" placeholder="password"
                    name="password" value="<?php echo isset($_POST["password"]) ? $_POST["password"]:''; ?>">
          </div>
        </div>
          
        <div class="form-group">
          <label class="control-label col-sm-2" for="confirmpwd">Confirm Password:</label>
          <div class="col-sm-3">
            <input type="password" class="form-control" id="confirm_password" placeholder="Confirm password"
                    name="confirm_password" value="<?php echo isset($_POST["confirm_password"]) ? $_POST["confirm_password"]:''; ?>">
          </div>
        </div>
         
         <div class="form-group">
          <label class="control-label col-sm-2" for="contact_num">Contact Number:</label>
          <div class="col-sm-3">
            <input type="text" class="form-control" id="contact_num" placeholder="9213321345"
                    name="contact_num" value="<?php echo isset($_POST["contact_num"]) ? $_POST["contact_num"]:''; ?>">
          </div>
        </div>
          
        <div class="form-group">
          <label class="control-label col-sm-2" >Gender:</label>
          <div class="col-sm-10">
           <label class="radio-inline"><input type="radio" name="gender" value="M"
                <?php echo isset($_POST["gender"]) && ($_POST["gender"]==='F') ?'':'checked="true"'?>> Male</label>
           <label class="radio-inline"><input type="radio" name="gender" value="F"
                <?php echo isset($_POST["gender"]) && ($_POST["gender"]==='F') ?'checked="true"':''?> > Female</label>
          </div>
        </div>
          
         <div class="form-group">
            <label class="control-label col-sm-2" for="dob">Date of birth:</label>
            <div class="col-sm-2">
              <input type="date" class="form-control" id="dob" name="dob"  
                     value="<?php echo isset($_POST["dob"]) ? $_POST["dob"] : '1993-02-01';?>">
            </div>
        </div>
          
        <div class="form-group">
          <label class="control-label col-sm-2" >User Type:</label>
          <div class="col-sm-10">
              <label class="radio-inline"><input type="radio" name="user_type" value="B"
                  <?php echo isset($_POST["user_type"]) && ($_POST["user_type"]==='S') ?'checked="false"':'checked="true"'?>> Buyer</label>
           <label class="radio-inline"><input type="radio" name="user_type" value="S"
                  <?php echo isset($_POST["user_type"]) && ($_POST["user_type"]==='S') ?'checked="true"':''?>> Seller</label>
          </div>
        </div>

        <div class="form-group">
          <label class="control-label col-sm-2" >Picture:</label>
          <div class="col-sm-10">
              <input type="file" name="profile_pic" id="profile_pic" />
          </div>
        </div>
          
        <div class="form-group">
          <label class="control-label col-sm-2" for="res_addrstate">Residence Address:</label>  
        
          <div class="col-sm-4">
            <select class="form-control " id="res_addrstate" name="res_addrstate">  
                <option value="" >Select State</option>
                <?php
                        foreach($states as $state_id => $state_name) {
                            echo '<option value="'.$state_id.'" ';
                            echo  (isset($_POST["res_addrstate"]) && $_POST["res_addrstate"] == $state_id) ?'selected ':'';
                            echo    '>'.$state_name.'</option>';
                        }         
                    ?>
            </select>
          </div>
          <div class="clearfix"></div>
          <div class="col-sm-offset-2 col-sm-4 needspacing">
            <input type="text" class="form-control" id="res_addrcity" placeholder="Bhubaneswar"
                    name="res_addrcity" value="<?php echo isset($_POST['res_addrcity']) ? $_POST["res_addrcity"]:''; ?>">
          </div>
          <div class="clearfix"></div>  
          <div class="col-sm-offset-2 col-sm-4 needspacing">
            <input type="text" class="form-control" id="res_addrstreet" placeholder="Gandhi Street"
                    name="res_addrstreet" value="<?php  echo isset($_POST["res_addrstreet"]) ? $_POST["res_addrstreet"]:''; ?>">
          </div>
          <div class="clearfix"></div>
          <div class="col-sm-offset-2 col-sm-4 needspacing">
            <input type="text" class="form-control" id="res_addrzip" placeholder="748123"
                    name="res_addrzip" value="<?php  echo isset($_POST["res_addrzip"]) ? $_POST["res_addrzip"]:''; ?>">
          </div>           
        </div>
          
        <div class="form-group">
          <label class="control-label col-sm-2" for="ofc_addrstate">Office Address:</label>           
         
          <div class="col-sm-4">
            <select class="form-control " id="ofc_addrstate" name="ofc_addrstate" >  
                <option value="" >Select State</option>
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
          <div class="clearfix"></div>
          <div class="col-sm-offset-2 col-sm-4 needspacing">
            <input type="text" class="form-control" id="ofc-addrcity" placeholder="Bhubaneswar"
                    name="ofc_addrcity" value="<?php  echo isset($_POST["ofc_addrcity"]) ? $_POST["ofc_addrcity"]:''; ?>">
          </div>
          <div class="clearfix"></div>  
          <div class="col-sm-offset-2 col-sm-4 needspacing">
            <input type="text" class="form-control" id="ofc-addrstreet" placeholder="Street-76"
                   name="ofc_addrstreet" value="<?php  echo isset($_POST["ofc_addrstreet"]) ? $_POST["ofc_addrstreet"]:''; ?>">
          </div>
          <div class="clearfix"></div>
          <div class="col-sm-offset-2 col-sm-4 needspacing">
            <input type="text" class="form-control" id="ofc-addrzip" placeholder="785103"
                   name="ofc_addrzip" value="<?php  echo isset($_POST["ofc_addrzip"]) ? $_POST["ofc_addrzip"]:''; ?>">
          </div>
        </div>

        <div class="form-group">
            <label class="control-label col-sm-2" for="comment">About Me:</label>
            <div class="col-sm-5">
                <textarea class="form-control" rows="5" id="comment" placeholder="Describe yourself here..."
                          name="comment" ><?php  echo isset($_POST["comment"]) ? $_POST["comment"]:''; ?></textarea>
            </div>
        </div> 
          
        <div class="form-group">
          <label class="control-label col-sm-2" >Preferred Communication Medium:</label>
          <div class="col-sm-10 give_padding" >
              <label class="checkbox-inline"><input type="checkbox" name="pref_comm[]" value="email"
                <?php echo isset($_POST["pref_comm"]) && in_array("email", $_POST["pref_comm"]) ? ' checked':''; ?> > Email</label>
            <label class="checkbox-inline"><input type="checkbox" name="pref_comm[]" value="sms"
                <?php echo isset($_POST["pref_comm"]) && in_array("sms", $_POST["pref_comm"]) ? ' checked':''; ?>> SMS</label>
            <label class="checkbox-inline"><input type="checkbox" name="pref_comm[]" value="call"
                <?php echo isset($_POST["pref_comm"]) && in_array("call", $_POST["pref_comm"]) ? ' checked':''; ?>> Call</label>
            <label class="checkbox-inline"><input type="checkbox" name="pref_comm[]" value="any"
                <?php echo isset($_POST["pref_comm"]) && in_array("any", $_POST["pref_comm"]) ? ' checked':''; ?>> ANY</label>            
          </div>
        </div>  
          
        <div class="form-group">
          <div class="col-sm-offset-2 col-sm-1">
            <button type="submit" class="btn btn-default btn-lg btn-success" name="submit">Submit</button>
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
