<?php
namespace Managers\Implementation;

use Managers\CountryDAOManager;
use Entities\Country;

/**
 *
 * @author Esaie MHS
 *        
 */
class CountryDAOManagerImplementation1 extends CountryDAOManager
{
    /**
     * {@inheritDoc}
     * @see \Library\AbstractDAOManager::create()
     * @param Country $entity
     */
    public function create($entity)
    {
        $this->pdo_insertInTable($this->getTableName(), array(
            'name' => $entity->getName(),
            'abbreviation' => $entity->getAbbreviation()
        ));
    }

    /**
     * {@inheritDoc}
     * @see \Library\AbstractDAOManager::update()
     */
    public function update($entity, $id)
    {
        $this->pdo_updateInTable($this->getTableName(), array(
            'name' => $entity->getName(),
            'abbreviation' => $entity->getAbbreviation()
        ), $id);
    }

}

