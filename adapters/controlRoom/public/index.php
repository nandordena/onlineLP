<?php
include_once __DIR__."/core/init.php";

$_ADAPTER="CONTROL_ROOM";

include_once __DIR__."/controlRoom.php";

$uri = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');

switch ($uri) {
    case '':
        echo 'Home';
        break;

    case 'miendpoint':
        echo 'Endpoint OK';
        break;

    default:
        http_response_code(404);
        echo '404';
}