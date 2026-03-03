<?
class BerericCurl {
    private static function getBaseUrl($adapter) {
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
    public static function post($adapter, $endpoint, $params = []) {
        $baseUrl = self::getBaseUrl($adapter);
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
        return [
            'errors' => ["Response not JSON: " . substr($response, 0, 200)],
            'data' => []
        ];
    }
}