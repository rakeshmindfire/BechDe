<?php

require_once 'config/constants.php';

/**
 * To validate an image file
 *
 * @access public
 * @param string $pic Name of the picture
 * @return string 
 */
function image_validation($pic) {
    $err = '';

    if ($_FILES[$pic]['error'] === 0) {
        $extension = pathinfo(basename($_FILES[$pic]['name']))['extension'];
        $check = getimagesize($_FILES[$pic]['tmp_name']);

        if ($check === FALSE) {
            $err = 'File is not an image.';
        }

        if (!in_array($extension, array('jpeg', 'jpg', 'png', 'gif'))) {
            $err = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
        }
        
    } elseif ($_FILES[$pic]['error'] === 1) {
        $err = "File is too large. Must be less than 100MB";
    }
    
    return $err;
}

/**
 * To check data if in proper format
 *
 * @access public
 * @param array $data Entire POSTed data
 * @return string 
 */
function validate_data($data) {
    $err = [];

    foreach ($data as $key => $value) {
        $type = 'any';
        $null_allowed = FALSE;
        switch ($key) {
            case 'user_name':
                $type = 'alnum';
                break;

            case 'password':
            case 'confirm_password':
                $type = 'pwd';
                break;

            case 'ofc_addrcity':
                $null_allowed = TRUE;
            case 'first_name':
            case 'last_name':
            case 'res_addrcity':
                $type = 'alphabet';
                break;

            case 'middle_name':
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
                $null_allowed = TRUE;   
            case 'res_addrzip':
                $type = 'zip';
                break;

            case 'product_price':
                $type = 'float';
                break;

            case 'comment':
            case 'pref_comm':
            case 'submit':
            case 'ofc_addrstate':
            case 'ofc_addrstreet':
            case 'description':
                $null_allowed = TRUE;   
            case 'res_addrstate':
            case 'res_addrstreet':
            case 'user_type':
            case 'gender':
            case 'product_name':
                $type = 'any';
                break;
        }
        
        $err[$key] = validate_using($value, $null_allowed, $type);

        if ($key === 'confirm_password' && $value !== $data['password']) {
            $err[$key] = 'Password do not match';
        }
    }

    return $err;
}

/**
 * To check if email already exists in the database
 *
 * @access public
 * @param string $data Email-id
 * @return string
 */
function existing_email($data) {
    $db = new dbOperation();
    $db->select('login', ['email'], ['email'=>$data]);
    $err = empty($db->num_rows_result) ? '': 'Email already exists';
    
    return $err;
}

/**
 * To check if a mandatory field has a null value
 *
 * @access public
 * @param mixed $data Data of the mandatory field
 * @return boolean
 */
function required($data) {

    $valid = TRUE;

    if (empty($data)) {
        $valid = FALSE;
    }

    return $valid;
}

/**
 * To check if a value is of a proper type
 *
 * @access public
 * @param mixed $value Value to be checked
 * @param boolean $null_allowed If it can be null
 * @param string $type Filter to check the value with
 * @return string 
 */
function validate_using($value, $null_allowed, $type) {
    $testpattern = ! $null_allowed ? required($value) : TRUE;
    $err = '';

    if ($testpattern) {

        if ( ! empty($value)) {
            
            switch ($type) {
                case 'alphabet':
                    if ( ! preg_match('/^[A-Za-z]+$/', $value)) {
                        $err = 'This field should contain only alphabets';
                    };
                    break;

                case 'alnum':
                    if ( ! preg_match('/^[A-Za-z0-9]+$/', $value)) {
                        $err = 'This field should contain only alphabets and numeric characters';
                    };
                    break;

                case 'email':
                    $email = filter_var($value, FILTER_SANITIZE_EMAIL);

                    if (filter_var($email, FILTER_VALIDATE_EMAIL) === FALSE) {
                        $err = "$email is not a valid email address";
                    } else {
                        $err = existing_email($value);
                    }
                    break;

                case 'mobile_num':
                    if ( ! preg_match('/^\d{10}$/', $value)) {
                        $err = 'This field should contain 10 numeric characters';
                    };
                    break;

                case 'zip':
                    if ( ! preg_match('/^\d{6}$/', $value)) {
                        $err = 'This field should contain 6 numeric characters';
                    };
                    break;

                case 'float':
                    if ( ! preg_match('/^(\d|\d*.\d*)$/', $value)) {
                        $err = 'This field should contain a number';
                    };
                    break;

                case 'dob':
                    $date = explode('-', $value);
                    if (( ! checkdate($date[1],$date[2],$date[0])) ||
                        ($value > '2000-12-31' || $value < '1916-01-01')) {
                        $err = 'Date is invalid . Must be between 01/01/1916 and 31/12/2000';
                    }
                    break;

                case 'pwd':
                    if ( ! preg_match("/^\w{6,}$/", $value)) {
                        $err = 'Password must be atleast 6 characters long';
                    }
                    break;

                case 'any':
                    // Nothing required
                    break;
            }
        }
    } else {
        $err = 'This field is required.';
    }

    return $err;
}

/**
 * To remove special characters and whitespaces
 *
 * @access public
 * @param array $data Entire POSTed data
 * @return array 
 */
function santizing($data) {

    foreach ($data as $key => $post_values) {
        
        // Remove white spaces and special characters from string
        if (is_string($post_values)) {
            $data[$key] = filter_var(trim($post_values), FILTER_SANITIZE_STRING);
        }
    }

    return $data;
}
?>