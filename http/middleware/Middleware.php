<?php

abstract class Middleware{

    /**
     * Handles incoming requests
     */
    abstract public function handleIncoming(Request $request);

    /**
     * Handles outgoing requests
     */
    abstract public function handleOutgoing(Request $request);
}