<?php
    //This is the original index file of this project
    require_once __DIR__ . '/vendor/autoload.php';
    require('./http/Router.php');
    require('./controllers/Controller.php');
    require_once('./http/config/errorHandler.php');
    require_once('./database/DB.php');
    include_once('./controllers/SampleController.php');
    require_once('./bootstrap/kernel.php');
    

    //Start the program
    bootstrap();
