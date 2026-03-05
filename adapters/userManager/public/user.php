<?php
include_once __DIR__."/core/php/MainController.php";
class User extends MainController {

    public static $tab = "user";
    public static $inserRequired = [
        'user'=>'email'
        ,'pass'=>'string'
    ];
    public static $inserPermit = [
        'user'
        ,'pass'
    ];
    
    public static function new($params){
        $result = [];
        try {
            // Error handling
            if (empty($params['email'])) {
                static::addError("Email cannot be empty.");
            }
            if (empty($params['pass'])) {
                static::addError("Password cannot be empty.");
            }
            if (empty($params['repass'])) {
                static::addError("Repeated password cannot be empty.");
            }
            if ($params['pass'] !== $params['repass']) {
                static::addError("Passwords do not match.");
            }
            static::isValidPassword($params['pass']);

            // If there are errors, return them
            if (!empty(static::getErrors())) {
                $result['errors'] = static::getErrors();
                $result['data'] = [];
                return $result;
            }
            // Check if user already exists in the database
            $existingUser = static::sqlFind([
                "and" => [
                    "user" => $params['email']
                ]
            ]);
            if (!empty($existingUser)) {
                static::addError("Email already exists.");
                $result['errors'] = static::getErrors();
                $result['data'] = [];
                return $result;
            }

            // Hash the password before storing
            $hashedPassword = password_hash($params['pass'], PASSWORD_DEFAULT);
            // Prepare data to insert
            $userData = [
                "user" => $params['email'],
                "pass" => $hashedPassword
            ];
            // Insert the new user
            $insertId = static::sqlInsert($userData);
            // Return insert id or success message
            $result['data'] = $insertId;
            // If insertId is valid, log the user in with the same data
            if ($insertId) {
                Session::createSession([
                    'email' => $params['email'],
                    'pass' => $params['pass']
                ]);
                $result['data'] = [
                    "register" => true,
                    "user" => $params['email'],
                    "sessionKey" => isset($_SESSION['sessionKey']) ? $_SESSION['sessionKey'] : null,
                    "sessionId" => isset($_SESSION['sessionId']) ? $_SESSION['sessionId'] : null,
                    "userId" => $insertId
                ];
            }
            return $result;

        } catch (\Throwable $th) {
            static::addError($th->getMessage());
            $result['errors'] = static::getErrors();
            $result['data'] = [];
            return $result;
        }
    }
    public static function login($params) {
        $result = [];
        try {
            // Check for required parameters
            if (empty($params['email']) || empty($params['pass'])) {
                static::addError("Email and password are required.");
                $result['errors'] = static::getErrors();
                $result['data'] = [];
                return $result;
            }

            // Find user by email
            $user = static::sqlFind([
                "and" => [
                    "user" => $params['email']
                ]
            ]);

            if (empty($user) || !isset($user[0]['pass'])) {
                static::addError("Invalid email or password.");
                $result['errors'] = static::getErrors();
                $result['data'] = [];
                return $result;
            }

            // Verify the password
            if (!password_verify($params['pass'], $user[0]['pass'])) {
                static::addError("Invalid email or password.");
                $result['errors'] = static::getErrors();
                $result['data'] = [];
                return $result;
            }

            Session::createSession($params);

            $result['data'] = [
                "login" => true,
                "user" => $params['email'],
                "sessionKey" => $_SESSION['sessionKey'],
                "sessionId" => $_SESSION['sessionId'],
            ];
            return $result;
        } catch (\Throwable $th) {
            static::addError($th->getMessage());
            $result['errors'] = static::getErrors();
            $result['data'] = [];
            return $result;
        }
    }
    public static function validateLogin($params) {
        $result = [];
        try {
            // Check for required parameters
            if (empty($params['email']) || empty($params['pass'])) {
                static::addError("Email and password are required.");
                $result['errors'] = static::getErrors();
                $result['data'] = [];
                return $result;
            }

            // Find user by email
            $user = static::sqlFind([
                "and" => [
                    "user" => $params['email']
                ]
            ]);

            if (empty($user) || !isset($user[0]['pass'])) {
                // User not found or password field missing
                $result['errors'] = static::getErrors();
                $result['data'] = [];
                return $result;
            }

            // Verify the password
            if (password_verify($params['pass'], $user[0]['pass'])) {
                $result['data'] = true;
                return $result;
            }

            $result['errors'] = static::getErrors();
            $result['data'] = [];
            return $result;
        } catch (\Throwable $th) {
            static::addError($th->getMessage());
            $result['errors'] = static::getErrors();
            $result['data'] = [];
            return $result;
        }
    }
    public static function isValidPassword($password) {
        $result = [];
        // Example requirements, could be loaded from global or config
        $requirements = [
            'min_length' => 6,
            'require_uppercase' => true,
            'require_lowercase' => true,
            'require_digit' => true,
            'require_special' => true,
        ];
        // Try to use global $_VALID_PASS if exists, otherwise fallback to above
        if (isset($GLOBALS['_VALID_PASS']) && is_array($GLOBALS['_VALID_PASS'])) {
            $requirements = $GLOBALS['_VALID_PASS'];
        }

        $errors = [];

        if (strlen($password) < $requirements['min_length']) {
            $errors[] = "Password must be at least {$requirements['min_length']} characters.";
        }
        if (!empty($requirements['require_uppercase']) && !preg_match('/[A-Z]/', $password)) {
            $errors[] = "Password must contain at least one uppercase letter.";
        }
        if (!empty($requirements['require_lowercase']) && !preg_match('/[a-z]/', $password)) {
            $errors[] = "Password must contain at least one lowercase letter.";
        }
        if (!empty($requirements['require_digit']) && !preg_match('/[0-9]/', $password)) {
            $errors[] = "Password must contain at least one digit.";
        }
        if (!empty($requirements['require_special']) && !preg_match('/[^a-zA-Z0-9]/', $password)) {
            $errors[] = "Password must contain at least one special character.";
        }

        if (!empty($errors)) {
            foreach ($errors as $err) {
                static::addError($err);
            }
            $result['errors'] = $errors;
            $result['data'] = [];
            return $result;
        }
        $result['data'] = true;
        return $result;
    }
    public static function logout($params) {
        $result = [];
        try {
            Session::destroySession();
            
            $result['data'] = [
                "logout" => true,
                "message" => "Successfully logged out."
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

