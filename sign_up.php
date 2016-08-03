<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="">
        <meta name="author" content="">

        <title>QuickSeller : Sign up</title>

        <!-- Bootstrap Core CSS -->
        <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">

        <!-- Custom Fonts -->
        <link href='https://fonts.googleapis.com/css?family=Sansita+One' rel='stylesheet' type='text/css'>
        <link href="https://fonts.googleapis.com/css?family=Montserrat:400,700" rel="stylesheet" type="text/css">

        <!-- Theme CSS -->
        <link href="css/theme.css" rel="stylesheet">
        <link href="css/style.css" rel="stylesheet">
    </head>
    <?php include 'helper/states.php'?>
<body id="sign_up">

    <!-- Navigation -->
    <nav id="mainNav" class="navbar navbar-default navbar-custom navbar-fixed-top">
        <div class="container">
            <!-- Brand and toggle get grouped for better mobile display -->
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                    <span class="sr-only">Toggle navigation</span> Menu
                </button>
                <a class="navbar-brand" href="index.html">QuickSeller</a>
            </div>

            <!-- Collect the nav links, forms, and other content for toggling -->
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                <ul class="nav navbar-nav navbar-right">
                    <li class="hidden">
                        <a href="index.html"></a>
                    </li>
                    <li>
                        <a class="" href="product_list.html">Product List</a>
                    </li>
                    <li>
                        <a class="" href="top_sellers.html">Top Sellers</a>
                    </li>
                    <li>
                        <a class="" href="about_us.html">About Us</a>
                    </li>
                    <li>
                        <a class="" href="sign_up.php">Register</a>
                    </li>
                </ul>
            </div>
            <!-- /.navbar-collapse -->
        </div>
        <!-- /.container-fluid -->
    </nav>
 
<!--    
    <?php
    
    if($_POST)
    {
        echo '<section><pre>';
        print_r($_POST);
        print_r ($states);        
        echo '</pre></section>';
       
    }
    
    ?>
