<?php
include_once __DIR__."/core/mainController.php";
include_once __DIR__."/core/sql.php";
class User extends mainController {
    use MethodsSql;

    public $tab = "user";
    
    public function new($params){
        try {
        // Error handling and control for params 'user', 'pass', and 'repass', check if correct

        // Validate 'user' is not empty
        if (empty($params['user'])) {
            $this->addError("Username cannot be empty.");
        }
        // Validate 'pass' is not empty
        if (empty($params['pass'])) {
            $this->addError("Password cannot be empty.");
        }
        // Validate 'repass' is not empty
        if (empty($params['repass'])) {
            $this->addError("Repeated password cannot be empty.");
        }
        // Check if pass and repass match
        if ($params['pass'] !== $params['repass']) {
            $this->addError("Passwords do not match.");
        }
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
}
$USER = new User();
