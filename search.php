<?php
require_once 'libraries/db.php';
require_once 'libraries/session.php';

$session = new Session;

if (isset($_POST['delete_id'])) {
    $action = 'delete';

} else if (isset($_POST['change_id'])) {
    $action = 'edit';

} else {
    $action = 'view';
} 

// If session not set redirect to index.php
if ( ! $session->is_user_authorized(TRUE, 'products', $action)) {
    error_log_file('Unauthorized access.');
}

$is_admin_or_buyer = ($_SESSION['role'] === '1' || $_SESSION['role'] === '3') ? TRUE: FALSE;

$db = new dbOperation;
$result = [];

if (isset($_GET['get_list']) && $_GET['get_list'] === '1') {
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
     unset ($_POST['change_id']);

} else if (isset($_POST['purchase_id'])) {

    
    $items = explode(',', trim($_POST['purchase_id'],'[]'));
    
    foreach ( $items as $item) {
        $pur_id = trim($item, '"');
        /**
        * Check if purchase id is valid 
        */
        $db->select('products_list', ['id'], ['is_active'=> 1, 'is_avail'=> 1, 'id'=> $pur_id]);
        
        if( $db->num_rows_result !== 1) {
            $stat = 0;
            break;
        
        } else {
            $stat = 1;
            $db->insert_or_update(2, 'products_list', ['is_active'=> 0, 'is_avail' => 0], ['id'=> $pur_id]);
            $db->insert_or_update(1, 'purchased', ['buyer_id'=> $_SESSION['id'], 'product_id' => $pur_id]);
        }
    }

    unset ($_POST['purchase_id']);
    echo json_encode(['status' => $stat]);

} else if (isset($_POST['get_user'])) {
    $db->get_all_users(['u.id'=>$_POST['get_user']]);
    echo json_encode(['status' => 1, 'result' => $db->fetch()]);
    unset ($_POST['get_user']);

} else if (isset($_POST['get_product'])) {
    $table = 'products_list pl JOIN users u ON u.id=pl.user_id JOIN products_category pc ON pl.category=pc.id ' ;
    $attr_list = ['pl.id', 'pc.name AS category_name', 'pl.image', 'pl.name AS product_name',
        'pl.amount', 'pl.description', 'pl.created_date',
        'CONCAT(u.first_name,\' \',u.middle_name,\' \',u.last_name) AS seller_name', 'u.id AS seller_id'];
    $where = ['pl.id' => $_POST['get_product']];
    $db->select($table, $attr_list, $where);
    echo json_encode(['status' => 1, 'result' => $db->fetch()]);
    unset ($_POST['get_product']);
    
} else if (isset($_POST['get_buyer_of'])) {
        $table = 'purchased pd JOIN users u ON pd.buyer_id=u.id';
        $attr_list = ['buyer_id'];        
        $where = ['pd.product_id' => $_POST['get_buyer_of']];
        $db->select($table, $attr_list, $where);
        unset ($_POST['get_buyer_of']);
        echo json_encode(['status' => 1, 'result' => $db->fetch()]);
        
} else if (isset($_POST['is_history'])) {
    $table = 'purchased pd JOIN products_list pl ON pl.id=pd.product_id JOIN users u ON u.id=pl.user_id'
        . ' JOIN products_category pc ON pc.id=pl.category';
    $attr_list = ['pl.id', 'pc.name AS category_name', 'pl.image', 'pl.name AS product_name',
        'pl.amount', 'pl.description', 'pl.created_date',
        'CONCAT(u.first_name,\' \',u.middle_name,\' \',u.last_name) AS seller_name', 'u.id AS seller_id',
        'pd.created_date AS purchase_date'];
    $where = ['pd.buyer_id' => $_SESSION['id']];
    $db->select($table, $attr_list, $where);
    
     while($row = $db->fetch()) {
        $result[] = $row;
    }
    
    echo json_encode(['status' => 1, 'result' => $result]);
    unset ($_POST['is_history']);

} else {
    
    // Get total products present in the user account
    $total_products_of_user = 0;
    $db->select('products_list pl',['COUNT(*) AS total_products_of_user'],
            $is_admin_or_buyer ? []: ['pl.user_id' => $_SESSION['id']]);
    
    while($row = $db->fetch()) {
        $total_products_of_user = $row['total_products_of_user'];
    }
    
    $total = 0;
    // Get total records
    
    $where_clause =[];
    
    switch ($_POST['status']) {
        case '1' :
            $where_clause = ['is_active' => $_POST['status']];
            break;
        
        case '0':
            $where_clause = ['is_active' => $_POST['status'], 'is_avail' => 1 ];
            break;
        
        case '3':
            $where_clause = ['is_avail' => 0 ];
            break;
        
        default :
            error_log_file('Wrong parameter');
            break;
    }
    
    if ( isset($_POST['id']) && $_POST['id'] !=='0') {
        $where_clause['pc.id'] = $_POST['id'];
    }
    
    $table = 'products_list pl JOIN users u ON u.id=pl.user_id JOIN products_category pc ON pl.category=pc.id '
            . ($is_admin_or_buyer ? '': 'AND pl.user_id=' . $_SESSION['id']) ;
            
    
    // Get count of total number of products based on filter criteria
    $db->select($table, ['COUNT(*) AS total'], $where_clause);
    
    while($row = $db->fetch()) {
        $total = $row['total'];
    }

    // Set parameters for fetching data
    $attr_list = ['pl.id', 'pc.name AS category_name', 'pl.image', 'pl.name AS product_name',
        'pl.amount', 'pl.description', 'pl.created_date',
        'CONCAT(u.first_name,\' \',u.middle_name,\' \',u.last_name) AS name', 'u.id AS seller_id'];
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
