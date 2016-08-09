<?php
require_once 'config/constants.php';

function image_check($pic){
    
    $err='';
    
    if($_FILES[$pic]['error']==0){ // $FILES['name']['error'] is 0 if successfully uploaded
    
        $extension = pathinfo( basename($_FILES[$pic]["name"]))['extension'];
        $check = getimagesize($_FILES[$pic]["tmp_name"]);
        
        if($check == false){
          $err = "File is not an image.";
        }

        if ($_FILES[$pic]["size"] > 500000) {
            $err = "Sorry, your file is too large.";
        }

        if(!in_array($extension, array('jpeg','jpg','png','gif'))) {
            $err = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";          
        }
    } 
    
return $err;
}

function validate_data($data) {
  $err = [];
  print_r($data);
    foreach ($data as $key => $value) {
        $type='any';
        $null_allowed = FALSE;
        switch ($key) {
            case 'username':
                $type = 'alnum';
                break;
            case 'password':
            case 'confirm_password': 
                $type='pwd';
                break;
            case 'ofc_addrcity':         //no break
                $null_allowed = TRUE;
            case 'firstname':            //no break
            case 'lastname':             //no break
            case 'res_addrcity':    
                $type = 'alphabet';
                break;
            
            case 'middlename':
                $null_allowed = TRUE;
                $type = 'alphabet';
                break;
            
            case 'email':
                $type = 'email';
                break;
            
            case 'contact_num':
                $type = 'mobile_num';
                break;
            
            case 'dob':
                $type = 'dob';
                break;
            
            case 'ofc_addrzip':
                $null_allowed = TRUE; //no break    
            case 'res_addrzip':
                $type = 'zip';
                break;
            
            case 'product_price':
                $type='float';
                break;
            
            case 'comment':
            case 'pref_comm':
            case 'submit':
            case 'ofc_addrstate':
            case 'ofc_addrstreet':
            case 'description':
                $null_allowed = TRUE;   //no break
            case 'res_addrstate':
            case 'res_addrstreet':
            case 'user_type':
            case 'gender':
            case 'product_name':
                $type = 'any';
                break;
        }
        $err[$key] = validate_using($value, $null_allowed, $type);

        if ($key == 'confirm_password' && $value !== $data['password']) {
                $err[$key] = 'Password do not match';
        }
    }

    return $err;
}

function existing_email($data) {
    $conn = mysqli_connect(SERVERNAME, USERNAME, PASSWORD, DBNAME);

    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }
    $checkemail = mysqli_query($conn,"SELECT * FROM `login` WHERE `email`= '".$data."'");
    $err = empty(mysqli_num_rows($checkemail)) ? '' : 'Email already exists';
    mysqli_close($conn);   
    return $err;
}

function required($data) {
    
    $valid = TRUE;
    
    if(empty($data)) {
        $valid = FALSE;
    }
    
    return $valid;
}


function validate_using($value, $null_allowed, $type) {

    $testpattern = !$null_allowed ? required($value) : TRUE;
    $err = '';
    
    if ($testpattern) {

        if (!empty($value)) {
            switch ($type) {
                case 'alphabet':
                    if (!preg_match('/^[A-Za-z]+$/', $value)) {
                        $err = 'This field should contain only alphabets';
                    };
                    break;

                case 'alnum':
                    if (!preg_match('/^[A-Za-z0-9]+$/', $value)) {
                        $err = 'This field should contain only alphabets and numeric characters';
                    };
                    break;

                case 'email':
                    $email = filter_var($value, FILTER_SANITIZE_EMAIL);
                    
                    if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
                        $err = "$email is not a valid email address";
                    } else {
                     $err = existing_email($value);
                    }
                    break;

                case 'mobile_num':
                    if (!preg_match('/^\d{10}$/', $value)) {
                        $err = 'This field should contain 10 numeric characters';
                    };
                    break;
                    
                case 'zip':
                    if (!preg_match('/^\d{6}$/', $value)) {
                        $err = 'This field should contain 6 numeric characters';
                    };
                    break;
                    
                case 'float':
                    if (!preg_match('/^(\d|\d*.\d*)$/', $value)) {
                        $err = 'This field should contain a number';
                    };
                    break;
                
                case 'dob':
                    if (!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/",$value)
                            || $value > '2000-12-31' || $value < '1916-01-01') {
                        $err = 'Date is invalid . Must be between 01/01/1916 and 31/12/2000';
                    }
                    break;
                    
                case 'pwd':
                    if (!preg_match("/^\w{6,}$/",$value)) {
                        $err = 'Password must be atleast 6 characters long';
                    }
                    break;
                    
                case 'any':
                    //nothing required
                    break;
            }
        }
    } else {
        $err = 'This field is required.';
    }

    return $err;
}

?>