<?php

namespace Vivian\DiaryApi\service;

use \Firebase\JWT\JWT;
use Firebase\JWT\Key;

class UpdateService
{
    public function reset_password($token,$newpwd, $cnewpwd)
    {
        $userData = JWT_decode($token);

        $link = mysqli_connect("localhost", "root", "", "mooddiary");

        // 檢查資料庫連線是否成功
        if (!$link) {
            return ['message' => 'MySQL資料庫連接錯誤!'];
        }

        if ($newpwd == "" || $cnewpwd == "") {
            throw new \Exception('請確認資訊完整性', 401);
            // return ['message' => '請確認資訊完整性!'];
        } else {
            // $hash_pwd = Hash_Pwd($pwd);
            $sql = mysqli_query($link, "SELECT * FROM `member` WHERE `Account` = '$userData->user';");
            $sel = mysqli_fetch_assoc($sql);
        }
        if ($sel) {
            if ($newpwd == $cnewpwd) {
                $hash_pwd = Hash_Pwd($newpwd);
                $sqlUpdate = "UPDATE member SET Password='$hash_pwd' WHERE Account='$userData->user'";
                if (mysqli_query($link, $sqlUpdate)) {
                    return [
                        // 'message' => '修改成功',
                        'user' => $userData->user
                    ];
                } else {
                    // return ['message' => '修改失敗'];
                    throw new \Exception('修改失敗', 401);
                }
            } else {
                throw new \Exception('確認密碼與新密碼不同', 401);
            }
        } else {
            throw new \Exception('密碼錯誤', 401);
        }
    }

    public function change_password($userData, $pwd, $newpwd, $cnewpwd)
    {
        // $userData = JWT_decode($token);
        // $account = $userData->user;
        $link = mysqli_connect("localhost", "root", "", "mooddiary");

        // 檢查資料庫連線是否成功
        if (!$link) {
            return ['message' => 'MySQL資料庫連接錯誤!'];
        }

        if ($userData->user == "" || $pwd == "" || $newpwd == "" || $cnewpwd == "") {
            throw new \Exception('請確認資訊完整性', 401);
            // return ['message' => '請確認資訊完整性!'];
        } else {
            $hash_pwd = Hash_Pwd($pwd);
            $sql = mysqli_query($link, "SELECT * FROM `member` WHERE `Account` = '$userData->user' AND `Password` = '$hash_pwd';");
            $sel = mysqli_fetch_assoc($sql);
        }
        if ($sel) {
            if ($newpwd == $cnewpwd) {
                $hash_pwd = Hash_Pwd($newpwd);
                $sqlUpdate = "UPDATE member SET Password='$hash_pwd' WHERE Account='$userData->user'";
                if (mysqli_query($link, $sqlUpdate)) {
                    return [
                        // 'message' => '修改成功',
                        'user' => $userData->user
                    ];
                } else {
                    // return ['message' => '修改失敗'];
                    throw new \Exception('修改失敗', 401);
                }
            } else {
                throw new \Exception('確認密碼與新密碼不同', 401);
            }
        } else {
            throw new \Exception('密碼錯誤', 401);
        }
    }


    public function change_sex($userData, $sex)
    {
        // $userData = JWT_decode($token);
        // $account = $userData->user;

        $link = mysqli_connect("localhost", "root", "", "mooddiary");

        // 檢查資料庫連線是否成功
        if (!$link) {
            return ['message' => 'MySQL資料庫連接錯誤!'];
        }

        $sql = mysqli_query($link, "SELECT * FROM `member` WHERE `Account` = '$userData->user';");
        $sel = mysqli_fetch_assoc($sql);

        if ($sel) {
            $sqlUpdate = "UPDATE member SET Gender='$sex' WHERE Account='$userData->user'";
            if (mysqli_query($link, $sqlUpdate)) {
                return [
                    // 'message' => '修改成功',
                    'user' => $userData->user
                ];
            } else {
                // return ['message' => '修改失敗'];
                throw new \Exception('修改失敗', 401);
            }
        }
    }

