<?php
include_once "auth.php";
$conn = connectToDatabase();
$sql = "SELECT * FROM inventory";
$result = $conn->query($sql);
$data = [];
if ($result->num_rows > 0) {
    // output data of each row
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
} else {
    echo "0 results";
}
$conn->close();