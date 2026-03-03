<?php
include_once __DIR__."/core/php/mainController.php";
include_once __DIR__."/core/php/sql.php";

class Session extends mainController {
    use MethodsSql;
    public $tab = 'session';
    public $inserPermit = [
        'user'
        ,'session_key'
    ];

    public function validate($params){
        try {
            
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }

            $user = isset($params['user']) ? $params['user'] : (isset($_SESSION['user']) ? $_SESSION['user'] : null);
            $sessionKey = isset($params['sessionKey']) ? $params['sessionKey'] : (isset($_SESSION['sessionKey']) ? $_SESSION['sessionKey'] : null);
            $sessionId = isset($params['sessionId']) ? $params['sessionId'] : (isset($_SESSION['sessionId']) ? $_SESSION['sessionId'] : null);

            if (!$user || !$sessionKey || !$sessionId) {
                return false;
            }

            $result = $this->sqlFind([
                "and"=>[
                    "session_key"=>$sessionKey
                    ,"id"=>$sessionId
                    ,"user"=>$user
                ]
            ]);
            return !empty($result);

        } catch (\Throwable $th) {
            $this->addError($th->getMessage());
            return $this->getErrors();
        }
    }

    public function createSession($params){
        try {
            if (session_status() != PHP_SESSION_NONE) {
                session_destroy();
            }
            session_unset();
            $this->removeSessions($params);

            session_start();

            $user = isset($params['email']) ? $params['email'] : 'guest';
            $date = date('dmY');
            $randomBytes = bin2hex(random_bytes(16));
            $sessionKey = hash('sha256', $user . $date . $randomBytes);

            $_SESSION['user'] = $user;
            $_SESSION['sessionKey'] = $sessionKey;

            $insertData = [
                "user" => $user,
                "session_key" => $sessionKey
            ];
            $_SESSION['sessionId'] = $this->sqlInsert($insertData);

            return [
                "user" => $_SESSION['user']
                ,"sessionKey" => $_SESSION['sessionKey']
                ,"sessionId" => $_SESSION['sessionId']
            ];
        } catch (\Throwable $th) {
            if (session_status() != PHP_SESSION_NONE) {
                session_destroy();
            }
            session_unset();
            $this->addError($th->getMessage());
            return $this->getErrors();
        }
    }
    
    public function removeSessions($params) {
        $result = [];
        $user = $params['email'];
        try {
            if (empty($user)) {
                $this->addError("User cannot be empty.");
                $result['errors'] = $this->getErrors();
                $result['data'] = [];
                return $result;
            }
            
            $existing = $this->sqlFind([
                "and" => [
                    "user" => $user
                ]
            ]);
            $sessionIds = [];
            if (!empty($existing)) {
                foreach ($existing as $session) {
                    if (isset($session['id'])) {
                        $sessionIds[] = $session['id'];
                    }
                }
            }
            if (!empty($sessionIds)) {
                $stmt = $this->sqlDeleteById($sessionIds);
                $deletedCount = $stmt->rowCount();
            } else {
                $deletedCount = 0;
            }

            $result['data'] = [
                'deletedCount' => $deletedCount,
                'deletedSessions' => $existing
            ];
            return $result;
        } catch (\Throwable $th) {
            $this->addError($th->getMessage());
            $result['errors'] = $this->getErrors();
            $result['data'] = [];
            return $result;
        }
    }
}
$SESSIONS = new Session();
