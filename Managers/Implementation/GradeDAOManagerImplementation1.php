<?php
namespace Managers\Implementation;

use Managers\GradeDAOManager;
use Entities\Grade;
use Library\DAOException;
use Managers\GenerationDAOManager;

/**
 *
 * @author Esaie MHS
 *        
 */
class GradeDAOManagerImplementation1 extends GradeDAOManager
{
    
    /**
     * @var GenerationDAOManager
     */
    protected $generationDAOManager;
    
    /**
     * {@inheritDoc}
     * @see \Library\AbstractDAOManager::create()
     * @param Grade $entity
     */
    public function create($entity)
    {
        $id = $this->pdo_insertInTable($this->getTableName(), array(
            'name' => $entity->getName(),
            'icon' => $entity->getIcon(),
            'maxGeneration' => $entity->getMaxGeneration()->getId(),
            'percentage' => $entity->getPercentage(),
            'amount' => $entity->getAmount()
        ));
        $entity->setId($id);
    }

    /**
     * {@inheritDoc}
     * @see \Library\AbstractDAOManager::update()
     */
    public function update($entity, $id)
    {
        
        $data = array(
            'name' => $entity->getName(),
            'maxGeneration' => $entity->getMaxGeneration()->getId(),
            'percentage' => $entity->getPercentage(),
            'amount' => $entity->getAmount()
        );
        if ($entity->getIcon() != null ) {
            $data['icon'] = $entity->getIcon();
        }
        $this->pdo_updateInTable($this->getTableName(), $data, $id);
    }
    
    /**
     * {@inheritDoc}
     * @see \Library\AbstractDAOManager::getAll()
     */
    public function getAll($limit = -1, $offset = -1)
    {
        $all = [];
        try {
            $statement = $this->pdo->prepare("SELECT * FROM {$this->getTableName()} ORDER BY percentage");
            if ($statement->execute()) {
                if ($row = $statement->fetch()) {
                    $grade  = new Grade($row);
                    $grade->setMaxGeneration($this->generationDAOManager->getForId($grade->getMaxGeneration()->getId()));
                    $all[] = $grade;
                    while ($row = $statement->fetch()) {
                        $grade  = new Grade($row);
                        $grade->setMaxGeneration($this->generationDAOManager->getForId($grade->getMaxGeneration()->getId()));
                        $all[] = $grade;
                    }
                    $statement->closeCursor();
                }else {
                    $statement->closeCursor();
                    throw new DAOException("No result resturn by selection query");
                }
            }else {
                $statement->closeCursor();
                throw new DAOException("failed to execute request");
            }
        } catch (\PDOException $e) {
            throw new DAOException($e->getMessage(), DAOException::ERROR_CODE, $e);
        }
        return $all;
    }

}

