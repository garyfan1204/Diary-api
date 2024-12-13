<?php

namespace Vivian\DiaryApi\controller;

use Vivian\DiaryApi\core\Request;
use Vivian\DiaryApi\core\Response;
use Vivian\DiaryApi\middlewares\Authorize;
use Vivian\DiaryApi\service\AdminService;

class AdminController
{
    private $adminService;
    public function __construct()
    {
        $this->adminService = new AdminService();
    }
    
    public function admin_show_music_all(Request $request, Response $response)
    {
        return $this->adminService->admin_show_music_all();
    }
    
    public function music_add(Request $request, Response $response)
    {
        $body = $request->getBody();
        $data = $this->adminService->music_add($body['Music_Name'],$body['Path']?? null,$body['Singer']?? null,$body['Emoji_Name']?? null);
        return $data;
    }
    
    public function emoji_add(Request $request, Response $response)
    {
        // 获取上传的文件信息
        $file = $_FILES["file"];

        // 检查是否上传成功
        if ($file["error"] === UPLOAD_ERR_OK) {

            // 调用上传文件的函数
            $data = $this->adminService->emoji_add_File($_POST['Emoji_Name'],$file);

            // 返回数据
            return $data;
        } else {
            // 文件上传失败
            $response->setMessage('文件上傳失敗');
            return $response;
        }
    }
    
    public function weather_add(Request $request, Response $response)
    {
        // 获取上传的文件信息
        $file = $_FILES["file"];

        // 检查是否上传成功
        if ($file["error"] === UPLOAD_ERR_OK) {
            

            // 调用上传文件的函数
            $data = $this->adminService->weather_add_File($_POST['Weather_Name'],$file);

            // 返回数据
            return $data;
        } else {
            // 文件上传失败
            $response->setMessage('文件上傳失敗');
            return $response;
        }
    }
    
    public function user_quantity(Request $request, Response $response)
    {
        return $this->adminService->user_quantity();
    }

    public function sentence_quantity(Request $request, Response $response)
    {
        return $this->adminService->sentence_quantity();
    }

    public function sentence_add(Request $request, Response $response)
    {
        $body = $request->getBody();
        $data = $this->adminService->sentence_add($body['Content'],$body['Type_Name']?? null);
        return $data;
    }

    public function admin_show_user_all(Request $request, Response $response)
    {
        return $this->adminService->admin_show_user_all();
    }

    public function admin_show_weather_all(Request $request, Response $response)
    {
        return $this->adminService->admin_show_weather_all();
    }

    public function admin_show_emoji_all(Request $request, Response $response)
    {
        return $this->adminService->admin_show_emoji_all();
    }

    public function admin_show_sentence_all(Request $request, Response $response)
    {
        return $this->adminService->admin_show_sentence_all();
    }

    public function user_del(Request $request, Response $response)
    {
        $body = $request->getBody();
        $data = $this->adminService->user_del($body['Account']?? null);
        return $data;
    }

    public function sentence_del(Request $request, Response $response)
    {
        $body = $request->getBody();
        $data = $this->adminService->sentence_del($body['Sentence_Id']?? null);
        return $data;
    }

    public function music_del(Request $request, Response $response)
    {
        $body = $request->getBody();
        $data = $this->adminService->music_del($body['Music_Id']?? null);
        return $data;
    }

    public function emoji_del(Request $request, Response $response)
    {
        $body = $request->getBody();
        $data = $this->adminService->emoji_del($body['Emoji_Id']?? null);
        return $data;
    }

    public function weather_del(Request $request, Response $response)
    {
        $body = $request->getBody();
        $data = $this->adminService->weather_del($body['Weather_Id']?? null);
        return $data;
    }

    public function Browse(Request $request, Response $response)
    {
        $body = $request->getBody();
        $data = $this->adminService->Browse($body['Account']?? null);
        return $data;
    }
}
