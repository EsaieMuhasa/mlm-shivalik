<?php
namespace Core\Shivalik\Managers\Implementation;

use Core\Shivalik\Entities\NotifiableComponent;
use Core\Shivalik\Managers\NotifiableComponentDAOManager;
use PHPBackend\Dao\DAOException;
use PHPBackend\Dao\UtilitaireSQL;

/**
 *
 * @author Esaie MUHASA
 *        
 */
class NotifiableComponentDAOManagerImplementation1 extends NotifiableComponentDAOManager
{
    /**
     * {@inheritDoc}
     * @see \Core\Shivalik\Managers\NotifiableComponentDAOManager::findByNotifiable()
     */
    public function findByNotifiable($key, string $entity): NotifiableComponent
    {
        $return = null;
        try {
            $statement = $this->pdo->prepare("SELECT * {$this->getTableName()} WHERE dataKey =:dataKey AND entity=:entity AND deleted != 0");
            $statement->execute(array("dataKey" => $key , "entity" => $entity));
            if ($row = $statement->fetch()) {
                $statement->closeCursor();
                $return = new NotifiableComponent($row);
                $return->setNotifiable($this->getDaoManager()->getManagerOf($entity)->getForId(intval($key, 10), false));
            }else {
                $statement->closeCursor();
                throw new DAOException("no noticeable component for the desired element");
            }
        } catch (\PDOException $e) {
            throw new DAOException("an error occurred while selecting data", DAOException::ERROR_CODE, $e);
        }
        return $return;
    }

    /**
     * {@inheritDoc}
     * @see \PHPBackend\Dao\DAOInterface::createInTransaction()
     * @param NotifiableComponent $entity
     */
    public function createInTransaction($entity, $pdo): void
    {
        $id = UtilitaireSQL::insert($pdo, $this->getTableName(), [            
            "entity" => $entity->getEntity(),
            "dataKey" => $entity->getNotifiable()->getKey(),
            self::FIELD_DATE_AJOUT => $entity->getFormatedDateAjout()
        ]);
        $entity->setId($id);
    }

    /**
     * {@inheritDoc}
     * @see \PHPBackend\Dao\DAOInterface::update()
     */
    public function update($entity, $id)
    {
        throw new DAOException("update operation is not supported");
    }
 
}

