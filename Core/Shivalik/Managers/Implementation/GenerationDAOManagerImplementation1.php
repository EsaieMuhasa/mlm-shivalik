<?php
namespace Core\Shivalik\Managers\Implementation;

use Core\Shivalik\Entities\Generation;
use Core\Shivalik\Managers\GenerationDAOManager;
use PHPBackend\Dao\DAOEvent;
use PHPBackend\Dao\UtilitaireSQL;

/**
 *
 * @author Esaie MHS
 *        
 */
class GenerationDAOManagerImplementation1 extends GenerationDAOManager
{

    /**
     * {@inheritDoc}
     * @see \PHPBackend\Dao\DefaultDAOInterface::findAll()
     */
    public function findAll(?int $limit = null, int $offset = 0) : array
    {
        return UtilitaireSQL::findAll($this->getConnection(), $this->getTableName(), $this->getMetadata()->getName(), "number", true, [], $limit, $offset);
    }

    /**
     * {@inheritDoc}
     * @see \PHPBackend\Dao\DAOInterface::update()
     * @param Generation $entity
     */
    public function update($entity, $id) : void
    {
        UtilitaireSQL::update($this->getConnection(), $this->getTableName(), [            
            'name' => $entity->getName(),
            'abbreviation' => $entity->getAbbreviation(),
            'number' => $entity->getNumber(),
            'percentage' => $entity->getPercentage(),
            self::FIELD_DATE_MODIF => $entity->getFormatedDateModif()
        ], $id);
        $event = new DAOEvent($this, DAOEvent::TYPE_UPDATION, $entity);
        $this->dispatchEvent($event);
    }
    /**
     * {@inheritDoc}
     * @see \PHPBackend\Dao\DAOInterface::createInTransaction()
     * @param Generation $entity
     */
    public function createInTransaction($entity, \PDO $pdo): void
    {
        $id = UtilitaireSQL::insert($pdo, $this->getTableName(), [
            'name' => $entity->getName(),
            'abbreviation' => $entity->getAbbreviation(),
            'number' => $entity->getNumber(),
            'percentage' => $entity->getPercentage(),
            self::FIELD_DATE_AJOUT => $entity->getFormatedDateAjout()
            
        ]);
        $entity->setId($id);
    }
  
}

