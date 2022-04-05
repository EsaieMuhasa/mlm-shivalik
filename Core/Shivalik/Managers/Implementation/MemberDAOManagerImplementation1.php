<?php
namespace Core\Shivalik\Managers\Implementation;


use Core\Shivalik\Entities\Account;
use Core\Shivalik\Entities\BonusGeneration;
use Core\Shivalik\Entities\Localisation;
use Core\Shivalik\Entities\Member;
use Core\Shivalik\Entities\PointValue;
use Core\Shivalik\Entities\Withdrawal;
use Core\Shivalik\Managers\MemberDAOManager;
use PHPBackend\Dao\DAOEvent;
use PHPBackend\Dao\DAOException;
use PHPBackend\Dao\UtilitaireSQL;
use Core\Shivalik\Entities\OfficeBonus;
use Core\Shivalik\Entities\GradeMember;

/**
 *
 * @author Esaie MHS
 *        
 */
class MemberDAOManagerImplementation1 extends AbstractUserDAOManager implements MemberDAOManager
{
    /**
     * {@inheritDoc}
     * @see \Core\Shivalik\Managers\MemberDAOManager::loadAccount()
     */
    public function loadAccount ($member, bool $calcul = true) : Account {
        $account = new Account(($member instanceof Member)? $member : $this->findById($member));
        
        $daos = [PointValue::class, BonusGeneration::class, OfficeBonus::class, Withdrawal::class];
        
        foreach ($daos as $dao) {
            /**
             * @var AbstractOperationDAOManager $interface
             */
            $interface = $this->getManagerFactory()->getManagerOf($dao);
            
            if ($interface->checkByMember($account->getMember()->getId())) {
                $operations = $interface->findByMember($account->getMember()->getId());
                $account->addOperations($operations, false);
            }
            
        }
        
        if ($calcul) {
            $account->calcul();
        }
        
        return $account;
    }
    
    
    /**
     * {@inheritDoc}
     * @see \Core\Shivalik\Managers\MemberDAOManager::insertBelow()
     */
    public function insertBelow(Account $newAccount, Account $existAcount): void {
        try {
            $pdo = $this->getConnection();
            if ($pdo->beginTransaction()) {
                
                //creation du membre
                $newAccount->getMember()->setParent($existAcount->getMember()->getParent());
                $newAccount->getMember()->setSponsor($existAcount->getMember()->getSponsor());
                $newAccount->getMember()->setFoot(4);//pour eviter de violer la contrainte d'unicite
                
                $this->createInTransaction($newAccount->getMember(), $pdo);

                UtilitaireSQL::update($pdo, $this->getTableName(), [
                    'parent' => $newAccount->getMember()->getId()
                ], $existAcount->getMember()->getId());
                
                $newAccount->getMember()->setFoot($existAcount->getMember()->getFoot());
                UtilitaireSQL::update($pdo, $this->getTableName(), [
                    'foot' => $newAccount->getMember()->getFoot()
                ], $newAccount->getMember()->getId());//correction du foot du nouveau membre
                //==
                
                //packet
                $this->getDaoManager()->getManagerOf(GradeMember::class)->createInTransaction($newAccount->getMember()->getPacket(), $pdo);
                UtilitaireSQL::update($pdo, "GradeMember", [
                    'enable' => 1,
                    'initDate' => $newAccount->getMember()->getPacket()->getFormatedDateAjout()
                ], $newAccount->getMember()->getPacket()->getId());
                //==
                
                //pv du membre
                $points = $existAcount->getPointValues();
                $pointsValues = [];
                $now = new \DateTime();
                for ($i = 0, $count = count($points); $i < $count; $i++) {
                    $pv = clone $points[$i];
                    $pv->setDateAjout($now);
                    $pv->setMember($newAccount->getMember());
                    $pv->setFoot($newAccount->getMember()->getFoot());
                    $pointsValues[] = $pv;
                }
                
                $point = new PointValue();
                $point->setDateAjout($now);
                $point->setMember($newAccount->getMember());
                $point->setGenerator($existAcount->getMember()->getPacket()->getId());
                $point->setValue($existAcount->getMember()->getPacket()->getProduct()/2);
                $point->setFoot($existAcount->getMember()->getFoot());
                
                $pointsValues[] = $point;
                $newAccount->setOperations($pointsValues);
                
                $this->getDaoManager()->getManagerOf(PointValue::class)->createAllInTransaction($pointsValues, $pdo);
                //==
                
                //pv pour les upline
                $node = $newAccount->getMember();
                while ($this->checkParent($node->getId())) {
                    $point = new PointValue();
                    $point->setDateAjout($now);
                    $point->setGenerator($newAccount->getMember()->getPacket());
                    $point->setValue($newAccount->getMember()->getPacket()->getProduct()/2);
                    $point->setFoot($node->getFoot());
                    
                    $node = $this->findParent($node->getId());
                    $point->setMember($node);
                    $this->getDaoManager()->getManagerOf(PointValue::class)->createInTransaction($point, $pdo);
                }
                //==
                
            } else {
                throw new DAOException("An error occurred while creating the transaction");
            }
            
            if (!$pdo->commit()) {
                throw new DAOException("An error occurred while closing the transaction");
            }
        } catch (\PDOException $e) {
            throw  new DAOException($e->getMessage(), DAOException::ERROR_CODE, $e);
        }
    }

