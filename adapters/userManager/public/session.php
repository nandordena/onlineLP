<?php
include_once __DIR__."/core/mainController.php";
include_once __DIR__."/core/sql.php";

class Session extends mainController {
    use MethodsSql;

    public $tab = 'session';
    public $idColumn = 'id';

    public function validate(){
        try {
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }
            if (!isset($_SESSION['user']) || !isset($_SESSION['sessionkey'])) {
                return false;
            }

            $result = $this->sqlFind([
                "and"=>[
                    "sessionkey"=>$_SESSION['sessionkey']
                  ,"user"=>$_SESSION['user']
                ]
            ]);
            return !empty($result);

        } catch (\Throwable $th) {
            echo $th->getMessage();
            $this->addError($th->getMessage());
            return $this->getErrors();
        }
    }

    public function createSession(){
        try {
            if (session_status() != PHP_SESSION_NONE) {
                
                session_destroy();
            }
            session_unset();
            session_start();
            $_SESSION['user'] = 'test';
            $_SESSION['sessionkey'] = 'test';
        } catch (\Throwable $th) {
            $this->addError($th->getMessage());
            return $this->getErrors();
        }
    }
    
}
$SESSIONS = new Session();
