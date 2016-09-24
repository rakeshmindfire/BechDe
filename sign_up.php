<?php
// Include the constant files
require_once 'helper/validation.php';
require_once 'libraries/db.php';
require_once 'libraries/send_mail.php';
require_once 'libraries/encrypt_decrypt.php';
require_once 'libraries/session.php';

$session = new Session;

// If session set redirect to update profile page
if ( ! isset($_GET['user']) && $session->validate_session()) {
    header('Location: sign_up.php?user=' . $_SESSION['id']);
    exit;
}

$role_str = $_SESSION['role'] === '2' ? 'seller' : 'buyer';

// If session not set redirect to index.php
if ( isset($_GET['user']) && ! $session->is_user_authorized(TRUE, $role_str.' profile', 'edit')) {
    error_log_file('Unauthorized access. User not allowed', FALSE);
    unset($_GET['user']);
}

$is_update = FALSE;
$db = new dbOperation;

// Get User details  
if (isset($_GET['user']) ) {
    
    // Check if profile belongs to that user
    $db->select('login', ['email'], ['user_id'=>$_GET['user']]);
    $res_user_email = $db->fetch();

    // Logging out User if try to update any other user product
    if ( $_SESSION['role'] !== '1' && $res_user_email['email'] !== $_SESSION['email']) {
        header('Location: logout.php');
        error_log_file('Unauthorized access');
    }

    // Set the flag to show data is being updated
    $is_update = TRUE;
    
    // Fetch all data of the product being updated
    $db->get_all_users(['u.id' => $_GET['user']] );
    $row_to_update = $db->fetch(); 
} 

// Fetch all the states from the database
$db->select('state_table');

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
            'mobile'=> $_POST['contact_num'], 'twitter_username'=> $_POST['twitter_username']];
        
        
        if ( ! $is_update) {
            $user_id = $db->insert_or_update(1, 'users', $data);

            $data_login = ['email'=> $_POST['email'],'password'=> md5($_POST['password']), 'user_id'=> $user_id];
            $db->insert_or_update(1, 'login', $data_login);
        } else {
            unset($data['user_name']);
            unset($data['type']);
            print_r($data);
            $db->insert_or_update(2, 'users', $data, ['id'=>$_SESSION['id']]);    
        }
       

        $data_res_addr = ['user_id'=> $user_id, 'type'=> '1', 'street'=> $_POST['res_addrstreet'],
        'city'=> $_POST['res_addrcity'], 'state'=> $_POST['res_addrstate'], 'zip'=> $_POST['res_addrzip']];
        
        if ( ! $is_update) {
            $db->insert_or_update(1, 'user_address', $data_res_addr);
        } else {     
            unset($data_res_addr['user_id']);
            unset($data_res_addr['type']);
            $db->insert_or_update(2, 'user_address', $data_res_addr, ['user_id'=>$_SESSION['id'], 'type'=> 1 ] );
        }
 
        // If the office address is filled then insert them in database
        if ( ! empty($_POST['ofc_addrstreet']) || $_POST['ofc_addrstate'] === '0' ||
                ! empty($_POST['ofc_addrcity']) || ! empty($_POST['ofc_addrstate'])) {
            
            $data_ofc_addr = ['user_id'=> $user_id, 'type'=> '2', 'street'=> $_POST['ofc_addrstreet'],
                'city'=> $_POST['ofc_addrcity'], 'state'=> $_POST['ofc_addrstate'], 'zip'=> $_POST['ofc_addrzip']];
            if( ! $is_update) {
                $db->insert_or_update(1, 'user_address', $data_ofc_addr);
            } else {
                unset($data_res_addr['user_id']);
                unset($data_res_addr['type']);
                $db->insert_or_update(2, 'user_address', $data_ofc_addr, ['user_id'=>$_SESSION['id'], 'type'=> 2 ] );
            }
        }

        // Delete the previous image if a new file is uploaded
        if ($is_update && $_FILES[$pic_name]['size'] !== 0) {
            $db->select('users', ['image'], ['id'=>$_SESSION['id']]);
             $img_to_update = $db->fetch();
           
           if ( ! is_null($img_to_update['image']) && file_exists(PROFILE_PIC.$img_to_update['image'])) {
               unlink(PROFILE_PIC.$img_to_update['image']);
           }
        }

        // Enter (new) image file to the database
        if ( ! empty($_FILES[$pic_name])) {
            $extension = (pathinfo(basename($_FILES[$pic_name]['name']))['extension']);
            $file_name = PROFILE_PIC . $user_id . '_' . time() . '.' . $extension;
            
            if (move_uploaded_file($_FILES[$pic_name]['tmp_name'], $file_name)) {
                
                 $db->insert_or_update(2, 'users', ['image'=> basename($file_name)], ['id'=>$_SESSION['id']]); 
            }
        }
        
        if ( ! $is_update) {
            // Send activation mail
            $msg = '<b>Thank You for registering in QuickSeller</b>'
                . '<br>To activate your account click on this '
                . '<a href="http://local.quickseller.com/activation.php?id='. urlencode(simple_encrypt($user_id)) 
                . '">link</a>';
            $sub = 'Account Activation Link : QuickSeller';
            send_mail($_POST['email'], $msg, $sub);

            // Redirect to login page after registration
            header('Location: login.php?success=1');exit;
        } else {
            header('Location: my_profile.php');exit;
        }
        
    }
}
?>

