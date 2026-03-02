<?php
class Sql{
    protected $pdo;

    public function pdo() {
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


    public function query($params){
        $result = [];
        try {
            // Build a PDO query with the given params
            $tab = isset($params['tab']) ? $params['tab'] : null;
            $conditions = isset($params['query']['and']) ? $params['query']['and'] : [];
            if (!$tab || empty($conditions)) {
                $result['error'] = "Invalid query parameters";
                $result['data'] = [];
                echo json_encode($result,true);
                die();
            }

            // Build WHERE clause with named placeholders
            $whereParts = [];
            foreach ($conditions as $column => $value) {
                $whereParts[] = "$column = :$column";
            }
            $whereClause = implode(" AND ", $whereParts);

            // Prepare SQL
            $sql = "SELECT * FROM `$tab` WHERE $whereClause";
            $pdo = $this->pdo();
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
    public function insert($params) {
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
            if (property_exists($this, 'inserPermit') && is_array($this->inserPermit)) {
                $permittedColumns = array_flip($this->inserPermit);
                $data = array_intersect_key($data, $permittedColumns);
            }
            $columns = array_keys($data);
            $placeholders = array_map(function($col){ return ":$col"; }, $columns);

            // Prepare SQL
            $sql = "INSERT INTO `$tab` (" . implode(', ', $columns) . ") VALUES (" . implode(', ', $placeholders) . ")";
            $pdo = $this->pdo();
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
}
$SQL = new Sql();

trait MethodsSql {
    public function sqlGet($param){
        global $SQL;
        try {
            return $SQL->query([
                "tab"=>$this->$tab
                ,"query"=>$param
            ]);
        } catch (\Throwable $th) {
            $this->addError($th->getMessage());
            $result['errors'] = $this->getErrors();
            $result['data'] = [];
            return $result;
        }
    }
    public function sqlFind($params){
        global $SQL;
        try {
            return $SQL->query([
                "tab"=>$this->tab
                ,"query"=>$params
            ]);
        } catch (\Throwable $th) {
            $this->addError($th->getMessage());
            $result['errors'] = $this->getErrors();
            $result['data'] = [];
            return $result;
        }
    }
    public function sqlInsert($params){
        global $SQL;
        try {
            return $SQL->insert([
                "tab" => $this->tab,
                "data" => $params
            ]);
        } catch (\Throwable $th) {
            $this->addError($th->getMessage());
            $result['errors'] = $this->getErrors();
            $result['data'] = [];
            return $result;
        }
    }
}