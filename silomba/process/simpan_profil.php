<?php
require_once __DIR__ . "/../includes/auth.php";
require_login();
set_flash("info", "Proses sedang dalam pengembangan.");
redirect("../index.php");
?>
