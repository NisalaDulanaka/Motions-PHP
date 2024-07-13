<?php

    include_once('./traits/terminal.php');

    class DB{
        use Terminal;

        private static ?mysqli $conn = null ;
        private static $host;
        private static $dataBase;
        private static $userName;
        private static $password;
        
        public static function connect(){
            self::$host = getenv('HOST');
            self::$dataBase = getenv('DB');
            self::$userName = getenv('USER_NAME');
            self::$password = getenv('PASSWORD');

            if(empty(self::$host)) return; // If env is not set return

            //Establish databse connections
            self::$conn = new mysqli(self::$host,self::$userName,self::$password,self::$dataBase);

            if(self::$conn->connect_error){
                //echo('Error connecting to database ' . self::$conn->error);
                self::log('Error connecting to database ' . self::$conn->error);
                exit(0);
            }
        }

        public static function getConnection() : mysqli
        {
            return self::$conn;
        }

        public static function select($query,$type = MYSQLI_ASSOC) : array
        {
            $results = [];

            try{
                $results = mysqli_fetch_all(self::$conn->query($query),$type);
            }catch(Exception $e){
                //Do something
            }

            return $results;
        }

        /**
         *  Executes multiple queries at once and frees results sets
         *  Use for insert update and delete related multi queries
         *  @param $query - the multi query to be executed
         *  @return bool - returns true if the query was successful and vice versa
         */
        public static function multiQuery($query) : bool
        {
            if (self::$conn->multi_query($query)) {
                // Iterate through all result sets and free them
                do {
                    if ($r = self::$conn->store_result()) {
                        $r->free();
                    }
                } while (self::$conn->more_results() && self::$conn->next_result());

                return true;
            } else {
                // Handle error
                return false;
            }
        }

        /**
         *  Executes multiple queries at once and frees result sets
         *  Use for multi queries which produce result sets
         *  @param $query - the multi query to be executed
         *  @return array - returns an associative array representing the resultset
         */
        public static function multiSelect(String $query,int $type = 0) : array
        {
            $results = [];
            if (self::$conn->multi_query($query)) {
                // Iterate through all result sets and free them
                do {
                    if ($r = self::$conn->store_result()) {
                        if($type === 0){
                            $results = $r->fetch_all();
                        }
                        else{
                            $results = $r->fetch_all($type);
                        }
                        $r->free();
                    }
                } while (self::$conn->more_results() && self::$conn->next_result());
            }

            return $results;
        }
    }