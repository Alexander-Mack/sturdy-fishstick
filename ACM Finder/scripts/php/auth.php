<?php
session_start();

/**
 * This function connects to the database and returns the connection
 * instance variable
 * @return - The connection to the db
 */
function connectToDatabase()
{
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "acmfinder";

    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        die("Connection Failed: " . $conn->connect_error);
    }
    return $conn;
}

/** 
 * This function resets the $_SESSION array and destroys the session on logout
 */
function logout()
{
    $_SESSION = array();
    session_destroy();
    redirect_to('../../index.php');
}

/**
 * This function will log in the user once it is determined that
 * the password they input is valid.
 * @param $row - The account info of the user
 */
function login($row)
{
    session_regenerate_id();
    foreach ($row as $key => $value) {
        if ($key != 'password') {
            $_SESSION[$key] = $value;
        }
    }
    redirect_to($_SERVER['HTTP_REFERER']);

}

/**
 * This function handles a failed login attempt
 * TODO: add lockout after 5 attempts?
 * @param string $message The error message to post
 */
function fail_login($message)
{
    $_SESSION['error'] = $message;
    $_SESSION['show_login'] = true;
    redirect_to($_SERVER['HTTP_REFERER']);
}
/**
 * This function is just shorthand for the header function
 * @param string $location The location URL that is being redirected to
 */
function redirect_to($location)
{
    header("Location: " . $location, true, 301);
}