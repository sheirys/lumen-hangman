<?php
namespace App\Libs;

define('JWT_SECRET', 'secret');

class Jwt {

    // returns JWT token
    public static function createToken($email) {

        $header = array(
            "typ" => "JWT",
            "alg" => "HS256",
        );

        $payload = array(
            //"jit" => uniqid(),
            //"typ" => "auth_token",
            "email" => $email,
        );

        $j_header = json_encode($header, JSON_UNESCAPED_UNICODE);
        $j_payload = json_encode($payload, JSON_UNESCAPED_UNICODE);

        $h_header = self::base64UrlEncode($j_header);
        $h_payload = self::base64UrlEncode($j_payload);

        $sign = hash_hmac('SHA256', "$h_header.$h_payload", JWT_SECRET, true);
        $h_sign = self::base64UrlEncode($sign);

        return "$h_header.$h_payload.$h_sign";

    }

    // verifies is JWT is valid. Returns FALSE if invalid, else
    // returns JWT payload as array
    public static function verify($token) {

        $chunks = explode(".", $token);

        if(count($chunks) !== 3) {
            return false;
        }

        list($h_header, $h_payload, $h_sign) = $chunks;

        $sign = self::base64UrlDecode($h_sign);

        if(hash_hmac('SHA256', "$h_header.$h_payload", JWT_SECRET, true) === $sign) {
            return json_decode(self::base64UrlDecode($h_payload));
        } else {
            return false;
        }

        return false;

    }

    // magic from RFC 4648 3.2
    private static function base64UrlEncode($string) {
        return strtr(rtrim(base64_encode($string), '='), '+/', '-_');
    }

    // magic from RFC 4648 3.2
    private static function base64UrlDecode($string) {
        return base64_decode(str_pad(strtr($string, '-_', '+/'), strlen($string) % 4, '=', STR_PAD_RIGHT));
    }

}
