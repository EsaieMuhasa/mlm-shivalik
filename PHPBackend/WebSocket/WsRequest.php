<?php
namespace PHPBackend\WebSocket;

use PHPBackend\Request;
use PHPBackend\File\UploadedFile;
use PHPBackend\Session;
use PHPBackend\Application;

/**
 *
 * @author Esaie MUHASA
 *        
 */
class WsRequest implements Request
{
    /**
     * {@inheritDoc}
     * @see \PHPBackend\Request::addAttribute()
     */
    public function addAttribute(string $name, $value): void
    {
        // TODO Auto-generated method stub
        
    }

    /**
     * {@inheritDoc}
     * @see \PHPBackend\Request::addDataPOST()
     */
    public function addDataPOST(string $name, $data): Request
    {
        // TODO Auto-generated method stub
        
    }

    /**
     * {@inheritDoc}
     * @see \PHPBackend\Request::attributeExist()
     */
    public function attributeExist(string $name): bool
    {
        // TODO Auto-generated method stub
        
    }

    /**
     * {@inheritDoc}
     * @see \PHPBackend\Request::existInFILES()
     */
    public function existInFILES(string $name): bool
    {
        // TODO Auto-generated method stub
        
    }

    /**
     * {@inheritDoc}
     * @see \PHPBackend\Request::existInGET()
     */
    public function existInGET(string $name): bool
    {
        // TODO Auto-generated method stub
        
    }

    /**
     * {@inheritDoc}
     * @see \PHPBackend\Request::existInPOST()
     */
    public function existInPOST(string $name): bool
    {
        // TODO Auto-generated method stub
        
    }

    /**
     * {@inheritDoc}
     * @see \PHPBackend\Request::forward()
     */
    public function forward(string $action, string $module = null, string $applicationName = null): void
    {
        // TODO Auto-generated method stub
        
    }

    /**
     * {@inheritDoc}
     * @see \PHPBackend\Request::getAttribute()
     */
    public function getAttribute(string $name)
    {
        // TODO Auto-generated method stub
        
    }

    /**
     * {@inheritDoc}
     * @see \PHPBackend\Request::getAttributes()
     */
    public function getAttributes(): array
    {
        // TODO Auto-generated method stub
        
    }

    /**
     * {@inheritDoc}
     * @see \PHPBackend\Request::getDataFILES()
     */
    public function getDataFILES(string $name): array
    {
        // TODO Auto-generated method stub
        
    }

    /**
     * {@inheritDoc}
     * @see \PHPBackend\Request::getDataGET()
     */
    public function getDataGET(string $name)
    {
        // TODO Auto-generated method stub
        
    }

    /**
     * {@inheritDoc}
     * @see \PHPBackend\Request::getDataPOST()
     */
    public function getDataPOST(string $name)
    {
        // TODO Auto-generated method stub
        
    }

    /**
     * {@inheritDoc}
     * @see \PHPBackend\Request::getExtensionURI()
     */
    public function getExtensionURI(): string
    {
        // TODO Auto-generated method stub
        
    }

    /**
     * {@inheritDoc}
     * @see \PHPBackend\Request::getMethod()
     */
    public function getMethod(): string
    {
        // TODO Auto-generated method stub
        
    }

    /**
     * {@inheritDoc}
     * @see \PHPBackend\Request::getSession()
     */
    public function getSession(): Session
    {
        // TODO Auto-generated method stub
        
    }

    /**
     * {@inheritDoc}
     * @see \PHPBackend\Request::getUploadedFile()
     */
    public function getUploadedFile(string $name): UploadedFile
    {
        // TODO Auto-generated method stub
        
    }

    /**
     * {@inheritDoc}
     * @see \PHPBackend\Request::getURI()
     */
    public function getURI(): string
    {
        // TODO Auto-generated method stub
        
    }

    /**
     * {@inheritDoc}
     * @see \PHPBackend\Request::removeAttribute()
     */
    public function removeAttribute(string $name): void
    {
        // TODO Auto-generated method stub
        
    }
    
    /**
     * {@inheritDoc}
     * @see \PHPBackend\Request::addToast()
     */
    public function addToast(\PHPBackend\ToastMessage $toast): void
    {
        // TODO Auto-generated method stub
        
    }

    /**
     * {@inheritDoc}
     * @see \PHPBackend\ApplicationComponent::getApplication()
     */
    public function getApplication(): Application
    {
        // TODO Auto-generated method stub
        
    }


    
}

