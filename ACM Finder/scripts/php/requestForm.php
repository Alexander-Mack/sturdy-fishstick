<?php
include_once "auth.php";
include_once "requestUtilities.php";
// get variables from POST and SESSION
$supplier = isset($_POST['supplierReq']) ? $_POST['supplierReq'] : null;
$colour = isset($_POST['colourReq']) ? $_POST['colourReq'] : null;
$reference = isset($_POST['refReq']) ? $_POST['refReq'] : null;
$core = isset($_POST['coreReq']) ? $_POST['coreReq'] : null;
$minDimX = isset($_POST['minDimReqX']) ? $_POST['minDimReqX'] : null;
$minDimY = isset($_POST['minDimReqY']) ? $_POST['minDimReqY'] : null;
$email = isset($_SESSION["email"]) ? $_SESSION["email"] : null;
$user_id = isset($_SESSION['internal_id']) ? $_SESSION['internal_id'] : null;

$MYSQLI_CODE_DUPLICATE_KEY = 1062;
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$today = date("Y-m-d");
// Create Connection
$conn = connectToDatabase();

try {
    if (
        isDuplicateRequest(
            $supplier,
            $colour,
            $reference,
            $core,
            $minDimX,
            $minDimY,
            $user_id,
            $conn
        )
    ) {
        throw new Exception("That request is already in our system!");
    }
    $sql = "INSERT INTO `requests` (
        supplier, 
        colour, 
        reference, 
        core, 
        min_dim_x,
        min_dim_y, 
        email,  
        user_id, 
        `date_added`
    )VALUES(
        ?, ?, ?, ?, ?, ?, ?, ?, ?
    )";
    $response = $conn->execute_query($sql, [
        $supplier,
        $colour,
        $reference,
        $core,
        $minDimX,
        $minDimY,
        $email,
        $user_id,
        $today
    ]);
    // on success, go to listings
    $conn->close();
    redirect_to('../../listings.php');

} catch (Exception $e) {
    $_SESSION['requestError'] = "You've already made that request!";
}
$conn->close();
// on fail, go back
redirect_to($_SERVER['HTTP_REFERER']);
