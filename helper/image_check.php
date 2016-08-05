<?php

function image_check($pic){
    
    $uploadOk = 1;
    
    if($_FILES[$pic]['error']==0){ // $FILES['name']['error'] is 0 if successfully uploaded
    
        $extension = pathinfo( basename($_FILES[$pic]["name"]))['extension'];
        $check = getimagesize($_FILES[$pic]["tmp_name"]);
        
        if($check == false){
        //  echo "File is not an image.";
          $uploadOk = 0;
        }

        if ($_FILES[$pic]["size"] > 500000) {
        //    echo "Sorry, your file is too large.";
            $uploadOk = 0;
        }

        if(!in_array($extension, array('jpeg','jpg','png','gif'))) {
        //   echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
           $uploadOk = 0;
        }
    } 
return $uploadOk;
 
}
?>