<?php 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once 'config/constants.php';

// Connecting to DB
$conn = mysqli_connect(SERVERNAME, USERNAME, PASSWORD, DBNAME);

// Handled case if connection failed
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$sql_getsellers="SELECT pl.id,pc.name as category_name,pl.image,pl.name as product_name,pl.amount,"
        . "pl.description,pl.created_date FROM products_list pl "
        . "JOIN products_category pc ON pl.category=pc.id WHERE pl.user_id=33 "
        . "ORDER BY pl.created_date DESC" ;
echo $sql_getsellers;

$sellers = mysqli_query($conn, $sql_getsellers);


?>
<!DOCTYPE html>
<html lang="en">
    <!--head-->
    <head>
        <title>QuickSeller:Top Sellers</title>  
        <?php
           require_once 'templates/header.php';     
        ?>
    </head>


<body >

    <!-- Include the navigation bar -->
    <?php require_once 'templates/navigation.php'; ?>
    <section>
    <div class="container table-responsive">
      <h2>Your Products</h2>
    
      <table class="table table-bordered table-condensed" >
        <thead>
          <tr>
            <th>Category</th>
            <th>Image</th>
            <th>Name</th>
            <th>Amount</th>
            <th>Description</th>
            <th>Uploaded on</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
            <?php
                if(mysqli_num_rows($products)>0){
                    while($row = mysqli_fetch_assoc($products)) {                    
                        echo '<tr> <td>'.$row['category_name'].'</td><td>';
                        echo  '<img src="'.((!is_null($row['image']) && file_exists(PRODUCT_PIC.$row['image']))
                                ?PRODUCT_PIC.$row['image']:NOIMAGE).'" alt= "product image" >';
                        echo '</td><td>'.$row['product_name'].'</td><td>'.$row['amount'].'</td><td>'
                                .$row['description'].'</td><td>'.$row['created_date'].'</td>';
                        echo '<td><a href="product_register.php?update_id='.$row['id'].'"'
                            . ' class="glyphicon glyphicon-pencil color_edit">&nbsp;'
                            . '<a href="product_list.php?delete_id='.$row['id'].'"'
                            . ' class=" glyphicon glyphicon-remove color_remove">'
                            . '</a></td></tr>';
                    }
                }
            ?>
        </tbody>
      </table>
    </div>
</section>    
</body>
</html>

