<?php

class TestMiddleWare extends Middleware{

    public function handleIncoming(Request $request){
        // Put your incoming logic here
        echo "OI huighie homelander done kill me wife and took me bloody son! Womp Womp!<br><br>";

        return NEXT_ROUTE; // This will indicate the request to move to the next middleware or endpoint
    }

    public function handleOutgoing(Request $request){
        // Put your outgoing logic here
    }
}