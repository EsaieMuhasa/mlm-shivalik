<?php
namespace Core\Shivalik\Managers\Implementation;

use Core\Shivalik\Entities\OfficeAdmin;
use Core\Shivalik\Managers\OfficeAdminDAOManager;
use PHPBackend\DAOException;
use PHPBackend\Dao\UtilitaireSQL;
use Core\Shivalik\Entities\Localisation;
use PHPBackend\Dao\DAOEvent;

/**
 * 
 * @author Esaie MUHASA
 *
 */
class OfficeAdminDAOManagerImplementation1 extends OfficeAdminDAOManager
{
    /**
     * {@inheritDoc}
     * @see \Core\Shivalik\Managers\OfficeAdminDAOManager::findActiveByOffice()
     */
	public function findActiveByOffice(int $officeId): OfficeAdmin {
		$return = null;
		try {
			$statement = $this->pdo->prepare("SELECT * FROM {$this->getTableName()} WHERE office={$officeId} AND enable = 1");
			$statement->execute();
			if ($row = $statement->fetch()) {
				$return = new OfficeAdmin($row);
			}else {
				$statement->closeCursor();
				throw new DAOException("no active administrator account for the office index in parameter");
			}
			$statement->closeCursor();
		} catch (\PDOException $e) {
			throw new DAOException($e->getMessage(), DAOException::ERROR_CODE, $e);
		}
		return  $return;
	}

	/**
	 * {@inheritDoc}
	 * @see \Core\Shivalik\Managers\OfficeAdminDAOManager::findAdminByOffice()
	 */
	public function findAdminByOffice(int $officeId): OfficeAdmin {
		$return = null;
		try {
			$statement = $this->pdo->prepare("SELECT * FROM {$this->getTableName()} WHERE office={$officeId}");
			$statement->execute();
			if ($row = $statement->fetch()) {
				$return = new OfficeAdmin($row);
			}else {
				$statement->closeCursor();
				throw new DAOException("no active administrator account for the office index in parameter");
			}
			$statement->closeCursor();
		} catch (\PDOException $e) {
			throw new DAOException($e->getMessage(), DAOException::ERROR_CODE, $e);
		}
		return  $return;
	}

    /**
     * {@inheritDoc}
     * @see \PHPBackend\Dao\DAOInterface::update()
     * @param OfficeAdmin $entity
     */
    public function update($entity, $id) : void
    {
        $this->pdo_updateInTable($this->getTableName(), array(
            'name' => $entity->getName(),
            'postName' => $entity->getPostName(),
            'lastName'=> $entity->getLastName(),
            'email' => $entity->getEmail(),
            'telephone' => $entity->getTelephone(),
            'kind' => $entity->getKind(),
            self::FIELD_DATE_MODIF => $entity->getFormatedDateModif()
        ), $id);
        
        $event = new DAOEvent($this, DAOEvent::TYPE_UPDATION, $entity);
        $this->dispatchEvent($event);
    }
    
    
    /**
     * {@inheritDoc}
     * @see \PHPBackend\Dao\DAOInterface::createInTransaction()
     * @param OfficeAdmin $entity
     */
    public function createInTransaction($entity, $pdo): void
    {
        if ($entity->getLocalisation() != null) {
            $this->getDaoManager()->getManagerOf(Localisation::class)->createInTransaction($entity->getLocalisation(), $pdo);
        }
        
        $id = UtilitaireSQL::insert($pdo, $this->getTableName(), [
            'name' => $entity->getName(),
            'postName' => $entity->getPostName(),
            'lastName'=> $entity->getLastName(),
            'password' => $entity->getPassword(),
            'email' => $entity->getEmail(),
            'photo' => $entity->getPhoto(),
            'telephone' => $entity->getTelephone(),
            'kind' => $entity->getKind(),
            'office' => ($entity->getOffice()->getId()),
            'localisation'=>($entity->getLocalisation()!=null? $entity->getLocalisation()->getId() : null),
            self::FIELD_DATE_AJOUT => $entity->getFormatedDateAjout()            
        ]);
        
        $entity->setId($id);
    }
    
	/**
	 * {@inheritDoc}
	 * @see \Core\Shivalik\Managers\OfficeAdminDAOManager::hasAdmin()
	 */
	public function checkByOffice(int $officeId, bool $active = true): bool {
		return UtilitaireSQL::checkAll($this->getConnection(), $this->getTableName(), [
		    "office" => $officeId,
		    "enable" => $active ? 1 : '0'
		]);
	}
	
	
}

