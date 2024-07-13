<?php
    require('./traits/models/SampleModel.php');

    class SampleController extends Controller{

        use SampleModel;

        //HTTP GET
        public function index()
        {
            // Sends the response
            $this->generateView("./views/pages/test.php");
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