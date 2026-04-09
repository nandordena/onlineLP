<?php
include_once __DIR__."/core/php/init.php";
include_once $BASEDIR."/core/php/curl.php";
include_once $BASEDIR."/core/php/user.php";

$_ADAPTER="CONTROL_ROOM";

$session = BerericCurl::post('userManager','session.validate',[
    'user' => $_REQUEST['user']
    ,'sessionId' => $_REQUEST['sessionId']
    ,'sessionKey' => $_REQUEST['sessionKey']
]);

if (
    !isset($session['data'])
    || !($session['data'] === true)
) {
    http_response_code(401);
    BerericCurl::post('userManager','session.logout',[
        'user' => $_REQUEST['user']
        ,'sessionId' => $_REQUEST['sessionId']
        ,'sessionKey' => $_REQUEST['sessionKey']
    ]);
    self::addError("session invalid");
    self::response("error");
    exit;
}

include_once $BASEDIR."/controlRoom.php";
include_once $BASEDIR."/workspace.php";

$uri = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');

$workspaceEndpoints = [
    "Workspace.getByUser"
    ,"Workspace.new"
    ,"Workspace.remove"
];
$roomsEndpoints = [
];

switch ($uri) {
    case '':
        echo '{"message":"Home","error":"404","errors":["invalid endpoint"]}';
        break;

    default:
        if (
            in_array($uri, $roomsEndpoints)
            && method_exists("Room", $uri)
        ) {
            $result = Room::$uri($_REQUEST);
            if(method_exists("Room", $endpoint)){
                MainController::response(
                    "Endpoint OK",
                    $result['data']
                );
            }
            die();
        }
        if (in_array($uri, $workspaceEndpoints)) {
            $endpoint = str_replace("Workspace.","",$uri);
            if(method_exists("Workspace", $endpoint)){
                $result['data'] = Workspace::$endpoint($_REQUEST);
                MainController::response(
                    "Endpoint OK",
                    $result['data']
                );
            }
            die();
        }
        break;
}