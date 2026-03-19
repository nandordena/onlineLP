<?php

function loadUiElement($element, $data = [], $uiType = "components") {
    global $BASEDIR,$INIT;

    $files = [
        "styles.php"
        ,"scripts.php"
        ,"index.php"
    ];
    
    $result = "";
    foreach ($files as $file) {
        ob_start();
        $uiPath = $BASEDIR . "/ui/{$uiType}/{$element}/{$file}";
        if (!file_exists($uiPath)) {
            echo "<!-- UI element file not found: {$uiType}:{$element}:{$element}/{$file} -->";
        }else{
            extract($data, EXTR_OVERWRITE);
            if ($file === "index.php") {
                include $uiPath;
                echo "<!-- UI element file load: {$uiType}:{$element}:{$file} -->";
            } else {
                $includes = include_once $uiPath;
                if($includes === 1){
                    echo "<!-- UI element file load: {$uiType}:{$element}:{$file} -->";
                };
            }
        }
        
        $result .= ob_get_clean();
    }
    return $result;
}
function loadComponent($element, $data = []){
    return loadUiElement($element,$data,"components");
}
function loadLayout($element, $data = []){
    return loadUiElement($element,$data,"layouts");
}
function loadForm($element,$data = []){
    return loadUiElement($element,$data,"forms");
}

function validateSession(){
    $result = post("userManager","session.validate",[
        "sessionId"=>$_COOKIE['sessionId']
        ,"user"=>$_COOKIE['user']
    ]);
    return $result; 
}

//PREPRINTS
//JS
function initJsClass($class,$object){
    if (!is_string($class) || !is_string($object)) {
        return '';
    }
    echo "
        if (typeof window.{$class} === 'undefined' || typeof window.{$class} !== 'function') {
            window.{$class} = class {$class} {};
        }
        if (typeof window.{$object} === 'undefined' || !(window.{$object} instanceof window.{$class})) {
            window.{$object} = new window.{$class}();
        }
    ";    
}