<?php

namespace Vivian\DiaryApi\service;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Vivian\DiaryApi\core\Request;

use \Firebase\JWT\JWT;
use Firebase\JWT\Key;

class MemberService
{

    public function login($account, $password)
    {
        // 建立資料庫連線
        $link = mysqli_connect("localhost", "root", "", "mooddiary");

        // 檢查資料庫連線是否成功
        if (!$link) {
            return ['message' => 'MySQL資料庫連接錯誤!'];
        }

        if ($account == "" || $password == "") {
            throw new \Exception('請確認資訊完整性', 401);
        } else {
            $hash_pwd = Hash_Pwd($password);

            // 在資料庫中查找用戶
            $sql = mysqli_query($link, "SELECT *, CASE Role WHEN 1 THEN 'admin' WHEN 2 THEN 'user' END AS RoleName FROM member WHERE Account='$account' AND Password='$hash_pwd'");
            $sel = mysqli_fetch_assoc($sql);

            $Account = $sel['Account'] ?? null;
            $Password = $sel['Password'] ?? null;
            $AuthCode = $sel['AuthCode'] ?? null;
            if ($AuthCode == "") {
                // 檢查用戶名和密碼是否匹配
                if ($Account == $account && $Password == $hash_pwd) {
                    $secretKey = 'abcdefghijklmnopqrstuvwxyz'; //密鑰
                    $algorithm = 'HS256'; //加密的演算法
                    $login_time = time();
                    $expiration_Time = $login_time + 3600; //Token有效期限為1小時
                    $payload = [
                        'user' => $account,
                        'role' => $sel["RoleName"] ?? null,
                        'iat' => $login_time,
                        'exp' => $expiration_Time
                    ];

                    //生成Token
                    $signature = JWT::encode($payload, $secretKey, $algorithm);

                    // return生成的Token
                    return [
                        'token' => $signature
                    ];
                } else {
                    throw new \Exception('帳號或密碼不正確', 401);
                }
            } else {
                throw new \Exception('無法登入', 401);
            }
        }
    }

    public function getuser_by_token($userData)
    {

        // 建立資料庫連線
        $link = mysqli_connect("localhost", "root", "", "mooddiary");

        // 檢查資料庫連線是否成功
        if (!$link) {
            return ['message' => 'MySQL資料庫連接錯誤!'];
        }

        // 在資料庫中查找用戶
        $sql = mysqli_query($link, "SELECT * FROM `member` WHERE `Account` = '$userData->user'");
        $sel = mysqli_fetch_assoc($sql);

        return [
            // 'user' => $acc,
            "Account" => $userData->user,
            'Name' => $sel['Name'],
            'Email' => $sel['Email'],
            'Gender' => $sel['Gender'],
            'Role' => $userData->role,
            "Mug_shot" => $sel['Mug_shot'],
            "BG_Image" => $sel['BG_Image']
        ];
    }

    public function logout($acc)
    {
        // 啟動會話
        session_start();

        // 如果用戶已經登入，銷毀會話並導向到登入頁面
        if (isset($_SESSION['acc'])) {
            // 銷毀會話
            session_destroy();
            return;
        }
    }

    public function forget_password($account)
    {
        $link = mysqli_connect("localhost", "root", "", "mooddiary");

        // 檢查資料庫連線是否成功
        if (!$link) {
            return ['message' => 'MySQL資料庫連接錯誤!'];
        }
        if ($account == "") {
            throw new \Exception('請確認資訊完整性', 401);
        } else {
            $sql = mysqli_query($link, "SELECT *, CASE Role WHEN 1 THEN 'admin' WHEN 2 THEN 'user' END AS RoleName FROM member WHERE Account = '$account';");
            $select = mysqli_fetch_assoc($sql);

            $Account = $select['Account'] ?? null;
            $Email = $select['Email'] ?? null;
            if ($Account == $account) {

                $secretKey = 'abcdefghijklmnopqrstuvwxyz'; //密鑰
                $algorithm = 'HS256'; //加密的演算法
                $login_time = time();
                $expiration_Time = $login_time + 300; //Token有效期限為5分鐘
                $payload = [
                    'user' => $account,
                    'role' => $sel["RoleName"] ?? null,
                    'email' => $sel["Email"] ?? null,
                    'iat' => $login_time,
                    'exp' => $expiration_Time
                ];
                //生成Token
                $signature = JWT::encode($payload, $secretKey, $algorithm);

                $mail = new PHPMailer(true);
                try {
                    // 設置SMTP服務器設置
                    $mail->isSMTP();
                    $mail->Host = 'smtp.gmail.com';
                    $mail->SMTPAuth = true;
                    $mail->Username = 'vivianguo93@gmail.com'; // 你的Gmail地址
                    $mail->Password = 'xhpw hytj xesd ynhu'; // 你的Gmail密碼
                    $mail->SMTPSecure = 'tls';
                    $mail->Port = 587;

                    // 設置寄件人和收件人
                    $mail->setFrom('vivianguo93@gmail.com', 'Vivian Guo');
                    $mail->addAddress($Email);

                    // 設置郵件主題和內容
                    $mail->Subject = mb_encode_mimeheader('重設密碼');

                    $mail->Body = "<html>
                    <body>
                        <a href='http://localhost:5501/resetpwd.html?token=$signature'>去重設密碼～</a>    
                    </body>
                    </html>";
                    $mail->IsHTML(true);

                    // 發送郵件
                    $mail->send();
                    // return;
                    return [
                        'token' => $signature
                    ];
                } catch (Exception $e) {
                    throw new \Exception('發送重設密碼信失敗', 401);
                }
            } else {
                throw new \Exception('沒有此帳號', 401);
            }
        }
    }

