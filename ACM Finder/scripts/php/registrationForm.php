<?php
include_once "auth.php";
include_once "registrationUtilities.php";

$nick = isset($_POST['nameReg']) ? $_POST['nameReg'] : null;
$user = isset($_POST['emailReg']) ? $_POST['emailReg'] : null;
$pass = isset($_POST['passwordReg']) ? $_POST['passwordReg'] : null;
$passConfirm = isset($_POST['passwordRegConfirm']) ? $_POST['passwordRegConfirm'] : null;
$street = isset($_POST['streetReg']) ? $_POST['streetReg'] : null;
$city = isset($_POST['cityReg']) ? $_POST['cityReg'] : null;
$province = isset($_POST['provinceReg']) ? $_POST['provinceReg'] : null;
$postal = isset($_POST['postReg']) ? $_POST['postReg'] : null;
$phone = isset($_POST['phoneReg']) ? $_POST['phoneReg'] : null;
$MYSQLI_CODE_DUPLICATE_KEY = 1062;
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$today = date("Y-m-d");
$phone = phoneNumberHandler($phone);
$postal = postCodeHandler($postal);
$safe = inspectInputs(
    $user,
    $pass,
    $passConfirm,
    $street,
    $city,
    $province,
    $postal,
    $phone
);
if ($safe) {
    // Create Connection
    $conn = connectToDatabase();
    $hash = password_hash($pass, PASSWORD_DEFAULT);
    $sql = "INSERT INTO `users` (`name`, email, `password`, 
    street, city, province, postal_code, 
    phone_num, date_joined) VALUES(?, ?, ?,
    ?, ?, ?, ?, ?, ?)";
    try {
        $response = $conn->execute_query($sql, [
            $nick,
            $user,
            $hash,
            $street,
            $city,
            $province,
            $postal,
            $phone,
            $today
        ]);
        $_SESSION['show_login'] = true;
        redirect_to('../../index.php');
    } catch (mysqli_sql_exception $e) {
        if ($e->getCode() == $MYSQLI_CODE_DUPLICATE_KEY) {
            $_SESSION['emailDupeError'] = "That email address is already in our system!";
            redirect_to($_SERVER['HTTP_REFERER']);
        } else {
            echo $e;
        }
    }
} else {
    redirect_to('../../index.php');
}
$conn->close();