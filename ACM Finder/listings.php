<?php
include_once 'data/templates/header.php';
include_once "scripts/php/getInventorySQL.php";
include_once "scripts/php/listingUtilities.php";
?>
<script src="scripts/javascript/listingScript.js"></script>
<script src="scripts/javascript/selectionScript.js"></script>
<div id="spacer"></div>
<div id="searchTable">
    <form action="listings.php" method="POST" id="searchControls">
        <label><select name="supplierSelect" id="supplierSelect"></label>
        <option id="Supplier" value="default" selected>Supplier</option>
        <?php
        getSupplierOptions($data);
        ?>
        </select>
        <input type="submit" id="searchTableBtn" value="Search">
        <input type="text" name="searchTableField" id="searchTableField"
            placeholder="Enter Keywords" />
    </form>
    <div id="tableContainer" class="tableFixHead">
        <?php
        
        $filter = isset($_POST['searchTableField']) ? strtolower($_POST['searchTableField']) : "";
        $option = isset($_POST['supplierSelect']) ? strtolower($_POST['supplierSelect']) : "default";
        printTable($data, $filter, $option);
        ?>
    </div>
</div>
</body>
<?php include_once 'data/templates/footer.html'; ?>

</html>