    public function Register($name, $acc, $email, $pwd, $checkpwd)
    {
        $link = mysqli_connect("localhost", "root", "", "mooddiary");

        // 檢查資料庫連線是否成功  
        if (!$link) {
            return ['message' => 'MySQL資料庫連接錯誤!'];
        }

        $sql_select = "SELECT * FROM member WHERE Account = '$acc' and Email='$email'";
        $select = mysqli_query($link, $sql_select);
        $num = mysqli_num_rows($select);
        if ($name == "" || $acc == "" || $email == "" || $pwd == "" || $checkpwd == "") {
            throw new \Exception('請確認資訊完整性', 401);
            // return ['message' => '請確認資訊完整性!'];
        } else if ($num) {
            throw new \Exception('已存在使用者名稱', 401);
            // return ['message' => '已存在使用者名稱!'];
        } else {
            $hash_pwd = Hash_Pwd($pwd);

            $sql = "INSERT INTO member (Account, Name, Password, Email, Gender, Role, Mug_shot, BG_image) VALUES ('$acc', '$name', '$hash_pwd', '$email', '不願透漏', '2', '5m1AgeRLf3.jpg', '');";
            $result = mysqli_query($link, $sql);

            if ($result) {
                // 發送驗證郵件
                $verification_code = Random_Code();
                $sql_code = "UPDATE member SET AuthCode = '$verification_code' WHERE Account = '$acc';";
                $result_code = mysqli_query($link, $sql_code);
                

                $secretKey = 'abcdefghijklmnopqrstuvwxyz'; //密鑰
                $algorithm = 'HS256'; //加密的演算法
                $login_time = time();
                $expiration_Time = $login_time + 300; //Token有效期限為5分鐘
                $payload = [
                    'user' => $acc,
                    'role' => $sel["RoleName"] ?? null,
                    'email' => $sel["AuthCode"] ?? null,
                    'iat' => $login_time,
                    'exp' => $expiration_Time
                ];
                //生成Token
                $signature = JWT::encode($payload, $secretKey, $algorithm);
                // 初始化PHPMailer
                $mail = new PHPMailer(true);

                try {
                    // 設置SMTP服務器設置
                    $mail->isSMTP();
                    $mail->Host = 'smtp.gmail.com';
                    $mail->SMTPAuth = true;
                    $mail->Username = 'vivianguo93@gmail.com'; // 你的Gmail地址
                    $mail->Password = 'xhpw hytj xesd ynhu'; // 你的Gmail密碼
                    $mail->SMTPSecure = 'tls';
                    $mail->Port = 587;

                    // 設置寄件人和收件人
                    $mail->setFrom('vivianguo93@gmail.com', 'Vivian Guo');
                    $mail->addAddress($email);

                    // 設置郵件主題和內容
                    $mail->Subject = mb_encode_mimeheader('帳號驗證');
                    $mail->Body = '您的驗證碼為:' . $verification_code;

                    // 發送郵件
                    $mail->send();

                    return [
                        'token' => $signature
                    ];
                } catch (Exception $e) {
                    throw new \Exception('註冊成功，但發送驗證信失敗', 401);
                }
            } else {
                throw new \Exception('註冊不成功', 401);
            }
        }
    }

    public function AuthCode($token,$AuthCode)
    {
        $userData = JWT_decode($token);

        $link = mysqli_connect("localhost", "root", "", "mooddiary");

        // 檢查資料庫連線是否成功  
        if (!$link) {
            return ['message' => 'MySQL資料庫連接錯誤!'];
        }
        $sql = mysqli_query($link, "SELECT Account,AuthCode FROM `member` WHERE `Account` LIKE '$userData->user';");
        $sel = mysqli_fetch_assoc($sql);


        // 提取用戶名和密碼
        $AuthCode1 = $sel['AuthCode'] ?? null;

        // 檢查用戶名和密碼是否匹配
        if ($AuthCode == $AuthCode1) {
            $upd = mysqli_query($link, "UPDATE `member` SET `AuthCode` = '' WHERE Account = '$userData->user';");
            if (!$upd) {
                throw new \Exception('發生未知錯誤', 404);
            } else {
                return [
                    'msg' => "驗證成功"
                ];
            }
        } else {
            throw new \Exception('驗證失敗', 401);
            //return ['message' => '登入失敗'];
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

function Random_Code()
{

    $lens = 8;
    $random = "";
    //FOR回圈以$random為判斷執行次數
    for ($i = 1; $i <= $lens; $i = $i + 1) {
        //亂數$c設定三種亂數資料格式大寫、小寫、數字，隨機產生
        $c = rand(1, 3);
        //在$c==1的情況下，設定$a亂數取值為97-122之間，並用chr()將數值轉變為對應英文，儲存在$b
        if ($c == 1) {
            $a = rand(97, 122);
            $b = chr($a);
        }
        //在$c==2的情況下，設定$a亂數取值為65-90之間，並用chr()將數值轉變為對應英文，儲存在$b
        if ($c == 2) {
            $a = rand(65, 90);
            $b = chr($a);
        }
        //在$c==3的情況下，設定$b亂數取值為0-9之間的數字
        if ($c == 3) {
            $b = rand(0, 9);
        }
        //使用$randoma連接$b
        $random = $random . $b;
    }
    //輸出$randoma每次更新網頁你會發現，亂數重新產生了
    return $random;
}

function JWT_decode($token)
{
    $secretKey = 'abcdefghijklmnopqrstuvwxyz'; //密鑰
    $algorithm = 'HS256'; //加密的演算法
    $data = JWT::decode($token, new Key($secretKey, $algorithm));

    return $data;
}
