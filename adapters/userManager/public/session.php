<?php
include_once __DIR__."/core/php/MainController.php";
include_once __DIR__."/core/php/sql.php";

class Session extends MainController {
    use MethodsSql;
    public static $tab = 'session';
    public static $inserPermit = [
        'user'
        ,'session_key'
    ];

    public static function validate($params){
        try {
            
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }

            $user = isset($params['user']) && $params['user'] !== 'undefined' ? $params['user'] : (isset($_SESSION['user']) ? $_SESSION['user'] : null);
            $sessionKey = isset($params['sessionKey']) && $params['sessionKey'] !== 'undefined' ? $params['sessionKey'] : (isset($_SESSION['sessionKey']) ? $_SESSION['sessionKey'] : null);
            $sessionId = isset($params['sessionId']) && $params['sessionId'] !== 'undefined' ? $params['sessionId'] : (isset($_SESSION['sessionId']) ? $_SESSION['sessionId'] : null);

            if (!$user || !$sessionKey || !$sessionId) {
                return false;
            }

            $result = static::sqlFind([
                "and"=>[
                    "session_key"=>$sessionKey
                    ,"id"=>$sessionId
                    ,"user"=>$user
                ]
            ]);
            return !empty($result);

        } catch (\Throwable $th) {
            static::addError($th->getMessage());
            return static::getErrors();
        }
    }

    public static function createSession($params){
        try {
            if (session_status() != PHP_SESSION_NONE) {
                session_destroy();
            }
            session_unset();
            static::removeSessions($params);

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
            $_SESSION['sessionId'] = static::sqlInsert($insertData);

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
            static::addError($th->getMessage());
            return static::getErrors();
        }
    }
    
    public static function removeSessions($params) {
        $result = [];
        $user = $params['email'];
        try {
            if (empty($user)) {
                static::addError("User cannot be empty.");
                $result['errors'] = static::getErrors();
                $result['data'] = [];
                return $result;
            }
            
            $existing = static::sqlFind([
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
                $stmt = static::sqlDeleteById($sessionIds);
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
            static::addError($th->getMessage());
            $result['errors'] = static::getErrors();
            $result['data'] = [];
            return $result;
        }
    }

    public static function destroySession() {
        $result = [];
        try {
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }
            $user = isset($_SESSION['user']) ? $_SESSION['user'] : null;
            if ($user) {
                static::removeSessions(['email' => $user]);
            }
            session_destroy();
            session_unset();
            $result['data'] = [
                "logout" => true
            ];
            return $result;
        } catch (\Throwable $th) {
            static::addError($th->getMessage());
            $result['errors'] = static::getErrors();
            $result['data'] = [];
            return $result;
        }
    }
}
