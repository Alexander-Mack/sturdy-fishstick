<?php
include_once "scripts/php/auth.php";
if (!isset($_SESSION["internal_id"])) {
    redirect_to("index.php");
}
$type = true;
include_once 'data/templates/header.php';
if (isset($_POST['panel_id'])) {
    $type = true;
    include_once 'scripts/php/getPanel.php';
} else if (isset($_POST['request_id'])) {

    $type = false;
    include_once "scripts/php/requestUtilities.php";
    getRequest($_POST['request_id']);
}
?>
<script src="scripts/javascript/accountScript.js"></script>
<div id="confirmDelete" style="display:none;">
    <span id="exitDelete" class="hidden" onclick="exitDelete()"> X</span>
    <form class="hidden" id="deleteForm" action=<?php
    if ($type) {
        echo "scripts/php/removeFromInventory.php";
    } else {
        echo "scripts/php/removeFromRequests.php";
    } ?> method="POST">
        <div id="deleteWarning">Are you sure you want to delete this
            <?php if ($type) {
                echo "panel?";
            } else {
                echo "request?";
            }
            ?>
            This cannot be undone.
        </div>
        <section id="deleteButtons">
            <button class="left" type="submit" name="delete" id="confirmDeleteButton">Yes, I am sure</button>
            <button class="right" type="button" id="cancelDeleteButton" onclick="exitDelete()">No, Cancel</button>
        </section>
        <?php
        if ($type) {
            echo '<input hidden type="text" name="panel_id"
            value="' . checkSet('PANEL ID') . '">';
        } else {
            echo '<input hidden type="text" name="request_id"
            value="' . $_SESSION['request']['internal_id'] . '">';
        }
        ?>
    </form>
</div><!-- end of confirm delete div -->
<div id="spacer"></div>
<div id="main">
    <div id="infoBox">
        <?php
        if ($type) {
            printPanelInfo();
        } else {
            printRequestInfo();
        }
        ?>
        <section id="postTableContainer">
            <button type="button" class="listingOptions" id="addToCart" name="returnToAcc" onclick=<?php
            if ($type) {
                echo "location.href='account.php?category=current+listings'";
            } else {
                echo "location.href='account.php?category=pending+requests'";
            }

            ?>>
                RETURN TO ACCOUNT PAGE</button>
            <button type="button" class="listingOptions" id="goBack" name="delete" onclick="confirmDelete()">
                DELETE</button>
        </section>
    </div>
    <div class="pre-footer"></div>
    </body>
    <?php include_once 'data/templates/footer.html'; ?>

    </html>