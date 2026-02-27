<?php
include_once __DIR__."/core/mainController.php";
class User extends mainController {

    public $tab = "user";
    public $inserRequired = [
        'user'=>'email'
        ,'pass'=>'string'
    ];
    
    public function new($params){
        try {
        // Error handling and control for params 'user', 'pass', and 'repass', check if correct

        if (empty($params['user'])) {
            $this->addError("Username cannot be empty.");
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
            return $this->getErrors();
        }
        // Check if user already exists in the database
        $existingUser = $this->sqlFind([
            "and" => [
                "user" => $params['user']
            ]
        ]);
        if (!empty($existingUser)) {
            $this->addError("Username already exists.");
            return $this->getErrors();
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
        return $insertId;

        } catch (\Throwable $th) {
            $this->addError($th->getMessage());
            return $this->getErrors();
        }
    }
    public function validateLogin($params) {
        try {
            // Check for required parameters
            if (empty($params['user']) || empty($params['pass'])) {
                $this->addError("Username and password are required.");
                return false;
            }

            // Find user by username
            $user = $this->sqlFind([
                "and" => [
                    "user" => $params['user']
                ]
            ]);

            if (empty($user) || !isset($user[0]['pass'])) {
                // User not found or password field missing
                return false;
            }

            // Verify the password
            if (password_verify($params['pass'], $user[0]['pass'])) {
                return true;
            }

            return false;
        } catch (\Throwable $th) {
            $this->addError($th->getMessage());
            return false;
        }
    }
    // Function to check if a password meets validity requirements
    public function isValidPassword($password) {
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
            return false;
        }
        return true;
    }
}
$USER = new User();