    /**
     * {@inheritDoc}
     * @see \Core\Shivalik\Managers\MemberDAOManager::countByOffice()
     */
    public function countByOffice(int $officeId) : int{
        if ($this->checkByOffice($officeId)) {
            return UtilitaireSQL::count($this->getConnection(), $this->getTableName(), array('office' => $officeId));
        }
        return 0;
    }
    /**
     * {@inheritDoc}
     * @see \Core\Shivalik\Managers\MemberDAOManager::findByOffice()
     */
    public function findByOffice (int $officeId, ?int $limit = null, int $offset = 0) : array{
        return UtilitaireSQL::findAll($this->getConnection(), $this->getTableName(), $this->getMetadata()->getName(), self::FIELD_DATE_AJOUT, true, array('office' => $officeId), $limit, $offset);
    }
    
    /**
     * {@inheritDoc}
     * @see \Core\Shivalik\Managers\MemberDAOManager::checkByOffice()
     */
    public function checkByOffice (int $officeId, ?int $limit = null, int $offset = 0) : bool {
        return UtilitaireSQL::checkAll($this->getConnection(), $this->getTableName(), ['office' => $officeId], $limit, $offset);
    }
    
    
    /**
     * {@inheritDoc}
     * @see \Core\Shivalik\Managers\MemberDAOManager::checkLeftChild()
     */
    public function checkLeftChild (int $memberId) : bool{
        return $this->checkChild($memberId, Member::LEFT_FOOT);
    }
    
    /**
     * {@inheritDoc}
     * @see \Core\Shivalik\Managers\MemberDAOManager::checkRightChild()
     */
    public function checkRightChild (int $memberId) : bool{
        return $this->checkChild($memberId, Member::RIGHT_FOOT);
    }
    
    /**
     * {@inheritDoc}
     * @see \Core\Shivalik\Managers\MemberDAOManager::checkMiddelChild()
     */
    public function checkMiddelChild (int $memberId) : bool{
        return $this->checkChild($memberId, Member::MIDDEL_FOOT);
    }
    
