<?php
/**
* To write error statements into a file  
*
* @access public
* @param string $statement Cause of the error
* @return void
*/

function error_log_file($statement, $redirect = TRUE, $is_db_err = FALSE) {
    $err_file = fopen(LOG_DIR.'log_'.date("Y-m-d").'.txt', 'a') or die('Unable to open');
    fwrite($err_file, date('h:i:sa') . ' error type: ' . $statement . "\n");
    fclose($err_file);

    // Redirect to error.php 
    if ($redirect) {
        
        if ($is_db_err) {
        // Show error page in case of db error
            header('Location: error.php');
        } else {
        // Redirect to home page or index page( when no user logged in)    
            if (isset($_SESSION['id'])) {
                header('Location: home.php');
            } else {
                header('Location: index.php');
            }
        }
        exit;
    }
}
