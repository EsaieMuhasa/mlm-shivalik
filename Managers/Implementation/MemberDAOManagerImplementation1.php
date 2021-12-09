<?php
namespace Managers\Implementation;

use Entities\Member;
use Managers\MemberDAOManager;
use Library\DAOException;

/**
 *
 * @author Esaie MHS
 *        
 */
class MemberDAOManagerImplementation1 extends MemberDAOManager
{
    /**
     * {@inheritDoc}
     * @see \Managers\MemberDAOManager::getChilds()
     */
    public function getChilds(int $memberId): array
    {
        $childs = array();
        try {
            $statement = $this->pdo->prepare("SELECT * FROM {$this->getTableName()} WHERE parent=:parent ORDER BY foot");
            if($statement->execute(array('parent' => $memberId))){
                if($row = $statement->fetch()){
                    $childs[] = new Member($row);
                    while ($row = $statement->fetch()) {
                        $childs[] = new Member($row);
                    }
                } else {
                    $statement->closeCursor();
                    throw new DAOException("no child knot");
                }
            }else {
                $statement->closeCursor();
                throw new DAOException("Query failed to execute");
            }
            $statement->closeCursor();
            
        } catch (\PDOException $e) {
            throw new DAOException($e->getMessage(), DAOException::ERROR_CODE, $e);
        }
        
        return $childs;
    }

    /**
     * {@inheritDoc}
     * @see \Managers\MemberDAOManager::getChild()
     */
    public function getChild(int $memberId, int $foot): Member
    {
        $child = null;
        try {
            $statement = $this->pdo->prepare("SELECT * FROM {$this->getTableName()} WHERE parent=:parent AND foot=:foot");
            if($statement->execute(array('parent' => $memberId, 'foot' => $foot))){
                if($row = $statement->fetch()){
                    $child = new Member($row);
                } else {
                    $statement->closeCursor();
                    throw new DAOException("no children on the foot {$foot}");
                }
            } else {
                $statement->closeCursor();
                throw new DAOException("Query failed to execute");
            }
            
            $statement->closeCursor();
            
        } catch (\PDOException $e) {
            throw new DAOException($e->getMessage(), DAOException::ERROR_CODE, $e);
        }
        
        return $child;
    }


    /**
     * {@inheritDoc}
     * @see \Managers\MemberDAOManager::getParent()
     */
    public function getParent(int $memberId): Member
    {
        if ($this->hasParent($memberId)) {
            /**
             * @var Member $member
             */
            $member = $this->getForId($memberId, false);
            return $this->getForId($member->getParent()->getId());
        }
        
        throw new DAOException("this node does not have a parent node");
    }


    /**
     * {@inheritDoc}
     * @see \Managers\MemberDAOManager::getSponsor()
     */
    public function getSponsor(int $memberId): Member
    {
        if ($this->hasSponsor($memberId)) {
            /**
             * @var Member $member
             */
            $member = $this->getForId($memberId, false);
            return $this->getForId($member->getSponsor()->getId());
        }
        
        throw new DAOException("this node does not have a sponsor node");
    }
    
    /**
     * {@inheritDoc}
     * @see \Managers\MemberDAOManager::hasChilds()
     */
    public function hasChild(int $memberId, int $foot): bool
    {
        $return = false;
        try {
            $statement = $this->pdo->prepare("SELECT * FROM {$this->getTableName()} WHERE parent=:parent AND foot=:foot");
            if($statement->execute(array('parent' => $memberId, 'foot' => $foot))){
                if($statement->fetch()){
                    $return = true;
                }
            } else {
                $statement->closeCursor();
                throw new DAOException("Query failed to execute");
            }
            
            $statement->closeCursor();
            
        } catch (\PDOException $e) {
            throw new DAOException($e->getMessage(), DAOException::ERROR_CODE, $e);
        }
        
        return $return;
    }

