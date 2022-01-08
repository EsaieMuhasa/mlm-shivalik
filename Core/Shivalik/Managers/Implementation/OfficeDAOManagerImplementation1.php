<?php
namespace Core\Shivalik\Managers\Implementation;

use Core\Shivalik\Entities\Office;
use Core\Shivalik\Managers\OfficeDAOManager;
use PHPBackend\DAOException;
use PHPBackend\Dao\DAOEvent;
use PHPBackend\Dao\UtilitaireSQL;

/**
 *
 * @author Esaie MHS
 *        
 */
class OfficeDAOManagerImplementation1 extends OfficeDAOManager
{
    /**
     * {@inheritDoc}
     * @see \PHPBackend\Dao\DAOInterface::update()
     */
    public function update($entity, $id) : void
    {
        try {
            $pdo = $this->getConnection();
            if ($pdo->beginTransaction()) {
                $this->updateInTransaction($entity, $id, $pdo);
                $pdo->commit();
                
                $event = new DAOEvent($this, DAOEvent::TYPE_UPDATION, $entity);
                $this->dispatchEvent($event);
            }else {
                throw new DAOException("An error occurred while creating the transaction");
            }
        } catch (\PDOException $e) {
            throw new DAOException("An error occurred in the plain banefice sharing transaction: {$e->getMessage()}", intval($e->getCode()), $e);
        }
    }
    
    /**
     * {@inheritDoc}
     * @see \PHPBackend\Dao\DAOInterface::createInTransaction()
     * @param Office $entity
     */
    public function createInTransaction($entity, \PDO $pdo): void
    {
        if ($entity->getLocalisation() != null) {
            $this->localisationDAOManager->createInTransaction($entity->getLocalisation(), $pdo);
        }
        
        $id = UtilitaireSQL::insert($pdo, $this->getTableName(), [
            'name' => $entity->getName(),
            'photo' => $entity->getPhoto(),
        	'member' => $entity->getMember()->getId(),
            'localisation' => ($entity->getLocalisation() != null? $entity->getLocalisation()->getId() : null),
            self::FIELD_DATE_AJOUT => $entity->getFormatedDateAjout()
        ]);
        
        $entity->setId($id);
    }
    
    /**
     * {@inheritDoc}
     * @see \PHPBackend\Dao\DefaultDAOInterface::updateInTransaction()
     * @param Office $entity
     */
    public function updateInTransaction($entity, $id, \PDO $pdo): void
    {
        $data = [
            'name' => $entity->getName(),
            self::FIELD_DATE_MODIF => $entity->getFormatedDateModif()            
        ];
        
        if ($entity->getPhoto() != null) {
            $data['photo'] = $entity->getPhoto();
        }
        
        UtilitaireSQL::update($pdo, $this->getTableName(), $data, $id);
        
    }


}

