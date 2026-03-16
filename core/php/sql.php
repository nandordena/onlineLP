<?php
class Sql{
    protected $pdo;

    //Conetion
    public static function pdo() {
        global $_ADAPTER;
        $result = [];
        try {
            $username = getenv($_ADAPTER.'_DB_USER') ?: '';
            $password = getenv($_ADAPTER.'_DB_PASS') ?: '';
            $host = getenv($_ADAPTER.'_DB_HOST') ?: '';
            $db   = getenv($_ADAPTER.'_DB_NAME') ?: '';
            $options = [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                \PDO::ATTR_EMULATE_PREPARES => false,
            ];
            $dsn = "mysql:host=$host;dbname=$db;charset=utf8mb4";
            return new \PDO($dsn, $username, $password, $options);
        } catch (\PDOException $e) {
            $result['error'] = "Database connection failed: " . $e->getMessage();
            $result['data'] = [];
            echo json_encode($result,true);
            die();
        }
    }

    //QUERYS
    public static function query($params){
        $result = [];
        try {
            // Build a PDO query with the given params
            $tab = isset($params['tab']) ? $params['tab'] : null;
            $conditions = isset($params['query']['and']) ? $params['query']['and'] : [];
            $query = isset($params['querystring']) ? $params['querystring'] : null;
            if ((!$query || empty($query)) && (!$tab || empty($conditions))) {
                $result['error'] = "Invalid query parameters";
                $result['data'] = [];
                echo json_encode($result,true);
                die();
            }

            // Build WHERE clause with named placeholders
            if(isset($query)){
                $sql = $query;
            }else{
                $whereParts = [];
                foreach ($conditions as $column => $value) {
                    $whereParts[] = "$column = :$column";
                }
                $whereClause = implode(" AND ", $whereParts);
                $sql = "SELECT * FROM `$tab` WHERE $whereClause";
            }

            // Prepare SQL
            $pdo = static::pdo();
            $stmt = $pdo->prepare($sql);

            // Bind values
            foreach ($conditions as $column => $value) {
                $stmt->bindValue(":$column", $value);
            }

            $stmt->execute();

            return $stmt->fetchAll(\PDO::FETCH_ASSOC);

        } catch (\Throwable $th) {
            $result['error'] = "Query faild: " . $th->getMessage();
            if(getenv("ENV")=="DEV"){
                $result['errorData'] = [
                    'sql'=>$sql ?? null
                ];
            }
            $result['data'] = [];
            echo json_encode($result,true);
            die();
        }
    }
    public static function insert($params) {
        $result = [];
        try {
            $tab = isset($params['tab']) ? $params['tab'] : null;
            $data = isset($params['data']) ? $params['data'] : [];
            if (!$tab || empty($data)) {
                $result['error'] = "Invalid insert parameters";
                $result['data'] = [];
                echo json_encode($result,true);
                die();
            }

            // Build columns and placeholders
            // Filter data to only allowed (permitted) columns
            if (property_exists(get_called_class(), 'inserPermit') && is_array(static::inserPermit)) {
                $permittedColumns = array_flip(static::inserPermit);
                $data = array_intersect_key($data, $permittedColumns);
            }
            $columns = array_keys($data);
            $placeholders = array_map(function($col){ return ":$col"; }, $columns);

            // Prepare SQL
            $sql = "INSERT INTO `$tab` (" . implode(', ', $columns) . ") VALUES (" . implode(', ', $placeholders) . ")";
            $pdo = static::pdo();
            $stmt = $pdo->prepare($sql);

            // Bind values
            foreach ($data as $column => $value) {
                $stmt->bindValue(":$column", $value);
            }

            $stmt->execute();

            return $pdo->lastInsertId();

        } catch (\Throwable $th) {
            $result['error'] = "Query faild: " . $th->getMessage();
            if(getenv("ENV")=="DEV"){
                $result['errorData'] = [
                    'sql'=>$sql ?? null
                ];
            }
            $result['data'] = [];
            echo json_encode($result,true);
            die();
        }
    }
    public static function delete($params) {
        $result = [];
        try {
            $tab = isset($params['tab']) ? $params['tab'] : null;
            $query = isset($params['query']) ? $params['query'] : [];
            if (!$tab || empty($query)) {
                $result['error'] = "Invalid delete parameters";
                $result['data'] = [];
                echo json_encode($result, true);
                die();
            }

            // Build WHERE clause and parameters
            $whereClauses = [];
            $bindings = [];
            foreach ($query as $column => $value) {
                if (is_array($value) && isset($value['in'])) { //"IN" clause
                    if (!is_array($value['in']) || empty($value['in'])) continue;
                    $inPlaceholders = [];
                    foreach ($value['in'] as $idx => $inVal) {
                        $ph = ":{$column}_in_$idx";
                        $inPlaceholders[] = $ph;
                        $bindings["{$column}_in_$idx"] = $inVal;
                    }
                    $whereClauses[] = "`$column` IN (" . implode(', ', $inPlaceholders) . ")";
                } else {
                    $whereClauses[] = "`$column` = :$column";
                    $bindings[$column] = $value;
                }
            }
            if (empty($whereClauses)) {
                $result['error'] = "No conditions for delete";
                $result['data'] = [];
                echo json_encode($result, true);
                die();
            }

            $sql = "DELETE FROM `$tab` WHERE " . implode(' AND ', $whereClauses);
            $pdo = static::pdo();
            $stmt = $pdo->prepare($sql);

            foreach ($bindings as $column => $value) {
                $stmt->bindValue(":$column", $value);
            }

            $stmt->execute();

            $result['deleted_count'] = $stmt->rowCount();
            return $result;

        } catch (\Throwable $th) {
            $result['error'] = "Delete failed: " . $th->getMessage();
            if(getenv("ENV")=="DEV"){
                $result['errorData'] = [
                    'sql'=>$sql ?? null
                ];
            }
            $result['data'] = [];
            echo json_encode($result, true);
            die();
        }
    }
    public static function update($params) {
        $result = [];
        try {
            $tab = isset($params['tab']) ? $params['tab'] : null;
            $data = isset($params['data']) ? $params['data'] : [];
            $conditions = isset($params['query']) ? $params['query'] : [];
            
            if (!$tab || empty($data) || empty($conditions)) {
                $result['error'] = "Invalid update parameters";
                $result['data'] = [];
                echo json_encode($result, true);
                die();
            }

            if (property_exists(get_called_class(), 'insertPermit') && is_array(static::insertPermit)) {
                $permittedColumns = array_flip(static::insertPermit);
                $data = array_intersect_key($data, $permittedColumns);
            }

            $setClause = implode(', ', array_map(function($col) { return "`$col` = :$col"; }, array_keys($data)));

            $whereClauses = [];
            foreach ($conditions as $column => $value) {
                $whereClauses[] = "`$column` = :where_$column";
            }
            $whereClause = implode(' AND ', $whereClauses);

            $sql = "UPDATE `$tab` SET $setClause WHERE $whereClause";
            $pdo = static::pdo();
            $stmt = $pdo->prepare($sql);

            foreach ($data as $column => $value) {
                $stmt->bindValue(":$column", $value);
            }

            foreach ($conditions as $column => $value) {
                $stmt->bindValue(":where_$column", $value);
            }

            $stmt->execute();

            $result['updated_count'] = $stmt->rowCount();
            return $result;

        } catch (\Throwable $th) {
            $result['error'] = "Update failed: " . $th->getMessage();
            if(getenv("ENV")=="DEV"){
                $result['errorData'] = [
                    'sql'=>$sql ?? null
                ];
            }
            $result['data'] = [];
            echo json_encode($result, true);
            die();
        }
    }

}

