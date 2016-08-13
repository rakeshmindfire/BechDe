<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

class dbOperation {
    private $conn;
    public $query;
    public $query_result;
    public $num_rows_result;
    
    public function __construct() {
        $this->conn = mysqli_connect(SERVERNAME, USERNAME, PASSWORD, DBNAME);
        
        if (!$this->conn) {
          $this->log_db_error('Connect');
        }
    }

    public function select($table_name, $fields_list=['*'], $where_clause=[], $order_by=[]) {
        $this->query = 'SELECT '.implode(',',$fields_list)
            . ' FROM ' . $table_name;

        if ($where_clause)
        {
            $this->query .= ' WHERE '.implode(array_keys($where_clause)).'= \''.implode(array_values($where_clause)).'\'';
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
            $this->query .= ' WHERE '.implode(array_keys($where_clause)).'= \''.implode(array_values($where_clause)).'\'';
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
    
    public function fetch() {
        return (mysqli_fetch_assoc($this->query_result));
    }
    
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
                $this->query .= ' WHERE '.implode(array_keys($where_clause)).'= \''.implode(array_values($where_clause)) . '\'';
                break;
        }   
        $this->query_result = mysqli_query($this->conn, $this->query);
        $this->validate_result('insert_or_update '.$query_type);
        return $query_type == 1 ? mysqli_insert_id($this->conn): FALSE;
    }
    
    public function delete($table_name, $where_clause=[]) {
        $this->query = 'DELETE FROM '.$table_name
            .' WHERE '.implode(array_keys($where_clause)).'= \''.implode(array_values($where_clause)).'\'';
        $this->query_result = mysqli_query($this->conn, $this->query);
        $this->validate_result('delete');
    }
    
    public function __destruct() {
        mysqli_close($this->conn);
    }
    
    public function validate_result($type) {
     
        if(!$this->query_result) {
            $this->log_db_error($type);
        };
    }
    
    public function log_db_error($type) {
        echo 'error type '.$type;
        exit;
    }
    
    
}


?>
