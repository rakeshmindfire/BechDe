<?php
// Include the constant files
require_once 'libraries/db.php';
require_once 'libraries/session.php';

$session = new Session;

// If session not set redirect to index.php
if ( ! $session->is_user_authorized(FALSE)) {
    error_log_file('Unauthorized access.');
}

$db = new dbOperation();

if (isset($_POST['get_permissions'])) {
    $db->select('role', ['id','name']);

    while ($role_row = $db->fetch()) {
        $role_list[] = $role_row;
    }

    $db->select('resource', ['id','name']);

    while ($resource_row = $db->fetch()) {
        $resource_list[] = $resource_row;
    }

    $db->select('permission', ['id','name']);

    while ($permission_row = $db->fetch()) {
        $permission_list[] = $permission_row;
    }

    $db->select('role_resource_permission', ['role','resource','permission']);
    $db_data_imploded = [];
    while ($permission_row = $db->fetch()) {
        $db_data_imploded = array_merge($db_data_imploded, [implode('-',  array_values($permission_row))]) ;
    }
    
    unset($_POST['get_permissions']); 
    echo json_encode(['status' => TRUE, 'role' => $role_list, 'resource' => $resource_list,
        'permission' => $permission_list, 'present_permissions_imploded' => $db_data_imploded]);
    
} elseif (isset($_POST['save_permissions'])){
    $db->delete('role_resource_permission');
    
    if(! empty ($_POST['permissions_data'])) {
        foreach($_POST['permissions_data'] as $values) {
            $relation_values = explode('-', $values);
            $data_list = array_combine(['role','resource','permission'], $relation_values);
            $db->insert_or_update(1 , 'role_resource_permission', $data_list);
        } 
    }
 
    unset($_POST['save_permissions']);
    unset($_POST['permissions_data']);
    echo json_encode(['status' => TRUE]);
}

?>
