<?php

namespace Vivian\DiaryApi;

use Vivian\DiaryApi\controller\AdminController;
use Vivian\DiaryApi\controller\DemoController;  //因為要引用別人目錄的程式，用use
use Vivian\DiaryApi\controller\MemberController;
use Vivian\DiaryApi\controller\DiaryController;
use Vivian\DiaryApi\controller\SentenceController;
use Vivian\DiaryApi\controller\UpdateController;
use Vivian\DiaryApi\controller\FormController;
use Vivian\DiaryApi\controller\SearchController;
use Vivian\DiaryApi\core\Application;

require "../vendor/autoload.php";

ini_set('display_errors', '0');
ini_set('display_startup_errors', '0');

// if ($_SERVER['REQUEST_METHOD']) {
    header("Access-Control-Allow-Origin: * ");
    // 允許的其他跨源資源共享（CORS）標頭，例如：允許的方法、標頭和時間
    header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
    header("Access-Control-Allow-Headers: Content-Type, Authorization");

    header("Access-Control-Max-Age: 3600"); // 一小時的時間
    // 設置返回的資料類型為 JSON
    header("Content-Type: application/json; charset=UTF-8");
    // header("Content-Type: multipart/form-data; charset=UTF-8");
    
    if($_SERVER['REQUEST_METHOD']=='OPTIONS'){
        exit(0);
    }
    
// }



$app = new Application();

// $app->router->get('/api/demo', [DemoController::class, 'index']);

// $app->router->post('/api/demo', [DemoController::class, 'create']);

$app->router->post('/api/login', [MemberController::class, 'login']);

$app->router->post('/api/logout', [MemberController::class, 'logout']);

$app->router->post('/api/forget_password', [MemberController::class, 'forget_password']);

$app->router->post('/api/reset_password', [UpdateController::class, 'reset_password']);

$app->router->post('/api/register', [MemberController::class, 'register']);

$app->router->post('/api/AuthCode', [MemberController::class, 'AuthCode']);

$app->router->post('/api/background',[MemberController::class,'background']);

$app->router->post('/api/write_diary', [DiaryController::class, 'write_diary']);

$app->router->get('/api/show_diary_all', [DiaryController::class, 'show_diary_all']);

$app->router->get('/api/show_diary', [DiaryController::class, 'show_diary']);

$app->router->get('/api/show_music_all', [DiaryController::class, 'show_music_all']);

$app->router->get('/api/getuser_by_token', [MemberController::class, 'getuser_by_token']);

$app->router->get('/api/sentence', [SentenceController::class, 'sentence']);

$app->router->post('/api/change_password', [UpdateController::class, 'change_password']);

$app->router->post('/api/change_sex', [UpdateController::class, 'change_sex']);

$app->router->post('/api/change_shot', [UpdateController::class, 'change_shot']);

$app->router->post('/api/change_BG_img', [UpdateController::class, 'change_BG_img']);

$app->router->post('/api/music_add', [AdminController::class, 'music_add']);

$app->router->post('/api/emoji_add', [AdminController::class, 'emoji_add']);

$app->router->post('/api/weather_add', [AdminController::class, 'weather_add']);

$app->router->post('/api/sentence_add', [AdminController::class, 'sentence_add']);

$app->router->post('/api/user_quantity', [AdminController::class, 'user_quantity']);

$app->router->post('/api/sentence_quantity', [AdminController::class, 'sentence_quantity']);

$app->router->get('/api/admin_show_user_all', [AdminController::class, 'admin_show_user_all']);

$app->router->get('/api/admin_show_weather_all', [AdminController::class, 'admin_show_weather_all']);

$app->router->get('/api/admin_show_emoji_all', [AdminController::class, 'admin_show_emoji_all']);

$app->router->get('/api/admin_show_sentence_all', [AdminController::class, 'admin_show_sentence_all']);

$app->router->get('/api/admin_show_music_all', [AdminController::class, 'admin_show_music_all']);

$app->router->get('/api/form_music', [FormController::class, 'form_music']);

$app->router->get('/api/form_emoji', [FormController::class, 'form_emoji']);

$app->router->get('/api/form_sex', [FormController::class, 'form_sex']);

$app->router->get('/api/form_Sentence', [FormController::class, 'form_Sentence']);

$app->router->get('/api/Search_music_type', [SearchController::class, 'Search_music_type']);

$app->router->get('/api/Search_sentence_type', [SearchController::class, 'Search_sentence_type']);

$app->router->get('/api/Search_user_sex', [SearchController::class, 'Search_user_sex']);

$app->router->get('/api/Search_music', [SearchController::class, 'Search_music']);

$app->router->get('/api/Search_sentence', [SearchController::class, 'Search_sentence']);

$app->router->get('/api/Search_emoji', [SearchController::class, 'Search_emoji']);

$app->router->get('/api/Search_weather', [SearchController::class, 'Search_weather']);

$app->router->get('/api/Search_user', [SearchController::class, 'Search_user']);

$app->router->delete('/api/user_del', [AdminController::class, 'user_del']);

$app->router->delete('/api/sentence_del', [AdminController::class, 'sentence_del']);

$app->router->delete('/api/music_del', [AdminController::class, 'music_del']);

$app->router->delete('/api/emoji_del', [AdminController::class, 'emoji_del']);

$app->router->delete('/api/weather_del', [AdminController::class, 'weather_del']);

$app->router->put('/api/change_sentence', [UpdateController::class, 'change_sentence']);

$app->router->put('/api/change_music', [UpdateController::class, 'change_music']);

$app->router->post('/api/change_emoji', [UpdateController::class, 'change_emoji']);

$app->router->post('/api/change_weather', [UpdateController::class, 'change_weather']);

$app->router->put('/api/change_user', [UpdateController::class, 'change_user']);

$app->router->put('/api/change_diary', [UpdateController::class, 'change_diary']);

$app->router->post('/api/Browse', [AdminController::class, 'Browse']);

// $app->router->get('/api/pagination', [MemberController::class, 'pagination']);

$app->run();
