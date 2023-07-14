<?php
namespace Core\Shivalik\Entities;

use PHPBackend\PHPBackendException;
use PHPBackend\Image2D\Mlm\DefaultNodeIcon;
use PHPBackend\Image2D\Mlm\Ternary\TernaryNode;
use PHPBackend\Image2D\Mlm\Node;

/**
 *
 * @author Esaie MHS
 *
 */
class Member extends User implements TernaryNode
{

    const LEFT_FOOT = 1;
    const MIDDEL_FOOT = 2;
    const RIGHT_FOOT = 3;

    /**
     * @var string
     */
    protected $matricule;

    /**
     * @var Member
     */
    protected $parent;

    /**
     * @var Member
     */
    protected $sponsor;

    /**
     * @var int
     */
    protected $foot;

    /**
     * @var OfficeAdmin
     */
    protected $admin;

    /**
     * @var Office
     */
    protected $office;

    /**
     * Pour les utilisateurs qui ont des offices
     * @var Office
     */
    protected $officeAccount;

    /**
     * contiens l'actuel packet de l'utilisateur
     * @var GradeMember
     */
    protected $packet;

    /**
     * @var Member[]
     */
    protected $childs = [];

    //==================================\\
    //      etats du compte du compte   \\
    //==================================\\
    /**
     * @var double
     */
    protected $withdrawals;

    /**
     * @var double
     */
    protected $withdrawalsRequest;

    /**
     * Le solde bonus office pour ceux qui en ont
     * @var float
     */
    protected $soldOfficeBonus;

    /**
     * Solde du compte principale (parainage)
     * @var float
     */
    protected $soldGeneration;

    /**
     * @var float
     */
    protected $purchaseBonus;//bonus de reachat

    /**
     * @var float
     */
    protected $particularBonus;//bonus de sensibilisateurs

    /**
     * @var boolean
     */
    protected $particularOperation;//le compte doit-elle avoir des operations partitculer??

    /**
     * @var float
     */
    protected $leftMembershipPv = 0;

    /**
     * @var float
     */
    protected $rightMembershipPv = 0;

    /**
     * @var float
     */
    protected $middleMembershipPv = 0;

    /**
     * efforts peronnels pour le re-acat
     *
     * @var float
     */
    protected $personalMembershipPv = 0;

    /**
     * @var float
     */
    protected $leftProductPv = 0;

    /**
     * @var float
     */
    protected $rightProductPv = 0;

    /**
     * @var float
     */
    protected $middleProductPv = 0;

    /**
     * effort personnel lors d'achat des produits
     * @var number
     */
    protected $personalProductPv;
    //===========================\\
    //============||=============\\
    //===========================\\

    /**
     * @return GradeMember
     */
    public function getPacket() : ?GradeMember
    {
        return $this->packet;
    }

    /**
     * @param GradeMember $packet
     */
    public function setPacket($packet) : void
    {
        if ($packet == null || $packet instanceof GradeMember) {
            $this->packet = $packet;
        } else if (self::isInt($packet)) {
            $this->packet = new GradeMember(array('id' => $packet));
        } else {
            throw new PHPBackendException("invalide param valeur in setPacket method");
        }
    }

    /**
     * @return string
     */
    public function getMatricule() : ?string
    {
        return $this->matricule;
    }

    /**
     * {@inheritDoc}
     * @return Member
     */
    public function getSponsor() : ?Node
    {
        return $this->sponsor;
    }

    /**
     * @param string $matricule
     */
    public function setMatricule($matricule) : void
    {
        $this->matricule = $matricule;
    }

    /**
     * @param Member|int $parent
     */
    public function setParent($parent) : void
    {
        if ($this->isInt($parent)) {
            $this->parent = new Member(array('id'=>$parent));
        }elseif ($parent instanceof Member || $parent == null){
            $this->parent = $parent;
        }else {
            throw new PHPBackendException("invalid param value");
        }
    }

    /**
     * @param Member"int $sponsor
     */
    public function setSponsor($sponsor) : void
    {
        if ($this->isInt($sponsor)) {
            $this->sponsor = new Member(array('id'=>$sponsor));
        }elseif ($sponsor instanceof Member || $sponsor == null){
            $this->sponsor = $sponsor;
        }else {
            throw new PHPBackendException("invalid param value");
        }
    }

    /**
     * @param int|null $foot
     */
    public function setFoot($foot) : void
    {
        $this->foot = $foot;
    }
    /**
     * @return OfficeAdmin
     */
    public function getAdmin() : ?OfficeAdmin
    {
        return $this->admin;
    }

    /**
     * @return Office
     */
    public function getOffice() : ?Office
    {
        return $this->office;
    }

