<?php
/**
 * This function gets all requests made by the specified user ID
 * @param mysqli $conn The connection to the database
 * @param string $user_id The user id
 * @return mysqli_result The list of requests of this user
 */
function getUserRequests($conn, $user_id)
{
    // get all requests from the user
    $sql = "SELECT * FROM `requests` WHERE 
        user_id = ?";
    return $conn->execute_query($sql, [$user_id]);
}


/**
 * This function formats request array keys so they can be displayed
 * @param string $val The value to parse
 * @return array the parsed value
 */
function formatRequest($key, $val)
{

    $val = strtolower($val);
    $key = strtolower($key);
    if ($val == 0 || $val == "") {
        $val = "N/A";
    }
    switch ($key) {
        case 'core':
            return ["Core Material", strtoupper($val)];
        case 'reference':
            return ["Reference", strtoupper($val)];
        case 'min_dim_x':
            return ["Minimum Width (mm)", $val];
        case 'min_dim_y':
            return ["Minimum Height (mm)", $val];
        case 'date_added':
            return ["Date Added", $val];
        default:
            $arr = explode("_", $val);
            $count = count($arr);
            for ($i = 0; $i < $count; $i++) {
                $arr[$i] = ucfirst($arr[$i]);
            }
            $arr = explode(" ", $val);
            $count = count($arr);
            for ($i = 0; $i < $count; $i++) {
                $arr[$i] = ucfirst($arr[$i]);
            }
            $val = implode(" ", $arr);

            $arr = explode("_", $key);
            $count = count($arr);
            for ($i = 0; $i < $count; $i++) {
                $arr[$i] = ucfirst($arr[$i]);
            }
            $arr = explode(" ", $key);
            $count = count($arr);
            for ($i = 0; $i < $count; $i++) {
                $arr[$i] = ucfirst($arr[$i]);
            }
            $key = implode(" ", $arr);
            return [$key, $val];
    }
}

function printRequestInfo()
{
    $req = $_SESSION['request'];
    /*
    '<div id="SUPPLIER"><span class="left">Supplier</span>
        <span class="right">
            ' . checkSet('SUPPLIER') . '
        </span>
    </div>
    */
    foreach ($req as $key => $value) {
        if (
            $key != 'internal_id' &&
            $key != 'user_id' &&
            $key != 'email'
        ) {
            [$fkey, $fval] = formatRequest($key, $value);
            echo "<div id='$key'>
                <span class='left'>" . $fkey . "</span>
                <span class='right'>" . $fval . "</span>
                </div>";
        }
    }
    echo "</div>";


}

/**
 * This function gets a request from the user's pending requests
 * @param int $request_id The id number of the request
 * @return mysqli_result The request request
 */
function getRequest($request_id)
{
    $conn = connectToDatabase();
    // make SQL query
    $sql = "SELECT * FROM `requests` WHERE
    internal_id = ?";
    // parse
    $result = $conn->execute_query($sql, [$request_id]);
    $_SESSION['request'] = $result->fetch_assoc();
    return $_SESSION['request'];
}

/**
 * This function uses usort to sort by date added first, 
 * and then by supplier second
 * @param array $requests The array to sort
 * @return array The sorted array
 */
function sortRequests($requests)
{
    usort($requests, function ($a, $b) {
        if ($a['date_added'] == $b['date_added']) {
            return $a['supplier'] <=> $b['supplier'];
        }
        return $a['date_added'] <=> $b['date_added'];
    });
    return $requests;
}

/**
 * This function prints the request table to HTML
 * @param array $requests The requests table data
 * @return int the number of rows created
 */
function printRequestTable($requests)
{
    $requests = sortRequests($requests);
    $count = 0;
    // create table head
    echo "<table id='dataTable'><thead><tr>";
    foreach ($requests[0] as $r => $h) {
        if (
            $r != "internal_id" &&
            $r != "user_id" &&
            $r != "email"
        ) {
            [$fr, $fh] = formatRequest($r, $h);
            echo "<th>" . strtoupper($fr);
            echo "</th>";
        }
    }
    echo "</tr></thead>";
    // for each row of data
    foreach ($requests as $key => $value) {
        // increment count
        $count++;
        // create a row in the table
        echo "<tr class='tableBody' id='"
            . $value['internal_id'] . "' onclick='requestSelection("
            . $value['internal_id'] . ", this)'>";
        foreach ($value as $k => $v) {
            if (
                $k != "internal_id" &&
                $k != "email" &&
                $k != "user_id"
            ) {
                [$fk, $fv] = formatRequest($k, $v);
                echo "<td>" . $fv . "</td>";
            }
        }
        echo "</tr>";
    }


    echo "</table>";
    // if no rows are created
    return $count;
}

/**
 * This function takes the inputs from the user, and checks them against
 * all other previous requests to see if they match within the margin or error
 * if it is a duplicate request, it sets a session variable to be shown
 */
function isDuplicateRequest(
    $supplier,
    $colour,
    $reference,
    $core,
    $minDimX,
    $minDimY,
    $user_id,
    $conn
) {
    $result = getUserRequests($conn, $user_id);
    $dupeArray = array();
    // for each request
    foreach ($result as $row) {
        // for each key value pair in the request
        foreach ($row as $key => $value) {
            // check which key it is
            switch ($key) {
                case 'date_added':
                case 'internal_id':
                case 'email':
                    break;
                case 'min_dim_x':
                    // if minDimX is less than the new value
                    $dupeArray[$key] = $value >= $minDimX;
                    break;
                case 'min_dim_y':
                    // if minDimY is less than the new value
                    $dupeArray[$key] = $value >= $minDimY;
                    break;
                default:
                    // if old value matches incoming value
                    $dupe = array_search(strtolower($value), [
                        strtolower($supplier),
                        strtolower($colour),
                        strtolower($reference),
                        strtolower($core),
                        $user_id
                    ]);
                    // set array key accordingly
                    if ($dupe !== false) {
                        $dupeArray[$key] = true;
                    } else {
                        $dupeArray[$key] = false;
                    }
            }

        }
    }
    // if any of the new array keys are false, not a duplicate
    foreach ($dupeArray as $dup) {
        if (!$dup) {
            return false;
        }
    }
    // else return true
    return true;

}