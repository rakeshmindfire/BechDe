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

require_once 'libraries/db.php';

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
    
    public function validate_session() {
        return ( ! empty($_SESSION['id']));
    }
    
    /**
     * To check if a session is existing 
     *
     * @access public
     * @return boolean
     */
    public function is_user_authorized($resource = '', $perm = '') {
        $access = FALSE;

        if ($this->validate_session()) {
            
            if ($_SESSION['role'] === '1') {
                $access = TRUE;
            
            } else if (empty($resource) && empty($perm)) {
                $access = TRUE;
                
            } else {
                $db = new dbOperation();
                $access = $db->permissions_exist($_SESSION['role'], $resource, $perm);
            }
        }

        return $access;

        /**
         * if both empty - any page (role not matter)
         * if both present( not admin)- check accessibility of page based on role 
         *                Yes - go ahead
         *              No - redirect to home page
         * if both present(admin) - No check
         **/
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
