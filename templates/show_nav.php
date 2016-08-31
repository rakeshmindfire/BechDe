<?php
$nav = '';

if( ! isset($_SESSION['role'])) {
    $nav = 'templates/navigation.php';

} else {
    switch($_SESSION['role']) {
        case '1': 
            $nav = 'templates/admin_navigation.php';
            break;
        
        case '2':
            $nav = 'templates/seller_navigation.php';
            break;
        
        default:
            $nav = 'templates/navigation.php';
            break;
    }
}
require_once $nav;
?>