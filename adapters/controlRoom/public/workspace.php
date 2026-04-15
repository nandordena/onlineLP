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

    public static function getMainWorispace(){
        // Get the user's main workspace; if it doesn't exist, build it and return the result
        global $USER;
        $user = isset($USER['user']) ? $USER['user'] : (isset($_COOKIE['user']) ? $_COOKIE['user'] : null);
        if (!$user) {
            return null;
        }
        $sql = "
            SELECT ws.*
            FROM workspace ws
            JOIN user_workspace uw ON uw.workspace_id = ws.id
            WHERE uw.user = '".addslashes($user)."' AND uw.access_type = 'main'
            LIMIT 1
        ";
        $mainWorkspace = Sql::query(['querystring' => $sql]);
        if (!empty($mainWorkspace) && isset($mainWorkspace[0])) {
            return $mainWorkspace[0];
        }
        return self::buildMainWorispace();
    }

    public static function buildMainWorispace(){
        // Check if the user has a "main" workspace. If not, create one with the user's own name.
        global $USER;
        $user = isset($USER['user']) ? $USER['user'] : (isset($_COOKIE['user']) ? $_COOKIE['user'] : null);
        if (!$user) {
            return null;
        }

        // 1. Search if there is a workspace associated as 'main' for this user
        $sql = "
            SELECT ws.*
            FROM workspace ws
            JOIN user_workspace uw ON uw.workspace_id = ws.id
            WHERE uw.user = '".addslashes($user)."' AND uw.access_type = 'main'
            LIMIT 1
        ";
        $mainWorkspace = Sql::query(['querystring'=>$sql]);
        if (!empty($mainWorkspace) && isset($mainWorkspace[0])) {
            return $mainWorkspace[0];
        }

        // 2. If it does not exist, create a new workspace with the user's name
        $workspaceId = self::insert(['name'=>$user]);
        if (is_array($workspaceId)) {
            // Error creating the workspace
            return null;
        }

        // 3. Insert into user_workspace as 'main'
        Sql::insert([
            "tab" => "user_workspace",
            "data" => [
                "user" => $user,
                "workspace_id" => $workspaceId,
                "access_type" => 'main'
            ]
        ]);

        // 4. Return the created workspace
        $ws = Sql::query(['querystring' => "SELECT * FROM workspace WHERE id = ".intval($workspaceId)]);
        return !empty($ws) ? $ws[0] : null;
    }

    public static function getByUser(){
        self::buildMainWorispace();
        $where[]= "uw.user = '".$_COOKIE['user']."'";
        $sql = "
            SELECT ws.*, uw.access_type
            FROM workspace ws
            JOIN user_workspace uw ON uw.workspace_id = ws.id
            WHERE
        ". join(" AND " , $where );
        $result = Sql::query(['querystring'=>$sql]);
        if (empty($result)) {
            $mainWorkspace = self::getMainWorispace();
            if ($mainWorkspace) {
                $result = [$mainWorkspace];
            }
        }
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