<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

/**
 * Database library for QuickSeller.com
 *
 * @category   Library
 * @package    QuickSeller
 * @author     Rakesh Ranjan Das  <rakesh.das@mindfiresolutions.com>
 * @license    QuickSeller
 * @link       void
 */
require_once 'config/constants.php';
require_once 'helper/error_log.php';

class dbOperation {
    private $conn;
    public $query;
    private $query_result;
    public $num_rows_result;
    
    /**
     * To create a database connection when an object is created
     *
     * @access public
     * @param void
     * @return void 
     */
    public function __construct() {
        $this->conn = mysqli_connect(SERVERNAME, USERNAME, PASSWORD, DBNAME);
        
        if ( ! $this->conn) {
          $this->log_db_error('Connect');
        }
    }

    /**
     * To select a table from database
     *
     * @access public
     * @param string $table_name Name of the table to fetch data
     * @param array $fields_list List of columns required
     * @param array $where_clause Array of key as column and corresponding value for where condition
     * @param array $order_by Array of ordering column at index 0 and ordering type at index 1
     * @return void 
     */
    public function select($table_name, $fields_list=['*'], $where_clause=[], $order_by=[], $limit=[]) {
        $this->query = 'SELECT '.implode(',',$fields_list)
            . ' FROM ' . $table_name;

        if ($where_clause) {
            $this->query .= ' WHERE ';
            $where_keys = array_keys($where_clause);
            $where_values = array_values($where_clause);
            for ( $i=0 ; $i < sizeof($where_clause); $i++) {
                $this->query .=  $where_keys[$i].'= \''.$where_values[$i].'\' AND ';
            }
            $this->query = rtrim($this->query,'AND ');

        }
        
        if ($order_by) {
            $order = isset($order_by[1]) ? $order_by[1] : 'ASC';
            $this->query .= ' ORDER BY '.$order_by[0].' '.$order;
        } 
        
        if ($limit) {
            $this->query .= ' LIMIT ';
            
            if (isset($limit[1])) {
                $this->query .= $limit[1] . ' , ';
            }
            
            $this->query .=  $limit[0];
        } 
        
        $this->query_result = mysqli_query($this->conn, $this->query);
        $this->validate_result('select'.$this->query);
        $this->num_rows_result = mysqli_num_rows($this->query_result);
    }
   
    /**
     * To select determined attributes of all users in the database
     *
     * @access public
     * @param array $where_clause Array of key as column and corresponding value for where condition
     * @param array $order_by Array of ordering column at index 0 and ordering type at index 1
     * @return void
     */
    public function get_all_users($where_clause=[], $order_by=[]) {  
        $this->query = 'SELECT u.user_name,l.email,u.id,u.first_name,u.middle_name,u.last_name,u.image,u.gender,'
            .'u.dob,u.bio AS comment,u.preferred_comm,u.mobile AS contact_num,'
            . 'uar.street AS res_addrstreet,uar.city AS res_addrcity,'
            .'u.type AS user_type,uar.state AS res_addrstate,st_uar.name AS res_addrstate_name,uar.zip AS res_addrzip,'
            .'uao.street AS ofc_addrstreet,uao.city AS ofc_addrcity,uao.state AS ofc_addrstate,'
            .'st_uao.name AS ofc_addrstate_name,uao.zip AS ofc_addrzip '
            .'FROM user_address uar '
            .'LEFT JOIN user_address uao ON uar.user_id=uao.user_id AND uao.type=2 '
            .'JOIN state_table st_uar ON st_uar.id=uar.state '
            .'LEFT JOIN state_table st_uao ON st_uao.id=uao.state '
            .'JOIN users u ON u.id=uar.user_id ' 
            .'JOIN login l ON u.id=l.user_id WHERE uar.type=1';
        
         if ($where_clause) {
            $this->query .= ' AND ';
            $where_keys = array_keys($where_clause);
            $where_values = array_values($where_clause);
            
            for ( $i=0 ; $i < sizeof($where_clause); $i++) {
                $this->query .=  $where_keys[$i].'= \''.$where_values[$i].'\' AND ';
            }
            $this->query = rtrim($this->query,'AND ');
        }
        
        if ($order_by) {
            $order = isset($order_by[1]) ? $order_by[1] : 'ASC';
            $this->query .= ' ORDER BY '.$order_by[0].' '.$order;
        }
        
        $this->query_result = mysqli_query($this->conn, $this->query);
        $this->validate_result('get_all_users');
        $this->num_rows_result = mysqli_num_rows($this->query_result);
    }
    
