<?php

    /**
     * The Request class provides easy to use methods and attributes, which can be used to 
     * interact with the requests made to server with ease
     */
    class Request{

        /**
         * Stores the http request method of the request
         */
        public string $method;
        /**
         * Stores the full url path of the request
         */
        public $url;
        /**
         * Stores the path which the request was made for
         */
        public string $path;
        /**
         * Stores get request query string
         */
        private array $query;
        /**
         * Stores all the request body data
         */
        private array $body;
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
        private function createRequestBody() : void
        {

            if($this->method === 'POST' && (isset($_SERVER['HTTP_CONTENT_TYPE']) && $_SERVER['HTTP_CONTENT_TYPE'] !== 'application/json')){
                $this->body = $_POST;
            }
            else{
                $this->body = self::getResponseParams();
            }
        }

        /**
         * @return array The body of the current request
         */
        public function getRequestBody() : array
        {
            return $this->body;
        }

        /**
         * Provides access to the query parameters
         * @param string $key The name of the parameter
         * @param null | int | string $defaultValue The value to be used if the parameter is missing or contains empty values
         * @return null | int | string If a specifc key is provided
         * @return array The whole query parameter array if a specifc key is not provided
         */
        public function query(string $key, null | int | string $defaultValue) : null | int | string | array
        {
            if( $key === null || empty($key))
                return $this->query;

            if( !isset($this->query[$key]) || empty($this->query[$key]))
                return $defaultValue;

            return $this->query[$key];
        }

        /**
         * Checks if the parameter is present in the request body
         * @param string $param The name of the parameter
         * @return bool Indicating whether the parameter is present and is not null
         */
        public function filled(string $param) : bool
        {
            return (isset($this->query[$param]) && !empty($this->query[$param]));
        }

        /**
         * Returns all the request parameters included in the json object of the request body as an associative array
         * @return array The data in the json structure
         */
        private static function getResponseParams() : array
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

        /**
         * Redirects the request to the specifed location
         * @param string $location The location to be redirected to
         */
        public function redirect(string $location) : void
        {
            header("Location: $location");
            exit(0);
        }
    }