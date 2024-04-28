<?php
/**
 * TODO:
 * This code will do the following
 * - take the form post action from the upload.php page,
 * - inspect the inputs such that none of the required
 * inputs are invalid
 *   - numbers are numbers
 *   - one of the core types selected
 *   - condition selected
 *   - notes do not contain any profanity???
 *     - oh dear
 *   - individual sale checked or not?
 *     - if so inspect the indv sale input, but can disregard if empty
 * - Depending on the outcome, output errors to the $_SESSION variables
 * that will be displayed to the user
 * - otherwise it will attempt to put the panel into the inventory
 *   - gather all extra info from session variables, (location, user id, etc) 
 * - if there is a duplicate panel by the same user (EXACT MATCH),
 * then prompt the user with an extra box to confirm
 * - when panel has been successfully uploaded, give response to user, and
 * then ask if they'd like to input another or return to index
 * 
 */

include_once "auth.php";
include_once "panelUtilities.php";
// miscellaneous functions

/**
 * This function returns what the condition terms are, and scraps any invalid inputs
 * @param string $cond - The condition string to translate
 * @return string/NULL - the new string
 */
function checkCondition($cond)
{
    switch ($cond) {
        case "T1":
            return "Good";
        case "T2":
            return "Great";
        case "T3":
            return "Excellent";
        case "T4":
            return "Factory New";
        default:
            return NULL;
    }
}

/**
 * This function gets the core material string and checks that it is valid
 * @param string $core The core material string
 * @return string/NULL The new string for core material
 */
function checkCore($core)
{
    if ($core != "PE" && $core != "FR" && $core != "OTHER") {
        return NULL;
    }
    return $core;
}

/**
 * This function checks the sheet size string to make sure both strings are valid
 * @param string $sheetSize the sheet size given by the user
 * @return bool whether the sheet size is valid
 */
function checkSheetSize($sheetSize)
{
    $array = explode(" x ", $sheetSize);
    if (count($array) == 2) {
        if (ctype_digit($array[0]) && ctype_digit($array[1])) {
            return true;
        }
    }
    return false;
}

/**
 * This function checks that the given string contains only numbers and decimal points
 * @param string $price the string of numbers
 * @return bool whether the string is valid or false
 */
function isPriceValid($price)
{
    $array = str_split($price);
    if (
        (array_search(".", $array) != count($array) - 3
            || count(array_keys($array, ".")) > 1)
        && array_search(".", $array) != null
    ) {
        return false;
    }
    foreach ($array as $l) {
        if (!ctype_digit($l) && !str_contains($l, ".")) {
            return false;
        }
    }
    return true;
}

/**
 * This function checks the core material and returns whether there are notes
 * @param string $core The core material selected
 * @param string $notes The notes provided when selected core is "other"
 * @return bool whether the notes are preset on "other" core material
 */
function otherHasNotes($core, $notes)
{
    if ($core == "OTHER") {
        return $notes != null;
    }
    return true;
}



/**
 * This function checks if the individual price of panels is more than the bulk price
 * This also returns false if the individual price is invalid
 * @param string $bulk The bulk price of the panels
 * @param string $indv The individual price of the panels
 * @param string $qty The number of panels 
 * @return bool whether the individual panels are more expensive than the bulk price, 
 * and if the individual price is valid input
 */
function indvMoreThanBulk($bulk, $indv, $qty)
{
    if ($indv != 0 && isPriceValid($indv)) {
        return floatval($bulk) <= (floatval($indv) * floatval($qty));
    } else if ($indv == 0) {
        return true;
    }
    return false;
}

function setIndvPrice($price)
{
    if ($price == "") {
        return 0;
    } else {
        return floatval($price);
    }
}