    /**
     * To select determined attributes of all users in the database
     *
     * @access public
     * @param array $where_clause Array of key as column and corresponding value for where condition
     * @param array $order_by Array of ordering column at index 0 and ordering type at index 1
     * @return void
     */
    public function permissions_exist($role, $resource, $permission) {  
        $this->query = 'SELECT rrp.role, re.name, p.name'
                     . ' FROM `role_resource_permission` rrp'
                     . ' JOIN role ro ON rrp.role = ro.id'
                     . ' JOIN resource re ON rrp.resource = re.id'
                     . ' JOIN permission p ON rrp.permission = p.id'
                     . ' WHERE rrp.role=' . $role . ' AND re.name="' . $resource .'" AND p.name="' . $permission .'"';

        $this->query_result = mysqli_query($this->conn, $this->query);
        $this->validate_result('permissions_exist');
        $this->num_rows_result = mysqli_num_rows($this->query_result);         
        return $this->num_rows_result > 0;
    }
    
    /**
     * To obtain rows after SELECT operation
     *
     * @access public
     * @return array 
     */
    public function fetch() {
        return (mysqli_fetch_assoc($this->query_result));
    }
      
    /**
     * To insert into or update a table  
     *
     * @access public
     * @param integer $query_type Type of operation: 1 for INSERT, 2 for UPDATE
     * @param string $table_name Name of the target table
     * @param array $data Array of keys as column and corresponding value as value
     * @param array $where_clause Array of key as column and corresponding value for where condition
     * @return  integer/void insert/update
     */
    public function insert_or_update($query_type, $table_name, $data, $where_clause=[]) {
        
        switch($query_type) {
            
            case 1:
                $this->query = 'INSERT INTO '.$table_name.' ('.implode(',',array_keys($data)).',created_date)'
                        . ' VALUES (\''.implode('\',\'',array_values($data)).'\',NOW())';
                break;            
            case 2:
                $this->query = 'UPDATE '.$table_name.' SET' ;
         
                foreach ($data as $attr => $value) {
                    $this->query .=' '.$attr.'="'.$value.'",';
                }
                
                $this->query = rtrim($this->query,',');
                
                if ( ! empty($where_clause)) {
                    $this->query .= ' WHERE '.implode(array_keys($where_clause)).'= \''
                        .implode(array_values($where_clause)) . '\'';
                }
                
                break;
            
            default: 
                $this->log_db_error('Unknown argument in dbOperation::insert_or_update()');
                break;
        }   

        $this->query_result = mysqli_query($this->conn, $this->query);
        $this->validate_result('insert_or_update '.$query_type);
        return $query_type === 1 ? mysqli_insert_id($this->conn): FALSE;
    }
    
    /**
     * To delete a row from a table
     *
     * @access public
     * @param string $table_name Name of the target table
     * @param array $where_clause Array of key as column and corresponding value for where condition
     * @return void
     */
    public function delete($table_name, $where_clause=[]) {
        $this->query = 'DELETE FROM '.$table_name;
       
        if ($where_clause) {
            $this->query .= ' WHERE ';
            $where_keys = array_keys($where_clause);
            $where_values = array_values($where_clause);
            for ( $i=0 ; $i < sizeof($where_clause); $i++) {
                $this->query .=  $where_keys[$i].'= \''.$where_values[$i].'\' AND ';
            }
            $this->query = rtrim($this->query,'AND ');
        }
        
        $this->query_result = mysqli_query($this->conn, $this->query);
        $this->validate_result('delete');
    }
    
    /**
     * To close connection when an object is destroyed  
     *
     * @access public
     * @return void 
     */
    public function __destruct() {
        mysqli_close($this->conn);
    }
    
    /**
     * To check if an operation is successfully executed
     *
     * @access public
     * @param string $type type of operation SELECT, INSERT, UPDATE, DELETE
     * @return void 
     */
    public function validate_result($type) {
     
        if ( ! $this->query_result) {
            $this->log_db_error($type. $this->query);
        }
    }
    
    /**
     * To log an error if encountered
     *
     * @access public
     * @param string $type type of operation SELECT, INSERT, UPDATE, DELETE
     * @return void 
     */
    public function log_db_error($type) {
        error_log_file('db error '.$type);
    }
}
?>
