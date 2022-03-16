<?php
namespace Core\Shivalik\Managers\Implementation;


use Core\Shivalik\Entities\Size;
use Core\Shivalik\Managers\SizeDAOManager;
use PHPBackend\Dao\UtilitaireSQL;
use PHPBackend\Dao\DAOEvent;
use PHPBackend\Dao\DefaultDAOInterface;

/**
 *
 * @author Esaie MHS
 *        
 */
class SizeDAOManagerImplementation1 extends DefaultDAOInterface implements SizeDAOManager
{
    
    /**
     * {@inheritDoc}
     * @see \PHPBackend\Dao\DefaultDAOInterface::createInTransaction()
     * @param Size $entity
     */
    public function createInTransaction($entity, \PDO $pdo): void
    {
        $id = UtilitaireSQL::insert($pdo, $this->getTableName(), [
            'name' => $entity->getName(),
            'abbreviation' => $entity->getAbbreviation(),
            'percentage' => $entity->getPercentage(),
            self::FIELD_DATE_AJOUT => $entity->getFormatedDateAjout()            
        ]);
        $entity->setId($id);        
    }

    /**
     * {@inheritDoc}
     * @see \PHPBackend\Dao\DAOInterface::update()
     * @param Size $entity
     */
    public function update($entity, $id) : void
    {
        UtilitaireSQL::update($this->getConnection(), $this->getTableName(), [            
            'name' => $entity->getName(),
            'abbreviation' => $entity->getAbbreviation(),
            'percentage' => $entity->getPercentage(),
            self::FIELD_DATE_MODIF => $entity->getFormatedDateModif()
        ], $id);
        $event = new DAOEvent($this, DAOEvent::TYPE_UPDATION, $entity);
        $this->dispatchEvent($event);
    }
    
    /**
     * {@inheritDoc}
     * @see \Core\Shivalik\Managers\SizeDAOManager::checkByAbbreviation()
     */
    public function checkByAbbreviation (string $abbreviation, ?int $id = null) : bool{
        return $this->columnValueExist('abbreviation', $abbreviation, $id);
    }
    
    /**
     * {@inheritDoc}
     * @see \Core\Shivalik\Managers\SizeDAOManager::checkByName()
     */
    public function checkByName (string $name, ?int $id = null) : bool {
        return $this->columnValueExist('name', $name, $id);
    }

}