function validateInput($panel_info)
{
    $count = count($panel_info);
    // if any fields are null return false
    for ($i = 0; $i < $count; $i++) {
        $valid[$i] = true;
        if (is_null($panel_info[$i])) {
            $_SESSION['fieldError'] = "Please fill out all fields properly";
            $valid[$i] = false;
        }
    }

    // if any of the number results are numbers
    if (!checkSheetSize($panel_info[4])) {
        $valid[4] = false;
        $_SESSION['sheetSizeError'] = "Please enter a valid number";
    }

    if (!ctype_digit($panel_info[5])) {
        $valid[5] = false;
        $_SESSION['dimZError'] = "Please enter a valid number";
    }
    if (!ctype_digit($panel_info[6])) {
        $valid[6] = false;
        $_SESSION['qtyError'] = "Please enter a valid number";
    }
    // if the bulk price is invalid return false
    if (!isPriceValid($panel_info[8])) {
        $_SESSION['bulkPriceError'] = "Please input a valid price for the panels";
        $valid[8] = false;
    }
    // if the individual price is set and invalid
    if (
        !indvMoreThanBulk($panel_info[8], $panel_info[9], $panel_info[6])
        || !isPriceValid($panel_info[9])
    ) {
        $_SESSION['indvPriceError'] = "Please input a valid price, where the total cost is higher than the bulk price";
        $valid[9] = false;
    }

    if (is_null($panel_info[7])) {
        $_SESSION['conditionError'] = "Please select a valid condition";
        $valid[7] = false;
    }

    // if other core is selected but no notes
    if (!otherHasNotes($panel_info[3], $panel_info[10])) {
        $valid[10] = false;
        $_SESSION['notesOtherError'] = "Please put some notes explaining the core materials";
    }
    if (!isset($panel_info[13])) {
        $valid[13] = false;
        $_SESSION['show_login'] = true;
        $_SESSION['noLoginError'] = "Please log in before uploading a panel";
    }
    echo '<script>console.log(' . json_encode(get_defined_vars()) . ');</script>';
    foreach ($valid as $v) {
        if ($v == false) {
            return false;
        }
    }
    return true;
}

// intialize data from POST
$supplierUpl = isset($_POST['supplierUpl'])
    ? strtoupper($_POST['supplierUpl']) : NULL;
$colourUpl = isset($_POST['colourUpl'])
    ? strtoupper($_POST['colourUpl']) : NULL;
$refUpl = isset($_POST['refUpl'])
    ? strtoupper($_POST['refUpl']) : NULL;
$coreUpl = isset($_POST['coreUpl'])
    ? checkCore(strtoupper($_POST['coreUpl'])) : NULL;
$dimUplX = isset($_POST['dimUplX'])
    ? $_POST['dimUplX'] : NULL;
$dimUplY = isset($_POST['dimUplY'])
    ? $_POST['dimUplY'] : NULL;
$dimUplZ = isset($_POST['dimUplZ'])
    ? $_POST['dimUplZ'] : NULL;
$quantityUpl = isset($_POST['quantityUpl'])
    ? $_POST['quantityUpl'] : NULL;
$conditionUpl = isset($_POST['conditionUpl'])
    ? checkCondition($_POST['conditionUpl']) : NULL;
$totalPriceUpl = isset($_POST['totalPriceUpl'])
    ? $_POST['totalPriceUpl'] : NULL;
$indvPriceUpl = isset($_POST['indvPriceUpl'])
    ? setIndvPrice($_POST['indvPriceUpl']) : 0;
$notesUpl = isset($_POST['notesUpl'])
    ? $_POST['notesUpl'] : NULL;
$today = date("Y-m-d");
$location = isset($_SESSION['province'])
    ? $_SESSION['province'] : NULL;
$user_id = isset($_SESSION['internal_id'])
    ? $_SESSION['internal_id'] : NULL;
// end of POST data
$MYSQLI_CODE_DUPLICATE_KEY = 1062;
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$panel_info = [
    $supplierUpl,
    $refUpl,
    $colourUpl,
    $coreUpl,
    convertSheetDim($dimUplX, $dimUplY),
    $dimUplZ,
    $quantityUpl,
    $conditionUpl,
    $totalPriceUpl,
    $indvPriceUpl,
    $notesUpl,
    $today,
    $location,
    $user_id
];
$safe = validateInput($panel_info);
if ($safe) {
    $conn = connectToDatabase();
    $sqlSend = "INSERT INTO 
        `inventory` (
            SUPPLIER, 
            REF, 
            COLOUR, 
            CORE, 
            `SHEET SIZE`, 
            THICKNESS,
            QTY, 
            `CONDITION`, 
            `BULK PRICE`, 
            PRICE, 
            `NOTES`,
            `FIRST UPLOADED`,
            `LOCATION`,
            `UPLOADER ID`
        ) VALUES (
            ?,
            ?,
            ?,
            ?,
            ?,
            ?,
            ?,
            ?,
            ?,
            ?,
            ?,
            ?,
            ?,
            ?
        )";

    try {
        if (isUserDuplicate($conn, $panel_info)) {
            // send error message?
            $_SESSION['duplicateError'] = "You have already uploaded this set of panels!";
            redirect_to($_SERVER['HTTP_REFERER']);
        } else {
            $response = $conn->execute_query($sqlSend, $panel_info);
            // continue
        }
    } catch (mysqli_sql_exception $e) {
        if ($e->getCode() == $MYSQLI_CODE_DUPLICATE_KEY) {
            $_SESSION['emailDupeError'] = "That email address is already in our system!";
            redirect_to($_SERVER['HTTP_REFERER']);
        } else {
            echo $e;
        }
    }
    redirect_to('../../index.php');
} else {
    redirect_to($_SERVER['HTTP_REFERER']);
}