    /**
     * {@inheritDoc}
     * @see \Managers\MemberDAOManager::hasChilds()
     */
    public function hasChilds(int $memberId): bool
    {
        $return = false;
        try {
            $statement = $this->pdo->prepare("SELECT * FROM {$this->getTableName()} WHERE parent=:parent");
            if($statement->execute(array('parent' => $memberId))){
                if($statement->fetch()){
                    $return = true;
                } 
            } else {
                $statement->closeCursor();
                throw new DAOException("Query failed to execute");
            }
            
            $statement->closeCursor();
            
        } catch (\PDOException $e) {
            throw new DAOException($e->getMessage(), DAOException::ERROR_CODE, $e);
        }
        
        return $return;
    }

    /**
     * {@inheritDoc}
     * @see \Managers\MemberDAOManager::hasParent()
     */
    public function hasParent(int $memberId): bool
    {
        $return = false;
        try {
            $statement = $this->pdo->prepare("SELECT id FROM {$this->getTableName()} WHERE id=:id AND parent IS NOT NULL");
            if($statement->execute(array('id' => $memberId))){
                if($statement->fetch()){
                    $return = true;
                }
            } else {
                $statement->closeCursor();
                throw new DAOException("Query failed to execute");
            }
            
            $statement->closeCursor();
            
        } catch (\PDOException $e) {
            throw new DAOException($e->getMessage(), DAOException::ERROR_CODE, $e);
        }
        
        return $return;
    }

    /**
     * {@inheritDoc}
     * @see \Managers\MemberDAOManager::hasSponsor()
     */
    public function hasSponsor(int $memberId): bool
    {
        $return = false;
        try {
            $statement = $this->pdo->prepare("SELECT id FROM {$this->getTableName()} WHERE id=:id AND sponsor IS NOT NULL");
            if($statement->execute(array('id' => $memberId))){
                if($statement->fetch()){
                    $return = true;
                }
            } else {
                $statement->closeCursor();
                throw new DAOException("Query failed to execute");
            }
            
            $statement->closeCursor();
            
        } catch (\PDOException $e) {
            throw new DAOException($e->getMessage(), DAOException::ERROR_CODE, $e);
        }
        
        return $return;
    }

    /**
     * {@inheritDoc}
     * @see \Library\AbstractDAOManager::create()
     */
    public function create($entity)
    {
        $this->createInTransaction($entity, $this->pdo);
    }

    /**
     * {@inheritDoc}
     * @see \Library\AbstractDAOManager::createInTransaction()
     * @param Member $entity
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
            'pseudo' => $entity->getPseudo(),
            'password' => $entity->getPassword(),
            'email' => $entity->getEmail(),
            'kind' => $entity->getKind(),
            'telephone' => $entity->getTelephone(),
            'foot' => $entity->getFoot(),
            'parent' => ($entity->getParent()!=null? $entity->getParent()->getId() : null),
            'sponsor' => ($entity->getSponsor()!=null? $entity->getSponsor()->getId() : null),
            'admin' => ($entity->getAdmin()!=null? $entity->getAdmin()->getId() : null),
            'office' => ($entity->getOffice()!=null? $entity->getOffice()->getId() : null),
            'localisation'=>($entity->getLocalisation()!=null? $entity->getLocalisation()->getId() : null)
        ));
        
        $entity->setId($id);
        
        $matricule = $entity->generateMatricule();
        $this->updateMatricule($matricule, $id);
        
        $this->pdo_updateInTableTransactionnel($api, $this->getTableName(), array(
            'matricule' => $matricule
        ), $id, false);
    }

    /**
     * {@inheritDoc}
     * @see \Library\AbstractDAOManager::update()
     * @param Member $entity
     */
    public function update($entity, $id)
    {
        $this->pdo_updateInTable($this->getTableName(), array(
            'name' => $entity->getName(),
            'postName' => $entity->getPostName(),
            'lastName'=> $entity->getLastName(),
            'email' => $entity->getEmail(),
            'telephone' => $entity->getTelephone(),
        	'pseudo' => $entity->getPseudo()
        ), $id);
    }



}

