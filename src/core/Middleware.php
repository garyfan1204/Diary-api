<?php

namespace Vivian\DiaryApi\core;
abstract class Middleware{
    public abstract function handle(Request $request,Response $response);
}