    public function change_shot_db($userData, $file)
    {
        $link = mysqli_connect("localhost", "root", "", "mooddiary");

        // 检查数据库连接是否成功
        if (!$link) {
            return ['message' => 'MySQL資料庫連接錯誤!'];
        }

        // 查询用户是否存在
        $sql = mysqli_query($link, "SELECT * FROM `member` WHERE `Account` = '$userData->user';");
        $sel = mysqli_fetch_assoc($sql);

        if ($sel) {
            //開啟圖片檔
            $file = fopen($_FILES["file"]["tmp_name"], "rb");
            // 讀入圖片檔資料
            $fileContents = fread($file, filesize($_FILES["file"]["tmp_name"])); 
            //關閉圖片檔
            fclose($file);
            //讀取出來的圖片資料必須使用base64_encode()函數加以編碼：圖片檔案資料編碼
            $fileContents = base64_encode($fileContents);
            
            $sql="UPDATE member SET Mug_shot='$fileContents' WHERE Account='$userData->user';";
            if($link->query($sql) === TRUE) {
                return ['message' => '修改成功', 'user' => $userData->user];
            }else {
                throw new \Exception('修改失敗', 500);
                
            }
        } else {
            // 用户不存在
            throw new \Exception('用户不存在', 404);
        }
    }

    public function change_BG_img($userData, $file)
    {
        $link = mysqli_connect("localhost", "root", "", "mooddiary");

        // 检查数据库连接是否成功
        if (!$link) {
            return ['message' => 'MySQL数据库连接错误!'];
        }

        // 查询用户是否存在
        $sql = mysqli_query($link, "SELECT * FROM `member` WHERE `Account` = '$userData->user';");
        $sel = mysqli_fetch_assoc($sql);

        if ($sel) {
            //開啟圖片檔
            $file = fopen($_FILES["file"]["tmp_name"], "rb");
            // 讀入圖片檔資料
            $fileContents = fread($file, filesize($_FILES["file"]["tmp_name"])); 
            //關閉圖片檔
            fclose($file);
            //讀取出來的圖片資料必須使用base64_encode()函數加以編碼：圖片檔案資料編碼
            $fileContents = base64_encode($fileContents);
            
            $sql="UPDATE member SET BG_Image='$fileContents' WHERE Account='$userData->user';";
            if($link->query($sql) === TRUE) {
                return ['message' => '修改成功', 'user' => $userData->user];
            }else {
                throw new \Exception('修改失敗', 500);
                
            }
        } else {
            // 用户不存在
            throw new \Exception('用户不存在', 404);
        }
    }

    public function change_emoji_BG($Emoji_Id,$Emoji_Name,$file)
    {
        $link = mysqli_connect("localhost", "root", "", "mooddiary");
        if (!$link) {
            return ['message' => 'MySQL数据库连接错误!'];
        }

        $sql = mysqli_query($link, "SELECT * FROM `emoji` WHERE `Emoji_Id` = $Emoji_Id;");
        $sel = mysqli_fetch_assoc($sql);

        //開啟圖片檔
        $file = fopen($_FILES["file"]["tmp_name"], "rb");
        // 讀入圖片檔資料
        $fileContents = fread($file, filesize($_FILES["file"]["tmp_name"])); 
        //關閉圖片檔
        fclose($file);
        //讀取出來的圖片資料必須使用base64_encode()函數加以編碼：圖片檔案資料編碼
        $fileContents = base64_encode($fileContents);

        if ($sel) {
            $sqlUpdate = "UPDATE `emoji` SET `Photo` = '$fileContents', `Emoji_Name` = '$Emoji_Name' WHERE `emoji`.`Emoji_Id` = $Emoji_Id;";
            if (mysqli_query($link, $sqlUpdate)) {
                return [
                    'message' => '修改成功'
                ];
            } else {
                throw new \Exception('修改失敗', 401);
            }
        }else {
            throw new \Exception('用户不存在', 404); 
        }
    }

