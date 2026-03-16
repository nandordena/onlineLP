<?php
include_once $BASEDIR."/core/php/mainController.php";
include_once $BASEDIR."/core/php/sql.php";



class Workspace extends MainController {
    use MethodsSql;
    public static $tab = 'workspace';
    public static $inserPermit = [
        "name"
    ];

    public static function getByUser(){
        $sql = "
            SELECT ws.*
            FROM workspace ws
            JOIN user_workspace uw ON uw.workspace_id = ws.id
            WHERE uw.user = '".$_COOKIE['user']."' 
        ";
        $result = Sql::query(['querystring'=>$sql]);
        return $result;
    }
}