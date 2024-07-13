<?php

    require_once('./http/Request.php');
    require_once('./http/Route.php');
    require_once('./http/Middleware.php');

    use Opis\Closure\SerializableClosure;

    class Router
    {
        private static SplObjectStorage $post; //Keeps track of all post routes

        private static SplObjectStorage $get; //Keeps track of all get routes

        private static SplObjectStorage $put; //Keeps track of all put routes

        private static SplObjectStorage $delete; //Keeps track of all delete routes

        private static SplObjectStorage $middleWare; //Keeps track of all middleware

        private static $errors = []; //Keeps track of error handler functions

        /**
         * Adds a new POST route to app
         */
        public static function POST($route, $callback){
            $route = new Route($route);
            self::$post[$route] = self::storeCallback($callback);
        }

        public static function GET($route, $callback){
            $route = new Route($route);
            self::$get[$route] = self::storeCallback($callback);
        }

        public static function PUT($route, $callback){
            $route = new Route($route);
            self::$put[$route] = self::storeCallback($callback);
        }

        public static function DELETE($route, $callback){
            $route = new Route($route);
            self::$delete[$route] = self::storeCallback($callback);
        }

        public static function MIDDLEWARE($route, $callback){
            if(empty($route)) $route = '/';
            $route = new Middleware($route);
            self::$middleWare[$route] = self::storeCallback($callback);
        }

        public static function notFound(){
            header("HTTP/1.1 404 Not Found");
            if(file_exists('./views/errors/404.php')){
                require('./views/errors/404.php');
                return;
            }

            echo "Oops Page Not Found";
        }

        /**
         * Starts the router
         * listens to all incoming requests and calls the relevant handler function
         */
        public static function listen(){
            $request = new Request();

            $used_middleware = 'none';

            foreach (self::$middleWare as $key => $value) {
                if($value->matchRoute($request)){
                    $used_middleware = self::handleCallback(self::$middleWare[$value])($request);
                    if($used_middleware !== 'NEXT'){
                        return;
                    }
                }
            }

            if( $request->method === 'POST'){
                self::validateRoute($request,self::$post)($request);
                return;
            }

            if( $request->method === 'PUT'){
                self::validateRoute($request,self::$put)($request);
                return;
            }

            if ($request->method === 'DELETE'){
                self::validateRoute($request,self::$delete)($request);
                return;
            }

            if( $request->method === 'GET'){
                self::validateRoute($request, self::$get)($request);
                return;
            }

            self::notFound();
            return;
        }

        /**
         * Used in middleware handlers
         * Indicates that the router should match the next route after executing the middleware handler
         */
        public static function GO_TO_NEXT_ROUTE(){
            return 'NEXT';
        }

        /**
         * Handles route not found exception
         * If the route was not found sends a 404 error status and executes the 404 handler
         */
        private static function validateRoute($path, $requestArray){
            
            foreach ($requestArray as $key => $value) {
                if($value->matchRoute($path)){
                    return self::handleCallback($requestArray[$value]);
                }
            }

            self::notFound();
            exit(0);
        }

        /**
         * Returns the danler function based on its' type
         * Returns: if $callback if a Controller function a new object of controller
         * else the $callback itself
         */
        private static function handleCallback($callback){

            //Check whether the callback is an anonymous function or a controller function
            
            if(gettype($callback) === 'array' ){

                return function($param) use($callback){
                    $object = new $callback[0]();
                    $method = $callback[1];
                    return $object->$method($param);
                };
            }

            return $callback->getClosure();
        }

        /**
         * Returns : A serializable callback object
         * Stores the callback object in a serializable way
         * Was added due to php not supporting serialization of closures directly
         */
        private static function storeCallback($callback){

            if(gettype($callback) === 'array'){
                return $callback;
            }

            return new SerializableClosure($callback);
        }

        /**
         *  Initializes all variables
         *  Put all configuration codes for this class in this method
         */
        public static function init(){

            /* if (file_exists('./routes/cached_routes.php')) {
                $routes = unserialize(file_get_contents('./routes/cached_routes.php'));
                //var_dump($routes);

                self::$get = $routes['get'];
                self::$post = $routes['post'];
                self::$put = $routes['put'];
                self::$delete = $routes['delete'];
                self::$middleWare = $routes['middleware'];

            } else { */
                // Load and define routes
                self::$post = new SplObjectStorage();
                self::$get = new SplObjectStorage();
                self::$put = new SplObjectStorage();
                self::$delete = new SplObjectStorage();
                self::$middleWare = new SplObjectStorage();
                
                include('./routes/routes.php');

                /* $routes = [
                    'get' => self::$get, 'post' => self::$post,
                    'put' => self::$put, 'delete' => self::$delete,
                    'middleware' => self::$middleWare
                ];

                // Cache the routes for future requests
                file_put_contents('./routes/cached_routes.php',serialize($routes)); */
            //}
            
        }

    }
    