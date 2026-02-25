<?php
$_ADAPTER="CONTROL_ROOM";
include_once __DIR__."/controlRoom.php";
include_once __DIR__."/core/user.php";


$username = getenv($_ADAPTER.'_DB_HOST') ?: '';

echo $username;
echo "<br>".$_ADAPTER.'_DB_HOST';