<?php

    require_once('./http/Request.php');
    require_once('./http/Route.php');
    require_once('./http/middleware/Middleware.php');

    use Opis\Closure\SerializableClosure;

    class Router
    {
        private static SplObjectStorage $post; //Keeps track of all post routes

        private static SplObjectStorage $get; //Keeps track of all get routes

        private static SplObjectStorage $put; //Keeps track of all put routes

        private static SplObjectStorage $delete; //Keeps track of all delete routes

        private static array $middleWare = []; //Keeps track of all middleware

        private static $errors = []; //Keeps track of error handler functions

        /**
         * Adds a new POST route to app
         */
        public static function POST($route, $callback) : Route
        {
            $route = new Route($route);
            self::$post[$route] = self::storeCallback($callback);

            return $route;
        }

        public static function GET($route, $callback) : Route
        {
            $route = new Route($route);
            self::$get[$route] = self::storeCallback($callback);

            return $route;
        }

        public static function PUT($route, $callback) : Route
        {
            $route = new Route($route);
            self::$put[$route] = self::storeCallback($callback);

            return $route;
        }

        public static function DELETE($route, $callback) : Route
        {
            $route = new Route($route);
            self::$delete[$route] = self::storeCallback($callback);

            return $route;
        }

        /**
         * Checks if an instance of the middleware exists and creates one if not.
         * @param middlewareName - name of the middleware file
         * @param className - name of the middleware class (Needed only if the file name and class name is different)
         */
        public static function MIDDLEWARE(string $middleWareName,string $className = null) : void
        {
            $className = ($className == null)? $middleWareName : $className;

            require_once("./http/middleware/$middleWareName.php");

            if(! class_exists($className)){
                throw new Exception("Class $className does not exist");
            }

            foreach (self::$middleWare as $mwre) {
                # code...
                if(is_a($mwre,$className)){
                    return;
                }
            }

            self::$middleWare[$className] = new $className();
        }

        /**
         * Handles 404 status
         */
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

            if( $request->method === 'POST'){
                self::executeRoute($request,self::$post);
                return;
            }

            if( $request->method === 'PUT'){
                self::executeRoute($request,self::$put);
                return;
            }

            if ($request->method === 'DELETE'){
                self::executeRoute($request,self::$delete);
                return;
            }

            if( $request->method === 'GET'){
                self::executeRoute($request, self::$get);
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
        private static function executeRoute(Request $request, $requestArray) : void
        {
            $callback = null;
            $route = null;

            foreach ($requestArray as $key => $value) {
                if($value->matchRoute($request)){
                    $callback = self::handleCallback($requestArray[$value]);
                    $route = $value;
                }
            }

            if($callback == null) {
                self::notFound();
                exit(0); // Exit the program if the endpoint is not found
            }

            $middleWareResult = $route->executeMiddleware(function ($middlewareClasses) use($request) {
                $result = NEXT_ROUTE;

                if(count($middlewareClasses) > 0){
                    $result = 0;

                    foreach ($middlewareClasses as $middlewareClass) {
                        # code...
                        if(array_key_exists($middlewareClass, self::$middleWare)){
                            $result = self::$middleWare[$middlewareClass]->handleIncoming($request);
                            if($result !== NEXT_ROUTE) return $result;
                        }
                    }
                }

                return $result;
            });

            if($middleWareResult !== NEXT_ROUTE) return;

            $callback($request);
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
            define("NEXT_ROUTE",25); // Used to indicate middleware should move to the next endpoint

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
    