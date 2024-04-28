<?php
include_once "scripts/php/auth.php";
if (!isset($_SESSION["internal_id"])) {
    redirect_to("index.php");
}
include_once "scripts/php/accountUtilities.php";
include_once "scripts/php/getInventorySQL.php";
include_once 'data/templates/header.php'; ?>
<script src="scripts/javascript/accountScript.js"></script>
<div id="confirmDelete" style="display:none;">
    <span id="exitDelete" class="hidden" onclick="clearAndExit()"> X</span>
    <form action="scripts/php/changeForm.php" id="changeForm" method="POST">
        <section id="deleteButtons">
            <button class="left" type="button" id="cancelChangeButton"
                onclick="clearAndExit()">Cancel</button>
            <button class="right" type="submit" name="change"
                id="confirmChangeButton">Change</button>
        </section>
    </form>
</div><!-- end of confirm delete div -->
<div id="spacer"></div>
<div id="main">
    <div id="middle">
        <h1>Account Information</h1>
        <div id="accInfoBlock" style="display:flex;">
            <section id="accInfoCategories">
                <form action=""
                    method="GET"
                    class="infoCat"
                    onclick="this.submit()">
                    <input name="category" value="account information" hidden>
                    Account Information
                </form>
                <form action=""
                    method="GET"
                    class="infoCat"
                    onclick="this.submit()">
                    <input name="category" value="recent transactions" hidden>
                    Recent Transactions
                </form>
                <form action=""
                    method="GET"
                    class="infoCat"
                    onclick="this.submit()">
                    <input name="category" value="current listings" hidden>
                    Current Listings
                </form>
                <form action=""
                    method="GET"
                    class="infoCat"
                    onclick="this.submit()">
                    <input name="category" value="pending requests" hidden>
                    Pending Requests
                </form>
            </section><!--end of categories-->
            <div id="accInfoDisplay">
                <?php
                $category = isset($_GET['category']) ? $_GET['category'] : NULL;
                switch ($category) {
                    case 'account information':
                        displayAcc();
                        break;
                    case 'recent transactions':
                        displayTrans();
                        break;
                    case 'current listings':
                        displayList($data);
                        break;
                    case 'pending requests':
                        displayPend();
                        break;
                    default:
                        displayAcc();
                        break;
                }
                ?>
            </div>
        </div>
    </div>
</div>
</body>
<?php include_once 'data/templates/footer.html'; ?>

</html>