<?php

/**
 * This function returns the full info from a panel when given an ID
 * @param mysqli $conn The connection to the database
 * @param int $id The ID of the panel
 * @return array the panel retrieved
 */
function getPanelByID($conn, $id)
{
    $sql = "SELECT * FROM `inventory` WHERE `PANEL ID` = ? ";
    $result = $conn->execute_query($sql, [$id]);
    return $result->fetch_assoc();
}

function formatPanel($key, $val)
{
    $prov_reference = [
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
    
    $val = strtolower($val);
    $key = strtolower($key);
    if($val == "" || $val == 0){
        $val = "N/A";
    }
    switch ($key) {
        case 'ref':
            return ["Reference", strtoupper($val)];

        case 'core':
            return ["Core Material", strtoupper($val)];
        case 'qty':
            return ["Quantity", $val];
        case 'sheet size':
            return ["Sheet Size", $val];
        case 'bulk price':
        case 'price':
            if($val != "N/A"){
                $val = "$".$val;
            }
            case 'location':
                [$key,$val] = ["Location", 
                isset($prov_reference[strtoUpper($val)])
                ?$prov_reference[strtoUpper($val)]:$val];
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

/**
 * This function turns two numbers into a sheet size
 * @param string $width The width in mm as a string
 * @param string $height The height in mm as a string
 * @return string The concatenation of the two, but with the larger value first
 */
function convertSheetDim($width, $height)
{
    if ($width > $height) {
        return $width . " x " . $height;
    }
    return $height . " x " . $width;
}

/**
 * This function takes the user's input and checks against the database for any duplicate entries
 * @param mysqli $conn The connection to the database
 * @param array $panel The information about the panel given by the user
 * @return bool whether the panel has a match in the database
 */
function isUserDuplicate($conn, $panel)
{
    /** panel info array
     * $panel = {
     * 0=>SUPPLIER
     * 1=>COLOUR
     * 2=>REF
     * 3=>CORE
     * 4=>SHEET SIZE
     * 5=>THICKNESS
     * 6=>QTY
     * 7=>CONDITION
     * 8=>BULK PRICE
     * 9=>PRICE
     * 10=>NOTES
     * 11=>FIRST UPLOADED
     * 12=>LOCATION
     * 13=>UPLOADER ID
     * }
     */
    $sqlRetrieve = "SELECT * FROM `inventory`
        WHERE `UPLOADER ID` = ? ";
    $response = $conn->execute_query($sqlRetrieve, [$panel[13]]);
    foreach ($response as $row) {
        // if a match is found
        if (
            $row["SUPPLIER"] == $panel[0]
            && $row["REF"] == $panel[1]
            && $row["COLOUR"] == $panel[2]
            && $row["CORE"] == $panel[3]
            && $row["SHEET SIZE"] == $panel[4]
            && $row["THICKNESS"] == $panel[5]
            && $row["QTY"] == $panel[6]
            && $row["CONDITION"] == $panel[7]
            && $row["BULK PRICE"] == $panel[8]
            && $row["PRICE"] == $panel[9]
            && $row["NOTES"] == $panel[10]
        ) {
            return true;
        }
    }
    return false;
}