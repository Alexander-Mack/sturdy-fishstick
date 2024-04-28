<?php
include_once('cartUtilities.php');
if(isset($_POST['changeQuantity'])){
    echo "change";
    $panel = $_POST['this_panel'];
    $new_quantity = $_POST['changeQuantity'];
    changeAmountInCart($panel, $new_quantity, $_SESSION['internal_id']);
    redirect_to("../../cart.php");
    // change quantity of that panel in cart
}else if (isset($_POST['cartPanelRemove'])){
    echo "remove";
    $panel = $_POST['cartPanelRemove'];
    $new_quantity = 0;
    changeAmountInCart($panel, $new_quantity, $_SESSION['internal_id']);
    redirect_to("../../cart.php");
    // remove all of that panel from cart
}