<?php
include_once "auth.php";
include_once "accountUtilities.php";
include_once "registrationUtilities.php";

// figure out which variable is being changed

// step 1 - find changed variable
// step 2 - use this information to define the other variables as default
// POST is set? = POST else = SESSION
$name = isset($_POST['Name']) ? $_POST['Name'] : $_SESSION['name'];
$email = isset($_POST['Email']) ? $_POST['Email'] : $_SESSION['email'];
$street = isset($_POST['Street']) ? $_POST['Street'] : $_SESSION['street'];
$city = isset($_POST['City']) ? $_POST['City'] : $_SESSION['city'];
$province = isset($_POST['Province']) ? $_POST['Province'] : $_SESSION['province'];
$postal_code = isset($_POST['Postal_Code']) ? $_POST['Postal_Code'] : $_SESSION['postal_code'];
$phone_num = isset($_POST['Phone_Number']) ? $_POST['Phone_Number'] : $_SESSION['phone_num'];
$confirm_new = isset($_POST['Confirm_new']) ? $_POST['Confirm_new'] : NULL;
$new_pass = isset($_POST['New']) ? $_POST['New'] : NULL;
$old_pass = isset($_POST['Old']) ? $_POST['Old'] : NULL;
$internal_id = isset($_SESSION['internal_id']) ? $_SESSION['internal_id'] : NULL;
$valid = isset($_SESSION['internal_id']);
// find out if we are changing the password
if (isset($confirm_new) && $valid) {
    
    // make an SQL query to find out if the password is valid
    if (checkPassword($old_pass, $internal_id)) {
        // check email and everything else
        $valid = inspectInputs(
            $email,
            $new_pass,
            $confirm_new,
            $street,
            $city,
            $province,
            $postal_code,
            $phone_num
        );
    } else {
        $valid = false;
    }
} else if ($valid){
    
    // check email and everything else
    $valid = inspectInputsNoPass(
        $email,
        $street,
        $city,
        $province,
        $postal_code,
        $phone_num
    );
    
    
}
// if all valid
if ($valid) {
    // last password check I swear
    if (isset($confirm_new)) {
        $result = changeValue(
            $name,
            $email,
            $new_pass,
            $confirm_new,
            $street,
            $city,
            $province,
            $postal_code,
            $phone_num,
            $internal_id
        );
    } else {
        $result = changeValueNoPass(
            $name,
            $email,
            $street,
            $city,
            $province,
            $postal_code,
            $phone_num,
            $internal_id
        );
    }
    
}else{ // if not valid, set a session variable ['changeError'] and report reason
    if(isset($_SESSION['phoneError'])){
        $_SESSION['changeError'] = $_SESSION['phoneError'];
    }else if (isset($_SESSION['emailError'])){
        $_SESSION['changeError'] = $_SESSION['emailError'];
    } else if (isset($_SESSION['addressError'])){
        $_SESSION['changeError'] = $_SESSION['addressError'];
    } else if (isset($_SESSION['emailError'])){
        $_SESSION['changeError'] = $_SESSION['emailError'];
    } else if (isset($_SESSION['passwordMatchError'])){
        $_SESSION['changeError'] = $_SESSION['passwordMatchError'];
    } else if (isset($_SESSION['passwordError'])){
        $_SESSION['changeError'] = $_SESSION['passwordError'];
    } else {
        $_SESSION['changeError'] = "Something else went wrong";
    }
    unset($_SESSION['emailError']);
    unset($_SESSION['phoneError']);
    unset($_SESSION['emailError']);
    unset($_SESSION['addressError']);
    unset($_SESSION['passwordMatchError']);
    unset($_SESSION['passwordError']);
    
}

redirect_to($_SERVER['HTTP_REFERER']);