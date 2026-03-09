<?php

function loadUiElement($element, $data = [], $type = "components") {
    global $BASEDIR;

    $files = [
        "styles.php"
        ,"scripts.php"
        ,"index.php"
    ];
    
    $result = "";
    foreach ($files as $file) {
        ob_start();
        $uiPath = $BASEDIR . "/ui/{$type}/{$element}/{$file}";
        if (!file_exists($uiPath)) {
            echo "<!-- UI element file not found: {$type}:{$element}:{$element}/{$file} -->";
        }else{
            echo "<!-- UI element file load: {$type}:{$element}:{$file} -->";
        }
        // Make data variables available inside the included file

        extract($data, EXTR_OVERWRITE);
        include $uiPath;
        $result .= ob_get_clean();
    }
    return $result;
}
function loadComponent($element, $data = []){
    return loadUiElement($element,$data = [],"components");
}
function loadLayout($element, $data = []){
    return loadUiElement($element,$data = [],"layouts");
}
function loadForm($element,$data = []){
    return loadUiElement($element,$data = [],"forms");
}

function validateSession(){
    $result = post("userManager","session.validate",[
        "sessionId"=>$_COOKIE['sessionId']
        ,"user"=>$_COOKIE['user']
    ]);
    return $result; 
}