-->

    <section id="signupform">
    <div class="container">
      <h3>Please fill in to sign up ...</h3>
      <form class="form-horizontal" role="form" method="post" action="sign_up.php">
        <div class="form-group">
          <label class="control-label col-sm-2" for="username">Username:</label>
          <div class="col-sm-2">
             <input type="text" class="form-control" id="username" placeholder="bob234"
                   name="username" value="<?php echo ($_POST["username"]) ? $_POST["username"]:''; ?>">
          </div>
        </div>
          
        <div class="form-group">
          <label class="control-label col-sm-2" for="firstname">First name:</label>
          <div class="col-sm-3">
            <input type="text" class="form-control" id="firstname" placeholder="Bob"
                   name="firstname" value="<?php echo ($_POST["firstname"]) ? $_POST["firstname"]:''; ?>">
          </div>
        </div>
        
         <div class="form-group">
          <label class="control-label col-sm-2" for="middlename">Middle name:</label>
          <div class="col-sm-3">
            <input type="text" class="form-control" id="middlename" placeholder="James"
                    name="middlename" value="<?php echo ($_POST["middlename"]) ? $_POST["middlename"]:''; ?>">
          </div>
        </div>
          
        <div class="form-group">
          <label class="control-label col-sm-2" for="lastname">Last name:</label>
          <div class="col-sm-3">
            <input type="text" class="form-control" id="lastname" placeholder="Martin"
                    name="lastname" value="<?php echo ($_POST["lastname"]) ? $_POST["lastname"]:''; ?>">
          </div>
        </div>      
           
        <div class="form-group">
          <label class="control-label col-sm-2" for="email">Email:</label>
          <div class="col-sm-3">
            <input type="email" class="form-control" id="email" placeholder="bobjmartin@example.com"
                    name="email" value="<?php echo ($_POST["email"]) ? $_POST["email"]:''; ?>">
          </div>
        </div>
          
        <div class="form-group">
          <label class="control-label col-sm-2" for="pwd">Password:</label>
          <div class="col-sm-3">
            <input type="password" class="form-control" id="pwd" placeholder="password"
                    name="password" value="<?php echo ($_POST["password"]) ? $_POST["password"]:''; ?>">
          </div>
        </div>
          
        <div class="form-group">
          <label class="control-label col-sm-2" for="confirmpwd">Confirm Password:</label>
          <div class="col-sm-3">
            <input type="password" class="form-control" id="confirm_password" placeholder="Confirm password"
                    name="confirm_password" value="<?php echo ($_POST["confirm_password"]) ? $_POST["confirm_password"]:''; ?>">
          </div>
        </div>
         
         <div class="form-group">
          <label class="control-label col-sm-2" for="contact_num">Contact Number:</label>
          <div class="col-sm-3">
            <input type="text" class="form-control" id="contact_num" placeholder="9213321345"
                    name="contact_num" value="<?php echo ($_POST["contact_num"]) ? $_POST["contact_num"]:''; ?>">
          </div>
        </div>
          
        <div class="form-group">
          <label class="control-label col-sm-2" >Gender:</label>
          <div class="col-sm-10">
           <label class="radio-inline"><input type="radio" name="gender" value="M"
                <?php echo ($_POST["gender"]) && ($_POST["gender"]==='F') ?'checked="false"':'checked="true"'?>> Male</label>
           <label class="radio-inline"><input type="radio" name="gender" value="F"
                <?php echo ($_POST["gender"]) && ($_POST["gender"]==='F') ?'checked="true"':''?> > Female</label>
          </div>
        </div>
          
         <div class="form-group">
          <label class="control-label col-sm-2" for="dob">Date of birth:</label>
          <div class="col-sm-2">
            <input type="date" class="form-control" id="dob" name="dob"  
                   value="<?php echo $_POST["dob"] ? $_POST["dob"] : '1993-02-01';?>">
          </div>
          
        </div>
          
        <div class="form-group">
          <label class="control-label col-sm-2" >User Type:</label>
          <div class="col-sm-10">
              <label class="radio-inline"><input type="radio" name="user_type" value="B"
                  <?php echo ($_POST["user_type"]) && ($_POST["user_type"]==='S') ?'checked="false"':'checked="true"'?>> Buyer</label>
           <label class="radio-inline"><input type="radio" name="user_type" value="S"
                  <?php echo ($_POST["user_type"]) && ($_POST["user_type"]==='S') ?'checked="true"':''?>> Seller</label>
          </div>
        </div>
                
        <div class="form-group">
          <label class="control-label col-sm-2" for="res_addrstate">Residence Address:</label>  
        
          <div class="col-sm-4">
            <select class="form-control " id="res_addrstate" name="res_addrstate">  
                <option value="" >Select State</option>
                <?php
                        foreach($states as $state_id => $state_name)
                        {
                            echo '<option value="'.$state_id.'" ';
                            echo  ($_POST["res_addrstate"] && $_POST["res_addrstate"] == $state_id) ?'selected ':'';
                            echo    '>'.$state_name.'</option>';
                        }         
                    ?>
            </select>
          </div>
          <div class="clearfix"></div>
          <div class="col-sm-offset-2 col-sm-4 needspacing">
            <input type="text" class="form-control" id="res_addrcity" placeholder="Bhubaneswar"
                    name="res_addrcity" value="<?php echo ($_POST['res_addrcity']) ? $_POST["res_addrcity"]:''; ?>">
          </div>
          <div class="clearfix"></div>  
          <div class="col-sm-offset-2 col-sm-4 needspacing">
            <input type="text" class="form-control" id="res_addrstreet" placeholder="Gandhi Street"
                    name="res_addrstreet" value="<?php  echo ($_POST["res_addrstreet"]) ? $_POST["res_addrstreet"]:''; ?>">
          </div>
          <div class="clearfix"></div>
          <div class="col-sm-offset-2 col-sm-4 needspacing">
            <input type="text" class="form-control" id="res_addrzip" placeholder="748123"
                    name="res_addrzip" value="<?php  echo ($_POST["res_addrzip"]) ? $_POST["res_addrzip"]:''; ?>">
          </div>           
        </div>
          
        <div class="form-group">
          <label class="control-label col-sm-2" for="ofc_addrstate">Office Address:</label>           
         
          <div class="col-sm-4">
            <select class="form-control " id="ofc_addrstate" name="ofc_addrstate" selec>  
                <option value="" >Select State</option>
                    <?php
                        foreach($states as $state_id => $state_name)
                        {
                            echo '<option value="'.$state_id.'" ';
                            echo  ($_POST["ofc_addrstate"] && $_POST["ofc_addrstate"] == $state_id) ?'selected ':'';
                            echo    '>'.$state_name.'</option>';
                        }         
                    ?>
            </select>
          </div>
          <div class="clearfix"></div>
          <div class="col-sm-offset-2 col-sm-4 needspacing">
            <input type="text" class="form-control" id="ofc-addrcity" placeholder="Bhubaneswar"
                    name="ofc-addrcity" value="<?php  echo ($_POST["ofc-addrcity"]) ? $_POST["ofc-addrcity"]:''; ?>">
          </div>
          <div class="clearfix"></div>  
          <div class="col-sm-offset-2 col-sm-4 needspacing">
            <input type="text" class="form-control" id="ofc-addrstreet" placeholder="Street-76"
                   name="ofc-addrstreet" value="<?php  echo ($_POST["ofc-addrstreet"]) ? $_POST["ofc-addrstreet"]:''; ?>">
          </div>
          <div class="clearfix"></div>
          <div class="col-sm-offset-2 col-sm-4 needspacing">
            <input type="text" class="form-control" id="ofc-addrzip" placeholder="785103"
                   name="ofc-addrzip" value="<?php  echo ($_POST["ofc-addrzip"]) ? $_POST["ofc-addrzip"]:''; ?>">
          </div>
        </div>
               
          
        <div class="form-group">
            <label class="control-label col-sm-2" for="comment">About Me:</label>
            <div class="col-sm-5">
                <textarea class="form-control" rows="5" id="comment" placeholder="Describe yourself here..."
                          name="comment" ><?php  echo ($_POST["comment"]) ? $_POST["comment"]:''; ?></textarea>
            </div>
        </div> 
          
        <div class="form-group">
          <label class="control-label col-sm-2" >Preferred Communication Medium:</label>
          <div class="col-sm-10 give_padding" >
              <label class="checkbox-inline"><input type="checkbox" name="pref_comm[]" value="email"
                    <?php echo $_POST["pref_comm"] && in_array("email",$_POST["pref_comm"]) ? ' checked':''?> > Email</label>
            <label class="checkbox-inline"><input type="checkbox" name="pref_comm[]" value="sms"
                    <?php echo $_POST["pref_comm"] && in_array("sms",$_POST["pref_comm"]) ? ' checked':''?>> SMS</label>
            <label class="checkbox-inline"><input type="checkbox" name="pref_comm[]" value="call"
                    <?php echo $_POST["pref_comm"] && in_array("call",$_POST["pref_comm"]) ? ' checked':''?>> Call</label>
            <label class="checkbox-inline"><input type="checkbox" name="pref_comm[]" value="any"
                    <?php echo $_POST["pref_comm"] && in_array("any",$_POST["pref_comm"]) ? ' checked':''?>> ANY</label>            
          </div>
        </div>  
          
        <div class="form-group">
          <div class="col-sm-offset-2 col-sm-1">
            <button type="submit" class="btn btn-default btn-lg btn-success">Submit</button>
          </div>
          <div class="col-sm-offset-1 col-sm-1">
            <button type="reset" class="btn btn-default btn-lg btn-danger">Reset</button>
          </div>
        </div>
      </form>
    </div>
    </section>   
     
    <!-- jQuery -->
    <script src="vendor/jquery/jquery.min.js"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="vendor/bootstrap/js/bootstrap.min.js"></script>
</body>

</html>
