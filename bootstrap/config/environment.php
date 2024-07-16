<?php

    try{
        $env = parse_ini_file('.env');

        foreach ($env as $key => $value) {
            putenv($key."=".$value);
        }

    }catch(ErrorException $e){
        printErrorMessage("Error: .env file not found. <br><br>
        Possible solutions:<br>
        <li> Create a .env file in the root folder.</li>
        <br>");
    }

?>