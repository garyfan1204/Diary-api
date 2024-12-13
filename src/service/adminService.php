<?php

namespace Vivian\DiaryApi\service;

class AdminService{
    public function admin_show_music_all(){
        $link = mysqli_connect("localhost", "root", "", "mooddiary");

        // 檢查資料庫連線是否成功
        if (!$link) {
            return ['message' => 'MySQL資料庫連接錯誤!'];
        }
        $result = mysqli_query($link, "SELECT * FROM `music`;");
        $music_data = [];
        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                $music = $row['Path'] ?? null;
                $music_name = $row['Music_Name'] ?? null;
                $singer = $row['Singer'] ?? null;
                $music_Id= $row['Music_Id'] ?? null;
        
                $music_data[]= [
                    'Music_Id' => $music_Id,
                    'Path' => $music,
                    'music_name' => $music_name,
                    'singer' => $singer
                ];
            }
        }
        return [$music_data];
    }

    // public function music_add($Music_Name, $Path,$Singer,$Emoji_Name)
    // {
    //     // 建立資料庫連線
    //     $link = mysqli_connect("localhost", "root", "", "mooddiary");

    //     // 檢查資料庫連線是否成功
    //     if (!$link) {
    //         return ['message' => 'MySQL資料庫連接錯誤!'];
    //     }

    //     $sql = "INSERT INTO `music` (`Music_Id`, `Music_Name`, `Path`, `Singer`) VALUES (NULL, '$Music_Name', '$Path', '$Singer');";
    //     $result = mysqli_query($link, $sql);
    //     if (!$result) {
    //         return ['message' => '新增失敗!'];
    //     } else {
            
    //         $result2 = mysqli_query($link, "SELECT * FROM `music` WHERE `Music_Name` = '$Music_Name' AND `Path` = '$Path' AND `Singer` = '$Singer';");
    //         $sel = mysqli_fetch_assoc($result2);
    //         $Music_Id = $sel['Music_Id'] ?? null;

    //         $result3 = mysqli_query($link, "SELECT * FROM `emoji` WHERE `Emoji_Name` = '$Emoji_Name';");
    //         $sel2 = mysqli_fetch_assoc($result3);
    //         $Emoji_Id = $sel2['Emoji_Id'] ?? null;

    //         if ($sel2 && $sel) {
    //             // 檢查必須的變數是否已經設置
    //             if (isset($Music_Id) && isset($Emoji_Id)) {
    //                 // 使用準備語句來防止 SQL 注入
    //                 $stmt = $link->prepare("INSERT INTO `type` (`Type_Id`, `Music_Id`, `Emoji_Id`) VALUES (NULL, ?, ?)");
                    
    //                 if ($stmt) {
    //                     // 綁定參數
    //                     $stmt->bind_param("ii", $Music_Id, $Emoji_Id);
                        
    //                     // 執行語句
    //                     if ($stmt->execute()) {
    //                         $response = ['message' => '新增成功!'];
    //                     } else {
    //                         $response = ['message' => '新增失敗: ' . $stmt->error];
    //                     }
            
    //                     // 關閉語句
    //                     $stmt->close();
    //                 } else {
    //                     $response = ['message' => '準備語句失敗: ' . $link->error];
    //                 }
    //             } else {
    //                 $response = ['message' => '缺少必要的參數'];
    //             }
    //         } else {
    //             $response = ['message' => '條件不符合'];
    //         }
    //         return $response;            
    //     }
    // }

    public function music_add($Music_Name, $Path,$Singer,$Emoji_Name)
    {
        // 建立資料庫連線
        $link = mysqli_connect("localhost", "root", "", "mooddiary");

        // 檢查資料庫連線是否成功
        if (!$link) {
            return ['message' => 'MySQL資料庫連接錯誤!'];
        }

        // 檢查 $Type_Name 和 $Content 是否已設置
        if (isset($Music_Name) && isset($Path) && isset($Singer) && isset($Emoji_Name)) {
            // 使用準備語句查詢 Type_Id
            $stmt1 = $link->prepare("SELECT `Emoji_Id` FROM `emoji` WHERE `Emoji_Name` = ?");
            if ($stmt1) {
                $stmt1->bind_param("s", $Emoji_Name);
                $stmt1->execute();
                $result = $stmt1->get_result();
                $sel = $result->fetch_assoc();
                $Emoji_Id = $sel['Emoji_Id'] ?? null;
                $stmt1->close();

                // 確認是否找到對應的 Type_Id
                if ($Emoji_Id !== null) {
                    // 使用準備語句插入新記錄
                    $stmt2 = $link->prepare("INSERT INTO `music` (`Music_Id`,`Music_Name`,`Path`, `Singer`, `Type_Id`) VALUES (NULL, ?, ?, ?, ?)");
                    if ($stmt2) {
                        $stmt2->bind_param("sssi", $Music_Name, $Path, $Singer,$Emoji_Id);
                        if ($stmt2->execute()) {
                            $response = ['message' => '新增成功!'];
                        } else {
                            $response = ['message' => '新增失敗: ' . $stmt2->error];
                        }
                        $stmt2->close();
                    } else {
                        $response = ['message' => '準備插入語句失敗: ' . $link->error];
                    }
                } else {
                    $response = ['message' => '找不到對應的 Type_Id'];
                }
            } else {
                $response = ['message' => '準備查詢語句失敗: ' . $link->error];
            }
        } else {
            $response = ['message' => '缺少必要的參數'];
        }
        return $response;
    }


    public function emoji_add_File($Emoji_Name,$file)
    {

        $link = mysqli_connect("localhost", "root", "", "mooddiary");

        // 检查数据库连接是否成功
        if (!$link) {
            return ['message' => 'MySQL数据库连接错误!'];
        }

        //開啟圖片檔
        $file = fopen($_FILES["file"]["tmp_name"], "rb");
        // 讀入圖片檔資料
        $fileContents = fread($file, filesize($_FILES["file"]["tmp_name"])); 
        //關閉圖片檔
        fclose($file);
        //讀取出來的圖片資料必須使用base64_encode()函數加以編碼：圖片檔案資料編碼
        $fileContents = base64_encode($fileContents);
        
        // $sql="INSERT INTO speechPost (img,imgType) VALUES ('$fileContents','$imgType')";
        $sql="INSERT INTO `emoji` (`Emoji_Id`, `Photo`, `Emoji_Name`) VALUES (NULL, '$fileContents', '$Emoji_Name');";
        if($link->query($sql) === TRUE) {
            return ['message' => '新增成功!'];
        }else {
            return ['message' => '新增失敗!'];
            
        }
    }

    public function weather_add_File($Weather_Name,$file)
    {
        $link = mysqli_connect("localhost", "root", "", "mooddiary");

        // 检查数据库连接是否成功
        if (!$link) {
            return ['message' => 'MySQL数据库连接错误!'];
        }

        //開啟圖片檔
        $file = fopen($_FILES["file"]["tmp_name"], "rb");
        // 讀入圖片檔資料
        $fileContents = fread($file, filesize($_FILES["file"]["tmp_name"])); 
        //關閉圖片檔
        fclose($file);
        //讀取出來的圖片資料必須使用base64_encode()函數加以編碼：圖片檔案資料編碼
        $fileContents = base64_encode($fileContents);
        
        // $sql="INSERT INTO speechPost (img,imgType) VALUES ('$fileContents','$imgType')";
        $sql="INSERT INTO `weather` (`Weather_Id`, `Photo`, `Weather_Name`) VALUES (NULL, '$fileContents', '$Weather_Name');";
        if($link->query($sql) === TRUE) {
            return ['message' => '新增成功!'];
        }else {
            return ['message' => '新增失敗!'];
            
        }
    }

    public function sentence_add($Content, $Type_Name)
    {
        // 建立資料庫連線
        $link = mysqli_connect("localhost", "root", "", "mooddiary");

        // 檢查資料庫連線是否成功
        if (!$link) {
            return ['message' => 'MySQL資料庫連接錯誤!'];
        }

        // 檢查 $Type_Name 和 $Content 是否已設置
        if (isset($Type_Name) && isset($Content)) {
            // 使用準備語句查詢 Type_Id
            $stmt1 = $link->prepare("SELECT `Type_Id` FROM `sentence_type` WHERE `Type_Name` = ?");
            if ($stmt1) {
                $stmt1->bind_param("s", $Type_Name);
                $stmt1->execute();
                $result = $stmt1->get_result();
                $sel = $result->fetch_assoc();
                $Type_Id = $sel['Type_Id'] ?? null;
                $stmt1->close();

                // 確認是否找到對應的 Type_Id
                if ($Type_Id !== null) {
                    // 使用準備語句插入新記錄
                    $stmt2 = $link->prepare("INSERT INTO `sentence` (`Sentence_Id`, `Content`, `Type_Id`) VALUES (NULL, ?, ?)");
                    if ($stmt2) {
                        $stmt2->bind_param("si", $Content, $Type_Id);
                        if ($stmt2->execute()) {
                            $response = ['message' => '新增成功!'];
                        } else {
                            $response = ['message' => '新增失敗: ' . $stmt2->error];
                        }
                        $stmt2->close();
                    } else {
                        $response = ['message' => '準備插入語句失敗: ' . $link->error];
                    }
                } else {
                    $response = ['message' => '找不到對應的 Type_Id'];
                }
            } else {
                $response = ['message' => '準備查詢語句失敗: ' . $link->error];
            }
        } else {
            $response = ['message' => '缺少必要的參數'];
        }
        return $response;
    }

    public function user_quantity()
    {
        $link = mysqli_connect("localhost", "root", "", "mooddiary");

        // 檢查資料庫連線是否成功
        if (!$link) {
            return ['message' => 'MySQL資料庫連接錯誤!'];
        }
        $result2 = mysqli_query($link, "SELECT COUNT(*)as quantity FROM `member`;");
        $sel = mysqli_fetch_assoc($result2);
        $quantity = $sel['quantity'] ?? null;
        return $quantity;
    }

    public function sentence_quantity()
    {
        $link = mysqli_connect("localhost", "root", "", "mooddiary");

        // 檢查資料庫連線是否成功
        if (!$link) {
            return ['message' => 'MySQL資料庫連接錯誤!'];
        }
        $result2 = mysqli_query($link, "SELECT COUNT(*) AS COUNT FROM `sentence`;");
        $sel = mysqli_fetch_assoc($result2);
        $quantity = $sel['COUNT'] ?? null;
        return $quantity;
    }

    public function admin_show_user_all(){
        $link = mysqli_connect("localhost", "root", "", "mooddiary");

        // 檢查資料庫連線是否成功
        if (!$link) {
            return ['message' => 'MySQL資料庫連接錯誤!'];
        }
        $result = mysqli_query($link, "SELECT * FROM `member`;");
        $user_data = [];
        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                $Account = $row['Account'] ?? null;
                $Name = $row['Name'] ?? null;
                $Email = $row['Email'] ?? null;
                $Gender = $row['Gender'] ?? null;
        
                $user_data[]= [
                    'Account' => $Account,
                    'Name' => $Name,
                    'Email' => $Email,
                    'Gender' => $Gender
                ];
            }
        }
        return [$user_data];
    }

    public function admin_show_weather_all(){
        $link = mysqli_connect("localhost", "root", "", "mooddiary");

        // 檢查資料庫連線是否成功
        if (!$link) {
            return ['message' => 'MySQL資料庫連接錯誤!'];
        }
        $result = mysqli_query($link, "SELECT * FROM `weather`;");
        $user_data = [];
        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                $Weather_Name = $row['Weather_Name'] ?? null;
                $Photo = $row['Photo'] ?? null;
                $Weather_Id = $row['Weather_Id'] ?? null;
        
                $user_data[]= [
                    'Weather_Id' => $Weather_Id,
                    'Weather_Name' => $Weather_Name,
                    'Photo' => $Photo
                ];
            }
        }
        return [$user_data];
    }

    public function admin_show_emoji_all(){
        $link = mysqli_connect("localhost", "root", "", "mooddiary");

        // 檢查資料庫連線是否成功
        if (!$link) {
            return ['message' => 'MySQL資料庫連接錯誤!'];
        }
        $result = mysqli_query($link, "SELECT * FROM `emoji`;");
        $emoji_data = [];
        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                $Emoji_Name = $row['Emoji_Name'] ?? null;
                $Photo = $row['Photo'] ?? null;
                $Emoji_Id = $row['Emoji_Id'] ?? null;
        
                $emoji_data[]= [
                    'Emoji_Id' => $Emoji_Id,
                    'Emoji_Name' => $Emoji_Name,
                    'Photo' => $Photo
                ];
            }
        }
        return [$emoji_data];
    }

    public function admin_show_sentence_all(){
        $link = mysqli_connect("localhost", "root", "", "mooddiary");

        // 檢查資料庫連線是否成功
        if (!$link) {
            return ['message' => 'MySQL資料庫連接錯誤!'];
        }
        $result = mysqli_query($link, "SELECT * FROM sentence INNER JOIN sentence_type ON sentence_type.Type_Id = sentence.Type_Id;");
        $sentence_data = [];
        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                $Content = $row['Content'] ?? null;
                $Type_Name = $row['Type_Name'] ?? null;
                $Sentence_Id = $row['Sentence_Id'] ?? null;
        
                $sentence_data[]= [
                    'Sentence_Id' => $Sentence_Id,
                    'Content' => $Content,
                    'Type_Name' => $Type_Name
                ];
            }
        }
        return [$sentence_data];
    }

    public function user_del($Account)
    {
        // 建立資料庫連線
        $link = mysqli_connect("localhost", "root", "", "mooddiary");

        // 檢查資料庫連線是否成功
        if (!$link) {
            return ['message' => 'MySQL資料庫連接錯誤!'];
        }

        $stmt1 = mysqli_query($link, "SELECT * FROM `member` WHERE `Account` = '$Account'");
        $sel = mysqli_fetch_assoc($stmt1);
        if ($sel != null) {
            $sql = "DELETE FROM member WHERE `member`.`Account` = '$Account';";
            $result = mysqli_query($link, $sql);
            if (!$result) {
                return ['message' => "刪除失敗"];
            } else {
                return ['message' => "刪除成功"];
            }
        }else{
            return ['message' => '帳戶不存在!'];
        }
    }

    public function sentence_del($Sentence_Id)
    {
        // 建立資料庫連線
        $link = mysqli_connect("localhost", "root", "", "mooddiary");

        // 檢查資料庫連線是否成功
        if (!$link) {
            return ['message' => 'MySQL資料庫連接錯誤!'];
        }

        $stmt1 = mysqli_query($link, "SELECT * FROM `sentence` WHERE `Sentence_Id` = $Sentence_Id");
        $sel = mysqli_fetch_assoc($stmt1);
        if ($sel != null) {
            $sql = "DELETE FROM sentence WHERE `sentence`.`Sentence_Id` = '$Sentence_Id'";
            $result = mysqli_query($link, $sql);
            if (!$result) {
                return ['message' => '刪除失敗!'];
            } else {
                return ['message' => '刪除成功!'];
            }
        }else{
            return ['message' => '佳句不存在!'];
        }


        
    }

    public function music_del($Music_Id)
    {
        // 建立資料庫連線
        $link = mysqli_connect("localhost", "root", "", "mooddiary");

        // 檢查資料庫連線是否成功
        if (!$link) {
            return ['message' => 'MySQL資料庫連接錯誤!'];
        }


        $stmt1 = mysqli_query($link, "SELECT * FROM `music` WHERE `Music_Id` = $Music_Id");
        $sel = mysqli_fetch_assoc($stmt1);
        if ($sel != null) {
            $sql = "DELETE FROM music WHERE `music`.`Music_Id` = $Music_Id";
            $result = mysqli_query($link, $sql);
            $sel = "DELETE FROM type WHERE `type`.`Music_Id` = $Music_Id";
            $result2 = mysqli_query($link, $sel);
            if (!$result && !$result2) {
                return ['message' => '刪除失敗!'];
            } else {
                return ['message' => '刪除成功!'];
            }
        }else{
            return ['message' => '音樂不存在!'];
        }
    }

    public function emoji_del($Emoji_Id)
    {
        // 建立資料庫連線
        $link = mysqli_connect("localhost", "root", "", "mooddiary");

        // 檢查資料庫連線是否成功
        if (!$link) {
            return ['message' => 'MySQL資料庫連接錯誤!'];
        }


        $stmt1 = mysqli_query($link, "SELECT * FROM `emoji` WHERE `Emoji_Id` = $Emoji_Id");
        $sel = mysqli_fetch_assoc($stmt1);
        if ($sel != null) {
            $sql = "DELETE FROM `emoji` WHERE `emoji`.`Emoji_Id` = $Emoji_Id";
            $result = mysqli_query($link, $sql);
            if (!$result) {
                return ['message' => '刪除失敗!'];
            } else {
                return ['message' => '刪除成功!'];
            }
        }else{
            return ['message' => '表情不存在!'];
        }
    }

    public function weather_del($Weather_Id)
    {
        // 建立資料庫連線
        $link = mysqli_connect("localhost", "root", "", "mooddiary");

        // 檢查資料庫連線是否成功
        if (!$link) {
            return ['message' => 'MySQL資料庫連接錯誤!'];
        }


        $stmt1 = mysqli_query($link, "SELECT * FROM `weather` WHERE `Weather_Id` = $Weather_Id");
        $sel = mysqli_fetch_assoc($stmt1);
        if ($sel != null) {
            $sql = "DELETE FROM `weather` WHERE `weather`.`Weather_Id` = $Weather_Id";
            $result = mysqli_query($link, $sql);
            if (!$result) {
                return ['message' => '刪除失敗!'];
            } else {
                return ['message' => '刪除成功!'];
            }
        }else{
            return ['message' => '天氣不存在!'];
        }
    }

    public function Browse($Account)
    {
        // 建立資料庫連線
        $link = mysqli_connect("localhost", "root", "", "mooddiary");

        // 檢查資料庫連線是否成功
        if (!$link) {
            return ['message' => 'MySQL資料庫連接錯誤!'];
        }

        $sql = "INSERT INTO `browse` (`browse_Id`, `Account`) VALUES (NULL, '$Account');";
        $result = mysqli_query($link, $sql);
        if (!$result) {
            return ['message' => '請重新刷新!'];
        } else {
            $result2 = mysqli_query($link, "SELECT COUNT(*) AS COUNT FROM `browse`;");
            $sel = mysqli_fetch_assoc($result2);
            $browse = $sel['COUNT'] ?? null;
            return $browse;
        }
    }
}