<?php

namespace Vivian\DiaryApi\core;

class Request
{
    private $userData;
    public function getPath()
    {
        $path = explode('?', $_SERVER['REQUEST_URI'] ?? '/')[0];
        return $path;
    }

    public function getMethod()
    {
        $method = $_SERVER['REQUEST_METHOD'];
        return $method;
    }

    public function getQuery()
    {
        $query = $_GET;
        return $query;
    }

    public function getBody()
    {
        $body = json_decode(file_get_contents('php://input'), true);
        return $body;
    }

    public function getHeaders(){
        $headers=getallheaders();
        return $headers;
    }
        
    public function setUserData($userData)
    {
        $this->userData = $userData;
    }

    public function getUserData()
    {
        return $this->userData;
    }
}
