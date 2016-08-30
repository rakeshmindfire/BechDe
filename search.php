<?php
require_once 'libraries/db.php';
require_once 'libraries/session.php';

$session = new Session;

// If session not set redirect to index.php
if ( ! $session->check_session()) {
    error_log_file('Unauthorized access. Session not set in search.php');
}

$db = new dbOperation;
$result = [];

if(isset($_GET['get_list']) && $_GET['get_list'] === '1') {
    $db->select('products_category');
    
    while($row = $db->fetch()) {
        $product_list[] = $row;
    }

    unset($_GET['get_list']);
    echo json_encode($product_list);
    
} else if (isset($_POST['delete_id'])) {
   // Check if product belongs to that user
    $db->select('products_list', ['user_id'], ['id'=>$_POST['delete_id']]);
    $res_user_id = $db->fetch();
    $status = FALSE;

    // Logging out User if try to update any other user product
    if ($res_user_id['user_id'] === $_SESSION['id']) {
        
        $db->select('products_list', ['image'],['id'=>$_POST['delete_id']]);
        $image_to_delete = $db->fetch();         

        if ( ! is_null($image_to_delete['image']) && file_exists(PRODUCT_PIC.$image_to_delete['image'])) {
            unlink(PRODUCT_PIC.$image_to_delete['image']);
        }

        $db->delete('products_list', ['id' => $_POST['delete_id']]);
        unset ($_POST['delete_id']);
        $status = TRUE;
    }

    echo json_encode(['status' => $status]);

} else if (isset($_POST['change_id'])) {
    $db->insert_or_update(2, 'products_list', ['is_active'=> ($_POST['status']+1)%2], ['id'=>$_POST['change_id']]);
   
} else {
    
    // Get total products present in the user account
    $total_products_of_user = 0;
    $db->select('products_list pl',['count(*) AS total_products_of_user'], ['pl.user_id' => $_SESSION['id']]);
    
    while($row = $db->fetch()) {
        $total_products_of_user = $row['total_products_of_user'];
    }
    
    $total = 0;
    // Get total records
    $where_clause = ['is_active'=>$_POST['status']];
    
    if ( $_POST['id'] !=='0') {
        $where_clause['pc.id'] = $_POST['id'];
    }
    
    // Fetch count of total number of products
    $db->select('products_list pl JOIN products_category pc ON pl.category=pc.id AND pl.user_id='.$_SESSION['id'],
            ['count(*) AS total'], $where_clause);
    
    while($row = $db->fetch()) {
        $total = $row['total'];
    }

    // Set parameters for fetching data
    $table = 'products_list pl JOIN products_category pc ON pl.category=pc.id AND pl.user_id='.$_SESSION['id'] ;
    $attr_list = ['pl.id', 'pc.name as category_name', 'pl.image', 'pl.name as product_name',
        'pl.amount', 'pl.description', 'pl.created_date'];
    $where = $_POST['id']==='0' ? NULL : ['pc.id'=>$_POST['id']];
    $order =  $_POST['order_in'] === '1' ? ['pl.created_date', 'DESC'] :
        ($_POST['order_in'] === '2' ? ['pl.amount', 'ASC'] : ['pl.amount', 'DESC']);
    $rows = $_POST['no_of_rows'];
    $limit_offset = (intval($_POST['start_row']) - 1) * $rows;
  
    // Get the data based on page number
    $db->select($table, $attr_list, $where_clause, $order, [$rows, $limit_offset]);
    
    while($row = $db->fetch()) {
        $result[] = $row;
    }

    echo json_encode(['status' => $total > 0, 'result' => $result, 'total' => $total,
        'products_exist' => $total_products_of_user > 0]);

}
