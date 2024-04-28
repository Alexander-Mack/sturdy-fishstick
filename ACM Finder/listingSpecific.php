<?php
include_once 'data/templates/header.php';
include_once 'scripts/php/getPanel.php';
?>
<script src="scripts/javascript/selectionScript.js"></script>
<div id="spacer"></div>
<div id="main">
    <div id="infoBox">
        <div id="SUPPLIER"><span class="left">Supplier</span>
            <span class="right">
                <?php checkSet('SUPPLIER'); ?>
            </span>
        </div>
        <div id="REF"><span class="left">Colour Reference</span>
            <span class="right">
                <?php checkSet('REF'); ?>
            </span>
        </div>
        <div id="COLOUR"><span class="left">Sheet Colour</span>
            <span class="right">
                <?php checkSet('COLOUR'); ?>
            </span>
        </div>
        <div id="CORE"><span class="left">Core Material</span>
            <span class="right">
                <?php checkSet('CORE'); ?>
            </span>
        </div>
        <div id="SHEET_SIZE"><span class="left">Sheet Size
                (milimeters&sup2)</span>
            <span class="right">
                <?php checkSet('SHEET SIZE'); ?>
            </span>
        </div>
        <div id="QTY"><span class="left">Quantity of Sheets</span>
            <span class="right">
                <?php checkSet('QTY'); ?>
            </span>
        </div>
        <div id="NOTES"><span class="left">Notes</span>
            <span class="right">
                <?php checkSet('NOTES'); ?>
            </span>
        </div>
        <div id="CONDITION"><span class="left">Condition of Sheets</span>
            <span class="right">
                <?php checkSet('CONDITION'); ?>
            </span>
        </div>
        <div id="FIRST_UPLOADED"><span class="left">Date of Upload</span>
            <span class="right">
                <?php checkSet('FIRST UPLOADED'); ?>
            </span>
        </div>
        <div id="LOCATION"><span class="left">Province of Origin</span>
            <span class="right">
                <?php checkSet('LOCATION'); ?>
            </span>
        </div>
        <div id="COSTPERSHEET"><span class="left">Cost Per Sheet (CAD$)</span>
            <span class="right">
                <?php checkSet('PRICE'); ?>
            </span>
        </div>
        <div id="COSTBULK"><span class="left">Bulk Cost(CAD$)</span>
            <span class="right">
                <?php checkSet('BULK PRICE'); ?>
            </span>
        </div>
    </div>
    <form id="postTableContainer" action="scripts/php/addToCart.php" method="POST">
        <button type="button" class="listingOptions" id="addToCart"
            <?php
            if (isset($_SESSION["email"])) {
                echo 'onclick="addToCartAll()"';
            } else {
                echo 'onclick="enterPopup()"';
            }
            ?>>ADD ALL TO CART</button>
        <div id="addPartialContainer" style="display:grid">
            <input type="number" id="addSelectedQTY"
                name="addSelectedQTY">
            <button type="button" id="addPartial"
                <?php
                if (isset($_SESSION["email"])) {
                    echo 'onclick="addToCartPartial()"';
                } else {
                    echo 'onclick="enterPopup()"';
                }
                ?>>SELECT AMOUNT TO ADD</button>
        </div>
        <button type="button" class="listingOptions" id="goBack"
            onclick="listingsPage()">RETURN TO LISTINGS</button>
        <input hidden type="text" name="panel_id"
            value="<?php checkSet('PANEL ID'); ?>">
    </form>
</div>
<div class="pre-footer"></div>
</body>
<?php include_once 'data/templates/footer.html'; ?>

</html>