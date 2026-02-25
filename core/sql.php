<?php
class Sql extends mainController{
    protected $pdo;

    public static function pdo() {
        try {
            $username = getenv($_ADAPTER.'DB_USER') ?: '';
            $password = getenv($_ADAPTER.'DB_PASS') ?: '';
            $host = getenv($_ADAPTER.'DB_HOST') ?: '';
            $options = [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                \PDO::ATTR_EMULATE_PREPARES => false,
            ];
            return new \PDO($host, $username, $password, $options);
        } catch (\PDOException $e) {
            $this->addError("Database connection failed: " . $e->getMessage());
            return $this->getErrors();
        }
    }


    public static function query($params){
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

            $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            return $result;

        } catch (\Throwable $th) {
            $this->addError($th->getMessage());
            return $this->getErrors();
        }
    }
}

trait MethodsSql {
    public function sqlGet($param){
        try {
            Sql:query([
                "tab"=>$this->$tab
                ,"query"=>[
                    "and"=>[
                        "sessionkey"=>$param['sessionkey']
                        ,"user"=>$param['user']
                    ]
                ]
            ]);

        } catch (\Throwable $th) {
            $this->addError($th->getMessage());
            return $this->getErrors();
        }
        

    }
}