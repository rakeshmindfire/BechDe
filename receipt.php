<?php

require_once 'libraries/db.php';
require_once 'libraries/encrypt_decrypt.php';

$receipt_id = simple_decrypt($_GET['id']);

// If fetch id is not an integer then show failure
if ( ! is_int($receipt_id)) {
    error_log_file('Invalid receipt id', FALSE);
}

$db = new dbOperation();
$db->select('purchased', ['items'], ['id' => $receipt_id]);
$items_list = $db->fetch() ['items'];
$db->select('products_list pl JOIN users u ON pl.buyer=u.id',
        ['name', 'amount', 'CONCAT(u.first_name,\' \',u.middle_name,\' \',u.last_name) AS buyer_name',
            'pl.updated_date'],
        ['pl.id' => $items_list], [], [], TRUE);

$result = [];

while($row = $db->fetch()) {
    $result[] = $row;
}


$buyer = $result[0]['buyer_name']; 
$db_date = $result[0]['updated_date'] ;
$delivery_date =  strtotime($db_date);


//Generate text file on the fly
header('Content-type: text/html');
header('Content-Disposition: attachment; filename=QuickSellerReceipt-'. $receipt_id . '-' . $result[0]['updated_date'] .'.html');

?>

<!DOCTYPE html>
<html lang="en">

    <head>
        <title>Payment Receipt</title>
        <style>

        </style>
    </head>
    <body>
        <h2>QUICKSELLER</h2>
        <h4>Receipt No:<?php echo $receipt_id?></h4>
        Hi <?php echo $buyer?> ,<br>
        You have purchased following items<br>
        <table border="1">
            <thead>
                <tr>
                    <td><b>Name</b></td>
                    <td><b>Price</b></td>
                </tr>
            </thead>
            <tbody>
                <?php
                    // Items list
                $total_amount = 0;
                for ($i=0; $i<sizeof($result);$i++) {
                    echo '<tr><td>'. $result[$i]['name'] 
                            .'</td><td>'. number_format($result[$i]['amount'], 2) .'</td></tr>';
                    $total_amount += $result[$i]['amount'];
                }
                echo '<tr><td>TOTAL</td><td>'. number_format($total_amount,2) .'</td>';
                ?>
            </tbody>
        </table>
        <br>
        Your items will be delivered by <?php echo date("d-m-Y", $delivery_date + (7*24*60*60));?><br>
        Thank you,<br>
        The QuickSeller Team
    </body>
      
    </head>
