<?php
namespace Core\Shivalik\Managers;

use Core\Shivalik\Entities\Localisation;
use PHPBackend\Dao\DefaultDAOInterface;

/**
 *
 * @author Esaie MHS
 *        
 */
abstract class LocalisationDAOManager extends DefaultDAOInterface
{
    /**
     * {@inheritDoc}
     * @see \PHPBackend\Dao\DefaultDAOInterface::findById()
     * @return Localisation
     */
    public function findById($id, bool $forward = true)
    {
        /**
         * @var Localisation $localisation
         */
        $localisation = parent::findById($id, $forward);
        $localisation->setCountry($this->getDaoManager()->getManagerOf('Country')->findById($localisation->getCountry()->getId()));
        return $localisation;
    }
}

