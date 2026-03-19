<?php
include_once $BASEDIR."/core/php/mainController.php";
include_once $BASEDIR."/core/php/sql.php";

class Workspace extends MainController {
    use MethodsSql;
    public static $tab = 'workspace';
    public static $inserPermit = [
        "name"
    ];
    public static $inserRequired = [
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
    public static function new($params){
        global $USER;
        foreach (self::$inserRequired as $required) {
            if (!isset($params[$required]) || empty($params[$required])) {
                $result['error'] = "Missing required field: $required";
                $result['data'] = [];
                return $result;
            }
        }
        
        $existing = self::sqlGet([
            "and" => [
                "name" => $params['name']
                ,"user" => $USER['user']
            ]
        ]);

        if (!empty($existing) && isset($existing[0])) {
            self::addError("Error on create workspace");
            self::response("This workspace name already exists");
            exit;
        }else{
            $insertId = self::insert(["name" => $params['name']]);
            if (is_array($insertId)) {
                self::addError("Failed create workspace");
                self::response("Error creating workspace");
                exit;
            }

            $userWorkspaceInsert = Sql::insert([
                "tab" => "user_workspace",
                "data" => [
                    "user" => $USER['user'],
                    "workspace_id" => $insertId,
                    "access_type" => 'owner'
                ]
            ]);

            if (!$userWorkspaceInsert || (is_array($userWorkspaceInsert) && isset($userWorkspaceInsert['errors']) && !empty($userWorkspaceInsert['errors']))) {
                self::addError("Failed to associate user with workspace.");
                self::response("Error creating workspace");
                exit;
            }

            self::response("Workspace created successfully", ["id" => $insertId]);
            
        }

    }
}