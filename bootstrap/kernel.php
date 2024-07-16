<?php

    //Add global middleware
    $globalMiddleware = [

    ];

    /**
     * Initializes the main components and starts the program.
     * Sessions and Database connections are made from here
     */
    function bootstrap(){
        ob_start();
        //Initialize the Router
        Router::init();

        //Setup databse connections
        DB::connect();

        //Initialize the session
        session_start();

        //Listens to incoming requests
        Router::listen();
    }