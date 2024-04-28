<?php
if (isset($_SESSION['panel'])){
    $temp = $_SESSION['panel'];
}
include_once ("auth.php");
$loguser = isset ($_POST['email']) ? $_POST['email'] : null;
$logpass = isset ($_POST['password']) ? $_POST['password'] : null;
$prompt = isset ($_POST['prompt']) ? $_POST['prompt'] : null;
$MYSQLI_CODE_DUPLICATE_KEY = 1062;
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$conn = connectToDatabase();
$sql = "SELECT * FROM `users` WHERE email = ? ;";
try {
    $result = $conn->execute_query($sql, [$loguser]);
    $row = $result->fetch_assoc();
    if (isset ($row["password"])) {
        $hash = $row['password'];
        if (password_verify($logpass, $hash)) {
            login($row);
            $_SESSION['panel'] = $temp;
        } else {
            fail_login('Invalid Password');
        }
    } else {
        fail_login('Invalid Email');
    }
    $conn->close();
} catch (mysqli_sql_exception $e) {
    echo $e->getMessage();
}