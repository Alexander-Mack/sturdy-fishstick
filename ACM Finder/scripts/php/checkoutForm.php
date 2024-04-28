<?php
include_once ("auth.php");
// This is where I would keep my checkout form, if I had one
echo "<pre>";
echo var_dump(get_defined_vars());
echo "</pre>";
// connect to the payment processor
// redirect to success page
redirect_to('../../index.php');