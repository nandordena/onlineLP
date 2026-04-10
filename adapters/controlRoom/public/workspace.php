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
        $where[]= "uw.user = '".$_COOKIE['user']."'";
        $sql = "
            SELECT ws.*
            FROM workspace ws
            JOIN user_workspace uw ON uw.workspace_id = ws.id
            WHERE
        ". join(" AND " , $where );

        $result = Sql::query(['querystring'=>$sql]);
        return $result;
    }
    public static function new($params){
        global $USER;
        foreach (self::$inserRequired as $required) {
            if (!isset($params[$required]) || empty($params[$required])) {
                self::addError("Missing required field: $required");
                return $result;
            }
        }
        
        $existing = self::getByUser(["ws.name = '".$params['name']."'"]);

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

            if (
                $userWorkspaceInsert!=="0"
                || (
                    is_array($userWorkspaceInsert)
                    && isset($userWorkspaceInsert['errors'])
                    && !empty($userWorkspaceInsert['errors'])
                )
            ) {
                self::sqlDeleteById(($insertId)*1);
                self::addError("Failed to associate user with workspace.");
                self::response("Error creating workspace");
                exit;
            }

            self::response("Workspace created successfully", [
                "id" => $insertId
                ,"name" => $params['name']
            ]);
            exit;
        }

    }

    public static function remove($params){
        // Check if user is owner of the workspace
        global $USER;

        $id = $params['id'] ?? null;
        if (!$id) {
            self::addError("Workspace id missing.");
            self::response("No workspace id provided.");
            exit;
        }

        $userWs = Sql::query([
            "tab" => "user_workspace",
            "query" => ["and"=>[
                "user" => $USER['user'],
                "workspace_id" => $id,
                "access_type" => 'owner'
            ]]
        ]);

        if (!$userWs || !isset($userWs[0])) {
            self::addError("You are not the owner of this workspace.");
            self::response("Only owners can remove the workspace.");
            exit;
        }

        $delete = self::sqlDeleteById([$id]);

        if ($delete) {
            self::response("Workspace removed successfully");
        } else {
            self::addError("Failed to delete workspace.");
            self::response("Error removing workspace");
        }
        exit;
    }
}