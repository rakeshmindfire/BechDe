<?php
/**
 * Session library for QuickSeller.com
 *
 * @category   Library
 * @package    QuickSeller
 * @author     Rakesh Ranjan Das  <rakesh.das@mindfiresolutions.com>
 * @license    QuickSeller
 * @link       void
 */

class Session {
    /**
     * To start session on creation of the object
     *
     * @access public
     * @return void
     */
    public function __construct() {
        session_start();
    }
    
    /**
     * To initialise a $_SESSION variable
     *
     * @access public
     * @param string $key Key in $_SESSION
     * @param string $value Value in $_SESSION
     * @return void
     */
    public function init($key, $value) {
        $_SESSION[$key] = $value;
    }
    
    /**
     * To check if a session is existing 
     *
     * @access public
     * @return boolean
     */
    public function check_session() {
        return ! empty($_SESSION);
    }

    /**
     * To close session on sign out 
     *
     * @access public
     * @return void
     */
    public function sign_out() {
        session_unset();
        session_destroy();
    }   
}
?>