    /**
     * {@inheritDoc}
     * @see \Core\Shivalik\Managers\MemberDAOManager::countChilds()
     */
    public function countChilds (int $memberId, ?int $foot = null) : int{
        switch ($foot){
            case Member::LEFT_FOOT : {//left
                return $this->countLeftChild($memberId);
            }break;
            
            case Member::MIDDEL_FOOT : {//middle
                return $this->countMiddleChild($memberId);
            }break;
            
            case Member::RIGHT_FOOT : {//right
                return $this->countRightChild($memberId);
            }break;
            
            default : {//to count all member
                $number = $this->countLeftChild($memberId);
                $number += $this->countMiddleChild($memberId);
                $number += $this->countRightChild($memberId);
                
                return $number;
            }
            
        }
    }
    
    /**
     * {@inheritDoc}
     * @see \Core\Shivalik\Managers\MemberDAOManager::countLeftChild()
     */
    public function countLeftChild (int $memberId) : int{
        $number = 0;
        
        if ($this->checkLeftChild($memberId)) {//s'il a un neud a gauche
            $leftChild = $this->findLeftChild($memberId);
            $number = 1;
            
            if ($this->checkChilds($leftChild->getId())) {//si le neud gauche a des afants
                $childs = $this->findChilds($leftChild->getId());
                
                foreach ($childs as $child) {//comptage des afents
                    $number++;
                    
                    if ($this->checkChilds($child->getId())) {//comptage des affant/afent
                        $number += $this->countChilds($child->getId());
                    }
                }
            }
            
        }
        
        return $number;
    }
    
    /**
     * {@inheritDoc}
     * @see \Core\Shivalik\Managers\MemberDAOManager::countMiddleChild()
     */
    public function countMiddleChild (int $memberId) : int{
        $number = 0;
        
        if ($this->checkMiddelChild($memberId)) {//s'il a un neud au centre
            $middleChild = $this->findMiddelChild($memberId);
            $number = 1;
            
            if ($this->checkChilds($middleChild->getId())) {//si le neud au centre a des afants
                $childs = $this->findChilds($middleChild->getId());
                
                foreach ($childs as $child) {//comptage des afents
                    $number++;
                    
                    if ($this->checkChilds($child->getId())) {//comptage des affant/afent
                        $number += $this->countChilds($child->getId());
                    }
                }
            }
            
        }
        return $number;
    }
    
    /**
     * {@inheritDoc}
     * @see \Core\Shivalik\Managers\MemberDAOManager::countRightChild()
     */
    public function countRightChild (int $memberId) : int{
        $number = 0;
        
        if ($this->checkRightChild($memberId)) {//s'il a un neud a droite
            $ringhtChild = $this->findRightChild($memberId);
            $number = 1;
            
            if ($this->checkChilds($ringhtChild->getId())) {//si le neud au centre a des afants
                $childs = $this->findChilds($ringhtChild->getId());
                
                foreach ($childs as $child) {//comptage des afents
                    $number++;
                    
                    if ($this->checkChilds($child->getId())) {//comptage des affant/afent
                        $number += $this->countChilds($child->getId());
                    }
                }
            }
            
        }
        
        return $number;
    }
    
    
    /**
     * {@inheritDoc}
     * @see \Core\Shivalik\Managers\MemberDAOManager::findDownlinesChilds()
     */
    public function findDownlinesChilds (int $memberId, ?int $foot = null) : array{
        
        switch ($foot){
            case Member::LEFT_FOOT : {//left
                return $this->findLeftDownlinesChilds($memberId);
            }break;
            
            case Member::MIDDEL_FOOT : {//middle
                return $this->findMiddleDownlinesChilds($memberId);
            }break;
            
            case Member::RIGHT_FOOT : {//right
                return $this->findRightDownlinesChilds($memberId);
            }break;
            
            default : {//all Member
                $members = $this->findLeftDownlinesChilds($memberId);
                $members = array_merge($members, $this->findMiddleDownlinesChilds($memberId));
                $members = array_merge($members, $this->findRightDownlinesChilds($memberId));
                return $members;
            }
            
        }
    }
    
