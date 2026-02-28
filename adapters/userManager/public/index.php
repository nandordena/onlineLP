<?php
$_ADAPTER="USER_MANAGER";
include_once __DIR__."/core/init.php";
include_once __DIR__."/user.php";
include_once __DIR__."/session.php";

$uri = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');

$sessionEndpoints = [
    
];
$userEndpoints = [
    "new"
];

switch ($uri) {
    case '':{
        http_response_code(404);
        echo '404';
        break;
    }
    
    case 'session':{
        echo 'Home';
        break;
    }

    default:{
        if (
            in_array($uri, $userEndpoints)
            && method_exists($USER, $uri)
        ) {
            echo $USER->$uri($_REQUEST);
        } else {
            http_response_code(404);
            echo '404';
        }
        break;
    }
}