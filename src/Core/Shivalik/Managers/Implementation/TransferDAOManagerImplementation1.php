<?php
namespace Core\Shivalik\Managers\Implementation;

use Core\Shivalik\Entities\Transfer;
use Core\Shivalik\Managers\TransferDAOManager;
use PHPBackend\Dao\DAOException;
use PHPBackend\Dao\UtilitaireSQL;

/**
 *
 * @author Esaie MHS
 *        
 */
class TransferDAOManagerImplementation1 extends AbstractOperationDAOManager implements TransferDAOManager
{

    /**
     * {@inheritDoc}
     * @see \PHPBackend\Dao\DAOInterface::createInTransaction()
     * @param Transfer $entity
     */
    public function createInTransaction($entity, \PDO $pdo): void
    {
        $id = UtilitaireSQL::insert($pdo, $this->getTableName(), [
            'source' => $entity->getMember()->getId(),
            'amount' => $entity->getAmount(),
            'receiver' => $entity->getReceiver()->getId(),
            self::FIELD_DATE_AJOUT => $entity->getFormatedDateAjout()
        ]);
        $entity->setId($id);
    }

    /**
     * {@inheritDoc}
     * @see \Core\Shivalik\Managers\Implementation\AbstractOperationDAOManager::update()
     */
    public function update($entity, $id) : void
    {
        throw new DAOException("no subsequent update");
    }


}

