<?php

namespace Vivian\DiaryApi\middlewares;

use Attribute;
use Vivian\DiaryApi\core\Middleware;
use Vivian\DiaryApi\core\Request;
use Vivian\DiaryApi\core\Response;

use \Firebase\JWT\JWT;
use Firebase\JWT\Key;

#[Attribute]
class Authorize extends Middleware
{
    private ?array $roles = null;  //null 的原因是要讓引用的時候可以不用定義角色

    public function __construct($roles = [])
    {
        $this->roles = $roles;
    }

    public function handle(Request $request, Response $response)
    {
        //get token
        $token = $request->getHeaders()['Authorization'] ?? ''; //需要 getheader拿token 
        //vertify token
        // $userData=json_decode($token,);  //用json_decode驗證，假設沒有資料就會寫未登入
        $secretKey = 'abcdefghijklmnopqrstuvwxyz'; //密鑰
        $algorithm = 'HS256'; //加密的演算法
        $userData = JWT::decode($token, new Key($secretKey, $algorithm));
        if (!$userData) {
            throw new \Exception('未登入', 401);
        }
        if (count($this->roles) > 0 && !in_array($userData->role, $this->roles)) {  //檢查權限
            throw new \Exception('權限不足', 403);
        }
        $request->setUserData($userData);
    }
}
