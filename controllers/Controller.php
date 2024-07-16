<?php
    require_once('./traits/terminal.php');

    class Controller{
        use Terminal;

        public function view($path,array $data = []){

            foreach ($data as $key => $value) {
                ${$key} = $value;
            }

            require($path);
        }
    }