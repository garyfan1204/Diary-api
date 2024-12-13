<?php

namespace Vivian\DiaryApi\controller;

use Vivian\DiaryApi\core\Request;
use Vivian\DiaryApi\core\Response;
use Vivian\DiaryApi\middlewares\Authorize;
use Vivian\DiaryApi\service\UpdateService;

use \Firebase\JWT\JWT;
use Firebase\JWT\Key;

class UpdateController
{
    private $updateService;
    public function __construct()
    {
        $this->updateService = new UpdateService();
    }



    public function reset_password(Request $request, Response $response)
    {
        //get token
        $token = $request->getHeaders()['Authorization'] ?? ''; //需要 getheader拿token 
        
        $body = $request->getBody();
        $data = $this->updateService->reset_password($token,$body['newpwd']?? null,$body['cnewpwd']?? null);
        $response->setMessage('修改成功');
        return $data;
    }   

    #[Authorize(['admin','user'])]
    public function change_password(Request $request, Response $response)
    {
        $userData = $request->getUserData();
        $body = $request->getBody();
        $data = $this->updateService->change_password($userData,$body['pwd']?? null,$body['newpwd']?? null,$body['cnewpwd']?? null);
        $response->setMessage('修改成功');
        return $data;
    }   
    

    #[Authorize(['admin','user'])]
    public function change_sex(Request $request, Response $response)
    {
        $userData = $request->getUserData();
        $body = $request->getBody();
        $data = $this->updateService->change_sex($userData,$body['sex']?? null);
        $response->setMessage('修改成功');
        return $data;
    }

    #[Authorize(['admin','user'])]
    public function change_shot(Request $request, Response $response)
    {
        $userData = $request->getUserData();
        // 获取上传的文件信息
        $file = $_FILES["file"];

        // 检查是否上传成功
        if ($file["error"] === UPLOAD_ERR_OK) {

            // 调用服务方法，传递账号和文件名
            $data = $this->updateService->change_shot_db($userData,$file);

            // 返回数据
            return $data;
        } else {
            // 文件上传失败
            $response->setMessage('文件上傳失敗');
            return $response;
        }
    }

    #[Authorize(['admin','user'])]
    public function change_BG_img(Request $request, Response $response)
    {
        $userData = $request->getUserData();
        // 获取上传的文件信息
        $file = $_FILES["file"];

        // 检查是否上传成功
        if ($file["error"] === UPLOAD_ERR_OK) {

            // 调用服务方法，传递账号和文件名
            $data = $this->updateService->change_BG_img($userData, $file);

            // 返回数据
            return $data;
        } else {
            // 文件上传失败
            $response->setMessage('文件上傳失敗');
            return $response;
        }
    }


    public function change_sentence(Request $request, Response $response)
    {
        $body = $request->getBody();
        $data = $this->updateService->change_sentence($body['Sentence_Id'],$body['Content'],$body['Type_Name']?? null);
        $response->setMessage('修改成功');
        return $data;
    }

    public function change_music(Request $request, Response $response)
    {
        $body = $request->getBody();
        $data = $this->updateService->change_music($body['Music_Id'],$body['Music_Name'],$body['Path'],$body['Singer'],$body['Emoji_Name']?? null);
        $response->setMessage('修改成功');
        return $data;
    }

    public function change_emoji(Request $request, Response $response)
    {
        // 获取上传的文件信息
        $file = $_FILES["file"];

        // 检查是否上传成功
        if ($file["error"] === UPLOAD_ERR_OK) {

            // 调用服务方法，传递账号和文件名
            $data = $this->updateService->change_emoji_BG($_POST['Emoji_Id'],$_POST['Emoji_Name'], $file);

            // 返回数据
            return $data;
        } else {
            // 文件上传失败
            $response->setMessage('文件上傳失敗');
            return $response;
        }
    }

    public function change_weather(Request $request, Response $response)
    {
        // 获取上传的文件信息
        $file = $_FILES["file"];

        // 检查是否上传成功
        if ($file["error"] === UPLOAD_ERR_OK) {
            
            // 调用上传文件的函数
            $data = $this->updateService->change_weather_BG($_POST['Weather_Id'],$_POST['Weather_Name'],$file);

            // 返回数据
            return $data;
        } else {
            // 文件上传失败
            $response->setMessage('文件上傳失敗');
            return $response;
        }
    }

    public function change_user(Request $request, Response $response)
    {
        $body = $request->getBody();
        $data = $this->updateService->change_user($body['oldAccount'],$body['Account'],$body['Name'],$body['Email']?? null);
        $response->setMessage('修改成功');
        return $data;
    }

    public function change_diary(Request $request, Response $response)
    {
        $userData = $request->getUserData();
        $body = $request->getBody();
        $data = $this->updateService->change_diary($body['Diary_Id']?? null,$body['emoji']?? null,$body['content']?? null,$body['weather']?? null,$body['title']?? null,$body['date']?? null);
        $response->setMessage('修改成功');
        return $data;
    }
}