<?php
include_once "auth.php";
// This program should delete user requests based on ID
try {
    // check that the post data is valid, throw exception if not
    if (isset($_POST["delete"])) {
        $delete_id = isset($_POST["request_id"]) ?
            $_POST["request_id"] : throw new Exception("no request selected");
    }
    // if for some reason user id on panel does not match internal ID,
    // abort
    if ($_SESSION['request']['user_id'] == $_SESSION['internal_id']) {
        // do as you do
        $conn = connectToDatabase();
        $sql = "DELETE FROM requests WHERE `internal_id` = ? ";
        $result = $conn->execute_query($sql, [$delete_id]);
        $conn->close();
        redirect_to("../../account.php?category=pending+requests");
    }else{
        // Soap, what the hell was that
        throw new Exception("invalid request selected");
    }
} catch (Exception $e) {
    echo $e->getMessage();
}