    /**
     * @param OfficeAdmin $admin
     */
    public function setAdmin($admin) : void
    {
        if ($admin == null || $admin instanceof OfficeAdmin) {
            $this->admin = $admin;
        }elseif ($this->isInt($admin)) {
            $this->admin = new OfficeAdmin(array('id' => $admin));
        }else{
            throw new PHPBackendException("invalid value in param of method setAdmin");
        }
    }

    /**
     * @param Office $office
     */
    public function setOffice($office) : void
    {
        if ($office == null || $office instanceof Office) {
            $this->office = $office;
        }elseif ($this->isInt($office)) {
            $this->office = new Office(array('id' => $office));
        }else{
            throw new PHPBackendException("invalid value in param of method setOffice");
        }
    }

    /**
     * @return Office
     */
    public function getOfficeAccount() : ?Office
    {
        return $this->officeAccount;
    }

    /**
     * @param Office $officeAccount
     */
    public function setOfficeAccount($officeAccount)
    {
        if ($officeAccount == null || $officeAccount instanceof Office) {
            $this->officeAccount = $officeAccount;
        }elseif ($this->isInt($officeAccount)) {
            $this->officeAccount = new Office(array('id' => $officeAccount));
        }else{
            throw new PHPBackendException("invalid value in param of method setOffice");
        }
    }

    /**
     * @return string|NULL
     */
    public function generateMatricule () : ?string{
        $name = $this->getName() != null ? $this->getName() : ($this->getPostName() != null ? $this->getPostName() : $this->getLastName());
        if ($name == null) {
            return null;
        }

        $matricule = strtoupper(substr(trim($name), 0, 1)).$this->id;//.strtoupper(substr($this->getLastName(), 0, 1));
        $this->setMatricule($matricule);
        return $matricule;
    }

    /**
     * @param Member[]  $childs
     */
    public function setChilds(array $childs) : void
    {
        $this->childs = $childs;
    }

    /**
     * @return int
     */
    public function getFoot() : ?int
    {
        return $this->foot;
    }

    /**
     * {@inheritDoc}
     * @return Member
     */
    public function getParent () : ?Node
    {
        return $this->parent;
    }

    /**
     * {@inheritDoc}
     * @see \PHPBackend\Image2D\Mlm\Node::getChilds()
     */
    public function getChilds()
    {
        return $this->childs;
    }

    /**
     * {@inheritDoc}
     * @see \PHPBackend\Image2D\Mlm\Ternary\TernaryNode::getLeftChild()
     */
    public function getLeftChild() : ?TernaryNode
    {
        return $this->getChild(self::LEFT_FOOT);
    }

    /**
     * {@inheritDoc}
     * @see \PHPBackend\Image2D\Mlm\Ternary\TernaryNode::getMiddleChild()
     */
    public function getMiddleChild() : ?TernaryNode
    {
        return $this->getChild(self::MIDDEL_FOOT);
    }

    /**
     * {@inheritDoc}
     * @see \PHPBackend\Image2D\Mlm\Ternary\TernaryNode::getRightChild()
     */
    public function getRightChild() : ?TernaryNode
    {
        return $this->getChild(self::RIGHT_FOOT);
    }

    /**
     * {@inheritDoc}
     * @see \PHPBackend\Image2D\Mlm\Ternary\TernaryNode::hasLeftChild()
     */
    public function hasLeftChild(): bool
    {
        return $this->hasChild(self::LEFT_FOOT);
    }

    /**
     * {@inheritDoc}
     * @see \PHPBackend\Image2D\Mlm\Ternary\TernaryNode::hasMiddleChild()
     */
    public function hasMiddleChild(): bool
    {
        return $this->hasChild(self::MIDDEL_FOOT);
    }

    /**
     * {@inheritDoc}
     * @see \PHPBackend\Image2D\Mlm\Ternary\TernaryNode::hasRightChild()
     */
    public function hasRightChild(): bool
    {
        return $this->hasChild(self::RIGHT_FOOT);
    }

    /**
     * {@inheritDoc}
     * @see \PHPBackend\Image2D\Mlm\Node::countChilds()
     */
    public function countChilds(): int
    {
        return count($this->getChilds());
    }

    /**
     * {@inheritDoc}
     * @see \PHPBackend\Image2D\Mlm\Node::getChild()
     */
    public function getChild(int $foot) : ?Node
    {
        foreach ($this->getChilds() as $child) {
            if ($child->getFoot() == $foot) {
                return $child;
            }
        }

        throw new PHPBackendException("no child node at foot {$foot} in {$this->getNodeName()} node");
    }

    /**
     * {@inheritDoc}
     * @see \Core\Shivalik\Entities\User::getData()
     */
    public function getData()
    {
        return $this;
    }

