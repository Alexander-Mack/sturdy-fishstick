<?php
include_once "auth.php";
include_once "listingUtilities.php";
include_once "requestUtilities.php";
include_once "panelUtilities.php";

/**
 * This function changes the user's values stored in the database and writes
 * the new values to $_SESSION array
 * @param string $name
 * @param string $email
 * @param string $new_pass
 * @param string $confirm_pass
 * @param string $street
 * @param string $city
 * @param string $province
 * @param string $postal_code
 * @param string $phone_num
 * @param string $internal_id
 * @return string The result from the query
 */
function changeValue(
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
) {
    $hash = password_hash($confirm_new, PASSWORD_DEFAULT);
    $sql = "UPDATE `users` SET 
    `name` = ?, 
    `email` = ?, 
    `password` = ?, 
    `street` = ?,
    `city` = ?,
    `province` = ?,
    `postal_code` = ?,
    `phone_num` = ?
    WHERE
    `internal_id` = ?";
    $conn = connectToDatabase();
    $result = $conn->execute_query(
        $sql,
        [
            $name,
            $email,
            $hash,
            $street,
            $city,
            $province,
            $postal_code,
            $phone_num,
            $internal_id
        ]
    );
    $_SESSION['name'] = $name;
    $_SESSION['email'] = $email;
    $_SESSION['street'] = $street;
    $_SESSION['city'] = $city;
    $_SESSION['province'] = $province;
    $_SESSION['postal_code'] = $postal_code;
    $_SESSION['phone_num'] = $phone_num;
    $conn->close();
    return $result;
}

/**
 * This function changes the user's values stored in the database and writes
 * the new values to $_SESSION array
 * @param string $name
 * @param string $email
 * @param string $street
 * @param string $city
 * @param string $province
 * @param string $postal_code
 * @param string $phone_num
 * @param string $internal_id
 * @return string The result from the query
 */
function changeValueNoPass(
    $name,
    $email,
    $street,
    $city,
    $province,
    $postal_code,
    $phone_num,
    $internal_id
) {
    $sql = "UPDATE `users` SET 
    `name` = ?, 
    `email` = ?, 
    `street` = ?,
    `city` = ?,
    `province` = ?,
    `postal_code` = ?,
    `phone_num` = ?
    WHERE
    `internal_id` = ?";
    $conn = connectToDatabase();
    $result = $conn->execute_query(
        $sql,
        [
            $name,
            $email,
            $street,
            $city,
            $province,
            $postal_code,
            $phone_num,
            $internal_id
        ]
    );
    $_SESSION['name'] = $name;
    $_SESSION['email'] = $email;
    $_SESSION['street'] = $street;
    $_SESSION['city'] = $city;
    $_SESSION['province'] = $province;
    $_SESSION['postal_code'] = $postal_code;
    $_SESSION['phone_num'] = $phone_num;
    $conn->close();
    return $result;
}

/**
 * This subroutine takes all the user inputs and attempts to 
 * 'sanitize' them, and cancel the process if there are any
 * invalid entries, without passwords
 * @param string $us Username
 * @param string $st Street
 * @param string $ci City
 * @param string $pr Province
 * @param string $po Postal Code
 * @param string $ph Phone Number
 * @return boolean the validity of the inputs
 */
function inspectInputsNoPass($us, $st, $ci, $pr, $po, $ph)
{
    
    $valid = true;
    // if phone number is incorrect length after cleaning
    if (strlen($ph) > 10 || strlen($ph) < 10) {
        $_SESSION['phoneError'] = "Please input a valid phone number.";
        $valid = false;
    }
    // if postal code is not alphanumeric and only 6 digits long
    if (!ctype_alnum($po) || strlen($po) != 6) {
        echo "1";
        $_SESSION['addressError'] = "Please enter a valid address and postal code.";
        $valid = false;
    }
    // if province was never set
    if (!isset($pr)) {
        echo "2";
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
        echo "3";
        $_SESSION['addressError'] = "Please enter a valid address and postal code.";
        $valid = false;
    }

    if (ctype_alnum($st)) {
        echo "4";
        $_SESSION['addressError'] = "Please enter a valid address and postal code.";
        $valid = false;
    }
    if (!checkEmailName($us)) {
        $valid = false;
    }
    return $valid;
}

/**
 * This function makes a quick query to find out if the user password is valid
 * @param string $password The current password of the user
 * @param int/NULL $internal_id The internal id of the user 
 * @return boolean whether the password is a match
 */
