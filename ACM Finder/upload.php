<?php
include_once("scripts/php/auth.php");
if (!isset($_SESSION["internal_id"])) {
    redirect_to("index.php");
}
include_once 'data/templates/header.php';

?>
<script src="scripts/javascript/uploadScript.js"></script>
<div id="spacer"></div>
<!-- 
    something similar to login form should go here to handle
    duplicate panel uploads and successful uploads 
    ie. popup with dimming backdrop, if fail explain why
    if success ask user for directions to next destination
        ie. ie. upload more, account, listings to see where theirs is
-->
<div id="infoPopup" hidden>test</div>
<div id="main">
    <div id="middle">
        <h1>Panel Upload Form</h1>
        <section class="styleBox">
            <div id="panelReqForm">
                <!-- This is the main upload form for new panels -->
                <form id="registrationForm"
                    action="scripts/php/panelUploadForm.php"
                    method="POST">
                    <span style="color:red; font-size: small;">Required
                        fields(*) -- At least one selection(**)<br></span>
                    <!-- each section is a different set of inputs -->
                    <section class="inUplForm">
                        <label for="supplierUpl" id="sup"
                            class="giveInfo">Supplier<span
                                style="color:red;">*</span>: </label>
                        <input type="text" name="supplierUpl" id="supplierUpl"
                            placeholder="Supplier" required>
                    </section>
                    <section class="inUplForm"><label for="colRefUpl"
                            class="giveInfo">Colour/Reference ID<span
                                style="color:red;">**</span>: </label>
                        <div class="uplFormContainer">
                            <div class="uplFormMultiContainer">
                                <div class="uplFormSingleContainer">
                                    <label for="colourUpl"></label>
                                    <input type="text" id="colourUpl"
                                        name="colourUpl"
                                        placeholder="Colour" required>
                                </div>
                                <div class="uplFormSingleContainer">
                                    <label for="refUpl">
                                    </label>
                                    <input type="text" id="refUpl"
                                        name="refUpl"
                                        placeholder="ABC/DE-1234/etc" required>
                                </div>
                            </div>
                        </div>
                    </section>
                    <section class="inUplForm">
                        <label for="coreUpl" class="giveInfo">Core
                            Material<span
                                style="color:red;">**</span>: </label>
                        <div class="uplFormSingleContainer">
                            <label for="frUpl">Fire-Resistant</label>
                            <input type="radio" name="coreUpl"
                                id="frUpl" style="width:auto;" value="fr"
                                required>
                            <label for="peUpl">Polyethylene</label>
                            <input type="radio" style="width:auto;"
                                name="coreUpl"
                                id="peUpl" value="pe">
                            <label for="otherUpl">Other</label>
                            <input type="radio" style="width:auto;"
                                name="coreUpl"
                                id="otherUpl" value="other">
                        </div>
                    </section>
                    <section class="inUplForm">
                        <label for="dimUpl" class="giveInfo">Sheet Dimensions
                            (mm)<span
                                style="color:red;">*</span>: </label>
                        <div class="uplFormMultiContainer">
                            <div class="uplFormSingleContainer">
                                <input type="number" id="dimUplX"
                                    name="dimUplX"
                                    placeholder="Length">
                                <label for="dimUplX">mm</label>
                            </div>
                            <div class="uplFormSingleContainer"><input
                                    type="number" id="dimUplY"
                                    name="dimUplY"
                                    placeholder="Width">
                                <label for="dimUplY">mm</label>
                            </div>
                            <!-- if sheet size is invalid, post error here -->
                            <?php
                            if (isset($_SESSION['sheetSizeError'])) {
                                echo "<span class='formError'>"
                                    . $_SESSION['sheetSizeError'] . "</span>";
                                unset($_SESSION['sheetSizeError']);
                            }
                            ?>
                            <div class="uplFormSingleContainer">
                                <input type="number" id="dimUplZ"
                                    name="dimUplZ"
                                    placeholder="Thickness">
                                <label for="dimUplZ">mm</label>
                            </div>
                            <!-- if thickness is invalid -->
                            <?php
                            if (isset($_SESSION['dimZError'])) {
                                echo "<span class='formError'>"
                                    . $_SESSION['dimZError'] . "</span>";
                                unset($_SESSION['dimZError']);
                            }
                            ?>
                        </div>
                    </section>
                    <section class="inUplForm">
                        <label for="quantityUpl" class="giveInfo">Quantity<span
                                style="color:red;">*</span>: </label>
                        <input type="number" name="quantityUpl"
                            id="quantityUpl"
                            placeholder="# Sheets" required>
                    </section>
                    <!-- if quantity is invalid -->
                    <?php
                    if (isset($_SESSION['qtyError'])) {
                        echo "<span class='formError'>"
                            . $_SESSION['qtyError'] . "</span>";
                        unset($_SESSION['qtyError']);
                    }
                    ?>
                    <section class="inUplForm">
                        <label for="conditionUpl"
                            class="giveInfo">Condition<span
                                style="color:red;">*</span>: </label>
                        <select name="conditionUpl" id="conditionUpl"
                            style="padding:2px" required>
                            <option value="" selected disabled>Condition
                            </option>
                            <option value="T1">Good</option>
                            <option value="T2">Great</option>
                            <option value="T3">Excellent</option>
                            <option value="T4">Factory New</option>
                        </select>
                    </section>
                    <!-- is condition is unselected -->
                    <?php
                    if (isset($_SESSION['conditionError'])) {
                        echo "<span class='formError'>"
                            . $_SESSION['conditionError'] . "</span>";
                        unset($_SESSION['conditionError']);
                    }
                    ?>
                    <section class="inUplForm">
                        <label for="totalPriceUpl" class="giveInfo">Total Price
                            ($CAD)<span
                                style="color:red;">*</span>: </label>
                        <span>$<input type="text" name="totalPriceUpl"
                                id="totalPriceUpl"
                                required placeholder="CAD"></span>
                    </section>
                    <!-- if bulk price is invalid -->
                    <?php
                    if (isset($_SESSION['bulkPriceError'])) {
                        echo "<span class='formError'>"
                            . $_SESSION['bulkPriceError'] . "</span>";
                        unset($_SESSION['bulkPriceError']);
                    }
                    ?>
                    <section class="inUplForm">
                        <div class="uplFormSingleContainer">
                            <label for="allowIndividual" class="giveInfo">Allow
                                individual sales?</label>
                            <input type="checkbox" name="allowIndividual"
                                id="allowIndividual"
                                onclick="popupIndividual(this)">
                        </div>
                        <div class="uplFormSingleContainer"
                            id="indvPriceUplContainer" style="display:none">
                            <label for="indvPriceUpl" class="giveInfo">$ per
                                sheet<span
                                    style="color:red;">*</span>:</label>
                            $<input type="text" name="indvPriceUpl"
                                id="indvPriceUpl" placeholder="CAD/Sheet">
                        </div>
                    </section>
                    <!-- if individual price is invalid -->
                    <?php
                    if (isset($_SESSION['indvPriceError'])) {
                        echo "<span class='formError'>"
                            . $_SESSION['indvPriceError'] . "</span>";
                        unset($_SESSION['indvPriceError']);
                    }
                    ?>
                    <section class="inUplForm">
                        <div style="display:grid; width:calc(100% - 5px);">
                            <label for="notesUpl" class="giveInfo">Additional
                                Notes:</label>
                            <textarea name="notesUpl" id="notesUpl"
                                placeholder="unique core materials, bonus accessories/components, custom grain, pricing explanations, etc."
                                maxlength="1024" rows="5"></textarea>
                        </div>
                    </section>
                    <!-- if there are no notes to explain "other" core material -->
                    <?php
                    if (isset($_SESSION['notesOtherError'])) {
                        echo "<span class='formError'>"
                            . $_SESSION['notesOtherError'] . "</span>";
                        unset($_SESSION['notesOtherError']);
                    }
                    ?>
                    <div id="formSubmitButtons" class="inRegForm">
                        <input class="red" type="reset">
                        <input class="green" value="Submit" type="submit">
                    </div>
                </form>
            </div>
        </section>
    </div>
</div>
<?php include_once 'data/templates/footer.html'; ?>
</body>

</html>