    /**
     * {@inheritDoc}
     * @see \PHPBackend\Image2D\Mlm\Node::getIcon()
     */
    public function getIcon()
    {
        return new DefaultNodeIcon($this->getPhoto());
    }

    /**
     * {@inheritDoc}
     * @see \PHPBackend\Image2D\Mlm\Node::getNodeName()
     */
    public function getNodeName(): string
    {
        return $this->getFullName();
    }

    /**
     * {@inheritDoc}
     * @see \PHPBackend\Image2D\Mlm\Node::hasChild()
     */
    public function hasChild(int $foot): bool
    {
        foreach ($this->getChilds() as $child) {
            if ($child->getFoot() == $foot) {
                return true;
            }
        }
        return false;
    }

    /**
     * {@inheritDoc}
     * @see \PHPBackend\Image2D\Mlm\Node::hasChilds()
     */
    public function hasChilds(): bool
    {
        return !empty($this->getChilds());
    }

    /**
     * {@inheritDoc}
     * @see \PHPBackend\Image2D\Mlm\Node::hasIcon()
     */
    public function hasIcon(): bool
    {
        return ($this->getPhoto() != null);
    }

    /**
     * {@inheritDoc}
     * @see \PHPBackend\Image2D\Mlm\Node::hasParent()
     */
    public function hasParent(): bool
    {
        return ($this->parent != null);
    }

    /**
     * {@inheritDoc}
     * @see \PHPBackend\Image2D\Mlm\Node::isRoot()
     */
    public function isRoot(): bool
    {
        return !$this->hasParent();
    }

    /**
     * {@inheritDoc}
     * @see \PHPBackend\Image2D\Mlm\Ternary\TernaryNode::isLeftChild()
     */
    public function isLeftChild($node): bool
    {
        return (($node instanceof Member) && $this->hasLeftChild() && $node->getId() == $this->getLeftChild()->getId());
    }

    /**
     * {@inheritDoc}
     * @see \PHPBackend\Image2D\Mlm\Ternary\TernaryNode::isMiddleChild()
     */
    public function isMiddleChild($node): bool
    {
        return (($node instanceof Member) && $this->hasMiddleChild() && $node->getId() == $this->getMiddleChild()->getId());
    }

    /**
     * {@inheritDoc}
     * @see \PHPBackend\Image2D\Mlm\Ternary\TernaryNode::isRightChild()
     */
    public function isRightChild($node): bool
    {
        return (($node instanceof Member) && $this->hasRightChild() && $node->getId() == $this->getRightChild()->getId());
    }

    /**
     * {@inheritDoc}
     * @see \PHPBackend\Image2D\Mlm\Node::hasSponsor()
     */
    public function hasSponsor(): bool
    {
        return $this->sponsor != null;
    }

    //getters des PVs
    public function getLeftMembershipPv () : ?float {
        return $this->leftMembershipPv;
    }

    public function getRightMembershipPv () : ?float {
        return $this->rightMembershipPv;
    }

    public function getMiddleMembershipPv () : ?float {
        return $this->middleMembershipPv;
    }

    public function getLeftProductPv () : ?float {
        return $this->leftProductPv;
    }

    public function getRightProductPv () : ?float {
        return $this->rightProductPv;
    }

    public function getMiddleProductPv () : ?float {
        return $this->middleProductPv;
    }

    public function getTotalPv () : ?float {
        return array_sum([
            $this->leftMembershipPv,
            $this->middleMembershipPv,
            $this->rightMembershipPv,
            $this->leftProductPv,
            $this->middleProductPv,
            $this->rightProductPv
        ]);
    }

    public function getProductPv () : float {
        return $this->getLeftProductPv() + $this->getRightProductPv() + $this->getMiddleProductPv();
    }
    public function getMembershipPv () : float {
        return $this->getLeftMembershipPv() + $this->getRightMembershipPv() + $this->getMiddleMembershipPv();
    }
    
    /**
     * renvoie le total des points valeurs generationnel
     *
     * @return float|null
     */
    public function getTotalMembershipPv () : ?float {
        return array_sum([
            $this->leftMembershipPv,
            $this->middleMembershipPv,
            $this->rightMembershipPv
        ]);
    } 

    /**
     * renvoie la sommes points valeurs sur les PVs de re-achat des produits
     *
     * @return float|null
     */
    public function getTotalProductPv () : ?float {
        return array_sum([
            $this->leftProductPv,
            $this->middleProductPv,
            $this->rightProductPv
        ]);
    }

    //==

