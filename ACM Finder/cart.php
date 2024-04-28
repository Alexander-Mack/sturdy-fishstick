<?php
include_once("scripts/php/auth.php");
if (!isset($_SESSION["internal_id"])) {
    redirect_to("index.php");
}
include_once 'data/templates/header.php';
include_once 'scripts/php/getCart.php';
include_once 'scripts/php/cartUtilities.php';
include_once 'scripts/php/panelUtilities.php';
?>
<div id="spacer"></div>
<div id="main">
    <div id="middle">
        <div id="cartContainer">
            <?php
            $total = 0;
            $count = 0;
            $cart = getCart($conn = connectToDatabase(), [$_SESSION['internal_id']]);
            $taxRate = getTaxRate($_SESSION['province']);
            foreach ($cart as $pid) {
                $count++;
                $panel = getPanelByID($conn, $pid['panel_id']);
                $price = rand(100, 1000) + rand(0, 100) / 100;//$pid['quantity'] * $panel['PRICE'];
                $total = $total + $price;
                echo '<section class="inCart">
                    <div class="left">
                        <div class="cartLeft">
                            <!--src image goes here-->
                        </div>
                        <div class="cartRight">
                            <span class="cartPanelName">' . $panel['SUPPLIER']
                    . ', ' . $panel['REF'] . ' ' . $panel['COLOUR'] . '</span>
                            <form action="scripts/removeFromCart.php" method="POST" >
                                <label class="cartPanelQty">Quantity:</label> 
                                    <input type="number" name="changeQuantity" 
                                        max="' . $panel['QTY'] . '" class="cartQuantity" value="'
                    . $pid['quantity'] . '" min="1">
                                    <input hidden name="this_panel" value="' . $panel['PANEL ID'] . '">
                                <input type="submit" value="change"  class="changeQuantityButton">
                                    (max ' . $panel['QTY'] . ')
                                </label>
                            </form>
                        </div>
                    </div>
                    <div class="cartRight">
                        <span class="cartPanelValue">$' . number_format($price, 2, '.', '') . '</span>
                        <form action="scripts/removeFromCart.php" method="POST">
                            <button type="submit" class="cartPanelRemove" name="cartPanelRemove" value="' . $pid['panel_id'] . '">Remove</a>
                        </form>
                    </div>
                </section>';
            }
            if ($count == 0) {
                echo "<h3 style='text-align:center'>There is nothing in your cart at the moment</h3>";
                echo "</div>";
            } else {
                $checkoutTax = round($total * $taxRate, 2);
                $checkoutTotal = $total + $checkoutTax;
                echo "
                </div>
                    <div id='cartTotalContainer'>
                        <span id='cartTotalText' class='cartLeft'>Cart Total: </span>
                        <div class='cartRight'>
                        <span class='cartPanelValue'>$" . number_format($total, 2, '.', '') . "</span>
                        <span class='cartPanelTax'>+ $" . number_format($checkoutTax, 2, '.', '') . " tax </span>
                        <span style='border-top:dashed lightsteelblue 2px;'> $" . number_format($checkoutTotal, 2, '.', '') . "</span>
                    </div>
                </div>";
                echo <<<EOF
                <form action="scripts/checkoutForm.php" method="POST">
                <div id="formSubmitButtons">
                    <input type="submit" value="Checkout" class="green"
                        style="float:right; margin-right:5%; margin-top: 1px;">
                </div>
            </form>
            EOF;
                $_SESSION['checkout_total'] = $checkoutTotal;
            }
            // may need more spans in the cost side for tax/shipping costs
            $conn->close();
            ?>
            
        </div>
    </div>
    </body>
    <?php include_once 'data/templates/footer.html'; ?>