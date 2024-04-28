<?php
include_once("scripts/php/auth.php");
if (!isset($_SESSION["internal_id"])) {
    redirect_to("index.php");
}
// clear billing confirmation
if (isset($_SESSION['billing_confirmed'])) {
    unset($_SESSION['billing_confirmed']);
}
include_once ("data/templates/headerNoBanner.php");
// if set, keep track of where the user came from
if (isset($_SESSION['REDIRECTED_FROM'])) {
    $redirectedFrom = $_SESSION['REDIRECTED_FROM'];
}

if (isset($redirectedFrom)) {
    //disambiguate, but show billing info
} else {
    $redirectedFrom = "index.php";
    //user is from registration, prompt for billing info, but pass if desired

}
?>
<div id="spacer"></div>
<div id="main">
    <div id="middle">
        <section class="styleBox">
            <form action="scripts/php/billingForm.php" method="POST"
                id="registrationForm" style="width: 70%; min-width:300px;">
                <div id="billingFormContainer">
                    <span style="color:red; font-weight:bold;">THIS INFORMATION
                        IS NOT BEING STORED</span>
                    <section class="inUplForm">
                        <label for="billingCardNickname">Nickname</label>
                        <input type="text" id="billingCardNickname"
                            name="billingCardNickname"
                            placeholder="Card Nickname"
                            autocomplete="nickname">
                    </section>
                    <section class="inUplForm">
                        <label for="billingCardName">Name on Card<span
                                style="color:red;">*</span></label>
                        <input type="text" id="billingCardName"
                            name="billingCardName"
                            placeholder="Full Name" autocomplete="cc-name">
                    </section>
                    <section class="inUplForm">
                        <label for="billingCardNumber">Card Number<span
                                style="color:red;">*</span></label>
                        <input type="tel"
                            inputmode="numeric"
                            pattern="[0-9\s]{13,19}"
                            id="billingCardNumber"
                            name="billingCardNumber"
                            placeholder="xxxx xxxx xxxx xxxx"
                            maxlength="19"
                            autocomplete="cc-number">
                    </section>
                    <section class="inUplForm">
                        <label for="billingCardExpiration">Expiration Date<span
                                style="color:red;">*</span></label>
                        <div name="billingCardExpiration"
                            class="uplFormSingleContainer">
                            <label for="billingCardExpMonth">Month:</label>
                            <select name="billingCardExpMonth"
                                id="billingCardExpMonth"
                                autocomplete="cc-exp-month">
                                <option value="01">01</option>
                                <option value="02">02</option>
                                <option value="03">03</option>
                                <option value="04">04</option>
                                <option value="05">05</option>
                                <option value="06">06</option>
                                <option value="07">07</option>
                                <option value="08">08</option>
                                <option value="09">09</option>
                                <option value="10">10</option>
                                <option value="11">11</option>
                                <option value="12">12</option>
                            </select>
                            <label for="billingCardExpYear">Year:</label>
                            <select name="billingCardExpYear"
                                id="billingCardExpYear"
                                autocomplete="cc-exp-year">
                                <?php
                                $this_year = intval(Date("Y"));
                                $twenty_years = $this_year + 20;
                                for ($year = $this_year; $year <= $twenty_years; $year++) {
                                    echo "<option value='" . $year . "'>" . $year . "</option>";
                                }

                                ?>
                            </select>
                        </div>
                    </section>
                    <section class="inUplForm">
                        <label for="billingCardAddress">Billing Address<span
                                style="color:red;">*</span></label>
                        <div class="uplFormMultiContainer">
                            <div class="uplFormSingleContainer">
                                <label for="streetReg">Address: </label>
                                <input id="streetReg" name="streetReg"
                                    type="text"
                                    autocomplete="street-address"
                                    placeholder="Street Address">
                            </div>
                            <div class="uplFormSingleContainer">
                                <label for="cityReg">City: </label>
                                <input id="cityReg" name="cityReg" type="text"
                                    autocomplete="address-level2"
                                    placeholder="City">
                            </div>
                            <div class="uplFormSingleContainer"><label
                                    for="provinceReg">Province: </label>
                                <select id="provinceReg" name="provinceReg">
                                    <option selected value="" disabled> Please
                                        choose one </default>
                                    <option value="AB">Alberta</option>
                                    <option value="BC">British Columbia
                                    </option>
                                    <option value="MB">Manitoba</option>
                                    <option value="NB">New Brunswick</option>
                                    <option value="NL">Newfoundland and
                                        Labrador </option>
                                    <option value="NT">Northwest Territories
                                    </option>
                                    <option value="NS">Nova Scotia</option>
                                    <option value="NU">Nunavut</option>
                                    <option value="ON">Ontario</option>
                                    <option value="PE">Prince Edward Island
                                    </option>
                                    <option value="QC">Québec</option>
                                    <option value="SK">Saskatchewan</option>
                                    <option value="YK">Yukon</option>
                                </select>
                            </div>
                            <div class="uplFormSingleContainer">
                                <label for="postReg">Postal Code: </label>
                                <input id="postReg" name="postReg" type="text"
                                    autocomplete="postal-code"
                                    placeholder="Postal Code">
                            </div>
                        </div>
                    </section>
                    <div id="formSubmitButtons" class="inRegForm">
                        <input class="red" type="button" value="Cancel"
                            onclick="location.href = '<?php echo $redirectedFrom; ?>'">
                        <input class="green" value="Submit" type="submit">
                    </div>
                </div>
            </form>
        </section>
    </div>
</div>
</body>

</html>