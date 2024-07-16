<?php
    require_once('./traits/terminal.php');

    class Controller{
        use Terminal;

        /**
         * Generates a view (UI)
         * @param string $path the path of the view file. This can be relative to the views folder or the project root
         * @param array $data Data to be passed to the view for templating
         * @param bool $manualPath Indicates whether the path is relative to the views folder or the project root
         */
        public function view(string $path,array $data = [],bool $manualPath = false) : void
        {

            foreach ($data as $key => $value) {
                ${$key} = $value;
            }

            if(! $manualPath) $path = "./views/$path";

            require($path);
        }
    }