<?php
session_start();
$isSessionValid = false;

// Only check session if the required fields exist
if (
    (isset($_SESSION['sessionId']) || isset($_COOKIE['sessionId'])) &&
    (isset($_SESSION['sessionKey']) || isset($_COOKIE['sessionKey'])) &&
    (isset($_SESSION['user']) || isset($_COOKIE['user']))
) {
    
    include_once $BASEDIR."/core/php/curl.php";

    $params = [
        'sessionId'   => $_SESSION['sessionId']   ?? $_COOKIE['sessionId']   ?? null,
        'sessionKey'  => $_SESSION['sessionKey']  ?? $_COOKIE['sessionKey']  ?? null,
        'user'        => $_SESSION['user']        ?? $_COOKIE['user']        ?? null
    ];

    $response = BerericCurl::post('userManager', 'session.validate', $params);
    if (
        isset($response['data']) 
        && (
            $response['data'] === true
            || (is_array($response['data']) && isset($response['data']['valid']) && $response['data']['valid'])
        )
        && empty($response['errors'])
    ) {
        $isSessionValid = true;
    }
}

if ($isSessionValid) {
    echo loadLayout("home");
} else {
    echo loadLayout("login");
}
