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
    
    <?php
    
    if($_POST)
    {
        echo '<section><pre>';
        print_r($_POST);
        print_r ($states);
        echo '</pre></section>';
       
    }
    
    ?>

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
            <input type="date" class="form-control" id="dob" value="" name="dob">
          </div>
          
          <?php
          if($_POST["dob"])
          {
               echo '<span>'.$_POST["dob"].'</span>';
          }   
          else
          {          
            echo '<span>e.g 21/03/2001</span>';
          }     
           
           ?>
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
                <option value="select res_addrstate" >Select State</option>
                <option value="Andaman and Nicobar Islands" >Andaman and Nicobar Islands</option>
                <option value="Andhra Pradesh" >Andhra Pradesh</option>
                <option value="Arunachal Pradesh" >Arunachal Pradesh</option>
                <option value="Assam" <?php echo ($_POST['res_addrstate'] && $_POST['res_addrstate'] === 'Assam') ? 'selected="selected"' : '';?>>Assam</option>
                <option value="Bihar" >Bihar</option>
                <option value="Chandigarh" >Chandigarh</option>
                <option value="Chhattisgarh" >Chhattisgarh</option>
                <option value="Dadra and Nagar Haveli" >Dadra and Nagar Haveli</option>
                <option value="Daman and Diu" >Daman and Diu</option>
                <option value="Delhi" >Delhi</option>
                <option value="Goa" >Goa</option>
                <option value="Gujarat" >Gujarat</option>
                <option value="Haryana" >Haryana</option>
                <option value="Himachal Pradesh" >Himachal Pradesh</option>
                <option value="Jammu and Kashmir" >Jammu and Kashmir</option>
                <option value="Jharkhand" >Jharkhand</option>
                <option value="Karnataka" >Karnataka</option>
                <option value="Kenmore" >Kenmore</option>
                <option value="Kerala">Kerala</option>
                <option value="Lakshadweep" >Lakshadweep</option>
                <option value="Madhya Pradesh" >Madhya Pradesh</option>
                <option value="Maharashtra" >Maharashtra</option>
                <option value="Manipur" >Manipur</option>
                <option value="Meghalaya" >Meghalaya</option>
                <option value="Mizoram" >Mizoram</option>
                <option value="Nagaland" >Nagaland</option>
                <option value="Narora" >Narora</option>
                <option value="Natwar" >Natwar</option>
                <option value="Odisha" >Odisha</option>
                <option value="Paschim Medinipur" >Paschim Medinipur</option>
                <option value="Pondicherry" >Pondicherry</option>
                <option value="Punjab" >Punjab</option>
                <option value="Rajasthan" >Rajasthan</option>
                <option value="Sikkim" >Sikkim</option>
                <option value="Tamil Nadu" >Tamil Nadu</option>
                <option value="Telangana" >Telangana</option>
                <option value="Tripura" >Tripura</option>
                <option value="Uttar Pradesh" >Uttar Pradesh</option>
                <option value="Uttarakhand" >Uttarakhand</option>
                <option value="Vaishali" >Vaishali</option>
                <option value="West Bengal" >West Bengal</option>
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
            <select class="form-control " id="ofc_addrstate" name="ofc_addrstate">  
                <option value="" >Select State</option>
                    <?php
                        foreach($states as $state_id => $state_name)
                        {
                            echo '<option value="'.$state_id.'" ';
                            echo  !($_POST["ofc_addrstate"] && $_POST["ofc_addrstate"] === $state_id) ?'selected="false"':'selected="true"';
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
              <label class="checkbox-inline"><input type="checkbox" name="pref_comm[]" value="email" > Email</label>
            <label class="checkbox-inline"><input type="checkbox" name="pref_comm[]" value="sms"> SMS</label>
            <label class="checkbox-inline"><input type="checkbox" name="pref_comm[]" value="call"> Call</label>
            <label class="checkbox-inline"><input type="checkbox" name="pref_comm[]" value="any"> ANY</label>            
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
