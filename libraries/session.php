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
    
    /**
     * To check if user is logged in
     *
     * @access public
     * @return boolean
     */
    public function validate_session() {
        return ( ! empty($_SESSION['id']));
    }
    
    /**
     * To check if a session is existing 
     *
     * @access public
     * @param boolean $is_user_only Specifies if a non-admin user is allowed
     * @param string $resource Specifies the type of resource user is trying to access
     * @param string $perm Specifies the permission required to access the resource
     * @return boolean
     */
    public function is_user_authorized($is_user_only=TRUE, $resource='', $perm='') {
        $access = FALSE;

        if ($this->validate_session()) {
            // Allow access if admin or if not specified
            if ($_SESSION['role'] === '1' || (empty($resource) && empty($perm) && $is_user_only)) {
                $access = TRUE;
           
            // Check if the user has permission
            } else {
                $db = new dbOperation();
                $access = $db->permissions_exist($_SESSION['role'], $resource, $perm);
            }
        }

        return $access;
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