    /**
     * renvoie le montant total deja retier par le membre proprietaire du compte
     *
     * @return float|null
     */
    public function getWithdrawals ()  : ?float {
        return $this->withdrawals;
    }

    /**
     * renvoie le montant demander par le membre
     *
     * @return float|null
     */
    public function getWithdrawalsRequest () : ?float {
        return $this->withdrawalsRequest;
    }

    public function getSoldGeneration () : ?float {
        return $this->soldGeneration;
    }

    public function getSoldOfficeBonus () : ?float {
        return $this->soldOfficeBonus;
    }

    public function getPurchaseBonus () : ?float {
        return $this->purchaseBonus;
    }

    /**
     * renvoie le montant retirable possible pour le compte du membre.
     * 
     * par defaut le montant renvoyer ne prend pas en compte les demandes qui ne sont pas encore valider.
     * dans ce cas utiliser pluto la methode, getAvailableCashMoney en mode structe
     * 
     * @param bool $structMode
     * @return float
     */
    public function getAvailableCashMoney (bool $structMode = false) : float {
        $amount = 0;

        $amount = $this->getSoldGeneration() + $this->getSoldOfficeBonus() + $this->getPurchaseBonus() + $this->getParticularBonus();

        if($amount !== null) {
            $amount -= $this->getWithdrawals();
        }

        if ($structMode) {
            $amount -= $this->getWithdrawalsRequest();
        }

        // if ($amount < 0) {
        //     $amount = 0;
        // }

        return $amount;
    }

    /**
     * renvoie la somme totale des inputs
     *
     * @return float
     */
    public function getSumInputs () : float {
       return ($this->getSoldGeneration() + $this->getSoldOfficeBonus() + $this->getPurchaseBonus() + $this->getParticularBonus());
    }

    /**
     * Renvoie la somme des etats de sorties du compte
     *
     * @return float
     */
    public function getSumOutputs () : float {
        return ($this->getWithdrawals() + $this->getWithdrawalsRequest());
    }

    //setter de PVs
    public function setLeftMembershipPv (?float $leftMembershipPv) : void {
        $this->leftMembershipPv = $leftMembershipPv;
    }
    
    public function setRightMembershipPv (?float $rightMembershipPv) : void  {
        $this->rightMembershipPv = $rightMembershipPv;
    }

    public function setMiddleMembershipPv (?float $middleMembershipPv) : void {
        $this->middleMembershipPv = $middleMembershipPv;
    }

    public function setLeftProductPv (?float $leftProductPv) : void {
        $this->leftProductPv = $leftProductPv;
    }

    public function setMiddelProductPv (?float $middleProductPv) : void {
        $this->middleProductPv = $middleProductPv;
    }

    public function setRightProductPv (?float $rightProductPv) : void {
        $this->rightProductPv = $rightProductPv;
    }

    //===

    public function setWithdrawals (?float $withdrawals) : void {
        if ($withdrawals == null) {
            $withdrawals = 0;
        }
        $this->withdrawals = $withdrawals;
    }

    public function setWithdrawalsRequest (?float $withdrawalsRequest) : void {
        if ($withdrawalsRequest == null) {
            $withdrawalsRequest = 0;
        }
        $this->withdrawalsRequest = $withdrawalsRequest;
    }

    public function setSoldOfficeBonus (?float $soldOfficeBonus) : void {
        if ($soldOfficeBonus == null) {
            $soldOfficeBonus = 0;
        }
        $this->soldOfficeBonus = $soldOfficeBonus;
    }

    public function setSoldGeneration (?float $soldGeneration) : void {
        if ($soldGeneration == null) {
            $soldGeneration = 0;
        }
        $this->soldGeneration = $soldGeneration;
    }

    public function setPurchaseBonus (?float $purchaseBonus) : void {
        if ($purchaseBonus == null) {
            $purchaseBonus = 0;
        }
        $this->purchaseBonus = $purchaseBonus;
    }


    /**
     * Get the value of particularBonus
     *
     * @return  float
     */ 
    public function getParticularBonus()
    {
        return $this->particularBonus;
    }

    /**
     * Set the value of particularBonus
     *
     * @param  float  $particularBonus
     *
     * @return  self
     */ 
    public function setParticularBonus(?float $particularBonus)
    {
        $this->particularBonus = $particularBonus;

        return $this;
    }

    /**
     * Get the value of particularOperation
     *
     * @return  boolean
     */ 
    public function hasParticularOperation()
    {
        return $this->particularOperation;
    }

    /**
     * Set the value of particularOperation
     *
     * @param  boolean  $particularOperation
     *
     * @return  self
     */ 
    public function setParticularOperation($particularOperation)
    {
        $this->particularOperation = $this->isTrue($particularOperation);

        return $this;
    }
}
