<?php
namespace PHPBackend\WebSocket;

/**
 *
 * @author Esaie MUHASA
 *        
 */
interface WebSocketListener
{
    
    /**
     * lors de la reception d'un message
     * @param WsRequest $request
     */
    public function onRequest (WsRequest $request) : void ;

}

