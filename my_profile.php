<?php
// Include the constant files
require_once 'libraries/db.php';
require_once 'libraries/session.php';

$session = new Session;

// If session not set redirect to index.php
if ( ! $session->is_user_authorized()) {
    error_log_file('Unauthorized access. Session not set');
}

// Fetch all details of the current user
$db = new dbOperation;
$db->get_all_users(['u.id'=>$_SESSION['id']]);
$profile = $db->fetch();
?>
<!DOCTYPE html>
<html lang="en">
    <!--head-->
    <head>
        <title>QuickSeller:My Profile</title>  
        <?php
        require_once 'templates/header.php';
        ?>

    </head>
    <body >
        <!-- Include the navigation bar -->
       <?php require_once 'templates/show_nav.php'; ?>
        
        <div class='confirmation margin-top120'> </div>
        <div class="container">
            <h2>My Profile</h2>

            <div id="<?php echo $profile['id'] ?>" class="panel-collapse collapse-in ">
                <div class="panel-body">
                    <div class="row">
                        <img src="<?php echo empty($profile['image']) || !file_exists(PROFILE_PIC . $profile['image']) ? NOIMAGE : PROFILE_PIC . $profile['image']; ?>" class="col-xs-2 img-rounded">
                        <div class="col-md-9"> 
                            <div>
                                <b>Name : </b><?php echo $profile['first_name'].' '.$profile['middle_name'].' '.$profile['last_name'] ?>
                            </div>
                            <div>
                                <b>Sex : </b><?php echo $profile['gender'] === 'M' ? 'Male' : 'Female'; ?>
                            </div>
                            <div>
                                <b>DOB : </b><?php echo $profile['dob']; ?>
                            </div>
                            <div>
                                <b>Mobile : </b><?php echo $profile['contact_num']; ?>
                            </div>
                            <div>
                                <b>About : </b><?php echo empty($profile['comment']) ? 'None' : $profile['comment']; ?>
                            </div>
                            <div>
                                <b>Preferred communication : </b><?php echo empty($profile['preferred_comm']) ? 'None' : $profile['preferred_comm']; ?>
                            </div>
                            <div>
                                <b>Residence Address : </b>
                                    <?php
                                    echo $profile['res_addrstreet'].','.$profile['res_addrcity'].','.$profile['res_addrstate_name'].','.$profile['res_addrzip']; 
                                    ?>
                            </div>
                            <div>
                                <b>Office Address : </b>
                                    <?php
                                    echo ! empty($profile['ofc_addrstreet']) || ! empty($profile['ofc_addrcity']) || ! empty ($profile['ofc_addrstate_name']) || ! empty($profile['ofc_addrzip'])
                                            ?  $profile['ofc_addrstreet'].','.$profile['ofc_addrcity'].','.$profile['ofc_addrstate_name'].','.$profile['ofc_addrzip'] : 'Not available' ;  ?>
                            </div>
                            <div>
                                <b>Email : </b><?php echo $profile['email']; ?>
                            </div>
                        </div>
                    </div>

                </div>
                 
            </div>
            <a type="button" class="btn btn-lg btn-success col-sm-offset-3 " href="sign_up.php?user=<?php echo $_SESSION['email']?>">Edit Profile</a>  
        </div>
<?php require_once 'templates/footer.php'; ?>
    </body>
</html>

