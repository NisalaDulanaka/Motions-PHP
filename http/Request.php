<?php

    class Request{

        public $method; // Stores the http request method of the request
        public $url; // Stores the full url pathe of the request
        public $path; // Stores the path which the request was made for
        private $query; // Stores get request query string
        private $body; // Stores all the request body data
        public $routeParameters = [];

        public function __construct(){
            //Set request variables
            $this->method = $_SERVER['REQUEST_METHOD'];
            $this->url = $_SERVER['REQUEST_URI'];
            $request_uri = parse_url($this->url);
            $this->path = $request_uri['path'];
            $this->query = $_REQUEST;

            //Created the request body
            $this->createRequestBody();
        }

        /**
         * Assings the request body data based on the request type
         */
        private function createRequestBody(){

            if($this->method === 'POST' && (isset($_SERVER['HTTP_CONTENT_TYPE']) && $_SERVER['HTTP_CONTENT_TYPE'] !== 'application/json')){
                $this->body = $_POST;
            }
            else{
                $this->body = self::getResponseParams();
            }
        }

        public function getRequestBody() : array
        {
            return $this->body;
        }
        /**
         * Provides access to the query parameters
         */
        public function query($key, $defaultValue) : null | int | string | array
        {
            if( $key === null || empty($key))
                return $this->query;

            if( !isset($this->query[$key]) || empty($this->query[$key]))
                return $defaultValue;

            return $this->query[$key];
        }

        /**
         * Checks if the parameter is present in the request body
         * Returns: TRUE if parameter is present and is not null, else false
         */
        public function filled($param) : bool
        {
            return (isset($this->query[$param]) && !empty($this->query[$param]));
        }

        /**
         * Returns all the request parameters as an associative array
         */
        public static function getResponseParams() : array
        {
            $data = [];
    
            try{
    
                if((isset($_SERVER['HTTP_CONTENT_TYPE']) && $_SERVER['HTTP_CONTENT_TYPE'] === 'application/json')){
                    $conent = file_get_contents('php://input');
                    $data = json_decode($conent, true);
                }
    
            }catch(Exception $ex){
                //Handle exceptions
                echo("Error in json parsing". $ex->getMessage());
            }
    
            return ($data === null)? []:$data;
        }

        public function redirect($location){
            header("Location: $location");
            exit(0);
        }
    }