    /**
     * {@inheritDoc}
     * @see \Core\Shivalik\Managers\MemberDAOManager::findLeftDownlinesChilds()
     */
    public function findLeftDownlinesChilds (int $memberId) : array{
        $members = array();
        
        if ($this->checkLeftChild($memberId)) {//s'il a un neud a gauche
            $leftChild = $this->findLeftChild($memberId);
            $members[] = $leftChild;
            
            if ($this->checkChilds($leftChild->getId())) {//si le neud gauche a des afants
                $childs = $this->findChilds($leftChild->getId());
                
                foreach ($childs as $child) {//comptage des afents
                    $members[] = $child;
                    if ($this->checkChilds($child->getId())) {
                        $members = array_merge($members, $this->findDownlinesChilds($child->getId()));
                    }
                }
            }
        }
        return $members;
    }
    
    /**
     * {@inheritDoc}
     * @see \Core\Shivalik\Managers\MemberDAOManager::findMiddleDownlinesChilds()
     */
    public function findMiddleDownlinesChilds (int $memberId) : array{
        $members = array();
        
        if ($this->checkMiddelChild($memberId)) {//s'il a un neud au centre
            $middleChild = $this->findMiddelChild($memberId);
            $members[] = $middleChild;
            
            if ($this->checkChilds($middleChild->getId())) {//si le neud au centre a des afants
                $childs = $this->findChilds($middleChild->getId());
                
                foreach ($childs as $child) {//comptage des afents
                    $members[] = $child;
                    
                    if ($this->checkChilds($child->getId())) {
                        $members = array_merge($members, $this->findDownlinesChilds($child->getId()));
                    }
                }
            }
            
        }
        
        return $members;
    }
    
    /**
     * {@inheritDoc}
     * @see \Core\Shivalik\Managers\MemberDAOManager::findRightDownlinesChilds()
     */
    public function findRightDownlinesChilds (int $memberId) : array{
        $members = array();
        
        if ($this->checkRightChild($memberId)) {//s'il a un neud a droite
            $ringhtChild = $this->findRightChild($memberId);
            $members[] = $ringhtChild;
            
            if ($this->checkChilds($ringhtChild->getId())) {//si le neud as des neuds afant
                $childs = $this->findChilds($ringhtChild->getId());
                
                foreach ($childs as $child) {//comptage pout tout les afant
                    $members[] = $child;
                    if ($this->checkChilds($child->getId())) {
                        $members = array_merge($members, $this->findDownlinesChilds($child->getId()));
                    }
                }
            }
        }
        
        return $members;
    }
    
    
    /**
     * {@inheritDoc}
     * @see \Core\Shivalik\Managers\MemberDAOManager::findDownlinesStacks()
     */
    public function findDownlinesStacks (int $memberId, ?int $foot = null) : array{
        
        $data = array();
        switch ($foot){
            case Member::LEFT_FOOT : {//left
                if ($this->checkLeftChild($memberId)) {
                    $data[] = $this->findLeftDownlineStack($memberId);
                }
            }break;
            
            case Member::MIDDEL_FOOT : {//middle
                if ($this->checkMiddelChild($memberId)) {
                    $data[] = $this->findMiddleDownlineStack($memberId);
                }
            }break;
            
            case Member::RIGHT_FOOT : {//right
                if ($this->checkRightChild($memberId)) {
                    $data[] = $this->getRightDownlineStack($memberId);
                }
            }break;
            
            default : {//all Member
                if ($this->checkLeftChild($memberId)) {
                    $data[] = $this->findLeftDownlineStack($memberId);
                }
                if ($this->checkMiddelChild($memberId)) {
                    $data[] = $this->findMiddleDownlineStack($memberId);
                }
                if ($this->checkRightChild($memberId)) {
                    $data[] = $this->findRightDownlineStack($memberId);
                }
            }
        }
        return $data;
    }
    
