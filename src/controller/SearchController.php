<?php

namespace Vivian\DiaryApi\controller;

use BadFunctionCallException;
use Vivian\DiaryApi\core\Request;
use Vivian\DiaryApi\core\Response;
use Vivian\DiaryApi\middlewares\Authorize;
use Vivian\DiaryApi\service\SearchService;

class SearchController
{
    private $SearchService;
    public function __construct()
    {
        $this->SearchService = new SearchService();
    }

    
    public function Search_music_type(Request $request, Response $response)
    {
        $body = $request->getQuery();
        $data = $this->SearchService->Search_music_type($body['mood']);
        return $data;
    }

    public function Search_sentence_type(Request $request, Response $response)
    {
        $body = $request->getQuery();
        $data = $this->SearchService->Search_sentence_type($body['Type_Id']);
        return $data;
    }

    public function Search_music(Request $request, Response $response)
    {
        $body = $request->getQuery();
        $data = $this->SearchService->Search_music($body['Content']);
        return $data;
    }

    public function Search_sentence(Request $request, Response $response)
    {
        $body = $request->getQuery();
        $data = $this->SearchService->Search_sentence($body['Content']);
        return $data;
    }

    public function Search_emoji(Request $request, Response $response)
    {
        $body = $request->getQuery();
        $data = $this->SearchService->Search_emoji($body['Content']);
        return $data;
    }

    public function Search_weather(Request $request, Response $response)
    {
        $body = $request->getQuery();
        $data = $this->SearchService->Search_weather($body['Content']);
        return $data;
    }

    public function Search_user(Request $request, Response $response)
    {
        $body = $request->getQuery();
        $data = $this->SearchService->Search_user($body['Content']);
        return $data;
    }

    public function Search_user_sex(Request $request, Response $response)
    {
        $body = $request->getQuery();
        $data = $this->SearchService->Search_user_sex($body['Gender']);
        return $data;
    }
}