    public function change_weather_BG($Weather_Id,$Weather_Name,$file)
    {
        $link = mysqli_connect("localhost", "root", "", "mooddiary");

        if (!$link) {
            return ['message' => 'MySQL数据库连接错误!'];
        }

        $sql = mysqli_query($link, "SELECT * FROM `weather` WHERE `Weather_Id` = $Weather_Id;");
        $sel = mysqli_fetch_assoc($sql);

        //開啟圖片檔
        $file = fopen($_FILES["file"]["tmp_name"], "rb");
        // 讀入圖片檔資料
        $fileContents = fread($file, filesize($_FILES["file"]["tmp_name"])); 
        //關閉圖片檔
        fclose($file);
        //讀取出來的圖片資料必須使用base64_encode()函數加以編碼：圖片檔案資料編碼
        $fileContents = base64_encode($fileContents);

        if ($sel) {
            $sqlUpdate = "UPDATE `weather` SET `Photo` = '$fileContents', `Weather_Name` = '$Weather_Name' WHERE `weather`.`Weather_Id` = $Weather_Id;";
            if (mysqli_query($link, $sqlUpdate)) {
                return [
                    'message' => '修改成功'
                ];
            } else {
                throw new \Exception('修改失敗', 401);
            }
        } else {
            throw new \Exception('用户不存在', 404);
        }
    }

    public function change_sentence($Sentence_Id,$Content, $Type_Name)
    {
        $link = mysqli_connect("localhost", "root", "", "mooddiary");

        // 檢查資料庫連線是否成功
        if (!$link) {
            return ['message' => 'MySQL資料庫連接錯誤!'];
        }

        $sql = mysqli_query($link, "SELECT * FROM `sentence` WHERE `Sentence_Id` = $Sentence_Id;");
        $sel = mysqli_fetch_assoc($sql);

        if ($sel != null) {
            $result = mysqli_query($link, "SELECT * FROM `sentence_type` WHERE `Type_Name` = '$Type_Name';");
            $sql1 = mysqli_fetch_assoc($result);
            $Type_Id = $sql1['Type_Id'] ?? null;

            $sqlUpdate = "UPDATE `sentence` SET `Content` = '$Content', `Type_Id` = '$Type_Id' WHERE `sentence`.`Sentence_Id` = $Sentence_Id;";
            if (mysqli_query($link, $sqlUpdate)) {
                return;
            } else {
                throw new \Exception('修改失敗', 401);
            }
        }else{
            throw new \Exception('修改失敗', 401);
        }
    }

    public function change_music($Music_Id,$Music_Name, $Path,$Singer,$Emoji_Name)
    {
        $link = mysqli_connect("localhost", "root", "", "mooddiary");

        // 檢查資料庫連線是否成功
        if (!$link) {
            return ['message' => 'MySQL資料庫連接錯誤!'];
        }

        $sql = mysqli_query($link, "SELECT * FROM `music` WHERE `Music_Id` = $Music_Id;");
        $sel = mysqli_fetch_assoc($sql);

        if ($sel != null) {
            $result = mysqli_query($link, "SELECT * FROM `emoji` WHERE `Emoji_Name` = '$Emoji_Name';");
            $sql1 = mysqli_fetch_assoc($result);
            $Emoji_Id = $sql1['Emoji_Id'] ?? null;
            
            $sqlUpdate = "UPDATE `music` SET `Music_Name` = '$Music_Name', `Path` = '$Path', `Singer` = '$Singer' WHERE `music`.`Music_Id` = $Music_Id;";
            $sqlUpdate2 = "UPDATE `type` SET `Emoji_Id` = '$Emoji_Id' WHERE `type`.`Music_Id` = $Music_Id;";
            if (mysqli_query($link, $sqlUpdate) && mysqli_query($link, $sqlUpdate2)) {
                return;
            } else {
                throw new \Exception('修改失敗', 401);
            }
        }else{
            throw new \Exception('修改失敗', 401);
        }
    }

