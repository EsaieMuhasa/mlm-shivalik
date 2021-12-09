<?php
namespace Managers\Implementation;

use Managers\OfficeAdminDAOManager;
use Entities\OfficeAdmin;
use Library\DAOException;

/**
 *
 * @author Esaie MHS
 *        
 */
class OfficeAdminDAOManagerImplementation1 extends OfficeAdminDAOManager
{
    /**
	 * {@inheritDoc}
	 * @see \Managers\OfficeAdminDAOManager::activeInOffice()
	 */
	public function activeInOffice(int $officeId): OfficeAdmin {
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
	 * @see \Managers\OfficeAdminDAOManager::getAdmin()
	 */
	public function getAdmin(int $officeId): OfficeAdmin {
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
     * @see \Library\AbstractDAOManager::create()
     * @param OfficeAdmin $entity
     */
    public function create($entity)
    {
        try {
            if ($this->pdo->beginTransaction()) {
                $this->createInTransaction($entity, $this->pdo);
                $this->pdo->commit();
            }            
        } catch (\PDOException $e) {
            try {
                $this->pdo->rollBack();
            } catch (\Exception $e) {
            }
            throw new DAOException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * {@inheritDoc}
     * @see \Library\AbstractDAOManager::update()
     */
    public function update($entity, $id)
    {
        $this->pdo_updateInTable($this->getTableName(), array(
            'name' => $entity->getName(),
            'postName' => $entity->getPostName(),
            'lastName'=> $entity->getLastName(),
            'email' => $entity->getEmail(),
            'telephone' => $entity->getTelephone(),
            'kind' => $entity->getKind()
        ), $id);
    }
    
    
    /**
     * {@inheritDoc}
     * @see \Library\AbstractDAOManager::createInTransaction()
     * @param OfficeAdmin $entity
     */
    public function createInTransaction($entity, $api): void
    {
        if ($entity->getLocalisation() != null) {
            $this->getDaoManager()->getManagerOf('Localisation')->createInTransaction($entity->getLocalisation(), $api);
        }
        
        $id = $this->pdo_insertInTableTansactionnel($api, $this->getTableName(), array(
            'name' => $entity->getName(),
            'postName' => $entity->getPostName(),
            'lastName'=> $entity->getLastName(),
            'password' => $entity->getPassword(),
            'email' => $entity->getEmail(),
            'photo' => $entity->getPhoto(),
            'telephone' => $entity->getTelephone(),
            'kind' => $entity->getKind(),
            'office' => ($entity->getOffice()->getId()),
            'localisation'=>($entity->getLocalisation()!=null? $entity->getLocalisation()->getId() : null)
        ));
        
        $entity->setId($id);
    }
    
	/**
	 * {@inheritDoc}
	 * @see \Managers\OfficeAdminDAOManager::hasAdmin()
	 */
	public function hasAdmin(int $officeId, bool $active = true): bool {
		$return = false;
		try {
			$statement = $this->pdo->prepare("SELECT * FROM {$this->getTableName()} WHERE office={$officeId} AND enable = ".($active? '1':'0'));
			$statement->execute();
			if ($statement->fetch()) {
				$return = true;
			}
			$statement->closeCursor();
		} catch (\PDOException $e) {
			throw new DAOException($e->getMessage(), DAOException::ERROR_CODE, $e);
		}
		return  $return;
	}
	
	

}

