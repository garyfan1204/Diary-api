<?php

namespace Vivian\DiaryApi\controller;

use Vivian\DiaryApi\core\Request;
use Vivian\DiaryApi\core\Response;
use Vivian\DiaryApi\middlewares\Authorize;
use Vivian\DiaryApi\service\DemoService;

class DemoController
{
    private DemoService $DemoService;
    public function __construct()
    {
        $this->DemoService = new DemoService();
    }

    #[Authorize(['admin','user'])]
    public function index(Request $request, Response $response)
    {      
        return $this->DemoService->GetData();
    }

    public function create(Request $request)
    {
        return $request->getBody();
    }

    public function login(Request $request)
    {
        $body = $request->getBody();
        return $this->DemoService->Login($body['account'], $body['password']);
    }

    public function register(Request $request)
    {
        $body = $request->getBody();
        return $this->DemoService->Register($body['account'], $body['password'], $body['name'], $body['email'], $body['sex']);
    }
}
