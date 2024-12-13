<?php

namespace Vivian\DiaryApi\core;

class Response
{
    private ?string $message = null; //string前面有?，允許為空，參數($message)可能為 string 類型或是 null
    public function returnBody($data = null, $code = 200, $msg = null)
    {
        header('Content-Type: application/json');
        http_response_code($code);

        //沒辦法對每個API客製化回傳的格式，要做統一的處理。設計格式化的目的為讓拿API的人好辨識回傳的格式，保證抓某一個KEY會得到所需的內容。
        $responseFormat = [
            //設計這個要看前端需要得知的內容是什麼，希望的內容有"data(資料)、msg(訊息，EX:登入成功)"
            'data' => $data,
            'msg' => $msg ?? $this->message,   //"??"為如果為false,null,0，就顯示message
        ];
        echo json_encode($responseFormat);
    }

    public function setMessage($message)
    {
        $this->message = $message;
    }
}
