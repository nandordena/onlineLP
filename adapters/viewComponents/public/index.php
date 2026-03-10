<?
include_once __DIR__."/core/php/init.php";
include_once $BASEDIR."/commons/php/functions.php";
include_once $BASEDIR."/commons/css/mainStyle.php";
include_once $BASEDIR."/core/js/fetch.php";
include_once $BASEDIR."/core/js/cookies.php";

$_ADAPTER="VIEW_COMPONENTS";

$uri = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');

switch ($uri) {
    case '':
    case 'home':
        include_once $BASEDIR."/app/web/index.php";
        break;

    case 'auth':
        include_once $BASEDIR."/ui/components/auth/index.php";
        break;

    default:
        http_response_code(404);
        echo '404';
}