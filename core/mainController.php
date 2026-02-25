<?php
// include_once __DIR__."/sql.php";

class mainController {
    protected $errors = [];
    protected $success = [];

    // Error handler
    public function addError($message) {
        $this->errors[] = $error;
    }
    public function getErrors() {
        return $this->errors;
    }
    public function hasErrors() {
        return !empty($this->errors);
    }
}