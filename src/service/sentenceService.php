<?php

namespace Vivian\DiaryApi\service;

class SentenceService
{
    public function sentence()
    {
        $link = mysqli_connect("localhost", "root", "", "mooddiary");

        // 檢查資料庫連線是否成功
        if (!$link) {
            return ['message' => 'MySQL資料庫連接錯誤!'];
        }

        
        $sql="SELECT Content FROM sentence ORDER BY RAND() LIMIT 1";
        $result = mysqli_query($link, $sql);
        $row=mysqli_fetch_assoc($result);
        $sentence=$row["Content"];

        return [
            'sentence' => $sentence
        ];
        

    }
}