function checkPassword($password, $internal_id)
{
    $conn = connectToDatabase();
    $sql = "SELECT `password` FROM `users` WHERE `internal_id` = ?";
    $result = $conn->execute_query($sql, [$internal_id]);
    $hash_row = $result->fetch_assoc();
    if (isset($hash_row["password"])) {
        $hash = $hash_row['password'];
        $conn->close();
        return password_verify($password, $hash);
    } else {
        $conn->close();
    }

    return false;
}
/**
 * This function puts the user information on display in the account page,
 * and allows the user to attempt to make changes to that information
 */
function displayAcc()
{
    // display user info
    // name, email, change password form, street,
    // city, province, postal code, phone number, date joined
    $array = [
        'Name' => $_SESSION['name'],
        'Email' => $_SESSION['email'],
        'Street' => $_SESSION['street'],
        'City' => $_SESSION['city'],
        'Province' => $_SESSION['province'],
        'Postal Code' => $_SESSION['postal_code'],
        'Phone Number' => $_SESSION['phone_num'],
        'Date Joined' => $_SESSION['date_joined']
    ];
    // quick postal code formatting
    $begin = substr($array['Postal Code'], 0, 3);
    $end = substr($array['Postal Code'], 3);
    $array['Postal Code'] = $begin." ".$end;

    // city, name formatting
    $array['City'] = ucfirst($array['City']);
    $array['Name'] = ucfirst($array['Name']);

    // phone number formatting
    $area = substr($array['Phone Number'], 0, 3);
    $three = substr($array['Phone Number'], 3, 3);
    $four = substr($array['Phone Number'], 6, 4);
    $array['Phone Number'] = "(".$area.") ".$three." - ".$four;

    $provArr = [
        "AB" => "Alberta",
        "BC" => "British Columbia",
        "MB" => "Manitoba",
        "NB" => "New Brunswick",
        "NL" => "Newfoundland and Labrador",
        "NT" => "Northwest Territories",
        "NS" => "Nova Scotia",
        "NU" => "Nunavut",
        "ON" => "Ontario",
        "PE" => "Prince Edward Island",
        "QC" => "Québec",
        "SK" => "Saskatchewan",
        "YK" => "Yukon"
    ];


    echo "<section id='accInfoBox'>";
    foreach ($array as $key => $value) {
        echo "<div style='align-content: end'>";
        echo "<span class='left'>" . $key . "</span>";
        if ($key != 'Date Joined') {
            echo "<button type= 'button' class='left' onclick='change(\"$key\",\"$value\")'>change</button>";
        }
        if ($key == 'Province') {
            foreach ($provArr as $acro => $full) {
                if ($acro == $value) {
                    $value = $full;
                    break;
                }
            }
        }
        echo "<span class='right'>" . $value . "</span>";
        echo "</div>";
    }
    echo "<div style='align-content: end'>";
    echo "<span style='margin-left:calc(50% - 97.075px);'>Change Password</span>";
    echo "<button type='button' onclick='change(\"Password\")'>change</button>";
    echo "</div>";
    if(isset($_SESSION['changeError'])){
        echo "<span class='changeError'> change request failed, reason: <br></span>";
        echo "<span class='changeError'>".$_SESSION['changeError']."</span>";
        unset($_SESSION['changeError']);
    }
    echo "</section>";

}

function displayTrans()
{

    // display transaction log
    // all transactions that were done by this account, sorted by date
}

/**
 * This function displays all uploads by the user as a table just like
 * the one on listings.php, but without the controls
 * @param $data The table data
 */
function displayList($data)
{
    // display all uploads by the user
    // displayed in a table like the one on listings.php,
    // but auto filtered to only show the user's uploads
    echo '<div id="tableContainer" class="tableAccInfo">';
    $count = printUserTable($data);
    if ($count == 0) {
        echo "<h2>You have not uploaded any panels that are currently listed</h2>";
    }
    echo '</div>';

}

/**
 * This function displays all requests made by the user, as a table
 */
function displayPend()
{
    $conn = connectToDatabase();
    $user_id = isset($_SESSION['internal_id'])?$_SESSION['internal_id']: NULL;
    $result = getUserRequests($conn, $user_id);
    $requests = [];
    if ($result->num_rows > 0) {
        // output data of each row
        while ($row = $result->fetch_assoc()) {
            $requests[] = $row;
        }
        echo '<div id="tableContainer" class="tableAccInfo">';
        $count = printRequestTable($requests);
        if($count == 0){
            echo "<h2>You have not made any requests that are currently listed</h2>";
        }
        echo '</div>';
    }else{
        
    }

    // display all panel requests made by the user,
    // with option to delete requests or modify them?
}