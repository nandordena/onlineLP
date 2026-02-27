<?php
$_ADAPTER="USER_MANAGER";
include_once __DIR__."/core/init.php";
include_once __DIR__."/user.php";
include_once __DIR__."/session.php";

var_dump($USER->new("nandordena@gmail.com","P1214-64ArcO"));
//$SESSIONS->createSession();