<!DOCTYPE html>
<html lang="en">
    <!--head-->
    <head>
        <title>QuickSeller : <?php echo $is_update? 'Edit Profile': 'Sign Up'; ?></title>
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
                <h3><?php echo ! $is_update ? 'Please fill in to sign up': 'Edit your profile';?> ...</h3>
                <form class="form-horizontal" role="form" method="post" enctype="multipart/form-data" 
                      action="sign_up.php<?php echo $is_update ? '?user='.$_GET['user']:''; ?>">

                    <div class="form-group">
                        <label class="control-label col-sm-2" for="user_name">Username 
                            <?php echo ! $is_update ? '<span class="color-remove">*</span>' : '';?>
                        </label>
                        <div class="col-sm-2">
                            <input type="text" class="form-control" id="user_name" placeholder="bob234"
                                   name="user_name" value="<?php
                                   echo $is_update ? $row_to_update['user_name'] :
                                       (isset($_POST['user_name']) ? $_POST['user_name'] : ''); 
                                   ?>" <?php echo $is_update ? 'disabled':'';?>>
                        </div>
                        <div class="col-sm-4 error-msg">
                            <?php echo isset($error['user_name']) ? $error['user_name'] : ''; ?> 
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label col-sm-2" for="first_name">First name 
                            <?php echo ! $is_update ? '<span class="color-remove">*</span>' : '';?>
                        </label>
                        <div class="col-sm-3">
                            <input type="text" class="form-control" id="first_name" placeholder="Bob"
                                   name="first_name" value="<?php 
                                   echo $is_update ? $row_to_update['first_name'] :
                                       (isset($_POST['first_name']) ? $_POST['first_name'] : ''); ?>">
                        </div>
                        <div class="col-sm-4 error-msg">
                            <?php echo isset($error['first_name']) ? $error['first_name'] : ''; ?> 
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label col-sm-2" for="middle_name">Middle name &nbsp;</label>
                        <div class="col-sm-3">
                            <input type="text" class="form-control" id="middle_name" placeholder="James"
                                   name="middle_name" value="<?php 
                                   echo $is_update ? $row_to_update['middle_name'] :
                                       (isset($_POST['middle_name']) ? $_POST['middle_name'] : ''); ?>">
                        </div>
                        <div class="col-sm-4 error-msg">
                            <?php echo isset($error['middle_name']) ? $error['middle_name'] : ''; ?> 
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label col-sm-2" for="last_name">Last name 
                            <?php echo ! $is_update ? '<span class="color-remove">*</span>' : '';?>
                        </label>
                        <div class="col-sm-3">
                            <input type="text" class="form-control" id="last_name" placeholder="Martin"
                                   name="last_name" value="<?php 
                                   echo $is_update ? $row_to_update['last_name'] :
                                       (isset($_POST['last_name']) ? $_POST['last_name'] : ''); ?>">
                        </div>
                        <div class="col-sm-4 error-msg">
                            <?php echo isset($error['last_name']) ? $error['last_name'] : ''; ?> 
                        </div>
                    </div>                        
                    <div class="form-group">
                        <label class="control-label col-sm-2" for="email">Email 
                        <?php echo ! $is_update ? '<span class="color-remove">*</span>' : '';?>
                        </label>
                        <div class="col-sm-3">
                            <input type="email" class="form-control" id="email" placeholder="bobjmartin@example.com"
                                   name="email" value="<?php 
                                   echo $is_update ? $row_to_update['email'] :
                                       (isset($_POST['email']) ? $_POST['email'] : ''); ?>"
                                       <?php echo $is_update ? 'disabled':'';?>>
                        </div>
                        <div class="col-sm-4 error-msg">
                            <?php echo isset($error['email']) ? $error['email'] : ''; ?> 
                        </div>
                    </div>
                    <?php if ( ! $is_update) { ?>
                    <div class="form-group">
                        <label class="control-label col-sm-2" for="pwd">Password 
                            <?php echo ! $is_update ? '<span class="color-remove">*</span>' : '';?>
                        </label>
                        <div class="col-sm-3">
                            <input type="password" class="form-control" id="pwd" placeholder="password"
                                   name="password" value="<?php 
                                   echo isset($_POST['password']) ? $_POST['password'] : ''; ?>">
                        </div>
                        <div class="col-sm-4 error-msg">
                            <?php echo isset($error['password']) ? $error['password'] : ''; ?> 
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label col-sm-2" for="confirmpwd">Confirm Password 
                            <?php echo ! $is_update ? '<span class="color-remove">*</span>' : '';?>
                        </label>
                        <div class="col-sm-3">
                            <input type="password" class="form-control" id="confirm_password" 
                                   placeholder="Confirm password" name="confirm_password"
                                   value="<?php isset($_POST['confirm_password']) ? $_POST['confirm_password'] : ''; ?>">         
                        </div>
                        <div class="col-sm-4 error-msg">
                            <?php echo isset($error['confirm_password']) ? $error['confirm_password'] : ''; ?>
                        </div>
                    </div>
                    <?php }?>
                    <div class="form-group">
                        <label class="control-label col-sm-2" for="contact_num">Contact Number 
                            <?php echo ! $is_update ? '<span class="color-remove">*</span>' : '';?>
                        </label>
                        <div class="col-sm-3">
                            <input type="text" class="form-control" id="contact_num" placeholder="9213321345" name="contact_num"
                                   value="<?php echo $is_update ? $row_to_update['contact_num'] :
                                       (isset($_POST['contact_num']) ? $_POST['contact_num'] : ''); ?>">
                        </div>
                        <div class="col-sm-4 error-msg">
                            <?php echo isset($error['contact_num']) ? $error['contact_num'] : ''; ?> 
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label col-sm-2" >Gender &nbsp;</label>
                        <div class="col-sm-10">
                            <label class="radio-inline"><input type="radio" name="gender" value="M"
                                <?php echo ($is_update && $row_to_update['gender'] === 'M') 
                                            || (isset($_POST['gender']) && ($_POST['gender'] === 'M'))
                                            ? 'checked="true"' : ''
                                ?>> Male</label>
                            <label class="radio-inline"><input type="radio" name="gender" value="F"
                                <?php echo ($is_update && $row_to_update['gender'] === 'F') 
                                        || (isset($_POST['gender']) && ($_POST['gender'] === 'F'))
                                        ? 'checked="true"' : ''
                                ?> > Female</label>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label col-sm-2" for="dob">Date of birth 
                            <?php echo ! $is_update ? '<span class="color-remove">*</span>' : '';?>
                        </label>
                        <div class="col-sm-2">
                            <input type="date" class="form-control" id="dob" name="dob"  
                                   value="<?php echo $is_update ? $row_to_update['dob'] :
                                           (isset($_POST['dob']) ? $_POST['dob'] : '1993-02-01'); ?>">
                        </div>
                        <div class="col-sm-5 error-msg">
                            <?php echo isset($error['dob']) ? $error['dob'] : ''; ?> 
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label col-sm-2" >User Type &nbsp;</label>
                        <div class="col-sm-10">
                            <label class="radio-inline"><input type="radio" name="user_type" value="3"
                                <?php echo ($is_update && $row_to_update['user_type'] === '2') 
                                        || (isset($_POST['user_type']) && ($_POST['user_type'] === '2'))
                                        ? 'checked="false"' : 'checked="true"' ?>
                                        <?php echo $is_update ? 'disabled':'';?>> Buyer</label>
                            <label class="radio-inline"><input type="radio" name="user_type" value="2"
                                 <?php echo ($is_update && $row_to_update['user_type'] === '2') 
                                            || (isset($_POST['user_type']) && ($_POST['user_type'] === '2'))
                                            ? 'checked="true"' : '' ?>
                                        <?php echo $is_update ? 'disabled':'';?>> Seller</label>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label col-sm-2" >Profile Picture &nbsp;</label>
                        <div class="col-sm-3">
                            <input type="file" name="profile_pic" id="profile_pic" />
                        </div>
                        <div class="col-sm-7 error-msg">
                            <?php
                        // Show the current picture during update
                        if ($is_update) { ?>
                               <!-- Trigger the modal with a button -->
                            <button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#myModal">Current Profile Image</button>

                            <!-- Modal -->
                            <div id="myModal" class="modal fade" role="dialog">
                              <div class="modal-dialog">

                                <!-- Modal content-->
                                <div class="modal-body" >
                                    <img src="<?php 
                                    echo empty( $row_to_update['image']) || !file_exists ( PROFILE_PIC.$row_to_update['image']) 
                                    ? NOIMAGE : PROFILE_PIC.$row_to_update['image']; ?>" 
                                    class="img-thumbnail">
                                    <div class="clearfix">
                                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                    </div>
                                </div>
                             </div>
                            </div>
                        <?php } echo isset($error['profile_pic']) ? $error['profile_pic'] : ''; ?>  
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="control-label col-sm-2" for="res_addrstate">Residence Address 
                        <?php echo ! $is_update ? '<span class="color-remove">*</span>' : '';?>
                        </label>  
                        <div class="col-sm-4">
                            <select class="form-control " id="res_addrstate" name="res_addrstate">  
                                <option value="" >Select State</option>
                                <?php
                                foreach ($state_list as $state) {
                                    echo '<option value="' . $state['id'] . '" ';
                                    echo ($is_update && $row_to_update['res_addrstate'] === $state['id']) 
                                        || (isset($_POST['res_addrstate']) && $_POST['res_addrstate'] === $state['id']) 
                                        ? 'selected ' : '';
                                    echo '>' . $state['name'] . '</option>';
                                }                                
                                ?>
                            </select>
                        </div>
                        <div class="col-sm-4 error-msg"><?php echo isset($error['res_addrstate']) ? $error['res_addrstate'] : ''; ?> </div>
                        <div class="clearfix"></div>
                        <div class="col-sm-offset-2 col-sm-4 need-spacing">
                            <input type="text" class="form-control" id="res_addrcity" placeholder="Bhubaneswar"
                                   name="res_addrcity" value="<?php 
                                        echo $is_update ? $row_to_update['res_addrcity'] :
                                       (isset($_POST['res_addrcity']) ? $_POST['res_addrcity'] : ''); ?>">
                        </div>
                        <div class="col-sm-4 need-spacing error-msg"><?php echo isset($error['res_addrcity']) ? $error['res_addrcity'] : ''; ?> </div>
                        <div class="clearfix"></div>  
                        <div class="col-sm-offset-2 col-sm-4 need-spacing">
                            <input type="text" class="form-control" id="res_addrstreet" placeholder="Gandhi Street"
                                   name="res_addrstreet" value="<?php 
                                        echo $is_update ? $row_to_update['res_addrstreet'] :
                                            (isset($_POST['res_addrstreet']) ? $_POST['res_addrstreet'] : ''); ?>">
                        </div>
                        <div class="col-sm-4 need-spacing error-msg"><?php echo isset($error['res_addrstreet']) ? $error['res_addrstreet'] : ''; ?> </div>
                        <div class="clearfix"></div>
                        <div class="col-sm-offset-2 col-sm-4 need-spacing">
                            <input type="text" class="form-control" id="res_addrzip" placeholder="748123"
                                   name="res_addrzip" value="<?php 
                                        echo $is_update ? $row_to_update['res_addrzip'] :
                                            (isset($_POST['res_addrzip']) ? $_POST['res_addrzip'] : ''); ?>">
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
                                    echo ($is_update && $row_to_update['ofc_addrstate'] === $state['id']) 
                                         ||  (isset($_POST['ofc_addrstate']) && $_POST['ofc_addrstate'] === $state['id']) ? 'selected ' : '';
                                    echo '>' . $state['name'] . '</option>';
                                }
                                
                                ?>
                            </select>
                        </div>
                        <div class="col-sm-4 error-msg"><?php echo isset($error['ofc_addrstate']) ? $error['ofc_addrstate'] : ''; ?> </div>
                        <div class="clearfix"></div>
                        <div class="col-sm-offset-2 col-sm-4 need-spacing">
                            <input type="text" class="form-control" id="ofc-addrcity" placeholder="Bhubaneswar"
                                   name="ofc_addrcity" value="<?php 
                                   echo $is_update ? $row_to_update['ofc_addrcity'] :
                                       (isset($_POST['ofc_addrcity']) ? $_POST['ofc_addrcity'] : ''); ?>">
                        </div>
                        <div class="col-sm-4 need-spacing error-msg"><?php echo isset($error['ofc_addrcity']) ? $error['ofc_addrcity'] : ''; ?> </div>
                        <div class="clearfix"></div>  
                        <div class="col-sm-offset-2 col-sm-4 need-spacing">
                            <input type="text" class="form-control" id="ofc-addrstreet" placeholder="Street-76"
                                   name="ofc_addrstreet" value="<?php 
                                        echo $is_update ? $row_to_update['ofc_addrstreet'] :
                                            (isset($_POST['ofc_addrstreet']) ? $_POST['ofc_addrstreet'] : ''); ?>">
                        </div>
                        <div class="col-sm-4 need-spacing error-msg"><?php echo isset($error['ofc_addrstreet']) ? $error['ofc_addrstreet'] : ''; ?> </div>
                        <div class="clearfix"></div>
                        <div class="col-sm-offset-2 col-sm-4 need-spacing">
                            <input type="text" class="form-control" id="ofc-addrzip" placeholder="785103"
                                   name="ofc_addrzip" value="<?php 
                                   echo $is_update ? $row_to_update['ofc_addrzip'] :
                                       (isset($_POST['ofc_addrzip']) ? $_POST['ofc_addrzip'] : ''); ?>">
                        </div>
                        <div class="col-sm-4 need-spacing error-msg"><?php 
                            echo isset($error['ofc_addrzip']) ? $error['ofc_addrzip'] : ''; ?> </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label col-sm-2" for="comment">About Me &nbsp;</label>
                        <div class="col-sm-5">
                            <textarea class="form-control" rows="5" id="comment" placeholder="Describe yourself here..."
                                      name="comment" ><?php echo $is_update ? $row_to_update['comment'] :
                                                                (isset($_POST['comment']) ? $_POST['comment'] : ''); ?></textarea>
                        </div>
                    </div> 

                    <div class="form-group">
                        <label class="control-label col-sm-2" >Preferred Communication Medium &nbsp;</label>
                        <div class="col-sm-10 give_padding" >
                            <label class="checkbox-inline"><input type="checkbox" name="pref_comm[]" value="email"
                                <?php echo ($is_update && strpos($row_to_update['preferred_comm'], 'email') !== FALSE) ||
                                        (isset($_POST['pref_comm']) && in_array("email", $_POST['pref_comm'])) ? ' checked' : ''; ?> > Email</label>
                            <label class="checkbox-inline"><input type="checkbox" name="pref_comm[]" value="sms"
                                <?php echo ($is_update && strpos($row_to_update['preferred_comm'], 'sms') !== FALSE) ||
                                    (isset($_POST['pref_comm']) && in_array("sms", $_POST['pref_comm'])) ? ' checked' : ''; ?>> SMS</label>
                            <label class="checkbox-inline"><input type="checkbox" name="pref_comm[]" value="call"
                                <?php echo ($is_update && strpos($row_to_update['preferred_comm'], 'call') !== FALSE) || 
                                    (isset($_POST['pref_comm']) && in_array("call", $_POST['pref_comm'])) ? ' checked' : ''; ?>> Call</label>
                            <label class="checkbox-inline"><input type="checkbox" name="pref_comm[]" value="any"
                                <?php echo ($is_update && strpos($row_to_update['preferred_comm'], 'any') !== FALSE) ||
                                        (isset($_POST['pref_comm']) && in_array("any", $_POST['pref_comm'])) ? ' checked' : ''; ?>> ANY</label>            
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="control-label col-sm-2" for="twitter_username">Twitter Username</label>
                        <div class="col-md-1" id="at">@</div>
                            <div class="col-sm-3">
                                <input type="text" class="form-control" id="twitter_username" placeholder="bob201tweet" name="twitter_username"
                                       value="<?php echo $is_update ? $row_to_update['twitter_username'] :
                                                    (isset($_POST['twitter_username']) ? $_POST['twitter_username'] : ''); ?>">
                            </div>
                    </div>

                    <div class="form-group">
                        <div class="col-sm-offset-2 col-sm-1">
                            <button type="submit" class="btn btn-default btn-lg btn-success"><?php echo $is_update ? 'Update': 'Submit';?></button>
                        </div>
                     
                        <div class="col-sm-offset-1 col-sm-1">
                            <button class="btn btn-default btn-lg btn-danger" <?php echo ! $is_update ? 'type="reset">Reset'
                                : 'onclick="window.location=\"my_profile.php\"">Cancel';?></button>
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