trait MethodsSql {
    public static function sqlGet($param){
        try {
            return Sql::query([
                "tab"=>static::$tab
                ,"query"=>$param
            ]);
        } catch (\Throwable $th) {
            static::addError($th->getMessage());
            $result['errors'] = static::getErrors();
            $result['data'] = [];
            return $result;
        }
    }
    public static function sqlFind($params){
        try {
            return Sql::query([
                "tab"=>static::$tab
                ,"query"=>$params
            ]);
        } catch (\Throwable $th) {
            static::addError($th->getMessage());
            $result['errors'] = static::getErrors();
            $result['data'] = [];
            return $result;
        }
    }
    public static function sqlInsert($params){
        try {
            return Sql::insert([
                "tab" => static::$tab,
                "data" => $params
            ]);
        } catch (\Throwable $th) {
            static::addError($th->getMessage());
            $result['errors'] = static::getErrors();
            $result['data'] = [];
            return $result;
        }
    }
    public static function sqlDeleteById($ids) {
        try {
            if (is_int($ids)) {
                $ids = [$ids];
            }
            if (empty($ids)) {
                static::addError("ID array cannot be empty.");
                $result['errors'] = static::getErrors();
                $result['data'] = [];
                return $result;
            }
            $deleted_count = Sql::delete([
                "tab" => static::$tab,
                "query" => [
                    "id" => ["in"=>$ids]
                ]
            ]);
            $result['data'] = ["deleted_count" => $deleted_count];
            return $result;
        } catch (\Throwable $th) {
            static::addError($th->getMessage());
            $result['errors'] = static::getErrors();
            $result['data'] = [];
            return $result;
        }
    }
    public static function sqlUpdate($data,$ids){
        try {
            return Sql::update([
                "tab" => static::$tab,
                "data" => $data,
                "query" => [
                    "id" => ["in"=>$ids]
                ]
            ]);
        } catch (\Throwable $th) {
            static::addError($th->getMessage());
            $result['errors'] = static::getErrors();
            $result['data'] = [];
            return $result;
        }
    }
}