<?php
// Include the constant files
require_once 'helper/validation.php';
require_once 'libraries/db.php';
require_once 'libraries/send_mail.php';
require_once 'libraries/encrypt_decrypt.php';

$db = new dbOperation;
$db->select('state_table');

// Fetch all the states from the database
while ($state_row = $db->fetch()) {
    $state_list[] = $state_row;
}

if ( ! empty($_POST)) {
    $pic_name = 'profile_pic';

    // Trim all whitespaces from string values
    $_POST = santizing($_POST);
    
    // Validate the POSTed data
    $error = validate_data($_POST);
  
    // Check if the email already exists
    if (empty($error['email'])) {
       $error['email'] = existing_email($_POST['email']); 
    }
    
    // Validate the image
    $error[$pic_name] = image_validation($pic_name);
    $fields_validated = TRUE;

    // Check if there are errors during validation
    foreach ($error as $error_keys => $error_messages) {

        if ( ! empty($error_messages)) {
            $fields_validated = FALSE;
            break;
        }
    }

    // If there are no validation errors then do database operations
    if (empty($error[$pic_name]) && $fields_validated) {
        
        $data = ['user_name'=> $_POST['user_name'] ,'first_name'=> $_POST['first_name'], 'middle_name'=> $_POST['middle_name'],
            'last_name'=> $_POST['last_name'], 'gender'=> $_POST['gender'], 'dob'=> $_POST['dob'],
            'type'=> $_POST['user_type'],'bio'=> $_POST['comment'],'preferred_comm'=> implode(',', $_POST['pref_comm']),
            'mobile'=> $_POST['contact_num']];
        $user_id = $db->insert_or_update(1, 'users', $data);
        
        $data_login = ['email'=> $_POST['email'],'password'=> md5($_POST['password']), 'user_id'=> $user_id];
        $db->insert_or_update(1, 'login', $data_login);
        
        $data_res_addr = ['user_id'=> $user_id, 'type'=> '1', 'street'=> $_POST['res_addrstreet'],
            'city'=> $_POST['res_addrcity'], 'state'=> $_POST['res_addrstate'], 'zip'=> $_POST['res_addrzip']]; 
        $db->insert_or_update(1, 'user_address', $data_res_addr);
        
        // If the office address is filled then insert them in database
        if ( ! empty($_POST['ofc_addrstreet']) || $_POST['ofc_addrstate'] === '0' ||
                ! empty($_POST['ofc_addrcity']) || ! empty($_POST['ofc_addrstate'])) {
            
            $data_ofc_addr = ['user_id'=> $user_id, 'type'=> '2', 'street'=> $_POST['ofc_addrstreet'],
                'city'=> $_POST['ofc_addrcity'], 'state'=> $_POST['ofc_addrstate'], 'zip'=> $_POST['ofc_addrzip']];
            $db->insert_or_update(1, 'user_address', $data_ofc_addr);
        }
        
        // If a file is uploaded
        if ($_FILES[$pic_name]['size'] !== 0) {

            $extension = (pathinfo(basename($_FILES[$pic_name]['name']))['extension']);
            $file_name = PROFILE_PIC . $user_id . '_' . time() . '.' . $extension;

            if (move_uploaded_file($_FILES[$pic_name]['tmp_name'], $file_name)) {
                $db->insert_or_update(2, 'users', ['image'=> basename($file_name)], ['id'=>$user_id]);              
            }
        }
        
        // Send activation mail
        $msg = '<b>Thank You for registering in QuickSeller</b>'
            . '<br>To activate your account click on this '
            . '<a href="http://local.quickseller.com/activation.php?id='. urlencode(simple_encrypt($user_id)) 
            . '">link</a>';
        $sub = 'Account Activation Link : QuickSeller';
        send_mail($_POST['email'], $msg, $sub);
        
        // Redirect to login page after registration
        header('Location: login.php?success=1');
    }
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
    <body>
        <!-- Include the navigation bar -->
        <?php require_once 'templates/show_nav.php'; ?>
        <div class='confirmation margin-top120'></div>
        <section id="signupform">
            <div class="container">
                <h3>Please fill in to sign up ...</h3>
                <form class="form-horizontal" role="form" method="post" enctype="multipart/form-data" 
                      action="sign_up.php">

                    <div class="form-group">
                        <label class="control-label col-sm-2" for="user_name">Username <span class="color-remove">*</span>
                        </label>
                        <div class="col-sm-2">
                            <input type="text" class="form-control" id="user_name" placeholder="bob234"
                                   name="user_name" value="<?php echo (isset($_POST['user_name'])) ? $_POST['user_name'] : ''; ?>">
                        </div>
                        <div class="col-sm-4 error-msg">
                            <?php echo isset($error['user_name']) ? $error['user_name'] : ''; ?> 
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label col-sm-2" for="first_name">First name <span class="color-remove">*</span>
                        </label>
                        <div class="col-sm-3">
                            <input type="text" class="form-control" id="first_name" placeholder="Bob"
                                   name="first_name" value="<?php echo (isset($_POST['first_name'])) ? $_POST['first_name'] : ''; ?>">
                        </div>
                        <div class="col-sm-4 error-msg">
                            <?php echo isset($error['first_name']) ? $error['first_name'] : ''; ?> 
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label col-sm-2" for="middle_name">Middle name &nbsp;</label>
                        <div class="col-sm-3">
                            <input type="text" class="form-control" id="middle_name" placeholder="James"
                                   name="middle_name" value="<?php echo isset($_POST['middle_name']) ? $_POST['middle_name'] : ''; ?>">
                        </div>
                        <div class="col-sm-4 error-msg">
                            <?php echo isset($error['middle_name']) ? $error['middle_name'] : ''; ?> 
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label col-sm-2" for="last_name">Last name <span class="color-remove">*</span>
                        </label>
                        <div class="col-sm-3">
                            <input type="text" class="form-control" id="last_name" placeholder="Martin"
                                   name="last_name" value="<?php echo isset($_POST['last_name']) ? $_POST['last_name'] : ''; ?>">
                        </div>
                        <div class="col-sm-4 error-msg">
                            <?php echo isset($error['last_name']) ? $error['last_name'] : ''; ?> 
                        </div>
                    </div>                        
                    <div class="form-group">
                        <label class="control-label col-sm-2" for="email">Email <span class="color-remove">*</span></label>
                        <div class="col-sm-3">
                            <input type="email" class="form-control" id="email" placeholder="bobjmartin@example.com"
                                   name="email" value="<?php 
                                   echo isset($_GET['email']) ? $_GET['email'] : (isset($_POST['email']) ? $_POST['email'] : ''); ?>">
                        </div>
                        <div class="col-sm-4 error-msg">
                            <?php echo isset($error['email']) ? $error['email'] : ''; ?> 
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-2" for="pwd">Password <span class="color-remove">*</span>
                        </label>
                        <div class="col-sm-3">
                            <input type="password" class="form-control" id="pwd" placeholder="password"
                                   name="password" value="<?php echo isset($_POST['password']) ? $_POST['password'] : ''; ?>">
                        </div>
                        <div class="col-sm-4 error-msg">
                            <?php echo isset($error['password']) ? $error['password'] : ''; ?> 
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label col-sm-2" for="confirmpwd">Confirm Password <span class="color-remove">*</span>
                        </label>
                        <div class="col-sm-3">
                            <input type="password" class="form-control" id="confirm_password" 
                                   placeholder="Confirm password" name="confirm_password"
                                   value="<?php echo isset($_POST['confirm_password']) ? $_POST['confirm_password'] : ''; ?>">
                        </div>
                        <div class="col-sm-4 error-msg">
                            <?php echo isset($error['confirm_password']) ? $error['confirm_password'] : ''; ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-2" for="contact_num">Contact Number <span class="color-remove">*</span>
                        </label>
                        <div class="col-sm-3">
                            <input type="text" class="form-control" id="contact_num" placeholder="9213321345" name="contact_num"
                                   value="<?php echo isset($_POST['contact_num']) ? $_POST['contact_num'] : ''; ?>">
                        </div>
                        <div class="col-sm-4 error-msg">
                            <?php echo isset($error['contact_num']) ? $error['contact_num'] : ''; ?> 
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label col-sm-2" >Gender &nbsp;</label>
                        <div class="col-sm-10">
                            <label class="radio-inline"><input type="radio" name="gender" value="M"
                                <?php echo isset($_POST['gender']) && ($_POST['gender'] === 'F') ? '' : 'checked="true"'
                                ?>> Male</label>
                            <label class="radio-inline"><input type="radio" name="gender" value="F"
                                <?php echo isset($_POST['gender']) && ($_POST['gender'] === 'F') ? 'checked="true"' : ''
                                ?> > Female</label>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label col-sm-2" for="dob">Date of birth <span class="color-remove">*</span>
                        </label>
                        <div class="col-sm-2">
                            <input type="date" class="form-control" id="dob" name="dob"  
                                   value="<?php echo isset($_POST['dob']) ? $_POST['dob'] : '1993-02-01'; ?>">
                        </div>
                        <div class="col-sm-5 error-msg">
                            <?php echo isset($error['dob']) ? $error['dob'] : ''; ?> 
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label col-sm-2" >User Type &nbsp;</label>
                        <div class="col-sm-10">
                            <label class="radio-inline"><input type="radio" name="user_type" value="3"
                                <?php echo isset($_POST['user_type']) && ($_POST['user_type'] === '2') ? 'checked="false"' : 'checked="true"' ?>> Buyer</label>
                            <label class="radio-inline"><input type="radio" name="user_type" value="2"
                                 <?php echo isset($_POST['user_type']) && ($_POST['user_type'] === '2') ? 'checked="true"' : '' ?>> Seller</label>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label col-sm-2" >Profile Picture &nbsp;</label>
                        <div class="col-sm-3">
                            <input type="file" name="profile_pic" id="profile_pic" />
                        </div>
                        <div class="col-sm-4 error-msg">
                             <?php echo isset($error['profile_pic']) ? $error['profile_pic'] : ''; ?>  
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="control-label col-sm-2" for="res_addrstate">Residence Address <span class="color-remove">*</span></label>  
                        <div class="col-sm-4">
                            <select class="form-control " id="res_addrstate" name="res_addrstate">  
                                <option value="" >Select State</option>
                                <?php
                                foreach ($state_list as $state) {
                                    echo '<option value="' . $state['id'] . '" ';
                                    echo (isset($_POST['res_addrstate']) && $_POST['res_addrstate'] === $state['id']) ? 'selected ' : '';
                                    echo '>' . $state['name'] . '</option>';
                                }                                
                                ?>
                            </select>
                        </div>
                        <div class="col-sm-4 error-msg"><?php echo isset($error['res_addrstate']) ? $error['res_addrstate'] : ''; ?> </div>
                        <div class="clearfix"></div>
                        <div class="col-sm-offset-2 col-sm-4 need-spacing">
                            <input type="text" class="form-control" id="res_addrcity" placeholder="Bhubaneswar"
                                   name="res_addrcity" value="<?php echo isset($_POST['res_addrcity']) ? $_POST['res_addrcity'] : ''; ?>">
                        </div>
                        <div class="col-sm-4 need-spacing error-msg"><?php echo isset($error['res_addrcity']) ? $error['res_addrcity'] : ''; ?> </div>
                        <div class="clearfix"></div>  
                        <div class="col-sm-offset-2 col-sm-4 need-spacing">
                            <input type="text" class="form-control" id="res_addrstreet" placeholder="Gandhi Street"
                                   name="res_addrstreet" value="<?php echo isset($_POST['res_addrstreet']) ? $_POST['res_addrstreet'] : ''; ?>">
                        </div>
                        <div class="col-sm-4 need-spacing error-msg"><?php echo isset($error['res_addrstreet']) ? $error['res_addrstreet'] : ''; ?> </div>
                        <div class="clearfix"></div>
                        <div class="col-sm-offset-2 col-sm-4 need-spacing">
                            <input type="text" class="form-control" id="res_addrzip" placeholder="748123"
                                   name="res_addrzip" value="<?php echo isset($_POST['res_addrzip']) ? $_POST['res_addrzip'] : ''; ?>">
                        </div>
                        <div class="col-sm-4 need-spacing error-msg"><?php echo isset($error['res_addrzip']) ? $error['res_addrzip'] : ''; ?> </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="control-label col-sm-2" for="ofc_addrstate">Office Address &nbsp;</label>           

                        <div class="col-sm-4">
                            <select class="form-control " id="ofc_addrstate" name="ofc_addrstate" >  
                                <option value="" >Select State</option>
                                <?php
                                foreach ($state_list as $state_id=>$state) {
                                    echo '<option value="' . $state_id . '" ';
                                    echo (isset($_POST['ofc_addrstate']) && $_POST['ofc_addrstate'] === $state['id']) ? 'selected ' : '';
                                    echo '>' . $state['name'] . '</option>';
                                }
                                
                                ?>
                            </select>
                        </div>
                        <div class="col-sm-4 error-msg"><?php echo isset($error['ofc_addrstate']) ? $error['ofc_addrstate'] : ''; ?> </div>
                        <div class="clearfix"></div>
                        <div class="col-sm-offset-2 col-sm-4 need-spacing">
                            <input type="text" class="form-control" id="ofc-addrcity" placeholder="Bhubaneswar"
                                   name="ofc_addrcity" value="<?php echo isset($_POST['ofc_addrcity']) ? $_POST['ofc_addrcity'] : ''; ?>">
                        </div>
                        <div class="col-sm-4 need-spacing error-msg"><?php echo isset($error['ofc_addrcity']) ? $error['ofc_addrcity'] : ''; ?> </div>
                        <div class="clearfix"></div>  
                        <div class="col-sm-offset-2 col-sm-4 need-spacing">
                            <input type="text" class="form-control" id="ofc-addrstreet" placeholder="Street-76"
                                   name="ofc_addrstreet" value="<?php echo isset($_POST['ofc_addrstreet']) ? $_POST['ofc_addrstreet'] : ''; ?>">
                        </div>
                        <div class="col-sm-4 need-spacing error-msg"><?php echo isset($error['ofc_addrstreet']) ? $error['ofc_addrstreet'] : ''; ?> </div>
                        <div class="clearfix"></div>
                        <div class="col-sm-offset-2 col-sm-4 need-spacing">
                            <input type="text" class="form-control" id="ofc-addrzip" placeholder="785103"
                                   name="ofc_addrzip" value="<?php echo isset($_POST['ofc_addrzip']) ? $_POST['ofc_addrzip'] : ''; ?>">
                        </div>
                        <div class="col-sm-4 need-spacing error-msg"><?php echo isset($error['ofc_addrzip']) ? $error['ofc_addrzip'] : ''; ?> </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label col-sm-2" for="comment">About Me &nbsp;</label>
                        <div class="col-sm-5">
                            <textarea class="form-control" rows="5" id="comment" placeholder="Describe yourself here..."
                                      name="comment" ><?php echo isset($_POST['comment']) ? $_POST['comment'] : ''; ?></textarea>
                        </div>
                    </div> 

                    <div class="form-group">
                        <label class="control-label col-sm-2" >Preferred Communication Medium &nbsp;</label>
                        <div class="col-sm-10 give_padding" >
                            <label class="checkbox-inline"><input type="checkbox" name="pref_comm[]" value="email"
                                <?php echo isset($_POST['pref_comm']) && in_array("email", $_POST['pref_comm']) ? ' checked' : ''; ?> > Email</label>
                            <label class="checkbox-inline"><input type="checkbox" name="pref_comm[]" value="sms"
                                <?php echo isset($_POST['pref_comm']) && in_array("sms", $_POST['pref_comm']) ? ' checked' : ''; ?>> SMS</label>
                            <label class="checkbox-inline"><input type="checkbox" name="pref_comm[]" value="call"
                                <?php echo isset($_POST['pref_comm']) && in_array("call", $_POST['pref_comm']) ? ' checked' : ''; ?>> Call</label>
                            <label class="checkbox-inline"><input type="checkbox" name="pref_comm[]" value="any"
                                <?php echo isset($_POST['pref_comm']) && in_array("any", $_POST['pref_comm']) ? ' checked' : ''; ?>> ANY</label>            
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

        <?php require_once 'templates/footer.php'; ?>
    </body>
    <script>
    
    </script>

</html>
