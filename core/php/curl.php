<?
class BerericCurl {
    private static function getBaseUrl($adapter) {
        if(getenv('ENV')=="DEV"){
            switch ($adapter) {
                case 'userManager':
                    return getenv('USER_MANAGER_DOCKER');;
                case 'controlRoom':
                    return getenv('CONTROL_ROOM_DOCKER');
                case 'viewComponents':
                    return getenv('VIEW_COMPONENTS_DOCKER');
                default:
                    return false;
            }
        }else{
            switch ($adapter) {
                case 'userManager':
                    return getenv('USER_MANAGER');
                case 'controlRoom':
                    return getenv('CONTROL_ROOM');
                case 'viewComponents':
                    return getenv('VIEW_COMPONENTS');
                default:
                    return false;
            }
        }
    }
    public static function post($adapter, $endpoint, $params = []) {
        $baseUrl = static::getBaseUrl($adapter);
        if (!$baseUrl) {
            return [
                'errors' => ["Unknown adapter or base URL not set for adapter: $adapter"],
                'data' => []
            ];
        }

        // Compose remote endpoint url (add slash if missing, avoid double slashes)
        $url = rtrim($baseUrl, '/').'/'.ltrim($endpoint, '/');

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Content-Type: application/x-www-form-urlencoded",
            "Accept: */*"
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
        // Optionally forward session cookies for authentication
        if (isset($_COOKIE) && count($_COOKIE)) {
            $cookies = [];
            foreach ($_COOKIE as $k => $v) $cookies[] = $k . '=' . urlencode($v);
            curl_setopt($ch, CURLOPT_COOKIE, implode('; ', $cookies));
        }
        $response = curl_exec($ch);
        $err = curl_error($ch);
        curl_close($ch);
        if ($err) {
            return [
                'errors' => ["Curl error: " . $err],
                'data' => []
            ];
        }
        $decoded = json_decode($response, true);
        if (is_array($decoded)) {
            return $decoded;
        }
        $result =[
            'errors' => ["Response not JSON: " . substr($response, 0, 200)],
            'data' => []
        ];
        return $result;
    
    }
    public static function stdPost($adapter, $endpoint, $params = []) {
        $params["sessionId"] = $_COOKIE['sessionId'];
        $params["sessionKey"] = $_COOKIE['sessionKey'];
        $params["user"] = $_COOKIE['user'];
        $result = static::post($adapter, $endpoint, $params);
        return $result;
    }
}