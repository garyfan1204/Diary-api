<?php

namespace Vivian\DiaryApi\service;

class SearchService{
    public function Search_music_type($mood){
        $link = mysqli_connect("localhost", "root", "", "mooddiary");

        // 檢查資料庫連線是否成功
        if (!$link) {
            return ['message' => 'MySQL資料庫連接錯誤!'];
        }
        
        $result = mysqli_query($link, "SELECT * FROM diary INNER JOIN music ON diary.Music_Id = music.Music_Id WHERE diary.mood='$mood';");
        $music_data = [];
        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                $music = $row['Path'] ?? null;
                $music_name = $row['Music_Name'] ?? null;
                $singer = $row['Singer'] ?? null;
        
                $music_data[]= [
                    'music' => $music,
                    'music_name' => $music_name,
                    'singer' => $singer
                ];
            }
        }
        return [$music_data];
    }
    
    public function Search_sentence_type($Type_Id){
        $link = mysqli_connect("localhost", "root", "", "mooddiary");

        // 檢查資料庫連線是否成功
        if (!$link) {
            return ['message' => 'MySQL資料庫連接錯誤!'];
        }
        
        $result = mysqli_query($link, "SELECT * FROM sentence INNER JOIN sentence_type ON sentence_type.Type_Id = sentence.Type_Id WHERE sentence_type.Type_Id=$Type_Id;");
        $sentence_data = [];
        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                $Content = $row['Content'] ?? null;
                $Type_Name = $row['Type_Name'] ?? null;
        
                $sentence_data[]= [
                    'Content' => $Content,
                    'Type_Name' => $Type_Name
                ];
            }
        }
        return [$sentence_data];
    }
    public function Search_music($Content){
        $link = mysqli_connect("localhost", "root", "", "mooddiary");

        // 檢查資料庫連線是否成功
        if (!$link) {
            return ['message' => 'MySQL資料庫連接錯誤!'];
        }
        
        $result = mysqli_query($link, "SELECT * FROM `music` WHERE `Music_Name` LIKE '%$Content%' OR `Singer` LIKE '%$Content%';");
        $music_data = [];
        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                $Path = $row['Path'] ?? null;
                $music_name = $row['Music_Name'] ?? null;
                $singer = $row['Singer'] ?? null;
        
                $music_data[]= [
                    'Path' => $Path,
                    'music_name' => $music_name,
                    'singer' => $singer
                ];
            }
        }
        return [$music_data];
    }
    public function Search_sentence($Content){
        $link = mysqli_connect("localhost", "root", "", "mooddiary");

        // 檢查資料庫連線是否成功
        if (!$link) {
            return ['message' => 'MySQL資料庫連接錯誤!'];
        }
        
        $result = mysqli_query($link, "SELECT * FROM `sentence` WHERE `Content` LIKE '%$Content%';");
        $sentence_data = [];
        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                $Content = $row['Content'] ?? null;
                $Type_Id = $row['Type_Id'] ?? null;

                $sql = mysqli_query($link, "SELECT * FROM `sentence_type` WHERE `Type_Id` = $Type_Id");
                $sel = mysqli_fetch_assoc($sql);
        
                // 提取用戶名和密碼
                $Type_Name = $sel['Type_Name'];

                $sentence_data[]= [
                    'Content' => $Content,
                    'Type_Name' => $Type_Name
                ];
            }
        }
        return [$sentence_data];
    }
    public function Search_emoji($Content){
        $link = mysqli_connect("localhost", "root", "", "mooddiary");

        // 檢查資料庫連線是否成功
        if (!$link) {
            return ['message' => 'MySQL資料庫連接錯誤!'];
        }
        
        $result = mysqli_query($link, "SELECT * FROM `emoji` WHERE `Emoji_Name` LIKE '%$Content%';");
        $emoji_data = [];
        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                $Photo = $row['Photo'] ?? null;
                $Emoji_Name = $row['Emoji_Name'] ?? null;
        
                $emoji_data[]= [
                    'Photo' => $Photo,
                    'Emoji_Name' => $Emoji_Name
                ];
            }
        }
        return [$emoji_data];
    }
    public function Search_weather($Content){
        $link = mysqli_connect("localhost", "root", "", "mooddiary");

        // 檢查資料庫連線是否成功
        if (!$link) {
            return ['message' => 'MySQL資料庫連接錯誤!'];
        }
        
        $result = mysqli_query($link, "SELECT * FROM `weather` WHERE `Weather_Name` LIKE '%$Content%';");
        $weather_data = [];
        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                $Photo = $row['Photo'] ?? null;
                $Weather_Name = $row['Weather_Name'] ?? null;
        
                $weather_data[]= [
                    'Photo' => $Photo,
                    'Weather_Name' => $Weather_Name
                ];
            }
        }
        return [$weather_data];
    }
    public function Search_user($Content){
        $link = mysqli_connect("localhost", "root", "", "mooddiary");

        // 檢查資料庫連線是否成功
        if (!$link) {
            return ['message' => 'MySQL資料庫連接錯誤!'];
        }
        
        $result = mysqli_query($link, "SELECT * FROM `member` WHERE `Account` LIKE '%$Content%' OR `Name` LIKE '%$Content%' OR `Email` LIKE '%$Content%';");
        $user_data = [];
        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                $Account  = $row['Account'] ?? null;
                $Name = $row['Name'] ?? null;
                $Email = $row['Email'] ?? null;
                $Gender=$row['Gender'] ?? null;
        
                $user_data[]= [
                    'Account' => $Account ,
                    'Name' => $Name,
                    'Email' => $Email,
                    'Gender' => $Gender
                ];
            }
        }
        return [$user_data];
    }
    public function Search_user_sex($Gender){
        $link = mysqli_connect("localhost", "root", "", "mooddiary");

        // 檢查資料庫連線是否成功
        if (!$link) {
            return ['message' => 'MySQL資料庫連接錯誤!'];
        }
        
        $result = mysqli_query($link, "SELECT * FROM `member` WHERE `Gender` = $Gender;");
        $user_data = [];
        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                $Account  = $row['Account'] ?? null;
                $Name = $row['Name'] ?? null;
                $Email = $row['Email'] ?? null;
                $Gender=$row['Gender'] ?? null;
        
                $user_data[]= [
                    'Account' => $Account ,
                    'Name' => $Name,
                    'Email' => $Email,
                    'Gender' => $Gender
                ];
            }
        }
        return [$user_data];
    }
}