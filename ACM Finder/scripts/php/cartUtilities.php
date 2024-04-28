<?php
include_once ("auth.php");

/**
 * This function gets all the rows that are part of the user's cart
 * @param mysqli $conn The connection to the database
 * @param array $vars The user id of the cart owner
 * @return mysqli_result The result of the sql query
 */
function getCart($conn, $vars)
{
    $sql = "SELECT * FROM `cart` WHERE `user_id` = ? ";
    return $conn->execute_query($sql, $vars);
}
/**
 * This function adds a new panel to the user's cart
 * @param mysqli The connection to the database
 * @param array $vars The array containing the information to add to the cart
 * @return mysqli_result The result of the query
 */
function addToCart($conn, $vars)
{
    $sql = "INSERT INTO `cart` (panel_id, 
    user_id, date_added, quantity) 
    VALUES(?, ?, ?, ?)";
    return $conn->execute_query($sql, $vars);
}

/**
 * This function gets the number of rows in the user's cart
 * @param mysqli $conn The connection to the database
 * @param array $vars the array with the info required to acquire more info
 * @return int The number of rows found that match the user id
 * @uses getCart($conn, $vars)
 */
function getCartCount($conn, $vars)
{
    $result = getCart($conn, $vars);
    return isset($result) ? mysqli_num_rows($result) : 0;
}

function getTaxRate($province)
{
    switch ($province) {
        case "AB":
            return 0.05;
        case "BC":
            return 0.12;
        case "MB":
            return 0.12;
        case "NB":
            return 0.15;
        case "NL":
            return 0.15;
        case "NT":
            return 0.05;
        case "NS":
            return 0.15;
        case "NU":
            return 0.05;
        case "ON":
            return 0.13;
        case "PE":
            return 0.15;
        case "QC":
            return 0.14975; // typical QC
        case "SK":
            return 0.11;
        case "YK":
            return 0.05;
        case "":
            return 1;
        default:
            return 1;
    }
}

/**
 * This function gets the IDs of all the panels in the user's cart
 * @param mysqli $conn The connection to the database
 * @param array $vars The information used to get the panel IDs
 * @return array $return The panel IDs of the panels in the cart
 */
function getCartPanelIDs($conn, $vars)
{
    $return = array();
    $result = getCart($conn, $vars);
    foreach ($result as $r) {
        array_push($return, $r['panel_id']);
    }
    // if $return is not an array but does exist somehow, set it to an array and push to it
    if (!is_array($return) && isset($return)) {
        $temp = $return;
        $return = array();
        array_push($return, $temp);
    }
    return $return;
}

/**
 * This function gets the quantity of a single panel ID in the cart, 
 * not multiple panel IDs
 * @param int $panel_id The id of the panel being searched for
 * @param int $user_id The id of the user who'd cart is to be searched
 * @return array|bool|null The result from the query 
 */
function getQuantityOfPanelInCart($panel_id, $user_id)
{
    $conn = connectToDatabase();
    $sql = "SELECT quantity 
            FROM cart 
            WHERE panel_id = ? AND user_id = ?";
    $result = $conn->execute_query($sql, [$panel_id, $user_id]);
    return $result->fetch_assoc();
}

/**
 * This function checks the user's cart for duplicates
 * @param array $new_panel The new panel being added
 * @param array $old_panels The panels that currently exist in the cart
 * @return bool whether or not the new panel is a duplicate
 */
function isDuplicate($new_panel, $old_panels)
{
    foreach ($old_panels as $o) {
        echo $new_panel . ", " . $o . "<br>";
        if ($o == $new_panel) {
            return true;
        }
    }
    return false;
}

/**
 * This function gets the total cost of all the items in the cart
 */
function getCartTotal($conn, $vars)
{
    $result = getCartTotal($conn, $vars);
    // get the quantities of each panel
    // query for the costs of each from the inventory
    // !!!! must find way to check if individual cost or bulk buyout
}

/** 
 * This function changes the number of panels of a single ID in the uder's cart
 * @param int $panel_id The id of the panel
 * @param int $newQuantity The new quantity to change to
 * @param int $user_id The id of the user who's cart to change quantity in
 */
function changeAmountInCart($panel_id, $newQuantity, $user_id)
{
    $conn = connectToDatabase();
    // find out if new_quantity > max quantity, set to max if true
    $maxQuantSQL = "SELECT `QTY` FROM `inventory` WHERE `PANEL ID` = ?";
    $result = $conn->execute_query($maxQuantSQL, [$panel_id]);
    $maxQuantity = $result->fetch_assoc()['QTY'];
    if ($newQuantity > $maxQuantity) {
        $newQuantity = $maxQuantity;
    }
    // set quantity of panel
    if ($newQuantity > 0) {
        echo $newQuantity;
        $newQuantSQL = "UPDATE cart SET quantity = ? WHERE panel_id = ? AND user_id = ?";
        $result = $conn->execute_query($newQuantSQL, [$newQuantity, $panel_id, $user_id]);
    } else {// or delete from cart if quantity is now <=0
        $deleteSQL = "DELETE FROM `cart` WHERE panel_id = ? AND user_id = ?";
        $result = $conn->execute_query($deleteSQL, [$panel_id, $user_id]);
    }
    $conn->close();
}

function sendCartToCheckout()
{
    // todo
}