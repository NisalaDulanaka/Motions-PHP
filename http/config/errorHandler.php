<?php

    //Print the erro message in a nice format
    function printErrorMessage(string $message){
        $errorMessage = $message;

        include("./views/error_page.php");
    }

    function errorToException($errno, $errstr, $errfile, $errline) {
        if (!(error_reporting() & $errno)) {
            // This error code is not included in error_reporting
            return false;
        }
        throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
    }

    function exceptionHandler($exception){
        printErrorMessage($exception);
    }

    set_error_handler("errorToException");
    set_exception_handler("exceptionHandler");