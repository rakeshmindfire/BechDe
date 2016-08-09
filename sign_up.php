<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'helper/states.php';
require_once 'helper/validation.php';
require_once 'config/constants.php';

$msg = '';

$conn = mysqli_connect(SERVERNAME, USERNAME, PASSWORD, DBNAME);

if (!$conn) {
         die("Connection failed: " . mysqli_connect_error());
    }

$sql_getstates = "SELECT `id`, `name` FROM `state_table`";
$state_query_result = mysqli_query($conn, $sql_getstates);

while ($state_row = mysqli_fetch_assoc($state_query_result)) {
    $state_list[] = $state_row;
}
//print_r($state_list);
    
if (!empty($_POST)) {
    $pic_name = 'profile_pic';
    
     //trim all whitespaces from string values
    foreach ($_POST as $key => $postvalues) {
        if (is_string($postvalues)) {
            $_POST[$key] = trim ($postvalues);
        }
    }
    $error = validate_data($_POST);
    $error[$pic_name] = image_check($pic_name);  
    $fields_validated=TRUE;
    
    foreach ($error as $error_keys => $error_messages) {
   
        if( ! empty($error_messages)) {
            $fields_validated = FALSE;
            break;
        }    
    }
    print_r($error);
    print_r($_FILES[$pic_name]);
    if (empty($error[$pic_name]) && $fields_validated) {
    
        $sql = "INSERT INTO `users` ( `first_name`, `middle_name`, `last_name`, `gender`, `dob`,"
                . " `type`, `bio`, `preferred_comm`, `mobile`, `created_date`) VALUES ('"
                . $_POST['firstname'] . "','"
                . $_POST['middlename'] . "','"
                . $_POST['lastname'] . "','"
                . $_POST['gender'] . "','"
                . $_POST['dob'] . "','"
                . $_POST['user_type'] . "','"
                . $_POST['comment'] . "','"
                . implode(',', $_POST['pref_comm']) . "','"
                . $_POST['contact_num'] . "', NOW())";

        if (!mysqli_query($conn, $sql)) {
            $msg = "New record in USERS FAILURE";
            echo 'err1-->' . mysqli_error($conn);
            header('Location: error.php');
        }

        $user_id = mysqli_insert_id($conn);
        $sql_login = "INSERT INTO `login`(`email`, `password`, `user_id`, `created_date`) VALUES ('"
                . $_POST['email']
                . "',"
                . " '"
                . $_POST['password']
                . "',"
                . " '"
                . $user_id
                . "',"
                . " NOW())";

        if (!mysqli_query($conn, $sql_login)) {
            $msg = "New record in LOGIN FAILURE";
            echo 'err2-->' . mysqli_error($conn);
        }

        $sql_addr = "INSERT INTO `user_address` (`user_id`, `type`, `street`, `city`, `state`, `zip`"
                . ",`created_date`) VALUES ('"
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

        if (!empty($_POST['ofc_addrstreet']) || $_POST['ofc_addrstate'] === '0' ||
                !empty($_POST['ofc_addrcity']) || !empty($_POST['ofc_addrstate'])) {

            //$_POST['ofc_addrstate']==='0' : because empty() is true for Andaman Nicobar id 0

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
        // echo $sql_addr;    
        if (!mysqli_query($conn, $sql_addr)) {
            $msg = "New record in USER_ADDR FAILURE";
            echo 'err-->' . mysqli_error($conn);

            //    echo 'shhjshhs';
        }

        if ($_FILES[$pic_name]['size']!=0 ) {

            $extension = (pathinfo(basename($_FILES[$pic_name]["name"]))['extension']);
            $file_name = PROFILE_PIC . $user_id .'_'. time() . '.' . $extension;

            if (move_uploaded_file($_FILES[$pic_name]["tmp_name"], $file_name)) {

                $sql_putimage = "UPDATE `users` SET `image`='"
                        . basename($file_name)
                        . "' WHERE `id`='" . $user_id . "'";

                if (!mysqli_query($conn, $sql_putimage)) {
                    $msg = "New record in USERS (image) FAILURE";
                    echo 'err1-->' . mysqli_error($conn);
                    header('Location: error.php');
                }
            }
        }
    header('Location: sign_up.php?success=1');
      
    } else {
        echo "Sorry, please check your file type and size";
    }
}
mysqli_close($conn);
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


    <body>

        <!-- Include the navigation bar -->
        <?php require_once 'templates/navigation.php'; 
        
        if(isset($_GET['success']) && 1) {
            echo "<section><div class='alert-success'>User registered successfully</div></section>";

            } 
        ?>
        <section id="signupform">
            <h3><?php echo $msg; ?></h3>
            <div class="container">
                <h3>Please fill in to sign up ...</h3>
                <form class="form-horizontal" role="form" method="post" enctype="multipart/form-data" action="sign_up.php">
                    <div class="form-group">
                        <label class="control-label col-sm-2" for="username">Username <span class="color_remove">*</span></label>
                        <div class="col-sm-2">
                            <input type="text" class="form-control" id="username" placeholder="bob234"
                                   name="username" value="<?php echo (isset($_POST["username"])) ? $_POST["username"] : ''; ?>">
                        </div>
                        <div class="col-sm-4 error_msg"><?php echo isset($error['username']) ? $error['username'] : '';?> </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label col-sm-2" for="firstname">First name <span class="color_remove">*</span></label>
                        <div class="col-sm-3">
                            <input type="text" class="form-control" id="firstname" placeholder="Bob"
                                   name="firstname" value="<?php echo (isset($_POST["firstname"])) ? $_POST["firstname"] : ''; ?>">
                        </div>
                          <div class="col-sm-4 error_msg"><?php echo isset($error['firstname']) ? $error['firstname'] : '';?> </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label col-sm-2" for="middlename">Middle name &nbsp;</label>
                        <div class="col-sm-3">
                            <input type="text" class="form-control" id="middlename" placeholder="James"
                                   name="middlename" value="<?php echo isset($_POST["middlename"]) ? $_POST["middlename"] : ''; ?>">
                        </div>
                          <div class="col-sm-4 error_msg"><?php echo isset($error['middlename']) ? $error['middlename'] : '';?> </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label col-sm-2" for="lastname">Last name <span class="color_remove">*</span></label>
                        <div class="col-sm-3">
                            <input type="text" class="form-control" id="lastname" placeholder="Martin"
                                   name="lastname" value="<?php echo isset($_POST["lastname"]) ? $_POST["lastname"] : ''; ?>">
                        </div>
                          <div class="col-sm-4 error_msg"><?php echo isset($error['lastname']) ? $error['lastname'] : '';?> </div>
                    </div>      

                    <div class="form-group">
                        <label class="control-label col-sm-2" for="email">Email <span class="color_remove">*</span></label>
                        <div class="col-sm-3">
                            <input type="email" class="form-control" id="email" placeholder="bobjmartin@example.com"
                                   name="email" value="<?php echo isset($_POST["email"]) ? $_POST["email"] : ''; ?>">
                        </div>
                          <div class="col-sm-4 error_msg"><?php echo isset($error['email']) ? $error['email'] : '';?> </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label col-sm-2" for="pwd">Password <span class="color_remove">*</span></label>
                        <div class="col-sm-3">
                            <input type="password" class="form-control" id="pwd" placeholder="password"
                                   name="password" value="<?php echo isset($_POST["password"]) ? $_POST["password"] : ''; ?>">
                        </div>
                        <div class="col-sm-4 error_msg"><?php echo isset($error['password']) ? $error['password'] : '';?> </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label col-sm-2" for="confirmpwd">Confirm Password <span class="color_remove">*</span></label>
                        <div class="col-sm-3">
                            <input type="password" class="form-control" id="confirm_password" placeholder="Confirm password"
                                   name="confirm_password" value="<?php echo isset($_POST["confirm_password"]) ? $_POST["confirm_password"] : ''; ?>">
                        </div>
                        <div class="col-sm-4 error_msg"><?php echo isset($error['confirm_password']) ? $error['confirm_password'] : '';?> </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label col-sm-2" for="contact_num">Contact Number <span class="color_remove">*</span></label>
                        <div class="col-sm-3">
                            <input type="text" class="form-control" id="contact_num" placeholder="9213321345"
                                   name="contact_num" value="<?php echo isset($_POST["contact_num"]) ? $_POST["contact_num"] : ''; ?>">
                        </div>
                          <div class="col-sm-4 error_msg"><?php echo isset($error['contact_num']) ? $error['contact_num'] : '';?> </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label col-sm-2" >Gender &nbsp;</label>
                        <div class="col-sm-10">
                            <label class="radio-inline"><input type="radio" name="gender" value="M"
                                <?php echo isset($_POST["gender"]) && ($_POST["gender"] === 'F')
                                    ? '' : 'checked="true"' ?>> Male</label>
                            <label class="radio-inline"><input type="radio" name="gender" value="F"
                                <?php echo isset($_POST["gender"]) && ($_POST["gender"] === 'F')
                                ? 'checked="true"' : '' ?> > Female</label>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label col-sm-2" for="dob">Date of birth <span class="color_remove">*</span></label>
                        <div class="col-sm-2">
                            <input type="date" class="form-control" id="dob" name="dob"  
                                   value="<?php echo isset($_POST["dob"]) ? $_POST["dob"] : '1993-02-01'; ?>">
                        </div>
                          <div class="col-sm-5 error_msg"><?php echo isset($error['dob']) ? $error['dob'] : '';?> </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label col-sm-2" >User Type &nbsp;</label>
                        <div class="col-sm-10">
                            <label class="radio-inline"><input type="radio" name="user_type" value="B"
                            <?php echo isset($_POST["user_type"]) && ($_POST["user_type"] === 'S') ? 'checked="false"' : 'checked="true"' ?>> Buyer</label>
                                                        <label class="radio-inline"><input type="radio" name="user_type" value="S"
                            <?php echo isset($_POST["user_type"]) && ($_POST["user_type"] === 'S') ? 'checked="true"' : '' ?>> Seller</label>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label col-sm-2" >Profile Picture &nbsp;</label>
                        <div class="col-sm-3">
                            <input type="file" name="profile_pic" id="profile_pic" />
                        </div>
                        <div class="col-sm-4 error_msg"><?php echo isset($error['profile_pic']) ? $error['profile_pic'] : '';?> </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label col-sm-2" for="res_addrstate">Residence Address <span class="color_remove">*</span></label>  

                        <div class="col-sm-4">
                            <select class="form-control " id="res_addrstate" name="res_addrstate">  
                            <option value="" >Select State</option>
                            <?php
                            foreach ($state_list as $state) {
                                echo '<option value="' . $state['id'] . '" ';
                                echo (isset($_POST["res_addrstate"]) && $_POST["res_addrstate"] == $state['id']) ? 'selected ' : '';
                                echo '>' . $state['name'] . '</option>';
                            }
                            ?>
                            </select>
                        </div>
                          <div class="col-sm-4 error_msg"><?php echo isset($error['res_addrstate']) ? $error['res_addrstate'] : '';?> </div>
                        <div class="clearfix"></div>
                        <div class="col-sm-offset-2 col-sm-4 needspacing">
                            <input type="text" class="form-control" id="res_addrcity" placeholder="Bhubaneswar"
                                   name="res_addrcity" value="<?php echo isset($_POST['res_addrcity']) ? $_POST["res_addrcity"] : ''; ?>">
                        </div>
                        <div class="col-sm-4 needspacing error_msg"><?php echo isset($error['res_addrcity']) ? $error['res_addrcity'] : '';?> </div>
                        <div class="clearfix"></div>  
                        <div class="col-sm-offset-2 col-sm-4 needspacing">
                            <input type="text" class="form-control" id="res_addrstreet" placeholder="Gandhi Street"
                                   name="res_addrstreet" value="<?php echo isset($_POST["res_addrstreet"]) ? $_POST["res_addrstreet"] : ''; ?>">
                        </div>
                        <div class="col-sm-4 needspacing error_msg"><?php echo isset($error['res_addrstreet']) ? $error['res_addrstreet'] : '';?> </div>
                        <div class="clearfix"></div>
                        <div class="col-sm-offset-2 col-sm-4 needspacing">
                            <input type="text" class="form-control" id="res_addrzip" placeholder="748123"
                                   name="res_addrzip" value="<?php echo isset($_POST["res_addrzip"]) ? $_POST["res_addrzip"] : ''; ?>">
                        </div>
                        <div class="col-sm-4 needspacing error_msg"><?php echo isset($error['res_addrzip']) ? $error['res_addrzip'] : '';?> </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label col-sm-2" for="ofc_addrstate">Office Address &nbsp;</label>           

                        <div class="col-sm-4">
                            <select class="form-control " id="ofc_addrstate" name="ofc_addrstate" >  
                                                            <option value="" >Select State</option>
                            <?php
                            foreach ($state_list as $state) {
                                echo '<option value="' . $state_id . '" ';
                                echo (isset($_POST["ofc_addrstate"]) && $_POST["ofc_addrstate"] == $state['id']) ? 'selected ' : '';
                                echo '>' . $state['name'] . '</option>';
                            }
                            ?>
                            </select>
                        </div>
                        <div class="col-sm-4 error_msg"><?php echo isset($error['ofc_addrstate']) ? $error['ofc_addrstate'] : '';?> </div>
                        <div class="clearfix"></div>
                        <div class="col-sm-offset-2 col-sm-4 needspacing">
                            <input type="text" class="form-control" id="ofc-addrcity" placeholder="Bhubaneswar"
                                   name="ofc_addrcity" value="<?php echo isset($_POST["ofc_addrcity"]) ? $_POST["ofc_addrcity"] : ''; ?>">
                        </div>
                        <div class="col-sm-4 needspacing error_msg"><?php echo isset($error['ofc_addrcity']) ? $error['ofc_addrcity'] : '';?> </div>
                        <div class="clearfix"></div>  
                        <div class="col-sm-offset-2 col-sm-4 needspacing">
                            <input type="text" class="form-control" id="ofc-addrstreet" placeholder="Street-76"
                                   name="ofc_addrstreet" value="<?php echo isset($_POST["ofc_addrstreet"]) ? $_POST["ofc_addrstreet"] : ''; ?>">
                        </div>
                        <div class="col-sm-4 needspacing error_msg"><?php echo isset($error['ofc_addrstreet']) ? $error['ofc_addrstreet'] : '';?> </div>
                        <div class="clearfix"></div>
                        <div class="col-sm-offset-2 col-sm-4 needspacing">
                            <input type="text" class="form-control" id="ofc-addrzip" placeholder="785103"
                                   name="ofc_addrzip" value="<?php echo isset($_POST["ofc_addrzip"]) ? $_POST["ofc_addrzip"] : ''; ?>">
                        </div>
                        <div class="col-sm-4 needspacing error_msg"><?php echo isset($error['ofc_addrzip']) ? $error['ofc_addrzip'] : '';?> </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label col-sm-2" for="comment">About Me &nbsp;</label>
                        <div class="col-sm-5">
                            <textarea class="form-control" rows="5" id="comment" placeholder="Describe yourself here..."
                                      name="comment" ><?php echo isset($_POST["comment"]) ? $_POST["comment"] : ''; ?></textarea>
                        </div>
                    </div> 

                    <div class="form-group">
                        <label class="control-label col-sm-2" >Preferred Communication Medium &nbsp;</label>
                        <div class="col-sm-10 give_padding" >
                            <label class="checkbox-inline"><input type="checkbox" name="pref_comm[]" value="email"
                            <?php echo isset($_POST["pref_comm"]) && in_array("email", $_POST["pref_comm"]) ? ' checked' : ''; ?> > Email</label>
                            <label class="checkbox-inline"><input type="checkbox" name="pref_comm[]" value="sms"
                            <?php echo isset($_POST["pref_comm"]) && in_array("sms", $_POST["pref_comm"]) ? ' checked' : ''; ?>> SMS</label>
                            <label class="checkbox-inline"><input type="checkbox" name="pref_comm[]" value="call"
                            <?php echo isset($_POST["pref_comm"]) && in_array("call", $_POST["pref_comm"]) ? ' checked' : ''; ?>> Call</label>
                            <label class="checkbox-inline"><input type="checkbox" name="pref_comm[]" value="any"
                            <?php echo isset($_POST["pref_comm"]) && in_array("any", $_POST["pref_comm"]) ? ' checked' : ''; ?>> ANY</label>            
                        </div>
                    </div>  

                    <div class="form-group">
                        <div class="col-sm-offset-2 col-sm-1">
                            <button type="submit" class="btn btn-default btn-lg btn-success">Submit</button>
                        </div>
                        <div class="col-sm-offset-1 col-sm-1">
                            <button type="reset" class="btn btn-default btn-lg btn-danger">Reset</button>
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