    /**
     * {@inheritDoc}
     * @see \Core\Shivalik\Managers\MemberDAOManager::findLeftDownlineStack()
     */
    public function findLeftDownlineStack (int $memberId) : Member{
        
        $leftChild = $this->findLeftChild($memberId);
        
        if ($this->checkChilds($leftChild->getId())) {//si le neud gauche a des afants
            $childs = $this->findChilds($leftChild->getId());
            
            foreach ($childs as $child) {//pour chaque noeud afant
                if ($this->checkChilds($child->getId())) {
                    $child->setChilds($this->findDownlinesStacks($child->getId()));//empilage de la methode parente
                }
            }
            
            $leftChild->setChilds($childs);
        }
        
        return $leftChild;
    }
    
    /**
     * {@inheritDoc}
     * @see \Core\Shivalik\Managers\MemberDAOManager::findMiddleDownlineStack()
     */
    public function findMiddleDownlineStack (int $memberId) : Member{
        
        $middleChild = $this->findMiddelChild($memberId);
        
        if ($this->checkChilds($middleChild->getId())) {//si le neud au centre a des afants
            $childs = $this->findChilds($middleChild->getId());
            
            foreach ($childs as $child) {//empilage des enfants -> des anfants
                if ($this->checkChilds($child->getId())) {
                    $child->setChilds($this->findDownlinesStacks($child->getId()));
                }
            }
            
            $middleChild->setChilds($childs);
        }
        
        return $middleChild;
    }
    
    /**
     * {@inheritDoc}
     * @see \Core\Shivalik\Managers\MemberDAOManager::findRightDownlineStack()
     */
    public function findRightDownlineStack (int $memberId) : Member{
        
        $ringhtChild = $this->findRightChild($memberId);
        
        if ($this->checkChilds($ringhtChild->getId())) {//si le neud as des neuds afant
            $childs = $this->findChilds($ringhtChild->getId());
            
            foreach ($childs as $child) {
                if ($this->checkChilds($child->getId())) {
                    $child->setChilds($this->findDownlinesStacks($child->getId()));
                }
            }
            
            $ringhtChild->setChilds($childs);
        }
        
        return $ringhtChild;
    }
    
    
    
    /**
     * {@inheritDoc}
     * @see \Core\Shivalik\Managers\MemberDAOManager::findParent()
     */
    public function findParent (int $memberId) : Member{
        if ($this->checkParent($memberId)) {
            /**
             * @var Member $member
             */
            $member = $this->findById($memberId, false);
            return $this->findById($member->getParent()->getId());
        }
        
        throw new DAOException("this node does not have a parent node");
    }
    
    /**
     * {@inheritDoc}
     * @see \Core\Shivalik\Managers\MemberDAOManager::findSponsor()
     */
    public function findSponsor (int $memberId) : Member{
        if ($this->checkSponsor($memberId)) {
            /**
             * @var Member $member
             */
            $member = $this->findById($memberId, false);
            return $this->findById($member->getSponsor()->getId());
        }
        
        throw new DAOException("this node does not have a sponsor node");
    }
    
    /**
     * {@inheritDoc}
     * @see \Core\Shivalik\Managers\MemberDAOManager::findChilds()
     */
    public function findChilds (int $memberId) : array{
        return UtilitaireSQL::findAll($this->getConnection(), $this->getTableName(), $this->getMetadata()->getName(), "foot", true, ['parent' => $memberId]);
    }
    
    /**
     * {@inheritDoc}
     * @see \Core\Shivalik\Managers\MemberDAOManager::findLeftChild()
     */
    public function findLeftChild (int $memberId) : Member{
        return $this->findChild($memberId, Member::LEFT_FOOT);
    }
    
    /**
     * {@inheritDoc}
     * @see \Core\Shivalik\Managers\MemberDAOManager::findRightChild()
     */
    public function findRightChild (int $memberId) : Member{
        return $this->findChild($memberId, Member::RIGHT_FOOT);
    }
    
