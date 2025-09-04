<?php

class JWT
{
    private static $secret_key = 'DMUK2025!'; 
    private static $algo = 'HS256';

    public static function generate($payload, $exp_minutes = 60)
    {
        $header = ['alg' => self::$algo, 'typ' => 'JWT'];
        $payload['exp'] = time() + ($exp_minutes * 60);

        $base64UrlHeader = self::base64UrlEncode(json_encode($header));
        $base64UrlPayload = self::base64UrlEncode(json_encode($payload));

        $signature = hash_hmac('sha256', "$base64UrlHeader.$base64UrlPayload", self::$secret_key, true);
        $base64UrlSignature = self::base64UrlEncode($signature);

        return "$base64UrlHeader.$base64UrlPayload.$base64UrlSignature";
    }

    public static function validate($jwt)
    {
        $parts = explode('.', $jwt);

        if (count($parts) !== 3) {
            return false;
        }

        list($base64UrlHeader, $base64UrlPayload, $base64UrlSignature) = $parts;

        $header = json_decode(self::base64UrlDecode($base64UrlHeader), true);
        $payload = json_decode(self::base64UrlDecode($base64UrlPayload), true);

        if (!isset($payload['exp']) || $payload['exp'] < time()) {
            return false;
        }

        $signature_check = hash_hmac('sha256', "$base64UrlHeader.$base64UrlPayload", self::$secret_key, true);
        $base64UrlSignatureCheck = self::base64UrlEncode($signature_check);

        if (!hash_equals($base64UrlSignatureCheck, $base64UrlSignature)) {
            return false;
        }

        return $payload;
    }

    private static function base64UrlEncode($data)
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    private static function base64UrlDecode($data)
    {
        $remainder = strlen($data) % 4;
        if ($remainder) {
            $padlen = 4 - $remainder;
            $data .= str_repeat('=', $padlen);
        }
        return base64_decode(strtr($data, '-_', '+/'));
    }
}
