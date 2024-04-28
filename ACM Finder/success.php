<?php
include_once "scripts/php/auth.php";
include_once "data/templates/headerNoBanner.php";
?>
<div id="spacer"></div>
<div id="main">
    <div id="middle">
        <section class="styleBox">
            <h1><?php echo isset($_SESSION['success_response'])
                ? $_SESSION['success_response'] : "Logout Successful!"; ?></h1>
            <h2><a href='<?php echo isset($_SESSION['redirect'])
                ? $_SESSION['redirect'] : "index.php"; ?>'>Click here to continue!</a>
            </h2>
        </section>
    </div>
</div>
</body>

</html>