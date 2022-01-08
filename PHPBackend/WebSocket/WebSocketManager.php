<?php
namespace PHPBackend\WebSocket;

use Ratchet\MessageComponentInterface;

/**
 *
 * @author Esaie MUHASA
 *        
 */
class WebSocketManager implements MessageComponentInterface
{
    /**
     * {@inheritDoc}
     * @see \Ratchet\ComponentInterface::onClose()
     */
    public function onClose(\Ratchet\ConnectionInterface $conn)
    {
        // TODO Auto-generated method stub
        
    }

    /**
     * {@inheritDoc}
     * @see \Ratchet\ComponentInterface::onError()
     */
    public function onError(\Ratchet\ConnectionInterface $conn, \Exception $e)
    {
        // TODO Auto-generated method stub
        
    }

    /**
     * {@inheritDoc}
     * @see \Ratchet\ComponentInterface::onOpen()
     */
    public function onOpen(\Ratchet\ConnectionInterface $conn)
    {
        // TODO Auto-generated method stub
        
    }

    /**
     * {@inheritDoc}
     * @see \Ratchet\MessageInterface::onMessage()
     */
    public function onMessage(\Ratchet\ConnectionInterface $from, $msg)
    {
        // TODO Auto-generated method stub
        
    }

    
}

