<?php
include_once __DIR__."/sql.php";

class mainController {
    global $_ADAPTER;
    use MethodsSql;
    
    protected $errors = [];

    // Error handler
    public function addError($message) {
        $this->errors[] = $message;
    }
    public function getErrors() {
        return $this->errors;
    }
    public function hasErrors() {
        return !empty($this->errors);
    }

    //MODEL
    public function insert($params) {
        try {
            // Handle required fields as per child (e.g., 'user' => 'email', 'pass' => 'pass')
            if (property_exists($this, 'inserRequired')) {
                foreach ($this->inserRequired as $key => $alias) {
                    // If defined as KEY=>VAL, use the field in $params by key or by value
                    $field = is_int($key) ? $alias : $key;
                    if (empty($params[$field])) {
                        $this->addError("Field '{$field}' is required.");
                    }
                // Switch on field for type-specific validation logic
                switch ($field) {
                    case 'email':
                        if (!filter_var($params[$field], FILTER_VALIDATE_EMAIL)) {
                            $this->addError("Field '{$field}' must be a valid email.");
                        }
                        break;
                    case 'string':
                        if (!is_string($params[$field]) || strlen(trim($params[$field])) === 0) {
                            $this->addError("Field '{$field}' must be a non-empty string.");
                        }
                        break;
                }
            }
            if (!empty($this->getErrors())) {
                return $this->getErrors();
            }
            // Use sqlInsert from MethodsSql trait
            $insertId = $this->sqlInsert($params);
            $this->success[] = $insertId;
            return $insertId;
        } catch (\Throwable $th) {
            $this->addError($th->getMessage());
            return $this->getErrors();
        }
    }

    //CURL
    public function curlCall($url, $data = [], $method = 'POST', $headers = []){
        $ch = curl_init();

        // Detect if URL is absolute or a local script (relative path)
        if (filter_var($url, FILTER_VALIDATE_URL) === false) {
            // Build full URL
            $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https" : "http";
            $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
            $basePath = dirname($_SERVER['SCRIPT_NAME']);
            $basePath = $basePath === '/' ? '' : $basePath;
            $fullUrl = $protocol . '://' . $host . $basePath . '/' . ltrim($url, '/');
        } else {
            $fullUrl = $url;
        }

        if (strtoupper($method) === 'GET') {
            if (!empty($data)) {
                $fullUrl .= (strpos($fullUrl, '?') === false ? '?' : '&') . http_build_query($data);
            }
            curl_setopt($ch, CURLOPT_HTTPGET, true);
        } else {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        }

        // Set headers
        if (!empty($headers)) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }

        curl_setopt($ch, CURLOPT_URL, $fullUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Optional: pass the session cookie
        if (session_status() === PHP_SESSION_ACTIVE && isset($_COOKIE[session_name()])) {
            curl_setopt($ch, CURLOPT_COOKIE, session_name() . '=' . $_COOKIE[session_name()]);
        }

        $response = curl_exec($ch);
        if ($response === false) {
            $this->addError('Curl error: ' . curl_error($ch));
            curl_close($ch);
            return $this->getErrors();
        }
        curl_close($ch);

        // Try to decode JSON
        $decoded = json_decode($response, true);
        return $decoded !== null ? $decoded : $response;
    }
    public function callAdapter($adapterName, $endpoint, $data = [], $method = 'POST', $headers = []) {
        globa $_ADAPTER;
        if($_ADAPTER == $adapterName){
            //TODO : Use this->
            return true;
        }
        // Try to pull base URL from env, supporting both getenv and $_ENV
        $baseUrl = getenv($adapterName);
        if (!$baseUrl && isset($_ENV[$adapterName])) {
            $baseUrl = $_ENV[$adapterName];
        }
        if (!$baseUrl && isset($GLOBALS[$adapterName])) {
            $baseUrl = $GLOBALS[$adapterName];
        }
        if (!$baseUrl) {
            $this->addError("Adapter base URL for '$adapterName' not found in environment.");
            return $this->getErrors();
        }

        // Normalize/concatenate URL
        $endpoint = ltrim($endpoint, '/');
        $url = rtrim($baseUrl, '/') . '/' . $endpoint;

        //TODO : Normalice result to json to asociative,
        return $this->curCall($url, $data, $method, $headers);
    }
}