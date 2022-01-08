<?php
namespace Core\Shivalik\Managers\Implementation;


use Core\Shivalik\Entities\Localisation;
use Core\Shivalik\Entities\Member;
use Core\Shivalik\Managers\MemberDAOManager;
use PHPBackend\DAOException;
use PHPBackend\Dao\DAOEvent;
use PHPBackend\Dao\UtilitaireSQL;

/**
 *
 * @author Esaie MHS
 *        
 */
class MemberDAOManagerImplementation1 extends MemberDAOManager
{
   
    /**
     * {@inheritDoc}
     * @see \Core\Shivalik\Managers\MemberDAOManager::findChild()
     */
    public function findChild(int $memberId, int $foot): Member
    {
        
        $child = null;
        try {
            $statement = $this->getConnection()->prepare("SELECT * FROM {$this->getTableName()} WHERE parent=:parent AND foot=:foot");
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
     * @see \Core\Shivalik\Managers\MemberDAOManager::checkChild()
     */
    public function checkChild(int $memberId, int $foot): bool
    {
        return UtilitaireSQL::checkAll($this->getConnection(), $this->getTableName(), ['parent' => $memberId, 'foot' => $foot]);
    }

    /**
     * {@inheritDoc}
     * @see MemberDAOManager::hasChilds()
     */
    public function checkChilds(int $memberId): bool
    {
        return UtilitaireSQL::checkAll($this->getConnection(), $this->getTableName(), ['parent' => $memberId]);
    }

    /**
     * {@inheritDoc}
     * @see \Core\Shivalik\Managers\MemberDAOManager::checkParent()
     */
    public function checkParent (int $memberId): bool
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
     * @see MemberDAOManager::hasSponsor()
     */
    public function checkSponsor(int $memberId): bool
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
     * @see \PHPBackend\Dao\DAOInterface::createInTransaction()
     * @param Member $entity
     */
    public function createInTransaction($entity, \PDO $pdo): void
    {
        if ($entity->getLocalisation() != null) {
            $this->getDaoManager()->getManagerOf(Localisation::class)->createInTransaction($entity->getLocalisation(), $pdo);
        }
        $id = UtilitaireSQL::insert($pdo, $this->getTableName(), [            
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
            'localisation'=>($entity->getLocalisation()!=null? $entity->getLocalisation()->getId() : null),
            self::FIELD_DATE_AJOUT => $entity->getFormatedDateAjout()
        ]);
        
        $entity->setId($id);
        
        $matricule = $entity->generateMatricule();
        
        UtilitaireSQL::update($pdo, $this->getTableName(), ['matricule' => $matricule], $id);
    }

    /**
     * {@inheritDoc}
     * @see \PHPBackend\Dao\DAOInterface::update()
     * @param Member $entity
     */
    public function update($entity, $id) : void
    {
        UtilitaireSQL::update($this->getConnection(), $this->getTableName(), [
            'name' => $entity->getName(),
            'postName' => $entity->getPostName(),
            'lastName'=> $entity->getLastName(),
            'email' => $entity->getEmail(),
            'telephone' => $entity->getTelephone(),
            self::FIELD_DATE_MODIF => $entity->getFormatedDateModif()
        ], $id);
        $event = new DAOEvent($this, DAOEvent::TYPE_UPDATION, $entity);
        $this->dispatchEvent($event);
    }

}

