<?php
include_once "auth.php";
include_once "panelUtilities.php";

/**
 * This function returns if first instance of the given value
 * is the same as its current index
 * @param string $value The value to search for
 * @param int $index The index of the value
 * @param array $array The array that contains the value
 * @return bool whether the instance is equal to the index
 */
function onlyUnique($value, $index, $array)
{
    return array_search($value, $array) == $index;
}

/**
 * This function creates options for the supplier select on the
 * listings.php page
 * @param array $data The data to get options from
 */
function getSupplierOptions($data)
{
    $suppliers = [];
        $uniqueSuppliers = [];
        // track the index
        $cs = count($data);
        // for the length of the data
        for ($i = 0; $i < $cs; $i++) {
            $suppliers[$i] = $data[$i]['SUPPLIER'];
            // if it is the first instance of that value
            if (onlyUnique($suppliers[$i], $i, $suppliers)) {
                echo "<option value='".$data[$i]['SUPPLIER']."'>".$data[$i]['SUPPLIER']."</option>";
            }
        }
}

/**
 * This function checks the row for any cells that contain matches to the
 * user input
 * @param $array - The row
 * @param $fil - The filters from the search bar
 * @param $opt - The filter from the dropdown
 * @return - Whether the row is a match
 */
function searchMatch($array, $fil, $opt)
{
    // if there are no filters, allow all
    if ($fil == "" && $opt == "default") {
        return true;
    }
    // for each value in the row
    foreach ($array as $key => $value) {
        // if the value matches the dropdown
        if (levenshtein(strtolower($value), $opt) < 1 || $opt == "default") {
            // check the filters
            return sub_searchMatch($array, $fil);
            // will return true if all filters match the row
        }
    }// if no matches
    return false;
}


/**
 * This sub function splits each index of an array into individual words,
 * does the same with the filter terms, and checks each possible cell for
 * matching terms.
 * @param $array - The row to search
 * @param $fil - The filters to search by
 * @return bool - whether all filters matched in the row
 */
function sub_searchMatch($array, $fil)
{
    // break filter into multiple terms
    $multifil = explode(" ", $fil);
    $matchfil = array();
    $count = count($multifil);
    // for each filter term
    for ($i = 0; $i < $count; $i++) {
        $matchfil[$i] = false;
        // for each value in the row
        foreach ($array as $key => $value) {
            // split the value into multiple terms
            $exvalue = explode(" ", strtolower($value));
            // for each term in the value
            foreach ($exvalue as $exv) {
                // if the term matches the filter
                // echo $exv.", ".$multifil[$i].": ".levenshtein($exv, $multifil[$i])."<br>";
                if (levenshtein($exv, $multifil[$i]) < 3) {

                    $matchfil[$i] = true;
                }
            }// continue for each term in value
        }//continue for each value
    }//continue for each filter term
    //for each filter term
    foreach ($matchfil as $f) {
        // if one of the filters is false
        if ($f == false) {
            return false;
        }
    }
    // if none of the filters are false
    return true;
}

/**
 * This function prints the listings for the user in a table
 * @param array $data The raw data to be used for the table
 * @param string $filter The filter string to narrow the table
 * @param string $option The supplier option currently selected by the user
 * @return void This function only echoes html
 */
function printTable($data, $filter, $option){
    $count = 0;
    // create table head
    echo "<table id='dataTable'><thead><tr>";
        foreach ($data[0] as $r => $h) {
            if (
                $r != "PANEL ID" &&
                $r != "BULK PRICE" &&
                $r != "PRICE" &&
                $r != "UPLOADER ID" &&
                $r != "NOTES"
            ) {
                echo "<th>" . $r;
                if (
                    $r == "SHEET SIZE"
                    || $r == "THICKNESS"
                ) {
                    echo " (mm)";
                }
                echo "</th>";
            }
        }
        echo "</tr></thead>";
        // for each row of data
        foreach ($data as $key => $value) {
            // if the row matches the filter
            if (searchMatch($value, $filter, $option)) {
                // increment count
                $count ++;
                // create a row in the table
                echo "<tr class='tableBody' id='"
                    . $value['PANEL ID'] . "' onclick='tableSelection("
                    . $value['PANEL ID'] . ", this)'>";
                foreach ($value as $k => $v) {
                    if (
                        $k != "PANEL ID" &&
                        $k != "BULK PRICE" &&
                        $k != "PRICE" &&
                        $k != "UPLOADER ID" &&
                        $k != "NOTES"
                    ) {
                        [$fk, $fv] = formatPanel($k, $v);
                        echo "<td>" . $fv . "</td>";
                    }
                }
                echo "</tr>";
            }
        }
        
        echo "</table>";
        // if no rows are created
        if($count == 0){
            echo "<span style='width:60%; text-align:justify; font-size:20px; display:block; margin:auto'>We could not find any panels matching your 
            search parameters.<br>Please broaden your search or <a href = 'panelRequest.php'>click
            here</a> to make a request for that panel!</span>"; 
        }
}

function printUserTable($data){
    $count =  0;
    // create table head
    echo "<table id='dataTable'><thead><tr>";
        foreach ($data[0] as $r => $h) {
            if (
                $r != "PANEL ID" &&
                $r != "BULK PRICE" &&
                $r != "PRICE" &&
                $r != "UPLOADER ID" &&
                $r != "NOTES"
            ) {
                echo "<th>" . $r;
                if (
                    $r == "SHEET SIZE"
                    || $r == "THICKNESS"
                ) {
                    echo " (mm)";
                }
                echo "</th>";
            }
        }
        echo "</tr></thead>";
        
        // for each row in the data
        foreach ($data as $key => $value) {
            // if the row belongs to the user
            if ($value['UPLOADER ID'] == $_SESSION['internal_id']) {
                $count++;
                echo "<tr class='tableBody' id='"
                    . $value['PANEL ID'] . "' onclick='tableSelection("
                    . $value['PANEL ID'] . ", this)'>";
                foreach ($value as $k => $v) {
                    if (
                        $k != "PANEL ID" &&
                        $k != "BULK PRICE" &&
                        $k != "PRICE" &&
                        $k != "UPLOADER ID" &&
                        $k != "NOTES"
                    ) {
                        [$fk, $fv] = formatPanel($k, $v);
                        echo "<td>" . $fv . "</td>";
                    }
                }
                echo "</tr>";
            }
        }
        echo "</table>";
        return $count;
}