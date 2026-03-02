<?php
include_once __DIR__."/core/init.php";

$_ADAPTER="USER_MANAGER";

include_once __DIR__."/user.php";
include_once __DIR__."/session.php";

$uri = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');

$sessionEndpoints = [
    
];
$userEndpoints = [
    "new"
];

switch ($uri) {
    case '':
    case 'session':{
        http_response_code(404);
        echo '{"error":"404","errors":["invalid endpoint"]}';
        break;
    }

    default:{
        if (
            in_array($uri, $userEndpoints)
            && method_exists($USER, $uri)
        ) {
            $result = $USER->$uri($_REQUEST);
            $MAIN->response(
                "Endpoint OK",
                $result['errors'],
                $result['data']
            );
        } else {
            http_response_code(404);
            echo '{"error":"404","errors":["invalid endpoint"]}';
        }
        break;
    }
}