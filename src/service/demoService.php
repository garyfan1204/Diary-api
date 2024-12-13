<?php

namespace Vivian\DiaryApi\service;

class DemoService
{
    public function GetData()
    {
        return [
            'data' => 123
        ];
    }


    public function Login($acc, $pwd)
    {
        // 建立資料庫連線
        $link = mysqli_connect("localhost", "root", "", "mooddiary");

        // 檢查資料庫連線是否成功
        if (!$link) {
            return ['message' => 'MySQL資料庫連接錯誤!'];
        }

        // 在資料庫中查找用戶
        $sql = mysqli_query($link, "SELECT * FROM `member` where `Account`='$acc' and `Password`='$pwd'");
        $sel = mysqli_fetch_assoc($sql);

        // 提取用戶名和密碼
        $Account = $sel['Account'];
        $Password = $sel['Password'];

        // 檢查用戶名和密碼是否匹配
        if ($Account == $acc && $Password == $pwd) {
            return [
                'message' => '登入成功',
                'user' => $acc
            ];
        } else {
            return ['message' => '登入失敗'];
        }
    }

    public function Register($acc, $pwd, $name, $email, $sex)
    {
        // 建立資料庫連線
        $link = mysqli_connect("localhost", "root", "", "mooddiary");

        // 檢查資料庫連線是否成功
        if (!$link) {
            return ['message' => 'MySQL資料庫連接錯誤!'];
        }

        $sql_select = "SELECT * FROM member WHERE Account = '$acc' and Email='$email'";
        $select = mysqli_query($link, $sql_select);
        $num = mysqli_num_rows($select);
        if ($name == "" || $acc == "" || $pwd == "" || $email == "") {
            //echo "請確認資訊完整性";
            return ['message' => '請確認資訊完整性!'];
        } else if ($num) {
            return ['message' => '已存在使用者名稱!'];
        } else {
            $sql = "INSERT INTO member (Account, Name, Password, Email, Gender, Role, Mug_shot, BG_image) VALUES ('$acc', '$name', '$pwd', '$email', '$sex', '1', '5m1AgeRLf3.jpg', '');";
            $result = mysqli_query($link, $sql);
            if (!$result) {
                return ['message' => '註冊不成功!'];
            } else {
                return ['message' => '註冊成功!'];
            }
        }
    }

}
