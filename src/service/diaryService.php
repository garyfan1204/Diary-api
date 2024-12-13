<?php

namespace Vivian\DiaryApi\service;

use \Firebase\JWT\JWT;
use Firebase\JWT\Key;

class DiaryService
{    
    public function write_diary($userData, $emoji, $content, $weather, $title, $date)
    {
        // $userData = JWT_decode($token);
        // $account = $userData->user;

        $link = mysqli_connect("localhost", "root", "", "mooddiary");
        // 檢查資料庫連線是否成功
        if (!$link) {
            throw new \Exception('資料庫連接失敗', 500);
        }

        // Query database to get emoji and weather IDs
        $sql = mysqli_query($link, "SELECT * FROM `emoji` WHERE `Emoji_Name` LIKE '$emoji'");
        $sel = mysqli_fetch_assoc($sql);
        $emoji_id = $sel['Emoji_Id'];

        $sql2 = mysqli_query($link, "SELECT * FROM `weather` WHERE `Weather_Name` LIKE '$weather'");
        $sel2 = mysqli_fetch_assoc($sql2);
        $weather_id = $sel2['Weather_Id'];

        $time =date("H:i:s");
        $time =  preg_replace('/\s(?=)/', '', $time);
        // // Convert newline characters to <br> tags
        $text_with_br = nl2br(mysqli_real_escape_string($link, $content)); // Escape and convert newline characters

        // // Prepare and execute the insert statement
        $stmt = $link->prepare("INSERT INTO `diary` (`Diary_Id`, `Account`, `Emoji_Id`, `Weather_Id`, `Day`, `time`, `Title`, `Content`) VALUES (NULL, '$userData->user', '$emoji_id', '$weather_id', '$date', '$time', '$title', '$text_with_br');");
        if ($stmt) {

            // 执行准备好的语句
            if ($stmt->execute()) {
                $sql3 = mysqli_query($link, "SELECT * FROM `diary` ORDER BY `Day` DESC,`time` DESC LIMIT 1;");
                $sel3 = mysqli_fetch_assoc($sql3);
                $Diary_Id = $sel3['Diary_Id'];

                $command = "python test.py";
                // 执行命令并获取输出
                $output = shell_exec($command);
                // 解析输出为JSON   
                $result = json_decode($output, true);
        
                $sql4 = mysqli_query($link, "SELECT * FROM diary INNER JOIN music_type ON diary.mood=music_type.Type_Name WHERE diary.Diary_Id = '$Diary_Id';");
                // $sql4 = mysqli_query($link, "SELECT * FROM `music_type` WHERE `Type_Name` = '$result';");
                $sel4 = mysqli_fetch_assoc($sql4);
                $music_type = $sel4['Type_Id'];

                // // Array to store music IDs
                $music_ids = array();
                $result2 = mysqli_query($link, "SELECT * FROM `music` WHERE `Type_Id` = '$music_type';");
                if (mysqli_num_rows($result2) > 0) {
                    // Store music IDs in the array
                    while ($row = mysqli_fetch_assoc($result2)) {
                        $music_ids[] = $row['Music_Id'];
                    }

                    // Randomly select a music ID
                    $random_music_id = $music_ids[array_rand($music_ids)];
                }

                // return $random_music_id;

                $sqlUpdate = "UPDATE `diary` SET `Music_Id` = $random_music_id WHERE `Diary_Id` = $Diary_Id;";
                
                
                if (mysqli_query($link, $sqlUpdate)) {

                    $sql5 = mysqli_query($link, "SELECT * FROM `music` WHERE `Music_Id` = $random_music_id;");
                    $sel5 = mysqli_fetch_assoc($sql5);
                    $Path = $sel5['Path'];
                    $Music_Name = $sel5['Music_Name'];
                    $Singer = $sel5['Singer'];
                    return [
                        // 'message' => '修改成功',
                        'Diary_Id' => $Diary_Id,
                        'Music_Id' => $random_music_id,
                        'Path' => $Path,
                        'Music_Name'=>$Music_Name,
                        'Singer'=>$Singer
                    ];
                }
                // return $Diary_Id;
            } else {
                throw new \Exception('新增失敗', 401);
            }
        } else {
            throw new \Exception('內容不完整', 401);
        }
    }
    public function show_diary_all($userData)
    {
        // $userData = JWT_decode($token);
        // $account = $userData->user;
        $link = mysqli_connect("localhost", "root", "", "mooddiary");

        // 檢查資料庫連線是否成功
        if (!$link) {
            return ['message' => 'MySQL資料庫連接錯誤!'];
        }
        $result = mysqli_query($link, "SELECT * FROM diary  WHERE `Account` LIKE '$userData->user';");
        $diary_data = [];
        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                $Diary_Id = $row['Diary_Id'] ?? null;
                $date = $row['Day'] ?? null;
                $emoji = $row['Emoji_Id'] ?? null;
                $title = $row['Title'] ?? null;
                $diary_data[] = [
                    'Diary_Id' => $Diary_Id,
                    'Day' => $date,
                    'Emoji' => $emoji,
                    'Title' => $title
                ];
            }
        }
        return $diary_data;
    }

    public function show_diary($userData, $Diary_Id)
    {
        $link = mysqli_connect("localhost", "root", "", "mooddiary");

        // 檢查資料庫連線是否成功
        if (!$link) {
            return ['message' => 'MySQL資料庫連接錯誤!'];
        }

        $result = mysqli_query($link, "SELECT * FROM diary INNER JOIN music ON diary.Music_Id=music.Music_Id WHERE `Account` = '$userData->user' AND `Diary_Id`='$Diary_Id';");
        $sel = mysqli_fetch_assoc($result);
        $diary_id=$sel['Diary_Id'] ?? null;
        $date = $sel['Day'] ?? null;
        $emoji = $sel['Emoji_Id'] ?? null;
        $weather = $sel['Weather_Id'] ?? null;
        $title = $sel['Title'] ?? null;

        // 从数据库中检索文本数据
        $text_from_db = stripslashes($sel['Content'] ?? null);
        // 将空格替换为换行符
        $content = str_replace('rn', '<br>', $text_from_db);

        $music = $sel['Path'] ?? null;
        $music_name = $sel['Music_Name'] ?? null;
        $singer = $sel['Singer'] ?? null;

        return [
            'Diary_Id'=>$diary_id,
            'Day' => $date,
            'Emoji' => $emoji,
            'Title' => $title,
            'weather' => $weather,
            'content' => $content,
            'music' => $music,
            'music_name' => $music_name,
            'singer' => $singer
        ];
    }

    public function show_music_all($userData)
    {
        // $userData = JWT_decode($token);
        // $account = $userData->user;

        $link = mysqli_connect("localhost", "root", "", "mooddiary");

        // 檢查資料庫連線是否成功
        if (!$link) {
            return ['message' => 'MySQL資料庫連接錯誤!'];
        }
        $result = mysqli_query($link, "SELECT * FROM diary INNER JOIN music ON diary.Music_Id=music.Music_Id WHERE `Account` LIKE '$userData->user';");
        $music_data = [];
        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                $diary_id=$row['Diary_Id'] ?? null;
                $date = $row['Day'] ?? null;
                $music = $row['Path'] ?? null;
                $music_name = $row['Music_Name'] ?? null;
                $singer = $row['Singer'] ?? null;

                $music_data[] = [
                    'Diary_Id'=>$diary_id,
                    'Day' => $date,
                    'music' => $music,
                    'music_name' => $music_name,
                    'singer' => $singer
                ];
            }
        }
        return $music_data;
    }
}

// function JWT_decode($token)
// {
//     $secretKey = 'abcdefghijklmnopqrstuvwxyz'; //密鑰
//     $algorithm = 'HS256'; //加密的演算法
//     $data = JWT::decode($token, new Key($secretKey, $algorithm));

//     return $data;
// }
