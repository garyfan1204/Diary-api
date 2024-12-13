<?php

namespace Vivian\DiaryApi\controller;

use BadFunctionCallException;
use Vivian\DiaryApi\core\Request;
use Vivian\DiaryApi\core\Response;
use Vivian\DiaryApi\middlewares\Authorize;
use Vivian\DiaryApi\service\DiaryService;

class DiaryController
{
    private $diaryService;
    public function __construct()
    {
        $this->diaryService = new DiaryService();
    }
    #[Authorize(['admin', 'user'])]
    public function write_diary(Request $request, Response $response)
    {
        $userData = $request->getUserData();
        $body = $request->getBody();
        $data = $this->diaryService->write_diary($userData,$body['emoji']?? null,$body['content']?? null,$body['weather']?? null,$body['title']?? null,$body['date']?? null);
        $response->setMessage('新增成功');
        return $data;
    }

    #[Authorize(['admin', 'user'])]
    public function show_diary_all(Request $request, Response $response){
        $userData = $request->getUserData();
        $body = $request->getQuery();
        $data = $this->diaryService->show_diary_all($userData);
        return $data;
    }

    #[Authorize(['admin', 'user'])]
    public function show_diary(Request $request, Response $response){
        $userData = $request->getUserData();
        $body = $request->getQuery();
        $data = $this->diaryService->show_diary($userData,$body['Diary_Id']);
        return $data;
    }

    #[Authorize(['admin', 'user'])]
    public function show_music_all(Request $request, Response $response){
        $userData = $request->getUserData();
        $body = $request->getQuery();
        $data = $this->diaryService->show_music_all($userData);
        return $data;
    }
}
