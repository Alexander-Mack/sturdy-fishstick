<?php
include_once "auth.php";

/**
 * This subroutine checks the email format to ensure that it 
 * is of the format username@domainName.domain
 * @param string $email The user's email address
 * @return boolean Whether the email is valid
 */
function checkEmailName($email)
{
    // split on @
    $in_two = explode("@", $email);
    $count = count($in_two);
    // check that there was only 1 @ (2 halves)
    if ($count == 2) {
        // split on '.'
        $in_two = explode(".", $in_two[1]);
        // check that there was only 1 '.' (2 halves)
        if ($count == 2) {
            return true;
        } else {
            $_SESSION['emailError'] = "Please input a valid email address.";
            return false;
        }
    } else {
        $_SESSION['emailError'] = "Please input a valid email address.";
        return false;
    }
}
/**
 * This subroutine clears non-numeric values from a phone number
 * @param string $ph - The phone number given by the user
 * @return string The phone number as only numbers, 
 * or nothing is a character is found making it invalid
 */
function phoneNumberHandler($ph)
{
    $pha = [];
    // turn the input into an array
    $phoneNumber = str_split($ph);
    $count = count($phoneNumber);
    for ($i = 0; $i < $count; $i++) {
        // check character type
        if (is_numeric($phoneNumber[$i])) {
            // push to new array
            array_push($pha, $phoneNumber[$i]);
        } else if (ctype_alpha($phoneNumber[$i])) {
            $_SESSION['phoneError'] = "Please input a valid phone number.";
            return "";
        }
    }
    //recombine into a string
    $ph = implode("", $pha);
    return $ph;
}

/**
 * This subroutine removes all whitespace from a given postal code
 * @param $po - The postal code given by the user
 */
function postCodeHandler($po)
{
    $poa = [];
    // split into array
    $postalCode = str_split($po);
    $count = count($postalCode);
    for ($i = 0; $i < $count; $i++) {
        // check character type
        if (!ctype_space($postalCode[$i])) {
            // push to new array
            array_push($poa, $postalCode[$i]);
        }
    }
    // recombine into string
    $po = implode("", $poa);
    return $po;
}
/**
 * This subroutine takes all the user inputs and attempts to 
 * 'sanitize' them, and cancel the process if there are any
 * invalid entries
 * @param string $us Username
 * @param string $pa Password
 * @param string $pc Confirm Password
 * @param string $st Street
 * @param string $ci City
 * @param string $pr Province
 * @param string $po Postal Code
 * @param string $ph Phone Number
 * @return boolean the validity of the inputs
 */
function inspectInputs($us, $pa, $pc, $st, $ci, $pr, $po, $ph)
{
    $valid = true;
    // if passwords do not match
    if ($pa != $pc) {
        $_SESSION['passwordMatchError'] = "Please ensure your passwords match.";
        $valid = false;
    }
    // if password is of bad length
    if (strlen($pa) < 7 || strlen($pa) > 32) {
        $_SESSION['passwordError'] = "Please input a password between 7 and 32 characters.";
        $valid = false;
    }
    // if phone number is incorrect length after cleaning
    if (strlen($ph) > 10 || strlen($ph) < 10) {
        $_SESSION['phoneError'] = "Please input a valid phone number.";
        $valid = false;
    }
    // if postal code is not alphanumeric and only 6 digits long
    if (!ctype_alnum($po) || strlen($po) != 6) {
        $_SESSION['addressError'] = "Please enter a valid address and postal code.";
        $valid = false;
    }
    // if province was never set
    if (!isset($pr)) {
        $_SESSION['addressError'] = "Please enter a valid address and postal code.";
        $valid = false;
    }
    switch ($pr) {
        case "AB":
        case "BC":
        case "MB":
        case "NB":
        case "NL":
        case "NT":
        case "NS":
        case "NU":
        case "ON":
        case "PE":
        case "QC":
        case "SK":
        case "YK":
        case "":
            break;
        default:
            $_SESSION["addressError"] = "You think you're so funny don't you?";
            $valid = false;
            break;
    }
    if (!ctype_alpha($ci)) {
        $_SESSION['addressError'] = "Please enter a valid address and postal code.";
        $valid = false;
    }

    if (ctype_alnum($st)) {
        $_SESSION['addressError'] = "Please enter a valid address and postal code.";
        $valid = false;
    }
    if (!checkEmailName($us)) {
        $valid = false;
    }
    return $valid;
}