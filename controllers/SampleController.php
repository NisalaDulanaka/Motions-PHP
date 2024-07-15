<?php
    require('./traits/models/SampleModel.php');

    class SampleController extends Controller{

        use SampleModel;

        //HTTP GET
        public function index()
        {
            // Sends the response
            return $this->view("./views/pages/test.php");
        }

        public function hello(){
            return [
                "message" => "Hello"
            ];
        }

        //HTTP POST
        public function insert()
        {
            // Sends the response
            echo json_encode([
                "message" => "Data inserted"
            ]);
        }

        //HTTP PUT
        public function update()
        {
            // Sends the response
            echo json_encode([
                "message" => "Data updated"
            ]);
        }

        //HTTP DELETE
        public function delete()
        {
            // Sends the response
            echo json_encode([
                "message" => "Data deleted"
            ]);
        }
    }