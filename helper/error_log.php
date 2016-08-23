<?php
/**
* To write error statements in a file  
*
* @access public
* @param string $statement Cause of the error
* @return  void
*/
function error_log_file($statement) {
    $err_file = fopen(LOG_DIR.'log_'.date("Y-m-d").'.txt', 'a') || die('Unable to open');
    fwrite($err_file,date("h:i:sa").' error type: '.$statement."\n");
    fclose($err_file);
    header('Location: logout.php');
}