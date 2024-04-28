<?php
include_once "auth.php";
try {
    if (isset($_POST['delete'])) {
        $panel_id = isset($_POST['panel_id']) ? 
        $_POST['panel_id'] : throw new Exception("no panel selected");
    }
    // if for some reason internal_id does not match UPLOADER ID
// abort deletion
    if ($_SESSION['internal_id'] == $_SESSION['panel']['UPLOADER ID']) {
        // do as you do
        $conn = connectToDatabase();
        $sql = "DELETE FROM inventory WHERE `PANEL ID` = ? ";
        $result = $conn->execute_query($sql, [$panel_id]);
        $conn->close();
        redirect_to("../../account.php?category=current+listings");
    } else {
        // Soap, what the hell was that?
        throw new Exception("invalid panel selected");
    }
} catch (Exception $e) {
    echo $e->getMessage();
}