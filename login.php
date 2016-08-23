<?php
// Include the constant files
require_once 'helper/validation.php';
require_once 'libraries/db.php';
require_once 'libraries/session.php';

$session = new Session; 
$db = new dbOperation;

if ( ! empty($_POST)) {
  // Trim all whitespaces from string values
    $_POST = santizing($_POST);
    $error = validate_data($_POST);
    $db->select('login', ['password', 'user_id'], ['email'=>$_POST['email']]);
    $fields_validated = TRUE;

    // Check whether there is any error after validation
    foreach ($error as $error_keys => $error_messages) {

        if ( ! empty($error_messages)) {
            $fields_validated = FALSE;
            break;
        }
    }

    // Perform database operation if no errors in validation
    if ($fields_validated) {

        if ($db->num_rows_result === 0) {
           $error['email'] = 'Email address does not match any account.<a href="sign_up.php?email='
                .$_POST['email'].'">Register Now</a>';
        } else {
            $db_result = $db->fetch();
            
            if ($db_result['password'] === $_POST['password']) {
                $session->init('email', $_POST['email']);
                $session->init('id', $db_result['user_id']);
                header('Location: home.php');
            
            } else {
              $error['password'] = 'Wrong password';  
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
    <!--head-->
    <head>
        <title>QuickSeller : Log in</title>
        <?php
        require_once 'templates/header.php';
        ?>
    </head>
    <body>
        <!-- Include the navigation bar -->
        <?php require_once 'templates/navigation.php'; ?>
        <div class='confirmation margin-top120'>           
            <?php
            if (isset($_GET['success']) && 1) {
                echo "You can now login !";
            }
            ?> 
        </div>
        <section >
            <div class="container">
                <h3>Log in to QuickSeller ...</h3>
                <form class="form-horizontal" role="form" method="post" action="login.php" onsubmit="return validate_form()">
                    <div class="form-group">
                        <label class="control-label col-sm-2" for="email">Email </label>
                        <div class="col-sm-3">
                            <input type="email" class="form-control" id="email" placeholder="bobjmartin@example.com"
                                   name="email" value="<?php echo isset($_POST['email']) ? $_POST['email'] : ''; ?>">
                        </div>
                        <div class="col-sm-4 error-msg" id="email_error">
                            <?php echo isset($error['email']) ? $error['email'] : ''; ?> 
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label col-sm-2" for="pwd">Password </label>
                        <div class="col-sm-3">
                            <input type="password" class="form-control" id="pwd" placeholder="password"
                                   name="password" value="<?php echo isset($_POST['password']) ? $_POST['password'] : ''; ?>">
                        </div>
                        <div class="col-sm-4 error-msg" id="password_error">
                            <?php echo isset($error['password']) ? $error['password'] : ''; ?> 
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-sm-offset-2 col-sm-1">
                            <button type="submit" class="btn btn-default btn-lg btn-success">Log in</button>
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

</html>
