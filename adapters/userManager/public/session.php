<?php
include_once __DIR__."/core/mainController.php";

class Session extends mainController {
    protected $table = 'session';
    protected $idColumn = 'id';

    public static function validateSession(){
        try {
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }
            if (!isset($_SESSION['user']) || !isset($_SESSION['sessionkey'])) {
                throw new Exception("Session is not valid.");
            }

            $result = $this->sqlFind([
                "and":[
                    "sessionkey":$_SESSION['sessionkey']
                    ,"user":$_SESSION['user']
                ]
            ]);



            return true;
        } catch (\Throwable $th) {
            $this->addError($th->getMessage());
            return $this->getErrors();
        }
    }

    public static function createSession(){
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
