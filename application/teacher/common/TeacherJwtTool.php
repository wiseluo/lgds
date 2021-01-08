<?php
namespace app\teacher\common;

use think\Cache;
use Firebase\JWT\JWT;

class TeacherJwtTool
{
	static private $key = "7cce6b7683649b61dc6056292d667636"; //md5('lgds-teacher')

    public static function setAccessToken($user)
    {
        $token = [
            "iat" => time(),  #token发布时间
            "exp" => time() + 2592000, #token过期时间 一月
            "id" => $user['id'],
            'phone' => $user['phone'],
            'nickname' => $user['nickname'],
            'username' => $user['username'],
            'avatar' => $user['avatar'],
            'gender' => $user['gender']
        ];
        $jwt = JWT::encode($token, self::$key);
        $md5 = md5($jwt);
        Cache::store('file')->set($md5, $jwt, 2592000);
        return $md5;
    }

    public static function validateToken($token)
    {
        if (!$token) {
            return false;
        }
        $jwt = Cache::store('file')->get($token);
        if (!$jwt) {
            return false;
        }
        try{
            return JWT::decode($jwt, self::$key, ['HS256']);
        }catch(\Exception $e) {
            //var_dump($e->getMessage());
            return 0;
        }
    }

    public static function deleteToken($token)
    {
        if (!$token) {
            return false;
        }
        Cache::store('file')->rm($token);
        return true;
    }
}