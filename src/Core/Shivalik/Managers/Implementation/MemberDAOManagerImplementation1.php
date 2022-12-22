<?php
namespace Core\Shivalik\Managers\Implementation;


use Core\Shivalik\Entities\Account;
use Core\Shivalik\Entities\BonusGeneration;
use Core\Shivalik\Entities\GradeMember;
use Core\Shivalik\Entities\Localisation;
use Core\Shivalik\Entities\Member;
use Core\Shivalik\Entities\OfficeBonus;
use Core\Shivalik\Entities\PointValue;
use Core\Shivalik\Entities\PurchaseBonus;
use Core\Shivalik\Entities\Withdrawal;
use Core\Shivalik\Managers\GradeMemberDAOManager;
use Core\Shivalik\Managers\MemberDAOManager;
use Core\Shivalik\Managers\PointValueDAOManager;
use DateTime;
use PDOException;
use PHPBackend\Dao\DAOEvent;
use PHPBackend\Dao\DAOException;
use PHPBackend\Dao\UtilitaireSQL;

/**
 *
 * @author Esaie MHS
 *        
 */
class MemberDAOManagerImplementation1 extends AbstractUserDAOManager implements MemberDAOManager
{
    const OPERATIONS_ENTITIES = [PointValue::class, BonusGeneration::class, OfficeBonus::class, Withdrawal::class, PurchaseBonus::class];
    /**
     * {@inheritDoc}
     * @deprecated 2.0
     */
    public function loadAccount ($member, bool $calcul = true) : Account {
        $account = new Account(($member instanceof Member)? $member : $this->findById($member));
        
        foreach (self::OPERATIONS_ENTITIES as $dao) {
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

        if($this->checkParent($account->getMember()->getId())) {
            /**
             * @var Member $parent
             */
            $parent = $this->findById($member->getParent()->getId());
            $parent->setPacket($this->getManagerFactory()->getManagerOf(GradeMember::class)->findCurrentByMember($parent->getId()));
            $account->getMember()->setParent($parent);
        }
        if($this->checkSponsor($account->getMember()->getId())) {
            /**
             * @var Member $sponsor
             */
            $sponsor = $this->findById($member->getSponsor()->getId());
            $sponsor->setPacket($this->getManagerFactory()->getManagerOf(GradeMember::class)->findCurrentByMember($sponsor->getId()));
            $account->getMember()->setSponsor($sponsor);

        }
        
        return $account;
    }
    
    
    /**
     * {@inheritDoc}
     * @see \Core\Shivalik\Managers\MemberDAOManager::changeParentByMember()
     */
    public function changeParentByMember(int $id, int $newParent): void {
        /**
         * @var Member $oldParent
         */
        $oldParent = $this->findParent($id);

        /**
         * @var Member $parent
         */
        $parent = $this->findById($newParent);

        /**
         * @var Member $member
         */
        $member = $this->findById($id);
        
        if (!$this->isUplineOf($member->getSponsor()->getId(), $newParent) || $oldParent->getId() == $newParent
            || $this->countDirectChilds($newParent) == 3 || $this->checkChilds($id)) {
            throw new DAOException("you cannot perform this operation (reseau)");
        }
        
        try {
            $pdo = $this->getConnection();
            
            if (!$pdo->beginTransaction()) {
                throw new DAOException("An error occurred while creating the transaction");
            }
            
            $member->setFoot(null);
            
            for ($i = 1; $i <= 3; $i++) {
                if (!$this->checkChild($newParent, $i)) {
                    $member->setFoot($i);
                    break;
                }
            }
            
            UtilitaireSQL::update($pdo, $this->getTableName(), [
                'foot' => $member->getFoot(),
                'parent' => $parent->getId()
            ], $id);
            
            /**
             * @var GradeMember[] $packets
             */
            $packets = $this->getDaoManager()->getManagerOf(GradeMember::class)->findByMember($id);
            foreach ($packets as $pack) {
                /**
                 * @var PointValue[] $points
                 */
                $points = $this->getDaoManager()->getManagerOf(PointValue::class)->findByGenerator($pack->getId());
                $ids = [];
                foreach ($points as $p) {
                    $ids[] = $p->getId();
                }
                
                if (empty($ids)) {
                    throw new DAOException("imposible to perform this operation");
                }
                
                $count = UtilitaireSQL::deleteAll($pdo, "PointValue", $ids);
                if($count == 0){
                    throw new DAOException("imposible to perform this operation because ");
                }
                
                $child = $member;
                while ($this->checkParent($child->getId())) {
                    $foot = $child->getFoot();
                    $child = $this->findParent($child->getId());
                    
                    $pv = new PointValue();
                    $pv->setMember($child);
                    $pv->setGenerator($pack);
                    $pv->setFoot($foot);
                    
                    $value = round(($pack->getProduct()/2), 0);
                    $pv->setValue($value);
                    $this->getDaoManager()->getManagerOf(PointValue::class)->createInTransaction($pv, $pdo);
                }
            }
            
            if(!$pdo->commit()) {
                throw new DAOException("An error occurred while closing the transaction");
            }
        } catch (\PDOException $e) {
            throw new DAOException($e->getMessage(), DAOException::ERROR_CODE, $e);
        }
        
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

    public function migrateToNetwork(Member $node, Member $newParent, ?Member $newSponsor = null): void
    {
        try {
            /**
             * Dans le cas où le sponsor est renseiger, on cherche le troue le plus 
             * proche dans le reseau du sponsor.
             */
            
            if ($newSponsor != null) {
                //recherche du nouveau parent
                /** @var Member $parent */
                $parent = $newSponsor;
                while ($this->countDirectChilds($parent->getId()) == 3) {
                    $childs = $this->findChilds($parent->getId());
    
                    $breack = false;
    
                    //pour chaque noeud du parent, on cherche un vide.
                    foreach ($childs as $child) {
    
                        $count = $this->countDirectChilds($child->getId());
                        if ($count != 3) {
                            //verification du pied disponible
                            $parent = $child;
                            $breack = true;
                            break;
                        }
    
                        $parent = $child;
                    }
    
                    if ($breack) {
                        break;
                    }
                }            
                //==
    
                $pdo = $this->getConnection();
                if(!$pdo->beginTransaction()) {
                    throw new DAOException("Une erreur est survenue lors du demarrage de la transaction", 500);
                }

                $now = new DateTime();
    
                //suppression des point valeurs
                //-------------------------------
    
                /** @var PointValueDAOManagerImplementation1 $pointDao */
                $pointDao = $this->getManagerFactory()->getManagerOf(PointValue::class);
                /** @var GradeMemberDAOManager $packetDao */
                $packetDao = $this->getManagerFactory()->getManagerOf(GradeMember::class);

                /** @var PointValue[] $points */
                $points = $pointDao->findByMember($node->getId());
                $deletablePoints = [];
                foreach ($points as $point) {
                    $byGenerator = $pointDao->findByGenerator($point->getGenerator()->getId());
                    foreach ($byGenerator as $p) {
                        if ($p->getId() != $point->getId()) {
                            $deletablePoints[] = $p->getId();
                        }
                    }
                }

                $packerts = $packetDao->findByMember($node->getId());
                $otherPoints = [];//le point de surplus au upline du compte $node
                
                foreach ($packerts as $pack) {
                    $additionnalPoints = $pointDao->findByGenerator($pack->getId());
                    foreach ($additionnalPoints as $p) {
                        $deletablePoints[] = $p->getId();
                    }
                    $otherPoints[] = $additionnalPoints[0];
                }

                //recherche du pied disponible
                $foot = $this->findAvailableFoot($parent->getId());
                $node->setFoot($foot);

                $pointDao->deleteAllInTransaction($pdo, $deletablePoints);//suppression definitive des autres points

                $parentNode = $parent;
                $childNode = $node;
                do {
                    foreach ($points as $point) {
                        $copy = clone $point;
                        $copy->setFoot($childNode->getFoot());
                        $copy->setDateAjout($now);
                        $copy->setMember($parentNode);
                        $pointDao->createInTransaction($copy, $pdo);
                    }
                    
                    //plus les points du compte $node
                    foreach ($otherPoints as $point) {
                        $copy = clone $point;
                        $copy->setMember($parentNode);
                        $copy->setFoot($childNode->getFoot());
                        $copy->setDateAjout($now);
                        $pointDao->createInTransaction($copy, $pdo);
                    }
                    $childNode = $parentNode;
                    $parentNode = $this->checkParent($parentNode->getId()) ? $this->findParent($parentNode->getId()) : null;
                    
                } while ($parentNode  != null);
                //==
    
                $node->setFoot($foot);
                $node->setParent($parent);
                $node->setSponsor($newSponsor);
                $node->setDateModif($now);
                UtilitaireSQL::update($pdo, $this->getTableName(), [
                    'parent' => $node->getParent()->getId(),
                    'sponsor' => $node->getSponsor()->getId(),
                    'foot' => $node->getFoot(),
                    self::FIELD_DATE_MODIF => $node->getDateModif()->format('Y-m-d H:i:s')
                ], $node->getId());
                //==

                if  (!$pdo->commit()) {
                    throw new DAOException("Une erreur est survenue lors du commit de la transaction ");
                }
            } else {
                throw new DAOException("Operation non pris en charge");
            }
        } catch (PDOException $e) {
            throw new DAOException("une erreur est survenue lors de la migration du compte: {$e->getMessage()}", 500, $e);
        }
    }

    public function regeneratePointsByDownlines(Member $node): void
    {
        /** @var GradeMemberDAOManager */
        $packetDao = $this->getManagerFactory()->getManagerOf(GradeMember::class);
        /** @var PointValueDAOManagerImplementation1 */
        $pointDao = $this->getManagerFactory()->getManagerOf(PointValue::class);

        /** @var Member[] */
        $downlines = $this->findDownlinesChilds($node->getId());

        $now = new DateTime();
        try {
            $pdo = $this->getConnection();
            if (!$pdo->beginTransaction()) {
                throw new DAOException("Une erreur est survenue lors du demarrage de la transaction");
            }

            foreach ($downlines as $downline) {
                $childs = $this->findDownlinesChilds($downline->getId());
                foreach ($childs as $child) {
                    $packerts = $packetDao->findByMember($child->getId());
    
                    foreach ($packerts as $packert) {
                        if (!$pointDao->checkByGenerator($packert->getId(), $downline->getId())) {//si les points n'existe pas, alors on le cree
                            $point = new PointValue();
                            $point->setMember($downline);
                            $point->setAmount($packert->getProduct() / 2);
                            $point->setGenerator($packert);
                            $point->setDateAjout($now);
                            $point->setFoot($this->findBindingFoot($child->getId(), $downline->getId()));
                            $pointDao->createInTransaction($point, $pdo);
                        }
                    }
                }
            }

            if (!$pdo->commit()) {
                throw new DAOException("Une erreur est survenue lors de la validation de la transaction");
            }
        } catch (\PDOException $e) {
            throw new DAOException("Une erreur est survenue lors de la regenreation des PVs des comptes downline du {$node->getMatricule()}: {$e->getMessage()}", 500, $e);
        }

    }

    public function findBindingFoot(int $nodeKey, int $parentKey): int
    {
        $parent = $this->findById($parentKey);

        //on verifie si $nodeKey ne fais pas partie des noeud directes.
        if ($this->checkChilds($parentKey)) {

            $childs  = $this->findChilds($parentKey);
            foreach ($childs as $child) {
                if ($child->getId() == $nodeKey) {
                    return $child->getFoot();
                }
            }

            $node = $this->findById($nodeKey);
            $parentNode = $node;
    
            while ($this->checkParent($parentNode->getId())) {
                $parentNode = $this->findParent($parentNode->getId());
                if ($parentNode->getParent()->getId() == $parent->getId()) {
                    return $parentNode->getFoot();
                }
            }
        }

        throw new DAOException("Le compte {$nodeKey} n'est pas dans le meme reseau que le compte {$parentKey}");
    }

    public function findAvailableFoot(int $memberId): ?int
    {
        $childs = $this->checkChilds($memberId) ? $this->findChilds($memberId) : null; 
        $foot = null;
        if ($childs == null) {
            $foot = Member::LEFT_FOOT;
        } else  {
            $left = false;
            $right = false;
            $middle = false;

            foreach ($childs as $child) {
                if ($child->getFoot() == Member::LEFT_FOOT) {
                    $left = true;
                }else if ($child->getFoot() == Member::MIDDEL_FOOT) {
                    $middle = true;
                } else if ($child->getFoot() == Member::RIGHT_FOOT) {
                    $right = true;
                }
            }

            if (!$left) {
                $foot = Member::LEFT_FOOT;
            } else if (!$middle) {
                $foot = Member::MIDDEL_FOOT;
            } else if (!$right) {
                $foot = Member::RIGHT_FOOT;
            }
        }
        return $foot;
    }

    /**
     * {@inheritDoc}
     *
     * @return boolean
     * @throws DAOException
     */
    public function checkUpTree(): bool
    {
        throw new DAOException("no implementation of whole network tree verification algorithms");
    }

    /**
     * {@inheritDoc}
     *
     * @return void
     * @throws DAOException
     */
    public function makeTreeSafy(): void
    {
        throw new DAOException("no implementation of whole network tree correction algorithms");
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
                    $data[] = $this->findRightDownlineStack($memberId);
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
     * @return Member[]
     */
    public function findChilds (int $memberId) : array{
        return UtilitaireSQL::findAll($this->getConnection(), $this->getViewName(), $this->getMetadata()->getName(), "foot", true, ['parent' => $memberId]);
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
    public function findChild(int $memberId, ?int $foot=null): Member {
        $child = null;
        try {
            $statement = $this->getConnection()->prepare("SELECT * FROM {$this->getViewName()} WHERE parent=:parent AND foot=:foot");
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
            $statement = $this->getConnection()->prepare("SELECT id FROM {$this->getTableName()} WHERE id=:id AND sponsor IS NOT NULL");
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
            'pseudo' => $entity->getPseudo(),
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
                $member = $this->findByMatricule($index);
                $partiels[] = $member;
            }
            
            if ($this->checkByPseudo($index)) {
                $member = $this->findByPseudo($index);
                $partiels[] = $member;
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
                    $member = new Member($row);
                    $member->setParent(null);
                    $member->setSponsor(null);
                    $data[] = $member;
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
     * {@inheritDoc}
     * @see \Core\Shivalik\Managers\MemberDAOManager::checkSponsorizedByMember()
     */
    public function checkSponsorizedByMember(int $id, ?int $limit = null, int $offset = 0): bool
    {
        return UtilitaireSQL::checkAll($this->getConnection(), $this->getTableName(), [
            'sponsor' => $id
        ], $limit, $offset);
    }

    /**
     * {@inheritDoc}
     * @see \Core\Shivalik\Managers\MemberDAOManager::countDirectChilds()
     */
    public function countDirectChilds(int $id): int {
        return UtilitaireSQL::count($this->getConnection(), $this->getTableName(), ['parent' => $id]);
    }

    /**
     * {@inheritDoc}
     * @see \Core\Shivalik\Managers\MemberDAOManager::countSponsorizedByMember()
     */
    public function countSponsorizedByMember(int $id): int {
        return UtilitaireSQL::count($this->getConnection(), $this->getTableName(), ['sponsor' => $id]);
    }

    /**
     * {@inheritDoc}
     * @see \Core\Shivalik\Managers\MemberDAOManager::findSponsorizedByMember()
     */
    public function findSponsorizedByMember(int $id, ?int $limit = null, int $offset = 0): array {
        return UtilitaireSQL::findAll($this->getConnection(), $this->getViewName(), 
            $this->getMetadata()->getName(), self::FIELD_DATE_AJOUT, true, ['sponsor' => $id], $limit, $offset);
    }

    /**
     * {@inheritDoc}
     * @see \Core\Shivalik\Managers\MemberDAOManager::isUplineOf()
     */
    public function isUplineOf (int $uplineMember, int $downlineMember): bool {
        if ($uplineMember == $downlineMember) {
            return true;
        }
        
        if ($this->checkChilds($uplineMember)) {
            $childs = $this->findChilds($uplineMember);
            foreach ($childs as $ch) {
                if ($ch->getId() == $downlineMember) {
                    return true;
                }
            }
            
            foreach ($childs as $ch) {
                if($this->isUplineOf($ch->getId(), $downlineMember)) {
                    return true;
                }
            }
        }
        
        return false;
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

    protected function hasView(): bool
    {
        return true;
    }

    protected function getViewName(): string
    {
        return "V_Account";
    } 

    /**
     * {@inheritDoc}
     */
    public function getSumAllAllAvailable(bool $structMode = false): float
    {
        if($structMode){
            $struct = "- SUM(withdrawalsRequest)";
        } else {
            $struct = "";
        }
        $sql = "SELECT (SUM(soldOfficeBonus) + SUM(soldGeneration) + SUM(purchaseBonus) - SUM(withdrawals)) {$struct} AS amount FROM {$this->getViewName()}";
        $amount = 0;
        try {
            $statement = UtilitaireSQL::prepareStatement($this->getConnection(), $sql, []);
            if($row = $statement->fetch()) {
                $amount = $row['amount'];
            }
            $statement->closeCursor();
        } catch (\PDOException $e) {
            throw new DAOException($e->getMessage(), DAOException::ERROR_CODE, $e);
        }
        return $amount;
    }

}

