<?php
class JWT {
    private static $secret_key = 'P@ssw0rd'; // секретный ключ 
    private static $algorithm = 'HS256';
    
    public static function encode($payload) {
        $header = [
            'typ' => 'JWT',
            'alg' => self::$algorithm
        ]; //Создаётся заголовок токена: тип JWT, алгоритм HS256
        
        $header_encoded = self::base64UrlEncode(json_encode($header));
        $payload_encoded = self::base64UrlEncode(json_encode($payload));
        $signature = self::sign($header_encoded . '.' . $payload_encoded);
        
        return $header_encoded . '.' . $payload_encoded . '.' . $signature;
        //получаем строку токен
    }
    
    public static function decode($token) {
        $parts = explode('.', $token);
        
        if (count($parts) !== 3) {
            throw new Exception('Invalid token format');
        }
        
        list($header_encoded, $payload_encoded, $signature) = $parts;
        
        // Проверяем подпись
        if (self::sign($header_encoded . '.' . $payload_encoded) !== $signature) {
            throw new Exception('Invalid signature');
        }
        
        $payload = json_decode(self::base64UrlDecode($payload_encoded), true);
        
        // Проверяем expiration time
        if (isset($payload['exp']) && $payload['exp'] < time()) {
            throw new Exception('Token expired');
        }
        
        return $payload;
    }
    
    private static function sign($data) {
        return self::base64UrlEncode(hash_hmac('sha256', $data, self::$secret_key, true));
    }
    
    private static function base64UrlEncode($data) {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }
    
    private static function base64UrlDecode($data) {
        return base64_decode(str_pad(strtr($data, '-_', '+/'), strlen($data) % 4, '=', STR_PAD_RIGHT));
    }
}
?>