<?php
namespace Managers\Implementation;

use Managers\SizeDAOManager;
use Entities\Size;

/**
 *
 * @author Esaie MHS
 *        
 */
class SizeDAOManagerImplementation1 extends SizeDAOManager
{
    /**
     * {@inheritDoc}
     * @see \Library\AbstractDAOManager::create()
     * @param Size $entity
     */
    public function create($entity)
    {
        $id = $this->pdo_insertInTable($this->getTableName(), array(
            'name' => $entity->getName(),
            'abbreviation' => $entity->getAbbreviation(),
            'percentage' => $entity->getPercentage()
        ));
        $entity->setId($id);
    }

    /**
     * {@inheritDoc}
     * @see \Library\AbstractDAOManager::update()
     * @param Size $entity
     */
    public function update($entity, $id)
    {
        $this->pdo_updateInTable($this->getTableName(), array(
            'name' => $entity->getName(),
            'abbreviation' => $entity->getAbbreviation(),
            'percentage' => $entity->getPercentage()
        ), $id);
    }

}

