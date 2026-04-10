<?
include_once __DIR__."/core/php/init.php";
include_once $BASEDIR."/commons/php/functions.php";
function includeCommonsFront(){
    global $BASEDIR;
    include_once $BASEDIR."/commons/css/mainStyle.php";
    include_once $BASEDIR."/core/js/fetch.php";
    include_once $BASEDIR."/core/js/cookies.php";
}

$_ADAPTER="VIEW_COMPONENTS";

$uri = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');

$endpoints = [
    "components.workspace"
    ,"form.null"
    ,"layouts.null"
];

switch ($uri) {
    case '':
    case 'home':
        includeCommonsFront();
        include_once $BASEDIR."/app/web/index.php";
        break;

    case 'auth':
        includeCommonsFront();
        include_once $BASEDIR."/ui/components/auth/index.php";
        break;

    default:
        // Ahora, genera de manera escalable basado en el formato de $uri:
        // ejemplo: "componets.workspace" -> "/ui/components/componets/workspace/index.php"
        if (in_array($uri, $endpoints)) {
            $parts = explode('.', $uri);
            if (count($parts) == 2) {
                $componentDir = $parts[0];
                $componentFile = $parts[1];
                $filePath = $BASEDIR . "/ui/{$componentDir}/{$componentFile}/index.php";
                if (file_exists($filePath)) {
                    ob_start();
                    includeCommonsFront();
                    $data = $_REQUEST;
                    include_once $filePath;
                    $htmlContent = ob_get_clean();
                    header('Content-Type: application/json');
                    // Escape problematic JSON chars, especially control and embed tags
                    echo json_encode(['content' => $htmlContent], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE);
                } else {
                    header('Content-Type: application/json');
                    echo json_encode(['error' => "Component for endpoint '$uri' not found at $filePath."], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE);
                }
                exit;
       
           
            } else {
                http_response_code(404);
                echo '404';
                exit;
            }
        }
        http_response_code(404);
        echo '404';
        exit;
}