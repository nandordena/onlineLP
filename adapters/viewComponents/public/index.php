<?
$_ADAPTER="VIEW_COMPONENTS";
include_once __DIR__."/core/init.php";

$uri = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');

switch ($uri) {
    case '':
        echo 'Home';
        break;

    case 'auth':
        include_once __DIR__."/auth/index.php";
        break;

    default:
        http_response_code(404);
        echo '404';
}