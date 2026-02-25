<?php
class Sql extends mainController{
    protected $pdo;

    public function pdo() {
        global $_ADAPTER;
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
            $this->addError("Database connection failed: " . $e->getMessage());
            return $this->getErrors();
        }
    }


    public function query($params){
        try {
            // Build a PDO query with the given params
            $tab = isset($params['tab']) ? $params['tab'] : null;
            $conditions = isset($params['query']['and']) ? $params['query']['and'] : [];
            if (!$tab || empty($conditions)) {
                $this->addError("Invalid query parameters");
                return $this->getErrors();
            }

            // Build WHERE clause with named placeholders
            $whereParts = [];
            foreach ($conditions as $column => $value) {
                $whereParts[] = "$column = :$column";
            }
            $whereClause = implode(" AND ", $whereParts);

            // Prepare SQL
            $sql = "SELECT * FROM `$tab` WHERE $whereClause";
            $pdo = $this::pdo();
            $stmt = $pdo->prepare($sql);

            // Bind values
            foreach ($conditions as $column => $value) {
                $stmt->bindValue(":$column", $value);
            }

            $stmt->execute();

            return $stmt->fetchAll(\PDO::FETCH_ASSOC);

        } catch (\Throwable $th) {
            $this->addError($th->getMessage());
            return $this->getErrors();
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
            return $this->getErrors();
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
            return $this->getErrors();
        }
    }
}