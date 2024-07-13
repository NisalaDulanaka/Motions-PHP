<?php

    require_once('./http/BaseRoute.php');

    class Middleware extends BaseRoute{

        public function __construct(string $routePath){
            parent::__construct($routePath);
            $this->parseRoute();
        }

        public function parseRoute() : void
        {

            $this->pattern = preg_replace('/\//', '\\/', $this->path);
	
            preg_match_all("/({[^}.]+})/", $this->pattern, $matches);
            $routeParas = [];
            $routeInputs = [];
            
            foreach($matches[0] as $match){
            array_push($routeParas, '/' . $match . '/');
            }
            
            foreach($routeParas as $routePara){
            
                $inputPara = preg_replace(
                    [ '/{/', '/\//', '/}/' ],
                    [ '(?<', '', '>[^\/]+)' ],
                    $routePara
                );
                array_push($routeInputs,$inputPara);
            
            }
            
            $this->pattern = "/" . preg_replace($routeParas,$routeInputs,$this->pattern) . "/";
        }

        public function matchRoute(Request $request): bool
        {
            preg_match_all($this->pattern, $request->path, $matches);

            if(count($matches[0]) < 1){
                return false;
            }
            
            foreach($matches as $key => $value){
            
                if(is_string($key) && !empty($value))
                    $request->routeParameters[$key] = $value[0];
            }

            return true;
        }
    }