<?php include_once "scripts/php/cartUtilities.php";?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title>ACM Finder</title>
        <link rel="stylesheet" href="stylesheet.css">
        <script src="https://d3js.org/d3.v4.min.js"
            charset="utf-8"></script>
        <script
            src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js">
        </script>
        <script src="scripts/javascript/navigationScript.js"></script>
        <script src="scripts/javascript/loginScript.js"></script>
        <!--<script src="searchScript.js"></script>-->
    </head>

    <body>
        <!-- if failed login, display login again -->
        <div id="backdrop" <?php
        if (isset ($_SESSION['show_login'])) {
            echo "style='display:flex;'";
        } else {
            echo "hidden";
        }
        ?>></div>
        <div id="header">
            <div id="banner">
                <div id="welcome"> ACM FINDER </div>
                <div class="bannerButtons">
                        <button type="button" id="cart" class="accButtons"
                        <?php 
                        if (isset ($_SESSION["email"])) { 
                            echo 'onclick="cartPage()"';
                        } else {
                            echo 'onclick="enterPopup()"';
                        } 
                        ?>
                        >Cart <?php
                            if (isset ($_SESSION['email'])) {
                                $conn = connectToDatabase();
                                echo "(".getCartCount($conn, [$_SESSION['internal_id']]).")";
                                $conn->close();
                            }
                        ?>
                        </button>
                        <!-- login or account button -->
                        <?php
                        if (
                            !isset ($_SESSION["user"])
                            && !isset ($_SESSION["internal_id"])
                        ) {
                            echo "<button type='button' id='loginButton'
                            onclick='enterPopup()' class='accButtons'>Login</button>
                            <button type='button' id='registrationButton'
                            onclick='registrationPage()' class='accButtons'>Register</button>";
                        } else {
                            echo "<button type='button' id='accountButton'
                            onclick='accountPage()' class='accButtons'>Account</button><form 
                            action='scripts/php/logoutForm.php' method='POST'><button 
                            type='submit' class='accButtons' id='logout'>
                            Logout</button></form>";
                        }
                        ?>
                        <div id='bannerInfo'>
                    <?php
                    if (isset ($_SESSION["email"])) {
                        echo "Welcome, ".ucFirst($_SESSION["name"])."!";
                    } else {
                        echo "Please log in!";
                    }
                    ?>
                </div>
                </div><!-- end of banner buttons div-->
                
            </div><!-- end of banner -->
            <div id="navigation">
                <div class="navbutton" id="home" onclick="homePage()"> Home
                </div>
                <div class="navbutton" id="listings"
                    onclick="listingsPage()"> Listings</div>
                <div class="navbutton" id="upload" 
                <?php 
                if (isset ($_SESSION["email"])) { 
                    echo 'onclick="uploadPage()"';
                } else {
                    echo 'onclick="enterPopup()"';
                } 
                ?>
                >Upload</div>
                <div class="navbutton" id="request"
                <?php 
                if (isset ($_SESSION["email"])) { 
                    echo 'onclick="requestPage()"';
                } else {
                    echo 'onclick="enterPopup()"';
                } 
                ?>> Request</div>
                <div class="navbutton" id="about"
                    onclick="aboutPage()"> About Us</div>
            </div><!-- end of navigation buttons -->
        </div><!-- end of header -->
        <div id="loginPopup" <?php
        if (isset ($_SESSION['show_login'])) {
            echo "style='display:grid;'";
            unset($_SESSION["show_login"]);
        } else {
            echo "hidden";
        }
        ?>>
            <span id="exitPopup" class="hidden" onclick="exitPopup()"> X</span>
            <!-- login form -->
            <form class="hidden" id="loginForm"
                action="scripts/php/loginForm.php" method="POST">
                <div>Please log in to continue</div>
                <!-- email -->
                <label class="hidden" for="email">Email: </label>
                <input class="hidden" id="email" name="email"
                    type="email" placeholder="Email"
                    autocomplete="email" required>
                <!-- password -->
                <label class="hidden" for="password">Password: </label>
                <input class="hidden" id="password" name="password"
                    type="password" placeholder="Password"
                    autocomplete="current-password" required>
                <!-- submit/cancel -->
                <div id="loginSubmitButtons">
                    <input type="reset" class="red" value="Cancel"
                        onclick="exitPopup()">
                    <input type="submit" class="green">
                </div>
            </form>
            <!-- Error text for invalid input -->
            <span style="color:red; margin-top: 5px;">
                <?php
                if(isset($_SESSION['noLoginError'])){
                    echo $_SESSION['noLoginError'];
                    unset($_SESSION['noLoginError']);
                }
                if (isset ($_SESSION['error'])) {
                    echo $_SESSION['error'];
                    unset($_SESSION['error']);
                }
                ?>
            </span>
            <!-- Utilities -->
            <div>
                <span class="loginExtras">Don't have an account?</span>
                <a href="registration.php"
                    class="loginExtras">Register Here!</a>
            </div>
            <div>
                <span class="loginExtras">Forgotten your password?</span>
                <a href="resetPassword.php" class="loginExtras">Reset
                    Password</a>
            </div>
            <br>
        </div> <!-- end of login popup-->