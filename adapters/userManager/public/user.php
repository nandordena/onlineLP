<?php
include_once __DIR__."/core/mainController.php";
class User extends mainController {

    public $tab = "user";
    public $inserRequired = [
        'user'=>'email'
        ,'pass'=>'string'
    ];
    public $inserPermit = [
        'user'
        ,'pass'
    ]
    
    public function new($params){
        $result = [];
        try {
            // Error handling
            if (empty($params['email'])) {
                $this->addError("Email cannot be empty.");
            }
            if (empty($params['pass'])) {
                $this->addError("Password cannot be empty.");
            }
            if (empty($params['repass'])) {
                $this->addError("Repeated password cannot be empty.");
            }
            if ($params['pass'] !== $params['repass']) {
                $this->addError("Passwords do not match.");
            }
            $this->isValidPassword($params['pass']);

            // If there are errors, return them
            if (!empty($this->getErrors())) {
                $result['errors'] = $this->getErrors();
                $result['data'] = [];
                return $result;
            }
            // Check if user already exists in the database
            $existingUser = $this->sqlFind([
                "and" => [
                    "user" => $params['user']
                ]
            ]);
            if (!empty($existingUser)) {
                $this->addError("Email already exists.");
                $result['errors'] = $this->getErrors();
                $result['data'] = [];
                return $result;
            }

            // Hash the password before storing
            $hashedPassword = password_hash($params['pass'], PASSWORD_DEFAULT);
            // Prepare data to insert
            $userData = [
                "user" => $params['user'],
                "pass" => $hashedPassword
            ];
            // Insert the new user
            $insertId = $this->sqlInsert($userData);
            // Return insert id or success message
            $result['data'] = $insertId;
            return $result;

        } catch (\Throwable $th) {
            $this->addError($th->getMessage());
            $result['errors'] = $this->getErrors();
            $result['data'] = [];
            return $result;
        }
    }
    public function validateLogin($params) {
        $result = [];
        try {
            // Check for required parameters
            if (empty($params['email']) || empty($params['pass'])) {
                $this->addError("Email and password are required.");
                $result['errors'] = $this->getErrors();
                $result['data'] = [];
                return $result;
            }

            // Find user by email
            $user = $this->sqlFind([
                "and" => [
                    "user" => $params['email']
                ]
            ]);

            if (empty($user) || !isset($user[0]['pass'])) {
                // User not found or password field missing
                $result['errors'] = $this->getErrors();
                $result['data'] = [];
                return $result;
            }

            // Verify the password
            if (password_verify($params['pass'], $user[0]['pass'])) {
                $result['data'] = true;
                return $result;
            }

            $result['errors'] = $this->getErrors();
            $result['data'] = [];
            return $result;
        } catch (\Throwable $th) {
            $this->addError($th->getMessage());
            $result['errors'] = $this->getErrors();
            $result['data'] = [];
            return $result;
        }
    }
    // Function to check if a password meets validity requirements
    public function isValidPassword($password) {
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
                $this->addError($err);
            }
            $result['errors'] = $errors;
            $result['data'] = [];
            return $result;
        }
        $result['data'] = true;
        return $result;
    }
}
$USER = new User();
