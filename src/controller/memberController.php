<?php

namespace Vivian\DiaryApi\controller;

use Vivian\DiaryApi\core\Request;
use Vivian\DiaryApi\core\Response;
use Vivian\DiaryApi\middlewares\Authorize;
use Vivian\DiaryApi\service\MemberService;

class MemberController
{
    private $memberService;
    public function __construct()
    {
        $this->memberService = new MemberService();
    }
    public function login(Request $request, Response $response)
    {
        $body = $request->getBody();
        $data = $this->memberService->login($body['account'] ?? null, $body['password'] ?? null);
        $response->setMessage('登入成功');
        return $data;
    }

    public function register(Request $request, Response $response)
    {
        $body = $request->getBody();
        $data = $this->memberService->register($body['name']?? null,$body['account']?? null, $body['email']?? null, $body['password']?? null, $body['checkpwd']?? null);
        $response->setMessage('註冊成功，請收取驗證信');
        return $data;
    }

    public function AuthCode(Request $request, Response $response){
        $token = $request->getHeaders()['Authorization'] ?? ''; //需要 getheader拿token
        $body = $request->getBody();
        $data = $this->memberService->AuthCode($token,$body['AuthCode']?? null);
        // $response->setMessage('驗證成功');
        return $data;
    }

    public function logout(Request $request, Response $response)
    {
        $body = $request->getBody();
        $data = $this->memberService->logout($body['account'] ?? null);
        $response->setMessage('已登出');
        return json_encode($data);
    }

    #[Authorize(['admin', 'user'])]
    public function getuser_by_token(Request $request, Response $response)
    {
        $userData = $request->getUserData();
        $body = $request->getQuery();
        $data = $this->memberService->getuser_by_token($userData);
        return $data;
    }

    public function forget_password(Request $request, Response $response)
    {
        $body = $request->getBody();
        $data = $this->memberService->forget_password($body['account'] ?? null,$body['email'] ?? null);
        $response->setMessage('已寄出，請收重設密碼信');
        return $data;
    }



    
}
