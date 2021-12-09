<?php
namespace Entities;

use Library\LibException;
use Library\Image2D\Mlm\Ternary\TernaryNode;
use Library\Image2D\Mlm\DefaultNodeIcon;

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
     * @return \Entities\GradeMember
     */
    public function getPacket() : ?GradeMember
    {
        return $this->packet;
    }

    /**
     * @param \Entities\GradeMember $packet
     */
    public function setPacket($packet) : void
    {
        if ($packet == null || $packet instanceof GradeMember) {
            $this->packet = $packet;
        } else if (self::isInt($packet)) {
            $this->packet = new GradeMember(array('id' => $packet));
        } else {
            throw new LibException("invalide param valeur in setPacket method");
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
     * @return \Entities\Member
     */
    public function getSponsor() : ?Member
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
     * @param \Entities\Member $parent
     */
    public function setParent($parent) : void
    {
        if ($this->isInt($parent)) {
            $this->parent = new Member(array('id'=>$parent));
        }elseif ($parent instanceof Member || $parent == null){
            $this->parent = $parent;
        }else {
            throw new LibException("invalid param value");
        } 
    }

    /**
     * @param \Entities\Member $sponsor
     */
    public function setSponsor($sponsor) : void
    {
        if ($this->isInt($sponsor)) {
            $this->sponsor = new Member(array('id'=>$sponsor));
        }elseif ($sponsor instanceof Member || $sponsor == null){
            $this->sponsor = $sponsor;
        }else {
            throw new LibException("invalid param value");
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
     * @return \Entities\OfficeAdmin
     */
    public function getAdmin() : ?OfficeAdmin
    {
        return $this->admin;
    }

    /**
     * @return \Entities\Office
     */
    public function getOffice() : ?Office
    {
        return $this->office;
    }

    /**
     * @param \Entities\OfficeAdmin $admin
     */
    public function setAdmin($admin) : void
    {
        if ($admin == null || $admin instanceof OfficeAdmin) {
            $this->admin = $admin;
        }elseif ($this->isInt($admin)) {
            $this->admin = new OfficeAdmin(array('id' => $admin));
        }else{
            throw new LibException("invalid value in param of method setAdmin");
        }
    }

    /**
     * @param \Entities\Office $office
     */
    public function setOffice($office) : void
    {
        if ($office == null || $office instanceof Office) {
            $this->office = $office;
        }elseif ($this->isInt($office)) {
            $this->office = new Office(array('id' => $office));
        }else{
            throw new LibException("invalid value in param of method setOffice");
        }
    }
    
    /**
     * @return \Entities\Office
     */
    public function getOfficeAccount() : ?Office
    {
        return $this->officeAccount;
    }

    /**
     * @param \Entities\Office $officeAccount
     */
    public function setOfficeAccount($officeAccount)
    {
        if ($officeAccount == null || $officeAccount instanceof Office) {
            $this->officeAccount = $officeAccount;
        }elseif ($this->isInt($officeAccount)) {
            $this->officeAccount = new Office(array('id' => $officeAccount));
        }else{
            throw new LibException("invalid value in param of method setOffice");
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
     * @param \Entities\Member[]  $childs
     */
    public function setChilds(array $childs)
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
     * @see \Library\Image2D\Mlm\Node::getParent()
     * @return Member
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * {@inheritDoc}
     * @see \Library\Image2D\Mlm\Node::getChilds()
     * @return Member[]
     */
    public function getChilds()
    {
        return $this->childs;
    }
    
    /**
     * {@inheritDoc}
     * @see \Library\Image2D\Mlm\Ternary\TernaryNode::getLeftChild()
     * @return Member
     */
    public function getLeftChild()
    {
        return $this->getChild(self::LEFT_FOOT);
    }

    /**
     * {@inheritDoc}
     * @see \Library\Image2D\Mlm\Ternary\TernaryNode::getMiddleChild()
     * @return Member
     */
    public function getMiddleChild()
    {
        return $this->getChild(self::MIDDEL_FOOT);
    }

    /**
     * {@inheritDoc}
     * @see \Library\Image2D\Mlm\Ternary\TernaryNode::getRightChild()
     * @return Member
     */
    public function getRightChild()
    {
        return $this->getChild(self::RIGHT_FOOT);
    }

    /**
     * {@inheritDoc}
     * @see \Library\Image2D\Mlm\Ternary\TernaryNode::hasLeftChild()
     */
    public function hasLeftChild(): bool
    {
        return $this->hasChild(self::LEFT_FOOT);
    }

    /**
     * {@inheritDoc}
     * @see \Library\Image2D\Mlm\Ternary\TernaryNode::hasMiddleChild()
     */
    public function hasMiddleChild(): bool
    {
        return $this->hasChild(self::MIDDEL_FOOT);
    }

    /**
     * {@inheritDoc}
     * @see \Library\Image2D\Mlm\Ternary\TernaryNode::hasRightChild()
     */
    public function hasRightChild(): bool
    {
        return $this->hasChild(self::RIGHT_FOOT);
    }

    /**
     * {@inheritDoc}
     * @see \Library\Image2D\Mlm\Node::countChilds()
     */
    public function countChilds(): int
    {
        return count($this->getChilds());
    }

    /**
     * {@inheritDoc}
     * @see \Library\Image2D\Mlm\Node::getChild()
     * @return Member
     */
    public function getChild(int $foot)
    {
        foreach ($this->getChilds() as $child) {
            if ($child->getFoot() == $foot) {
                return $child;
            }
        }
        
        throw new LibException("no child node at foot {$foot} in {$this->getNodeName()} node");
    }

    /**
     * {@inheritDoc}
     * @see \Library\Image2D\Mlm\Node::getData()
     */
    public function getData()
    {
        return $this;
    }

    /**
     * {@inheritDoc}
     * @see \Library\Image2D\Mlm\Node::getIcon()
     */
    public function getIcon()
    {
        return new DefaultNodeIcon($this->getPhoto());
    }

    /**
     * {@inheritDoc}
     * @see \Library\Image2D\Mlm\Node::getNodeName()
     */
    public function getNodeName(): string
    {
        return $this->getLastName();
    }

    /**
     * {@inheritDoc}
     * @see \Library\Image2D\Mlm\Node::hasChild()
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
     * @see \Library\Image2D\Mlm\Node::hasChilds()
     */
    public function hasChilds(): bool
    {
        return !empty($this->getChilds());
    }

    /**
     * {@inheritDoc}
     * @see \Library\Image2D\Mlm\Node::hasIcon()
     */
    public function hasIcon(): bool
    {
        return ($this->getPhoto() != null);
    }

    /**
     * {@inheritDoc}
     * @see \Library\Image2D\Mlm\Node::hasParent()
     */
    public function hasParent(): bool
    {
        return ($this->parent != null);
    }

    /**
     * {@inheritDoc}
     * @see \Library\Image2D\Mlm\Node::isRoot()
     */
    public function isRoot(): bool
    {
        return !$this->hasParent();
    }
    
    /**
     * {@inheritDoc}
     * @see \Library\Image2D\Mlm\Ternary\TernaryNode::isLeftChild()
     */
    public function isLeftChild($node): bool
    {
        return (($node instanceof Member) && $this->hasLeftChild() && $node->getId() == $this->getLeftChild()->getId());
    }

    /**
     * {@inheritDoc}
     * @see \Library\Image2D\Mlm\Ternary\TernaryNode::isMiddleChild()
     */
    public function isMiddleChild($node): bool
    {
        return (($node instanceof Member) && $this->hasMiddleChild() && $node->getId() == $this->getMiddleChild()->getId());
    }

    /**
     * {@inheritDoc}
     * @see \Library\Image2D\Mlm\Ternary\TernaryNode::isRightChild()
     */
    public function isRightChild($node): bool
    {
        return (($node instanceof Member) && $this->hasRightChild() && $node->getId() == $this->getRightChild()->getId());
    }
    
    


}

