<?php
namespace Managers\Implementation;

use Managers\GenerationDAOManager;
use Entities\Generation;
use Library\DAOException;

/**
 *
 * @author Esaie MHS
 *        
 */
class GenerationDAOManagerImplementation1 extends GenerationDAOManager
{
    /**
     * {@inheritDoc}
     * @see \Library\AbstractDAOManager::create()
     * @param Generation $entity
     */
    public function create($entity)
    {
        $this->pdo_insertInTable($this->getTableName(), array(
            'name' => $entity->getName(),
            'abbreviation' => $entity->getAbbreviation(),
            'number' => $entity->getNumber(),
            'percentage' => $entity->getPercentage()
        ));
    }

    /**
     * {@inheritDoc}
     * @see \Library\AbstractDAOManager::getAll()
     */
    public function getAll($limit = -1, $offset = -1)
    {
        $all = [];
        try {
            $statement = $this->pdo->prepare("SELECT * FROM {$this->getTableName()} ORDER BY number");
            if ($statement->execute()) {
                if ($row = $statement->fetch()) {
                    $all[] = new Generation($row);
                    while ($row = $statement->fetch()) {
                        $all[] = new Generation($row);
                    }
                }else {
                    throw new DAOException("No result resturn by selection query");
                }
            }else {
                throw new DAOException("failed to execute request");
            }
        } catch (\PDOException $e) {
            throw new DAOException($e->getMessage(), DAOException::ERROR_CODE, $e);
        }
        return $all;
    }

    /**
     * {@inheritDoc}
     * @see \Library\AbstractDAOManager::update()
     */
    public function update($entity, $id)
    {
        $this->pdo_updateInTable($this->getTableName(), array(
            'name' => $entity->getName(),
            'abbreviation' => $entity->getAbbreviation(),
            'number' => $entity->getNumber(),
            'percentage' => $entity->getPercentage()
        ), $id);
    }


    
}

