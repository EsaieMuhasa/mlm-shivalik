<?php
namespace PHPBackend\WebSocket;

use PHPBackend\Application;
use PHPBackend\Response;
use PHPBackend\Request;
use PHPBackend\Controller;
use PHPBackend\AppConfig;

/**
 *
 * @author Esaie MUHASA
 *        
 */
class WebSocketApplication implements Application
{
    
    private $config;
    
    
    private $name;
    
    
    
    /**
     * {@inheritDoc}
     * @see \PHPBackend\Application::getConfig()
     */
    public function getConfig(): AppConfig
    {
        // TODO Auto-generated method stub
        
    }

    /**
     * {@inheritDoc}
     * @see \PHPBackend\Application::getContainer()
     */
    public function getContainer(): string
    {
        // TODO Auto-generated method stub
        
    }

    /**
     * {@inheritDoc}
     * @see \PHPBackend\Application::getController()
     */
    public function getController(): Controller
    {
        // TODO Auto-generated method stub
        
    }

    /**
     * {@inheritDoc}
     * @see \PHPBackend\Application::getName()
     */
    public function getName(): string
    {
        // TODO Auto-generated method stub
        
    }

    /**
     * {@inheritDoc}
     * @see \PHPBackend\Application::getRequest()
     */
    public function getRequest(): Request
    {
        // TODO Auto-generated method stub
        
    }

    /**
     * {@inheritDoc}
     * @see \PHPBackend\Application::getResponse()
     */
    public function getResponse(): Response
    {
        // TODO Auto-generated method stub
        
    }

    /**
     * {@inheritDoc}
     * @see \PHPBackend\Application::logger()
     */
    public function logger(\PHPBackend\PHPBackendException $exception): void
    {
        // TODO Auto-generated method stub
        
    }

    /**
     * {@inheritDoc}
     * @see \PHPBackend\Application::run()
     */
    public function run(): void
    {
        // TODO Auto-generated method stub
        
    }

    
}

