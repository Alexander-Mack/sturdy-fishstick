<?php
include_once("scripts/php/auth.php");
if (!isset($_SESSION["internal_id"])) {
    redirect_to("index.php");
}
include_once 'data/templates/header.php'; ?>
<script src="scripts/javascript/formScript.js"></script>
<div id="spacer"></div>
<div id="main">
    <div id="middle">
        <h2 style="text-align: justify;"><span
                style="font-size: 30px; text-decoration: underline;"> Unable to
                find what you were looking for?</span>
            <br>If it is not in our inventory you may submit a request<br> form
            and we will contact you when it becomes available!
        </h2>
        <section class="styleBox">
            <div id="panelReqForm">
                <form id="requestForm" action="scripts/php/requestForm.php"
                    method="POST">
                    <span
                        style="color:red; font-size: small;">Required fields(*)
                        -- At least one selection(**)<br></span>
                    <div class="inReqForm">
                        <label for="supplierReq">Supplier<span
                                style="color:red;">*</span>: </label>
                        <input type="text" name="supplierReq" id="supplierReq"
                            placeholder="Supplier" required>
                    </div>
                    <div class="inReqForm">
                        <span>Colour/Reference ID<span
                                style="color:red;">**</span>: </span>
                        <div id=colourForms>
                            <label for="colourReq">Colour: </label>
                            <input type="text" id="colourReq" name="colourReq"
                                placeholder="Colour" required>
                            <label for="refReq">Or Reference ID: </label>
                            <input type="text" id="refReq" name="refReq"
                                placeholder="ABC/DE-1234/etc" required>
                        </div>
                    </div>
                    <div class="inReqForm">
                        <span>Core Material<span
                                style="color:red;">**</span>: </span>
                        <div id="coreRadio">
                            <label for="frReq">Fire-Resistant</label>
                            <input type="radio" name="coreReq"
                                id="frReq" value="fr" required>
                            <label for="peReq">Polyethylene</label>
                            <input type="radio" name="coreReq"
                                id="peReq" value="pe">
                                <label for="otherReq">Any</label>
                            <input type="radio" style="width:auto;"
                                name="coreReq"
                                id="otherReq" value="other">
                        </div>
                    </div>
                    <div class="inReqForm">
                        <span>Minimum Dimensions (mm): </span>
                        <div id="minDim">
                            <input type="number" id="minDimReqX"
                                name="minDimReqX"
                                placeholder="Length">
                            <label for="minDimReqX">by</label>
                            <input type="number" id="minDimReqY"
                                name="minDimReqY"
                                placeholder="Width">
                            <label for="minDimReqY"></label>
                        </div>
                    </div>
                    <div id="formSubmitButtons" class="inReqForm">
                        <input class="red" type="reset">
                        <?php if(isset($_SESSION['requestError'])){
                            echo "<span style='color:red'>".$_SESSION['requestError']."</span>";
                            unset($_SESSION['requestError']);
                        }?>
                        <input class="green" type="submit"
                            onclick="checkColourRef()">
                    </div>
                </form>
            </div>
        </section>
    </div>
</div>
<?php include_once 'data/templates/footer.html'; ?>
</body>

</html>