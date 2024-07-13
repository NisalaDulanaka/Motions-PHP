<?php

require_once('./http/Router.php');
require_once('./http/config/environment.php');

//This is the file where you should set up all your routes

//Setup Routes
Router::GET('/',[SampleController::class,'index']);

Router::POST('/add', [SampleController::class, 'insert']);

Router::PUT('/update', [SampleController::class, 'update']);

Router::DELETE('/delete', [SampleController::class, 'delete']);