    /**
     * {@inheritDoc}
     * @see \Core\Shivalik\Managers\MemberDAOManager::findMiddelChild()
     */
    public function findMiddelChild (int $memberId) : Member{
        return $this->findChild($memberId, Member::MIDDEL_FOOT);
    }
    
    /**
     * {@inheritDoc}
     * @see \Core\Shivalik\Managers\MemberDAOManager::updateMatricule()
     */
    public function updateMatricule (string $matricule, int $memberId) : void {
        UtilitaireSQL::update($this->getConnection(), $this->getTableName(), array('matricule' => $matricule), $memberId);
    }
    
    /**
     * {@inheritDoc}
     * @see \Core\Shivalik\Managers\MemberDAOManager::checkByMatricule()
     */
    public function checkByMatricule (string $matricule, ?int $id = null) : bool {
        return $this->columnValueExist('matricule', $matricule, $id);
    }
    
    /**
     * {@inheritDoc}
     * @see \Core\Shivalik\Managers\MemberDAOManager::findByMatricule()
     */
    public function findByMatricule (string $matricule) : Member {
        return UtilitaireSQL::findUnique($this->getConnection(), $this->getTableName(), $this->getMetadata()->getName(), "matricule", $matricule);
    }
    
    /**
     * {@inheritDoc}
     * @see \Core\Shivalik\Managers\MemberDAOManager::checkCreationHistoryByOffice()
     */
    public function checkCreationHistoryByOffice (int $officeId, \DateTime $dateMin, \DateTime $dateMax = null, ?int $limit = null, int $offset= 0) : bool {
        return UtilitaireSQL::hasCreationHistory($this->getConnection(), $this->getTableName(), self::FIELD_DATE_AJOUT, true, $dateMin, $dateMax, ['office' => $officeId], $limit, $offset);
    }
    
    /**
     * {@inheritDoc}
     * @see \Core\Shivalik\Managers\MemberDAOManager::findCreationHistoryByOffice()
     */
    public function findCreationHistoryByOffice (int $officeId, \DateTime $dateMin, \DateTime $dateMax = null, ?int $limit = null, int $offset= 0) : array {
        return UtilitaireSQL::findCreationHistory($this->getConnection(), $this->getTableName(), $this->getMetadata()->getName(), self::FIELD_DATE_AJOUT, true, $dateMin, $dateMax, ['office' => $officeId], $limit, $offset);
    }
    
    /**
     * {@inheritDoc}
     * @see \Core\Shivalik\Managers\MemberDAOManager::countCreationHistoryByOffice()
     */
    public function countCreationHistoryByOffice (int $officeId, \DateTime $dateMin, \DateTime $dateMax = null) : int{
        return 0;
    }

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
        return UtilitaireSQL::checkAll(
            $this->getConnection(),
            $this->getTableName(),
            ['parent' => $memberId, 'foot' => $foot]
        );
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
            $statement = $this->getConnection()->prepare("SELECT id FROM {$this->getTableName()} WHERE id=:id AND parent IS NOT NULL");
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
     * @see \PHPBackend\Dao\DefaultDAOInterface::createInTransaction()
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
    
