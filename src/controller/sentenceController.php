<?php

namespace Vivian\DiaryApi\controller;


use Vivian\DiaryApi\service\SentenceService;

class SentenceController
{
    private $sentenceService;
    public function __construct()
    {
        $this->sentenceService = new SentenceService();
    }
    public function sentence(){
        return $this->sentenceService->sentence();
    }
}