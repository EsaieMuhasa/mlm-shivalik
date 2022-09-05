<?php
namespace Core\Shivalik\Managers\Implementation;


use Core\Shivalik\Entities\Country;
use Core\Shivalik\Managers\CountryDAOManager;
use PHPBackend\Dao\DAOEvent;
use PHPBackend\Dao\DefaultDAOInterface;
use PHPBackend\Dao\UtilitaireSQL;

/**
 * 
 * @author Esaie MUHASA
 *
 */
class CountryDAOManagerImplementation1 extends DefaultDAOInterface implements CountryDAOManager
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
     * @see \PHPBackend\Dao\DefaultDAOInterface::createInTransaction()
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
    
    /**
     * @param string $name
     * @param int $id
     * @return bool
     */
    public function checkByName (string $name, ?int $id = null) : bool {
        return $this->columnValueExist('name', $name, $id);
    }
    
    /**
     *
     * @param string $abbreviation
     * @param int $id
     * @return bool
     */
    public function checkByAbreviation (string $abbreviation, ?int $id = null) : bool {
        return $this->columnValueExist('abbreviation', $abbreviation, $id);
    }


}

