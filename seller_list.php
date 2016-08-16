<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once 'config/constants.php';
require_once 'libraries/db.php';

$db = new dbOperation;
$db->get_all_users(['u.type'=>'S'], ['u.id','ASC']);

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
        <?php require_once 'templates/navigation.php'; ?>
        <div class='confirmation margin-top120'> </div>
        <div class="container">
            <h2>Top Sellers</h2>

            <?php if ($db->num_rows_result > 0) { ?>
                <div class="panel-group" id="accordion">
                    <?php while ($seller = $db->fetch()) { ?>

                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h4 class="panel-title">
                                    <a data-toggle="collapse" data-parent="#accordion" href="#<?php echo $seller['id'] ?>"><?php echo $seller['full_name'] ?></a>
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
                                                <b>Mobile : </b><?php echo $seller['mobile']; ?>
                                            </div>
                                            <div>
                                                <b>About : </b><?php echo empty($seller['bio']) ? 'None' : $seller['bio']; ?>
                                            </div>
                                            <div>
                                                <b>Preferred communication : </b><?php echo empty($seller['preferred_comm']) ? 'None' : $seller['preferred_comm']; ?>
                                            </div>
                                            <div>
                                                <b>Residence Address : </b><?php echo $seller['address']; ?>
                                            </div>
                                            <div>
                                                <b>Office Address : </b><?php echo empty($seller['office_address']) ? 'Not Available' : $seller['office_address']; ?>
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

