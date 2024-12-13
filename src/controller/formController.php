<?php

namespace Vivian\DiaryApi\controller;

use BadFunctionCallException;
use Vivian\DiaryApi\core\Request;
use Vivian\DiaryApi\core\Response;
use Vivian\DiaryApi\middlewares\Authorize;
use Vivian\DiaryApi\service\FormService;

class FormController
{
    private $FormService;
    public function __construct()
    {
        $this->FormService = new FormService();
    }

    public function form_music(Request $request, Response $response)
    {
        return $this->FormService->form_music();
    }

    public function form_Sentence(Request $request, Response $response)
    {
        return $this->FormService->form_Sentence();
    }

    
    public function form_emoji(Request $request, Response $response)
    {
        return $this->FormService->form_emoji();
    }

    public function form_sex(Request $request, Response $response)
    {
        return $this->FormService->form_sex();
    }
}
