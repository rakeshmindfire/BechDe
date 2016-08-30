<?php
require_once 'libraries/db.php';
require_once 'libraries/session.php';

$session = new Session; 

// Get data of all the sellers
$db = new dbOperation;
$db->get_all_users(['u.type'=>'2'], ['u.id','ASC']);
?>
<!DOCTYPE html>
<html lang="en">
    <!--head-->
    <head>
        <title>QuickSeller:Top Sellers</title>  
        <?php
        require_once 'templates/header.php';
        ?>

    </head>
    <body >
        <!-- Include the navigation bar -->
        <?php require_once 'templates/show_nav.php'; ?>
        
        <div class='confirmation margin-top120'> </div>
        <div class="container">
            <h2>Top Sellers</h2>
            <?php if ($db->num_rows_result > 0) { ?>
                <div class="panel-group" id="accordion">
                    <?php while ($seller = $db->fetch()) { //print_r($seller); ?>

                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h4 class="panel-title">
                                    <a data-toggle="collapse" data-parent="#accordion" href="#<?php echo $seller['id'] ?>"><?php 
                                    echo $seller['first_name'].' '.$seller['middle_name'].' '.$seller['last_name'] ?></a>
                                </h4>
                            </div>
                            <div id="<?php echo $seller['id'] ?>" class="panel-collapse collapse ">
                                <div class="panel-body">
                                    <div class="row">
                                        <img src="<?php echo empty($seller['image']) || !file_exists(PROFILE_PIC . $seller['image']) ? NOIMAGE : PROFILE_PIC . $seller['image']; ?>" class="col-xs-2 img-circle">
                                        <div class="col-md-9"> 
                                            <div>
                                                <b>Sex : </b><?php echo $seller['gender'] === 'M' ? 'Male' : 'Female'; ?>
                                            </div>
                                            <div>
                                                <b>DOB : </b><?php echo $seller['dob']; ?>
                                            </div>
                                            <div>
                                                <b>Mobile : </b><?php echo $seller['contact_num']; ?>
                                            </div>
                                            <div>
                                                <b>About : </b><?php echo empty($seller['comment']) ? 'None' : $seller['comment']; ?>
                                            </div>
                                            <div>
                                                <b>Preferred communication : </b><?php echo empty($seller['preferred_comm']) ? 'None' : $seller['preferred_comm']; ?>
                                            </div>
                                            <div>
                                                <b>Residence Address : </b>
                                                    <?php
                                                    echo $seller['res_addrstreet'].','.$seller['res_addrcity'].','.$seller['res_addrstate_name'].','.$seller['res_addrzip']; 
                                                    ?>
                                            </div>
                                            <div>
                                                <b>Office Address : </b>
                                                    <?php
                                                    echo ! empty($seller['ofc_addrstreet']) || ! empty($seller['ofc_addrcity']) || ! empty ($seller['ofc_addrstate_name']) || ! empty($seller['ofc_addrzip'])
                                                            ?  $seller['ofc_addrstreet'].','.$seller['ofc_addrcity'].','.$seller['ofc_addrstate_name'].','.$seller['ofc_addrzip'] : 'Not available' ;  ?>
                                            </div>
                                            <div>
                                                <b>Email : </b><?php echo $seller['email']; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

        <?php
    }
} else {
    ?>
                    <h2>There are no sellers currently. </h2>
                <?php } ?>   
            </div>
        </div>
<?php require_once 'templates/footer.php'; ?>
    </body>
</html>

