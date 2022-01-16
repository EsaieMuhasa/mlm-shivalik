<?php
namespace Core\Shivalik\Managers\Implementation;


use Core\Shivalik\Entities\Country;
use Core\Shivalik\Managers\CountryDAOManager;
use PHPBackend\Dao\UtilitaireSQL;
use PHPBackend\Dao\DAOEvent;

/**
 * 
 * @author Esaie MUHASA
 *
 */
class CountryDAOManagerImplementation1 extends CountryDAOManager
{

    /**
     * {@inheritDoc}
     * @see \PHPBackend\Dao\DAOInterface::update()
     * @param Country $entity
     */
    public function update($entity, $id) : void
    {
        UtilitaireSQL::update($this->getConnection(), $this->getTableName(), [
            'name' => $entity->getName(),
            'abbreviation' => $entity->getAbbreviation(),
            self::FIELD_DATE_MODIF => $entity->getFormatedDateModif()
        ], $id);
        $event = new DAOEvent($this, DAOEvent::TYPE_UPDATION, $entity);
        $this->dispatchEvent($event);
    }
    
    /**
     * {@inheritDoc}
     * @see \PHPBackend\Dao\DAOInterface::createInTransaction()
     * @param Country $entity
     */
    public function createInTransaction($entity, \PDO $pdo): void
    {
        $id = UtilitaireSQL::insert($pdo, $this->getTableName(), [
            'name' => $entity->getName(),
            'abbreviation' => $entity->getAbbreviation(),
            self::FIELD_DATE_AJOUT => $entity->getFormatedDateAjout()
        ]);
        $entity->setId($id);
    }


}