    public function change_user($oldAccount, $Account, $Name, $Email)
    {
        $link = mysqli_connect("localhost", "root", "", "mooddiary");

        // 檢查資料庫連線是否成功
        if (!$link) {
            return ['message' => 'MySQL資料庫連接錯誤!'];
        }

        // 使用預處理語句來防止SQL注入
        $stmt = $link->prepare("SELECT * FROM `member` WHERE `Account` = ?");
        $stmt->bind_param("s", $oldAccount);
        $stmt->execute();
        $sel = $stmt->get_result()->fetch_assoc();

        $stmt2 = $link->prepare("SELECT * FROM `member` WHERE `Account` = ?");
        $stmt2->bind_param("s", $Account);
        $stmt2->execute();
        $sel2 = $stmt2->get_result()->fetch_assoc();

        if ($sel2) {
            throw new \Exception('此帳戶已存在請重新輸入', 401);
        } else {
            if ($sel != null) {
                $stmtUpdate = $link->prepare("UPDATE `member` SET `Account` = ?, `Name` = ?, `Email` = ? WHERE `Account` = ?");
                $stmtUpdate->bind_param("ssss", $Account, $Name, $Email, $oldAccount);
                if ($stmtUpdate->execute()) {
                    return;
                } else {
                    throw new \Exception('修改失敗', 401);
                }
            } else {
                throw new \Exception('舊帳號不存在', 404);
            }
        }
    }

    public function change_diary($Diary_Id, $emoji, $content, $weather, $title, $date)
    {
        $link = mysqli_connect("localhost", "root", "", "mooddiary");

        // 檢查資料庫連線是否成功
        if (!$link) {
            return ['message' => 'MySQL資料庫連接錯誤!'];
        }

        // Query database to get emoji and weather IDs
        $sql = mysqli_query($link, "SELECT * FROM `emoji` WHERE `Emoji_Name` LIKE '$emoji'");
        $sel = mysqli_fetch_assoc($sql);
        $emoji_id = $sel['Emoji_Id'];

        $sql2 = mysqli_query($link, "SELECT * FROM `weather` WHERE `Weather_Name` LIKE '$weather'");
        $sel2 = mysqli_fetch_assoc($sql2);
        $weather_id = $sel2['Weather_Id'];

        // // Array to store music IDs
        $music_ids = array();

        $time =date("H:i:s");

        // // Query database to get music IDs based on emoji ID
        $result = mysqli_query($link, "SELECT * FROM `type` WHERE `Emoji_Id` = '$emoji_id'");
        if (mysqli_num_rows($result) > 0) {
            // Store music IDs in the array
            while ($row = mysqli_fetch_assoc($result)) {
                $music_ids[] = $row['Music_Id'];
            }

            // Randomly select a music ID
            $random_music_id = $music_ids[array_rand($music_ids)];
        } else {
            throw new \Exception('查無結果', 404);
        }

        // // Convert newline characters to <br> tags
        $text_with_br = nl2br(mysqli_real_escape_string($link, $content)); // Escape and convert newline characters

        $sqlUpdate = "UPDATE `diary` SET `Emoji_Id` = '$emoji_id', `Music_Id` = '$random_music_id', `Weather_Id` = '$weather_id', `Title` = '$title', `Content` = '$text_with_br', `Day` = '$date', `time` ='$time' WHERE `diary`.`Diary_Id` = $Diary_Id;";
        if (mysqli_query($link, $sqlUpdate)) {
            return;
        } else {
            throw new \Exception('修改失敗', 401);
        }
    }
}

function Hash_Pwd($pwd)
{
    $saltkey = "yhgyjuitrti65rtuyybglkjhgf";
    $salt_and_pwd = $saltkey . $pwd;
    $hashpwd = hash('sha256', $salt_and_pwd, PASSWORD_DEFAULT);
    $base64pwd = base64_encode($hashpwd);
    return $base64pwd;
}
function JWT_decode($token)
{
    $secretKey = 'abcdefghijklmnopqrstuvwxyz'; //密鑰
    $algorithm = 'HS256'; //加密的演算法
    $data = JWT::decode($token, new Key($secretKey, $algorithm));

    return $data;
}
