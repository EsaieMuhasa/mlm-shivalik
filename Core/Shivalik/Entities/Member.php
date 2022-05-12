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
    private $matricule;
    
    /**
     * @var Member
     */
    private $parent;
    
    /**
     * @var Member
     */
    private $sponsor;
    
    /**
     * @var int
     */
    private $foot;
    
    /**
     * @var OfficeAdmin
     */
    private $admin;
    
    /**
     * @var Office
     */
    private $office;
    
    /**
     * Pour les utilisateurs qui ont des offices
     * @var Office
     */
    private $officeAccount;
    
    /**
     * contiens l'actuel packet de l'utilisateur
     * @var GradeMember
     */
    private $packet;
    
    /**
     * @var Member[]
     */
    private $childs = [];
    
    
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
        
        //if($this->packet != null) $this->packet->setMember($this);
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
     * @see \PHPBackend\Image2D\Mlm\Node::getSponsor()
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
     * @param Member $parent
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
     * @param Member $sponsor
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
     * @param number $foot
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
        if ($this->getName() == null || $this->getLastName() == null) {
            return null;
        }
        
        $matricule = strtoupper(substr($this->getName(), 0, 1)).$this->id;//.strtoupper(substr($this->getLastName(), 0, 1));
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
     * @return number
     */
    public function getFoot() : ?int
    {
        return $this->foot;
    }

    /**
     * {@inheritDoc}
     * @see \PHPBackend\Image2D\Mlm\Node::getParent()
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
        return $this->getLastName();
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

}

