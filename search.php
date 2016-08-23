<?php
require_once 'libraries/db.php';
require_once 'libraries/session.php';

$session = new Session;
$db = new dbOperation;
$result = [];

if(isset($_GET['get_list']) && $_GET['get_list'] === '1') {
    $db->select('products_category');
    
    while($row = $db->fetch()) {
        $product_list[] = $row;
    };
    unset($_GET['get_list']);
    echo (json_encode($product_list));
    
} else if (isset($_POST['delete_id'])) {
   // Check if product belongs to that user
    $db->select('products_list', ['user_id'], ['id'=>$_POST['delete_id']]);
    $res_user_id = $db->fetch();
    
    // Logging out User if try to update any other user product
    if ($res_user_id['user_id'] !== $_SESSION['id']) {
        $status = FALSE;
    
    } else {

        $db->select('products_list', ['image'],['id'=>$_POST['delete_id']]);
        $image_to_delete = $db->fetch();         

        if ( ! is_null($image_to_delete['image']) && file_exists(PRODUCT_PIC.$image_to_delete['image'])) {
            unlink(PRODUCT_PIC.$image_to_delete['image']);
        }

        $db->delete('products_list', ['id'=>$_POST['delete_id']]);
        unset ($_POST['delete_id']);
        $status = TRUE;
    }
    echo (json_encode(['status' => $status]));
  
} else {
    
    $db->select('products_list pl JOIN products_category pc ON pl.category=pc.id AND pl.user_id='.$_SESSION['id'] ,
    ['pl.id', 'pc.name as category_name', 'pl.image', 'pl.name as product_name', 'pl.amount', 'pl.description', 'pl.created_date'],
    $_POST['id']==='0' ? NULL : ['pc.id'=>$_POST['id']], ['pl.created_date', 'DESC']);
    
    while($row = $db->fetch()) {
        $result[] = $row;
    };

    $status = ! empty($result) ? TRUE : FALSE;
    echo json_encode(['status' => $status, 'result' => $result]);

}




