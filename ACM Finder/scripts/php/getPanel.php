<?php
include_once 'auth.php';
include_once 'panelUtilities.php';

/**
 * This function checks if the session variable for the panel is set,
 * and echoes it to the HTML if it is, otherwise it will output N/A
 * @param string $ref The reference array for the panel
 * @return void This function only outputs to HTML
 */
function checkSet($ref)
{
    if (isset($_SESSION['panel'])) {
        if (
            $_SESSION['panel'][$ref] == null
            || $_SESSION['panel'][$ref] == ""
        ) {
            echo "N/A";
        } else {
            echo $_SESSION['panel'][$ref];
        }
    } else {
        echo 'error';
    }
}


function printPanelInfo()
{
    $req = $_SESSION['panel'];

    foreach ($req as $key => $value) {
        if (
            $key != 'UPLOADER ID' &&
            $key != 'PANEL ID'
        ) {
            [$fkey, $fval] = formatPanel($key, $value);
            echo "<div id='$key'>
                <span class='left'>" . $fkey . "</span>
                <span class='right'>" . $fval . "</span>
                </div>";
        }
    }
    echo "</div>";
}

$sheet = isset($_POST['panel_id']) ? $_POST['panel_id'] : null;
if ($sheet == null && isset($_SESSION['panel'])) {
    $sheet = $_SESSION['panel']['PANEL ID'];
}
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
// Create Connection
$conn = connectToDatabase();
try {
    $_SESSION["panel"] = getPanelByID($conn, $sheet);
    $conn->close();
} catch (mysqli_sql_exception $e) {
    echo $e->getMessage();
}