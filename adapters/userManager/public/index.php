<?php
$_ADAPTER="USER_MANAGER";
include_once __DIR__."/user.php";

$username = getenv($_ADAPTER.'_DB_HOST') ?: '';

echo $username;
echo "<br>".$_ADAPTER.'_DB_HOST';