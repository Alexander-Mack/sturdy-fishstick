<?php
include_once ("scripts/php/auth.php");
// if already logged in, prevent return
if (isset($_SESSION["internal_id"])) {
    redirect_to("index.php");
}
include_once 'data/templates/header.php';

?>
<script src="scripts/javascript/formScript.js"></script>
<div id="spacer"></div>
<div id="main">
    <div id="middle">
        <h2 style="text-align: justify;">
            <span style="font-size: 30px; text-decoration:underline;"
                style="text-align:center;"> Thank your for creating an
                account!</span>
            <br> By creating an account you will be able to upload<br> and sell
            your extra panels, and purchase panels from<br> others!
        </h2>
        <section class="styleBox">
            <form id="registrationForm"
                action="scripts/php/registrationForm.php"
                method="POST">
                <span style="color:red; font-size: small;"> All fields are
                    required -- This will only be visible to you</span>
                <div class="inRegForm">
                    <label for="nameReg">Name: </label>
                    <input type="text" id="nameReg" name="nameReg"
                        placeholder="Username" autocomplete="nickname"
                        required>
                </div>
                <div class="inRegForm">
                    <label for="emailReg">Email Address: </label>
                    <input type="email" id="emailReg"
                        name="emailReg" placeholder="Email"
                        autocomplete="email" required>
                </div>
                <span class="formError" id="emailError">
                    <?php
                    if (isset($_SESSION['emailError'])) {
                        echo $_SESSION['emailError'];
                        unset($_SESSION['emailError']);
                    }
                    ?>
                </span>
                <div class="inRegForm">
                    <label for="passwordReg">Password: </label>
                    <input id="passwordReg" name="passwordReg"
                        type="password" placeholder="Password"
                        autocomplete="new-password" required>
                </div>
                <div class="inRegForm">
                    <label for="passwordRegConfirm">Confirm Password: </label>
                    <input id="passwordRegConfirm"
                        name="passwordRegConfirm"
                        type="password"
                        placeholder="Confirm Password"
                        autocomplete="new-password" required>
                </div>
                <span class="formError"
                    id="passwordError">
                    <?php
                    if (isset($_SESSION['passwordError'])) {
                        echo $_SESSION['passwordError'];
                        unset($_SESSION['passwordError']);
                    }
                    ?>
                </span><br>
                <span class="formError"
                    id="passwordMatchError">
                    <?php
                    if (isset($_SESSION['passwordMatchError'])) {
                        echo $_SESSION['passwordMatchError'];
                        unset($_SESSION['passwordMatchError']);
                    }
                    ?>
                </span>
                <div class="inRegForm">
                    <label for="streetReg">Street: </label>
                    <input id="streetReg" name="streetReg"
                        type="text" autocomplete="street-address" placeholder="Street">
                    <label for="cityReg">City: </label>
                    <input id="cityReg" name="cityReg" type="text"
                        placeholder="City"
                        autocomplete="address-level2" required>
                </div>
                <div class="inRegForm">
                    <label for="provinceReg">Province: </label>
                    <select id="provinceReg" name="provinceReg"
                        required>
                        <option selected value="" disabled> Please choose one
                            </default>
                        <option value="AB">Alberta</option>
                        <option value="BC">British Columbia </option>
                        <option value="MB">Manitoba</option>
                        <option value="NB">New Brunswick</option>
                        <option value="NL">Newfoundland and Labrador </option>
                        <option value="NT">Northwest Territories </option>
                        <option value="NS">Nova Scotia</option>
                        <option value="NU">Nunavut</option>
                        <option value="ON">Ontario</option>
                        <option value="PE">Prince Edward Island </option>
                        <option value="QC">Québec</option>
                        <option value="SK">Saskatchewan</option>
                        <option value="YK">Yukon</option>
                    </select>
                    <label for="postReg">Postal Code: </label>
                    <input id="postReg" name="postReg" type="text"
                        autocomplete="postal-code"
                        placeholder="Postal Code"
                        style="width:10%" required>
                </div>
                <span class="formError" id="addressError">
                    <?php
                    if (isset($_SESSION['addressError'])) {
                        echo $_SESSION['addressError'];
                        unset($_SESSION['addressError']);
                    }
                    ?>
                </span>
                <div class="inRegForm">
                    <label for="phoneReg">Phone Number: </label>
                    <input type="tel" id="phoneReg"
                        name="phoneReg"
                        placeholder="###-###-####"
                        autocomplete="tel-national" required>
                </div>
                <span class="formError" id="phoneError">
                    <?php
                    if (isset($_SESSION['phoneError'])) {
                        echo $_SESSION['phoneError'];
                        unset($_SESSION['phoneError']);
                    }
                    ?>
                </span>
                <div id="formSubmitButtons" class="inRegForm">
                    <input class="red" type="reset">
                    <span class="formError" id="emailDupeError"
                        style="font-size: 16px; text-align:center;">
                        <?php
                        if (isset($_SESSION['emailDupeError'])) {
                            echo $_SESSION['emailDupeError'];
                            unset($_SESSION['emailDupeError']);
                        }
                        ?>
                    </span>
                    <input class="green" type="submit">
                </div>
            </form>
        </section>
    </div>
</div>
<?php include 'data/templates/footer.html'; ?>
</body>

</html>