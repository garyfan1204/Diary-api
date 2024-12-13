<?php

namespace Vivian\DiaryApi\service;

class FormService{
    public function form_music(){
        $link = mysqli_connect("localhost", "root", "", "mooddiary");

        // 檢查資料庫連線是否成功
        if (!$link) {
            return ['message' => 'MySQL資料庫連接錯誤!'];
        }
        $result = mysqli_query($link, "SELECT music_type.Type_Id,COUNT(*) AS COUNT,music_type.Type_Name FROM music INNER JOIN music_type ON music_type.Type_Id = music.Type_Id GROUP BY music.Type_Id;");
        $music_data = [];
        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                $Type_Name = $row['Type_Name'] ?? null;
                $COUNT = $row['COUNT'] ?? null;
        
                $music_data[]= [
                    'Emoji_Name' => $Type_Name,
                    'COUNT' => $COUNT
                ];
            }
        }
        return [$music_data];
    }
    
    public function form_Sentence(){
        $link = mysqli_connect("localhost", "root", "", "mooddiary");

        // 檢查資料庫連線是否成功
        if (!$link) {
            return ['message' => 'MySQL資料庫連接錯誤!'];
        }
        $result = mysqli_query($link, "SELECT sentence.Type_Id,COUNT(*) AS COUNT,sentence_type.Type_Name FROM sentence INNER JOIN sentence_type ON sentence.Type_Id = sentence_type.Type_Id GROUP BY Type_Id;");
        $Sentence_data = [];
        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                $Type_Name = $row['Type_Name'] ?? null;
                $COUNT = $row['COUNT'] ?? null;
        
                $Sentence_data[]= [
                    'Type_Name' => $Type_Name,
                    'COUNT' => $COUNT
                ];
            }
        }
        return [$Sentence_data];
    }

    public function form_emoji(){
        $link = mysqli_connect("localhost", "root", "", "mooddiary");

        // 檢查資料庫連線是否成功
        if (!$link) {
            return ['message' => 'MySQL資料庫連接錯誤!'];
        }
        $result = mysqli_query($link, "SELECT emoji.Emoji_Id, COUNT(diary.Emoji_Id) AS COUNT, emoji.Emoji_Name FROM emoji LEFT JOIN diary ON emoji.Emoji_Id = diary.Emoji_Id GROUP BY emoji.Emoji_Id, emoji.Emoji_Name;");
        $emoji_data = [];
        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                $Emoji_Name = $row['Emoji_Name'] ?? null;
                $COUNT = $row['COUNT'] ?? null;
        
                $emoji_data[]= [
                    'Emoji_Name' => $Emoji_Name,
                    'COUNT' => $COUNT
                ];
            }
        }
        return [$emoji_data];
    }
    public function form_sex(){
        $link = mysqli_connect("localhost", "root", "", "mooddiary");

        // 檢查資料庫連線是否成功
        if (!$link) {
            return ['message' => 'MySQL資料庫連接錯誤!'];
        }
        $result = mysqli_query($link, "SELECT Gender,COUNT(*)AS COUNT FROM `member` GROUP BY Gender;");
        $sex_data = [];
        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                if($row['Gender']==0){
                    $Gender="不透露"?? null;
                }elseif($row['Gender']==1){
                    $Gender="男生"?? null;
                }elseif($row['Gender']==2){
                    $Gender="女生"?? null;
                }
                // $Gender = $row['Gender'] ?? null;

                $COUNT = $row['COUNT'] ?? null;
        
                $sex_data[]= [
                    'Gender' => $Gender,
                    'COUNT' => $COUNT
                ];
            }
        }
        return [$sex_data];
    }
}