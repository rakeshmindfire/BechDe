<?php
/**
 * Database library for QuickSeller.com
 *
 * @category   Library
 * @package    QuickSeller
 * @author     Rakesh Ranjan Das  <rakesh.das@mindfire.com>
 * @license    QuickSeller
 * @link       void
 */

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

class dbOperation {
    private $conn;
    private $query;
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
        
        if (!$this->conn) {
          $this->log_db_error('Connect');
        }
    }


    /**
     * To select a table from database
     *
     * @access public
     * @param string $table_name Name of the table to fetch data
     * @param array $fields_list List of columns required
     * @param array $where_clause Array of key as column and corresrponding value as value of 
     * that column to apply WHERE condition
     * @param array $order_by Array containing ordering column at index 0 and ordering type at index 1
     * @return void 
     */
    public function select($table_name, $fields_list=['*'], $where_clause=[], $order_by=[]) {
        $this->query = 'SELECT '.implode(',',$fields_list)
            . ' FROM ' . $table_name;

        if ($where_clause)
        {
            $this->query .= ' WHERE '.implode(array_keys($where_clause)).'= \''
                .implode(array_values($where_clause)).'\'';
        }
        
        if ($order_by)
        {
            $order = isset($order_by[1]) ? $order_by[1] : 'ASC';
            $this->query .= ' ORDER BY '.$order_by[0].' '.$order;
        }     
        $this->query_result = mysqli_query($this->conn, $this->query);
        $this->validate_result('select');
        $this->num_rows_result = mysqli_num_rows($this->query_result);
    }

    
    /**
     * To select determined attributes of all users in the database
     *
     * @access public
     * @param array $where_clause Array of key as column and corresrponding value as value of that 
     * column to apply WHERE condition
     * @param array $order_by Array containing ordering column at index 0 and ordering type at index 1
     * @return void
     */
    public function get_all_users($where_clause=[], $order_by=[]) {  
        $this->query = "SELECT u.id,CONCAT(u.first_name,' ',u.middle_name,' ',u.last_name) as full_name,"
            . "u.image,u.gender,u.dob,u.bio,u.preferred_comm,u.mobile,ua.type,"
            . "CONCAT(ua.street,', ',ua.city,', ',st.name,', ',ua.zip) as address, l.email,"
            . "CONCAT(uao.street,', ',uao.city,', ',st.name,', ',uao.zip) as office_address "
            . "FROM `users` u "
            . "JOIN user_address ua ON u.id=ua.user_id AND ua.type =1 "
            . "LEFT JOIN user_address uao ON u.id=uao.user_id AND uao.type =2 "
            . "LEFT JOIN state_table st ON ua.state= st.id OR uao.state = st.id "
            . "JOIN `login` l ON u.id=l.user_id ";
        
        if ($where_clause)
        {
            $this->query .= ' WHERE '.implode(array_keys($where_clause)).'= \''
                .implode(array_values($where_clause)).'\'';
        }
        
        if ($order_by)
        {
            $order = isset($order_by[1]) ? $order_by[1] : 'ASC';
            $this->query .= ' ORDER BY '.$order_by[0].' '.$order;
        }     
        $this->query_result = mysqli_query($this->conn, $this->query);
        $this->validate_result('get_all_users');
        $this->num_rows_result = mysqli_num_rows($this->query_result);
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
     * @param array $data Array of keys as column and corresponding value as value of that column
     *  to effect the change
     * @param array $where_clause Array of key as column and corresrponding value as value of that 
     * column to apply WHERE condition
     * @return integer when INSERT / void when UPDATE  
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
                $this->query .= ' WHERE '.implode(array_keys($where_clause)).'= \''
                    .implode(array_values($where_clause)) . '\'';
                break;
        }   
        $this->query_result = mysqli_query($this->conn, $this->query);
        $this->validate_result('insert_or_update '.$query_type);
        return $query_type == 1 ? mysqli_insert_id($this->conn): FALSE;
    }
    
    /**
     * To delete a row from a table
     *
     * @access public
     * @param string $table_name Name of the target table
     * @param array $where_clause Array of key as column and corresrponding value as value of that 
     * column to apply WHERE condition
     * @return void
     */
    public function delete($table_name, $where_clause=[]) {
        $this->query = 'DELETE FROM '.$table_name
            .' WHERE '.implode(array_keys($where_clause)).'= \''.implode(array_values($where_clause)).'\'';
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
     * @param $type type of operation SELECT, INSERT, UPDATE, DELETE
     * @return void 
     */
    public function validate_result($type) {
     
        if(!$this->query_result) {
            $this->log_db_error($type);
        };
    }
    
    /**
     * To log an error if encountered
     *
     * @access public
     * @param $type type of operation SELECT, INSERT, UPDATE, DELETE
     * @return void 
     */
    public function log_db_error($type) {
        echo 'error type '.$type;
        exit;
    }
    
    
}
?>