    /**
     * {@inheritDoc}
     * @see \Core\Shivalik\Managers\MemberDAOManager::search()
     */
    public function search($indexs) : array
    {
        $data = [];
        $indexs = is_array($indexs)? $indexs : [$indexs];
        $restruct = [];//conserve le identifiant des membres deja selectionner, pour faciliter la restruction pour les requettes suiventes
        $selectedIndex = [];//les indexs qui doivement etre utiliser dans la suite, apres selection selon lematricule et le pseudo de connexion
        foreach ($indexs as $index) {//premier niveau de recherche, pour est elements uniques
            $partiels = [];
            if ($this->checkByMatricule($index)) {
                $partiels[] = $this->findByMatricule($index);
            }
            
            if ($this->checkByPseudo($index)) {
                $partiels[] = $this->findByPseudo($index);
            }
            
            if (empty($partiels)) {//s'il on n'a rien eu pour la selection prioritarie
                $selectedIndex[] = $index;
                continue;
            }
            
            foreach ($partiels as $user) {
                if (!$this->inArray($data, $user)) {
                    $data [] = $user;
                    $restruct[] = $user->getId();
                }
            }
        }
        
        if (!empty($selectedIndex)) {
            $sql1 = "SELECT DISTINCT * FROM {$this->getTableName()} WHERE ";//deuxieme categorie de recherche, pour le index qui correpond exactement au nom des utilisateur
            $sql2 = $sql1;//et au finish, recherche aproximative
            
            $sqlParams = [];
            $sql2Params = [];
            
            $sqlContent = ["", "", ""];
            $sql2Content = ["", "", ""];
            
            $max = count($selectedIndex);
            foreach ($selectedIndex as $key => $index) {
                $last = $max == ($key+1);
                
                $sqlContent[0] .= ":name{$key}".($last? '':', ');
                $sqlContent[1] .= ":postName{$key}".($last? '':', ');
                $sqlContent[2] .= ":lastName{$key}".($last? '':', ');
                
                $sql2Content[0] .= "name LIKE :name{$key}".($last? '':' OR ');
                $sql2Content[1] .= "postName LIKE :postName{$key}".($last? '':' OR ');
                $sql2Content[2] .= "lastName LIKE :lastName{$key}".($last? '':' OR ');
                
                $sqlParams["name{$key}"] = strtoupper($index);
                $sqlParams["postName{$key}"] = strtoupper($index);
                $sqlParams["lastName{$key}"] = $index;
                
                $sql2Params["name{$key}"] = strtoupper("{$index}%");
                $sql2Params["postName{$key}"] = strtoupper("{$index}%");
                $sql2Params["lastName{$key}"] = "{$index}%";
            }
            
            $sql1 .= "((name IN ({$sqlContent[0]})) OR (postName IN({$sqlContent[1]})) OR (lastName IN ({$sqlContent[2]})))";
            $sql2 .= " ({$sql2Content[0]}) OR ({$sql2Content[1]}) OR ({$sql2Content[2]})";
            
            if (!empty($restruct)) {
                $sql1 .= " AND (id NOT IN (".implode(", ", $restruct)."))";
            }
            
            $sql1 .= "  ORDER BY name ";
            
            try {
                $pdo = $this->getConnection();
                $statement = UtilitaireSQL::prepareStatement($pdo, $sql1, $sqlParams);
                
                while ($row = $statement->fetch()) {
                    $member = new Member($row);
                    $data[] = $member;
                    $restruct[] = $member->getId();
                }
                $statement->closeCursor();
                
            
                if (!empty($restruct) ) {//pour faire une restruction aux donnees deja selectionner parmis ceux qui sont plus prioritaire                    
                    $not = implode(", ", $restruct);
                    $sql2 .= " AND (id NOT IN ({$not}))";
                }
                
                $sql2 .= " ORDER BY name";
                $statement2 = UtilitaireSQL::prepareStatement($pdo, $sql2, $sql2Params);
                while ($row = $statement2->fetch()) {
                    $data[] = new Member($row);
                }
                $statement2->closeCursor();
            } catch (\PDOException $e) {
                throw  new DAOException($e->getMessage()." => {$sql1}", DAOException::ERROR_CODE, $e);
            }
            
        }
        
        
        if (empty($data)) {
            throw new DAOException("Sorry. No results for the search index. Please rephrase your search index(es)");
        }
        
        return $data;
    }
    
    /**
     * Est-ce que cette utilisateur existe dans cette collection des utilisateurs??
     * @param Member[] $members
     * @param Member $member
     * @return bool
     */
    private function inArray (array $members, Member $member) : bool {
        foreach ($members as $m) {
            if ($m->getId() == $member->getId()) {
                return true;
            }
        }
        
        return false;
    }
    
    
    

}

