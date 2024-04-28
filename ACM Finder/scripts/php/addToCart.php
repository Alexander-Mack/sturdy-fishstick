<?php
include_once ("auth.php");
include_once ("cartUtilities.php");

// check that the user is logged in
if (!isset($_SESSION['email'])) {
    $_SESSION['show_login'] = true;
    redirect_to('index.php');
    exit;
}
// collect post variables
$panel = isset($_POST['panel_id']) ? $_POST['panel_id'] : null;
$qty = isset($_POST['addSelectedQTY']) ? $_POST['addSelectedQTY'] : null;
// if the quantity of panels is 0 (this is the output from adding "all" to cart)
if ($qty < 1 && isset($_SESSION['panel'])) {
    // set the quantity to the max value
    $qty = $_SESSION['panel']['QTY'];
} else {
    // set to 0 because something went wrong
    $qty = 0;
}
// This is the code for duplicate key errors
$MYSQLI_CODE_DUPLICATE_KEY = 1062;
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$conn = connectToDatabase();
$today = date("Y-m-d");
$uid = $_SESSION['internal_id'];
// as long as the quantity of panels is greater than 0, add
if ($qty > 0) {
    try {
        $response = getCartPanelIDs($conn, [$uid]);
        // check for duplicates in card
        if (isDuplicate($panel, $response)) {
            throw new mysqli_sql_exception(
                'This panel already exists in your cart!',
                $MYSQLI_CODE_DUPLICATE_KEY
            );
        }
        addToCart($conn, [$panel, $uid, $today, $qty]);

    } catch (mysqli_sql_exception $e) { // catch duplicate error
        echo $e->getMessage();
        $currQTY = getQuantityOfPanelInCart($panel, $_SESSION['internal_id']);
        echo var_dump($currQTY);
        if(is_int($currQTY["quantity"])){
            
            changeAmountInCart($panel, $currQTY["quantity"]+$qty, $_SESSION['internal_id']);
        }
    }
}
$conn->close();
redirect_to("../../listings.php");