<?php
include_once __DIR__."/core/php/init.php";

$_ADAPTER="USER_MANAGER";

include_once __DIR__."/user.php";
include_once __DIR__."/session.php";

$uri = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');

$sessionEndpoints = [
    "session.validate"
];
$userEndpoints = [
    "new"
    ,"login"
];
switch ($uri) {
    case '':
    case 'user':
    case 'session':{
        http_response_code(404);
        echo '{"error":"404","errors":["invalid endpoint"]}';
        break;
    }

    default:{
        if (
            in_array($uri, $userEndpoints)
            && method_exists("User", $uri)
        ) {
            $result = User::$uri($_REQUEST);
            MainController::response(
                "Endpoint OK",
                $result['errors'],
                $result['data']
            );
            die();
        }
        if (in_array($uri, $sessionEndpoints)) {
            $endpoint = str_replace("session.","",$uri);
            if(method_exists("Session", $endpoint)){
                $result['data'] = Session::$endpoint($_REQUEST);
                MainController::response(
                    "Endpoint OK",
                    $result['errors'],
                    $result['data']
                );
                die();
            }
        }
        break;
    }
}

http_response_code(404);
echo '{"error":"404","errors":["invalid endpoint"]}';
