<?php
namespace Applications\Index;

use Library\Application;

/**
 *
 * @author Esaie MHS
 *        
 */
class IndexApplication extends Application
{
    /**
     * {@inheritDoc}
     * @see \Library\Application::__construct()
     */
    public function __construct()
    {
        parent::__construct();
        $this->name = 'Index';
    }

}

