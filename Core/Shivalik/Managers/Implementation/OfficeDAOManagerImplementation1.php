<?php
namespace Core\Shivalik\Managers\Implementation;

use Core\Shivalik\Entities\GradeMember;
use Core\Shivalik\Entities\Office;
use Core\Shivalik\Entities\RequestVirtualMoney;
use Core\Shivalik\Entities\VirtualMoney;
use Core\Shivalik\Entities\Withdrawal;
use Core\Shivalik\Managers\LocalisationDAOManager;
use Core\Shivalik\Managers\MemberDAOManager;
use Core\Shivalik\Managers\OfficeDAOManager;
use PHPBackend\Dao\DAOEvent;
use PHPBackend\Dao\DAOException;
use PHPBackend\Dao\DefaultDAOInterface;
use PHPBackend\Dao\UtilitaireSQL;

/**
 *
 * @author Esaie MHS
 *        
 */
class OfficeDAOManagerImplementation1 extends DefaultDAOInterface implements OfficeDAOManager
{
    /**
     * @var MemberDAOManager
     */
    protected $memberDAOManager;
    
    /**
     * @var LocalisationDAOManager
     */
    protected $localisationDAOManager;
    
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
     * @see \PHPBackend\Dao\DefaultDAOInterface::findByColumnName()
     */
    public function findByColumnName(string $columnName, $value, bool $forward = true)
    {
        $office = parent::findByColumnName($columnName, $value, $forward);
        if ($office->member != null) {
            $office->setMember($this->memberDAOManager->findById($office->member->id));
        }
        $office->setLocalisation($this->localisationDAOManager->findById($office->localisation->id));
        return $office;
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
    
    
    /**
     * {@inheritDoc}
     * @see \PHPBackend\Dao\DefaultDAOInterface::findAll()
     */
    public function findAll(?int $limit = null, int $offset = 0) : array {
        $all = parent::findAll($limit, $offset);
        foreach ($all as $o) {
            if ($o->member != null) {
                $o->setMember($this->memberDAOManager->findById($o->member->id, false));
            }
            $o->setLocalisation($this->localisationDAOManager->findById($o->localisation->id));
        }
        return $all;
    }
    
    
    /**
     * {@inheritDoc}
     * @see \Core\Shivalik\Managers\OfficeDAOManager::checkByName()
     */
    public function checkByName (string $name, ?int $id = null) : bool {
        return $this->columnValueExist('name', $name, $id);
    }
    
    /**
     * {@inheritDoc}
     * @see \Core\Shivalik\Managers\OfficeDAOManager::updateVisibility()
     */
    public function updateVisibility (int $id, bool $visible) : void {
        UtilitaireSQL::update(
            $this->getConnection(), $this->getTableName(), ['visible' => $visible? '1':'0'], $id);
    }
    
    /**
     * {@inheritDoc}
     * @see \Core\Shivalik\Managers\OfficeDAOManager::findByVisibility()
     */
    public function findByVisibility (bool $visible = true, ?int $limit = null, int $offset = 0) {
        return UtilitaireSQL::findAll(
            $this->getConnection(), $this->getTableName(), $this->getMetadata()->getName(),
            "dateAjout", true, ['visible' => $visible? '1':'0'], $limit, $offset);
    }
    
    /**
     * {@inheritDoc}
     * @see \Core\Shivalik\Managers\OfficeDAOManager::checkByVisibility()
     */
    public function checkByVisibility (bool $visible = true, ?int $limit = null, int $offset = 0) {
        return UtilitaireSQL::checkAll(
            $this->getConnection(), $this->getTableName(), ['visible' => $visible? '1':'0'],
            $limit, $offset);
    }
    
    /**
     * {@inheritDoc}
     * @see \Core\Shivalik\Managers\OfficeDAOManager::updatePhoto()
     */
    public function updatePhoto (int $id, string $photo) : void {
        UtilitaireSQL::update($this->getConnection(), $this->getTableName(), array('photo' =>$photo), $id);
    }
    
    
    /**
     * {@inheritDoc}
     * @see \Core\Shivalik\Managers\OfficeDAOManager::load()
     */
    public function load ($office) : Office {
        /**
         * @var Office $return
         */
        $return = ($office instanceof Office)? $office : $this->findById($office);
        
        if ($this->getDaoManager()->getManagerOf(Withdrawal::class)->checkByOffice($return->getId(), null)) {//operation de matching dans le bureau
            $withdrawals = $this->getDaoManager()->getManagerOf(Withdrawal::class)->findByOffice($return->getId(), null);
            $return->setWithdrawals($withdrawals);
        }
        
        if ($this->getDaoManager()->getManagerOf(GradeMember::class)->checkByOffice($return->getId())) {//chargement des operations qui touches les membres adherant
            $return->setOperations($this->getDaoManager()->getManagerOf(GradeMember::class)->findByOffice($return->getId()));
        }
        
        if ($this->getDaoManager()->getManagerOf(VirtualMoney::class)->checkByOffice($return->getId())) {//operations qui touches le monais virtuel pour facilister l'adhesion des membre
            $return->setVirtualMoneys($this->getDaoManager()->getManagerOf(VirtualMoney::class)->findByOffice($return->getId()));
        }
        
        if ($this->getDaoManager()->getManagerOf(RequestVirtualMoney::class)->checkWaiting($return->getId())) {//Les demandes des monais virtuel effectuer par le proprietaire de l'argent
            $requests = $this->getDaoManager()->getManagerOf(RequestVirtualMoney::class)->findWaiting($return->getId());
            $return->setRequests($requests);
        }
        
        return $return;
    }
    
    /**
     * {@inheritDoc}
     * @see \Core\Shivalik\Managers\OfficeDAOManager::checkByMember()
     */
    public function checkByMember (int $memberId) : bool {
        return $this->columnValueExist('member', $memberId);
    }
    
    /**
     * {@inheritDoc}
     * @see \Core\Shivalik\Managers\OfficeDAOManager::findByMember()
     */
    public function findByMember (int $memberId) : Office {
        return $this->findByColumnName("member", $memberId);
    }


}

