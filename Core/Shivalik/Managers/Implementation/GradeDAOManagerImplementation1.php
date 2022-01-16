<?php
namespace Core\Shivalik\Managers\Implementation;

use Core\Shivalik\Entities\Grade;
use Core\Shivalik\Managers\GenerationDAOManager;
use Core\Shivalik\Managers\GradeDAOManager;
use PHPBackend\Dao\DAOEvent;
use PHPBackend\Dao\UtilitaireSQL;

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
     * @see \PHPBackend\Dao\DAOInterface::createInTransaction()
     * @param Grade $entity
     */
    public function createInTransaction($entity, \PDO $pdo): void
    {
        $id = UtilitaireSQL::insert($pdo, $this->getTableName(), [
            'name' => $entity->getName(),
            'icon' => $entity->getIcon(),
            'maxGeneration' => $entity->getMaxGeneration()->getId(),
            'percentage' => $entity->getPercentage(),
            'amount' => $entity->getAmount(),
            self::FIELD_DATE_AJOUT => $entity->getFormatedDateAjout()
        ]);
        $entity->setId($id);
    }

    /**
     * {@inheritDoc}
     * @see \PHPBackend\Dao\DAOInterface::update()
     * @param Grade $entity
     */
    public function update($entity, $id) : void
    {
        
        $data = [
            'name' => $entity->getName(),
            'maxGeneration' => $entity->getMaxGeneration()->getId(),
            'percentage' => $entity->getPercentage(),
            'amount' => $entity->getAmount(),
            self::FIELD_DATE_MODIF => $entity->getFormatedDateModif()
        ];
        
        if ($entity->getIcon() != null ) {
            $data['icon'] = $entity->getIcon();
        }
        
        UtilitaireSQL::update($this->getConnection(), $this->getTableName(), $data, $id);
        
        $event = new DAOEvent($this, DAOEvent::TYPE_UPDATION, $entity);
        $this->dispatchEvent($event);
    }
    
    /**
     * {@inheritDoc}
     * @see \Core\Shivalik\Managers\GradeDAOManager::findAll()
     * @return Grade[]
     */
    public function findAll(?int $limit = null, int $offset = 0) : array
    {
        $all = UtilitaireSQL::findAll($this->getConnection(), $this->getTableName(), $this->getMetadata()->getName(), "percentage", true, [], $limit, $offset);
        
        foreach ($all as $grade) {
            $grade->setMaxGeneration($this->generationDAOManager->findById($grade->getMaxGeneration()->getId()));
        }
        return $all;
    }

}

