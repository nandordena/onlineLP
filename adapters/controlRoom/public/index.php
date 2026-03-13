<?php
include_once __DIR__."/core/php/init.php";
include_once $BASEDIR."/core/php/curl.php";

$_ADAPTER="CONTROL_ROOM";

$session = BerericCurl::post('userManager','session.validate',[
    'user' => $_REQUEST['user']
    ,'sessionId' => $_REQUEST['sessionId']
    ,'sessionKey' => $_REQUEST['sessionKey']
]);

echo json_encode($session,true);

include_once $BASEDIR."/controlRoom.php";

$uri = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');

switch ($uri) {
    case '':
        echo '{"message":"Home","error":"404","errors":["invalid endpoint"]}';
        break;

    default:
        http_response_code(404);
        echo '{"error":"404","errors":["invalid endpoint"]}';
}